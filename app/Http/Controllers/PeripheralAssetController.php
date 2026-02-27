<?php

namespace App\Http\Controllers;

use App\Events\AssetUpdated;
use App\Models\PeripheralAsset;
use App\Models\PeripheralIssuance;
use App\Models\PeripheralReturn;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PeripheralAssetController extends Controller
{
    public function index(Request $request)
    {
        $filterBrand = $request->get('brand');
        $sort = $request->get('sort', 'date_modified');
        $search = $request->get('search');

        $perPage = 12;
        $page = max((int) $request->get('page', 1), 1);
        $start = (($page - 1) * $perPage) + 1;
        $end = $page * $perPage;

        $orderBy = match ($sort) {
            'name' => 'a.brand ASC, a.model ASC, a.id ASC',
            'availability' => "
                CASE
                    WHEN a.status = 'available' THEN 1
                    WHEN a.status = 'issued' THEN 2
                    WHEN a.status = 'return' THEN 3
                    ELSE 4
                END ASC, a.id DESC
            ",
            default => 'a.updated_at DESC, a.id DESC',
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
                OR a.serial_num LIKE ?
                OR status LIKE ?
            )";
            array_push($bindings, "%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%");
        }

        $total = DB::selectOne("SELECT COUNT(*) AS total FROM peripheral_assets a $where", $bindings)->total;

        $assetsRaw = DB::select("
            SELECT *
            FROM (
                SELECT
                    a.*,
                    ai.issued_to as trans_issued_to,
                    ai.department as trans_dept,
                    ai.date_issued as trans_date,
                    ROW_NUMBER() OVER (ORDER BY $orderBy) AS row_num
                FROM peripheral_assets a
                LEFT JOIN peripheral_issuances ai ON a.serial_num = ai.serial_num
                     AND NOT EXISTS (
                        SELECT 1 FROM peripheral_returns ar
                        WHERE ar.peripheral_issuance_id = ai.id
                     )
                $where
            ) AS numbered
            WHERE row_num BETWEEN ? AND ?
            ORDER BY row_num
        ", [...$bindings, $start, $end]);

        $formattedData = array_map(function ($asset) {
            $asset->current_transaction = $asset->trans_dept ? [
                'issued_to' => $asset->trans_issued_to,
                'department' => $asset->trans_dept,
                'date_issued' => $asset->trans_date,
            ] : null;
            return $asset;
        }, $assetsRaw);

        return Inertia::render('AssetInventoryManagement/PeripheralsList', [
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9048',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_num' => 'required|string|max:255|unique:peripheral_assets,serial_num',
            'imei_one' => 'nullable|string|max:255|unique:peripheral_assets,imei_one',
            'imei_two' => 'nullable|string|max:255',
            'ram' => 'required|integer',
            'rom' => 'required|integer',
            'sim_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            File::ensureDirectoryExists(public_path('img/peripheral_uploads'));
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('img/peripheral_uploads'), $fileName);
            $validated['image_path'] = 'img/peripheral_uploads/' . $fileName;
        }

        $validated['status'] = 'available';
        unset($validated['image']);

        PeripheralAsset::create($validated);

        return redirect()->back()->with('success', 'Peripheral asset registered successfully.');
    }

    public function show(PeripheralAsset $peripheral)
    {
        $peripheral->load(['issuances.return']);

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate(route('peripherals.show', $peripheral->id));

        $base64Qr = 'data:image/svg+xml;base64,' . base64_encode($qrCode);

        return Inertia::render('AssetInventoryManagement/PeripheralsDetails', [
            'phone' => $peripheral,
            'phone_issuance' => $peripheral->currentIssuance()->first(),
            'phone_return' => $peripheral->currentIssuance()?->first()?->return,
            'qr_code' => $base64Qr,
        ]);
    }

    public function update(Request $request, PeripheralAsset $peripheral)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9048',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_num' => 'required|string|max:255|unique:peripheral_assets,serial_num,' . $peripheral->id,
            'imei_one' => 'nullable|string|max:255|unique:peripheral_assets,imei_one,' . $peripheral->id,
            'imei_two' => 'nullable|string|max:255',
            'ram' => 'required|string|max:255',
            'rom' => 'required|string|max:255',
            'sim_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                File::ensureDirectoryExists(public_path('img/peripheral_uploads'));

                $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move(public_path('img/peripheral_uploads'), $fileName);
                $validated['image_path'] = 'img/peripheral_uploads/' . $fileName;

                if (
                    $peripheral->image_path &&
                    str_starts_with($peripheral->image_path, 'img/peripheral_uploads/')
                ) {
                    $oldPath = public_path($peripheral->image_path);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
            }

            unset($validated['image']);
            $peripheral->update($validated);
            event(new AssetUpdated('Peripheral asset manage and inventory updated! ... '));
            return redirect()->back()->with('success', 'Peripheral asset updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update peripheral asset.');
        }
    }

    public function destroy(PeripheralAsset $peripheral)
    {
        try {
            $peripheral->delete();
            event(new AssetUpdated('Peripheral asset manage and inventory updated! ... '));
            return redirect()->route('peripherals.index')->with('success', 'Asset record and all related history have been deleted');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete asset.');
        }
    }

    public function issue(Request $request, PeripheralAsset $peripheral)
    {
        $validated = $request->validate([
            'issued_to' => 'required|string|max:255',
            'issued_by' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date_issued' => 'required|date',
            'issued_accessories' => 'nullable|string',
            'headphones' => 'nullable|boolean',
            'charger' => 'nullable|boolean',
            'cashout' => 'required|boolean',
            'acknowledgement' => 'nullable|boolean',
            'remarks' => 'nullable|string|max:255',
        ]);

        $validated['serial_num'] = $peripheral->serial_num;

        $issuanceData = [
            'serial_num' => $validated['serial_num'],
            'issued_to' => $validated['issued_to'],
            'issued_by' => $validated['issued_by'],
            'department' => $validated['department'],
            'date_issued' => $validated['date_issued'],
            'issued_accessories' => $validated['issued_accessories'] ?? null,
            'headphones' => $validated['headphones'] ?? false,
            'charger' => $validated['charger'] ?? false,
            'acknowledgement' => $validated['acknowledgement'] ?? null,
            'cashout' => $validated['cashout'],
            'remarks' => $validated['remarks'] ?? null,
        ];

        PeripheralIssuance::create($issuanceData);

        $peripheral->update([
            'status' => 'issued',
            'remarks' => $validated['remarks'] ?? $peripheral->remarks,
        ]);

        event(new AssetUpdated('Peripheral asset manage and inventory updated! ... '));

        return redirect()->back()->with('success', 'The device has been issued successfully to ' . $validated['issued_to']);
    }

    public function return(Request $request, PeripheralAsset $peripheral)
    {
        $validated = $request->validate([
            'returned_to' => 'required|string|max:255',
            'returned_by' => 'required|string|max:255',
            'returnee_department' => 'required|string|max:255',
            'date_returned' => 'required|date',
            'returned_accessories' => 'nullable|string',
            'charger' => 'nullable|boolean',
            'headphones' => 'nullable|boolean',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $result = DB::transaction(function () use ($peripheral, $validated) {
                $issuance = PeripheralIssuance::where('serial_num', $peripheral->serial_num)
                    ->whereDoesntHave('return')
                    ->latest()
                    ->lockForUpdate()
                    ->first();

                if (!$issuance) {
                    return false;
                }

                PeripheralReturn::create([
                    'peripheral_issuance_id' => $issuance->id,
                    'returned_to' => $validated['returned_to'],
                    'returned_by' => $validated['returned_by'],
                    'returnee_department' => $validated['returnee_department'],
                    'date_returned' => $validated['date_returned'],
                    'returned_accessories' => $validated['returned_accessories'],
                    'charger' => $validated['charger'] ?? false,
                    'headphones' => $validated['headphones'] ?? false,
                    'remarks' => $validated['remarks'] ?? null,
                ]);

                $peripheral->update([
                    'status' => 'available',
                    'remarks' => $validated['remarks'] ?? $peripheral->remarks,
                ]);

                return true;
            });

            if (!$result) {
                return redirect()->back()->withErrors(['error' => 'No active issuance found for this device.']);
            }

            event(new AssetUpdated('Peripheral asset manage and inventory updated! ... '));
            return redirect()->back()->with('success', 'The device has been returned successfully.');
        } catch (\Throwable $e) {
            Log::error('Peripheral return failed.', [
                'peripheral_id' => $peripheral->id,
                'serial_num' => $peripheral->serial_num,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Failed to process return. Please retry.',
            ]);
        }
    }

    public function generateLogsheetReport(PeripheralAsset $peripheral)
    {
        $transactions = $peripheral->issuances()
            ->with('return')
            ->orderBy('date_issued', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $data = [
            'asset' => $peripheral,
            'transactions' => $transactions,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('reports.CompanyPeripheralLogsheet', $data);
        $pdf->setPaper('legal', 'landscape');

        return $pdf->stream("Peripheral-{$peripheral->serial_num}-logsheet.pdf");
    }
}
