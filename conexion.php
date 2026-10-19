<?php
/**
 * Conexión a PostgreSQL.
 * Los datos de acceso se leen de config.php para no dejarlos escritos en el código.
 */

$archivoConfig = __DIR__ . '/config.php';

if (!file_exists($archivoConfig)) {
    http_response_code(500);
    exit('Falta el archivo config.php. Copia config.example.php, renómbralo como config.php y escribe tus datos de PostgreSQL.');
}

$config = require $archivoConfig;

// Cada valor va entre comillas simples para que funcione aunque tenga espacios.
$partes = [];
foreach (['host', 'port', 'dbname', 'user', 'password'] as $clave) {
    $valor = isset($config[$clave]) ? (string) $config[$clave] : '';
    $partes[] = $clave . "='" . addcslashes($valor, "'\\") . "'";
}

$conexion = @pg_connect(implode(' ', $partes));

if (!$conexion) {
    http_response_code(500);
    exit('No se pudo conectar a PostgreSQL. Revisa que el servicio esté encendido y que los datos de config.php sean correctos.');
}

pg_set_client_encoding($conexion, 'UTF8');

/**
 * Crea la tabla si no existe y agrega las columnas nuevas (correo y fecha de registro)
 * a instalaciones anteriores. Se revisa una sola vez por sesión.
 */
if (empty($_SESSION['esquema_listo'])) {
    pg_query($conexion, "
        CREATE TABLE IF NOT EXISTS usuarios (
            id     SERIAL PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            edad   INT
        )
    ");
    pg_query($conexion, "
        ALTER TABLE usuarios
            ADD COLUMN IF NOT EXISTS correo    VARCHAR(150),
            ADD COLUMN IF NOT EXISTS creado_en TIMESTAMP NOT NULL DEFAULT NOW()
    ");
    $_SESSION['esquema_listo'] = true;
}
