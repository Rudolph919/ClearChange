<?php

use App\Models\AuditLog;
use App\Models\ChangeRequest;
use App\Models\User;

test('audit log can be created with polymorphic relation', function () {
    $user = User::factory()->create();
    $changeRequest = ChangeRequest::factory()->create();

    $log = AuditLog::create([
        'user_id' => $user->id,
        'auditable_type' => ChangeRequest::class,
        'auditable_id' => $changeRequest->id,
        'action' => 'status_changed',
        'old_values' => ['status' => 'draft'],
        'new_values' => ['status' => 'submitted'],
    ]);

    expect($log->auditable_type)->toBe(ChangeRequest::class)
        ->and($log->auditable_id)->toBe($changeRequest->id)
        ->and($log->action)->toBe('status_changed');
});

test('audit log belongs to user', function () {
    $user = User::factory()->create();
    $log = AuditLog::factory()->create(['user_id' => $user->id]);

    expect($log->user)->toBeInstanceOf(User::class)
        ->and($log->user->id)->toBe($user->id);
});

test('audit log morphs to auditable model', function () {
    $changeRequest = ChangeRequest::factory()->create();
    $log = AuditLog::factory()->create([
        'auditable_type' => ChangeRequest::class,
        'auditable_id' => $changeRequest->id,
    ]);

    expect($log->auditable)->toBeInstanceOf(ChangeRequest::class)
        ->and($log->auditable->id)->toBe($changeRequest->id);
});
