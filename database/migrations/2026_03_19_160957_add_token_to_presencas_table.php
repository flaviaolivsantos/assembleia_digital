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
        Schema::table('presencas', function (Blueprint $table) {
            $table->string('token', 6)->nullable()->after('nome');
        });
    }

    public function down(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
