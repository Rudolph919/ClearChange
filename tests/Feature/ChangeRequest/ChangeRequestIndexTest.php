<?php

use App\Models\ChangeRequest;
use App\Models\User;

test('authenticated user can view change requests index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('change-requests.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('ChangeRequest/Index')
        ->has('changeRequests')
    );
});

test('index displays only users change requests (drafts, submitted, approved)', function () {
    $user = User::factory()->create();
    $ownDraft = ChangeRequest::factory()->create(['user_id' => $user->id]);
    $ownSubmitted = ChangeRequest::factory()->create([
        'user_id' => $user->id,
        'status' => ChangeRequest::STATUS_SUBMITTED,
    ]);
    $otherUser = User::factory()->create();
    ChangeRequest::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('change-requests.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('ChangeRequest/Index')
        ->has('changeRequests', 2)
    );
    $ids = collect($response->inertiaProps('changeRequests'))->pluck('id')->toArray();
    expect($ids)->toContain($ownDraft->id);
    expect($ids)->toContain($ownSubmitted->id);
});

test('guest cannot view change requests index', function () {
    $response = $this->get(route('change-requests.index'));

    $response->assertRedirect(route('login'));
});
