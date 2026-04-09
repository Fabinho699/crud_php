<?php include("conexion.php"); ?>

<form method="POST">
    ID a eliminar: <input type="number" name="id"><br>
    <input type="submit" value="Eliminar">
</form>

<?php
if ($_POST) {
    $id = $_POST['id'];

    $query = "DELETE FROM usuarios WHERE id='$id'";
    pg_query($conexion, $query);

    echo "Eliminado correctamente";
}
?>