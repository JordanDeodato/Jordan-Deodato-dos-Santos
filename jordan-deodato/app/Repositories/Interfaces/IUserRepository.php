<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface IUserRepository
{
    /**
     * Retrieve all users.
     *
     * @return Collection<User>
     */
    public function getAllUsers(): Collection;
    
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
}
