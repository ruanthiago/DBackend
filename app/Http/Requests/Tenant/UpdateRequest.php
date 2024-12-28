<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta solicitação
     *
     * @return bool
     *
     * @throws HTTP_FORBIDDEN
     */
    public function authorize(): bool
    {
        return userLogged()->admin;
    }

    /**
     * Regras de validação que se aplicam à solicitação
     *
     * @return array
     *
     * @throws HTTP_UNPROCESSABLE_ENTITY
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'rule_ids' => 'required|array',
            'rule_ids.*' => 'exists:rules,id',
        ];
    }
}
