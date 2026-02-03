<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\PhoneTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $page = max((int) $request->get('page', 1), 1);
        $start = (($page - 1) * $perPage) + 1;
        $end = $page * $perPage;

        // Explicitly prefix columns with 'p.' to avoid ambiguity during JOINs
        $orderBy = match ($sort) {
            'name' => 'p.brand ASC, p.model ASC, p.id ASC',
            'availability' => "
        CASE
            WHEN p.status = 'available' THEN 1
            WHEN p.status = 'issued' THEN 2
            WHEN p.status = 'return' THEN 3
            ELSE 4
        END ASC, p.id DESC
        ",
            default => 'p.updated_at DESC, p.id DESC',
        };

        $where = "WHERE 1 = 1";
        $bindings = [];

        if ($filterBrand) {
            $where .= " AND brand LIKE ?";
            $bindings[] = "%{$filterBrand}%";
        }

        if ($search) {
            $where .= " AND (
            brand LIKE ?
            OR model LIKE ?
            OR p.serial_num LIKE ?
            OR status LIKE ?
        )";
            array_push($bindings, "%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%");
        }

        $total = DB::selectOne("SELECT COUNT(*) AS total FROM phones p $where", $bindings)->total;

        /*
        |--------------------------------------------------------------------------
        | JOINED QUERY
        | We join phone_transactions where date_returned is NULL to get the active user
        |--------------------------------------------------------------------------
        */
        $phonesRaw = DB::select("
        SELECT *
        FROM (
            SELECT
                p.*,
                t.department as trans_dept,
                t.date_issued as trans_date,
                ROW_NUMBER() OVER (ORDER BY $orderBy) AS row_num
            FROM phones p
            LEFT JOIN phone_transactions t ON p.serial_num = t.serial_num
                 AND t.date_returned IS NULL
            $where
        ) AS numbered
        WHERE row_num BETWEEN ? AND ?
        ORDER BY row_num
    ", [...$bindings, $start, $end]);

        $formattedData = array_map(function ($phone) {
            $phone->current_transaction = $phone->trans_dept ? [
                'department' => $phone->trans_dept,
                'date_issued' => $phone->trans_date,
            ] : null;
            return $phone;
        }, $phonesRaw);

        return Inertia::render('AssetInventoryManagement/PhoneList', [
            'phones' => [
                'data' => $formattedData,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'from' => $start <= $total ? $start : null,
                'to' => min($end, $total),
                'last_page' => (int) ceil($total / $perPage),
            ],
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
            'issued_by' => 'nullable|string|max:255',
            'department' => 'required|string|max:255',
            'date_issued' => 'required|date',
            'issued_accessories' => 'nullable|string',
            'cashout' => 'required|boolean',
            'remarks' => 'nullable|string|max:255',
        ]);

        $validated['serial_num'] = $phone->serial_num;

        PhoneTransaction::create($validated);

        $phone->update([
            'status' => 'issued',
            'remarks' => $validated['remarks'] ?? $phone->remarks,
        ]);

        return redirect()->back()->with('success', 'The device has been issued successfully to ' . $validated['issued_to']);
    }

    // This controller is for the storing data of phone issuance modals
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

    /**
     * Generate a pdf report for the company phone logsheet
     */
    public function generateLogsheetReport(Phone $phone)
    {
        $phone->load('currentTransaction');

        $data = [
            'phone' => $phone,
            'current' => $phone->currentTransaction()->latest()->first(),
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('reports.CompanyPhoneLogsheet', $data);

        // 2. Options (Optional: change paper size per PDF)
        $pdf->setPaper('legal', 'landscape');

        // 3. Output
        // Use ->stream() to show in browser, or ->download() to force download
        return $pdf->stream("Phone-{$phone->serial_num}-logsheet.pdf");
    }
}
