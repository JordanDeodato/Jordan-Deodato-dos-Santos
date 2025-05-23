<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateRequest;
use App\Services\CandidateService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar os candidatos. " . $exception->getMessage()
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
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar a candidatura. " . $exception->getMessage()
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
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao criar o candidaturo. " . $exception->getMessage()
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
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao atualizar o candidaturo. " . $exception->getMessage()
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
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao excluir o candidato. " . $exception->getMessage()
            ], 500);
        }
    }
}
