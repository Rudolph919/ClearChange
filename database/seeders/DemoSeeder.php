<?php

namespace Database\Seeders;

use App\Jobs\ProcessChangeRequestJob;
use App\Models\AuditLog;
use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Seed the database with demo data showcasing the full application workflow.
     *
     * Creates users and change requests in every state: draft, submitted,
     * completed, and failed. Use these credentials to explore:
     * - alice@example.com (user) - owns most requests
     * - bob@example.com (admin) - can approve, sees Pending my approval
     * - carol@example.com (user) - owns the failed request
     * Password for all: password
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $alice = User::updateOrCreate(
            ['email' => 'alice@example.com'],
            [
                'name' => 'Alice',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $alice->syncRoles(['user']);

        $bob = User::updateOrCreate(
            ['email' => 'bob@example.com'],
            [
                'name' => 'Bob',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $bob->syncRoles(['admin']);

        $carol = User::updateOrCreate(
            ['email' => 'carol@example.com'],
            [
                'name' => 'Carol',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $carol->syncRoles(['user']);

        // 1. Draft — Alice has a change request she's still editing
        Auth::login($alice);
        $draft = $alice->changeRequests()->create([
            'title' => 'Update product pricing',
            'description' => 'Apply new tier structure to enterprise plan',
            'status' => ChangeRequest::STATUS_DRAFT,
        ]);
        $draft->items()->create([
            'field_name' => 'title',
            'old_value' => 'Legacy Enterprise Plan',
            'new_value' => 'Enterprise Plan v2',
        ]);
        $draft->items()->create([
            'field_name' => 'description',
            'old_value' => 'Basic enterprise features',
            'new_value' => 'Apply new tier structure to enterprise plan',
        ]);
        Auth::logout();

        // 2. Submitted — Alice created as draft, then submitted; Bob can approve
        Auth::login($alice);
        $submitted = $alice->changeRequests()->create([
            'title' => 'Rename Marketing department',
            'description' => 'Align with company rebrand',
            'status' => ChangeRequest::STATUS_DRAFT,
        ]);
        $submitted->items()->create([
            'field_name' => 'title',
            'old_value' => 'Marketing',
            'new_value' => 'Growth & Brand',
        ]);
        $submitted->items()->create([
            'field_name' => 'description',
            'old_value' => 'Marketing team',
            'new_value' => 'Align with company rebrand',
        ]);
        $submitted->update(['status' => ChangeRequest::STATUS_SUBMITTED]);
        Auth::logout();

        // 3. Completed — Full flow: draft → submitted → approved → processed
        Auth::login($alice);
        $completed = $alice->changeRequests()->create([
            'title' => 'Extend API rate limit',
            'description' => 'Increase from 100 to 500 requests/minute',
            'status' => ChangeRequest::STATUS_DRAFT,
        ]);
        $completed->items()->create([
            'field_name' => 'description',
            'old_value' => '100 requests/minute',
            'new_value' => '500 requests/minute',
        ]);
        $completed->update(['status' => ChangeRequest::STATUS_SUBMITTED]);
        Auth::logout();

        Auth::login($bob);
        $completed->update(['status' => ChangeRequest::STATUS_APPROVED]);
        ProcessChangeRequestJob::dispatchSync($completed);
        Auth::logout();

        // 4. Failed — Shows Retry button; owner can retry
        Auth::login($carol);
        $failed = $carol->changeRequests()->create([
            'title' => 'Sync payroll data',
            'description' => 'Push December adjustments to external system',
            'status' => ChangeRequest::STATUS_DRAFT,
        ]);
        $failed->items()->create([
            'field_name' => 'description',
            'old_value' => null,
            'new_value' => 'Push December adjustments to external system',
        ]);
        $failed->update(['status' => ChangeRequest::STATUS_SUBMITTED]);
        Auth::logout();

        Auth::login($bob);
        $failed->update(['status' => ChangeRequest::STATUS_APPROVED]);
        Auth::logout();

        // Simulate job failure — in production this happens when the job throws
        $failed->update([
            'status' => ChangeRequest::STATUS_FAILED,
            'failure_message' => 'Connection timeout: Could not reach external payroll API',
        ]);
        AuditLog::create([
            'user_id' => null,
            'auditable_type' => ChangeRequest::class,
            'auditable_id' => $failed->id,
            'action' => 'updated',
            'old_values' => ['status' => ChangeRequest::STATUS_APPROVED],
            'new_values' => ['status' => ChangeRequest::STATUS_FAILED],
        ]);
    }
}
