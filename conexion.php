<?php
$conexion = pg_connect("host=localhost dbname=actividad user=postgres password=1206");

if (!$conexion) {
    die("Error de conexión: " . pg_last_error());
}
?>