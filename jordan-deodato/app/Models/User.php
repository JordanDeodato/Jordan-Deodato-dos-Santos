<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'cpf',
        'email',
        'password',
        'user_type_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Method booted
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            $user->uuid = (string) Str::uuid();
        });
    }

    /**
     * Relationship with user type
     *
     * @return BelongsTo
     */
    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    /**
     * Filter the user by uuid
     *
     * @param Builder $query $query
     * @param string $uuid $uuid
     *
     * @return Builder
     */
    public function scopeFilterByUuid(Builder $query, string|null $uuid): Builder
    {
        if ($uuid) {
            return $query->where('uuid', $uuid);
        }
        return $query;
    }

    /**
     * Filter the user by user type id
     *
     * @param Builder $query $query
     * @param int|null $userTypeId $userTypeId
     *
     * @return Builder
     */
    public function scopeFilterByUserTypeId(Builder $query, int|null $userTypeId): Builder
    {
        if ($userTypeId) {
            return $query->where('user_type_id', $userTypeId);
        }
        return $query;
    }

    /**
     * Filter the user by name
     *
     * @param Builder $query $query
     * @param string|null $name $name
     *
     * @return Builder
     */
    public function scopeFilterByName(Builder $query, string|null $name): Builder
    {
        if ($name) {
            return $query->where('name', 'like', "%{$name}%");
        }
        return $query;
    }

    /**
     * Filter the user by cpf
     *
     * @param Builder $query $query
     * @param $cpf $cpf
     *
     * @return Builder
     */
    public function scopeFilterByCpf(Builder $query, int|null $cpf): Builder
    {
        if ($cpf) {
            return $query->where('cpf', 'like', "%{$cpf}%");
        }
        return $query;
    }

    /**
     * Filter the user by email
     *
     * @param Builder $query $query
     * @param string|null $email $email
     *
     * @return Builder
     */
    public function scopeFilterByEmail(Builder $query, string|null $email): Builder
    {
        if ($email) {
            return $query->where('email', 'like', "%{$email}%");
        }
        return $query;
    }

    /**
     * Order the query
     *
     * @param Builder $query $query
     * @param string $field $field
     * @param string $direction $direction
     *
     * @return Builder
     */
    public function scopeOrderByField($query, $field, $direction = 'asc'): Builder
    {
        if ($field) {
            return $query->orderBy($field, $direction);
        }
        return $query;
    }
}
