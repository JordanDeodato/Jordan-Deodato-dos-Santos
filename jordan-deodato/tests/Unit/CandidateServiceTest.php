<?php

namespace Tests\Unit\Services;

use App\Dtos\CandidateDto;
use App\DTOs\PaginatedResponseDto;
use App\Exceptions\BusinessRuleException;
use App\Models\Candidate;
use App\Models\Education;
use App\Models\User;
use App\Repositories\Interfaces\ICandidateRepository;
use App\Services\CandidateService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class CandidateServiceTest extends TestCase
{
    private $candidateRepositoryMock;
    private $candidateService;
    private $uuid;
    private $user;
    private $education;
    private $candidate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uuid = '123e4567-e89b-12d3-a456-426614174000';

        $this->user = new User([
            'uuid' => '987e6543-e21b-43d3-b456-426614174999',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_type_id' => 2
        ]);

        $this->education = new Education([
            'id' => 1,
            'name' => 'Bachelor in Computer Science'
        ]);

        $this->candidate = new Candidate([
            'uuid' => $this->uuid,
            'user_uuid' => $this->user->uuid,
            'education_id' => $this->education->id,
            'resume' => 'Sample resume text',
            'experience' => 3,
            'skills' => 'PHP, Laravel, JavaScript',
            'linkedin_profile' => 'https://linkedin.com/in/johndoe',
        ]);

        $this->candidateRepositoryMock = Mockery::mock(ICandidateRepository::class);
        $this->candidateService = new CandidateService($this->candidateRepositoryMock);
    }

    public function test_get_all_candidates()
    {
        $perPage = 20;
        $filters = ['resume' => 'Sample resume text'];

        $candidate = new Candidate([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'user_uuid' => $this->user->uuid,
            'education_id' => $this->education->id,
            'resume' => 'Sample resume text',
            'experience' => 3,
            'skills' => 'PHP, Laravel, JavaScript',
            'linkedin_profile' => 'https://linkedin.com/in/johndoe',
        ]);

        $candidate->setRelation('user', $this->user);
        $candidate->setRelation('education', $this->education);

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);
        $paginatorMock->shouldReceive('items')->andReturn(collect([$candidate]));
        $paginatorMock->shouldReceive('currentPage')->andReturn(1);
        $paginatorMock->shouldReceive('perPage')->andReturn($perPage);
        $paginatorMock->shouldReceive('total')->andReturn(1);
        $paginatorMock->shouldReceive('lastPage')->andReturn(1);

        $expectedCacheKey = 'candidates_' . md5(json_encode($filters) . "_page{$perPage}");

        Cache::shouldReceive('get')
            ->once()
            ->with($expectedCacheKey)
            ->andReturnNull();

        Cache::shouldReceive('put')
            ->once()
            ->with(
                $expectedCacheKey,
                Mockery::type(PaginatedResponseDto::class),
                600
            );

        $this->candidateRepositoryMock
            ->shouldReceive('getAllCandidates')
            ->once()
            ->with($perPage, $filters)
            ->andReturn($paginatorMock);

        $result = $this->candidateService->getAllCandidates($perPage, $filters);

        $this->assertInstanceOf(PaginatedResponseDto::class, $result);
        $this->assertCount(1, $result->data);

        $dto = $result->data[0];
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $dto->uuid);
        $this->assertEquals('Sample resume text', $dto->resume);
        $this->assertEquals('PHP, Laravel, JavaScript', $dto->skills);
    }

    public function test_get_all_candidates_uses_cache()
    {
        $perPage = 20;
        $filters = ['name' => 'John'];

        $cachedResult = new PaginatedResponseDto([], 1, $perPage, 0, 1);

        Cache::shouldReceive('get')
            ->once()
            ->andReturn($cachedResult);

        $result = $this->candidateService->getAllCandidates($perPage, $filters);

        $this->assertSame($cachedResult, $result);
    }

    public function test_get_candidate_by_uuid()
    {
        $this->candidate->setRelation('user', $this->user);
        $this->candidate->setRelation('education', $this->education);

        $this->candidateRepositoryMock->shouldReceive('getCandidateByUuid')
            ->once()
            ->with($this->uuid)
            ->andReturn($this->candidate);

        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), Mockery::type('int'), Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $time, $callback) {
                return $callback();
            });

        $result = $this->candidateService->getCandidateByUuid($this->uuid);

        $this->assertInstanceOf(CandidateDto::class, $result);
        $this->assertEquals($this->uuid, $result->uuid);
        $this->assertEquals('John Doe', $result->candidateName);
        $this->assertEquals('Bachelor in Computer Science', $result->educationName);
    }

    public function test_create_candidate_success()
    {
        $this->candidateRepositoryMock->shouldReceive('candidateExists')
            ->once()
            ->with()
            ->andReturn(false);

        $this->candidateRepositoryMock->shouldReceive('createCandidate')
            ->once()
            ->with([
                'name' => 'John Doe',
                'user_uuid' => $this->user->uuid,
            ])
            ->andReturn($this->candidate);

        Cache::shouldReceive('forget')
            ->with('candidates_*')
            ->once();

        Auth::shouldReceive('user')->andReturn($this->user);

        $result = $this->candidateService->createCandidate(['name' => 'John Doe']);

        $this->assertInstanceOf(Candidate::class, $result);
        $this->assertEquals($this->uuid, $result->uuid);
    }

    public function test_create_candidate_throws_when_not_candidate()
    {
        $user = new User();
        $user->user_type_id = 1;
        Auth::shouldReceive('user')->andReturn($user);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Apenas candidatos podem se candidatar a vagas.');

        $this->candidateService->createCandidate(['name' => 'John']);
    }

    public function test_create_candidate_throws_when_duplicate()
    {
        Auth::shouldReceive('user')->andReturn($this->user);

        $this->candidateRepositoryMock->shouldReceive('candidateExists')
            ->once()
            ->with()
            ->andReturn(true);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Candidato já registrado.');

        $this->candidateService->createCandidate(['name' => 'John']);
    }

    public function test_update_candidate()
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $data = ['name' => 'Updated Name'];

        $this->candidateRepositoryMock->shouldReceive('updateCandidate')
            ->once()
            ->with($uuid, $data)
            ->andReturn($this->candidate);

        Cache::shouldReceive('forget')
            ->with("candidate_{$uuid}")
            ->once();
        Cache::shouldReceive('forget')
            ->with('candidates_*')
            ->once();

        $result = $this->candidateService->updateCandidate($uuid, $data);

        $this->assertInstanceOf(Candidate::class, $result);
    }

    public function test_delete_candidate()
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $this->candidateRepositoryMock->shouldReceive('deleteCandidate')
            ->once()
            ->with($uuid)
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with("candidate_{$uuid}")
            ->once();
        Cache::shouldReceive('forget')
            ->with('candidates_*')
            ->once();

        $result = $this->candidateService->deleteCandidate($uuid);

        $this->assertTrue($result);
    }

    public function test_delete_candidates_by_uuids_success()
    {
        $user = new User();
        $user->user_type_id = 1;
        Auth::shouldReceive('user')->andReturn($user);

        $uuids = ['uuid1', 'uuid2'];

        $this->candidateRepositoryMock->shouldReceive('deleteCandidatesByUuids')
            ->once()
            ->with($uuids)
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with('candidates_*')
            ->once();

        $result = $this->candidateService->deleteCandidatesByUuids($uuids);

        $this->assertTrue($result);
    }

    public function test_delete_candidates_by_uuids_throws_when_candidate()
    {
        $user = new User();
        $user->user_type_id = 2;
        Auth::shouldReceive('user')->andReturn($user);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Apenas recrutadores podem excluir múltiplos candidatos.');

        $this->candidateService->deleteCandidatesByUuids(['uuid1']);
    }

    public function test_delete_all_candidates_success()
    {
        $user = new User();
        $user->user_type_id = 1;
        Auth::shouldReceive('user')->andReturn($user);

        $this->candidateRepositoryMock->shouldReceive('deleteAllCandidates')
            ->once()
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with('candidates_*')
            ->once();

        $result = $this->candidateService->deleteAllCandidates();

        $this->assertTrue($result);
    }

    public function test_delete_all_candidates_throws_when_candidate()
    {
        $user = new User();
        $user->user_type_id = 2;
        Auth::shouldReceive('user')->andReturn($user);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Apenas recrutadores podem excluir todas os candidatos.');

        $this->candidateService->deleteAllCandidates();
    }

    public function test_is_candidate_returns_true()
    {
        $user = new User();
        $user->user_type_id = 2;
        Auth::shouldReceive('user')->andReturn($user);

        $result = $this->invoke_private_method($this->candidateService, 'isCandidate');

        $this->assertTrue($result);
    }

    public function test_is_candidate_returns_false()
    {
        $user = new User();
        $user->user_type_id = 1;
        Auth::shouldReceive('user')->andReturn($user);

        $result = $this->invoke_private_method($this->candidateService, 'isCandidate');

        $this->assertFalse($result);
    }

    public function test_is_candidate_returns_false_when_no_user()
    {
        Auth::shouldReceive('user')->andReturn(null);

        $result = $this->invoke_private_method($this->candidateService, 'isCandidate');

        $this->assertFalse($result);
    }

    private function invoke_private_method($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}