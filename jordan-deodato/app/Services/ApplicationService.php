<?php

namespace App\Services;

use App\Dtos\ApplicationDto;
use App\DTOs\PaginatedResponseDto;
use App\Exceptions\BusinessRuleException;
use App\Models\Application;
use App\Models\Vacancy;
use App\Repositories\Interfaces\IApplicationRepository;
use Illuminate\Support\Facades\Auth;
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
        if (!$this->isCandidate()) {
            throw new BusinessRuleException('Apenas candidatos podem se candidatar a vagas.');
        }

        $this->validateVacancyIsOpen($data['vacancy_uuid']);

        $data['candidate_uuid'] = Auth::user()->uuid;
        $this->validateDuplicateApplication($data['vacancy_uuid']);
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
        if (!$this->isCandidate()) {
            throw new BusinessRuleException('Apenas candidatos podem editar sua candidatura.');
        }

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
        if (!$this->isCandidate()) {
            throw new BusinessRuleException('Apenas candidatos podem excluir sua candidatura.');
        }

        $deleted = $this->applicationRepository->deleteApplication($uuid);

        Cache::forget("application_{$uuid}");
        Cache::forget('applications_*');

        return $deleted;
    }

    /**
     * Delete applications by uuid.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteApplicationsByUuids(array $dataUuid): bool
    {
        if ($this->isCandidate()) {
            throw new BusinessRuleException('Apenas recrutadores podem excluir múltiplas candidaturas.');
        }

        $deleted = $this->applicationRepository->deleteApplicationsByUuids($dataUuid);
        Cache::forget('applications_*');

        return $deleted;
    }

    /**
     * Delete all applications.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteAllApplications(): bool
    {
        if ($this->isCandidate()) {
            throw new BusinessRuleException('Apenas recrutadores podem excluir todas as candidaturas.');
        }

        $deleted = $this->applicationRepository->deleteAllApplications();
        Cache::forget('applications_*');

        return $deleted;
    }

    /**
     * Check if candidate already applied for the vacancy
     *
     * @param string $vacancyUuid
     *
     * @return void
     */
    private function validateDuplicateApplication(string $vacancyUuid): void
    {
        if ($this->applicationRepository->applicationExists($vacancyUuid)) {
            throw new BusinessRuleException('Candidato já inscrito nesta vaga.');
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
        if (!$this->applicationRepository->isVacancyOpen($vacancyUuid)) {
            throw new BusinessRuleException('Esta vaga já foi encerrada.');
        }
    }

    /**
     * Check if user is candidate
     *
     * @return bool
     */
    private function isCandidate(): bool
    {
        $user = Auth::user();

        if (!$user || $user->user_type_id !== 2)
            return false;

        return true;
    }
}