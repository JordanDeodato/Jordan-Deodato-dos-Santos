<?php

namespace App\Repositories;

use App\Models\Candidate;
use App\Repositories\Interfaces\ICandidateRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CandidateRepository implements ICandidateRepository
{
    /**
     * Retrieve all candidates.
     *
     * @return LengthAwarePaginator
     */
    public function getAllCandidates(int $size, array $filters = []): LengthAwarePaginator
    {
        return Candidate::query()
            ->filterByUuid($filters['uuid'] ?? null)
            ->filterByUserUuid($filters['user_uuid'] ?? null)
            ->filterByResume($filters['resume'] ?? null)
            ->filterByEducationId($filters['education_id'] ?? null)
            ->filterByExperience($filters['experience'] ?? null)
            ->filterBySkills($filters['skills'] ?? null)
            ->filterByLinkedinProfile($filters['linkedin_profile'] ?? null)
            ->orderByField($filters['order_by'] ?? null, $filters['order_direction'] ?? 'asc')
            ->paginate($size);
    }

    /**
     * Retrieve a candidate.
     *
     * @param string $uuid 
     *
     * @return Candidate
     */
    public function getCandidateByUuid(string $uuid): Candidate
    {
        return Candidate::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Create a new candidate.
     *
     * @param array $data
     * @return Candidate
     */
    public function createCandidate(array $data): Candidate
    {
        return Candidate::create($data);
    }

    /**
     * Update an existing candidate.
     *
     * @param string $uuid
     * @param array $data
     * @return Candidate
     */
    public function updateCandidate(string $uuid, array $data): Candidate
    {
        $candidate = Candidate::where('uuid', $uuid)->firstOrFail();
        $candidate->update($data);

        return $candidate;
    }

    /**
     * Delete a candidate.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteCandidate(string $uuid): bool
    {
        return Candidate::where('uuid', $uuid)->delete() > 0;
    }

    /**
     * Delete candidates by uuid.
     *
     * @param string $dataUuid
     * @return bool
     */
    public function deleteCandidatesByUuids(array $dataUuid): bool
    {
        $candidates = Candidate::whereIn('uuid', $dataUuid)->get();

        foreach ($candidates as $candidate) {
            $candidate->delete();
        }

        return $candidates->isNotEmpty();
    }

    /**
     * Delete all candidates.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteAllCandidates(): bool
    {
        $candidates = Candidate::all();

        foreach ($candidates as $candidate) {
            $candidate->delete();
        }

        return $candidates->isNotEmpty();
    }
    
    /**
     * Method candidateExists
     *
     * @return bool
     */
    public function candidateExists(): bool
    {
        $user = Auth::user();

        return Candidate::where('user_uuid', $user->uuid)
            ->exists();
    }
}
