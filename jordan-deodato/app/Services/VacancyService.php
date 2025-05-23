<?php

namespace App\Services;

use App\Dtos\VacancyDto;
use App\Models\Vacancy;
use App\Repositories\Interfaces\IVacancyRepository;

class VacancyService
{
    private IVacancyRepository $vacancyRepository;

    public function __construct(IVacancyRepository $vacancyRepository)
    {
        $this->vacancyRepository = $vacancyRepository;
    }

    /**
     * Retrieve all vacancies.
     *
     * @return array
     */
    public function getAllVacancies(): array
    {
        $vacancies = $this->vacancyRepository->getAllVacancies();
        return VacancyDto::collection($vacancies);
    }
    
    /**
     * Retrieve a vacancy.
     *
     * @param string $uuid
     *
     * @return VacancyDto
     */
    public function getVacancyByUuid(string $uuid): VacancyDto
    {
        $vacancy = $this->vacancyRepository->getVacancyByUuid($uuid);
        return new VacancyDto($vacancy);
    }

    /**
     * Create a new vacancy.
     *
     * @param array $data
     * @return Vacancy
     */
    public function createVacancy(array $data): Vacancy
    {
        return $this->vacancyRepository->createVacancy($data);
    }

    /**
     * Update an existing vacancy.
     *
     * @param string $uuid
     * @param array $data
     * @return Vacancy
     */
    public function updateVacancy(string $uuid, array $data): Vacancy
    {
        return $this->vacancyRepository->updateVacancy($uuid, $data);
    }

    /**
     * Delete a vacancy.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteVacancy(string $uuid)
    {
        return $this->vacancyRepository->deleteVacancy($uuid);
    }
}