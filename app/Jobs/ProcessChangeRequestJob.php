<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\ChangeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessChangeRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ChangeRequest $changeRequest
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->changeRequest->load('items');

        if ($this->changeRequest->items->isEmpty()) {
            $this->ensureItemsFromLegacyFields();
            $this->changeRequest->unsetRelation('items')->load('items');
        }

        Model::withoutEvents(fn () => $this->changeRequest->update(['status' => ChangeRequest::STATUS_PROCESSING]));
        $this->auditStatusChange(ChangeRequest::STATUS_APPROVED, ChangeRequest::STATUS_PROCESSING);

        foreach ($this->changeRequest->items as $item) {
            // Placeholder: in a real system this would apply the change (API call, DB update, etc.)
            // The payload (field_name, old_value, new_value) is ready for extension
        }

        Model::withoutEvents(fn () => $this->changeRequest->update([
            'status' => ChangeRequest::STATUS_COMPLETED,
            'failure_message' => null,
        ]));
        $this->auditStatusChange(ChangeRequest::STATUS_PROCESSING, ChangeRequest::STATUS_COMPLETED);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $previousStatus = $this->changeRequest->status ?: ChangeRequest::STATUS_APPROVED;

        Model::withoutEvents(fn () => $this->changeRequest->update([
            'status' => ChangeRequest::STATUS_FAILED,
            'failure_message' => $exception?->getMessage(),
        ]));

        $this->auditStatusChange($previousStatus, ChangeRequest::STATUS_FAILED);
    }

    /**
     * Create an audit log entry for status transitions made by the background job.
     */
    private function auditStatusChange(string $from, string $to): void
    {
        AuditLog::create([
            'user_id' => null,
            'auditable_type' => ChangeRequest::class,
            'auditable_id' => $this->changeRequest->id,
            'action' => 'updated',
            'old_values' => ['status' => $from],
            'new_values' => ['status' => $to],
        ]);
    }

    private function ensureItemsFromLegacyFields(): void
    {
        $items = [];
        if ($this->changeRequest->title) {
            $items[] = ['field_name' => 'title', 'old_value' => null, 'new_value' => $this->changeRequest->title];
        }
        if ($this->changeRequest->description) {
            $items[] = ['field_name' => 'description', 'old_value' => null, 'new_value' => $this->changeRequest->description];
        }
        foreach ($items as $item) {
            $this->changeRequest->items()->create($item);
        }
    }
}
