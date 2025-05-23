<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidate_uuid',
        'vacancy_uuid'
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
     * Relationship with vacancy type
     *
     * @return BelongsTo
     */
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_uuid', 'uuid');
    }

    /**
     * Relationship with candidate type
     *
     * @return BelongsTo
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_uuid', 'uuid');
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
     * Filter the user by candidate uuid
     *
     * @param Builder $query $query
     * @param string|null $candidateUuId $candidateUuid
     *
     * @return Builder
     */
    public function scopeFilterByCandidateUuId(Builder $query, string|null $candidateUuId): Builder
    {
        if ($candidateUuId) {
            return $query->where('candidate_uuid', $candidateUuId);
        }
        return $query;
    }

    /**
     * Filter the user by vacancy uuid
     *
     * @param Builder $query $query
     * @param string|null $vacancyUuid $vacancyUuid
     *
     * @return Builder
     */
    public function scopeFilterByVacancyUuid(Builder $query, string|null $vacancyUuid): Builder
    {
        if ($vacancyUuid) {
            return $query->where('vacancy_uuid', $vacancyUuid);
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
