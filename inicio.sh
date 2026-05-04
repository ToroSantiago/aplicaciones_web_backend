#!/bin/bash

# Configuración
ADMIN_EMAIL="admin@admin.com"
ADMIN_PASSWORD="admin"  # ¡Solo para desarrollo!

# Paso 2: Limpiar cache y generar claves
echo "🚀 Preparando entorno Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan key:generate

# Paso 3: Migrar tablas
echo "📦 Ejecutando migraciones..."
php artisan migrate:fresh --seed

# Paso 4: Crear usuario admin manualmente
# IMPORTANTE: el guard de auth apunta a App\Models\Usuario (tabla 'usuarios'),
# por eso el admin se crea ahí (antes se creaba en 'users' y no podía loguearse).
echo "👤 Creando usuario administrador..."
php artisan tinker --execute="
\\Illuminate\\Database\\Eloquent\\Model::unguard();
\\App\\Models\\Usuario::updateOrCreate(
    ['email' => '$ADMIN_EMAIL'],
    [
        'username' => '$ADMIN_EMAIL',
        'nombre' => 'admin',
        'apellido' => 'admin',
        'email' => '$ADMIN_EMAIL',
        'password' => bcrypt('$ADMIN_PASSWORD'),
        'rol' => 'Administrador',
        'email_verified_at' => now()
    ]
);
\\Illuminate\\Database\\Eloquent\\Model::reguard();
"

echo "✅ Usuario admin creado: $ADMIN_EMAIL / $ADMIN_PASSWORD"

# Correr el servidor
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}