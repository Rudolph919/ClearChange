<?php

use App\Models\ChangeRequest;
use App\Models\User;

test('owner can retry a failed change request', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create([
        'user_id' => $user->id,
        'status' => ChangeRequest::STATUS_FAILED,
        'failure_message' => 'Previous processing failed',
    ]);

    $response = $this->actingAs($user)->post(route('change-requests.retry', $cr));

    $response->assertRedirect(route('change-requests.index'));
    $cr->refresh();
    // With sync queue, job runs immediately and completes
    expect($cr->status)->toBe(ChangeRequest::STATUS_COMPLETED);
    expect($cr->failure_message)->toBeNull();
});

test('user cannot retry another users failed change request', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $cr = ChangeRequest::factory()->create([
        'user_id' => $owner->id,
        'status' => ChangeRequest::STATUS_FAILED,
    ]);

    $response = $this->actingAs($other)->post(route('change-requests.retry', $cr));

    $response->assertForbidden();
});

test('user cannot retry a non-failed change request', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create([
        'user_id' => $user->id,
        'status' => ChangeRequest::STATUS_COMPLETED,
    ]);

    $response = $this->actingAs($user)->post(route('change-requests.retry', $cr));

    $response->assertForbidden();
});

test('guest cannot retry a change request', function () {
    $cr = ChangeRequest::factory()->create([
        'status' => ChangeRequest::STATUS_FAILED,
    ]);

    $response = $this->post(route('change-requests.retry', $cr));

    $response->assertRedirect(route('login'));
});
