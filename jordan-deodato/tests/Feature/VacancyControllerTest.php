<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VacancyControllerTest extends TestCase
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
    public function it_lists_vacancies()
    {
        $recruiter = User::factory()->create(['user_type_id' => 1]);
        Sanctum::actingAs($recruiter);

        Vacancy::factory()->count(3)->create();

        $response = $this->getJson('/api/vacancy');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
                'message'
            ]);
    }

    /** @test */
    public function it_shows_a_single_vacancy()
    {
        $recruiter = User::factory()->create(['user_type_id' => 1]);
        Sanctum::actingAs($recruiter);

        $vacancy = Vacancy::factory()->create();

        $response = $this->getJson("/api/vacancy/{$vacancy->uuid}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'uuid' => $vacancy->uuid
                ]
            ]);
    }

    /** @test */
    public function it_creates_a_vacancy()
    {
        $recruiter = User::factory()->create(['user_type_id' => 1]);
        Sanctum::actingAs($recruiter);

        $this->withoutExceptionHandling();

        $vacancyType = VacancyType::factory()->create();

        $payload = [
            'name' => 'Nova Vaga',
            'description' => 'Descrição da vaga',
            'vacancy_type_id' => $vacancyType->id,
            'recruiter_uuid' => $recruiter->uuid,
            'opened' => true
        ];

        $response = $this->postJson('/api/vacancy', $payload);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Nova Vaga'
                ]
            ]);

        $this->assertDatabaseHas('vacancies', ['name' => 'Nova Vaga']);
    }


    /** @test */
    public function it_updates_a_vacancy()
    {
        $recruiter = User::factory()->create(['user_type_id' => 1]);
        Sanctum::actingAs($recruiter);

        $vacancy = Vacancy::factory()->create();

        $payload = ['name' => 'Vaga Atualizada'];

        $response = $this->putJson("/api/vacancy/{$vacancy->uuid}", array_merge($vacancy->toArray(), $payload));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Vaga Atualizada'
                ]
            ]);
    }

    /** @test */
    public function it_deletes_a_vacancy()
    {
        $recruiter = User::factory()->create(['user_type_id' => 1]);
        Sanctum::actingAs($recruiter);

        $vacancy = Vacancy::factory()->create();

        $response = $this->deleteJson("/api/vacancy/{$vacancy->uuid}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['deleted' => true]
            ]);

        $this->assertSoftDeleted('vacancies', ['id' => $vacancy->id]);
    }

    /** @test */
    public function it_closes_a_vacancy()
    {
        $recruiter = User::factory()->create(['user_type_id' => 1]);
        Sanctum::actingAs($recruiter);

        $vacancy = Vacancy::factory()->create(['opened' => true]);

        $response = $this->putJson("/api/vacancy/close/{$vacancy->uuid}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Vaga pausada com sucesso.'
            ]);
    }
}
