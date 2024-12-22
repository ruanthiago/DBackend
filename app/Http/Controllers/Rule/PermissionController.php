<?php

namespace App\Http\Controllers\Rule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rule\Permission\StoreRequest;
use App\Http\Requests\Rule\Permission\UpdateRequest;
use App\Models\Rule\Permission;
use App\Models\Rule\Rule;
use App\Traits\DatabaseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    use DatabaseTrait;

    public function __construct(
        protected Permission $model,
        protected Rule $rule,
    ) {}

    /**
     * Retorna todas as permissões
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return response()->json(
            $this->model->all(),
        );
    }

    /**
     * Retorna os dados da permissão
     *
     * @param Permission $permission
     * @return JsonResponse
     *
     * @throws HTTP_NOT_FOUND
     */
    public function find(Permission $permission): JsonResponse
    {
        return response()->json($permission);
    }

    /**
     * Cria nova permissão
     *
     * @param StoreRequest $request
     * @return Response
     *
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->transaction(function () use ($request) {
            $permission = $this->model->create($request->validated());

            if (!$permission) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            return response()->json($permission, 201);
        });
    }

    /**
     * Atualiza os dados da permissão
     *
     * @param UpdateRequest $request
     * @param Permission $permission
     * @return Response
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function update(UpdateRequest $request, Permission $permission): Response
    {
        return $this->transaction(function () use ($request, $permission) {
            if (!$permission->update($request->validated())) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            return response()->noContent();
        });
    }

    /**
     * Exclui a permissão
     *
     * @param Permission $permission
     * @return Response
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function destroy(Permission $permission): Response
    {
        return $this->transaction(function () use ($permission) {
            if (!$permission->delete()) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to delete");
            }

            $this->rule->where('permission_id', $permission->id)->delete();
            return response()->noContent();
        });
    }
}
