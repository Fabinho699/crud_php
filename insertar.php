<?php include("conexion.php"); ?>

<form method="POST">
    Nombre: <input type="text" name="nombre"><br>
    Edad: <input type="number" name="edad"><br>
    <input type="submit" value="Guardar">
</form>

<?php
if ($_POST) {
    $nombre = $_POST['nombre'];
    $edad = $_POST['edad'];

    $query = "INSERT INTO usuarios(nombre, edad) VALUES ('$nombre', '$edad')";
    pg_query($conexion, $query);

    echo "Datos guardados";
}
?>