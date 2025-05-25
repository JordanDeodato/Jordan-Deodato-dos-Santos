<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessRuleException;
use App\Http\Requests\CandidateRequest;
use App\Services\CandidateService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CandidateController extends Controller
{
    private $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    /**
     * Retrieve all candidates.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 20);
            $filters = $request->only(['uuid', 'user_uuid', 'resume', 'education_id', 'experience', 'skills', 'linkedin_profile', 'order_by', 'order_direction']);
            $responseDto = $this->candidateService->getAllCandidates($perPage, $filters);

            return response()->json([
                "success" => true,
                "data" => $responseDto->getData(),
                "meta" => $responseDto->getMeta(),
                "message" => "Candidatos listados com sucesso."
            ]);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar os candidatos. " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a candidate.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            $candidate = $this->candidateService->getCandidateByUuid($uuid);

            return response()->json([
                "success" => true,
                "data" => $candidate,
                "message" => "Candidato listado com sucesso."
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar a candidatura. " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new candidate
     *
     * @param CandidateRequest $request
     *
     * @return JsonResponse
     */
    public function store(CandidateRequest $request): JsonResponse
    {
        try {
            $candidate = $this->candidateService->createCandidate($request->validated());

            return response()->json([
                "success" => true,
                "data" => $candidate,
                "message" => "Candidato criado com sucesso."
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
     * Update an existing candidate.
     *
     * @param string $uuid
     * @param CandidateRequest $request
     * @return JsonResponse
     */
    public function update(string $uuid, CandidateRequest $request): JsonResponse
    {
        try {
            $user = $this->candidateService->updateCandidate($uuid, $request->validated());

            return response()->json([
                "success" => true,
                "data" => $user,
                "message" => "Candidato atualizado com sucesso."
            ], 200);
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
     * Delete a candidate.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $this->candidateService->deleteCandidate($uuid);

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Candidato excluído com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

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
     * Delete candidates by uuid.
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

            $this->candidateService->deleteCandidatesByUuids($uuids);

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Candidatos foram excluídas com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Candidatos não encontradas."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir os candidatos. " . $e->getMessage()
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
            $this->candidateService->deleteAllCandidates();

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Todas os candidatos foram excluídas com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Candidatos não encontradas."
            ], 404);

        } catch (BusinessRuleException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir todas os candidatos. " . $e->getMessage()
            ], 500);
        }
    }
}
