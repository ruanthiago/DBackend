<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Verifica se o usuário é um administrador
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     *
     * @throws HTTP_UNAUTHORIZED
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!userLogged()->admin) {
            abort(Response::HTTP_UNAUTHORIZED, 'Only administrators can access this route');
        }

        return $next($request);
    }
}
