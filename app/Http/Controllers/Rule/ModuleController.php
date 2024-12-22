<?php

namespace App\Http\Controllers\Rule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rule\Module\StoreRequest;
use App\Http\Requests\Rule\Module\UpdateRequest;
use App\Models\Rule\Module;
use App\Traits\DatabaseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ModuleController extends Controller
{
    use DatabaseTrait;

    public function __construct(
        protected Module $model,
    ) {}

    /**
     * Retorna todos os módulos
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
     * Retorna os dados do módulo
     *
     * @param Module $module
     * @return JsonResponse
     *
     * @throws HTTP_NOT_FOUND
     */
    public function find(Module $module): JsonResponse
    {
        return response()->json($module);
    }

    /**
     * Cria novo módulo
     *
     * @param StoreRequest $request
     * @return Response
     *
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->transaction(function () use ($request) {
            if (!($module = $this->model->create($request->validated()))) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            return response()->json($module, 201);
        });
    }

    /**
     * Atualiza os dados do módulo
     *
     * @param UpdateRequest $request
     * @param Module $module
     * @return Response
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function update(UpdateRequest $request, Module $module): Response
    {
        return $this->transaction(function () use ($request, $module) {
            if (!($module->update($request->validated()))) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            return response()->noContent();
        });
    }

    /**
     * Exclui o módulo
     *
     * @param Module $module
     * @return Response
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function destroy(Module $module): Response
    {
        return $this->transaction(function () use ($module) {
            if (!$module->delete()) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to delete");
            }

            return response()->noContent();
        });
    }
}
