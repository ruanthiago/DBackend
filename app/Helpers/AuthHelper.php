<?php

use App\Models\User;

/**
 * Retorna o usuário logado
 *
 * @return User
 *
 * @todo Depois que fizer a autenticação, substituir o retorno por `auth()->user()`
 */
function userLogged(): User
{
    return User::find(1);
}
