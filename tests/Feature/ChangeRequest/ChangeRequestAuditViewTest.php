<?php

use App\Models\AuditLog;
use App\Models\ChangeRequest;
use App\Models\User;

test('owner can view audit log of their change request', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $cr = ChangeRequest::factory()->create(['user_id' => $user->id]);
    AuditLog::create([
        'user_id' => $user->id,
        'auditable_type' => ChangeRequest::class,
        'auditable_id' => $cr->id,
        'action' => 'created',
        'new_values' => ['title' => $cr->title, 'status' => 'draft'],
    ]);

    $response = $this->actingAs($user)->get(route('change-requests.audit', $cr));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('ChangeRequest/Audit')
        ->has('changeRequest')
        ->has('auditLogs', 1)
    );
});

test('user with view audit permission can view another users change request audit', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $viewer->givePermissionTo('view audit logs');
    $cr = ChangeRequest::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($viewer)->get(route('change-requests.audit', $cr));

    $response->assertStatus(200);
});

test('user without permission cannot view another users audit log', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $cr = ChangeRequest::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->get(route('change-requests.audit', $cr));

    $response->assertForbidden();
});

test('guest cannot view audit log', function () {
    $cr = ChangeRequest::factory()->create();

    $response = $this->get(route('change-requests.audit', $cr));

    $response->assertRedirect(route('login'));
});
