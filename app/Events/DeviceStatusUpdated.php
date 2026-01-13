<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\NetworkDevice;

class DeviceStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The device instance.
     *
     * @var \App\Models\NetworkDevice
     */
    public $device;

    /**
     * Create a new event instance.
     */
    public function __construct(NetworkDevice $device)
    {
        $this->device = $device;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('network-status');
    }

    /**
     * Optional: rename the broadcast payload key to 'device' (default is property name)
     */
    public function broadcastWith()
    {
        return ['device' => $this->device->toArray()];
    }
}
