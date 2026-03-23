<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('token_votacaos', function (Blueprint $table) {
            $table->enum('escopo', ['vida', 'alianca'])->default('alianca')->after('usado');
        });
    }

    public function down(): void
    {
        Schema::table('token_votacaos', function (Blueprint $table) {
            $table->dropColumn('escopo');
        });
    }
};
