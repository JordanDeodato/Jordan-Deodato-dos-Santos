<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Vacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vacancies';

    protected $fillable = [
        'name',
        'description',
        'vacancy_type_id',
        'recruiter_uuid',
        'opened',
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
    public function vacancyType(): BelongsTo
    {
        return $this->belongsTo(VacancyType::class);
    }

    /**
     * Relationship with user type
     *
     * @return BelongsTo
     */
    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recruiter_uuid', 'uuid');
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
     * Filter the user by description
     *
     * @param Builder $query $query
     * @param string|null $description $description
     *
     * @return Builder
     */
    public function scopeFilterByDescription(Builder $query, string|null $description): Builder
    {
        if ($description) {
            return $query->where('description', 'like', "%{$description}%");
        }
        return $query;
    }

    /**
     * Filter the user by vacancy type id
     *
     * @param Builder $query $query
     * @param int|null $vacancyTypeId $vacancyTypeId
     *
     * @return Builder
     */
    public function scopeFilterByVacancyTypeId(Builder $query, int|null $vacancyTypeId): Builder
    {
        if ($vacancyTypeId) {
            return $query->where('vacancy_type_id', $vacancyTypeId);
        }
        return $query;
    }

    /**
     * Filter the user by recruiter id
     *
     * @param Builder $query $query
     * @param int|null $recruiterId $recruiterId
     *
     * @return Builder
     */
    public function scopeFilterByRecruiterId(Builder $query, int|null $recruiterId): Builder
    {
        if ($recruiterId) {
            return $query->where('recruiter_id', $recruiterId);
        }
        return $query;
    }

    /**
     * Filter the user by status
     *
     * @param Builder $query $query
     * @param bool|null $opened $status
     *
     * @return Builder
     */
    public function scopeFilterByOpened(Builder $query, bool|null $opened): Builder
    {
        if ($opened) {
            return $query->where('opened', $opened);
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
