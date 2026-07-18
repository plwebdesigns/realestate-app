<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LeadStatusSeeder::class,
            LeadSourceSeeder::class,
        ]);

        if (! app()->isProduction()) {
            User::factory()->admin()->create([
                'name' => 'Paul Longo',
                'email' => 'paullongo@outlook.com',
                'password' => Hash::make('Password123'),
            ]);

            User::factory()->create([
                'name' => 'Test Agent',
                'email' => 'agent@example.com',
                'password' => Hash::make('Password123'),
            ]);

            Lead::factory()
                ->count(20)
                ->recycle(LeadStatus::all())
                ->recycle(LeadSource::all())
                ->create();
        }
    }
}
