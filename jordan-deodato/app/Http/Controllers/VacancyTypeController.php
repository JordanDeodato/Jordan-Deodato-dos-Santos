<?php

namespace App\Http\Controllers;

use App\Models\VacancyType;
use Exception;
use Illuminate\Http\JsonResponse;

class VacancyTypeController extends Controller
{
    /**
     * Retrieve all vacancy type.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $vacancyType = VacancyType::all();

            return response()->json([
                "success" => true,
                "data" => $vacancyType,
                "message" => "Tipos de vaga listados com sucesso."
            ]);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar os tipos de vaga. " . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a vacancy type.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $vacancyType = VacancyType::where('id', $id)->first();

            return response()->json([
                "success" => true,
                "data" => $vacancyType,
                "message" => "Tipo de vaga listado com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar o tipo de vaga. " . $exception->getMessage()
            ], 500);
        }
    }
}
