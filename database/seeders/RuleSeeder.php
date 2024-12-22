<?php

namespace Database\Seeders;

use App\Models\Rule\Rule;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Executa os seeds no banco de dados
     */
    public function run(): void
    {
        // Módulos
        Rule::create(['module_id' => 1, 'permission_id' => 1]);
        Rule::create(['module_id' => 1, 'permission_id' => 2]);
        Rule::create(['module_id' => 1, 'permission_id' => 3]);
        Rule::create(['module_id' => 1, 'permission_id' => 4]);

        // Permissões
        Rule::create(['module_id' => 2, 'permission_id' => 1]);
        Rule::create(['module_id' => 2, 'permission_id' => 2]);
        Rule::create(['module_id' => 2, 'permission_id' => 3]);
        Rule::create(['module_id' => 2, 'permission_id' => 4]);
    }
}
