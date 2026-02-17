<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChangeRequestRequest;
use App\Http\Requests\UpdateChangeRequestRequest;
use App\Models\ChangeRequest;
use App\Models\ChangeRequestItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ChangeRequestController extends Controller
{
    /**
     * Display a listing of the user's change requests (drafts, submitted, approved).
     */
    public function index(): Response
    {
        $changeRequests = ChangeRequest::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', [
                ChangeRequest::STATUS_DRAFT,
                ChangeRequest::STATUS_SUBMITTED,
                ChangeRequest::STATUS_APPROVED,
                ChangeRequest::STATUS_PROCESSING,
                ChangeRequest::STATUS_COMPLETED,
            ])
            ->latest()
            ->get();

        return Inertia::render('ChangeRequest/Index', [
            'changeRequests' => $changeRequests,
        ]);
    }

    /**
     * Display change requests pending the current user's approval.
     */
    public function pendingApproval(): Response
    {
        $changeRequests = ChangeRequest::query()
            ->where('user_id', '!=', auth()->id())
            ->where('status', ChangeRequest::STATUS_SUBMITTED)
            ->with('user')
            ->latest()
            ->get();

        return Inertia::render('ChangeRequest/PendingApproval', [
            'changeRequests' => $changeRequests,
        ]);
    }

    /**
     * Submit a draft change request (draft → submitted).
     */
    public function submit(ChangeRequest $changeRequest): RedirectResponse
    {
        $this->authorize('submit', $changeRequest);

        $changeRequest->update(['status' => ChangeRequest::STATUS_SUBMITTED]);

        return Redirect::route('change-requests.index')
            ->with('status', 'Change request submitted.');
    }

    /**
     * Approve a submitted change request (submitted → approved) and process it.
     */
    public function approve(ChangeRequest $changeRequest): RedirectResponse
    {
        $this->authorize('approve', $changeRequest);

        $changeRequest->update(['status' => ChangeRequest::STATUS_APPROVED]);

        $this->processChangeRequest($changeRequest);

        return Redirect::route('change-requests.pending-approval')
            ->with('status', 'Change request approved and processed.');
    }

    /**
     * Display the audit log for a change request.
     */
    public function audit(ChangeRequest $changeRequest): Response
    {
        $this->authorize('viewAudit', $changeRequest);

        $auditLogs = $changeRequest->auditLogs()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('ChangeRequest/Audit', [
            'changeRequest' => $changeRequest,
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * Show the form for creating a new change request.
     */
    public function create(): Response
    {
        return Inertia::render('ChangeRequest/Create');
    }

    /**
     * Store a newly created change request in storage.
     */
    public function store(StoreChangeRequestRequest $request): RedirectResponse
    {
        $titleProposed = $request->validated('title_proposed');
        $descriptionProposed = $request->validated('description_proposed');

        $changeRequest = $request->user()->changeRequests()->create([
            'title' => $titleProposed ?: ($descriptionProposed ? '(Description change)' : '(Untitled)'),
            'description' => $descriptionProposed,
            'status' => ChangeRequest::STATUS_DRAFT,
        ]);

        $this->syncItemsFromRequest($changeRequest, $request->validated());

        return Redirect::route('change-requests.index')
            ->with('status', 'Change request created.');
    }

    /**
     * Show the form for editing the specified change request.
     */
    public function edit(ChangeRequest $changeRequest): Response
    {
        $this->authorize('update', $changeRequest);

        $changeRequest->load('items');

        return Inertia::render('ChangeRequest/Edit', [
            'changeRequest' => $changeRequest,
        ]);
    }

    /**
     * Update the specified change request in storage.
     */
    public function update(UpdateChangeRequestRequest $request, ChangeRequest $changeRequest): RedirectResponse
    {
        $this->authorize('update', $changeRequest);

        $titleProposed = $request->validated('title_proposed');
        $descriptionProposed = $request->validated('description_proposed');

        $changeRequest->update([
            'title' => $titleProposed ?: ($descriptionProposed ? '(Description change)' : '(Untitled)'),
            'description' => $descriptionProposed,
        ]);

        $this->syncItemsFromRequest($changeRequest, $request->validated());

        return Redirect::route('change-requests.index')
            ->with('status', 'Change request updated.');
    }

    /**
     * Remove the specified change request from storage.
     */
    public function destroy(ChangeRequest $changeRequest): RedirectResponse
    {
        $this->authorize('delete', $changeRequest);

        $changeRequest->delete();

        return Redirect::route('change-requests.index')
            ->with('status', 'Change request deleted.');
    }

    /**
     * Process an approved change request (processing → completed).
     * Uses items as the payload; creates items from title/description if none exist (legacy CRs).
     */
    private function processChangeRequest(ChangeRequest $changeRequest): void
    {
        $changeRequest->load('items');

        if ($changeRequest->items->isEmpty()) {
            $this->ensureItemsFromLegacyFields($changeRequest);
            $changeRequest->unsetRelation('items')->load('items');
        }

        $changeRequest->update(['status' => ChangeRequest::STATUS_PROCESSING]);

        foreach ($changeRequest->items as $item) {
            // Placeholder: in a real system this would apply the change (API call, DB update, etc.)
            // For now we simply mark each item as "processed" by iterating — the payload is ready for extension
        }

        $changeRequest->update(['status' => ChangeRequest::STATUS_COMPLETED]);
    }

    /**
     * Backfill items from title/description for change requests created before item-based forms.
     */
    private function ensureItemsFromLegacyFields(ChangeRequest $changeRequest): void
    {
        $items = [];
        if ($changeRequest->title) {
            $items[] = ['field_name' => 'title', 'old_value' => null, 'new_value' => $changeRequest->title];
        }
        if ($changeRequest->description) {
            $items[] = ['field_name' => 'description', 'old_value' => null, 'new_value' => $changeRequest->description];
        }
        foreach ($items as $item) {
            $changeRequest->items()->create($item);
        }
    }

    /**
     * Sync change request items from validated form data.
     */
    private function syncItemsFromRequest(ChangeRequest $changeRequest, array $validated): void
    {
        $changeRequest->items()->delete();

        $items = [];

        if (($proposed = $validated['title_proposed'] ?? '') !== '') {
            $items[] = [
                'field_name' => 'title',
                'old_value' => $validated['title_current'] ?? null,
                'new_value' => $proposed,
            ];
        }
        if (($proposed = $validated['description_proposed'] ?? '') !== '') {
            $items[] = [
                'field_name' => 'description',
                'old_value' => $validated['description_current'] ?? null,
                'new_value' => $proposed,
            ];
        }

        foreach ($items as $item) {
            $changeRequest->items()->create($item);
        }
    }
}
