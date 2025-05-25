<?php

namespace App\Repositories;

use App\Models\Vacancy;
use App\Repositories\Interfaces\IVacancyRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VacancyRepository implements IVacancyRepository
{
    /**
     * Retrieve all vacancies.
     *
     * @return LengthAwarePaginator
     */
    public function getAllVacancies(int $size, array $filters = []): LengthAwarePaginator
    {
        return Vacancy::query()
            ->filterByUuid($filters['uuid'] ?? null)
            ->filterByName($filters['name'] ?? null)
            ->filterByDescription($filters['description'] ?? null)
            ->filterByVacancyTypeId($filters['vacancy_type_id'] ?? null)
            ->filterByRecruiterId($filters['recruiter_id'] ?? null)
            ->filterByOpened($filters['opened'] ?? null)
            ->orderByField($filters['order_by'] ?? null, $filters['order_direction'] ?? 'asc')
            ->paginate($size);
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

    /**
     * Delete vacancies by uuid.
     *
     * @param string $dataUuid
     * @return bool
     */
    public function deleteVacanciesByUuids(array $dataUuid): bool
    {
        $vacancies = Vacancy::whereIn('uuid', $dataUuid)->get();

        foreach ($vacancies as $vacancy) {
            $vacancy->delete();
        }

        return $vacancies->isNotEmpty();
    }

    /**
     * Delete all vacancies.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteAllVacancies(): bool
    {
        $vacancies = Vacancy::all();

        foreach ($vacancies as $vacancy) {
            $vacancy->delete();
        }

        return $vacancies->isNotEmpty();
    }

    /**
     * Close (deactivate) a vacancy.
     *
     * @param string $uuid
     * @return bool
     *
     * @throws \Exception
     */
    public function closeVacancy(string $uuid): bool
    {
        $vacancy = Vacancy::where('uuid', $uuid)->first();

        if (!$vacancy) {
            throw new \Exception('Vaga não encontrada.');
        }

        if ($vacancy->opened === 0) {
            throw new \Exception('Essa vaga já está pausada.');
        }

        return $vacancy->update(['opened' => false]);
    }
}
