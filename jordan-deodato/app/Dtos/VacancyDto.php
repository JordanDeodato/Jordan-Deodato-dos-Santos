<?php

namespace App\Dtos;

use App\Models\Vacancy;

class VacancyDto
{
    public string $uuid;
    public string $name;
    public string $description;
    public int $vacancyTypeId;
    public string $recruiterUuid;
    public bool $opened;
    public string $vacancyTypeName;
    public string $recruiterName;

    public function __construct(Vacancy $vacancy)
    {
        $this->uuid = $vacancy->uuid;
        $this->name = $vacancy->name;
        $this->description = $vacancy->description;
        $this->vacancyTypeId = $vacancy->vacancy_type_id;
        $this->recruiterUuid = $vacancy->recruiter_uuid;
        $this->opened = $vacancy->opened;
        $this->vacancyTypeName = $vacancy->vacancyType?->name;
        $this->recruiterName = $vacancy->recruiter?->name;
    }

    /**
     * Transform a vacancy list in DTOs list.
     *
     * @param iterable<Vacancy> $vacancies
     * @return array<VacancyDto>
     */
    public static function collection(iterable $vacancies): array
    {
        return array_map(fn ($vacancy) => new self($vacancy), $vacancies instanceof \Illuminate\Support\Collection ? $vacancies->all() : $vacancies);
    }
}
