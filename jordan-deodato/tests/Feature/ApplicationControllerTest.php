<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
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

    /** @test */
    public function it_lists_applications()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Application::factory()->count(3)->create();

        $response = $this->getJson('/api/application');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
                'message'
            ]);
    }

    /** @test */
    public function it_shows_a_single_application()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $application = Application::factory()->create();

        $response = $this->getJson("/api/application/{$application->uuid}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'message'
            ])
            ->assertJson(['data' => ['uuid' => $application->uuid]]);
    }

    /** @test */
    public function it_creates_a_new_application()
    {
        $candidate = User::factory()->create(['user_type_id' => 2]);
        $vacancy = Vacancy::factory()->create(['opened' => true]);

        Sanctum::actingAs($candidate);

        $data = [
            'vacancy_uuid' => $vacancy->uuid,
            'candidate_uuid' => $candidate->uuid,
        ];

        $response = $this->postJson('/api/application', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_updates_an_application()
    {
        $candidate = User::factory()->create(['user_type_id' => 2]);
        $vacancy = Vacancy::factory()->create();
        $application = Application::factory()->create();

        Sanctum::actingAs($candidate);

        $data = [
            'vacancy_uuid' => $vacancy->uuid,
            'candidate_uuid' => $candidate->uuid,
        ];

        $response = $this->putJson("/api/application/{$application->uuid}", $data);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_deletes_an_application()
    {
        $candidate = User::factory()->create(['user_type_id' => 2]);
        $application = Application::factory()->create();

        Sanctum::actingAs($candidate);

        $response = $this->deleteJson("/api/application/{$application->uuid}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['deleted' => true]
            ]);
    }
}