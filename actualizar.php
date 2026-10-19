<?php
require 'funciones.php';

$id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
$usuario = $id > 0 ? buscarUsuario($conexion, $id) : null;

if (!$usuario) {
    flash('error', 'Ese usuario no existe o ya fue eliminado.');
    redirigir('index.php');
}

$valores = [
    'nombre' => $usuario['nombre'],
    'correo' => (string) $usuario['correo'],
    'edad'   => (string) $usuario['edad'],
];
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf();
    list($valores, $errores) = validarUsuario($conexion, $_POST, $id);

    if (!$errores) {
        $guardado = pg_query_params(
            $conexion,
            'UPDATE usuarios SET nombre = $1, correo = $2, edad = $3 WHERE id = $4',
            [$valores['nombre'], $valores['correo'] !== '' ? $valores['correo'] : null, (int) $valores['edad'], $id]
        );

        if ($guardado) {
            flash('exito', 'Se guardaron los cambios de ' . $valores['nombre'] . '.');
            redirigir('index.php');
        }
        $errores['general'] = 'No se pudieron guardar los cambios. Revisa la conexión con la base de datos e intenta de nuevo.';
    }
}

$titulo = 'Editar a ' . $usuario['nombre'];
$textoBoton = 'Guardar cambios';
require 'includes/header.php';
?>

<a href="index.php" class="volver">Volver a la lista</a>

<section class="panel">
    <div class="panel-persona">
        <span class="avatar avatar-grande tono-<?= tonoAvatar($usuario['nombre']) ?>" aria-hidden="true"><?= e(iniciales($usuario['nombre'])) ?></span>
        <div>
            <h1>Editar usuario</h1>
            <p class="subtitulo"><?= e($usuario['nombre']) ?>, ID <?= (int) $usuario['id'] ?></p>
        </div>
    </div>
    <?php if (isset($errores['general'])): ?>
        <div class="aviso aviso-error" role="alert"><?= e($errores['general']) ?></div>
    <?php endif; ?>
    <?php require 'includes/formulario.php'; ?>
</section>

<?php require 'includes/footer.php'; ?>
