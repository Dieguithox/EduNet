<?php
session_start();
require_once __DIR__ . '/../Controller/controlador.php';

/* Verificar si el usuario está autenticado como docente */
if (!isset($_SESSION['usuario']) || $_SESSION['tipoUsuario'] != 'docente') {
    echo '<script>alert("Debes iniciar sesión como profesor!!");</script>';
    session_destroy();
    header("Location: login.php");
    exit();
}

$controlador = new Controlador();

/* Verificar si se ha enviado el formulario para aprobar material */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idMaterial']) && isset($_POST['comentarios'])) {
    $idMaterial = $_POST['idMaterial'];
    $comentarios = $_POST['comentarios'];
    $fechaAprobacion = date('Y-m-d H:i:s');
    $accion = $_POST['accion'];

    // Verificar si los campos están completos
    if (empty($idMaterial) || empty($comentarios)) {
        echo '<script>alert("Todos los campos son obligatorios.");</script>';
    } else {
        if ($accion == 'aceptar') {
            $controlador->aprobarMaterial($idMaterial, $comentarios, $fechaAprobacion);
        } elseif ($accion == 'rechazar') {
            $controlador->rechazarMaterial($idMaterial, $comentarios, $fechaAprobacion);
        }
    }
}

// Verificar si se ha enviado una solicitud para cerrar sesión
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $controlador->cerrarSesion();
    session_unset();
    header("Location: login.php");
    exit();
}

/* Obtener la lista de materiales pendientes */
$materialesPendientes = $controlador->obtenerMaterialesPendientes();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Material</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/styleAprobarMaterial.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include 'header.php'; ?>

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

        <!-- Contenido para aprobar material -->
        <div class="col-12 col-md-10 content">
            <h2>Material</h2>
            <div class="approval-container">
                <h3>Aprobar material</h3>
                
                <?php foreach ($materialesPendientes as $material): ?>
                    <form method="POST" action="aprobarMaterial.php">
                        <div class="material-card">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <strong>Autor:</strong> <?php echo htmlspecialchars($material['autor']); ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <strong>Título:</strong> <?php echo htmlspecialchars($material['titulo']); ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <strong>URL:</strong>
                                    <?php if (filter_var($material['URL'], FILTER_VALIDATE_URL)): ?>
                                        <a href="<?php echo htmlspecialchars($material['URL']); ?>" target="_blank">Ver material</a>
                                        <br>
                                        <small class="text-muted">
                                            <a href="<?php echo htmlspecialchars($material['URL']); ?>" target="_blank">
                                                <?php echo htmlspecialchars($material['URL']); ?>
                                            </a>
                                        </small>
                                    <?php else: ?>
                                        <a href="/estancia/uploads/<?php echo basename($material['URL']); ?>" target="_blank">Ver material</a>
                                        <br>
                                        <small class="text-muted">
                                            <a href="/estancia/uploads/<?php echo basename($material['URL']); ?>" target="_blank">
                                                /estancia/uploads/<?php echo basename($material['URL']); ?>
                                            </a>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <strong>Categoría:</strong> <?php echo htmlspecialchars($material['categoria']); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Comentarios:</strong>
                                    <textarea name="comentarios" class="comment-input" placeholder="Escribe tus comentarios aquí..."></textarea>
                                </div>
                            </div>
                            <div class="row justify-content-center mt-3">
                                <input type="hidden" name="idMaterial" value="<?php echo $material['idM']; ?>">
                                <button type="submit" name="accion" value="aceptar" class="btn btn-success action-button accept">Aceptar</button>
                                <button type="submit" name="accion" value="rechazar" class="btn btn-danger action-button reject">Rechazar</button>
                            </div>
                        </div>
                    </form>
                <?php endforeach; ?>
                
            </div>
        </div>

    </div>
</div>

</body>
</html>

<?php include 'footer.php'; ?>