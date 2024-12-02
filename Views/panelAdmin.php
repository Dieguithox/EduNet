<?php
session_start();

require_once __DIR__ . '/../Controller/controlador.php'; // Ruta correcta para el controlador
include 'header.php';

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador(); // Instanciando el controlador

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo '
        <script>
            alert("Debes iniciar sesión!!");
        </script>
    ';
    session_destroy();
    header("Location: login.php");
    exit();
}

// Verificar si se ha enviado una solicitud para cerrar sesión
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $controlador->cerrarSesion(); // Llama a la función para cerrar la sesión
}

?>
<!-- Contenido principal -->
<div class="container-fluid">
    <div class="row">
        <!-- Barra lateral -->
        <div class="col-12 col-md-2 bg-light sidebar custom-sidebar">
            <h4 class="sidebar_title">Ajustes</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="gestionPE.php" class="nav-link">Programa educativo</a></li>
                <li class="nav-item"><a href="gestionA.php" class="nav-link">Gestionar avisos</a></li>
                <li class="nav-item"><a href="gestionUsers.php" class="nav-link">Usuarios</a></li>
            </ul>
            <h4 class="sidebar_title">Respaldo y restauración de BD</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="respaldoDB.php" class="nav-link">Respaldar</a></li>
                <li class="nav-item"><a href="respaldoDB.php" class="nav-link">Restaurar</a></li>
            </ul>
        </div>

        <!-- Área de contenido -->
            <div class="col-12 col-md-10">
                <!-- Mensaje de bienvenida -->
                <div class="alert alert-primary mt-4" role="alert">
                    <h4 class="alert-heading">¡Bienvenido, Administrador!</h4>
                    <p>Estamos encantados de tenerte de vuelta. Aquí podrás gestionar programas educativos, usuarios, avisos, 
                        y realizar respaldos y restauraciones de la base de datos.</p>
                    <hr>
                    <p class="mb-0">Recuerda cerrar sesión al finalizar tu trabajo para mantener la seguridad de la plataforma.</p>
                </div>

            </div>
    </div>
</div>
<?php include 'footer.php'; ?>