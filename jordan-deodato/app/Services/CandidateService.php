<?php

namespace App\Services;

use App\Dtos\CandidateDto;
use App\DTOs\PaginatedResponseDto;
use App\Models\Candidate;
use App\Repositories\Interfaces\ICandidateRepository;
use Illuminate\Support\Facades\Cache;

class CandidateService
{
    private ICandidateRepository $candidateRepository;

    public function __construct(ICandidateRepository $candidateRepository)
    {
        $this->candidateRepository = $candidateRepository;
    }

    /**
     * Retrieve all candidates.
     *
     * @return PaginatedResponseDto
     */
    public function getAllCandidates(int $perPage = 20, array $filters = []): PaginatedResponseDto
    {
        $cacheKey = 'candidates_' . md5(json_encode($filters) . "_page{$perPage}");
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $paginated = $this->candidateRepository->getAllCandidates($perPage, $filters);
        $candidates = CandidateDto::collection($paginated->items());

        $result = new PaginatedResponseDto(
            $candidates,
            $paginated->currentPage(),
            $paginated->perPage(),
            $paginated->total(),
            $paginated->lastPage()
        );

        Cache::put($cacheKey, $result, 600);

        return $result;
    }

    /**
     * Retrieve a candidate.
     *
     * @param string $uuid
     *
     * @return CandidateDto
     */
    public function getCandidateByUuid(string $uuid): CandidateDto
    {
        $cacheKey = 'candidate_' . $uuid;

        return Cache::remember($cacheKey, 600, function () use ($uuid) {
            $candidate = $this->candidateRepository->getCandidateByUuid($uuid);
            return new CandidateDto($candidate);
        });
    }

    /**
     * Create a new candidate.
     *
     * @param array $data
     * @return Candidate
     */
    public function createCandidate(array $data): Candidate
    {
        $this->validateDuplicateRegistration($data['user_uuid']);

        $candidate = $this->candidateRepository->createCandidate($data);

        Cache::forget('candidates_*');

        return $candidate;
    }

    /**
     * Update an existing Candidate.
     *
     * @param string $uuid
     * @param array $data
     * @return Candidate
     */
    public function updateCandidate(string $uuid, array $data): Candidate
    {
        $candidate = $this->candidateRepository->updateCandidate($uuid, $data);

        Cache::forget("candidate_{$uuid}");
        Cache::forget('candidates_*');

        return $candidate;
    }

    /**
     * Delete a Candidate.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteCandidate(string $uuid)
    {
        $deleted = $this->candidateRepository->deleteCandidate($uuid);

        Cache::forget("candidate_{$uuid}");
        Cache::forget('candidates_*');

        return $deleted;
    }

    /**
     * Check if candidate has been registred
     *
     * @param string $userUuid
     *
     * @return void
     */
    private function validateDuplicateRegistration(string $userUuid): void
    {
        $exists = Candidate::where('user_uuid', $userUuid)
            ->exists();

        if ($exists) {
            throw new \Exception('Candidato já registrado.');
        }
    }
}