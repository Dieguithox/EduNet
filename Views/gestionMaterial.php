<?php
session_start();
require_once __DIR__ . '/../Controller/controlador.php';
include 'header.php'; 

/* Manejo de sesión y autenticación */
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'alumno') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador();

/* Redirección a inicio de sesión si no está autenticado */
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión!!");</script>';
    session_destroy();
    header("Location: login.php");
    exit();
}

/* Manejo de eliminación de material */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminarMaterial'])) {
    if (isset($_POST['idMaterial']) && !empty($_POST['idMaterial'])) {
        $idMaterial = $_POST['idMaterial'];
        $controlador->eliminarMaterial($idMaterial);
    } else {
        echo "<script>alert('ID de material no recibido');</script>";
    }
}

$usuarioId = $_SESSION['idU']; // Obtener el ID del usuario desde la sesión
$materiales = $controlador->listarMateriales($usuarioId); // Pasar el ID de usuario a la función

?>

<!-- Interfaz de usuario -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
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
            <h1>Lista de Materiales</h1>
            <div class="table-responsive"> <!-- Contenedor responsivo para la tabla -->
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Categoría</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Comentarios</th>
                            <th>URL</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($materiales)): ?>
                            <?php foreach ($materiales as $material): ?>
                                <?php $comentarios = $controlador->obtenerComentarios($material['idM']); ?>
                                <tr>
                                    <td><?= htmlspecialchars($material['idM']); ?></td>
                                    <td><?= htmlspecialchars($material['categoria']); ?></td>
                                    <td><?= htmlspecialchars($material['titulo']); ?></td>
                                    <td><?= htmlspecialchars($material['descripcion']); ?></td>
                                    <td><?= htmlspecialchars($material['estado']); ?></td>
                                    <td><small><?= $comentarios ?: 'Sin comentarios'; ?></small></td>
                                    <td><a href="<?= htmlspecialchars($material['URL']); ?>" target="_blank">Ver</a></td>
                                    <td>
                                        <a href="editarMaterial.php?id=<?= $material['idM']; ?>" class="btn btn-primary">Editar</a>
                                        <form action="gestionMaterial.php" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este material?');" style="display:inline;">
                                            <input type="hidden" name="idMaterial" value="<?= $material['idM'] ?>">
                                            <button type="submit" name="eliminarMaterial" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No hay materiales disponibles.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div> <!-- Fin del contenedor responsivo -->
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>