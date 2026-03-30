<?php

namespace App\Events;

use App\Models\DesignRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DesignRequestAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $designRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(DesignRequest $designRequest)
    {
        $this->designRequest = $designRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
