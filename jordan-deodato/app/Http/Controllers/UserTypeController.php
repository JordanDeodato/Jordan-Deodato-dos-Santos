<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Exception;
use Illuminate\Http\JsonResponse;

class UserTypeController extends Controller
{
    /**
     * Retrieve all users type.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $usertype = UserType::all();

            return response()->json([
                "success" => true,
                "data" => $usertype,
                "message" => "Tipo de usuários listados com sucesso."
            ]);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar os tipos de usuários. " . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a user type.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $userType = UserType::where('id', $id)->first();

            return response()->json([
                "success" => true,
                "data" => $userType,
                "message" => "Tipo de usuário listado com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar o tipo de usuário. " . $exception->getMessage()
            ], 500);
        }
    }
}
