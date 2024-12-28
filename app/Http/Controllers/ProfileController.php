<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreRequest;
use App\Http\Requests\Profile\UpdateRequest;
use App\Models\Profile\Profile;
use App\Models\Profile\ProfileRule;
use App\Models\Tenant\Tenant;
use App\Traits\DatabaseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    use DatabaseTrait;

    public function __construct(
        protected Profile $model,
        protected ProfileRule $profileRule,
    ) {}

    /**
     * Retorna todos os perfis do inquilino
     *
     * @return JsonResponse
     */
    public function all(Tenant $tenant): JsonResponse
    {
        return response()->json(
            $this->model
                ->whereTenant($tenant)
                ->get()
        );
    }

    /**
     * Retorna os dados do perfil
     *
     * @param Tenant $tenant
     * @param Profile $profile
     * @return JsonResponse
     *
     * @throws HTTP_NOT_FOUND
     */
    public function find(Tenant $tenant, Profile $profile): JsonResponse
    {
        if ($profile->tenant_id !== $tenant->id) {
            abort(Response::HTTP_NOT_FOUND, "Profile not found");
        }

        // Só libera as regras que o inquilino tem
        $tenantRuleIds = $tenant->rules->pluck('id')->toArray();
        $profileRules = $profile->rules->filter(function ($item) use ($tenantRuleIds) {
            return in_array($item->id, $tenantRuleIds);
        });

        return response()->json([
             ...$profile->getRawOriginal(),
            'modules' => modules($profileRules),
        ]);
    }

    /**
     * Cria novo perfil
     *
     * @param StoreRequest $request
     * @param Tenant $tenant
     * @return JsonResponse
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function store(StoreRequest $request, Tenant $tenant): JsonResponse
    {
        return $this->transaction(function () use ($request, $tenant) {
            $profile = $this->model->create([
                'tenant_id' => $tenant->id,
                ...$request->validated(),
            ]);

            if (!$profile) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            $this->rules($profile, $request->rule_ids);
            return response()->json($profile->getRawOriginal(), 201);
        });
    }

    /**
     * Atualiza os dados do perfil
     *
     * @param UpdateRequest $request
     * @param Tenant $tenant
     * @param Profile $profile
     * @return Response
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function update(UpdateRequest $request, Tenant $tenant, Profile $profile): Response
    {
        if ($profile->tenant_id !== $tenant->id) {
            abort(Response::HTTP_NOT_FOUND, "Profile not found");
        }

        return $this->transaction(function () use ($request, $profile) {
            if (!$profile->update($request->validated())) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to save");
            }

            $this->rules($profile, $request->rule_ids);
            return response()->noContent();
        });
    }

    /**
     * Exclui o perfil
     *
     * @param Tenant $tenant
     * @param Profile $profile
     * @return Response
     *
     * @throws HTTP_NOT_FOUND
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public function destroy(Tenant $tenant, Profile $profile): Response
    {
        if ($profile->tenant_id !== $tenant->id) {
            abort(Response::HTTP_NOT_FOUND, "Profile not found");
        }

        return $this->transaction(function () use ($profile) {
            if (!$profile->delete()) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Unable to delete");
            }

            $profile->profileRules()->delete();
            return response()->noContent();
        });
    }

    /**
     * Salva as regras
     *
     * @param Profile $profile
     * @param array $ruleIds
     * @return void
     */
    public function rules(Profile $profile, array $ruleIds): void
    {
        $this->transaction(function () use ($profile, $ruleIds) {
            $profileRules = $profile->profileRules;
            foreach ($ruleIds as $ruleId) {
                $profileRule = $profileRules->where('rule_id', $ruleId)->first();

                if ($profileRule) { // Remove da lista de regras
                    $profileRules = $profileRules->filter(function ($item) use ($profileRule) {
                        return $item->id !== $profileRule->id;
                    });

                } else {
                    $data = [
                        'profile_id' => $profile->id,
                        'rule_id' => $ruleId,
                    ];

                    // Verifica se a regra está excluída, caso esteja, restaura
                    if ($profileRule = $this->profileRule->withTrashed()->where($data)->first()) {
                        $profileRule->restore();
                    } else {
                        $this->profileRule->create($data);
                    }
                }
            }

            // Regras que foram removidas
            if ($profileRules->isNotEmpty()) {
                $this->profileRule
                    ->whereIn('id', $profileRules->pluck('id')->toArray())
                    ->delete();
            }
        });
    }
}
