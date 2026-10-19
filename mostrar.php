<?php
require 'funciones.php';

$id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
$usuario = $id > 0 ? buscarUsuario($conexion, $id) : null;

if (!$usuario) {
    flash('error', 'Ese usuario no existe o ya fue eliminado.');
    redirigir('index.php');
}

$titulo = $usuario['nombre'];
require 'includes/header.php';
?>

<a href="index.php" class="volver">Volver a la lista</a>

<section class="panel ficha">
    <div class="panel-persona">
        <span class="avatar avatar-grande tono-<?= tonoAvatar($usuario['nombre']) ?>" aria-hidden="true"><?= e(iniciales($usuario['nombre'])) ?></span>
        <div>
            <h1><?= e($usuario['nombre']) ?></h1>
            <p class="subtitulo">ID <?= (int) $usuario['id'] ?></p>
        </div>
    </div>

    <dl class="datos">
        <div>
            <dt>Correo</dt>
            <dd>
                <?php if ($usuario['correo']): ?>
                    <a href="mailto:<?= e($usuario['correo']) ?>"><?= e($usuario['correo']) ?></a>
                <?php else: ?>
                    <span class="sin-dato">Sin correo</span>
                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt>Edad</dt>
            <dd><?= $usuario['edad'] !== null ? (int) $usuario['edad'] . ' años' : '<span class="sin-dato">Sin edad</span>' ?></dd>
        </div>
        <div>
            <dt>Registrado el</dt>
            <dd><?= e(fechaCorta($usuario['creado_en'])) ?></dd>
        </div>
    </dl>

    <div class="acciones-formulario">
        <a href="actualizar.php?id=<?= (int) $usuario['id'] ?>" class="boton">Editar usuario</a>
        <form method="post" action="eliminar.php">
            <?= csrfCampo() ?>
            <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
            <button type="submit" class="boton boton-peligro-suave" data-eliminar data-nombre="<?= e($usuario['nombre']) ?>">Eliminar usuario</button>
        </form>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
