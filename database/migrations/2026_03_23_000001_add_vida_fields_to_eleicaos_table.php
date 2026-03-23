<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleicaos', function (Blueprint $table) {
            $table->boolean('aberta_vida')->default(false)->after('status');
            $table->timestamp('data_abertura_vida')->nullable()->after('aberta_vida');
            $table->timestamp('data_encerramento_vida')->nullable()->after('data_abertura_vida');
            $table->unsignedBigInteger('aberta_por_vida')->nullable()->after('data_encerramento_vida');
            $table->unsignedBigInteger('encerrada_por_vida')->nullable()->after('aberta_por_vida');
        });
    }

    public function down(): void
    {
        Schema::table('eleicaos', function (Blueprint $table) {
            $table->dropColumn([
                'aberta_vida',
                'data_abertura_vida',
                'data_encerramento_vida',
                'aberta_por_vida',
                'encerrada_por_vida',
            ]);
        });
    }
};
