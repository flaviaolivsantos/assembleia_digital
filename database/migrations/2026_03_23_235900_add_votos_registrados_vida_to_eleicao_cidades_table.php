<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->unsignedInteger('votos_registrados_vida')->default(0)->after('votos_presenciais_vida');
        });
    }

    public function down(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->dropColumn('votos_registrados_vida');
        });
    }
};
