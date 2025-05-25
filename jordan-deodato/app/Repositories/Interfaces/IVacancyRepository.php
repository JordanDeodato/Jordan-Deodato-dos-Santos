<?php

namespace App\Repositories\Interfaces;

use App\Models\Vacancy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IVacancyRepository
{
    /**
     * Retrieve all vacancies.
     *
     * @param int $size 
     * @param array $filters 
     * 
     * @return LengthAwarePaginator
     */
    public function getAllVacancies(int $size, array $filters): LengthAwarePaginator;

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

    /**
     * Close a vacancy.
     *
     * @param string $uuid
     * @return bool
     */
    public function closeVacancy(string $uuid): bool;

    /**
     * Delete vacancies by uuid.
     *
     * @param string $dataUuid
     * @return bool
     */
    public function deleteVacanciesByUuids(array $dataUuid): bool;

    /**
     * Delete all vacancies.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteAllVacancies(): bool;
}
