<?php
require 'funciones.php';

// Búsqueda y página actual (vienen por la URL: index.php?q=ana&p=2)
$busqueda = trim(isset($_GET['q']) ? (string) $_GET['q'] : '');
$pagina   = max(1, (int) (isset($_GET['p']) ? $_GET['p'] : 1));

$condicion  = '';
$parametros = [];
if ($busqueda !== '') {
    // Se escapan % y _ para que se busquen como texto normal
    $patron = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $busqueda) . '%';
    $condicion  = 'WHERE nombre ILIKE $1 OR correo ILIKE $1';
    $parametros = [$patron];
}

// Totales para el encabezado y la paginación
$resumen = pg_fetch_assoc(pg_query($conexion, 'SELECT COUNT(*) AS total, ROUND(AVG(edad)) AS promedio FROM usuarios'));
$totalRegistrados = (int) $resumen['total'];

$conteo = pg_query_params($conexion, "SELECT COUNT(*) FROM usuarios $condicion", $parametros);
$totalEncontrados = (int) pg_fetch_result($conteo, 0, 0);
$totalPaginas = max(1, (int) ceil($totalEncontrados / POR_PAGINA));
$pagina = min($pagina, $totalPaginas);
$desde = ($pagina - 1) * POR_PAGINA;

$consulta = pg_query_params(
    $conexion,
    "SELECT id, nombre, correo, edad, creado_en FROM usuarios $condicion ORDER BY id DESC LIMIT " . POR_PAGINA . " OFFSET $desde",
    $parametros
);
$usuarios = $consulta ? pg_fetch_all($consulta) : [];
$usuarios = $usuarios ?: [];

function enlacePagina($numero, $busqueda)
{
    $query = ['p' => $numero];
    if ($busqueda !== '') {
        $query['q'] = $busqueda;
    }
    return 'index.php?' . http_build_query($query);
}

$titulo = 'Usuarios';
require 'includes/header.php';
?>

<section class="encabezado">
    <div>
        <h1>Usuarios</h1>
        <p class="subtitulo">
            <?php if ($totalRegistrados === 0): ?>
                Todavía no hay nadie registrado.
            <?php else: ?>
                <?= $totalRegistrados ?> <?= $totalRegistrados === 1 ? 'persona registrada' : 'personas registradas' ?>,
                con una edad promedio de <?= (int) $resumen['promedio'] ?> años.
            <?php endif; ?>
        </p>
    </div>
    <a href="insertar.php" class="boton">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        Agregar usuario
    </a>
</section>

<?php if ($totalRegistrados > 0): ?>
<form class="buscador" method="get" role="search">
    <label for="q" class="solo-lectores">Buscar usuarios</label>
    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/></svg>
    <input type="search" id="q" name="q" placeholder="Buscar por nombre o correo" value="<?= e($busqueda) ?>">
    <?php if ($busqueda !== ''): ?>
        <a href="index.php" class="limpiar">Quitar búsqueda</a>
    <?php endif; ?>
</form>
<?php endif; ?>

<?php if ($totalRegistrados === 0): ?>
    <div class="vacio">
        <h2>Empieza tu registro</h2>
        <p>Agrega el primer usuario con su nombre, correo y edad.</p>
        <a href="insertar.php" class="boton">Agregar usuario</a>
    </div>

<?php elseif (!$usuarios): ?>
    <div class="vacio">
        <h2>Ningún usuario coincide con «<?= e($busqueda) ?>»</h2>
        <p>Revisa cómo está escrito o busca por otra parte del nombre o del correo.</p>
        <a href="index.php" class="boton boton-secundario">Ver todos los usuarios</a>
    </div>

<?php else: ?>
    <?php if ($busqueda !== ''): ?>
        <p class="resultado-busqueda">
            <?= $totalEncontrados ?> <?= $totalEncontrados === 1 ? 'resultado' : 'resultados' ?> para «<?= e($busqueda) ?>»
        </p>
    <?php endif; ?>

    <div class="tabla-marco">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Usuario</th>
                    <th scope="col">Correo</th>
                    <th scope="col" class="num">Edad</th>
                    <th scope="col">Registrado</th>
                    <th scope="col"><span class="solo-lectores">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td class="celda-usuario">
                        <a href="mostrar.php?id=<?= (int) $u['id'] ?>" class="persona">
                            <span class="avatar tono-<?= tonoAvatar($u['nombre']) ?>" aria-hidden="true"><?= e(iniciales($u['nombre'])) ?></span>
                            <span>
                                <span class="persona-nombre"><?= e($u['nombre']) ?></span>
                                <span class="persona-id">ID <?= (int) $u['id'] ?></span>
                            </span>
                        </a>
                    </td>
                    <td data-etiqueta="Correo">
                        <?php if ($u['correo']): ?>
                            <?= e($u['correo']) ?>
                        <?php else: ?>
                            <span class="sin-dato">Sin correo</span>
                        <?php endif; ?>
                    </td>
                    <td data-etiqueta="Edad" class="num"><?= $u['edad'] !== null ? (int) $u['edad'] . ' años' : '<span class="sin-dato">Sin edad</span>' ?></td>
                    <td data-etiqueta="Registrado"><?= e(fechaCorta($u['creado_en'])) ?></td>
                    <td class="celda-acciones">
                      <div class="acciones-fila">
                        <a href="actualizar.php?id=<?= (int) $u['id'] ?>" class="accion">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16v4Z"/><path d="m13.5 6.5 4 4"/></svg>
                            Editar<span class="solo-lectores"> a <?= e($u['nombre']) ?></span>
                        </a>
                        <form method="post" action="eliminar.php" class="form-eliminar">
                            <?= csrfCampo() ?>
                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                            <button type="submit" class="accion accion-peligro" data-eliminar data-nombre="<?= e($u['nombre']) ?>">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 7h14M10 7V5h4v2M7 7l1 12h8l1-12"/></svg>
                                Eliminar<span class="solo-lectores"> a <?= e($u['nombre']) ?></span>
                            </button>
                        </form>
                      </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPaginas > 1): ?>
        <nav class="paginacion" aria-label="Páginas">
            <?php if ($pagina > 1): ?>
                <a href="<?= e(enlacePagina($pagina - 1, $busqueda)) ?>">Anterior</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="<?= e(enlacePagina($i, $busqueda)) ?>" <?= $i === $pagina ? 'aria-current="page"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($pagina < $totalPaginas): ?>
                <a href="<?= e(enlacePagina($pagina + 1, $busqueda)) ?>">Siguiente</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
