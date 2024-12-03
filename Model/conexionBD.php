<?php
/* Funcion donde obtenemos los datos de la conexion a la base de datos */
function obtenerConexion() {
    $conexion = mysqli_connect("localhost", "root", "", "eduNet");
    if (!$conexion) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }
    return $conexion;
}
?>