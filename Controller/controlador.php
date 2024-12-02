<?php
// Incluye los modelos necesarios para manejar la logica del sistema
require_once __DIR__ . '/../Model/conexionBD.php';
require_once __DIR__ . '/../Model/usuario.php';
require_once __DIR__ . '/../Model/admin.php';
require_once __DIR__ . '/../Model/alumno.php';
require_once __DIR__ . '/../Model/maestro.php';

class Controlador {
    private $usuarioModel;
    private $adminModel; 
    private $alumnoModel;
    private $maestroModel;  

    /* Constructor: Inicializa los modelos para trabajar con datos del sistema */
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->adminModel = new Admin();
        $this->alumnoModel = new Alumno();
        $this->maestroModel = new Maestro();
    }

    /* Registrar un usuario en la base de datos */
    public function registrarUsuario($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE = null) {
        return $this->usuarioModel->registrar($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE);
    }

    /* Iniciar sesion y redirigir al panel correspondiente segun el tipo de usuario */
    public function iniciarSesion($correo, $contrasena) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $resultado = $this->usuarioModel->iniciarSesion($correo, $contrasena);
    
        if (isset($_SESSION['tipoUsuario'])) {
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
            }
            exit();
        }
    }

    /* Cerrar la sesion del usuario y redirigir al inicio de sesion */
    public function cerrarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset(); // Elimina variables de sesin
        session_destroy(); // Destruye la sesion
        header("Location: login.php");
        exit();
    }

    /* Obtener la lista de programas educativos */
    public function obtenerProgramasEducativos() {
        return $this->usuarioModel->obtenerProgramasEducativos();
    }

    /* Listar todos los usuarios registrados en el sistema */
    public function listarUsuarios() {
        return $this->adminModel->listarUsuarios(); 
    }    

    /* Eliminar un usuario de la base de datos */
    public function eliminarUsuario($id) {
        return $this->adminModel->eliminarUsuario($id);
    }

    /* Obtener informacion de un usuario por su ID */
    public function obtenerUsuarioPorId($id) {
        return $this->adminModel->obtenerUsuarioPorId($id);
    }

    /* Actualizar la informacion de un usuario existente */
    public function actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE) {
        return $this->adminModel->actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE);
    }

    /* Obtener programas educativos (version administrativa) */
    public function obtenerProgramasEducativos2() {
        return $this->adminModel->obtenerProgramas();
    }

    /* Registrar un nuevo programa educativo */
    public function registrarPrograma($nombre, $descripcion, $clave) {
        return $this->adminModel->registrarPrograma($nombre, $descripcion, $clave);
    }

    /* Actualizar un programa educativo existente */
    public function actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave) {
        return $this->adminModel->actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave);
    }

    /* Eliminar un programa educativo */
    public function eliminarPrograma($idPE) {
        return $this->adminModel->eliminarPrograma($idPE);
    }

    /* Obtener un programa educativo por su ID */
    public function obtenerProgramaPorID($idPE) {
        return $this->adminModel->obtenerProgramaPorID($idPE);
    }

    /* Realizar el respaldo de la base de datos */
    public function procesarRespaldo() {
        if (isset($_POST['backupnow'])) {
            $server = $_POST['server'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $dbname = $_POST['dbname'];

            $resultado = $this->adminModel->backDb($server, $username, $password, $dbname);

            if ($resultado) {
                echo "<script>alert('Respaldo realizado correctamente: " . $resultado . "'); window.location.href = 'respaldoDB.php';</script>";
            } else {
                echo "<script>alert('Hubo un error al realizar el respaldo'); window.location.href = 'respaldoDB.php';</script>";
            }
        }
    }

    /* Restaurar la base de datos desde un archivo SQL */
    public function restaurarBaseDeDatos() {
        $conn = new mysqli('localhost', 'root', '', 'edunet');
        
        if ($conn->connect_error) {
            echo "<script>alert('Error al conectar con la base de datos.'); window.location.href = 'respaldoDB.php';</script>";
            return;
        }
        
        $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            while ($table = $result->fetch_row()) {
                $conn->query("DROP TABLE IF EXISTS `" . $table[0] . "`;");
            }
        } else {
            echo "<script>alert('Error al obtener las tablas de la base de datos.'); window.location.href = 'respaldoDB.php';</script>";
            return;
        }
        
        $archivoSQL = $_FILES['archivo_sql']['tmp_name'];
        
        if (file_exists($archivoSQL)) {
            $query = file_get_contents($archivoSQL);
            
            if ($conn->multi_query($query)) {
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->next_result());
                
                echo "<script>alert('La base de datos se ha restaurado correctamente.'); window.location.href = 'respaldoDB.php';</script>";
            } else {
                echo "<script>alert('Error al restaurar la base de datos.'); window.location.href = 'respaldoDB.php';</script>";
            }
        } else {
            echo "<script>alert('El archivo no existe o no es válido.'); window.location.href = 'respaldoDB.php';</script>";
            return;
        }
        
        $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
        $conn->close();
    }

    /* Obtener avisos para mostrarlos a los usuarios */
    public function obtenerAvisos() {
        return $this->adminModel->obtenerAvisos();
    }
}
?>