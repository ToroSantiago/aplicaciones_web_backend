<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Paso 1: Eliminar el default existente
        DB::statement('ALTER TABLE perfumes ALTER COLUMN stock DROP DEFAULT');
        
        // Paso 2: Convertir el tipo de datos
        DB::statement('ALTER TABLE perfumes ALTER COLUMN stock TYPE integer USING (CASE WHEN stock = true THEN 10 ELSE 0 END)');
        
        // Paso 3: Establecer el nuevo default
        DB::statement('ALTER TABLE perfumes ALTER COLUMN stock SET DEFAULT 0');
        
        // Paso 4: Agregar campo para imagen
        Schema::table('perfumes', function (Blueprint $table) {
            $table->string('imagen_url')->nullable()->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar campo de imagen
        Schema::table('perfumes', function (Blueprint $table) {
            $table->dropColumn('imagen_url');
        });
        
        // Eliminar default
        DB::statement('ALTER TABLE perfumes ALTER COLUMN stock DROP DEFAULT');
        
        // Convertir stock de vuelta a boolean
        DB::statement('ALTER TABLE perfumes ALTER COLUMN stock TYPE boolean USING (stock > 0)');
        
        // Restaurar default
        DB::statement('ALTER TABLE perfumes ALTER COLUMN stock SET DEFAULT true');
    }
};