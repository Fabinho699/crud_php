<?php
/**
 * Formulario compartido por insertar.php y actualizar.php.
 * Variables esperadas: $valores, $errores, $textoBoton
 */
function claseCampo($errores, $campo)
{
    return isset($errores[$campo]) ? 'campo con-error' : 'campo';
}
?>
<form method="post" class="formulario" novalidate>
    <?= csrfCampo() ?>

    <div class="<?= claseCampo($errores, 'nombre') ?>">
        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" required autofocus
               autocomplete="name" value="<?= e($valores['nombre']) ?>"
               <?= isset($errores['nombre']) ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>>
        <?php if (isset($errores['nombre'])): ?>
            <p class="error" id="error-nombre"><?= e($errores['nombre']) ?></p>
        <?php endif; ?>
    </div>

    <div class="<?= claseCampo($errores, 'correo') ?>">
        <label for="correo">Correo <span class="opcional">(opcional)</span></label>
        <input type="email" id="correo" name="correo" maxlength="150"
               autocomplete="email" value="<?= e($valores['correo']) ?>"
               <?= isset($errores['correo']) ? 'aria-invalid="true" aria-describedby="error-correo"' : '' ?>>
        <?php if (isset($errores['correo'])): ?>
            <p class="error" id="error-correo"><?= e($errores['correo']) ?></p>
        <?php endif; ?>
    </div>

    <div class="<?= claseCampo($errores, 'edad') ?> campo-corto">
        <label for="edad">Edad</label>
        <input type="number" id="edad" name="edad" min="1" max="120" required inputmode="numeric"
               value="<?= e($valores['edad']) ?>"
               <?= isset($errores['edad']) ? 'aria-invalid="true" aria-describedby="error-edad"' : '' ?>>
        <?php if (isset($errores['edad'])): ?>
            <p class="error" id="error-edad"><?= e($errores['edad']) ?></p>
        <?php endif; ?>
    </div>

    <div class="acciones-formulario">
        <button type="submit" class="boton"><?= e($textoBoton) ?></button>
        <a href="index.php" class="boton boton-secundario">Cancelar</a>
    </div>
</form>
