<?php
/* Conexion a la base de datos y Modelos */
require_once __DIR__ . '/../Model/conexionBD.php';
require_once __DIR__ . '/../Model/usuario.php';
require_once __DIR__ . '/../Model/admin.php';
require_once __DIR__ . '/../Model/alumno.php';
require_once __DIR__ . '/../Model/maestro.php';

/* Controlador de los modelos */
class Controlador {
    private $usuarioModel;
    private $adminModel; 
    private $alumnoModel;
    private $maestroModel;    

    /* Instancia los modelos */
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->adminModel = new Admin();
        $this->alumnoModel = new Alumno();
        $this->maestroModel = new Maestro();
    }

    /*-------------------------------------------------------------------USUARIOS---------------------------------------------------------------------------------*/

    /* Funcion registrar Usuario */
    public function registrarUsuario($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE = null) {
        $resultado = $this->usuarioModel->registrar($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE);
        return $resultado;
    }          

    /* Funcion para cuando inicias sesion segun tu tipo de archivo */
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
    
    /* Funcion cerrar sesion */
    public function cerrarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset(); // Elimina todas las variables de sesión
        session_destroy(); // Destruye la sesión
        header("Location: login.php");
        exit();
    }
    
    /*-------------------------------------------------------------------ADMIN---------------------------------------------------------------------------------*/
    /* Funcion para obtener los programas educativos */
    public function obtenerProgramasEducativos() {
        return $this->usuarioModel->obtenerProgramasEducativos();
    }

    /* Funcion para listar, eliminar, obtener por ID y actualizar los usuarios */
    public function listarUsuarios() {
        return $this->adminModel->listarUsuarios(); 
    }    

    /* Funcion para eliminar usuario */
    public function eliminarUsuario($id) {
        return $this->adminModel->eliminarUsuario($id);
    }
    
    /* Funcion para obtener usuarios por su ID */
    public function obtenerUsuarioPorId($id) {
        return $this->adminModel->obtenerUsuarioPorId($id);
    }
    
    /* Funcion para actualizar a un usuario */
    public function actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE) {
        return $this->adminModel->actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE);
    }
    
    /*                          ADMIN PE                           */

    /* Funcion para obtener todos los programas educativos */
    public function obtenerProgramasEducativos2() {
        return $this->adminModel->obtenerProgramas();
    }

    /* Funcion para registrar un nuevo programa educativo */
    public function registrarPrograma($nombre, $descripcion, $clave) {
        return $this->adminModel->registrarPrograma($nombre, $descripcion, $clave);
    }

    /* Funcion para actualizar un programa educativo */
    public function actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave) {
        return $this->adminModel->actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave);
    }

    /* Funcion para eliminar un programa educativo */
    public function eliminarPrograma($idPE) {
        return $this->adminModel->eliminarPrograma($idPE);
    }

    /* Funcion para obtener un programa educativo por ID */
    public function obtenerProgramaPorID($idPE) {
        return $this->adminModel->obtenerProgramaPorID($idPE);
    }
    
    /*-------------------------------------------------------------------RESPALDO Y RESTAURACION DE LA BASE DE DATOS-------------------------------------------------------*/

    /* Función para realizar el respaldo de la base de datos */
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
    
    public function restaurarBaseDeDatos() {
        /* Obtener la conexión a la base de datos */
        $conn = new mysqli('localhost', 'root', '', 'edunet');
        
        if ($conn->connect_error) {
            echo "<script>alert('Error al conectar con la base de datos.'); window.location.href = 'respaldoDB.php';</script>";
            return;
        }
        $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            while ($table = $result->fetch_row()) {
                /* Comando para eliminar las tablas */
                $conn->query("DROP TABLE IF EXISTS " . $table[0] . ";");
            }
        } else {
            echo "<script>alert('Error al obtener las tablas de la base de datos.'); window.location.href = 'respaldoDB.php';</script>";
            return;
        }
        
        /* Restaurar el archivo SQL */
        $archivoSQL = $_FILES['archivo_sql']['tmp_name'];
        
        if (file_exists($archivoSQL)) {
            $query = file_get_contents($archivoSQL); // Leer el contenido del archivo SQL
            
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
        // Activar las restricciones de claves foráneas
        $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
        $conn->close();
    }    
    
    /*-------------------------------------------------------------------GESTION DE AVISOS---------------------------------------------------------------------------------*/

    /* Funcion para obtener los avisos */
    public function obtenerAvisos() {
        return $this->adminModel->obtenerAvisos();
    }

    /* Funcion para agregar nuevo aviso */
    public function agregarAviso($titulo, $descripcion, $fecha) {
        return $this->adminModel->agregarAviso($titulo, $descripcion, $fecha);
    }                    
    
    /* Funcion para eliminar aviso */
    public function eliminarAviso($idAviso) {
        $this->adminModel->eliminarAviso($idAviso);
    }
    
    /* Funcion para obtener un aviso por su ID */
    public function obtenerAvisoPorID($idAviso) {
        return $this->adminModel->avisoId($idAviso);
    }

    /* Funcion para actualizar el aviso */
    public function actualizarAviso($idA, $titulo, $descripcion) {
        return $this->adminModel->actualizarAviso($idA, $titulo, $descripcion);
    }

    /*-------------------------------------------------------------------ALUMNO---------------------------------------------------------------------------------*/

    /* Funcion para crear un material nuevo */
    public function crearMaterial($categoria, $titulo, $descripcion, $usuarioId, $file, $url) {
        return $this->alumnoModel->crearMaterial($categoria, $titulo, $descripcion, $usuarioId, $file, $url);
    }    

    /* Funcion para listar los materiales */
    public function listarMateriales($idU) {
        return $this->alumnoModel->listarMateriales($idU);
    }    

    /* Funcion para eliminar un material */
    public function eliminarMaterial($idMaterial) {
        return $this->alumnoModel->eliminarMaterial($idMaterial);
    }

    /* Funcion para obtene el material por su ID */
    public function obtenerMaterialPorId($idMaterial) {
        return $this->alumnoModel->obtenerMaterialPorId($idMaterial);
    }

    /* Funcion para actualizar un material */
    public function actualizarMaterial($idM, $categoria, $titulo, $descripcion, $fechaSubida, $URL) {
        // Llamar a la función del modelo para actualizar el material
        return $this->alumnoModel->actualizarMaterial($idM, $categoria, $titulo, $descripcion, $fechaSubida, $URL);
    }    
    
    /*-------------------------------------------------------------------GESTION DE AVISOS MOSTRADOS--------------------------------------------------------------------*/

    /* Funcion para obtener los avisos Alumno */
    public function obtenerAvisosA() {
        return $this->alumnoModel->obtenerAvisosA();  
    }

    /* Funcion para obtener los comentarios de los materiales aprobados o rechazados */
    public function obtenerComentarios($idMaterial) {
        return $this->alumnoModel->obtenerComentarios($idMaterial); 
    }
    
    /*-------------------------------------------------------------------APROBAR O RECHAZAR MATERIAL--------------------------------------------------------------------*/
    
    /* Funcion para obtener los materiales pendientes por revision */
    public function obtenerMaterialesPendientes() {
        return $this->maestroModel->obtenerMaterialesPendientes(); 
    }

    /* Funcion para aprobar el material */
    public function aprobarMaterial($idMaterial, $comentarios, $fechaAprobacion, $esRechazado = false){
        return $this->maestroModel->aprobarMaterial($idMaterial, $comentarios, $fechaAprobacion, $esRechazado);
    }
    
    /* Funcion para rechazar material */
    public function rechazarMaterial($idMaterial, $comentarios, $fechaAprobacion){
        return $this->aprobarMaterial($idMaterial, $comentarios, $fechaAprobacion, true);
    }

    /* Funcion para obtener los materiales aprobados */
    public function obtenerMaterialesAprobados() {
        return $this->alumnoModel->obtenerMaterialesAprobados();
    }

    /* Funcion para calificar material */
    public function calificarMaterial($materialId, $calificacion, $comentarios,$usuarioId) {
        return $this->alumnoModel->calificarMaterial($materialId, $calificacion, $comentarios, $usuarioId);
    }
    
    /* Funcion para Obtener la calificacion por usuario dependiendo del material*/
    public function obtenerCalificacionPorUsuarioYMaterial($usuarioId, $materialId) {
        return $this->alumnoModel->obtenerCalificacionPorUsuarioYMaterial($usuarioId, $materialId);
    }

    /* Función para obtener la calificación y los comentarios de un usuario para un material */
    public function obtenerCalificacionYComentarios($materialId, $usuarioId) {
        return $this->alumnoModel->obtenerCalificacionYComentarios($materialId, $usuarioId);
    }

    /* Funcion para promediar la calificacion */
    public function obtenerCalificacionPromedio($materialId) {
        return $this->alumnoModel->obtenerCalificacionPromedio($materialId);
    }

    /* Funcion para el historial alumno */
    public function obtenerUsuarioIdPorNombre($nombreUsuario) {
        $alumno = new Alumno();
        return $alumno->obtenerIdPorNombre($nombreUsuario);
    }

    public function mostrarHistorialAlumno($usuarioId) {
        return $this->alumnoModel->obtenerHistorialAlumno($usuarioId);
    }
}
?>