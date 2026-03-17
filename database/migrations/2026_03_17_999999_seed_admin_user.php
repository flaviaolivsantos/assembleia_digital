<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('users')->where('email', 'admin@assembleia.com')->exists();

        if (!$exists) {
            DB::table('users')->insert([
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

    public function down(): void
    {
        DB::table('users')->where('email', 'admin@assembleia.com')->delete();
    }
};
