<?php

namespace App\Repositories\Interfaces;

use App\Models\Candidate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ICandidateRepository
{
    /**
     * Retrieve all candidates.
     *
     * @param int $size 
     * @param array $filters 
     * 
     * @return LengthAwarePaginator
     */
    public function getAllCandidates(int $size, array $filters): LengthAwarePaginator;
    
    /**
     * Retrieve a candidate.
     *
     * @param string $uuid 
     *
     * @return Candidate
     */
    public function getCandidateByUuid(string $uuid): Candidate;

    /**
     * Create a new candidate.
     *
     * @param array $data
     * @return Candidate
     */
    public function createCandidate(array $data): Candidate;

    /**
     * Update an existing candidate.
     *
     * @param string $uuid
     * @param array $data
     * @return Candidate
     */
    public function updateCandidate(string $uuid, array $data): Candidate;

    /**
     * Delete a candidate.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteCandidate(string $uuid): bool;
}
