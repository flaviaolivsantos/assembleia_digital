<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->unsignedInteger('qtd_presencial_vida')->default(0)->after('qtd_vida');
            $table->unsignedInteger('votos_presenciais_vida')->default(0)->after('qtd_presencial_vida');
        });
    }

    public function down(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->dropColumn(['qtd_presencial_vida', 'votos_presenciais_vida']);
        });
    }
};
