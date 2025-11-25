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
        Schema::table('afluencias', function (Blueprint $table) {
            $table->foreignId('votacion_centro_id')
                  ->after('evento_id')
                  ->nullable()
                  ->constrained('votacion_centros')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afluencias', function (Blueprint $table) {
            $table->dropForeign(['votacion_centro_id']);
            $table->dropColumn('votacion_centro_id');
        });
    }
};