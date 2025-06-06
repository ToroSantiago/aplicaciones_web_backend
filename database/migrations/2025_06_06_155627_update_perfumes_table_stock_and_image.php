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
        Schema::table('perfumes', function (Blueprint $table) {
            // Cambiar stock de boolean a integer
            $table->integer('stock')->default(0)->change();
            
            // Agregar campo para imagen de Cloudinary
            $table->string('imagen_url')->nullable()->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perfumes', function (Blueprint $table) {
            // Revertir stock a boolean
            $table->boolean('stock')->default(true)->change();
            
            // Eliminar campo de imagen
            $table->dropColumn('imagen_url');
        });
    }
};