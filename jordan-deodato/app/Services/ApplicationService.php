<?php

namespace App\Services;

use App\Dtos\ApplicationDto;
use App\DTOs\PaginatedResponseDto;
use App\Models\Application;
use App\Models\Vacancy;
use App\Repositories\Interfaces\IApplicationRepository;
use Illuminate\Support\Facades\Cache;

class ApplicationService
{
    private IApplicationRepository $applicationRepository;

    public function __construct(IApplicationRepository $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * Retrieve all applications.
     *
     * @return PaginatedResponseDto
     */
    public function getAllApplications(int $perPage = 20, array $filters = []): PaginatedResponseDto
    {
        $cacheKey = 'applications_' . md5(json_encode($filters) . "_page{$perPage}");
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $paginated = $this->applicationRepository->getAllApplications($perPage, $filters);
        $applications = ApplicationDto::collection($paginated->items());

        $result = new PaginatedResponseDto(
            $applications,
            $paginated->currentPage(),
            $paginated->perPage(),
            $paginated->total(),
            $paginated->lastPage()
        );

        Cache::put($cacheKey, $result, 600);

        return $result;
    }

    /**
     * Retrieve a application.
     *
     * @param string $uuid
     *
     * @return ApplicationDto
     */
    public function getApplicationByUuid(string $uuid): ApplicationDto
    {
        $cacheKey = 'application_' . $uuid;

        return Cache::remember($cacheKey, 600, function () use ($uuid) {
            $application = $this->applicationRepository->getApplicationByUuid($uuid);
            return new ApplicationDto($application);
        });
    }

    /**
     * Create a new application.
     *
     * @param array $data
     * @return Application
     */
    public function createApplication(array $data): Application
    {
        $this->validateDuplicateApplication($data['candidate_uuid'], $data['vacancy_uuid']);
        $this->validateVacancyIsOpen($data['vacancy_uuid']);

        $application = $this->applicationRepository->createApplication($data);

        Cache::forget('applications_*');

        return $application;
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
        $application = $this->applicationRepository->updateApplication($uuid, $data);

        Cache::forget("application_{$uuid}");
        Cache::forget('applications_*');

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
        $deleted = $this->applicationRepository->deleteApplication($uuid);

        Cache::forget("application_{$uuid}");
        Cache::forget('applications_*');

        return $deleted;
    }

    /**
     * Check if candidate already applied for the vacancy
     *
     * @param string $candidateUuid
     * @param string $vacancyUuid
     *
     * @return void
     */
    private function validateDuplicateApplication(string $candidateUuid, string $vacancyUuid): void
    {
        $exists = Application::where('candidate_uuid', $candidateUuid)
            ->where('vacancy_uuid', $vacancyUuid)
            ->exists();

        if ($exists) {
            throw new \Exception('Candidato já inscrito nesta vaga.');
        }
    }

    /**
     * Check if the vacancy is closed
     *
     * @param string $vacancyUuid
     *
     * @return void
     */
    private function validateVacancyIsOpen(string $vacancyUuid): void
    {
        $isOpen = Vacancy::where('uuid', $vacancyUuid)
            ->where('opened', 1)
            ->exists();

        if (!$isOpen) {
            throw new \Exception('Esta vaga já foi encerrada.');
        }
    }
}