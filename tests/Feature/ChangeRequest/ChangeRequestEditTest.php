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
        'title_current' => 'Original title',
        'title_proposed' => 'Updated title',
        'description_current' => 'Original description',
        'description_proposed' => 'Updated description',
    ]);

    $response->assertRedirect(route('change-requests.index'));
    $cr->refresh();
    expect($cr->title)->toBe('Updated title');
    expect($cr->description)->toBe('Updated description');
});

test('validation requires at least one proposed value on update', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('change-requests.update', $cr), [
        'title_current' => '',
        'title_proposed' => '',
        'description_current' => '',
        'description_proposed' => '',
    ]);

    $response->assertSessionHasErrors('title_proposed');
});

test('user cannot edit another users change request', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->put(route('change-requests.update', $cr), [
        'title_proposed' => 'Hacked title',
        'description_proposed' => 'Hacked description',
    ]);

    $response->assertForbidden();
});

test('guest cannot update a change request', function () {
    $cr = ChangeRequest::factory()->create();

    $response = $this->put(route('change-requests.update', $cr), [
        'title_proposed' => 'Test',
        'description_proposed' => 'Test',
    ]);

    $response->assertRedirect(route('login'));
});
