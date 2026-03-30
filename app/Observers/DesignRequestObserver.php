<?php

namespace App\Observers;

use App\Models\DesignRequest;
use App\Models\DesignRequestStatusHistory;
use App\Events\DesignRequestStatusChanged;
use App\Events\DesignRequestAssigned;
use Illuminate\Support\Str;

class DesignRequestObserver
{
    /**
     * Handle the DesignRequest "creating" event.
     */
    public function creating(DesignRequest $designRequest): void
    {
        // Generate request number if empty
        if (empty($designRequest->request_number)) {
            $designRequest->request_number = 'DR-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));
        }

        // Set default status if not provided
        if (empty($designRequest->status)) {
            $designRequest->status = 'pending';
        }
    }

    /**
     * Handle the DesignRequest "created" event.
     */
    public function created(DesignRequest $designRequest): void
    {
        // Log initial status
        DesignRequestStatusHistory::create([
            'design_request_id' => $designRequest->id,
            'status' => $designRequest->status,
            'changed_by' => $designRequest->assigned_to ?? auth()->id() ?? 1,
            'notes' => 'Design request created'
        ]);

        // If designer is assigned on creation, fire assignment event
        if ($designRequest->assigned_to) {
            event(new DesignRequestAssigned($designRequest));
        }
    }

    /**
     * Handle the DesignRequest "updating" event.
     */
    public function updating(DesignRequest $designRequest): void
    {
        // Update estimated cost when cost-related fields change
        $costRelatedFields = ['unit_cost', 'distance', 'total_distance', 'tax_rate'];
        if ($designRequest->isDirty($costRelatedFields)) {
            $designRequest->updateEstimatedCost();
        }
    }

    /**
     * Handle the DesignRequest "updated" event.
     */
    public function updated(DesignRequest $designRequest): void
    {
        // Log status changes
        if ($designRequest->isDirty('status')) {
            $oldStatus = $designRequest->getOriginal('status');
            $newStatus = $designRequest->status;

            // Create status history record
            DesignRequestStatusHistory::create([
                'design_request_id' => $designRequest->id,
                'status' => $newStatus,
                'changed_by' => auth()->id() ?? $designRequest->assigned_to ?? 1,
                'notes' => "Status changed from {$oldStatus} to {$newStatus}"
            ]);

            // Fire status change event
            event(new DesignRequestStatusChanged($designRequest, $oldStatus, $newStatus));
        }

        // Log and fire event for assignment changes
        if ($designRequest->isDirty('assigned_to')) {
            $oldAssignee = $designRequest->getOriginal('assigned_to');
            $newAssignee = $designRequest->assigned_to;

            // Log the assignment change
            $historyNotes = $oldAssignee
                ? "Assignment changed from user #{$oldAssignee} to user #{$newAssignee}"
                : "Design request assigned to user #{$newAssignee}";

            DesignRequestStatusHistory::create([
                'design_request_id' => $designRequest->id,
                'status' => $designRequest->status,
                'changed_by' => auth()->id() ?? 1,
                'notes' => $historyNotes
            ]);

            // Fire assignment event if a new designer is assigned (not unassigned)
            if ($newAssignee) {
                event(new DesignRequestAssigned($designRequest));
            }
        }
    }

    /**
     * Handle the DesignRequest "deleted" event.
     */
    public function deleted(DesignRequest $designRequest): void
    {
        // Log deletion in status history
        DesignRequestStatusHistory::create([
            'design_request_id' => $designRequest->id,
            'status' => 'deleted',
            'changed_by' => auth()->id() ?? 1,
            'notes' => 'Design request deleted'
        ]);
    }

    /**
     * Handle the DesignRequest "restored" event.
     */
    public function restored(DesignRequest $designRequest): void
    {
        // Log restoration in status history
        DesignRequestStatusHistory::create([
            'design_request_id' => $designRequest->id,
            'status' => $designRequest->status,
            'changed_by' => auth()->id() ?? 1,
            'notes' => 'Design request restored from trash'
        ]);
    }

    /**
     * Handle the DesignRequest "force deleted" event.
     */
    public function forceDeleted(DesignRequest $designRequest): void
    {
        // Optionally delete related status history records
        DesignRequestStatusHistory::where('design_request_id', $designRequest->id)->delete();
    }
}
