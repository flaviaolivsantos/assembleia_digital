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
        Schema::table('perguntas', function (Blueprint $table) {
            $table->string('escopo')->default('alianca')->after('qtd_respostas');
        });
    }

    public function down(): void
    {
        Schema::table('perguntas', function (Blueprint $table) {
            $table->dropColumn('escopo');
        });
    }
};
