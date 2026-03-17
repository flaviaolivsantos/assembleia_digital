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
        Schema::table('votos', function (Blueprint $table) {
            $table->string('origem')->default('remoto')->after('opcao_id');
            $table->unsignedBigInteger('maquina_id')->nullable()->after('origem');
            $table->foreign('maquina_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('votos', function (Blueprint $table) {
            $table->dropForeign(['maquina_id']);
            $table->dropColumn(['origem', 'maquina_id']);
        });
    }
};
