<?php

namespace Database\Factories;

use App\Models\ChangeRequest;
use App\Models\ChangeRequestItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChangeRequestItem>
 */
class ChangeRequestItemFactory extends Factory
{
    protected $model = ChangeRequestItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'change_request_id' => ChangeRequest::factory(),
            'field_name' => fake()->word(),
            'old_value' => fake()->optional()->word(),
            'new_value' => fake()->word(),
        ];
    }
}
