<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\IUserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

class UserRepository implements IUserRepository
{
    /**
     * Retrieve all users.
     *
     * @return LengthAwarePaginator
     */
    public function getAllUsers(int $size, array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->filterByUuid($filters['uuid'] ?? null)
            ->filterByUserTypeId($filters['user_type_id'] ?? null)
            ->filterByName($filters['name'] ?? null)
            ->filterByCpf($filters['cpf'] ?? null)
            ->filterByEmail($filters['email'] ?? null)
            ->orderByField($filters['order_by'] ?? null, $filters['order_direction'] ?? 'asc')
            ->paginate($size);
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

    /**
     * Delete users by uuid.
     *
     * @param string $dataUuid
     * @return bool
     */
    public function deleteUsersByUuids(array $dataUuid): bool
    {
        $users = User::whereIn('uuid', $dataUuid)->get();

        foreach ($users as $user) {
            $user->delete();
        }

        return $users->isNotEmpty();
    }

    /**
     * Delete all users.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteAllUsers(): bool
    {
        $users = User::all();

        foreach ($users as $user) {
            $user->delete();
        }

        return $users->isNotEmpty();
    }
}
