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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('restrict');
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('completada');
            $table->string('metodo_pago')->nullable(); // efectivo, tarjeta, transferencia
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index('usuario_id');
            $table->index('created_at');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};