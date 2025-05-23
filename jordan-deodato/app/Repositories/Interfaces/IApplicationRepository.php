<?php

namespace App\Repositories\Interfaces;

use App\Models\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IApplicationRepository
{
    /**
     * Retrieve all applications.
     *
     * @param int $size 
     * @param array $filters 
     * 
     * @return LengthAwarePaginator
     */
    public function getAllApplications(int $size, array $filters): LengthAwarePaginator;
    
    /**
     * Retrieve a application.
     *
     * @param string $uuid 
     *
     * @return Application
     */
    public function getApplicationByUuid(string $uuid): Application;

    /**
     * Create a new application.
     *
     * @param array $data
     * @return Application
     */
    public function createApplication(array $data): Application;

    /**
     * Update an existing application.
     *
     * @param string $uuid
     * @param array $data
     * @return Application
     */
    public function updateApplication(string $uuid, array $data): Application;

    /**
     * Delete a application.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteApplication(string $uuid): bool;
}
