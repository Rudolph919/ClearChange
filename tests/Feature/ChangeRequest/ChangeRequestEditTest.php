<?php

use App\Models\ChangeRequest;
use App\Models\User;

test('authenticated user can edit their draft change request', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create([
        'user_id' => $user->id,
        'title' => 'Original title',
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($user)->put(route('change-requests.update', $cr), [
        'title' => 'Updated title',
        'description' => 'Updated description',
    ]);

    $response->assertRedirect(route('change-requests.index'));
    $cr->refresh();
    expect($cr->title)->toBe('Updated title');
    expect($cr->description)->toBe('Updated description');
});

test('validation requires title on update', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('change-requests.update', $cr), [
        'title' => '',
        'description' => 'Some description',
    ]);

    $response->assertSessionHasErrors('title');
});

test('user cannot edit another users change request', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->put(route('change-requests.update', $cr), [
        'title' => 'Hacked title',
        'description' => 'Hacked description',
    ]);

    $response->assertForbidden();
});

test('guest cannot update a change request', function () {
    $cr = ChangeRequest::factory()->create();

    $response = $this->put(route('change-requests.update', $cr), [
        'title' => 'Test',
        'description' => 'Test',
    ]);

    $response->assertRedirect(route('login'));
});
