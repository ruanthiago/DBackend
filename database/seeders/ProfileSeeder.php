<?php

namespace Database\Seeders;

use App\Models\Profile\Profile;
use App\Models\Tenant\Tenant;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Executa os seeds no banco de dados
     */
    public function run(): void
    {
        Profile::create(['id' => 1, 'tenant_id' => 1, 'name' => 'Perfil 01']);
    }
}
