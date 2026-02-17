<?php

use App\Models\User;

test('authenticated user can create a change request', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('change-requests.store'), [
        'title' => 'Update employee salary',
        'description' => 'Increase base salary by 5%',
    ]);

    $response->assertRedirect(route('change-requests.index'));
    $this->assertDatabaseHas('change_requests', [
        'user_id' => $user->id,
        'title' => 'Update employee salary',
        'description' => 'Increase base salary by 5%',
        'status' => 'draft',
    ]);
});

test('validation requires title', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('change-requests.store'), [
        'title' => '',
        'description' => 'Some description',
    ]);

    $response->assertSessionHasErrors('title');
});

test('guest cannot create a change request', function () {
    $response = $this->post(route('change-requests.store'), [
        'title' => 'Test',
        'description' => 'Test',
    ]);

    $response->assertRedirect(route('login'));
});
