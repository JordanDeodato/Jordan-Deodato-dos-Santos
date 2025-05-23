<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\IUserRepository;
use Illuminate\Support\Collection;

class UserRepository implements IUserRepository
{
    /**
     * Retrieve all users.
     *
     * @return Collection<User>
     */
    public function getAllUsers(): Collection
    {
        return User::with('userType')->get();
    }
    
    /**
     * Retrieve a user.
     *
     * @param string $uuid 
     *
     * @return \App\Models\User
     */
    public function getUserByUuid(string $uuid): User
    {
        return User::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return User::create($data);
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
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->update($data);

        return $user;
    }

    /**
     * Delete a user.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteUser(string $uuid): bool
    {
        return User::where('uuid', $uuid)->delete() > 0;
    }
}
