<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Models\User;
use App\Repositories\Interfaces\IUserRepository;

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
     * @return array
     */
    public function getAllUsers(): array
    {
        $users = $this->userRepository->getAllUsers();
        return UserDto::collection($users);
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
        $user = $this->userRepository->getUserByUuid($uuid);
        return new UserDto($user);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return $this->userRepository->createUser($data);
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
        return $this->userRepository->updateUser($uuid, $data);
    }

    /**
     * Delete a user.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteUser(string $uuid)
    {
        return $this->userRepository->deleteUser($uuid);
    }
}