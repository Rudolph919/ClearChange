<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChangeRequestRequest;
use App\Http\Requests\UpdateChangeRequestRequest;
use App\Models\ChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ChangeRequestController extends Controller
{
    /**
     * Display a listing of the user's draft change requests.
     */
    public function index(): Response
    {
        $changeRequests = ChangeRequest::query()
            ->where('user_id', auth()->id())
            ->where('status', ChangeRequest::STATUS_DRAFT)
            ->latest()
            ->get();

        return Inertia::render('ChangeRequest/Index', [
            'changeRequests' => $changeRequests,
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
        $request->user()->changeRequests()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'status' => ChangeRequest::STATUS_DRAFT,
        ]);

        return Redirect::route('change-requests.index')
            ->with('status', 'Change request created.');
    }

    /**
     * Show the form for editing the specified change request.
     */
    public function edit(ChangeRequest $changeRequest): Response
    {
        $this->authorize('update', $changeRequest);

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

        $changeRequest->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
        ]);

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
}
