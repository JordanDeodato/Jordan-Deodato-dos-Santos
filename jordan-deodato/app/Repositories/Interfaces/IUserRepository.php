<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IUserRepository
{
    /**
     * Retrieve all users.
     *
     * @param int $size 
     * @param array $filters 
     * 
     * @return LengthAwarePaginator
     */
    public function getAllUsers(int $size, array $filters): LengthAwarePaginator;
    
    /**
     * Retrieve a user.
     *
     * @param string $uuid 
     *
     * @return User
     */
    public function getUserByUuid(string $uuid): User;

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User;

    /**
     * Update an existing user.
     *
     * @param string $uuid
     * @param array $data
     * @return User
     */
    public function updateUser(string $uuid, array $data): User;

    /**
     * Delete a user.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteUser(string $uuid): bool;

    /**
     * Delete users by uuid.
     *
     * @param string $dataUuid
     * @return bool
     */
    public function deleteUsersByUuids(array $dataUuid): bool;

    /**
     * Delete all users.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteAllUsers(): bool;
}
