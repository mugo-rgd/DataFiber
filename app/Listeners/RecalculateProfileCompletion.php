<?php

namespace App\Listeners;

use App\Events\DocumentsUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RecalculateProfileCompletion
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
    public function handle(DocumentsUploaded $event): void
    {
        $user = $event->user;

        // Call your profile completion calculation method here
        // This depends on where your calculation logic is located

        // Option 1: If it's in a service class
        // app(ProfileCompletionService::class)->calculateForUser($user->id);

        // Option 2: If it's in a model method
        // $user->calculateProfileCompletion();

        // Option 3: If it's a helper function
        // calculateProfileCompletion($user->id);

        Log::info('Profile completion recalculated after document upload', [
            'user_id' => $user->id
        ]);
    }
}
