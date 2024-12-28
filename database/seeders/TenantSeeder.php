<?php

namespace Database\Seeders;

use App\Models\Tenant\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Executa os seeds no banco de dados
     */
    public function run(): void
    {
        Tenant::create(['id' => 1, 'name' => 'Administrativo']);
    }
}
