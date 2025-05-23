<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_uuid',
        'resume',
        'education_id',
        'experience',
        'skills',
        'linkedin_profile',
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
     * Relationship with user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * Relationship with user
     *
     * @return BelongsTo
     */
    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class, 'education_id', 'id');
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
     * Filter the user by user uuid
     *
     * @param Builder $query $query
     * @param string $userUuid $userUuid
     *
     * @return Builder
     */
    public function scopeFilterByUserUuid(Builder $query, string|null $userUuid): Builder
    {
        if ($userUuid) {
            return $query->where('user_uuid', $userUuid);
        }
        return $query;
    }

    /**
     * Filter the user by resume
     *
     * @param Builder $query $query
     * @param string|null $resume $resume
     *
     * @return Builder
     */
    public function scopeFilterByResume(Builder $query, string|null $resume): Builder
    {
        if ($resume) {
            return $query->where('resume', 'like', "%{$resume}%");
        }
        return $query;
    }

    /**
     * Filter the user by education id
     *
     * @param Builder $query $query
     * @param int $educationId $educationId
     *
     * @return Builder
     */
    public function scopeFilterByEducationId(Builder $query, string|null $educationId): Builder
    {
        if ($educationId) {
            return $query->where('user_uuid', $educationId);
        }
        return $query;
    }

    /**
     * Filter the user by experience
     *
     * @param Builder $query $query
     * @param string|null $experience $experience
     *
     * @return Builder
     */
    public function scopeFilterByExperience(Builder $query, string|null $experience): Builder
    {
        if ($experience) {
            return $query->where('experience', 'like', "%{$experience}%");
        }
        return $query;
    }

    /**
     * Filter the user by skills
     *
     * @param Builder $query $query
     * @param string|null $skills $skills
     *
     * @return Builder
     */
    public function scopeFilterBySkills(Builder $query, string|null $skills): Builder
    {
        if ($skills) {
            return $query->where('skills', 'like', "%{$skills}%");
        }
        return $query;
    }

    /**
     * Filter the user by linkedin profile
     *
     * @param Builder $query $query
     * @param string|null $linkedinProfile $linkedinProfile
     *
     * @return Builder
     */
    public function scopeFilterByLinkedinProfile(Builder $query, string|null $linkedinProfile): Builder
    {
        if ($linkedinProfile) {
            return $query->where('linkedin_profile', 'like', "%{$linkedinProfile}%");
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
