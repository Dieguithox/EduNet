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

// Verificar si se pasó el parámetro 'id' en la URL
if (isset($_GET['id'])) {
    $idPE = $_GET['id'];
    // Aquí puedes usar el controlador para obtener los datos del programa educativo por su ID
    $programa = $controlador->obtenerProgramaPorID($idPE); // Asegúrate de tener este método en el controlador
    if (!$programa) {
        // Si no se encuentra el programa, redirige o muestra un mensaje de error
        echo '<script>alert("Programa no encontrado."); window.location.href = "gestionPE.php";</script>';
        exit();
    }
} else {
    // Si no se pasa el ID, redirige o muestra un mensaje de error
    echo '<script>alert("ID de programa no proporcionado."); window.location.href = "gestionPE.php";</script>';
    exit();
}

// Asegúrate de que el programa se recuperó correctamente
$nombre = $programa['nombre'];
$descripcion = $programa['descripcion'];
$clave = $programa['clave'];
$idPE = $programa['idPE'];

// Verificar si el formulario fue enviado para actualizar el programa educativo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombreNuevo = $_POST['nombre'];
    $descripcionNueva = $_POST['descripcion'];
    $claveNueva = $_POST['clave'];

    // Llamar al método del controlador para actualizar el programa educativo
    $actualizado = $controlador->actualizarProgramaEducativo($idPE, $nombreNuevo, $descripcionNueva, $claveNueva);
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
            <h2>Editar Programa Educativo</h2>
            <form method="POST">
                <!-- Campo oculto para el ID -->
                <input type="hidden" name="idPE" value="<?php echo $idPE; ?>">

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Programa</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo htmlspecialchars($descripcion); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="clave" class="form-label">Clave</label>
                    <input type="text" class="form-control" id="clave" name="clave" value="<?php echo htmlspecialchars($clave); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="gestionPE.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>