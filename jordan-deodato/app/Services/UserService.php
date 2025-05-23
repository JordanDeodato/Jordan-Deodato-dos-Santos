<?php

namespace App\Services;

use App\DTOs\PaginatedResponseDto;
use App\Dtos\UserDto;
use App\Models\User;
use App\Repositories\Interfaces\IUserRepository;
use Illuminate\Support\Facades\Cache;

class UserService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve all users.
     *
     * @return PaginatedResponseDto
     */
    public function getAllUsers(int $perPage = 20, array $filters = []): PaginatedResponseDto
    {
        $cacheKey = 'users_' . md5(json_encode($filters) . "_page{$perPage}");
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $paginated = $this->userRepository->getAllUsers($perPage, $filters);
        $users = UserDto::collection($paginated->items());

        $result = new PaginatedResponseDto(
            $users,
            $paginated->currentPage(),
            $paginated->perPage(),
            $paginated->total(),
            $paginated->lastPage()
        );

        Cache::put($cacheKey, $result, 600);

        return $result;
    }

    /**
     * Retrieve a user.
     *
     * @param string $uuid
     *
     * @return UserDto
     */
    public function getUserByUuid(string $uuid): UserDto
    {
        $cacheKey = 'user_' . $uuid;

        return Cache::remember($cacheKey, 600, function () use ($uuid) {
            $user = $this->userRepository->getUserByUuid($uuid);
            return new UserDto($user);
        });
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = $this->userRepository->createUser($data);

        Cache::forget('users_*');

        return $user;
    }

    /**
     * Update an existing user.
     *
     * @param string $uuid
     * @param array $data
     * @return User
     */
    public function updateUser(string $uuid, array $data): User
    {
        $user = $this->userRepository->updateUser($uuid, $data);

        Cache::forget("user_{$uuid}");
        Cache::forget('users_*');

        return $user;
    }

    /**
     * Delete a user.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteUser(string $uuid)
    {
        $deleted = $this->userRepository->deleteUser($uuid);

        Cache::forget("user_{$uuid}");
        Cache::forget('users_*');

        return $deleted;
    }
}