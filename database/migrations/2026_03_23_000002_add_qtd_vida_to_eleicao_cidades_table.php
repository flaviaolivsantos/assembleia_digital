<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->integer('qtd_vida')->default(0)->after('qtd_eleitorado');
        });
    }

    public function down(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->dropColumn('qtd_vida');
        });
    }
};
