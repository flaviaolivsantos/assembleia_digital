<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            $table->enum('escopo', ['vida', 'alianca'])->default('alianca')->after('votou');
        });
    }

    public function down(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            $table->dropColumn('escopo');
        });
    }
};
