<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_demo_users_and_change_requests(): void
    {
        $this->seed(\Database\Seeders\DemoSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'bob@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'carol@example.com']);

        $draft = ChangeRequest::where('title', 'Update product pricing')->first();
        $this->assertNotNull($draft);
        $this->assertSame('draft', $draft->status);

        $submitted = ChangeRequest::where('title', 'Rename Marketing department')->first();
        $this->assertNotNull($submitted);
        $this->assertSame('submitted', $submitted->status);

        $completed = ChangeRequest::where('title', 'Extend API rate limit')->first();
        $this->assertNotNull($completed);
        $this->assertSame('completed', $completed->status);

        $failed = ChangeRequest::where('title', 'Sync payroll data')->first();
        $this->assertNotNull($failed);
        $this->assertSame('failed', $failed->status);
        $this->assertSame(
            'Connection timeout: Could not reach external payroll API',
            $failed->failure_message
        );
    }
}
