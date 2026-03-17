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
        Schema::create('opcaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pergunta_id')->constrained('perguntas')->cascadeOnDelete();
            $table->foreignId('cidade_id')->constrained('cidades')->cascadeOnDelete();
            $table->string('nome');
            $table->string('foto')->nullable();
            $table->integer('ordem')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcaos');
    }
};
