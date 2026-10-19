<?php
require 'funciones.php';

$valores = ['nombre' => '', 'correo' => '', 'edad' => ''];
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf();
    list($valores, $errores) = validarUsuario($conexion, $_POST);

    if (!$errores) {
        // Consulta preparada: los datos viajan aparte del SQL (evita inyección SQL)
        $guardado = pg_query_params(
            $conexion,
            'INSERT INTO usuarios (nombre, correo, edad) VALUES ($1, $2, $3)',
            [$valores['nombre'], $valores['correo'] !== '' ? $valores['correo'] : null, (int) $valores['edad']]
        );

        if ($guardado) {
            flash('exito', $valores['nombre'] . ' se agregó al registro.');
            redirigir('index.php');
        }
        $errores['general'] = 'No se pudo guardar el usuario. Revisa la conexión con la base de datos e intenta de nuevo.';
    }
}

$titulo = 'Agregar usuario';
$textoBoton = 'Agregar usuario';
require 'includes/header.php';
?>

<a href="index.php" class="volver">Volver a la lista</a>

<section class="panel">
    <h1>Agregar usuario</h1>
    <?php if (isset($errores['general'])): ?>
        <div class="aviso aviso-error" role="alert"><?= e($errores['general']) ?></div>
    <?php endif; ?>
    <?php require 'includes/formulario.php'; ?>
</section>

<?php require 'includes/footer.php'; ?>
