<?php

namespace App\Http\Controllers;

use App\Models\NetworkDevice; // Your model name
use App\Jobs\PollNetworkDevice; // Ensure this is imported
use Inertia\Inertia;

class NetworkMonitoringController extends Controller
{
    public function index()
    {
        return Inertia::render('NetworkMonitoringManagement'
        // , [// Changed "Device" to "NetworkDevice" to match your import/model
        //     'initialDevices' => NetworkDevice::all(['id', 'name', 'ip_address', 'status', 'community', 'uptime'])    ]
    );
    }

    public function list()
    {
        return response()->json(NetworkDevice::all());
    }

    /**
     * Optional convenience endpoint to trigger an immediate poll.
     * Use this to test if your Reverb/Echo updates are working!
     */
    public function pollNow()
    {
        $devices = NetworkDevice::all();

        foreach ($devices as $device) {
            // Using the imported class name directly is cleaner
            PollNetworkDevice::dispatch($device);
        }

        return response()->json(['dispatched' => $devices->count()]);
    }
}
