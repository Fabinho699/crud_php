</main>

<!-- Ventana para confirmar antes de eliminar -->
<dialog id="dialogo-eliminar" class="dialogo" aria-labelledby="dialogo-titulo">
    <form method="post" action="eliminar.php">
        <?= csrfCampo() ?>
        <input type="hidden" name="id" id="dialogo-id">
        <h2 id="dialogo-titulo">¿Eliminar a <span id="dialogo-nombre"></span>?</h2>
        <p>Se borrará su registro de forma permanente.</p>
        <div class="acciones-dialogo">
            <button type="button" class="boton boton-secundario" data-cerrar-dialogo>Cancelar</button>
            <button type="submit" class="boton boton-peligro">Eliminar usuario</button>
        </div>
    </form>
</dialog>

<script src="app.js?v=<?= filemtime(__DIR__ . '/../app.js') ?>"></script>
</body>
</html>
