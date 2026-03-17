<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $missoes = ['Tatuí', 'Fortaleza'];

        foreach ($missoes as $nome) {
            DB::table('cidades')->insertOrIgnore([
                'nome'       => $nome,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('users')->insertOrIgnore([
            'nome'       => 'Administrador',
            'email'      => 'admin@assembleia.com',
            'password'   => Hash::make('Admin@2026'),
            'perfil'     => 'admin',
            'cidade_id'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
