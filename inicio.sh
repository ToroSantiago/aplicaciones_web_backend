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
echo "👤 Creando usuario administrador..."
php artisan tinker --execute="
\\Illuminate\\Database\\Eloquent\\Model::unguard();
\\App\\Models\\User::updateOrCreate(
    ['email' => '$ADMIN_EMAIL'],
    [
        'name' => 'admin',
        'email' => '$ADMIN_EMAIL',
        'password' => bcrypt('$ADMIN_PASSWORD'),
        'email_verified_at' => now()
    ]
);
\\Illuminate\\Database\\Eloquent\\Model::reguard();
"

echo "✅ Usuario admin creado: $ADMIN_EMAIL / $ADMIN_PASSWORD"

# Correr el servidor
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}