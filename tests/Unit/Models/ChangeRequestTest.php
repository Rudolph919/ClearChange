<?php

use App\Models\AuditLog;
use App\Models\ChangeRequest;
use App\Models\User;

test('change request has draft status by default', function () {
    $request = ChangeRequest::factory()->create();
    expect($request->status)->toBe('draft');
});

test('change request belongs to user', function () {
    $user = User::factory()->create();
    $request = ChangeRequest::factory()->create(['user_id' => $user->id]);

    expect($request->user)->toBeInstanceOf(User::class)
        ->and($request->user->id)->toBe($user->id);
});

test('change request has fillable attributes', function () {
    $user = User::factory()->create();
    $request = ChangeRequest::create([
        'user_id' => $user->id,
        'title' => 'Update employee salary',
        'status' => 'draft',
    ]);

    expect($request->title)->toBe('Update employee salary')
        ->and($request->status)->toBe('draft');
});

test('change request factory creates draft by default', function () {
    $request = ChangeRequest::factory()->create();

    expect($request->status)->toBe('draft')
        ->and($request->user_id)->not->toBeNull()
        ->and($request->title)->not->toBeEmpty();
});

test('change request has audit logs relation', function () {
    $cr = ChangeRequest::factory()->create();
    AuditLog::create([
        'user_id' => $cr->user_id,
        'auditable_type' => ChangeRequest::class,
        'auditable_id' => $cr->id,
        'action' => 'created',
        'new_values' => ['title' => $cr->title],
    ]);

    expect($cr->auditLogs)->toHaveCount(1)
        ->and($cr->auditLogs->first()->action)->toBe('created');
});
