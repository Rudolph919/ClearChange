<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Note: WithoutModelEvents is deliberately not used so that the ChangeRequestObserver
     * can create audit log entries when DemoSeeder creates and updates change requests.
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);
    }
}
