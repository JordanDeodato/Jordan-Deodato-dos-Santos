<?php

namespace Database\Seeders;

use App\Models\VacancyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VacancyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VacancyType::factory()->create([
            'name' => 'CLT',
        ]);

        VacancyType::factory()->create([
            'name' => 'Pessoa Jurídica',
        ]);

        VacancyType::factory()->create([
            'name' => 'Freelancer',
        ]);
    }
}
