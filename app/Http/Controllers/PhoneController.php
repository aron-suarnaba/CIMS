<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\PhoneTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filterBrand = $request->get('brand');

        $phones = Phone::query()
            ->when($filterBrand, function ($query, $filterBrand) {
                $query->where('brand', 'LIKE', '%' . $filterBrand . '%');
            })
            ->with(['transactions', 'currentTransaction'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('AssetInventoryManagement/Phone', [
            'phones' => $phones,
            'filters' => $request->only(['brand']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('AssetInventoryManagement/AddPhone');
    }

    /**
     * Store a newly created resource in storage.
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

    public function phoneTransStore(Request $request, Phone $phone)
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
        $validated['issued_by'] = auth()->user()->name ?? 'System Admin';

        // 3. Create the Transaction
        PhoneTransaction::create($validated);

        // 4. Update the Phone Status
        $phone->update([
            'status' => 'issued'
            // If you want to track acks on the phone table specifically:
            // 'it_ack_issued' => $validated['it_ack_issued'] 
        ]);

        return redirect()->back()->with('message', 'Phone issued successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Phone $phone, PhoneTransaction $phoneTransaction)
    {
        return Inertia::render('AssetInventoryManagement/PhoneDetails', [
            'phone' => $phone,
            'phone_transaction' => $phoneTransaction,
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
