<?php 
session_start();

require_once __DIR__ . '/../Controller/controlador.php'; 
include 'header.php';

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador(); 

/* Verifica si el usuario está autentificado */
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión!!");</script>';
    session_destroy();
    header("Location: login.php");
    exit();
}

/* Verificar si se ha enviado una solicitud para cerrar sesión */
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $controlador->cerrarSesion();
}

/* Obtener la lista de avisos */
$avisos = $controlador->obtenerAvisos();

/* Verificar si el formulario de agregar aviso fue enviado */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo']) && isset($_POST['descripcion'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = date('Y-m-d');
    $controlador->agregarAviso($titulo, $descripcion, $fecha);
}

/* Metodo eliminar aviso */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminarAviso'])) {
    $idAviso = $_POST['idAviso'];
    $controlador->eliminarAviso($idAviso);
}
?>

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
            <h1>Gestión de Avisos</h1>

            <!-- Modal para agregar aviso -->
            <div class="modal fade" id="agregarAvisoModal" tabindex="-1" role="dialog" aria-labelledby="agregarAvisoModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="agregarAvisoModalLabel">Agregar Nuevo Aviso</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="gestionA.php?action=agregarAviso" method="POST">
                                <div class="form-group">
                                    <label for="titulo">Título:</label>
                                    <input type="text" id="titulo" name="titulo" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripción:</label>
                                    <textarea id="descripcion" name="descripcion" class="form-control" required></textarea>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Agregar Aviso</button>
                        </div>
                            </form>
                    </div>
                </div>
            </div>

            <!-- Tabla de avisos existentes -->
            <h2>Lista de Avisos</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avisos as $aviso) { ?>
                        <tr>
                            <td><?= $aviso['idA'] ?></td>
                            <td><?= $aviso['titulo'] ?></td>
                            <td><?= $aviso['descripcion'] ?></td>
                            <td><?= $aviso['fecha'] ?></td>
                            <td>
                                <!-- Enlace para editar (puedes mantenerlo como GET) -->
                                <a href="editarA.php?id=<?php echo $aviso['idA']; ?>" class="btn btn-primary">Editar</a>
                                
                                <!-- Formulario para eliminar el aviso -->
                                <form action="gestionA.php" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este aviso?');" style="display:inline;">
                                    <input type="hidden" name="idAviso" value="<?= $aviso['idA'] ?>">
                                    <button type="submit" name="eliminarAviso" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- Botón para abrir el modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#agregarAvisoModal">Agregar Nuevo Aviso</button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>