<?php
session_start();
require_once __DIR__ . '/../Controller/controlador.php'; // Ruta correcta para el controlador

// Crear una instancia del controlador
$controlador = new Controlador();

// Redirigir si el usuario ya está autenticado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['formulario'])) {
    if ($_POST['formulario'] === "login") {
        // Recibir los datos del formulario de inicio de sesión
        $correo = trim($_POST['correo']);
        $contrasena = trim($_POST['contrasena']);
        
        // Validación de campos vacíos
        if (empty($correo) || empty($contrasena)) {
            echo '<script>alert("Por favor, ingrese tanto correo como contraseña.");</script>';
        } else {
            // Llama a la función iniciarSesion
            $resultado = $controlador->iniciarSesion($correo, $contrasena);
            if ($resultado === true) {
                // Redirigir según el tipo de usuario
                switch ($_SESSION['tipoUsuario']) {
                    case 'admin':
                        header("Location: panelAdmin.php");
                        break;
                    case 'docente':
                        header("Location: panelMaestro.php");
                        break;
                    case 'alumno':
                        header("Location: panelAlumno.php");
                        break;
                    default:
                        echo "Tipo de usuario no válido.";
                        break;
                }
                exit();
            } else {
                echo $resultado; // Muestra el mensaje de error
            }
        }
    }
}

// Llamar a la función para obtener programas educativos
$programas = $controlador->obtenerProgramasEducativos(); // Obtener programas educativos

// Procesar formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['formulario']) && $_POST['formulario'] === "registro") {
    // Recibir los datos del formulario de registro
    $usuario = trim($_POST['usuario']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $tipoUsuario = $_POST['tipoUsuario'];
    $programaE_idPE = $_POST['programaE_idPE'] ?? null;

    // Obtener la fecha actual en formato YYYY-MM-DD
    $currentDate = date('Y-m-d');

    // Validación de fecha de nacimiento no posterior a la fecha actual
    if (strtotime($fechaNacimiento) > strtotime($currentDate)) {
        echo '<script>alert("La fecha de nacimiento no puede ser posterior a la fecha actual.");
        window.location.href = "login.php";    
        </script>';
        return;
    }

    // Validaciones adicionales (si las necesitas)
    if (empty($usuario) || empty($nombre) || empty($apellido) || empty($correo) || empty($contrasena) || empty($tipoUsuario)) {
        echo '<script>alert("Por favor, complete todos los campos obligatorios.");
        window.location.href = "login.php";    
        </script>';
        return;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("El correo electrónico no es válido.");
        window.location.href = "login.php";    
        </script>';
        return;
    }

    if (strlen($contrasena) < 7) {
        echo '<script>alert("La contraseña debe tener al menos 7 caracteres.");
        window.location.href = "login.php";    
        </script>';
        return;
    }

    // Si todo es correcto, llamar a la función de registro
    $resultadoRegistro = $controlador->registrarUsuario($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleLog.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <main>
        <div class="contenedor_todo">
            <div class="caja_trasera">
                <div class="caja_trasera-login">
                    <h3>¿Ya tienes una cuenta?</h3>
                    <p>Inicia sesión para entrar en la página</p>
                    <button id="btn_iniciar-sesion">Iniciar Sesión</button>
                </div>
                <div class="caja_trasera-register">
                    <h3>¿Aún no tienes una cuenta?</h3>
                    <p>Regístrate para que puedas iniciar sesión</p>
                    <button id="btn_registrarse">Registrarse</button>
                </div>
            </div>
            <div class="contenedor_login-register">
                <!--Login-->
                <form method="POST" class="formulario_login">
                    <h2>Iniciar Sesión</h2>
                    <input type="hidden" name="formulario" value="login">
                    <input type="text" placeholder="Correo Electrónico" name="correo" required>
                    <input type="password" placeholder="Contraseña" name="contrasena" required>
                    <button type="submit">Entrar</button><br>
                </form>
                <!--Register-->
                <form method="POST" class="formulario_register">
                    <h2>Registrarse</h2>
                    <input type="hidden" name="formulario" value="registro">
                    <input type="text" placeholder="Usuario" name="usuario" required>
                    <input type="text" placeholder="Nombre" name="nombre" required>
                    <input type="text" placeholder="Apellido" name="apellido" required>
                    <input type="date" placeholder="Fecha de Nacimiento" name="fechaNacimiento" required>
                    <input type="email" placeholder="Correo Electrónico" name="correo" required>
                    <input type="password" placeholder="Contraseña" name="contrasena" required>

                    <select name="tipoUsuario" required>
                        <option value="" disabled selected>Tipo de Usuario</option>
                        <option value="alumno">Alumno</option>
                    </select>

                    <select name="programaE_idPE">
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

                    <button type="submit">Registrarse</button><br>
                </form>
            </div>
        </div>
    </main>
    <script src="js/script.js"></script>
</body>
</html>