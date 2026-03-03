<?php

use App\Models\AuditLog;
use App\Models\ChangeRequest;
use App\Models\User;

test('creating a change request logs audit entry', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('change-requests.store'), [
        'title_proposed' => 'Test change',
        'description_proposed' => 'Test description',
    ]);

    $cr = ChangeRequest::query()->where('title', 'Test change')->first();
    expect($cr)->not->toBeNull();

    $log = AuditLog::query()
        ->where('auditable_type', ChangeRequest::class)
        ->where('auditable_id', $cr->id)
        ->where('action', 'created')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->user_id)->toBe($user->id)
        ->and($log->new_values)->toMatchArray([
            'title' => 'Test change',
            'description' => 'Test description',
            'status' => 'draft',
        ]);
});

test('updating a change request logs audit entry with old and new values', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create([
        'user_id' => $user->id,
        'title' => 'Original title',
        'description' => 'Original description',
    ]);

    $this->actingAs($user)->put(route('change-requests.update', $cr), [
        'title_current' => 'Original title',
        'title_proposed' => 'Updated title',
        'description_current' => 'Original description',
        'description_proposed' => 'Updated description',
    ]);

    $log = AuditLog::query()
        ->where('auditable_type', ChangeRequest::class)
        ->where('auditable_id', $cr->id)
        ->where('action', 'updated')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->user_id)->toBe($user->id)
        ->and($log->old_values)->toMatchArray([
            'title' => 'Original title',
            'description' => 'Original description',
        ])
        ->and($log->new_values)->toMatchArray([
            'title' => 'Updated title',
            'description' => 'Updated description',
        ]);
});

test('status change logs audit entry with old and new status', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);
    $cr->update(['status' => ChangeRequest::STATUS_SUBMITTED]);

    $log = AuditLog::query()
        ->where('auditable_type', ChangeRequest::class)
        ->where('auditable_id', $cr->id)
        ->where('action', 'updated')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->user_id)->toBe($user->id)
        ->and($log->old_values['status'])->toBe('draft')
        ->and($log->new_values['status'])->toBe('submitted');
});

test('audit log is not created when no user is authenticated', function () {
    $cr = ChangeRequest::factory()->create();

    $cr->update(['title' => 'New title']);

    $log = AuditLog::query()
        ->where('auditable_type', ChangeRequest::class)
        ->where('auditable_id', $cr->id)
        ->where('action', 'updated')
        ->first();

    expect($log)->toBeNull();
});
