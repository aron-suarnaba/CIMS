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

        $perPage = 12;
        $page = $request->integer('page', 1);
        $offset = ($page - 1) * $perPage;

        /** Base query (NO pagination yet) */
        $query = Phone::query()
            ->when($filterBrand, function ($query, $filterBrand) {
                $query->where('brand', 'LIKE', '%' . $filterBrand . '%');
            })
            ->with('currentTransaction');

        /** Sorting */
        switch ($sort) {
            case 'name':
                $query->orderBy('brand', 'asc')
                    ->orderBy('model', 'asc');
                break;

            case 'availability':
                $query->orderByRaw("
                CASE
                    WHEN status = 'available' THEN 1
                    WHEN status = 'issued' THEN 2
                    WHEN status = 'returned' THEN 3
                    ELSE 4
                END
            ");
                break;

            case 'date_modified':
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        /** Total count (clone query to avoid mutation) */
        $total = (clone $query)->count();

        /** Get paginated IDs using ROW_NUMBER (SQL Server 2008 safe) */
        $ids = DB::table(DB::raw("(
        SELECT id,
               ROW_NUMBER() OVER (ORDER BY updated_at DESC) AS row_num
        FROM phones
    ) AS t"))
            ->whereBetween('row_num', [$offset + 1, $offset + $perPage])
            ->pluck('id');

        /** Fetch actual models with relationships */
        $phones = $query->whereIn('id', $ids)->get();

        /** Create paginator */
        $phones = new LengthAwarePaginator(
            $phones,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return Inertia::render('AssetInventoryManagement/Phone', [
            'phones' => $phones,
            'filters' => $request->only(['brand', 'sort']),
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
            'brand' => 'required|string',
            'model' => 'required|string',
            'serial_num' => 'required|string|unique:phones,serial_num',
            'imei_one' => 'required|string|unique:phones,imei_one',
            'imei_two' => 'nullable|string',
            'ram' => 'required|string',
            'rom' => 'required|string',
            'sim_no' => 'nullable|string',
            'cashout' => 'required|boolean',
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
            'it_ack_issued' => 'required|boolean',
            'purch_ack_issued' => 'required|boolean', // Ensure this exists in your DB or handle it
        ]);

        // 2. Add the foreign key (serial_num or phone_id)
        $validated['serial_num'] = $phone->serial_num;

        // 3. Create the Transaction
        PhoneTransaction::create($validated);

        // 4. Update the Phone Status
        $phone->update([
            'status' => 'issued'
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
            'it_ack_returned' => 'required|boolean',
            'purch_ack_returned' => 'required|boolean',
            'remarks' => 'nullable|string|max:255',
        ]);

        $transaction = PhoneTransaction::where('serial_num', $phone->serial_num)
            ->whereNull('date_returned')
            ->latest()
            ->first();

        if ($transaction) {

            $phone->update(['status' => 'available']);

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
        //
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
