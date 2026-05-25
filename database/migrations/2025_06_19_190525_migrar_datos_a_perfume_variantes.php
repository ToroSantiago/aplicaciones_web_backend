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
        // Primero, migrar los datos existentes a la nueva tabla
        $perfumes = DB::table('perfumes')->get();
        
        foreach ($perfumes as $perfume) {
            // Crear una variante con los datos actuales del perfume
            DB::table('perfume_variantes')->insert([
                'perfume_id' => $perfume->id,
                'volumen' => $perfume->volumen,
                'precio' => $perfume->precio,
                'stock' => $perfume->stock,
                'created_at' => $perfume->created_at,
                'updated_at' => $perfume->updated_at,
            ]);
        }
        
        // Ahora eliminar las columnas que ya no necesitamos en la tabla perfumes
        Schema::table('perfumes', function (Blueprint $table) {
            $table->dropColumn(['volumen', 'precio', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Agregar las columnas de vuelta
        Schema::table('perfumes', function (Blueprint $table) {
            $table->integer('volumen')->after('descripcion');
            $table->decimal('precio', 10, 2)->after('volumen');
            $table->integer('stock')->default(0)->after('genero');
        });
        
        // Migrar el primer registro de cada perfume de vuelta
        $variantes = DB::table('perfume_variantes')
            ->select('perfume_id', DB::raw('MIN(id) as min_id'))
            ->groupBy('perfume_id')
            ->get();
            
        foreach ($variantes as $variante) {
            $data = DB::table('perfume_variantes')->find($variante->min_id);
            if ($data) {
                DB::table('perfumes')
                    ->where('id', $data->perfume_id)
                    ->update([
                        'volumen' => $data->volumen,
                        'precio' => $data->precio,
                        'stock' => $data->stock,
                    ]);
            }
        }
        
        // Eliminar la tabla de variantes
        Schema::dropIfExists('perfume_variantes');
    }
};