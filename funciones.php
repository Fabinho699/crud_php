<?php
/**
 * Funciones compartidas por todas las páginas.
 * Cada página empieza con: require 'funciones.php';
 */

session_start();
require __DIR__ . '/conexion.php';

const POR_PAGINA = 8;

/** Escapa texto antes de mostrarlo en HTML (evita ataques XSS). */
function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function redirigir($url)
{
    header('Location: ' . $url);
    exit;
}

/* ---------- Mensajes que se muestran una sola vez ---------- */

function flash($tipo, $mensaje)
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function tomarFlash()
{
    $flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
    unset($_SESSION['flash']);
    return $flash;
}

/* ---------- Protección CSRF para los formularios ---------- */

function csrfToken()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfCampo()
{
    return '<input type="hidden" name="csrf" value="' . e(csrfToken()) . '">';
}

function verificarCsrf()
{
    $enviado = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
    if (!hash_equals(csrfToken(), $enviado)) {
        flash('error', 'La sesión expiró. Intenta de nuevo.');
        redirigir('index.php');
    }
}

/* ---------- Usuarios ---------- */

function buscarUsuario($conexion, $id)
{
    $resultado = pg_query_params($conexion, 'SELECT * FROM usuarios WHERE id = $1', [(int) $id]);
    $fila = $resultado ? pg_fetch_assoc($resultado) : false;
    return $fila ?: null;
}

/**
 * Valida los datos del formulario.
 * Devuelve [datosLimpios, errores]. Si errores está vacío, se puede guardar.
 */
function validarUsuario($conexion, array $entrada, $idActual = 0)
{
    $datos = [
        'nombre' => trim(preg_replace('/\s+/u', ' ', isset($entrada['nombre']) ? (string) $entrada['nombre'] : '')),
        'correo' => mb_strtolower(trim(isset($entrada['correo']) ? (string) $entrada['correo'] : '')),
        'edad'   => trim(isset($entrada['edad']) ? (string) $entrada['edad'] : ''),
    ];
    $errores = [];

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Escribe el nombre.';
    } elseif (mb_strlen($datos['nombre']) < 2 || mb_strlen($datos['nombre']) > 100) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 100 caracteres.';
    } elseif (!preg_match("/^\p{L}[\p{L}\s'.-]*$/u", $datos['nombre'])) {
        $errores['nombre'] = 'El nombre solo puede tener letras, espacios, puntos, guiones y apóstrofos.';
    }

    if ($datos['correo'] !== '') {
        if (mb_strlen($datos['correo']) > 150 || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'Escribe un correo válido, por ejemplo nombre@dominio.com.';
        } else {
            $repetido = pg_query_params(
                $conexion,
                'SELECT 1 FROM usuarios WHERE LOWER(correo) = $1 AND id <> $2',
                [$datos['correo'], (int) $idActual]
            );
            if ($repetido && pg_num_rows($repetido) > 0) {
                $errores['correo'] = 'Ya hay otro usuario con este correo.';
            }
        }
    }

    if ($datos['edad'] === '') {
        $errores['edad'] = 'Escribe la edad.';
    } elseif (!ctype_digit($datos['edad']) || (int) $datos['edad'] < 1 || (int) $datos['edad'] > 120) {
        $errores['edad'] = 'La edad debe ser un número entero entre 1 y 120.';
    }

    return [$datos, $errores];
}

/* ---------- Ayudas visuales ---------- */

/** "Juliana Pérez" -> "JP" */
function iniciales($nombre)
{
    $palabras = preg_split('/\s+/u', trim((string) $nombre), -1, PREG_SPLIT_NO_EMPTY);
    $letras = '';
    foreach (array_slice($palabras, 0, 2) as $palabra) {
        $letras .= mb_strtoupper(mb_substr($palabra, 0, 1));
    }
    return $letras !== '' ? $letras : '?';
}

/** Cada nombre recibe siempre el mismo color de avatar (0 a 5). */
function tonoAvatar($nombre)
{
    return abs(crc32(mb_strtolower((string) $nombre))) % 6;
}

function fechaCorta($marcaDeTiempo)
{
    if (!$marcaDeTiempo) {
        return '';
    }
    $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    $t = strtotime($marcaDeTiempo);
    return date('j', $t) . ' ' . $meses[(int) date('n', $t) - 1] . ' ' . date('Y', $t);
}
