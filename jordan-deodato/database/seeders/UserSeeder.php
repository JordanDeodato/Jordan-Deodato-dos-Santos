<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Recrutador',
            "email" => 'recrutador@teste.com',
            "password" => Hash::make('Recrutador12345@'),
            "cpf" => substr(str_shuffle('01234567890123456789'),1,11),
            "user_type_id" => "1",
        ]);

        User::factory()->create([
            'name' => 'Candidato',
            "email" => 'candidato@teste.com',
            "password" => Hash::make('Candidato12345@'),
            "cpf" => substr(str_shuffle('01234567890123456789'),1,11),
            "user_type_id" => "2",
        ]);

        User::factory()->recruiter()->count(10)->create();
        User::factory()->candidate()->count(50)->create();
    }
}
