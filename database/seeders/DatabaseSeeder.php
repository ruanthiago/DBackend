<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Semeia o banco de dados
     */
    public function run(): void
    {
        // Cria um usuário de desenvolvimento
        User::factory()->create([
            'name' => 'DEV',
            'email' => 'dev@dev.com',
            'password' => Hash::make('123456'),
            'admin' => true,
        ]);

        $this->call([
            ModuleSeeder::class,
        ]);
    }
}
