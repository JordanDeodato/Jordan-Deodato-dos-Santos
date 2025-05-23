<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacancyRequest;
use App\Services\VacancyService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;

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
            $vacancies = $this->vacancyService->getAllVacancies();

            return response()->json([
                "success" => true,
                "data" => $vacancies,
                "message" => "Vagas listadas com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar as vagas." . $exception->getMessage()
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
                "message" => "Falha ao listar a vaga." . $exception->getMessage()
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
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao criar a vaga." . $exception->getMessage()
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
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao atualizar a vaga." . $exception->getMessage()
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
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao excluir a vaga." . $exception->getMessage()
            ], 500);
        }
    }
}
