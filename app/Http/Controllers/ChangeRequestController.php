<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ChangeRequestController extends Controller
{
    /**
     * Display a listing of the user's change requests.
     */
    public function index(): Response
    {
        return Inertia::render('ChangeRequest/Index', [
            'changeRequests' => [],
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
    public function store(Request $request): RedirectResponse
    {
        return Redirect::route('change-requests.index');
    }

    /**
     * Show the form for editing the specified change request.
     */
    public function edit(ChangeRequest $changeRequest): Response
    {
        return Inertia::render('ChangeRequest/Edit', [
            'changeRequest' => $changeRequest,
        ]);
    }

    /**
     * Update the specified change request in storage.
     */
    public function update(Request $request, ChangeRequest $changeRequest): RedirectResponse
    {
        return Redirect::route('change-requests.index');
    }

    /**
     * Remove the specified change request from storage.
     */
    public function destroy(ChangeRequest $changeRequest): RedirectResponse
    {
        return Redirect::route('change-requests.index');
    }
}
