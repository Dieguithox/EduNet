<?php
require_once __DIR__ . '/../Controller/Controlador.php';

$controlador = new Controlador();

/* Obtener los avisos para el alumno */
$avisos = $controlador->obtenerAvisosA();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>EduNet</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/styles.css"> <!-- Archivo CSS personalizado -->
</head>
<body>
    <!-- Encabezado -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-edunet">
        <div class="container-fluid">
        <a class="navbar-brand" href="<?php 
    // Verificar el tipo de usuario y redirigir al panel correspondiente
    if (isset($_SESSION['tipoUsuario'])) {
        switch ($_SESSION['tipoUsuario']) {
            case 'admin':
                echo 'panelAdmin.php';  // Enlace al panel de administrador
                break;
            case 'docente':
                echo 'panelMaestro.php';  // Enlace al panel de docente
                break;
            case 'alumno':
                echo 'panelAlumno.php';  // Enlace al panel de alumno
                break;
            default:
                echo 'login.php';  // Enlace a login si no se encuentra el tipo de usuario
        }
    } else {
        echo 'login.php';  // Si no hay sesión activa, redirigir al login
    }
?>">
    <img src="img/logoEDU.png" alt="Logo" class="navbar-logo">
    EduNet
</a>
            
            <form class="d-flex search-form">
                <input class="form-control" type="search" placeholder="Buscar..." aria-label="Buscar">
                <button class="btn btn-outline-light" type="submit">Buscar</button>
            </form>

            <div class="d-flex icons">
                <!-- Menú desplegable de notificaciones -->
                <div class="dropdown position-relative">
                    <button class="icon_btn dropdown-toggle" id="notificationDropdown" data-toggle="dropdown" 
                            aria-haspopup="true" aria-expanded="false" aria-label="Notificación">
                        <img src="img/sobre.png" alt="Aviso" class="icon">
                        <span class="notification-badge badge badge-danger"><?= count($avisos) ?></span> <!-- Cuántas notificaciones/avisos -->
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown">
                        <h6 class="dropdown-header">Avisos</h6>

                        <?php if (!empty($avisos)): ?>
                            <?php foreach ($avisos as $aviso): ?>
                                <a class="dropdown-item" href="#">
                                    <strong><?= htmlspecialchars($aviso['titulo']) ?></strong><br>
                                    <?= htmlspecialchars($aviso['descripcion']) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a class="dropdown-item" href="#">No hay nuevos avisos</a>
                        <?php endif; ?>

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="#">Ver todos</a>
                    </div>
                </div>

                <!-- Menú desplegable de usuario -->
                <div class="dropdown">
                    <button class="icon_btn dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Usuario">
                        <img src="img/usuario.png" alt="Usuario" class="icon">
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="gestionUsers.php?action=logout">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</body>
</html>