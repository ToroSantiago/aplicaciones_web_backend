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
        // Crear tabla de variantes
        Schema::create('perfume_variantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfume_id')->constrained('perfumes')->onDelete('cascade');
            $table->integer('volumen'); // 75, 100, 200
            $table->decimal('precio', 10, 2); // Hasta 10 dígitos, 2 decimales
            $table->integer('stock')->default(0);
            $table->timestamps();
            
            // Índice único para evitar duplicados del mismo volumen para un perfume
            $table->unique(['perfume_id', 'volumen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfume_variantes');
    }
};