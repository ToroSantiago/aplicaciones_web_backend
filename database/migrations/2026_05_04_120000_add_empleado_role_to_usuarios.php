<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el rol 'Empleado' al enum de usuarios.rol y cambia el default
     * a 'Empleado' para que los registros nuevos no sean Administrador por defecto.
     *
     * En Postgres los enums de Laravel se implementan con un CHECK constraint
     * sobre una columna varchar; por eso lo recreamos a mano.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // Buscar el nombre del check constraint existente para 'rol'
            $constraintName = $this->findRolCheckConstraint();

            if ($constraintName) {
                DB::statement("ALTER TABLE usuarios DROP CONSTRAINT \"{$constraintName}\"");
            }

            DB::statement("ALTER TABLE usuarios ALTER COLUMN rol DROP DEFAULT");
            DB::statement(
                "ALTER TABLE usuarios ADD CONSTRAINT usuarios_rol_check " .
                "CHECK (rol IN ('Cliente', 'Empleado', 'Administrador'))"
            );
            DB::statement("ALTER TABLE usuarios ALTER COLUMN rol SET DEFAULT 'Empleado'");
        } else {
            // SQLite / MySQL: trabajamos con la API de Schema.
            // Para SQLite Laravel recrea la tabla; para MySQL usa MODIFY.
            Schema::table('usuarios', function ($table) {
                $table->string('rol')->default('Empleado')->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $constraintName = $this->findRolCheckConstraint();

            if ($constraintName) {
                DB::statement("ALTER TABLE usuarios DROP CONSTRAINT \"{$constraintName}\"");
            }

            DB::statement("ALTER TABLE usuarios ALTER COLUMN rol DROP DEFAULT");
            DB::statement(
                "ALTER TABLE usuarios ADD CONSTRAINT usuarios_rol_check " .
                "CHECK (rol IN ('Cliente', 'Administrador'))"
            );
            DB::statement("ALTER TABLE usuarios ALTER COLUMN rol SET DEFAULT 'Administrador'");
        } else {
            Schema::table('usuarios', function ($table) {
                $table->string('rol')->default('Administrador')->change();
            });
        }
    }

    /**
     * Devuelve el nombre del check constraint asociado a la columna usuarios.rol.
     */
    private function findRolCheckConstraint(): ?string
    {
        $row = DB::selectOne(
            "SELECT con.conname AS name
             FROM pg_constraint con
             JOIN pg_class rel ON rel.oid = con.conrelid
             WHERE rel.relname = 'usuarios'
               AND con.contype = 'c'
               AND pg_get_constraintdef(con.oid) ILIKE '%rol%'
             LIMIT 1"
        );

        return $row?->name;
    }
};
