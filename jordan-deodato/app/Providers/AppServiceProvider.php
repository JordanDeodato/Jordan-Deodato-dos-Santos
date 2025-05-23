<?php

namespace App\Providers;

use App\Repositories\ApplicationRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\Interfaces\IApplicationRepository;
use App\Repositories\Interfaces\ICandidateRepository;
use App\Repositories\Interfaces\IUserRepository;
use App\Repositories\Interfaces\IVacancyRepository;
use App\Repositories\UserRepository;
use App\Repositories\VacancyRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IVacancyRepository::class, VacancyRepository::class);
        $this->app->bind(IApplicationRepository::class, ApplicationRepository::class);
        $this->app->bind(ICandidateRepository::class, CandidateRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
