<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
    }
}
