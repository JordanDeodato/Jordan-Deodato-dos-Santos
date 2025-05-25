<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessRuleException;
use App\Http\Requests\VacancyRequest;
use App\Services\VacancyService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VacancyController extends Controller
{
    private $vacancyService;

    public function __construct(VacancyService $vacancyService)
    {
        $this->vacancyService = $vacancyService;
    }

    /**
     * Retrieve all vacancies.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 20);
            $filters = $request->only(['uuid', 'name', 'description', 'vacancy_type_id', 'recruiter_id', 'opened', 'order_by', 'order_direction']);
            $responseDto = $this->vacancyService->getAllVacancies($perPage, $filters);

            return response()->json([
                "success" => true,
                "data" => $responseDto->getData(),
                "meta" => $responseDto->getMeta(),
                "message" => "Vagas listadas com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar as vagas. " . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a vacancy.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            $vacancy = $this->vacancyService->getVacancyByUuid($uuid);

            return response()->json([
                "success" => true,
                "data" => $vacancy,
                "message" => "Vaga listada com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar a vaga. " . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new vacancy
     *
     * @param VacancyRequest $request
     *
     * @return JsonResponse
     */
    public function store(VacancyRequest $request): JsonResponse
    {
        try {
            $vacancy = $this->vacancyService->createVacancy($request->validated());

            return response()->json([
                "success" => true,
                "data" => $vacancy,
                "message" => "Vaga criada com sucesso."
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
     * Update an existing vacancy.
     *
     * @param string $uuid
     * @param VacancyRequest $request
     * @return JsonResponse
     */
    public function update(string $uuid, VacancyRequest $request): JsonResponse
    {
        try {
            $vacancy = $this->vacancyService->updateVacancy($uuid, $request->validated());

            return response()->json([
                "success" => true,
                "data" => $vacancy,
                "message" => "Vaga atualizada com sucesso."
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
     * Delete a vacancy.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $this->vacancyService->deleteVacancy($uuid);

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Vaga excluída com sucesso."
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
     * Delete vacancies by uuid.
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

            $this->vacancyService->deleteVacanciesByUuids($uuids);

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Vagas excluídas com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Vagas não encontradas."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir as vagas. " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all vacancies.
     *
     * @return JsonResponse
     */
    public function deleteAll(): JsonResponse
    {
        try {
            $this->vacancyService->deleteAllVacancies();

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Todas as vagas foram excluídas com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Vagas não encontradas."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir todas as vagas. " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close a vacancy.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function closeVacancy(string $uuid): JsonResponse
    {
        try {
            $this->vacancyService->closeVacancy($uuid);

            return response()->json([
                "success" => true,
                "message" => "Vaga pausada com sucesso."
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
}
