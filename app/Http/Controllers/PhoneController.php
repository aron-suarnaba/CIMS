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

        $phonesQuery = Phone::query();

        if ($filterBrand) {
            $phonesQuery->where('brand', 'LIKE', '%' . $filterBrand . '%');
        }
        $phones = $phonesQuery
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('AssetInventoryManagement/Phone', [
            'phones' => $phones,
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
