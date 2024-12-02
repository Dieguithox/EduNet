<?php
session_start();

require_once __DIR__ . '/../Controller/controlador.php';
include 'header.php';

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'docente') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador(); // Instanciando el controlador

// Verificar si se ha enviado una solicitud para cerrar sesión
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $controlador->cerrarSesion(); // Llama a la función para cerrar la sesión
}

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
?>

<!-- Contenido principal -->
<div class="container-fluid">
    <div class="row">
        <!-- Barra lateral -->
        <div class="col-12 col-md-2 bg-light sidebar custom-sidebar">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="aprobarMaterial.php" class="btn btn-link nav-link">Material</a>
                </li>
                <li class="nav-item">
                    <a href="historialAlumno.php" class="btn btn-link nav-link">Historial Alumno</a>
                </li>
            </ul>
        </div>

        <!-- Área de contenido -->
            <div class="col-12 col-md-10">
            <!-- Carrusel -->
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="img/panel3.png" alt="Primer slide">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="img/panel1.png" alt="Segundo slide">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="img/panel2.jpeg" alt="Tercer slide">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Anterior</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Siguiente</span>
                    </a>
                </div>
                <!-- Mensaje de bienvenida -->
                <div class="alert alert-success mt-4" role="alert">
                    <h4 class="alert-heading">¡Bienvenido, Docente!</h4>
                    <p>Nos alegra tenerte en esta plataforma. Aquí podrás gestionar materiales, consultar el historial de alumnos y realizar diversas tareas académicas de manera eficiente.</p>
                    <hr>
                    <p class="mb-0">Recuerda que puedes utilizar la barra lateral para acceder a las diferentes secciones disponibles.</p>
                </div>
            </div>
    </div>
</div>
<?php include 'footer.php'; ?>