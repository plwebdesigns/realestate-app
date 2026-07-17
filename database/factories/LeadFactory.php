<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $budgetMin = fake()->optional()->numberBetween(100_000, 400_000);

        return [
            'name' => fake()->name(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'status' => fake()->randomElement(LeadStatus::cases()),
            'lead_source_id' => LeadSource::factory(),
            'assigned_to' => null,
            'notes' => fake()->optional()->paragraph(),
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMin !== null
                ? fake()->numberBetween($budgetMin, $budgetMin + 300_000)
                : null,
            'preferred_location' => fake()->optional()->city(),
        ];
    }

    public function assignedTo(?User $user = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'assigned_to' => $user?->id ?? User::factory(),
        ]);
    }

    public function withStatus(LeadStatus $status): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => $status,
        ]);
    }
}
