<?php
session_start();

require_once __DIR__ . '/../Controller/controlador.php'; // Ruta correcta para el controlador
include 'header.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador(); // Instanciando el controlador

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo '
        <script>
            alert("Debes iniciar sesión!!");
        </script>
    ';
    session_destroy();
    header("Location: login.php");
    exit();
}

// Verificar si se ha solicitado un respaldo
if (isset($_POST['backupnow'])) {
    // Obtener los datos del formulario
    $server = $_POST['server'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dbname = $_POST['dbname'];

    // Llamar al método de respaldo desde el controlador
    $controlador->procesarRespaldo(); // Esto llama al método procesarRespaldo

    exit();
}

// Verificar si se ha solicitado la restauración
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo_sql']) && $_FILES['archivo_sql']['error'] == 0) {
    // Llamar a la función para restaurar la base de datos
    $controlador->restaurarBaseDeDatos();
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
                <li class="nav-item"><a href="respaldoDB.php?action=respaldar" class="nav-link">Respaldar</a></li>
                <li class="nav-item"><a href="respaldoDB.php" class="nav-link" data-bs-toggle="modal" data-bs-target="#restaurarModal">Restaurar</a></li>
            </ul>
        </div>

        <!-- Área de contenido -->
        <div class="col-12 col-md-10">
            <h1>Respaldo de la Base de Datos</h1>
            <div class="form-wrap">
                <form action="respaldoDB.php" method="post">
                <div class="form-group">
                    <label class="control-label mb-10">Host</label>
                    <input type="text" class="form-control" placeholder="Ingrese Name Server de DB, ejemplo: Localhost" name="server" id="server" required autocomplete="on" 
                        value="localhost">
                </div>
                <div class="form-group">
                    <label class="control-label mb-10">Nombre de usuario de la base de datos</label>
                    <input type="text" class="form-control" placeholder="Ingrese user de DB, ejemplo: root" name="username" id="username" required autocomplete="on" 
                        value="root">
                </div>
                <div class="form-group">
                    <label class="pull-left control-label mb-10">Contraseña de la base de datos</label>
                    <input type="password" class="form-control" placeholder="Ingrese la contraseña de la base de datos" name="password" id="password" 
                        value="">
                </div>
                <div class="form-group">
                    <label class="pull-left control-label mb-10">Nombre de la base de datos</label>
                    <input type="text" class="form-control" placeholder="Ingresa Nombre de DB" name="dbname" id="dbname" required autocomplete="on" 
                        value="edunet">
                </div>
                    <div class="form-group text-center">
                        <button type="submit" name="backupnow" class="btn btn-primary">Iniciar copia de seguridad</button>
                    </div>
                </form>
            </div>

            <!-- Modal de restaurar -->
            <h1>Restauracion de la Base de Datos</h1>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="archivo_sql" class="form-label">Seleccionar archivo SQL</label>
                    <input type="file" class="form-control" id="archivo_sql" name="archivo_sql" required>
                </div>
                <button type="submit" class="btn btn-primary">Restaurar</button>
            </form>
        </div>
    </div>
</div>
<!-- Bootstrap JS Bundle (sin integridad) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>