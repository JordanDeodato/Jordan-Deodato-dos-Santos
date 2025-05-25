<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CandidateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            \Database\Seeders\UserTypeSeeder::class,
            \Database\Seeders\EducationSeeder::class,
            \Database\Seeders\VacancyTypeSeeder::class,
        ]);
    }

    public function test_can_list_candidates(): void
    {
        $candidate = User::factory()->create(['user_type_id' => 2]);
        Sanctum::actingAs($candidate);
        
        Candidate::factory()->count(5)->create();

        $response = $this->getJson('/api/candidate');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
                'message'
            ]);
    }

    public function test_can_show_a_candidate(): void
    {
        $candidate = User::factory()->create(['user_type_id' => 2]);
        Sanctum::actingAs($candidate);

        $candidate = Candidate::factory()->create();

        $response = $this->getJson("/api/candidate/{$candidate->uuid}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
    }

    public function test_can_create_a_candidate(): void
    {
        $candidate = User::factory()->create(['user_type_id' => 2]);
        Sanctum::actingAs($candidate);

        $data = [
            'user_uuid' => $candidate->uuid,
            'resume' => 'Currículo em PDF',
            'education_id' => 1,
            'experience' => '3 anos como dev.',
            'skills' => 'PHP, Laravel, MySQL',
            'linkedin_profile' => 'https://linkedin.com/in/teste'
        ];
        
        $response = $this->postJson('/api/candidate', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
    }

    public function test_can_update_a_candidate(): void
    {
        $candidate = User::factory()->create(['user_type_id' => 2]);
        Sanctum::actingAs($candidate);

        $candidate = Candidate::factory()->create();

        $data = [
            'user_uuid' => $candidate->user_uuid,
            'resume' => 'Novo CV',
            'education_id' => $candidate->education_id,
            'experience' => 'Atualizado',
            'skills' => 'Atualizado',
            'linkedin_profile' => $candidate->linkedin_profile
        ];

        $response = $this->putJson("/api/candidate/{$candidate->uuid}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ]);
    }

    public function test_can_delete_a_candidate(): void
    {
        $candidate = User::factory()->create(['user_type_id' => 2]);
        Sanctum::actingAs($candidate);
        
        $candidate = Candidate::factory()->create();

        $response = $this->deleteJson("/api/candidate/{$candidate->uuid}");

        $response->assertStatus(200)
            ->assertJsonFragment(['deleted' => true]);
    }
}
