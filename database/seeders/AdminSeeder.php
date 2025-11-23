<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@cemiterio.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'tipo_usuario' => 'admin',
            'ativo' => true,
            'empresa_id' => null,
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@cemiterio.com',
            'email_verified_at' => now(),
            'password' => Hash::make('super123'),
            'tipo_usuario' => 'admin',
            'ativo' => true,
            'empresa_id' => null,
        ]);
    }
}
