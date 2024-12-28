<?php

namespace Database\Seeders;

use App\Models\Rule\Rule;
use App\Models\Tenant\TenantRule;
use Illuminate\Database\Seeder;

class TenantRuleSeeder extends Seeder
{
    /**
     * Executa os seeds no banco de dados
     */
    public function run(): void
    {
        // Administrativo
        foreach (Rule::all() as $rule) {
            TenantRule::create(['tenant_id' => 1, 'rule_id' => $rule->id]);
        }
    }
}
