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
    echo '
        <script>
            alert("Debes iniciar sesión!!");
        </script>
    ';
    session_destroy();
    header("Location: login.php");
    exit();
}

/* Verificar si se ha enviado una solicitud para cerrar sesión */
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $controlador->cerrarSesion();
}

/* Obtener el ID del usuario a editar */
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $usuario = $controlador->obtenerUsuarioPorId($id);

    if (!$usuario) {
        echo '<script>alert("Usuario no encontrado."); window.location.href="gestionUsers.php";</script>';
        exit();
    }
}

/* Obtener la lista de programas educativos */
$programas = $controlador->obtenerProgramasEducativos();

/* Manejo de la actualización de los datos del usuario */
if (isset($_POST['submit'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $tipoUsuario = $_POST['tipoUsuario'];
    $programaE_idPE = $_POST['programaE_idPE'];

    $exito = $controlador->actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE);

    if ($exito) {
        echo '<script>alert("Usuario actualizado exitosamente"); window.location.href="gestionUsers.php";</script>';
    } else {
        echo '<script>alert("Error al actualizar el usuario.");</script>';
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
            <h1>Editar Usuario</h1>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="tipoUsuario">Tipo de Usuario:</label>
                    <select name="tipoUsuario" id="tipoUsuario" class="form-control" required>
                        <option value="admin" <?php echo ($usuario['tipoUsuario'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="docente" <?php echo ($usuario['tipoUsuario'] == 'docente') ? 'selected' : ''; ?>>Docente</option>
                        <option value="alumno" <?php echo ($usuario['tipoUsuario'] == 'alumno') ? 'selected' : ''; ?>>Alumno</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="programaE_idPE">Programa Educativo:</label>
                    <select name="programaE_idPE" id="programaE_idPE" class="form-control">
                        <option value="" disabled selected>Programa Educativo</option>
                        <?php if (!empty($programas)): ?>
                            <?php foreach ($programas as $programa): ?>
                                <option value="<?php echo htmlspecialchars($programa['idPE']); ?>" <?php echo ($usuario['programaE_idPE'] == $programa['idPE']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($programa['clave']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No hay programas educativos disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Actualizar</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='gestionUsers.php';">Cancelar</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>