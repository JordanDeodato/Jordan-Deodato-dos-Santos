<?php

namespace Tests\Unit\Services;

use App\DTOs\PaginatedResponseDto;
use App\Dtos\VacancyDto;
use App\Exceptions\BusinessRuleException;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyType;
use App\Repositories\Interfaces\IVacancyRepository;
use App\Services\VacancyService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class VacancyServiceTest extends TestCase
{
    private $vacancyRepositoryMock;
    private $vacancyService;
    private $vacancy;
    private $vacancyType;
    private $recruiter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vacancyType = new VacancyType([
            'id' => 1,
            'name' => 'CLT'
        ]);

        $this->recruiter = new User ([
            'uuid' => '987e6543-e21b-43d3-b456-426614174999',
            'name' => 'Recruiter',
            'email' => 'recruiter@example.com',
            'user_type_id' => 1
        ]);

        $this->vacancy = new Vacancy([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Desenvolvedor PHP',
            'description' => 'Vaga para desenvolvedor PHP',
            'vacancy_type_id' => 1,
            'opened' => true,
            'recruiter_uuid' => $this->recruiter->uuid
        ]);

        $this->vacancy->vacancyType = $this->vacancyType;
        $this->vacancy->recruiter = $this->recruiter;

        $this->vacancyRepositoryMock = Mockery::mock(IVacancyRepository::class);
        $this->vacancyService = new VacancyService($this->vacancyRepositoryMock);
    }

    public function test_get_all_vacancies()
    {
        $perPage = 20;
        $filters = ['name' => 'PHP'];

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);
        $paginatorMock->shouldReceive('items')->andReturn(collect([$this->vacancy]));
        $paginatorMock->shouldReceive('currentPage')->andReturn(1);
        $paginatorMock->shouldReceive('perPage')->andReturn($perPage);
        $paginatorMock->shouldReceive('total')->andReturn(1);
        $paginatorMock->shouldReceive('lastPage')->andReturn(1);

        $this->vacancyRepositoryMock->shouldReceive('getAllVacancies')
            ->once()
            ->with($perPage, $filters)
            ->andReturn($paginatorMock);

        Cache::shouldReceive('get')
            ->once()
            ->andReturnNull();

        Cache::shouldReceive('put')
            ->once();

        $result = $this->vacancyService->getAllVacancies($perPage, $filters);

        $this->assertInstanceOf(PaginatedResponseDto::class, $result);
        $this->assertCount(1, $result->data);
    }

    public function test_get_vacancy_by_uuid()
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $this->vacancyRepositoryMock->shouldReceive('getVacancyByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($this->vacancy);

        Cache::shouldReceive('remember')
            ->once()
            ->with("vacancy_{$uuid}", 600, Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $time, $callback) {
                return $callback();
            });

        $result = $this->vacancyService->getVacancyByUuid($uuid);

        $this->assertInstanceOf(VacancyDto::class, $result);
    }

    public function test_create_vacancy_success()
    {
        Auth::shouldReceive('user')->andReturn($this->recruiter);

        $data = ['title' => 'Nova Vaga'];

        $this->vacancyRepositoryMock->shouldReceive('createVacancy')
            ->once()
            ->with(array_merge($data, ['recruiter_uuid' => $this->recruiter->uuid]))
            ->andReturn($this->vacancy);

        Cache::shouldReceive('forget')
            ->with('vacancies_*')
            ->once();

        $result = $this->vacancyService->createVacancy($data);

        $this->assertInstanceOf(Vacancy::class, $result);
    }

    public function test_create_vacancy_throws_when_not_recruiter()
    {
        $nonRecruiter = new User(['user_type_id' => 2]);
        Auth::shouldReceive('user')->andReturn($nonRecruiter);

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Apenas recrutadores podem criar vagas.');

        $this->vacancyService->createVacancy(['title' => 'Vaga']);
    }

    public function test_update_vacancy_success()
    {
        Auth::shouldReceive('user')->andReturn($this->recruiter);

        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $data = ['title' => 'Título Atualizado'];

        $this->vacancyRepositoryMock->shouldReceive('updateVacancy')
            ->once()
            ->with($uuid, $data)
            ->andReturn($this->vacancy);

        Cache::shouldReceive('forget')
            ->with("vacancy_{$uuid}")
            ->once();
        Cache::shouldReceive('forget')
            ->with('vacancies_*')
            ->once();

        $result = $this->vacancyService->updateVacancy($uuid, $data);

        $this->assertInstanceOf(Vacancy::class, $result);
    }

    public function test_delete_vacancy_success()
    {
        Auth::shouldReceive('user')->andReturn($this->recruiter);

        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $this->vacancyRepositoryMock->shouldReceive('deleteVacancy')
            ->once()
            ->with($uuid)
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with("vacancy_{$uuid}")
            ->once();
        Cache::shouldReceive('forget')
            ->with('vacancies_*')
            ->once();

        $result = $this->vacancyService->deleteVacancy($uuid);

        $this->assertTrue($result);
    }

    public function test_close_vacancy_success()
    {
        Auth::shouldReceive('user')->andReturn($this->recruiter);

        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $this->vacancyRepositoryMock->shouldReceive('closeVacancy')
            ->once()
            ->with($uuid)
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->with("vacancy_{$uuid}")
            ->once();
        Cache::shouldReceive('forget')
            ->with('vacancies_*')
            ->once();

        $result = $this->vacancyService->closeVacancy($uuid);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}