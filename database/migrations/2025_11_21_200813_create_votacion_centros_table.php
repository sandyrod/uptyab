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
        Schema::create('votacion_centros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ubicacion_id')->constrained('ubicaciones')->onDelete('cascade');
            $table->foreignId('persona_id')->nullable()->constrained('personas')->onDelete('set null');
            $table->string('nombre', 255);
            $table->integer('cantidad_electores');
            $table->integer('codigo', false, true); // Unsigned integer
            $table->timestamps();
            
            // Asegurar que el código sea único
            $table->unique('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votacion_centros');
    }
};
