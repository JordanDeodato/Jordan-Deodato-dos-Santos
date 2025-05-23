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
            'name' => 'João o Recrutador',
            "email" => 'recrutadorjoão@teste.com',
            "password" => Hash::make('Joao1234@'),
            "cpf" => substr(str_shuffle('01234567890123456789'),1,11),
            "user_type_id" => "1",
        ]);

        User::factory()->create([
            'name' => 'Zezinho o Candidato',
            "email" => 'candidatozezinho@teste.com',
            "password" => Hash::make('Zezinho1234@'),
            "cpf" => substr(str_shuffle('01234567890123456789'),1,11),
            "user_type_id" => "2",
        ]);
    }
}
