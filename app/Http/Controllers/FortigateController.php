<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FortigateController extends Controller
{
    public function getSystemStatus()
    {
        $url = "https://" . config('services.fortigate.base_url') . "/api/v2/monitor/system/status";

        try {
            $response = Http::timeout(5)
                ->withoutVerifying()
                ->withToken(config('services.fortigate.token'))
                ->get($url);

            if ($response->failed()) {
                return response()->json(['error' => 'FortiGate unreachable'], $response->status());
            }

            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getNetworkDevices()
    {
        $url = "https://" . config('services.fortigate.base_url') . "/api/v2/monitor/user/device/query";

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withToken(config('services.fortigate.token'))
                ->get($url);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'FortiGate API request failed',
                    'status' => $response->status(),
                    'body' => $response->body()
                ], $response->status());
            }

            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection failed: ' . $e->getMessage()], 500);
        }
    }
}
