<?php
session_start();

require_once __DIR__ . '/../Controller/controlador.php';
include 'header.php'; 

// Llamada al método de cerrar sesión si la acción es 'logout'
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $controlador = new Controlador();
    $controlador->cerrarSesion();
}

if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] != 'admin') {
    header("Location: login.php");
    exit();
}

$controlador = new Controlador();

if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Debes iniciar sesión!!");</script>';
    session_destroy();
    header("Location: login.php");
    exit();
}

// Manejo de la creación de un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $contrasena = $_POST['contrasena'];
    $tipoUsuario = $_POST['tipoUsuario'];
    $programaE_idPE = $_POST['programaE_idPE'] ?? null;

    // Ahora llama a registrar con los parámetros correctos
    $admin = new Admin();
    $admin->registrar($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE);
}

// Verificar si se solicita eliminar un usuario
if (isset($_GET['action']) && $_GET['action'] == 'eliminar' && isset($_GET['id'])) {
    $idUsuario = $_GET['id'];
    // Llamar al método eliminarUsuario del controlador
    $resultado = $controlador->eliminarUsuario($idUsuario);
    exit();
}

// Obtener la lista de usuarios
$usuarios = $controlador->listarUsuarios();
$programas = $controlador->obtenerProgramasEducativos();  // Obtener los programas educativos
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

        <div class="col-12 col-md-10">
            <h1>Lista de Usuarios</h1>
            
            <!-- Contenedor responsivo de la tabla -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo</th>
                            <th>Tipo de Usuario</th>
                            <th>Fecha de Registro</th>
                            <th>Programa Edu</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['idU']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['tipoUsuario']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['fechaRegistro']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['programaE_idPE']); ?></td> 
                                    <td>
                                        <a href="editarUsuario.php?id=<?php echo htmlspecialchars($usuario['idU']); ?>" class="btn btn-primary btn-sm">Editar</a>
                                        <a href="gestionUsers.php?action=eliminar&id=<?php echo htmlspecialchars($usuario['idU']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">No hay usuarios disponibles.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <button class="btn btn-primary" data-toggle="modal" data-target="#crearUsuarioModal">Crear nuevo usuario</button>
        </div>
    </div>
</div>

<!-- Modal de crear usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearUsuarioModalLabel">Registrar nuevo usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="gestionUsers.php" method="POST">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="mb-3">
                        <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                        <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipoUsuario" class="form-label">Tipo de Usuario</label>
                        <select name="tipoUsuario" required class="form-select">
                            <option value="" disabled selected>Tipo de Usuario</option>
                            <option value="alumno">Alumno</option>
                            <option value="docente">Docente</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="programaE_idPE" class="form-label">Programa Educativo</label>
                        <select name="programaE_idPE" class="form-select">
                            <option value="" disabled selected>Programa Educativo</option>
                            <?php if (!empty($programas)): ?>
                                <?php foreach ($programas as $programa): ?>
                                    <option value="<?php echo htmlspecialchars($programa['idPE']); ?>">
                                        <?php echo htmlspecialchars($programa['clave']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No hay programas educativos disponibles</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>