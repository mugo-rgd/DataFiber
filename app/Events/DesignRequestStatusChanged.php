<?php

namespace App\Events;

use App\Models\DesignRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DesignRequestStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $designRequest;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(DesignRequest $designRequest, $oldStatus, $newStatus)
    {
        $this->designRequest = $designRequest;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
