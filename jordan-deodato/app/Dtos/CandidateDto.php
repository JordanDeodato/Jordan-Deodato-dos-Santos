<?php

namespace App\Dtos;

use App\Models\Candidate;

class CandidateDto
{
    public string $uuid;
    public string $userUuid;
    public string $resume;
    public string $educationId;
    public string $experience;
    public string $skills;
    public string $linkedinProfile;
    public string $candidateName;
    public string $educationName;

    public function __construct(Candidate $candidate)
    {
        $this->uuid = $candidate->uuid;
        $this->userUuid = $candidate->user_uuid;
        $this->resume = $candidate->resume;
        $this->educationId = $candidate->education_id;
        $this->experience = $candidate->experience;
        $this->skills = $candidate->skills;
        $this->linkedinProfile = $candidate->linkedin_profile;
        $this->candidateName = $candidate->user?->name;
        $this->educationName = $candidate->education?->name;
    }

    /**
     * Transform a candidate list in DTOs list.
     *
     * @param iterable<Candidate> $candidates
     * @return array<CandidateDto>
     */
    public static function collection(iterable $candidates): array
    {
        return array_map(fn ($candidate) => new self($candidate), $candidates instanceof \Illuminate\Support\Collection ? $candidates->all() : $candidates);
    }
}
