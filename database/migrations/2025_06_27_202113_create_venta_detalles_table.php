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
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('perfume_variante_id')->constrained('perfume_variantes')->onDelete('restrict');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2); // Guardamos el precio al momento de la venta
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            // Índices
            $table->index('venta_id');
            $table->index('perfume_variante_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};