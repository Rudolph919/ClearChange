<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $changeRequest = ChangeRequest::factory()->create();

        return [
            'user_id' => User::factory(),
            'auditable_type' => ChangeRequest::class,
            'auditable_id' => $changeRequest->id,
            'action' => fake()->randomElement(['created', 'updated', 'status_changed', 'deleted']),
            'old_values' => ['status' => 'draft'],
            'new_values' => ['status' => 'submitted'],
        ];
    }
}
