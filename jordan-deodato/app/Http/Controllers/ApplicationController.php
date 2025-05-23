<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    private $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Retrieve all applications.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 20);
            $filters = $request->only(['uuid', 'candidate_uuid', 'vacancy_uuid', 'order_by', 'order_direction']);
            $responseDto = $this->applicationService->getAllApplications($perPage, $filters);

            return response()->json([
                "success" => true,
                "data" => $responseDto->getData(),
                "meta" => $responseDto->getMeta(),
                "message" => "Candidaturas listadas com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar as candidaturas. " . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a application.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            $application = $this->applicationService->getApplicationByUuid($uuid);

            return response()->json([
                "success" => true,
                "data" => $application,
                "message" => "Candidatura listada com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar a candidatura. " . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new application
     *
     * @param ApplicationRequest $request
     *
     * @return JsonResponse
     */
    public function store(ApplicationRequest $request): JsonResponse
    {
        try {
            $application = $this->applicationService->createApplication($request->validated());

            return response()->json([
                "success" => true,
                "data" => $application,
                "message" => "Candidatura criada com sucesso."
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao criar a candidatura. " . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing application.
     *
     * @param string $uuid
     * @param ApplicationRequest $request
     * @return JsonResponse
     */
    public function update(string $uuid, ApplicationRequest $request): JsonResponse
    {
        try {
            $user = $this->applicationService->updateApplication($uuid, $request->validated());

            return response()->json([
                "success" => true,
                "data" => $user,
                "message" => "Candidatura atualizada com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao atualizar a candidatura. " . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a application.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $this->applicationService->deleteApplication($uuid);

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Candidatura excluída com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao excluir a candidatura. " . $exception->getMessage()
            ], 500);
        }
    }
}
