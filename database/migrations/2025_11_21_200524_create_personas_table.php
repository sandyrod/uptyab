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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->string('cedula', 20);
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('cuenta', 50)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('cargo', 100)->nullable();
            $table->timestamps();
            
            // Asegurar que la cédula sea única
            $table->unique('cedula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
