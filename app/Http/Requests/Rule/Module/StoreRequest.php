<?php

namespace App\Http\Requests\Rule\Module;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ];
    }
}
