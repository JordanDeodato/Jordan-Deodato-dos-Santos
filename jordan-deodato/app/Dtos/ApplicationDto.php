<?php

namespace App\Dtos;

use App\Models\Application;

class ApplicationDto
{
    public string $uuid;
    public string $candidateUuid;
    public string $vacancyUuid;
    public string $candidateName;
    public string $vacancyName;

    public function __construct(Application $application)
    {
        $this->uuid = $application->uuid;
        $this->candidateUuid = $application->candidate_uuid;
        $this->vacancyUuid = $application->vacancy_uuid;
        $this->candidateName = $application->candidate?->name;
        $this->vacancyName = $application->vacancy?->name;
    }

    /**
     * Transform a application list in DTOs list.
     *
     * @param iterable<Application> $applications
     * @return array<ApplicationDto>
     */
    public static function collection(iterable $applications): array
    {
        return array_map(fn ($application) => new self($application), $applications instanceof \Illuminate\Support\Collection ? $applications->all() : $applications);
    }
}
