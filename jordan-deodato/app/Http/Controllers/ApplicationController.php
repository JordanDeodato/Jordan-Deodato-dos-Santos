<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessRuleException;
use App\Http\Requests\ApplicationRequest;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar as candidaturas. " . $e->getMessage()
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
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar a candidatura. " . $e->getMessage()
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
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
                "errors" => $e->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Entidade não encontrada."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro inesperado. " . $e->getMessage()
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
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Candidatura não encontrada."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao atualizar candidatura. " . $e->getMessage()
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
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Candidatura não encontrada."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir candidatura. " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete applications by uuid.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function deleteByUuid(Request $request): JsonResponse
    {
        try {
            $uuids = $request->input('uuids');

            if (!is_array($uuids) || empty($uuids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'É necessário fornecer uma lista de UUIDs válidos.'
                ], 422);
            }

            $this->applicationService->deleteApplicationsByUuids($uuids);

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Candidaturas foram excluídas com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Candidaturas não encontradas."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir as candidaturas. " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all application.
     *
     * @return JsonResponse
     */
    public function deleteAll(): JsonResponse
    {
        try {
            $this->applicationService->deleteAllApplications();

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Todas as candidaturas foram excluídas com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Candidaturas não encontradas."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir todas as candidaturas. " . $e->getMessage()
            ], 500);
        }
    }
}
