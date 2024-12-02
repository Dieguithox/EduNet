<?php
function obtenerConexion() {
    $conexion = mysqli_connect("localhost", "root", "", "eduNet");
    if (!$conexion) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }
    return $conexion;
}
?>

<?php
    /*$conexion = mysqli_connect("localhost","root","","barbershop");
        //VERIFICACION DE CONEXION A LA BD
    if($conexion){
        echo 'Conectado exitosamente a la BD';
    }else{
        echo 'Error al conectar a la BD';
    }*/
?>