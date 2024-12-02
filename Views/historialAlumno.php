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

// Verificar si se ha enviado el formulario de búsqueda
$historial = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['usuario'])) {
    $usuario = $_POST['usuario']; // Obtener el nombre de usuario desde el formulario
    // Obtener el ID del usuario desde el controlador
    $usuarioId = $controlador->obtenerUsuarioIdPorNombre($usuario);
    
    // Si el usuario fue encontrado, buscar su historial
    if ($usuarioId) {
        $historial = $controlador->mostrarHistorialAlumno($usuarioId);
    } else {
        echo "<script>alert('Usuario no encontrado');</script>";
    }
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
            <h3 class="text-center mt-4">Historial del alumno</h3>

            <!-- Formulario -->
            <div class="card p-4 mt-4">
                <h5>Buscar historial por nombre de usuario</h5>
                <form id="formHistorial" method="POST" class="mt-3">
                    <div class="form-group">
                        <label for="usuario">Usuario</label>
                        <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Ingresa el nombre del usuario" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Consultar</button>
                    <button type="button" class="btn btn-danger" onclick="window.location = 'historialAlumno.php' ;">Borrar consulta</button>
                </form>
            </div>

            <!-- Resultados -->
            <div class="mt-4 text-center">
                <h5>Resultados obtenidos</h5>
                <?php if (!empty($historial)): ?>
                    <div class="table-responsive"> <!-- Contenedor responsivo para la tabla -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    <th>Apellido</th>
                                    <th>Título del Material</th>
                                    <th>Categoría</th>
                                    <th>Fecha Subida</th>
                                    <th>Estado</th>
                                    <th>Calificación</th>
                                    <th>Comentarios de Calificación</th>
                                    <th>Comentarios de Aprobación</th>
                                    <th>Fecha de revisión</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial as $registro): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($registro['Alumno']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Titulo_Material']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Fecha_Subida']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Estado_Material']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Calificacion']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Comentarios_Calificacion']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Comentarios_Aprobacion']); ?></td>
                                        <td><?php echo htmlspecialchars($registro['Fecha_Aprobacion']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div> <!-- Fin del contenedor responsivo -->
                <?php else: ?>
                    <p>No se encontraron registros para el alumno.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>