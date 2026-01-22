<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\PhoneTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PhoneController extends Controller
{
    /**
     * Display a listing of the smartphone device in the Phone.vue
     */

    public function index(Request $request)
    {
        $filterBrand = $request->get('brand');
        $sort = $request->get('sort', 'date_modified');
        $search = $request->get('search');

        $perPage = 12;

        $query = Phone::query()
            // Filter by Brand (Dropdown)
            ->when($filterBrand, function ($q) use ($filterBrand) {
                $q->where('brand', 'LIKE', '%' . $filterBrand . '%');
            })
            // Global Search Bar
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    // Search Phone Table
                    $sub->where('brand', 'LIKE', "%{$search}%")
                        ->orWhere('model', 'LIKE', "%{$search}%")
                        ->orWhere('serial_num', 'LIKE', "%{$search}%")
                        ->orWhere('status', 'LIKE', "%{$search}%")
                        ->orWhereHas('currentTransaction', function ($rel) use ($search) {
                        $rel->where('department', 'LIKE', "%{$search}%");
                    });
                });
            });

        /** Sorting Logic **/
        switch ($sort) {
            case 'name':
                $query->orderBy('brand', 'asc')->orderBy('model', 'asc');
                break;
            case 'availability':
                $query->orderByRaw("CASE
                WHEN status = 'available' THEN 1
                WHEN status = 'issued' THEN 2
                WHEN status = 'return' THEN 3
                ELSE 4 END");
                break;
            case 'date_modified':
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        // paginate() automatically handles the SQL Server OFFSET logic
        $phones = $query->with(['currentTransaction'])->paginate($perPage)->withQueryString();

        return Inertia::render('AssetInventoryManagement/PhoneList', [
            'phones' => $phones,
            'filters' => $request->only(['brand', 'sort', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new smartphone device.
     */
    public function create()
    {
        return Inertia::render('AssetInventoryManagement/AddPhone');
    }

    /**
     * Function store is where the data stored or validate from the creation of smartphone device.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_num' => 'required|string|max:255|unique:phones,serial_num',
            'imei_one' => 'nullable|string|max:255|unique:phones,imei_one',
            'imei_two' => 'nullable|string|max:255|unique:phones,imei_two',
            'ram' => 'required|string|max:255',
            'rom' => 'required|string|max:255',
            'sim_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $validated['status'] = 'available';

        Phone::create($validated);

        return redirect()->back()->with('success', 'Phone registered successfully.');
    }


    /**
     * This is where the data of smartphone issuance store the data from the modals.
     */
    public function issue(Request $request, Phone $phone)
    {
        // 1. Validate Transaction Data
        $validated = $request->validate([
            'issued_to' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date_issued' => 'required|date',
            'issued_by' => 'nullable|string|max:255',
            'issued_accessories' => 'nullable|string',
            'cashout' => 'required|boolean',
            'remarks' => 'nullable|string|max:255',
        ]);

        // 2. Add the foreign key (serial_num or phone_id)
        $validated['serial_num'] = $phone->serial_num;

        // 3. Create the Transaction
        PhoneTransaction::create($validated);

        $phone->update([
            'status' => 'issued',
            'remarks' => $validated['remarks'] ?? $phone->remarks,
        ]);

        return redirect()->back()->with('success', 'The device has been issued successfully to ' . $validated['issued_to']);
    }

    public function return(Request $request, Phone $phone)
    {
        $validated = $request->validate([
            'returned_to' => 'required|string|max:255',
            'returned_by' => 'required|string|max:255',
            'returnee_department' => 'required|string|max:255',
            'date_returned' => 'required|date',
            'returned_accessories' => 'nullable|string',
            'remarks' => 'nullable|string|max:255',
        ]);

        $transaction = PhoneTransaction::where('serial_num', $phone->serial_num)
            ->whereNull('date_returned')
            ->latest()
            ->first();

        if ($transaction) {

            $phone->update([
                'status' => 'available',
                'remarks' => $validated['remarks'] ?? $phone->remarks,
            ]);

            $transaction->update($validated);

            return redirect()->back()->with('success', 'The device has been returned successfully.');

        }

        return redirect()->back()->withErrors(['error' => 'No active issuance found for this device.']);

    }

    /**
     * Display the specified resource.
     */
    public function show(Phone $phone)
    {
        $phone->load(['currentTransaction', 'transactions']);

        return Inertia::render('AssetInventoryManagement/PhoneDetails', [
            'phone' => $phone,
            'phone_transaction' => $phone->transactions()->latest()->first(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Phone $phone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phone $phone)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_num' => 'required|string|max:255|unique:phones,serial_num,' . $phone->id,
            'imei_one' => 'required|string|max:255|unique:phones,imei_one,' . $phone->id,
            'imei_two' => 'nullable|string|max:255|unique:phones,imei_two,' . $phone->id,
            'ram' => 'required|string|max:255',
            'rom' => 'required|string|max:255',
            'sim_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        try {
            $phone->update($validated);
            return redirect()->back()->with('success', 'Phone asset updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update phone asset.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phone $phone)
    {
        try {
            $phone->delete();
            return redirect()->route('phone.index')->with('success', 'Asset record and all related history have been deleted');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete asset.');
        }
    }
}
