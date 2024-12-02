<?php
session_start();
require_once __DIR__ . '/../Controller/controlador.php';
include 'header.php';

// Validación de sesión
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'alumno') {
    header("Location: login.php");
    exit();
}

// Instanciar el controlador
$controlador = new Controlador();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión!!");</script>';
    session_destroy();
    header("Location: login.php");
    exit();
}

// Verificar si el id del material está en la URL y obtener sus datos a través del controlador
if (isset($_GET['idAM'])) {
    $idAM = $_GET['idAM'];
    $material = $controlador->obtenerMaterialPorId($idAM);
}

// Procesar la solicitud POST solo si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['materialId'], $_POST['calificacion'], $_POST['comentarios'])) {
        $materialId = intval($_POST['materialId']);
        $calificacion = intval($_POST['calificacion']);
        $comentarios = trim($_POST['comentarios']);

        $usuarioId = $_SESSION['idU'];  // O el valor que corresponda
        // Procesar la calificación
        $resultado = $controlador->calificarMaterial($materialId, $calificacion, $comentarios, $usuarioId);

        // Mensaje de éxito o error con JavaScript alert
        if ($resultado) {
            echo '<script>alert("¡Calificación registrada con éxito!");</script>';
        } else {
            echo '<script>alert("Hubo un error al registrar la calificación.");</script>';
        }
    }
}

$calificacionExistente = $controlador->obtenerCalificacionYComentarios($idAM, $_SESSION['idU']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar Material</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .star-rating {
            direction: rtl;
            display: flex;
            justify-content: center;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
        }
        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f5c518;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Barra lateral -->
        <div class="col-12 col-md-2 bg-light sidebar custom-sidebar">
            <div class="button-container d-flex justify-content-center mb-3">
                <a href="subirMaterial.php" class="btn btn-primary btn_custom">
                    <img src="img/subir.png" alt="Subir" class="icon-img"> Subir material
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

        <!-- Área principal -->
        <div class="col-12 col-md-10 mt-5">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Calificar Material</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($material)) : ?>
                        <h5>Detalles del Material</h5>
                        <p><strong>Título:</strong> <?= htmlspecialchars($material['titulo']) ?></p>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($material['autor']) ?></p>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($material['categoria']) ?></p>
                        <p><strong>Descripción:</strong> <?= htmlspecialchars($material['descripcion']) ?></p>
                        <p><strong>URL:</strong> 
                        <?php if (filter_var($material['URL'], FILTER_VALIDATE_URL)): ?>
                            <a href="<?= htmlspecialchars($material['URL']) ?>" target="_blank">Ver material</a>
                        <?php else: ?>
                            <a href="/estancia/uploads/<?= basename($material['URL']) ?>" target="_blank">Ver material</a>
                        <?php endif; ?>
                    </p>

                    <!-- Formulario de calificación -->
                    <form action="calificarMaterial.php?idAM=<?= htmlspecialchars($idAM) ?>" method="post">
                        <input type="hidden" name="materialId" value="<?= htmlspecialchars($idAM) ?>">
                        <h5 class="mt-4">Calificación</h5>
                        <div class="star-rating" role="radiogroup">
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="star<?= $i ?>" name="calificacion" value="<?= $i ?>"
                                    <?= isset($calificacionExistente['calificacion']) && $calificacionExistente['calificacion'] == $i ? 'checked' : '' ?>>
                                <label for="star<?= $i ?>" title="<?= $i ?> estrellas" aria-label="<?= $i ?> estrellas">&#9733;</label>
                            <?php endfor; ?>
                        </div>

                        <div class="form-group mt-4">
                            <label for="comentarios">Comentarios (opcional):</label>
                            <textarea class="form-control" name="comentarios" id="comentarios" rows="3" placeholder="Escribe tus comentarios aquí..."><?= isset($calificacionExistente['comentarios']) ? htmlspecialchars($calificacionExistente['comentarios']) : '' ?></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary mt-3">Enviar calificación</button>
                            <a href="panelAlumno.php" class="btn btn-secondary mt-3">Volver</a>
                        </div>
                    </form>
                    <?php else : ?>
                        <div class="alert alert-danger" role="alert">
                            No se encontró información sobre el material.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>