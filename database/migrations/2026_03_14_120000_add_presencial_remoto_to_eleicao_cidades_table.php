<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->integer('qtd_presencial')->default(0)->after('qtd_membros');
            $table->integer('qtd_remoto')->default(0)->after('qtd_presencial');
            $table->integer('votos_presenciais')->default(0)->after('votos_registrados');
        });
    }

    public function down(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->dropColumn(['qtd_presencial', 'qtd_remoto', 'votos_presenciais']);
        });
    }
};
