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
        Schema::create('eleicao_cidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleicao_id')->constrained('eleicaos')->cascadeOnDelete();
            $table->foreignId('cidade_id')->constrained('cidades')->cascadeOnDelete();
            $table->integer('qtd_membros')->default(0);
            $table->integer('votos_registrados')->default(0);
            $table->boolean('aberta')->default(false);
            $table->timestamp('data_abertura')->nullable();
            $table->timestamp('data_encerramento')->nullable();
            $table->foreignId('aberta_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('encerrada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eleicao_cidades');
    }
};
