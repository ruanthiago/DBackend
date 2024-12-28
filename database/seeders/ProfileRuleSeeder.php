<?php

namespace Database\Seeders;

use App\Models\Profile\ProfileRule;
use App\Models\Rule\Rule;
use Illuminate\Database\Seeder;

class ProfileRuleSeeder extends Seeder
{
    /**
     * Executa os seeds no banco de dados
     */
    public function run(): void
    {
        foreach (Rule::all() as $rule) {
            ProfileRule::create(['profile_id' => 1, 'rule_id' => $rule->id]);
        }
    }
}
