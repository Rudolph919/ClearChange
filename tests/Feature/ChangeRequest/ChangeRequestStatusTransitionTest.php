<?php

use App\Models\ChangeRequest;
use App\Models\User;

test('owner can submit a draft change request', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('change-requests.submit', $cr));

    $response->assertRedirect(route('change-requests.index'));
    $cr->refresh();
    expect($cr->status)->toBe(ChangeRequest::STATUS_SUBMITTED);
});

test('owner cannot submit another users change request', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->post(route('change-requests.submit', $cr));

    $response->assertForbidden();
    $cr->refresh();
    expect($cr->status)->toBe(ChangeRequest::STATUS_DRAFT);
});

test('owner cannot submit an already submitted change request', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create([
        'user_id' => $user->id,
        'status' => ChangeRequest::STATUS_SUBMITTED,
    ]);

    $response = $this->actingAs($user)->post(route('change-requests.submit', $cr));

    $response->assertForbidden();
});

test('another user can approve a submitted change request', function () {
    $owner = User::factory()->create();
    $approver = User::factory()->create();
    $cr = ChangeRequest::factory()->create([
        'user_id' => $owner->id,
        'status' => ChangeRequest::STATUS_SUBMITTED,
    ]);

    $response = $this->actingAs($approver)->post(route('change-requests.approve', $cr));

    $response->assertRedirect(route('change-requests.pending-approval'));
    $cr->refresh();
    expect($cr->status)->toBe(ChangeRequest::STATUS_APPROVED);
});

test('owner cannot approve their own change request', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create([
        'user_id' => $user->id,
        'status' => ChangeRequest::STATUS_SUBMITTED,
    ]);

    $response = $this->actingAs($user)->post(route('change-requests.approve', $cr));

    $response->assertForbidden();
});

test('guest cannot submit or approve', function () {
    $cr = ChangeRequest::factory()->create();

    $this->post(route('change-requests.submit', $cr))->assertRedirect(route('login'));
    $this->post(route('change-requests.approve', $cr))->assertRedirect(route('login'));
});

test('authenticated user can view pending approval page', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    ChangeRequest::factory()->create([
        'user_id' => $other->id,
        'status' => ChangeRequest::STATUS_SUBMITTED,
    ]);

    $response = $this->actingAs($user)->get(route('change-requests.pending-approval'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('ChangeRequest/PendingApproval')
        ->has('changeRequests', 1)
    );
});

test('guest cannot view pending approval page', function () {
    $response = $this->get(route('change-requests.pending-approval'));

    $response->assertRedirect(route('login'));
});
