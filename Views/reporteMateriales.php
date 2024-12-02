<?php
    session_start();
    require_once __DIR__ . '/../Controller/controlador.php';
    require_once "../Model/conexionBD.php";
    $conex = obtenerConexion();

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
                <div class="report-header">Materiales aprobados y rechazados</div>
                
                <!-- Botón para imprimir -->
                <p><a href="../ReportesPDF/ReporteMateriales.php"><img src="../Views/img/pdf.png"></a>Imprimir</p>
                
                <!-- Tabla de materiales aprobados y rechazados -->
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Fecha Subida</th>
                            <th>Estado</th>
                            <th>Autor</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $query = "SELECT m.titulo, m.descripcion, m.fechaSubida, m.estado, u.usuario AS usuario FROM material m
                            JOIN usuario u ON m.usuario_idU_M = u.idU WHERE m.estado IN ('aprobado', 'rechazado');";
                        $result = mysqli_query($conex, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['titulo'] . "</td>";
                            echo "<td>" . $row['descripcion'] . "</td>";
                            echo "<td>" . $row['fechaSubida'] . "</td>";
                            echo "<td>" . ucfirst($row['estado']) . "</td>";  // Muestra 'Aprobado' o 'Rechazado'
                            echo "<td>" . $row['usuario'] . "</td>";  // Solo muestra el nombre del usuario
                            echo "</tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>