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
                <div class="report-header">Materiales por categoría</div>
                
                <!-- Botón para imprimir -->
                <p><a href="../ReportesPDF/Categorias.php"><img src="../Views/img/pdf.png"></a>Imprimir</p>
                
                <!-- Tabla de materiales aprobados y rechazados -->
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Total de Materiales</th>
                            <th>Calificación Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $query = "SELECT m.categoria, COUNT(DISTINCT m.idM) AS total_materiales, AVG(c.calificacion) AS promedio_calificacion FROM material m 
                        LEFT JOIN Calificacion c ON m.idM = c.materialId GROUP BY m.categoria ORDER BY total_materiales DESC;";
                    
                        $result = mysqli_query($conex, $query);
            
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['categoria']) . "</td>";
                                echo "<td>" . (int)$row['total_materiales'] . "</td>";
                                echo "<td>" . number_format($row['promedio_calificacion'], 2) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No se encontraron resultados</td></tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>