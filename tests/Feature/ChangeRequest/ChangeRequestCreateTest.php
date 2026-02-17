<?php

use App\Models\ChangeRequest;
use App\Models\User;

test('authenticated user can create a change request', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('change-requests.store'), [
        'title_current' => '',
        'title_proposed' => 'Update employee salary',
        'description_current' => '',
        'description_proposed' => 'Increase base salary by 5%',
    ]);

    $response->assertRedirect(route('change-requests.index'));
    $this->assertDatabaseHas('change_requests', [
        'user_id' => $user->id,
        'title' => 'Update employee salary',
        'description' => 'Increase base salary by 5%',
        'status' => 'draft',
    ]);

    $cr = ChangeRequest::query()
        ->where('user_id', $user->id)
        ->latest()
        ->with('items')
        ->first();
    expect($cr->items)->toHaveCount(2);
    expect($cr->items->pluck('field_name')->toArray())->toContain('title', 'description');
});

test('validation requires at least one proposed value', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('change-requests.store'), [
        'title_current' => '',
        'title_proposed' => '',
        'description_current' => '',
        'description_proposed' => '',
    ]);

    $response->assertSessionHasErrors('title_proposed');
});

test('guest cannot create a change request', function () {
    $response = $this->post(route('change-requests.store'), [
        'title_proposed' => 'Test',
        'description_proposed' => 'Test',
    ]);

    $response->assertRedirect(route('login'));
});
