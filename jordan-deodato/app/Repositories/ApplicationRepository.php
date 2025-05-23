<?php

namespace App\Repositories;

use App\Models\Application;
use App\Repositories\Interfaces\IApplicationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApplicationRepository implements IApplicationRepository
{
    /**
     * Retrieve all applications.
     *
     * @return LengthAwarePaginator
     */
    public function getAllApplications(int $size, array $filters = []): LengthAwarePaginator
    {
        return Application::query()
            ->filterByUuid($filters['uuid'] ?? null)
            ->filterByCandidateUuId($filters['candidate_uuid'] ?? null)
            ->filterByVacancyUuid($filters['vacancy_uuid'] ?? null)
            ->orderByField($filters['order_by'] ?? null, $filters['order_direction'] ?? 'asc')
            ->paginate($size);
    }

    /**
     * Retrieve a application.
     *
     * @param string $uuid 
     *
     * @return Application
     */
    public function getApplicationByUuid(string $uuid): Application
    {
        return Application::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Create a new application.
     *
     * @param array $data
     * @return Application
     */
    public function createApplication(array $data): Application
    {
        return Application::create($data);
    }

    /**
     * Update an existing application.
     *
     * @param string $uuid
     * @param array $data
     * @return Application
     */
    public function updateApplication(string $uuid, array $data): Application
    {
        $application = Application::where('uuid', $uuid)->firstOrFail();
        $application->update($data);

        return $application;
    }

    /**
     * Delete a application.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteApplication(string $uuid): bool
    {
        return Application::where('uuid', $uuid)->delete() > 0;
    }
}
