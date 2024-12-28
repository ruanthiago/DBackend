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
            $this->model->all()
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
        $module->load('permissions');
        return response()->json($module->toArray());
    }

    /**
     * Cria novo módulo
     *
     * @param StoreRequest $request
     * @return JsonResponse
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

            $this->rules($module, $request->permission_ids);
            return response()->json($module->getRawOriginal(), 201);
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

            $this->rules($module, $request->permission_ids);
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

            $this->rule->deleteByIds(
                $module->rules->pluck('id')->toArray()
            );

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
    public function rules(Module $module, array $permissionIds): void
    {
        $this->transaction(function () use ($module, $permissionIds) {
            $moduleRules = $module->rules;
            foreach ($permissionIds as $permissionId) {
                $moduleRule = $moduleRules->where('permission_id', $permissionId)->first();

                if ($moduleRule) { // Remove da lista de regras
                    $moduleRules = $moduleRules->filter(function ($item) use ($moduleRule) {
                        return $item->id !== $moduleRule->id;
                    });

                } else {
                    $data = [
                        'module_id' => $module->id,
                        'permission_id' => $permissionId,
                    ];

                    // Verifica se a regra está excluída, caso esteja, restaura
                    if ($rule = $this->rule->withTrashed()->where($data)->first()) {
                        $rule->restore();
                    } else {
                        $this->rule->create($data);
                    }
                }
            }

            // Regras que foram removidas
            if ($moduleRules->isNotEmpty()) {
                $this->rule->deleteByIds(
                    $moduleRules->pluck('id')->toArray()
                );
            }
        });
    }
}
