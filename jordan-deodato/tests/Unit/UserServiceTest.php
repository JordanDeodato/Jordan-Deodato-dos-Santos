<?php

namespace Tests\Unit\Services;

use App\DTOs\PaginatedResponseDto;
use App\Dtos\UserDto;
use App\Models\User;
use App\Models\UserType;
use App\Repositories\Interfaces\IUserRepository;
use App\Services\UserService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private $userRepositoryMock;
    private $userService;
    private $user;
    private $userType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userType = new UserType([
            'id' => 1,
            'name' => 'Recrutador'
        ]);

        $this->user = new User([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf' => '44848411516',
            'user_type_id' => 1,
        ]);

        $this->user->userType = $this->userType;

        $this->userRepositoryMock = Mockery::mock(IUserRepository::class);
        $this->userService = new UserService($this->userRepositoryMock);
    }

    public function test_get_all_users()
    {
        $perPage = 20;
        $filters = ['name' => 'John'];

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);

        $paginatorMock->shouldReceive('items')->andReturn(collect([$this->user]));
        $paginatorMock->shouldReceive('currentPage')->andReturn(1);
        $paginatorMock->shouldReceive('perPage')->andReturn($perPage);
        $paginatorMock->shouldReceive('total')->andReturn(1);
        $paginatorMock->shouldReceive('lastPage')->andReturn(1);

        $expectedCacheKey = 'users_' . md5(json_encode($filters) . "_page{$perPage}");

        Cache::shouldReceive('get')
            ->once()
            ->with($expectedCacheKey)
            ->andReturnNull();

        Cache::shouldReceive('put')
            ->once()
            ->with(
                $expectedCacheKey,
                Mockery::type(PaginatedResponseDto::class),
                600
            );

        $this->userRepositoryMock->shouldReceive('getAllUsers')
            ->once()
            ->with($perPage, $filters)
            ->andReturn($paginatorMock);

        $result = $this->userService->getAllUsers($perPage, $filters);

        $this->assertInstanceOf(PaginatedResponseDto::class, $result);
        $this->assertCount(1, $result->data);
    }

    public function test_get_user_by_uuid()
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $this->userRepositoryMock->shouldReceive('getUserByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($this->user);

        Cache::shouldReceive('remember')
            ->once()
            ->with("user_{$uuid}", 600, Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $time, $callback) {
                return $callback(); 
            });

        $result = $this->userService->getUserByUuid($uuid);

        $this->assertInstanceOf(UserDto::class, $result);
    }

    public function test_create_user()
    {
        $data = ['name' => 'John Doe'];

        $this->userRepositoryMock->shouldReceive('createUser')
            ->once()
            ->with($data)
            ->andReturn($this->user);

        Cache::shouldReceive('forget')
            ->with('users_*')
            ->once();

        $result = $this->userService->createUser($data);

        $this->assertInstanceOf(User::class, $result);
    }

    public function test_update_user()
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $data = ['name' => 'Updated Name'];

        $this->userRepositoryMock->shouldReceive('updateUser')
            ->once()
            ->with($uuid, $data)
            ->andReturn($this->user);

        Cache::shouldReceive('forget')
            ->with("user_{$uuid}")
            ->once();
        Cache::shouldReceive('forget')
            ->with('users_*')
            ->once();

        $result = $this->userService->updateUser($uuid, $data);

        $this->assertInstanceOf(User::class, $result);
    }

    public function test_delete_user()
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $this->userRepositoryMock->shouldReceive('deleteUser')
            ->once()
            ->with($uuid)
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with("user_{$uuid}")
            ->once();
        Cache::shouldReceive('forget')
            ->with('users_*')
            ->once();

        $result = $this->userService->deleteUser($uuid);

        $this->assertTrue($result);
    }

    public function test_delete_users_by_uuids()
    {
        $uuids = ['uuid1', 'uuid2'];

        $this->userRepositoryMock->shouldReceive('deleteUsersByUuids')
            ->once()
            ->with($uuids)
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with('users_*')
            ->once();

        $result = $this->userService->deleteUsersByUuids($uuids);

        $this->assertTrue($result);
    }

    public function test_delete_all_users()
    {
        $this->userRepositoryMock->shouldReceive('deleteAllUsers')
            ->once()
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with('users_*')
            ->once();

        $result = $this->userService->deleteAllUsers();

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}