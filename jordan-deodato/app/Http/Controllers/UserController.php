<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

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
            $perPage = (int) $request->query('per_page', 20);

            $filters = $request->only(['uuid', 'user_type_id', 'name', 'cpf', 'email', 'order_by', 'order_direction']);
            $responseDto = $this->userService->getAllUsers($perPage, $filters);

            return response()->json([
                "success" => true,
                "data" => $responseDto->getData(),
                "meta" => $responseDto->getMeta(),
                "message" => "Usuários listados com sucesso."
            ]);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar os usuários. " . $e->getMessage()
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
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Falha ao listar o usuário. " . $e->getMessage()
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

        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro inesperado. " . $e->getMessage()
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

        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro inesperado. " . $e->getMessage()
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

        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro inesperado. " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete users by uuid.
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

            $this->userService->deleteUsersByUuids($uuids);

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Usuários foram excluídos com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Usuários não encontrados."
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir os usuários. " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all users.
     *
     * @return JsonResponse
     */
    public function deleteAll(): JsonResponse
    {
        try {
            $this->userService->deleteAllUsers();

            return response()->json([
                "success" => true,
                "data" => ["deleted" => true],
                "message" => "Todas os usuários foram excluídos com sucesso."
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "Usuários não o."
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao excluir todas os usuários. " . $e->getMessage()
            ], 500);
        }
    }
}
