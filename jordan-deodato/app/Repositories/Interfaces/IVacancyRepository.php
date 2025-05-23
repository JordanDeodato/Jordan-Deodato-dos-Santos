<?php

namespace App\Repositories\Interfaces;

use App\Models\Vacancy;
use Illuminate\Support\Collection;

interface IVacancyRepository
{
    /**
     * Retrieve all vacancies.
     *
     * @return Collection<Vacancy>
     */
    public function getAllVacancies(): Collection;
    
    /**
     * Retrieve a vacancy.
     *
     * @param string $uuid 
     *
     * @return Vacancy
     */
    public function getVacancyByUuid(string $uuid): Vacancy;

    /**
     * Create a new vacancy.
     *
     * @param array $data
     * @return Vacancy
     */
    public function createVacancy(array $data): Vacancy;

    /**
     * Update an existing vacancy.
     *
     * @param string $uuid
     * @param array $data
     * @return Vacancy
     */
    public function updateVacancy(string $uuid, array $data): Vacancy;

    /**
     * Delete a vacancy.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteVacancy(string $uuid): bool;
}
