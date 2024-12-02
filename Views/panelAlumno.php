<?php
session_start();
require_once __DIR__ . '/../Controller/controlador.php';
include 'header.php';

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'alumno') {
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

// Llamada al controlador para obtener los materiales aprobados
$materialesAprobados = $controlador->obtenerMaterialesAprobados();
?>

<!-- Contenedor principal -->
<div class="container-fluid">
    <div class="row">
        <!-- Barra lateral -->
        <div class="col-12 col-md-2 bg-light sidebar custom-sidebar">
            <div class="button-container d-flex justify-content-center mb-3">
                <a href="subirMaterial.php" class="btn btn-primary btn_custom">
                    <img src="img/subir.png" alt="Subir" class="icon-img">Subir material
                </a>
            </div>
            <ul class="nav flex-column">
                <!--<li class="nav-item">
                    <a href="panelAlumno.php" class="btn btn-link nav-link">Página Principal</a>
                </li>-->
                <li class="nav-item">
                    <a href="gestionMaterial.php" class="btn btn-link nav-link">Mi material</a>
                </li>
                <li class="nav-item">
                    <a href="generarReporte.php" class="btn btn-link nav-link">Generar reporte</a>
                </li>
            </ul>
        </div>

        <!-- Área de contenido con carrusel y materiales -->
        <div class="col-12 col-md-10">
            <!-- Carrusel -->
            <div id="carouselExampleIndicators" class="carousel slide mb-4" data-ride="carousel">
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

            <!-- Área para mostrar los materiales subidos -->
            <div class="container mt-5">
                <h2 class="text-center mb-4">Materiales Aprobados</h2>
                <!-- Tabla de materiales -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Autor</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Calificación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($materialesAprobados)): ?>
                                <?php foreach ($materialesAprobados as $material): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($material['autor']); ?></td>
                                        <td><?php echo htmlspecialchars($material['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($material['categoria']); ?></td>
                                        <td>
                                            <span class="text-warning">
                                                <?php 
                                                // Obtener el promedio de calificación para el material actual
                                                $promedioCalificacion = $controlador->obtenerCalificacionPromedio($material['idM']);
                                                
                                                // Mostrar estrellas según el promedio
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $promedioCalificacion) {
                                                        echo '&#9733;'; // Estrella llena
                                                    } else {
                                                        echo '&#9734;'; // Estrella vacía
                                                    }
                                                }
                                                ?>
                                            </span>
                                            <br>
                                            <small>Promedio: <?php echo $promedioCalificacion; ?></small>
                                        </td>
                                        <td>
                                            <a href="calificarMaterial.php?idAM=<?php echo urlencode($material['idM']); ?>" class="btn btn-primary btn-sm">
                                                Calificar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay materiales aprobados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>