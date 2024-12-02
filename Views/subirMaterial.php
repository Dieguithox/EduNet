<?php 
// Controlador y encabezado de página
require_once __DIR__ . '/../Controller/controlador.php';
include 'header.php';

// Manejo de sesión y autenticación
session_start();
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'alumno') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $categoria = $_POST['categoria'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $usuarioId = $_SESSION['idU']; // Obtener el ID del usuario desde la sesión

    // Verifica que el archivo haya sido subido correctamente
    $file = $_FILES['archivo'];
    $url = isset($_POST['url']) ? $_POST['url'] : null;
    
    // Llamar al controlador para crear el material
    $controlador->crearMaterial($categoria, $titulo, $descripcion, $usuarioId, $file, $url);
}
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

        <!-- Formulario para subir material -->
        <div class="container">
            <h2>Subir Material</h2>
            <form action="subirMaterial.php" method="POST" enctype="multipart/form-data">
                <!-- Selección de categoría (desplegable) -->
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select name="categoria" id="categoria" class="form-control" required>
                        <option value="" disabled selected>Selecciona una categoría</option>
                        <option value="Word">Word</option>
                        <option value="PDF">PDF</option>
                        <option value="Excel">Excel</option>
                        <option value="Power Point">Power Point</option>
                        <option value="Video">Video</option>
                        <option value="Link">Link</option>
                    </select>
                </div>

                <!-- Título -->
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Titulo de archivo" required>
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripcion de archivo" required></textarea>
                </div>

                <!-- Archivo -->
                <div class="mb-3">
                    <label for="archivo" class="form-label">Archivo (opcional)</label>
                    <input type="file" class="form-control" id="archivo" name="archivo">
                </div>

                <!-- URL -->
                <div class="mb-3">
                    <label for="url" class="form-label">URL (opcional, si no sube archivo)</label>
                    <input type="text" class="form-control" id="url" name="url" placeholder="Si no sube archivo, ingrese la URL">
                </div>

                <button type="submit" class="btn btn-primary">Subir Material</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>