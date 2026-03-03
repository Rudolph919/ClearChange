<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\ChangeRequest;

class ChangeRequestObserver
{
    /**
     * Tracked attributes for audit logging.
     */
    private const TRACKED_ATTRIBUTES = ['title', 'description', 'status'];

    /**
     * Handle the ChangeRequest "created" event.
     */
    public function created(ChangeRequest $changeRequest): void
    {
        $this->log(
            $changeRequest,
            'created',
            null,
            $this->getTrackedValues($changeRequest),
        );
    }

    /**
     * Handle the ChangeRequest "updating" event (before save).
     * Use "updating" rather than "updated" because by the time "updated" fires,
     * Laravel has synced the model and getOriginal()/wasChanged() no longer
     * reflect the previous state. In "updating", the old values are still available.
     */
    public function updating(ChangeRequest $changeRequest): void
    {
        $oldValues = [];
        $newValues = [];

        foreach (self::TRACKED_ATTRIBUTES as $key) {
            if ($changeRequest->isDirty($key)) {
                $oldValues[$key] = $changeRequest->getOriginal($key);
                $newValues[$key] = $changeRequest->getAttribute($key);
            }
        }

        if ($oldValues !== [] || $newValues !== []) {
            $this->log($changeRequest, 'updated', $oldValues, $newValues);
        }
    }

    private function log(
        ChangeRequest $changeRequest,
        string $action,
        ?array $oldValues,
        ?array $newValues,
    ): void {
        $userId = auth()->id();
        if ($userId === null) {
            return;
        }

        AuditLog::create([
            'user_id' => $userId,
            'auditable_type' => ChangeRequest::class,
            'auditable_id' => $changeRequest->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    private function getTrackedValues(ChangeRequest $changeRequest): array
    {
        $values = [];
        foreach (self::TRACKED_ATTRIBUTES as $key) {
            $values[$key] = $changeRequest->getAttribute($key);
        }

        return $values;
    }
}
