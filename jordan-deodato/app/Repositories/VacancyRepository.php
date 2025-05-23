<?php

namespace App\Repositories;

use App\Models\Vacancy;
use App\Repositories\Interfaces\IVacancyRepository;
use Illuminate\Support\Collection;

class VacancyRepository implements IVacancyRepository
{
    /**
     * Retrieve all vacancies.
     *
     * @return Collection<Vacancy>
     */
    public function getAllVacancies(): Collection
    {
        return Vacancy::get();
    }
    
    /**
     * Retrieve a vacancy.
     *
     * @param string $uuid 
     *
     * @return Vacancy
     */
    public function getVacancyByUuid(string $uuid): Vacancy
    {
        return Vacancy::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Create a new vacancy.
     *
     * @param array $data
     * @return Vacancy
     */
    public function createVacancy(array $data): Vacancy
    {
        return Vacancy::create($data);
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
        $vacancy = Vacancy::where('uuid', $uuid)->firstOrFail();
        $vacancy->update($data);

        return $vacancy;
    }

    /**
     * Delete a vacancy.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteVacancy(string $uuid): bool
    {
        return Vacancy::where('uuid', $uuid)->delete() > 0;
    }
}
