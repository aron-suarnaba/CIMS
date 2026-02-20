<?php

namespace App\Http\Controllers;

use App\Models\MiniPc;
use App\Models\MiniPcIssuance;
use App\Models\MiniPcPullout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MiniPcController extends Controller
{
    public function index(Request $request)
    {
        $query = MiniPc::with(['issuances' => function ($q) {
            $q->latest()->limit(1);
        }]);

        // search term
        if ($search = $request->get('search')) {
            $query->where('manufacturer_model', 'like', "%{$search}%");
        }

        // sorting logic mirrors the frontend dropdown choices
        $sort = $request->get('sort', 'status');
        $direction = 'asc';

        switch ($sort) {
            case 'manufacturer_model':
                $query->orderBy('manufacturer_model', $direction);
                break;

            case 'status':
                // order by availability similar to phones
                $query->orderByRaw(
                    "CASE
                        WHEN status = 'available' THEN 1
                        WHEN status = 'issued' THEN 2
                        WHEN status = 'pullout' THEN 3
                        ELSE 4
                    END ASC, id DESC"
                );
                break;

            case 'date_modified':
                $query->orderBy('updated_at', 'desc');
                break;

            default:
                // fallback to latest created
                $query->orderBy('id', 'desc');
                break;
        }

        $pcs = $query->paginate(15)->withQueryString();

        return inertia('AssetInventoryManagement/MiniPcList', [
            'pcs' => $pcs,
        ]);
    }

    public function create()
    {
        return inertia('AssetInventoryManagement/AddMiniPc');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'manufacturer_model' => 'required|string|max:255',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'rom' => 'nullable|string|max:255',
            'mac' => 'nullable|string|max:255',
            'ip' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        MiniPc::create($validated);

        return redirect()->route('minipc.index')->with('success', 'Mini PC added successfully.');
    }

    public function show(MiniPc $minipc)
    {
        // load relation data
        $minipc->load(['issuances.pullout']);
        // Current issuance
        $currentIssuance = $minipc->issuances()->whereDoesntHave('pullout')->latest()->first();

        return inertia('AssetInventoryManagement/MiniPcDetails', [
            'minipc' => $minipc,
            'minipc_issuance' => $currentIssuance,
            'minipc_pullout' => $currentIssuance ? $currentIssuance->pullout : null,
        ]);
    }

    public function update(Request $request, MiniPc $minipc)
    {
        $validated = $request->validate([
            'manufacturer_model' => 'required|string|max:255',
            'cpu' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'rom' => 'nullable|string|max:255',
            'mac' => 'nullable|string|max:255',
            'ip' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $minipc->update($validated);

        return redirect()->back()->with('success', 'Mini PC updated.');
    }

    public function destroy(MiniPc $minipc)
    {
        $minipc->delete();
        return redirect()->route('minipc.index')->with('success', 'Mini PC removed.');
    }

    public function issue(Request $request, MiniPc $minipc)
    {
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'date_issued' => 'required|date',
        ]);

        $validated['mini_pc_id'] = $minipc->id;

        MiniPcIssuance::create($validated);

        $minipc->update(['status' => 'issued']);

        return redirect()->back()->with('success', 'Mini PC issued.');
    }

    public function pullout(Request $request, MiniPc $minipc)
    {
        $validated = $request->validate([
            'pullout_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        try {
            $result = DB::transaction(function () use ($minipc, $validated) {
                $issuance = MiniPcIssuance::where('mini_pc_id', $minipc->id)
                    ->whereDoesntHave('pullout')
                    ->latest()
                    ->lockForUpdate()
                    ->first();

                if (!$issuance) {
                    return false;
                }

                MiniPcPullout::create([
                    'mini_pc_issuance_id' => $issuance->id,
                    'pullout_date' => $validated['pullout_date'],
                    'reason' => $validated['reason'] ?? null,
                ]);

                $minipc->update(['status' => 'available']);
                return true;
            });

            if (!$result) {
                return redirect()->back()->withErrors(['error' => 'No active issuance found']);
            }

            return redirect()->back()->with('success', 'Pullout recorded.');
        } catch (\Throwable $e) {
            Log::error('MiniPc pullout failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to record pullout']);
        }
    }
}
