<?php
// Suprimir deprecations ANTES de que Laravel arranque.
//
// PHP 8.5 (la version de vercel-php@0.9.0) deprecó constantes que Laravel 12
// todavía usa en config/database.php (PDO::MYSQL_ATTR_SSL_CA, etc.). Esos
// warnings se disparan dentro del bootstrapper LoadConfiguration, ANTES de
// que HandleExceptions desactive display_errors. El output que generan
// rompe los Set-Cookie posteriores: sin cookie de sesión no hay token CSRF,
// y el login termina en 419 PAGE EXPIRED.
//
// Apagando display_errors y filtrando E_DEPRECATED acá, antes de cargar
// Laravel, el problema desaparece. Cuando Laravel actualice esas constantes
// se puede sacar este bloque.
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Path to the Laravel application
require __DIR__ . '/../public/index.php';