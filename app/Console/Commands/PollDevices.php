<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NetworkDevice;
use App\Jobs\PollNetworkDevice;

class PollDevices extends Command
{
    protected $signature = 'network:poll';

    protected $description = 'Dispatch PollNetworkDevice jobs for all configured devices';

    public function handle()
    {
        $devices = NetworkDevice::all();

        foreach ($devices as $device) {
            PollNetworkDevice::dispatch($device);
        }

        $this->info('Dispatched poll for ' . $devices->count() . ' devices.');

        return 0;
    }
}
