<?php
/** @var string $titulo Título de la página (lo define cada archivo antes de incluir este). */
$flash = tomarFlash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | Registro de usuarios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- ?v= cambia cada vez que se edita el archivo, así el navegador no usa una copia vieja -->
    <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/../style.css') ?>">
</head>
<body>

<header class="barra">
    <a class="marca" href="index.php">
        <svg class="marca-icono" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="9" cy="8" r="3.5"/>
            <path d="M2.5 19.5c.8-3.6 3.4-5.5 6.5-5.5s5.7 1.9 6.5 5.5"/>
            <path d="M16 4.8a3.4 3.4 0 0 1 0 6.4M18.2 14.4c1.7.8 2.9 2.5 3.3 5.1"/>
        </svg>
        Registro de usuarios
    </a>
</header>

<main class="contenedor">
<?php if ($flash): ?>
    <div class="aviso aviso-<?= e($flash['tipo']) ?>" role="status">
        <?= e($flash['mensaje']) ?>
    </div>
<?php endif; ?>
