<?php

use Illuminate\Database\Eloquent\Collection;

/**
 * Retorna os módulos com as suas permissões
 *
 * @param Collection $rules
 * @return array
 */
function modules(Collection $rules): array
{
    $modules = [];
    foreach ($rules as $rule) {
        $moduleId = $rule->module_id;

        if (!isset($modules[$moduleId])) {
            $modules[$moduleId] = $rule->module->toArray();
        }

        $modules[$moduleId]['permissions'][] = $rule->permission->toArray();
    }

    return array_values($modules);
}
