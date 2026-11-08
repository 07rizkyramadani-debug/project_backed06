<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Membership\Models\Member;
use Modules\Membership\Models\MemberRole;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'member_role_id' => MemberRole::query()->value('id')
                ?? MemberRole::create(['name' => 'Mahasiswa', 'slug' => 'mahasiswa', 'max_loans' => 3, 'loan_days' => 7])->id,
            'member_number' => 'AGT-' . fake()->unique()->numerify('######'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('08##########'),
            'status' => 'active',
            'active_loans_count' => 0,
            'joined_at' => now(),
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => 'suspended']);
    }

    /** Buat keanggotaan aktif setelah anggota dibuat. */
    public function withMembership(string $type = 'regular', int $months = 12): static
    {
        return $this->afterCreating(function (Member $member) use ($type, $months) {
            $member->memberships()->create([
                'type' => $type,
                'starts_at' => now()->toDateString(),
                'ends_at' => now()->addMonths($months)->toDateString(),
                'status' => 'active',
            ]);
        });
    }
}
