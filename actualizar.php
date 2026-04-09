<?php include("conexion.php"); ?>

<form method="POST">
    ID: <input type="number" name="id"><br>
    Nuevo nombre: <input type="text" name="nombre"><br>
    Nueva edad: <input type="number" name="edad"><br>
    <input type="submit" value="Actualizar">
</form>

<?php
if ($_POST) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $edad = $_POST['edad'];

    $query = "UPDATE usuarios SET nombre='$nombre', edad='$edad' WHERE id='$id'";
    pg_query($conexion, $query);

    echo "Actualizado correctamente";
}
?>