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
        Schema::table('opcaos', function (Blueprint $table) {
            $table->dropForeign(['cidade_id']);
            $table->unsignedBigInteger('cidade_id')->nullable()->change();
            $table->foreign('cidade_id')->references('id')->on('cidades')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('opcaos', function (Blueprint $table) {
            $table->dropForeign(['cidade_id']);
            $table->unsignedBigInteger('cidade_id')->nullable(false)->change();
            $table->foreign('cidade_id')->references('id')->on('cidades')->cascadeOnDelete();
        });
    }
};
