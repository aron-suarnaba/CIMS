<?php

namespace App\Http\Controllers;

use App\Events\AssetUpdated;
use App\Models\Phone;
use App\Models\PhoneTransaction;
use App\Models\PhoneIssuance;
use App\Models\PhoneReturn;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
        | We join phone_issuances where there's no associated return to get the active user
        |--------------------------------------------------------------------------
        */
        $phonesRaw = DB::select("
        SELECT *
        FROM (
            SELECT
                p.*,
                pi.department as trans_dept,
                pi.date_issued as trans_date,
                ROW_NUMBER() OVER (ORDER BY $orderBy) AS row_num
            FROM phones p
            LEFT JOIN phone_issuances pi ON p.serial_num = pi.serial_num
                 AND NOT EXISTS (
                    SELECT 1 FROM phone_returns pr
                    WHERE pr.phone_issuance_id = pi.id
                 )
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9048',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_num' => 'required|string|max:255|unique:phones,serial_num',
            'imei_one' => 'nullable|string|max:255|unique:phones,imei_one',
            'imei_two' => 'nullable|string|max:255|unique:phones,imei_two',
            'ram' => 'required|integer',
            'rom' => 'required|integer',
            'sim_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        if($request->hasFile('image')){
            $file = $request->file('image');
            File::ensureDirectoryExists(public_path('img/phone_uploads'));

        // Generate a clean, unique filename
        $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        // Move to public/img/phone_uploads
        $file->move(public_path('img/phone_uploads'), $fileName);

        // Save the path string to the database column
        $validated['image_path'] = 'img/phone_uploads/' . $fileName;

        }

        $validated['status'] = 'available';

        unset($validated['image']);

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

        // Create issuance record
        PhoneIssuance::create($validated);

        $phone->update([
            'status' => 'issued',
            'remarks' => $validated['remarks'] ?? $phone->remarks,
        ]);

        event(new AssetUpdated('Phone asset manage and inventory updated! ... '));

        return redirect()->back()->with('success', 'The device has been issued successfully to ' . $validated['issued_to']);
    }

    // This controller is for the storing data of phone return modals
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

        try {
            $result = DB::transaction(function () use ($phone, $validated) {
                // Lock active issuance row to prevent duplicate return submissions.
                $issuance = PhoneIssuance::where('serial_num', $phone->serial_num)
                    ->whereDoesntHave('return')
                    ->latest()
                    ->lockForUpdate()
                    ->first();

                if (!$issuance) {
                    return false;
                }

                PhoneReturn::create([
                    'phone_issuance_id' => $issuance->id,
                    'returned_to' => $validated['returned_to'],
                    'returned_by' => $validated['returned_by'],
                    'returnee_department' => $validated['returnee_department'],
                    'date_returned' => $validated['date_returned'],
                    'returned_accessories' => $validated['returned_accessories'],
                ]);

                $phone->update([
                    'status' => 'available',
                    'remarks' => $validated['remarks'] ?? $phone->remarks,
                ]);

                return true;
            });

            if (!$result) {
                return redirect()->back()->withErrors(['error' => 'No active issuance found for this device.']);
            }

            event(new AssetUpdated('Phone asset manage and inventory updated! ... '));
            return redirect()->back()->with('success', 'The device has been returned successfully.');
        } catch (\Throwable $e) {
            Log::error('Phone return failed.', [
                'phone_id' => $phone->id,
                'serial_num' => $phone->serial_num,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Failed to process return. Please retry.',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Phone $phone)
    {
        $phone->load(['issuances.return']);

        $currentIssuance = $phone->currentIssuance()->first();
        $currentReturn = $currentIssuance?->return;

        return Inertia::render('AssetInventoryManagement/PhoneDetails', [
            'phone' => $phone,
            'phone_issuance' => $currentIssuance,
            'phone_return' => $currentReturn,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9048',
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
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                File::ensureDirectoryExists(public_path('img/phone_uploads'));

                $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move(public_path('img/phone_uploads'), $fileName);
                $validated['image_path'] = 'img/phone_uploads/' . $fileName;

                // Delete previous uploaded image to avoid orphan files.
                if (
                    $phone->image_path &&
                    str_starts_with($phone->image_path, 'img/phone_uploads/')
                ) {
                    $oldPath = public_path($phone->image_path);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
            }

            unset($validated['image']);
            $phone->update($validated);
            event(new AssetUpdated('Phone asset manage and inventory updated! ... '));
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
            event(new AssetUpdated('Phone asset manage and inventory updated! ... '));
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
        // Load the history of transactions/issuances
        $transactions = $phone->issuances()->with('return')->get();

        $data = [
            'phone' => $phone,
            'transactions' => $transactions,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('reports.CompanyPhoneLogsheet', $data);
        $pdf->setPaper('legal', 'landscape');

        return $pdf->stream("Phone-{$phone->serial_num}-logsheet.pdf");
    }
}
