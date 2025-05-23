<?php

namespace App\Services;

use App\DTOs\PaginatedResponseDto;
use App\Dtos\VacancyDto;
use App\Models\Vacancy;
use App\Repositories\Interfaces\IVacancyRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
     * @return PaginatedResponseDto
     */
    public function getAllVacancies(int $perPage = 20, array $filters = []): PaginatedResponseDto
    {
        $cacheKey = 'vacancies_' . md5(json_encode($filters) . "_page{$perPage}");
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $paginated = $this->vacancyRepository->getAllVacancies($perPage, $filters);
        $vacancies = VacancyDto::collection($paginated->items());

        $result = new PaginatedResponseDto(
            $vacancies,
            $paginated->currentPage(),
            $paginated->perPage(),
            $paginated->total(),
            $paginated->lastPage()
        );

        Cache::put($cacheKey, $result, 600);

        return $result;
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
        $cacheKey = 'vacancy_' . $uuid;

        return Cache::remember($cacheKey, 600, function () use ($uuid) {
            $vacancy = $this->vacancyRepository->getVacancyByUuid($uuid);
            return new VacancyDto($vacancy);
        });
    }

    /**
     * Create a new vacancy.
     *
     * @param array $data
     * @return Vacancy
     */
    public function createVacancy(array $data): Vacancy
    {
        $application = $this->vacancyRepository->createVacancy($data);

        Cache::forget('vacancies_*');

        return $application;
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
        $vacancy = $this->vacancyRepository->updateVacancy($uuid, $data);

        Cache::forget("vacancy_{$uuid}");
        Cache::forget('vacancies_*');

        return $vacancy;
    }

    /**
     * Delete a vacancy.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteVacancy(string $uuid)
    {
        $deleted = $this->vacancyRepository->deleteVacancy($uuid);

        Cache::forget("vacancy_{$uuid}");
        Cache::forget('vacancies_*');

        return $deleted;
    }

    /**
     * Close a vacancy.
     *
     * @param string $uuid
     * @return bool
     */
    public function closeVacancy(string $uuid)
    {
        $user = Auth::user();

        if (!$user || $user->user_type_id !== 1) {
            throw new \Exception('Apenas recrutadores podem atualizar vagas.');
        }

        $vacancy = $this->vacancyRepository->closeVacancy($uuid);

        Cache::forget("vacancy_{$uuid}");
        Cache::forget('vacancies_*');

        return $vacancy;
    }
}