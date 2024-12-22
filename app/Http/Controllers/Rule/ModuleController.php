<?php

namespace App\Http\Controllers\Rule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rule\Module\StoreRequest;
use App\Http\Requests\Rule\Module\UpdateRequest;
use App\Models\Rule\Module;
use App\Models\Rule\Rule;
use App\Traits\DatabaseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ModuleController extends Controller
{
    use DatabaseTrait;

    public function __construct(
        protected Module $model,
        protected Rule $rule,
    ) {}

    /**
     * Retorna todos os módulos
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return response()->json(
            $this->model->with('permissions')->get()
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
        return response()->json([
             ...$module->toArray(),
            'permissions' => $module->permissions->toArray(),
        ]);
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
            $module = $this->model->create($request->validated());

            if (!$module) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            $this->rules($module->id, $request->permission_ids);
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
            if (!$module->update($request->validated())) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            $this->rules($module->id, $request->permission_ids);
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

            $this->rule->where('module_id', $module->id)->delete();
            return response()->noContent();
        });
    }

    /**
     * Salva as regras
     *
     * @param int $moduleId
     * @param array $permissionIds
     * @return void
     */
    public function rules(int $moduleId, array $permissionIds): void
    {
        // Exclui as regras relacionadas ao módulo
        $this->rule->where('module_id', $moduleId)->delete();

        // Cria ou restaura a regra
        foreach ($permissionIds as $permissionId) {
            $this->rule->withTrashed()->updateOrCreate([
                'module_id' => $moduleId,
                'permission_id' => $permissionId,
            ], [
                'deleted_at' => null,
            ]);
        }
    }
}
