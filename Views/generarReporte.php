<?php
    session_start();
    require_once __DIR__ . '/../Controller/controlador.php';

    if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'alumno') {
        header("Location: login.php");
        exit();
    }
    
    $controlador = new Controlador();
    
    /* Verificar si se ha enviado una solicitud para cerrar sesión */
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        $controlador->cerrarSesion();
    }
    
    /* Verifica si el usuario está autenticado */
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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Reportes</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include 'header.php'; ?>

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
                <li class="nav-item">
                    <a href="gestionMaterial.php" class="btn btn-link nav-link">Mi material</a>
                </li>
                <li class="nav-item">
                    <a href="generarReporte.php" class="btn btn-link nav-link">Generar reporte</a>
                </li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="col-12 col-md-10">
            <div class="report-container">
                <div class="report-header">Generar reporte</div>

                <!-- Opciones de reportes -->
                <div class="options">
                    <a href="../Views/reporteMateriales.php" class="option-card">
                        <img src="../Views/img/libros.png">
                        <p>Materiales aprobados y rechazados</p>
                    </a>
                    <a href="../Views/reporteTop10.php" class="option-card">
                        <img src="../Views/img/top-10.png" alt="Top 10">
                        <p>10 Mejores</p>
                    </a>
                    <a href="../Views/reporteCategorias.php" class="option-card">
                        <img src="../Views/img/opciones.png" alt="Ascendente">
                        <p>Materiales por categoría</p>
                    </a>
                    <a href="../Views/reporteSemanal.php" class="option-card">
                        <img src="../Views/img/semanal.png" alt="Descendente">
                        <p>Actividad semanal</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php include 'footer.php'; ?>