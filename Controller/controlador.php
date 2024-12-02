<?php
require_once __DIR__ . '/../Model/conexionBD.php'; // Conexión a la base de datos
require_once __DIR__ . '/../Model/usuario.php'; // Modelo de Usuario
require_once __DIR__ . '/../Model/admin.php'; // Modelo de Admin
require_once __DIR__ . '/../Model/alumno.php'; //Modelo Alumno
require_once __DIR__ . '/../Model/maestro.php';

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
    
                                /*                          ADMIN                           */
    /* Funcion para obtener los programas educativos */
    public function obtenerProgramasEducativos() {
        return $this->usuarioModel->obtenerProgramasEducativos();
    }

    /* Funciones para listar, eliminar, obtener por ID y actualizar los usuarios */
    public function listarUsuarios() {
        return $this->adminModel->listarUsuarios(); 
    }    

    public function eliminarUsuario($id) {
        return $this->adminModel->eliminarUsuario($id);
    }
    
    public function obtenerUsuarioPorId($id) {
        return $this->adminModel->obtenerUsuarioPorId($id);
    }
    
    public function actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE) {
        return $this->adminModel->actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE);
    }
    
    /*                          ADMIN PE                           */

    // Método para obtener todos los programas educativos
    public function obtenerProgramasEducativos2() {
        return $this->adminModel->obtenerProgramas();
    }

    // Método para registrar un nuevo programa educativo
    public function registrarPrograma($nombre, $descripcion, $clave) {
        return $this->adminModel->registrarPrograma($nombre, $descripcion, $clave);
    }

    // Método para actualizar un programa educativo
    public function actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave) {
        return $this->adminModel->actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave);
    }

    // Método para eliminar un programa educativo
    public function eliminarPrograma($idPE) {
        return $this->adminModel->eliminarPrograma($idPE); // Mantener solo si es necesario
    }

    // Método para obtener un programa educativo por ID
    public function obtenerProgramaPorID($idPE) {
        return $this->adminModel->obtenerProgramaPorID($idPE);
    }
    
    /*                          RESPALDO Y RESTAURACIÓN DE BD                        */

    // Función para realizar el respaldo de la base de datos
    public function procesarRespaldo() {
        if (isset($_POST['backupnow'])) {
            // Recibe los datos del formulario
            $server = $_POST['server'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $dbname = $_POST['dbname'];

            // Llama al método del Modelo para hacer el respaldo
            $resultado = $this->adminModel->backDb($server, $username, $password, $dbname);

            // Verifica si el respaldo fue exitoso
            if ($resultado) {
                // Si el respaldo es exitoso, muestra un mensaje de éxito
                echo "<script>alert('Respaldo realizado correctamente: " . $resultado . "'); window.location.href = 'respaldoDB.php';</script>";
            } else {
                // Si ocurre un error, muestra un mensaje de error
                echo "<script>alert('Hubo un error al realizar el respaldo'); window.location.href = 'respaldoDB.php';</script>";
            }
        }
    }
    
    public function restaurarBaseDeDatos() {
        // Obtener la conexión a la base de datos
        $conn = new mysqli('localhost', 'root', '', 'edunet');
        
        if ($conn->connect_error) {
            // Error en la conexión
            echo "<script>alert('Error al conectar con la base de datos.'); window.location.href = 'respaldoDB.php';</script>";
            return;
        }
        
        // Desactivar las restricciones de claves foráneas para evitar errores con claves foráneas
        $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        // Obtener las tablas en la base de datos
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            while ($table = $result->fetch_row()) {
                // Generar el comando para eliminar las tablas
                $conn->query("DROP TABLE IF EXISTS `" . $table[0] . "`;");
            }
        } else {
            // Error al obtener las tablas
            echo "<script>alert('Error al obtener las tablas de la base de datos.'); window.location.href = 'respaldoDB.php';</script>";
            return;
        }
        
        // Ahora restaurar el archivo SQL
        $archivoSQL = $_FILES['archivo_sql']['tmp_name'];
        
        if (file_exists($archivoSQL)) {
            $query = file_get_contents($archivoSQL); // Leer el contenido del archivo SQL
            
            if ($conn->multi_query($query)) {
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->next_result());
                
                // Si todo salió bien, mostrar mensaje de éxito
                echo "<script>alert('La base de datos se ha restaurado correctamente.'); window.location.href = 'respaldoDB.php';</script>";
            } else {
                // Error en la restauración de la base de datos
                echo "<script>alert('Error al restaurar la base de datos.'); window.location.href = 'respaldoDB.php';</script>";
            }
        } else {
            // Error al encontrar el archivo
            echo "<script>alert('El archivo no existe o no es válido.'); window.location.href = 'respaldoDB.php';</script>";
            return;
        }
        
        // Volver a activar las restricciones de claves foráneas
        $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
        
        // Cerrar la conexión
        $conn->close();
    }    
    
                            /*                         Gestion de Avisos                          */
    public function obtenerAvisos() {
        return $this->adminModel->obtenerAvisos();
    }

    public function agregarAviso($titulo, $descripcion, $fecha) {
        return $this->adminModel->agregarAviso($titulo, $descripcion, $fecha);
    }                    
    
    public function eliminarAviso($idAviso) {
        $this->adminModel->eliminarAviso($idAviso);
    }
    
    /// Método para obtener un aviso por su ID
    public function obtenerAvisoPorID($idAviso) {
        return $this->adminModel->avisoId($idAviso);  // Llama al método del modelo
    }

    // Método para actualizar el aviso
    public function actualizarAviso($idA, $titulo, $descripcion) {
        return $this->adminModel->actualizarAviso($idA, $titulo, $descripcion);  // Llama al método del modelo
    }

                            /*                                   ALUMNO                      */

    // Controlador para mover el archivo
    public function crearMaterial($categoria, $titulo, $descripcion, $usuarioId, $file, $url) {
        return $this->alumnoModel->crearMaterial($categoria, $titulo, $descripcion, $usuarioId, $file, $url);
    }    

    public function listarMateriales($idU) {
        // Pasa el parámetro $idU al modelo
        return $this->alumnoModel->listarMateriales($idU);
    }    

    public function eliminarMaterial($idMaterial) {
        return $this->alumnoModel->eliminarMaterial($idMaterial);
    }

    public function obtenerMaterialPorId($idMaterial) {
        return $this->alumnoModel->obtenerMaterialPorId($idMaterial);
    }

    public function actualizarMaterial($idM, $categoria, $titulo, $descripcion, $fechaSubida, $URL) {
        // Llamar a la función del modelo para actualizar el material
        return $this->alumnoModel->actualizarMaterial($idM, $categoria, $titulo, $descripcion, $fechaSubida, $URL);
    }    
    
                        /*                         Gestion de Avisos para mostrarlos                        */
    public function obtenerAvisosA() {
        return $this->alumnoModel->obtenerAvisosA();  
    }

    public function obtenerComentarios($idMaterial) {
        return $this->alumnoModel->obtenerComentarios($idMaterial); 
    }
    
                                            /* APROBAR O RECHAZAR MATERIAL */
    
    // Método para obtener los materiales pendientes
    public function obtenerMaterialesPendientes() {
        return $this->maestroModel->obtenerMaterialesPendientes(); 
    }

    // Método para aprobar un material
    public function aprobarMaterial($idMaterial, $comentarios, $fechaAprobacion, $esRechazado = false){
        return $this->maestroModel->aprobarMaterial($idMaterial, $comentarios, $fechaAprobacion, $esRechazado);
    }
    
    public function rechazarMaterial($idMaterial, $comentarios, $fechaAprobacion){
        return $this->aprobarMaterial($idMaterial, $comentarios, $fechaAprobacion, true);
    }

    // Función para obtener los materiales aprobados
    public function obtenerMaterialesAprobados() {
        return $this->alumnoModel->obtenerMaterialesAprobados();
    }

    /* Calificar Material */
    public function calificarMaterial($materialId, $calificacion, $comentarios,$usuarioId) {
        // Llama al modelo para registrar la calificación
        return $this->alumnoModel->calificarMaterial($materialId, $calificacion, $comentarios, $usuarioId);
    }
    
    public function obtenerCalificacionPorUsuarioYMaterial($usuarioId, $materialId) {
        // Llamar a una función en el modelo para obtener la calificación
        return $this->alumnoModel->obtenerCalificacionPorUsuarioYMaterial($usuarioId, $materialId);
    }

    // Función para obtener la calificación y los comentarios de un usuario para un material
    public function obtenerCalificacionYComentarios($materialId, $usuarioId) {
        return $this->alumnoModel->obtenerCalificacionYComentarios($materialId, $usuarioId);
    }

    // Promediar calificacion
    public function obtenerCalificacionPromedio($materialId) {
        return $this->alumnoModel->obtenerCalificacionPromedio($materialId);
    }

    //Historial alumno
    public function obtenerUsuarioIdPorNombre($nombreUsuario) {
        $alumno = new Alumno();
        return $alumno->obtenerIdPorNombre($nombreUsuario);
    }

    public function mostrarHistorialAlumno($usuarioId) {
        return $this->alumnoModel->obtenerHistorialAlumno($usuarioId);
    }
}
?>