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
    }
}
