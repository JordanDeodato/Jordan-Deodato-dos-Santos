<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Vacancy extends Model
{
    use HasFactory;

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
}
