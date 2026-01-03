<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\PhoneTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PhoneController extends Controller
{
    /**
     * Display a listing of the smartphone device in the Phone.vue
     */
    public function index(Request $request)
    {
        $filterBrand = $request->get('brand');
        $sort = $request->get('sort', 'date_modified'); // Default to date_modified

        $query = Phone::query()
            ->when($filterBrand, function ($query, $filterBrand) {
                $query->where('brand', 'LIKE', '%' . $filterBrand . '%');
            })
            ->with('currentTransaction');

        // Handle Sorting Logic
        switch ($sort) {
            case 'name':
                $query->orderBy('brand', 'asc')->orderBy('model', 'asc');
                break;

            case 'availability':
                $query->orderByRaw("CASE 
                WHEN status = 'available' THEN 1 
                WHEN status = 'issued' THEN 2 
                WHEN status = 'returned' THEN 3 
                ELSE 4 END");
                break;

            case 'date_modified':
            default:
                $query->latest('updated_at');
                break;
        }

        $phones = $query->paginate(12)->withQueryString();

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
        ]);

        $validated['status'] = 'available';

        Phone::create($validated);

        return redirect()->route('phone.create')->with('message', 'Phone registered successfully.');
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
        // $validated['issued_by'] = auth()->user()->name ?? 'System Admin';

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
        // 1. Validate Return Data
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

        // 2. Find the LATEST transaction for this phone that hasn't been returned yet
        $transaction = PhoneTransaction::where('serial_num', $phone->serial_num)
            ->whereNull('date_returned')
            ->latest()
            ->first();

        if ($transaction) {
            // 3. Update that specific row with return details
            $transaction->update($validated);
        } else {
            // Optional: Handle case where no active issuance exists
            return redirect()->back()->withErrors(['error' => 'No active issuance found for this device.']);
        }

        // 4. Update the Phone Status to available (or returned)
        $phone->update(['status' => 'returned']);

        return redirect()->back()->with('success', 'The device has been returned successfully.');
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
        //
    }
}
