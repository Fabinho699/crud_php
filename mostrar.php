<?php include("conexion.php"); ?>

<?php
$query = "SELECT * FROM usuarios";
$resultado = pg_query($conexion, $query);

while ($fila = pg_fetch_assoc($resultado)) {
    echo "ID: ".$fila['id']." - ".$fila['nombre']." - ".$fila['edad']."<br>";
}
?>