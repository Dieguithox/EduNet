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
    echo '<script>alert("Debes iniciar sesión!!");</script>';
    session_destroy();
    header("Location: login.php");
    exit();
}

// Verificar si se pasó el parámetro 'id' en la URL para editar un aviso
if (isset($_GET['id'])) {
    $idAviso = $_GET['id'];
    $aviso = $controlador->obtenerAvisoPorID($idAviso); // Llama al método del controlador
}

// Asegúrate de que el aviso se recuperó correctamente
$titulo = $aviso['titulo'];
$descripcion = $aviso['descripcion'];
$fecha = $aviso['fecha'];
$idAviso = $aviso['idA'];

// Verificar si el formulario fue enviado para actualizar el aviso
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $tituloNuevo = $_POST['titulo'];
    $descripcionNueva = $_POST['descripcion'];
    $idAviso = $_POST['idAviso']; // Asegurarse de que el ID del aviso se esté recibiendo correctamente

    // Llamar al método del modelo para actualizar el aviso
    $actualizado = $controlador->actualizarAviso($idAviso, $tituloNuevo, $descripcionNueva);
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
            <h2>Editar Aviso</h2>
            <form method="POST">
                <!-- Campo oculto para el ID -->
                <input type="hidden" name="idAviso" value="<?php echo $idAviso; ?>">

                <div class="mb-3">
                    <label for="titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo htmlspecialchars($descripcion); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="gestionA.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>