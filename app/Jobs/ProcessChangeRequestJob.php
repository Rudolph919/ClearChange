<?php

namespace App\Jobs;

use App\Models\ChangeRequest;
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

        $this->changeRequest->update(['status' => ChangeRequest::STATUS_PROCESSING]);

        foreach ($this->changeRequest->items as $item) {
            // Placeholder: in a real system this would apply the change (API call, DB update, etc.)
            // The payload (field_name, old_value, new_value) is ready for extension
        }

        $this->changeRequest->update([
            'status' => ChangeRequest::STATUS_COMPLETED,
            'failure_message' => null,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->changeRequest->update([
            'status' => ChangeRequest::STATUS_FAILED,
            'failure_message' => $exception?->getMessage(),
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
