<?php

namespace App\Listeners;

use App\Events\DesignRequestAssigned;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDesignerAssignmentNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DesignRequestAssigned $event): void
    {
        $designRequest = $event->designRequest;

        // Get the assigned designer from the design request
        $designer = User::find($designRequest->designer_id);

        if (!$designer) {
            Log::warning('Designer not found for assignment notification', [
                'design_request_id' => $designRequest->id,
                'designer_id' => $designRequest->designer_id
            ]);
            return;
        }

        // Log the assignment
        Log::info('Design request assigned to designer', [
            'design_request_id' => $designRequest->id,
            'designer_id' => $designer->id,
            'designer_email' => $designer->email,
            'design_request_title' => $designRequest->title ?? 'Untitled'
        ]);

        // TODO: Send email notification
        // Mail::to($designer->email)->send(new \App\Mail\DesignRequestAssigned($designRequest));

        // TODO: Send database notification
        // $designer->notify(new \App\Notifications\DesignRequestAssigned($designRequest));

        // You can add additional notifications here
        // - SMS notification
        // - Push notification
        // - Slack message, etc.
    }
}
