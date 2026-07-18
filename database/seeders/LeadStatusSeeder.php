<?php

namespace Database\Seeders;

use App\Models\LeadStatus;
use Illuminate\Database\Seeder;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'New',
            'Contacted',
            'Qualified',
            'Negotiation',
            'Won',
            'Lost',
        ];

        foreach ($statuses as $name) {
            LeadStatus::query()->updateOrCreate(
                ['name' => $name],
                [
                    'is_active' => true,
                    'is_default' => $name === 'New',
                ],
            );
        }
    }
}
