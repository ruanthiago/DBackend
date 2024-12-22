<?php

namespace App\Traits;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

trait DatabaseTrait
{
    /**
     * Método responsável por executar uma transação no banco de dados
     *
     * @param Closure $callback
     * @return mixed
     *
     * @throws HTTP_INTERNAL_SERVER_ERROR
     */
    public static function transaction(Closure $callback): mixed
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $th->getMessage());
        }
    }
}
