<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
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

    /**
     * Test list users (index).
     */
    public function test_can_list_users()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
                'message',
            ]);
    }

    /**
     * Test show user by uuid.
     */
    public function test_can_show_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/user/{$user->uuid}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    // outros campos que desejar validar
                ],
                'message' => 'Usuário listado com sucesso.'
            ]);
    }

    /**
     * Test create user.
     */
    public function test_can_create_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $userData = User::factory()->make()->toArray();

        $userData['password'] = bcrypt('password123');

        $response = $this->postJson('/api/user', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Usuário criado com sucesso.'
            ]);

        $this->assertDatabaseHas('users', [
            'uuid' => $response->json('data.uuid'),
            'email' => $userData['email'],
        ]);
    }


    /**
     * Test update user.
     */
    public function test_can_update_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updatedData = [
            'name' => 'Nome Atualizado',
            'email' => fake()->unique()->safeEmail, // evita duplicidade
        ];

        $response = $this->putJson("/api/user/{$user->uuid}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso.'
            ]);

        $this->assertDatabaseHas('users', [
            'uuid' => $user->uuid,
            'name' => 'Nome Atualizado',
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * Test delete user.
     */
    public function test_can_delete_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/user/{$user->uuid}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['deleted' => true],
                'message' => 'Usuário excluído com sucesso.'
            ]);

        $this->assertSoftDeleted('users', [
            'uuid' => $user->uuid,
        ]);
    }
}
