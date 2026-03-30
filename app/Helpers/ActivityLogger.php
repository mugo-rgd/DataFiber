<?php

namespace App\Helpers;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLogger
{
    protected ?string $logName = null;
    protected ?Model $subject = null;
    protected ?Model $causer = null;
    protected array $properties = [];
    protected ?string $event = null;
    protected ?string $batchUuid = null;

    /**
     * Create a new ActivityLogger instance.
     */
    public function __construct(string $logName = null)
    {
        if ($logName) {
            $this->logName = $logName;
        }

        // Auto-set batch UUID if we're in a batch
        if (app()->has('activity-batch-uuid')) {
            $this->batchUuid = app('activity-batch-uuid');
        }
    }

    /**
     * Set the log name.
     */
    public function inLog(string $logName): self
    {
        $this->logName = $logName;
        return $this;
    }

    /**
     * Set the subject (model the activity is performed on).
     */
    public function performedOn(?Model $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set the causer (who performed the activity).
     */
    public function causedBy(?Model $causer): self
    {
        $this->causer = $causer;
        return $this;
    }

    /**
     * Set additional properties.
     */
    public function withProperties(array $properties): self
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * Set a single property.
     */
    public function withProperty(string $key, $value): self
    {
        $this->properties[$key] = $value;
        return $this;
    }

    /**
     * Set the event type.
     */
    public function event(string $event): self
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Set batch UUID for grouping related activities.
     */
    public function withBatch(string $batchUuid): self
    {
        $this->batchUuid = $batchUuid;
        return $this;
    }

    /**
     * Log the activity.
     */
    public function log(string $description): Activity
    {
        $activity = new Activity([
            'log_name' => $this->logName,
            'description' => $description,
            'event' => $this->event,
            'batch_uuid' => $this->batchUuid ?? Str::uuid()->toString(),
            'properties' => $this->properties,
        ]);

        if ($this->subject) {
            $activity->subject()->associate($this->subject);
        }

        if ($this->causer) {
            $activity->causer()->associate($this->causer);
        }

        $activity->save();

        // Reset the logger for next use
        $this->reset();

        return $activity;
    }

    /**
     * Reset the logger state.
     */
    protected function reset(): void
    {
        $this->logName = null;
        $this->subject = null;
        $this->causer = null;
        $this->properties = [];
        $this->event = null;
        $this->batchUuid = null;
    }

    /**
     * Create a batch of activities.
     */
    public static function batch(callable $callback): string
    {
        $batchUuid = Str::uuid()->toString();

        app()->instance('activity-batch-uuid', $batchUuid);

        try {
            $callback();
        } finally {
            app()->forgetInstance('activity-batch-uuid');
        }

        return $batchUuid;
    }
}
