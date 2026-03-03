<?php

namespace App\Policies;

use App\Models\ChangeRequest;
use App\Models\User;

class ChangeRequestPolicy
{
    /**
     * Determine whether the user can update the change request.
     */
    public function update(User $user, ChangeRequest $changeRequest): bool
    {
        return $changeRequest->user_id === $user->id
            && $changeRequest->status === ChangeRequest::STATUS_DRAFT;
    }

    /**
     * Determine whether the user can delete the change request.
     */
    public function delete(User $user, ChangeRequest $changeRequest): bool
    {
        return $changeRequest->user_id === $user->id
            && $changeRequest->status === ChangeRequest::STATUS_DRAFT;
    }

    /**
     * Determine whether the user can submit the change request (draft → submitted).
     */
    public function submit(User $user, ChangeRequest $changeRequest): bool
    {
        return $changeRequest->user_id === $user->id
            && $changeRequest->status === ChangeRequest::STATUS_DRAFT;
    }

    /**
     * Determine whether the user can approve the change request (submitted → approved).
     */
    public function approve(User $user, ChangeRequest $changeRequest): bool
    {
        return $changeRequest->user_id !== $user->id
            && $changeRequest->status === ChangeRequest::STATUS_SUBMITTED;
    }

    /**
     * Determine whether the user can view the audit log for the change request.
     */
    public function viewAudit(User $user, ChangeRequest $changeRequest): bool
    {
        return $changeRequest->user_id === $user->id
            || $user->can('view audit logs');
    }

    /**
     * Determine whether the user can retry a failed change request.
     */
    public function retry(User $user, ChangeRequest $changeRequest): bool
    {
        return $changeRequest->user_id === $user->id
            && $changeRequest->status === ChangeRequest::STATUS_FAILED;
    }
}
