<?php

namespace App\Http\Controllers;

use App\Models\Computers;
use App\Models\MiniPc;
use App\Models\Phone;
use App\Models\SoftwareLicense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class AutomationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('AssetInventoryManagement/Automation', [
            'actions' => [
                [
                    'key' => 'optimize_clear',
                    'label' => 'Optimize Clear',
                    'description' => 'Clear route, view, config, and cache artifacts.',
                    'icon' => 'bi bi-lightning-charge-fill',
                ],
                [
                    'key' => 'queue_restart',
                    'label' => 'Queue Restart',
                    'description' => 'Gracefully restart queue workers.',
                    'icon' => 'bi bi-arrow-clockwise',
                ],
                [
                    'key' => 'cache_clear',
                    'label' => 'Cache Clear',
                    'description' => 'Clear the application cache store.',
                    'icon' => 'bi bi-database-x',
                ],
                [
                    'key' => 'inventory_health_summary',
                    'label' => 'Inventory Health Summary',
                    'description' => 'Get a quick summary of asset availability and license risks.',
                    'icon' => 'bi bi-bar-chart-line-fill',
                ],
                [
                    'key' => 'expire_licenses',
                    'label' => 'Auto-Mark Expired Licenses',
                    'description' => 'Automatically tag software licenses as expired based on expiry date.',
                    'icon' => 'bi bi-calendar2-x-fill',
                ],
                [
                    'key' => 'reconcile_asset_status',
                    'label' => 'Reconcile Asset Status',
                    'description' => 'Fix phone and mini PC statuses based on active issuance records.',
                    'icon' => 'bi bi-shield-check',
                ],
            ],
        ]);
    }

    public function run(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:optimize_clear,queue_restart,cache_clear,inventory_health_summary,expire_licenses,reconcile_asset_status',
        ]);

        $actionMap = [
            'optimize_clear' => 'optimize:clear',
            'queue_restart' => 'queue:restart',
            'cache_clear' => 'cache:clear',
        ];

        try {
            if (isset($actionMap[$validated['action']])) {
                Artisan::call($actionMap[$validated['action']]);
                return back()->with('success', "Automation action '{$validated['action']}' executed successfully.");
            }

            if ($validated['action'] === 'inventory_health_summary') {
                return $this->runInventoryHealthSummary();
            }

            if ($validated['action'] === 'expire_licenses') {
                return $this->runLicenseExpiryAutomation();
            }

            if ($validated['action'] === 'reconcile_asset_status') {
                return $this->runAssetStatusReconciliation();
            }

            return back()->with('error', 'Unsupported automation action.');
        } catch (\Throwable $e) {
            return back()->with('error', "Automation action failed: {$e->getMessage()}");
        }
    }

    private function runInventoryHealthSummary()
    {
        $phones = [
            'total' => Phone::count(),
            'issued' => Phone::where('status', 'issued')->count(),
            'available' => Phone::where('status', 'available')->count(),
        ];

        $miniPcs = [
            'total' => MiniPc::count(),
            'issued' => MiniPc::where('status', 'issued')->count(),
            'available' => MiniPc::where('status', 'available')->count(),
        ];

        $workstations = [
            'total' => Computers::count(),
            'issued' => Computers::where('status', 'issued')->count(),
            'available' => Computers::where('status', 'available')->count(),
        ];

        $expiringSoon = SoftwareLicense::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', Carbon::today())
            ->whereDate('expiry_date', '<=', Carbon::today()->copy()->addDays(30))
            ->count();

        $lowAvailability = SoftwareLicense::get()
            ->filter(fn ($license) => $license->available_licenses <= 2)
            ->count();

        $message = "Inventory Summary | Phones: {$phones['available']}/{$phones['total']} available, "
            ."Mini PCs: {$miniPcs['available']}/{$miniPcs['total']} available, "
            ."Workstations: {$workstations['available']}/{$workstations['total']} available, "
            ."Licenses expiring in 30 days: {$expiringSoon}, "
            ."Low license availability (<=2): {$lowAvailability}.";

        return back()->with('success', $message);
    }

    private function runLicenseExpiryAutomation()
    {
        $updated = SoftwareLicense::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', Carbon::today())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        return back()->with('success', "License expiry automation complete. {$updated} license record(s) marked as expired.");
    }

    private function runAssetStatusReconciliation()
    {
        $phonesUpdated = 0;
        $miniPcsUpdated = 0;

        Phone::with('currentIssuance')->chunkById(100, function ($phones) use (&$phonesUpdated) {
            foreach ($phones as $phone) {
                $expectedStatus = $phone->currentIssuance ? 'issued' : 'available';
                if ($phone->status !== $expectedStatus) {
                    $phone->update(['status' => $expectedStatus]);
                    $phonesUpdated++;
                }
            }
        });

        MiniPc::with(['issuances.pullout'])->chunkById(100, function ($miniPcs) use (&$miniPcsUpdated) {
            foreach ($miniPcs as $miniPc) {
                $activeIssuance = $miniPc->issuances->first(function ($issuance) {
                    return $issuance->pullout === null;
                });
                $expectedStatus = $activeIssuance ? 'issued' : 'available';
                if ($miniPc->status !== $expectedStatus) {
                    $miniPc->update(['status' => $expectedStatus]);
                    $miniPcsUpdated++;
                }
            }
        });

        return back()->with(
            'success',
            "Asset status reconciliation complete. Phones updated: {$phonesUpdated}, Mini PCs updated: {$miniPcsUpdated}."
        );
    }
}
