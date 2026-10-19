<?php
require 'funciones.php';

// Solo se elimina por POST (con token CSRF), nunca abriendo un enlace.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('index.php');
}

verificarCsrf();

$id = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
$usuario = $id > 0 ? buscarUsuario($conexion, $id) : null;

if (!$usuario) {
    flash('error', 'Ese usuario no existe o ya fue eliminado.');
    redirigir('index.php');
}

$borrado = pg_query_params($conexion, 'DELETE FROM usuarios WHERE id = $1', [$id]);

if ($borrado) {
    flash('exito', $usuario['nombre'] . ' se eliminó del registro.');
} else {
    flash('error', 'No se pudo eliminar a ' . $usuario['nombre'] . '. Intenta de nuevo.');
}

redirigir('index.php');
