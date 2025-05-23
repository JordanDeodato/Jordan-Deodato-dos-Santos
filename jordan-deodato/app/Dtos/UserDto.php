<?php

namespace App\Dtos;

use App\Models\User;

class UserDto
{
    public string $uuid;
    public string $name;
    public string $email;
    public string $cpf;
    public int $userTypeId;
    public string $userTypeName;

    public function __construct(User $user)
    {
        $this->uuid = $user->uuid;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->cpf = $user->cpf;
        $this->userTypeId = $user->user_type_id;
        $this->userTypeName = $user->userType?->name;
    }

    /**
     * Transform a user list in DTOs list.
     *
     * @param iterable<User> $users
     * @return array<UserDto>
     */
    public static function collection(iterable $users): array
    {
        return array_map(fn ($user) => new self($user), $users instanceof \Illuminate\Support\Collection ? $users->all() : $users);
    }
}
