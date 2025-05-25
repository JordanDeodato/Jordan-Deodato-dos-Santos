<?php

namespace Tests\Unit\Services;

use App\Dtos\ApplicationDto;
use App\DTOs\PaginatedResponseDto;
use App\Exceptions\BusinessRuleException;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Education;
use App\Models\User;
use App\Models\Vacancy;
use App\Repositories\Interfaces\IApplicationRepository;
use App\Services\ApplicationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class ApplicationServiceTest extends TestCase
{
    private $applicationRepositoryMock;
    private $applicationService;
    private $user;
    private $uuid;
    private $candidate;
    private $recruiter;
    private $vacancy;
    private $application;
    private $education;

    protected function setUp(): void
    {
        parent::setUp();

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
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'user_uuid' => $this->user->uuid,
            'education_id' => $this->education->id,
            'resume' => 'Sample resume text',
            'experience' => "Dev Laravel",
            'skills' => 'PHP, Laravel, JavaScript',
            'linkedin_profile' => 'https://linkedin.com/in/johndoe',
        ]);

        $this->recruiter = new User([
            'uuid' => '987e6543-e21b-43d3-b456-426614174999',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_type_id' => 2
        ]);

        $this->vacancy = new Vacancy([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Desenvolvedor PHP',
            'description' => 'Vaga para desenvolvedor PHP',
            'vacancy_type_id' => 1,
            'opened' => true,
            'recruiter_uuid' => $this->recruiter->uuid
        ]);

        $this->application = new Application([
            'uuid' => '22222222-2222-2222-2222-222222222222',
            'candidate_uuid' => $this->candidate->uuid,
            'vacancy_uuid' => $this->vacancy->uuid,
        ]);

        $this->candidate->setRelation('user', $this->user);
        $this->application->setRelation('candidate', $this->candidate);
        $this->application->setRelation('vacancy', $this->vacancy);

        $this->applicationRepositoryMock = Mockery::mock(IApplicationRepository::class);
        $this->applicationService = new ApplicationService($this->applicationRepositoryMock);
    }

    public function test_get_all_applications()
    {
        $perPage = 20;
        $filters = ['uuid' => $this->application->uuid];

        $this->user->name = 'John Doe Candidate';
        $this->vacancy->name = 'Desenvolvedor Laravel Pleno';

        $this->candidate->user = $this->user;
        $this->application->candidate->name = 'John Doe Candidate';
        $this->application->vacancy = $this->vacancy;

        $applicationMock = new Application([
            'uuid' => $this->application->uuid,
            'candidate_uuid' => $this->candidate->uuid,
            'vacancy_uuid' => $this->vacancy->uuid,
        ]);

        $applicationMock->setRelation('candidate', $this->candidate);
        $applicationMock->setRelation('vacancy', $this->vacancy);

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);
        $paginatorMock->shouldReceive('items')->andReturn(collect([$applicationMock]));
        $paginatorMock->shouldReceive('currentPage')->andReturn(1);
        $paginatorMock->shouldReceive('perPage')->andReturn($perPage);
        $paginatorMock->shouldReceive('total')->andReturn(1);
        $paginatorMock->shouldReceive('lastPage')->andReturn(1);

        $this->applicationRepositoryMock->shouldReceive('getAllApplications')
            ->once()
            ->with($perPage, $filters)
            ->andReturn($paginatorMock);

        Cache::shouldReceive('get')
            ->once()
            ->andReturnNull();

        Cache::shouldReceive('put')
            ->once();

        $result = $this->applicationService->getAllApplications($perPage, $filters);

        $this->assertInstanceOf(PaginatedResponseDto::class, $result);
        $this->assertCount(1, $result->data);
        $this->assertEquals('John Doe Candidate', $result->data[0]->candidateName);
        $this->assertEquals('Desenvolvedor Laravel Pleno', $result->data[0]->vacancyName);
    }

    public function test_get_application_by_uuid()
    {
        $this->user->name = 'John Doe Candidate';
        $this->vacancy->name = 'Desenvolvedor Laravel Pleno';

        $this->candidate->user = $this->user;
        $this->application->candidate->name = 'John Doe Candidate';
        $this->application->vacancy = $this->vacancy;

        $applicationMock = new Application([
            'uuid' => $this->application->uuid,
            'candidate_uuid' => $this->candidate->uuid,
            'vacancy_uuid' => $this->vacancy->uuid,
        ]);

        $applicationMock->setRelation('candidate', $this->candidate);
        $applicationMock->setRelation('vacancy', $this->vacancy);

        $this->applicationRepositoryMock->shouldReceive('getApplicationByUuid')
            ->once()
            ->with($this->application->uuid)
            ->andReturn($this->application);

        Cache::shouldReceive('remember')
            ->once()
            ->with("application_{$this->application->uuid}", 600, Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $time, $callback) {
                return $callback();
            });

        $result = $this->applicationService->getApplicationByUuid($this->application->uuid);

        $this->assertInstanceOf(ApplicationDto::class, $result);
    }

    public function test_create_application_success()
    {
        $this->user->user_type_id = 2;
        Auth::shouldReceive('user')->andReturn($this->user);

        $data = [
            'vacancy_uuid' => $this->vacancy->uuid,
        ];

        $this->applicationRepositoryMock->shouldReceive('isVacancyOpen')
            ->once()
            ->with($this->vacancy->uuid)
            ->andReturn(true);

        $this->applicationRepositoryMock->shouldReceive('applicationExists')
            ->once()
            ->with($this->vacancy->uuid)
            ->andReturn(false);

        $this->applicationRepositoryMock->shouldReceive('createApplication')
            ->once()
            ->with([
                'vacancy_uuid' => $this->vacancy->uuid,
                'candidate_uuid' => $this->user->uuid
            ])
            ->andReturn($this->application);

        Cache::shouldReceive('forget')
            ->with('applications_*')
            ->once();

        $result = $this->applicationService->createApplication($data);

        $this->assertEquals($this->application->uuid, $result->uuid);
    }

    public function test_create_application_throws_when_not_candidate()
    {
        $recruiter = new User([
            'uuid' => '987e6543-e21b-43d3-b456-426614174999',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_type_id' => 1
        ]);

        Auth::shouldReceive('user')->andReturn($recruiter);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Apenas candidatos podem se candidatar a vagas.');

        $this->applicationService->createApplication(['vacancy_uuid' => '11111111-1111-1111-1111-111111111111']);
    }

    public function test_create_application_throws_when_vacancy_closed()
    {
        $vacancy = new Vacancy([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Desenvolvedor PHP',
            'description' => 'Vaga para desenvolvedor PHP',
            'vacancy_type_id' => 1,
            'opened' => false,
            'recruiter_uuid' => $this->recruiter->uuid
        ]);

        $candidate = new User([
            'uuid' => '987e6543-e21b-43d3-b456-426614174999',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_type_id' => 2
        ]);

        Auth::shouldReceive('user')->andReturn($candidate);

        $this->applicationRepositoryMock->shouldReceive('isVacancyOpen')
            ->once()
            ->with($vacancy->uuid)
            ->andReturn(false);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Esta vaga já foi encerrada.');

        $this->applicationService->createApplication([
            'vacancy_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'candidate_uuid' => '987e6543-e21b-43d3-b456-426614174999',
        ]);
    }

    public function test_create_application_throws_when_duplicate()
    {
        $vacancy = new Vacancy([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Desenvolvedor PHP',
            'description' => 'Vaga para desenvolvedor PHP',
            'vacancy_type_id' => 1,
            'opened' => false,
            'recruiter_uuid' => $this->recruiter->uuid
        ]);

        $candidate = new User([
            'uuid' => '987e6543-e21b-43d3-b456-426614174999',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_type_id' => 2
        ]);

        Auth::shouldReceive('user')->andReturn($candidate);

        $this->applicationRepositoryMock->shouldReceive('isVacancyOpen')
            ->once()
            ->with($vacancy->uuid)
            ->andReturn(true);

        $this->applicationRepositoryMock->shouldReceive('applicationExists')
            ->once()
            ->with($vacancy->uuid)
            ->andReturn(true);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Candidato já inscrito nesta vaga.');

        $this->applicationService->createApplication([
            'vacancy_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'candidate_uuid' => '987e6543-e21b-43d3-b456-426614174999',
        ]);
    }

    public function test_delete_application_success()
    {
        $candidate = new User([
            'uuid' => '987e6543-e21b-43d3-b456-426614174999',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_type_id' => 2
        ]);

        Auth::shouldReceive('user')->andReturn($candidate);

        $uuid = '22222222-2222-2222-2222-222222222222';

        $this->applicationRepositoryMock->shouldReceive('deleteApplication')
            ->once()
            ->with($uuid)
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with("application_{$uuid}")
            ->once();
        Cache::shouldReceive('forget')
            ->with('applications_*')
            ->once();

        $result = $this->applicationService->deleteApplication($uuid);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}