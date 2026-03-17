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
            $table->integer('qtd_eleitorado')->default(0)->after('qtd_membros');
        });
    }

    public function down(): void
    {
        Schema::table('eleicao_cidades', function (Blueprint $table) {
            $table->dropColumn('qtd_eleitorado');
        });
    }
};
