<?php  
require_once __DIR__ . '/../Controller/controlador.php';
include 'header.php'; 

/* Manejo de sesión y autenticación */
session_start();
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'alumno') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador();

/* Obtener el ID del material a editar */
if (isset($_GET['id'])) {
    $idMaterial = $_GET['id'];
    $material = $controlador->obtenerMaterialPorId($idMaterial);

    if (!$material) {
        exit();
    }
} else {
    exit();
}

/* Actualizar el material si se envió el formulario */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoria = $_POST['categoria'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];

    // Asignamos la fecha actual a fechaSubida
    $fechaSubida = date('Y-m-d H:i:s');
    
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
        $fileTmpPath = $_FILES['archivo']['tmp_name'];
        $fileName = $_FILES['archivo']['name'];
        $fileDestination = $_SERVER['DOCUMENT_ROOT'] . '/estancia/uploads/' . $fileName; // Usando ruta relativa a la raíz del proyecto
    
        if (move_uploaded_file($fileTmpPath, $fileDestination)) {
            $URL = '/estancia/uploads/' . $fileName; // URL del archivo subido
        } else {
            echo "Error al mover el archivo.";
            exit();
        }
    } else {
        /* URL */
        $URL = $_POST['url'] ?: $material['URL']; // Si no hay URL proporcionada, mantén la original
    }    

    /* Actualizar material en la base de datos */
    $controlador->actualizarMaterial($idMaterial, $categoria, $titulo, $descripcion, $fechaSubida, $URL);
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
                <!--<li class="nav-item">
                    <a href="panelAlumno.php" class="btn btn-link nav-link">Página Principal</a>
                </li>-->
                <li class="nav-item">
                    <a href="gestionMaterial.php" class="btn btn-link nav-link">Mi material</a>
                </li>
                <li class="nav-item">
                    <a href="generarReporte.php" class="btn btn-link nav-link">Generar reporte</a>
                </li>
            </ul>
        </div>
        
        <!-- Formulario para editar material -->
        <div class="container">
            <h2>Editar Material</h2>
            <form action="editarMaterial.php?id=<?= $material['idM']; ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select name="categoria" id="categoria" class="form-control" required>
                        <option value="" disabled>Selecciona una categoría</option>
                        <option value="Word" <?= $material['categoria'] == 'Word' ? 'selected' : ''; ?>>Word</option>
                        <option value="PDF" <?= $material['categoria'] == 'PDF' ? 'selected' : ''; ?>>PDF</option>
                        <option value="Excel" <?= $material['categoria'] == 'Excel' ? 'selected' : ''; ?>>Excel</option>
                        <option value="Power Point" <?= $material['categoria'] == 'Power Point' ? 'selected' : ''; ?>>Power Point</option>
                        <option value="Video" <?= $material['categoria'] == 'Video' ? 'selected' : ''; ?>>Video</option>
                        <option value="Link" <?= $material['categoria'] == 'Link' ? 'selected' : ''; ?>>Link</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $material['titulo']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" required><?= $material['descripcion']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="archivo" class="form-label">Archivo</label>
                    <input type="file" class="form-control" id="archivo" name="archivo">
                    <small class="form-text text-muted">Si no subes un archivo, puedes dejar la URL tal como está.</small>
                    <?php if ($material['URL']) { ?>
                        <p>Archivo actual: <a href="<?= $material['URL']; ?>" target="_blank">Ver archivo</a></p>
                    <?php } ?>
                </div>
                <div class="mb-3">
                    <label for="url" class="form-label">URL del archivo</label>
                    <input type="text" class="form-control" id="url" name="url" value="<?= $material['URL']; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Material</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='gestionMaterial.php';">Cancelar</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>