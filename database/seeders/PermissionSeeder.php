<?php

namespace Database\Seeders;

use App\Models\Rule\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Executa os seeds no banco de dados
     */
    public function run(): void
    {
        Permission::create(['id' => 1, 'name' => 'Visualizar']);
        Permission::create(['id' => 2, 'name' => 'Adicionar']);
        Permission::create(['id' => 3, 'name' => 'Editar']);
        Permission::create(['id' => 4, 'name' => 'Excluir']);
    }
}
