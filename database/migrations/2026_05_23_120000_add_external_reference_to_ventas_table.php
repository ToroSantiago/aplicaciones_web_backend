<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vincula una Venta con un pago de MercadoPago via external_reference.
     *
     * Lo generamos al crear la preferencia de pago y lo usamos en el webhook
     * para localizar la venta y marcarla como completada/cancelada según el
     * estado real del pago.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Nullable porque las ventas que no pasan por MP (cash, transferencia
            // directa, etc) no tienen referencia externa.
            $table->string('external_reference')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropUnique(['external_reference']);
            $table->dropColumn('external_reference');
        });
    }
};
