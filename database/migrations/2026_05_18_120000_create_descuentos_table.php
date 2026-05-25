<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de descuentos + pivot M2M con perfume_variantes.
     *
     * Un descuento tiene un % y un rango de fechas (vigencia). Se aplica a
     * una o más variantes específicas (no al perfume entero), por lo que
     * el admin puede ofertar solo el de 75ml si quiere.
     */
    public function up(): void
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('porcentaje', 5, 2); // 0.00 - 100.00
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['activo', 'fecha_inicio', 'fecha_fin'], 'descuentos_vigencia_idx');
        });

        // CHECK constraint para validar el rango del porcentaje a nivel DB
        // (sqlite no soporta ALTER ADD CHECK; lo aplicamos solo en pgsql/mysql).
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['pgsql', 'mysql'], true)) {
            DB::statement(
                'ALTER TABLE descuentos ADD CONSTRAINT descuentos_porcentaje_check ' .
                'CHECK (porcentaje > 0 AND porcentaje <= 100)'
            );
            DB::statement(
                'ALTER TABLE descuentos ADD CONSTRAINT descuentos_fechas_check ' .
                'CHECK (fecha_fin >= fecha_inicio)'
            );
        }

        Schema::create('descuento_perfume_variante', function (Blueprint $table) {
            $table->foreignId('descuento_id')
                ->constrained('descuentos')
                ->onDelete('cascade');
            $table->foreignId('perfume_variante_id')
                ->constrained('perfume_variantes')
                ->onDelete('cascade');

            $table->primary(['descuento_id', 'perfume_variante_id'], 'desc_var_pkey');
            $table->index('perfume_variante_id', 'desc_var_variante_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descuento_perfume_variante');

        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['pgsql', 'mysql'], true)) {
            DB::statement('ALTER TABLE descuentos DROP CONSTRAINT IF EXISTS descuentos_porcentaje_check');
            DB::statement('ALTER TABLE descuentos DROP CONSTRAINT IF EXISTS descuentos_fechas_check');
        }

        Schema::dropIfExists('descuentos');
    }
};
