<?php

use App\Models\ChangeRequest;
use App\Models\User;

test('authenticated user can delete their draft change request', function () {
    $user = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('change-requests.destroy', $cr));

    $response->assertRedirect(route('change-requests.index'));
    $this->assertModelMissing($cr);
});

test('user cannot delete another users change request', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->delete(route('change-requests.destroy', $cr));

    $response->assertForbidden();
    $this->assertModelExists($cr);
});

test('guest cannot delete a change request', function () {
    $cr = ChangeRequest::factory()->create();

    $response = $this->delete(route('change-requests.destroy', $cr));

    $response->assertRedirect(route('login'));
});
