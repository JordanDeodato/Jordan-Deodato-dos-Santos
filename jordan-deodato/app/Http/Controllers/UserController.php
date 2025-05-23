<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Retrieve all users.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers();

            return response()->json([
                "success" => true,
                "data" => $users,
                "message" => "Usuários listados com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar os usuários." . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            $user = $this->userService->getUserByUuid($uuid);

            return response()->json([
                "success" => true,
                "data" => $user,
                "message" => "Usuário listado com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar o usuário." . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new User
     *
     * @param UserRequest $request
     *
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return response()->json([
                "success" => true,
                "data" => $user,
                "message" => "Usuário criado com sucesso."
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao criar o usuário." . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing user.
     *
     * @param string $uuid
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function update(string $uuid, UserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($uuid, $request->validated());

            return response()->json([
                "success" => true,
                "data" => $user,
                "message" => "Usuário atualizado com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao atualizar o usuário." . $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $this->userService->deleteUser($uuid);

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Usuário excluído com sucesso."
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao excluir o usuário." . $exception->getMessage()
            ], 500);
        }
    }
}
