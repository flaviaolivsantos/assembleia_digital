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
        Schema::create('token_votacaos', function (Blueprint $table) {
            $table->id();
            $table->string('token_hash')->unique();
            $table->foreignId('eleicao_id')->constrained('eleicaos')->cascadeOnDelete();
            $table->foreignId('cidade_id')->constrained('cidades')->cascadeOnDelete();
            $table->boolean('usado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_votacaos');
    }
};
