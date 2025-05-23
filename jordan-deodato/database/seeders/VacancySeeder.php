<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recruiter = User::where('user_type_id', 1)->first();

        if (!$recruiter) {
            $this->command->warn('Nenhum recrutador encontrado para criar as vagas.');
            return;
        }

        Vacancy::factory()->create([
            'name' => 'Dev Backend Laravel Pleno',
            "description" => 'Vaga para desenvolvedor Laravel pleno',
            "vacancy_type_id" => "1",
            'recruiter_uuid' => $recruiter->uuid,
            "opened" => true,
        ]);

        Vacancy::factory()->create([
            'name' => 'Dev Frontend Angular Pleno',
            "description" => 'Vaga para desenvolvedor Angular pleno',
            "vacancy_type_id" => "2",
            'recruiter_uuid' => $recruiter->uuid,
            "opened" => true,
        ]);

        Vacancy::factory()->create([
            'name' => 'Dev Backend Python Sênior',
            "description" => 'Vaga para desenvolvedor Python sênior',
            "vacancy_type_id" => "3",
            'recruiter_uuid' => $recruiter->uuid,
            "opened" => true,
        ]);
    }
}
