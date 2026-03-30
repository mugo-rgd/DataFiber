<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\DesignRequestAssigned;
use App\Listeners\SendDesignerAssignmentNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
    DesignRequestAssigned::class => [
        SendDesignerAssignmentNotification::class,
    ],
    DesignRequestStatusChanged::class => [
        // You can add listeners for status changes here
        // SendStatusChangeNotification::class,
    ],
];
    /**
     * The model observers for your application.
     *
     * @var array<class-string, class-string>
     */
    protected $observers = [
        \App\Models\DesignRequest::class => \App\Observers\DesignRequestObserver::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be discovered automatically.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
