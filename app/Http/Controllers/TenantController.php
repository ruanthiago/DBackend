<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreRequest;
use App\Http\Requests\Tenant\UpdateRequest;
use App\Models\Tenant\Tenant;
use App\Models\Tenant\TenantRule;
use App\Traits\DatabaseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TenantController extends Controller
{
    use DatabaseTrait;

    public function __construct(
        protected Tenant $model,
        protected TenantRule $tenantRule,
    ) {}

    /**
     * Retorna todos os inquilinos
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
     * Retorna os dados do inquilino
     *
     * @param Tenant $tenant
     * @return JsonResponse
     *
     * @throws HTTP_NOT_FOUND
     */
    public function find(Tenant $tenant): JsonResponse
    {
        return response()->json([
             ...$tenant->toArray(),
            'modules' => modules($tenant->rules),
        ]);
    }

    /**
     * Cria novo inquilino
     *
     * @param StoreRequest $request
     * @return JsonResponse
     *
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->transaction(function () use ($request) {
            $tenant = $this->model->create($request->validated());

            if (!$tenant) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            $this->rules($tenant, $request->rule_ids);
            return response()->json($tenant, 201);
        });
    }

    /**
     * Atualiza os dados do inquilino
     *
     * @param UpdateRequest $request
     * @param Tenant $tenant
     * @return Response
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function update(UpdateRequest $request, Tenant $tenant): Response
    {
        return $this->transaction(function () use ($request, $tenant) {
            if (!$tenant->update($request->validated())) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            $this->rules($tenant, $request->rule_ids);
            return response()->noContent();
        });
    }

    /**
     * Exclui o inquilino
     *
     * @param Tenant $tenant
     * @return Response
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function destroy(Tenant $tenant): Response
    {
        return $this->transaction(function () use ($tenant) {
            if (!$tenant->delete()) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to delete");
            }

            $tenant->tenantRules()->delete();
            return response()->noContent();
        });
    }

    /**
     * Salva as regras
     *
     * @param int $tenantId
     * @param array $ruleIds
     * @return void
     */
    public function rules(Tenant $tenant, array $ruleIds): void
    {
        $this->transaction(function () use ($tenant, $ruleIds) {
            $tenantRules = $tenant->tenantRules;
            foreach ($ruleIds as $ruleId) {
                $tenantRule = $tenantRules->where('rule_id', $ruleId)->first();

                if ($tenantRule) { // Remove da lista de regras
                    $tenantRules = $tenantRules->filter(function ($item) use ($tenantRule) {
                        return $item->id !== $tenantRule->id;
                    });

                } else {
                    $data = [
                        'tenant_id' => $tenant->id,
                        'rule_id' => $ruleId,
                    ];

                    // Verifica se a regra está excluída, caso esteja, restaura
                    if ($tenantRule = $this->tenantRule->withTrashed()->where($data)->first()) {
                        $tenantRule->restore();
                    } else {
                        $this->tenantRule->create($data);
                    }
                }
            }

            // Regras que foram removidas
            if ($tenantRules->isNotEmpty()) {
                $this->tenantRule
                    ->whereIn('id', $tenantRules->pluck('id')->toArray())
                    ->delete();
            }
        });
    }
}
