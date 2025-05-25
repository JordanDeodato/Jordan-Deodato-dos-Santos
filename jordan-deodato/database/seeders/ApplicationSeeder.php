<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = User::where('user_type_id', 2)->get();
        $vacancy = Vacancy::first();

        if ($candidates->isEmpty() || !$vacancy) {
            $this->command->warn('Necessário ao menos um candidato e uma vaga para criar aplicações.');
            return;
        }

        foreach ($candidates as $candidate) {
            Application::factory()->create([
                'candidate_uuid' => $candidate->uuid,
                'vacancy_uuid' => $vacancy->uuid,
            ]);
        }
    }
}
