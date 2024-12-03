<?php
session_start();

require_once __DIR__ . '/../Controller/controlador.php';
include 'header.php';

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador();

/* Verifica si el usuario está autenticado */
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión!!");</script>';
    session_destroy();
    header("Location: login.php");
    exit();
}

/* Obtener la lista de programas educativos */
$programas = $controlador->obtenerProgramasEducativos2();

/* Verificar si se solicita eliminar un programa */
if (isset($_GET['action']) && $_GET['action'] == 'eliminar' && isset($_GET['id'])) {
    $idPE = $_GET['id'];
    $controlador->eliminarPrograma($idPE);
    header("Location: gestionPE.php");
    exit();
}

/* Verificar si se envió el formulario de registrar programa educativo */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'registrar') {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $clave = $_POST['clave'];

        $controlador->registrarPrograma($nombre, $descripcion, $clave);
    } elseif (isset($_POST['action']) && $_POST['action'] == 'actualizar') {
        // Actualizar programa
        $idPE = $_POST['idPE'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $clave = $_POST['clave'];

        $controlador->actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave);
    }
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
            <h1>Lista de Programas Educativos</h1>

            <!-- Tabla de programas educativos -->
            <div class="table-responsive"> <!-- Contenedor responsivo -->
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Clave</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($programas)): ?>
                            <?php foreach ($programas as $programa): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($programa['idPE']); ?></td>
                                    <td><?php echo htmlspecialchars($programa['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($programa['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($programa['clave']); ?></td>
                                    <td>
                                        <a href="editarPE.php?id=<?php echo htmlspecialchars($programa['idPE']); ?>" class="btn btn-primary">Editar</a>
                                        <a href="gestionPE.php?action=eliminar&id=<?php echo htmlspecialchars($programa['idPE']); ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este programa?');" class="btn btn-danger">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No hay programas educativos disponibles.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Botón para agregar un nuevo programa educativo -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#crearProgramaModal">Crear nuevo programa</button>
        </div>
    </div>
</div>

<!-- Modal de crear programa -->
<div class="modal fade" id="crearProgramaModal" tabindex="-1" aria-labelledby="crearProgramaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearProgramaModalLabel">Registrar nuevo programa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="gestionPE.php" method="POST">
                    <input type="hidden" name="action" value="registrar"> <!-- Campo oculto para registrar -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label">Clave</label>
                        <input type="text" class="form-control" id="clave" name="clave" required>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>