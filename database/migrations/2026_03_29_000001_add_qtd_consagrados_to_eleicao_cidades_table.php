<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->integer('qtd_consagrados')->default(0)->after('qtd_eleitorado');
            $table->integer('qtd_consagrados_vida')->default(0)->after('qtd_consagrados');
            $table->integer('qtd_eleitorado_vida')->default(0)->after('qtd_consagrados_vida');
        });
    }

    public function down(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->dropColumn(['qtd_consagrados', 'qtd_consagrados_vida', 'qtd_eleitorado_vida']);
        });
    }
};
