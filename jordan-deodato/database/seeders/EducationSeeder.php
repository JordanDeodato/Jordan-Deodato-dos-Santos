<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            'Ensino Médio Incompleto',
            'Ensino Médio Completo',
            'Ensino Superior Incompleto',
            'Ensino Superior Completo',
            'Pós-graduação',
            'Mestrado',
            'Doutorado',
        ];

        foreach ($options as $educationName) {
            Education::create([
                'name' => $educationName,
            ]);
        }
    }
}
