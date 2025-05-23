<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = User::where('user_type_id', '2')->get();

        foreach ($candidates as $user) {
            Candidate::factory()->create([
                'user_uuid' => $user->uuid,
            ]);
        }
    }
}
