<?php

namespace Database\Seeders;

use App\Models\Rule\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Executa os seeds no banco de dados
     */
    public function run(): void
    {
        Module::create(['id' => 1, 'name' => 'Módulos']);
        Module::create(['id' => 2, 'name' => 'Permissões']);
        Module::create(['id' => 3, 'name' => 'Inquilinos']);
        Module::create(['id' => 4, 'name' => 'Perfis']);
    }
}
