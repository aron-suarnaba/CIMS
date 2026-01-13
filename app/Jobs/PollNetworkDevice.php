<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Queue\SerializesModels;
use App\Models\NetworkDevice;

class PollNetworkDevice implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var \App\Models\NetworkDevice */
    public $device;

    /**
     * Create a new job instance.
     */
    public function __construct(NetworkDevice $device)
    {
        $this->device = $device;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Use the native PHP SNMP class enabled in Step 1
        try {
            $snmp = new \SNMP(\SNMP::VERSION_2C, $this->device->ip_address, $this->device->community ?? 'public');

            // Example OID for System Uptime
            $uptime = $snmp->get("1.3.6.1.2.1.1.3.0");
            $this->device->update(['status' => 'online', 'uptime' => $uptime]);
        } catch (\Exception $e) {
            // If SNMP fails or not available, mark offline
            $this->device->update(['status' => 'offline']);
        }

        // Broadcast the change to Vue
        broadcast(new \App\Events\DeviceStatusUpdated($this->device));
    }
}
