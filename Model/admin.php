<?php
require_once '../Model/conexionBD.php';

class Admin {
    private $db;

    public function __construct() {
        $this->db = obtenerConexion(); // Obtener conexión en el constructor
    }

    public function eliminarUsuario($id) {
        // Primero, elimina los registros en la tabla 'profesor' que hacen referencia al usuario
        $stmtDeleteProfesor = $this->db->prepare("DELETE FROM profesor WHERE usuario_idU_P = ?");
        $stmtDeleteProfesor->bind_param("i", $id);
        $stmtDeleteProfesor->execute(); // Ejecutar eliminación de profesores asociados
    
        // Luego, procede a eliminar el usuario
        $stmt = $this->db->prepare("DELETE FROM usuario WHERE idU = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo '<script>
                    alert("Usuario eliminado exitosamente");
                    window.location.href = "gestionUsers.php";
                </script>';
            $stmt->close();
            return true;
        } else {
            echo '<script>alert("Error al eliminar el usuario");</script>';
            $stmt->close();
            return false;
        }
    }        

    public function listarUsuarios($limit = 20, $offset = 0) {
        $stmt = $this->db->prepare("SELECT idU, usuario, nombre, apellido, correo, tipoUsuario, fechaRegistro, programaE_idPE FROM usuario LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuarios = $resultado->fetch_all(MYSQLI_ASSOC); // Devuelve solo una parte de los usuarios
        $stmt->close(); // Cerrar el statement
        return $usuarios;
    }    

    public function obtenerUsuarioPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE idU = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        return $usuario;
    }
    
    public function actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE) {
        $stmt = $this->db->prepare("UPDATE usuario SET nombre = ?, apellido = ?, correo = ?, tipoUsuario = ?, programaE_idPE = ? WHERE idU = ?");
        $stmt->bind_param("sssssi", $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE, $id);
        return $stmt->execute();
    }
    
    // Función para verificar si el correo ya existe en la base de datos
    public function correoExistente($correo) {
        $stmt = $this->db->prepare("SELECT idU FROM usuario WHERE correo = ?");
        
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();
        $numRows = $stmt->num_rows;

        $stmt->close();

        return $numRows > 0;
    }

    public function registrar($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE = null) {
        // Verifica si el correo ya existe
        if ($this->correoExistente($correo)) {
            echo '<script>alert("El correo electrónico ya está registrado");</script>';
            return false;
        }
    
        // Prepara la consulta SQL para insertar el usuario
        $stmt = $this->db->prepare("INSERT INTO usuario (usuario, nombre, apellido, fecha_nacimiento, correo, contrasena, tipoUsuario, fechaRegistro, programaE_idPE) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");

        // Asignar los valores a los parámetros de la consulta
        $stmt->bind_param("sssssssi", $usuario, $nombre, $apellido, $fechaNacimiento, $correo, $contrasena, $tipoUsuario, $programaE_idPE);
    
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo '<script>alert("Registro exitoso"); window.location.href="gestionUsers.php";</script>';
            $stmt->close();
            return true; // Si el registro es exitoso, redirige al listado de usuarios
        } else {
            echo '<script>alert("Error en el registro");</script>';
            $stmt->close();
            return false; // Si ocurre un error en el registro
        }
    
        $stmt->close(); // Cerrar la sentencia preparada
    }    

    public function obtenerProgramasEducativos() {
        $stmt = $this->db->prepare("SELECT idPE, clave FROM programaeducativo"); // Selecciona también 'idPE'
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $programas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close(); // Cerrar el statement
            return $programas;
        } else {
            return [];
        }
    }
    
    /* CRUD PROGRAMAS EDUCATIVOS */
    // Método para obtener todos los programas educativos
    public function obtenerProgramas() {
        $sql = "SELECT * FROM programaeducativo";
        $resultado = $this->db->query($sql);
        if ($resultado) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    // Registrar un nuevo programa educativo
    public function registrarPrograma($nombre, $descripcion, $clave) {
        // Consulta SQL para insertar el nuevo programa
        $sql = "INSERT INTO programaeducativo (nombre, descripcion, clave) VALUES (?, ?, ?)";
        
        // Preparar la consulta
        $stmt = $this->db->prepare($sql);
        
        if ($stmt) {
            // Enlazar los parámetros
            $stmt->bind_param("sss", $nombre, $descripcion, $clave);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Cerrar la sentencia
            $stmt->close();
            
            // Redirigir a la página de gestión de programas educativos
            header("Location: gestionPE.php");
            exit(); // Asegurarse de detener la ejecución después de la redirección
        } else {
            // Si hay un error al preparar la consulta, muestra un mensaje
            die("Error al preparar la consulta: " . $this->db->error);
        }
    }

    // Método para actualizar un programa educativo
    public function actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave) {
        // Consulta SQL para actualizar el programa educativo
        $stmt = $this->db->prepare("UPDATE programaeducativo SET nombre = ?, descripcion = ?, clave = ? WHERE idPE = ?");
        
        if ($stmt) {
            $stmt->bind_param("sssi", $nombre, $descripcion, $clave, $idPE);
            $stmt->execute();
            $stmt->close();
            
            // Si la actualización fue exitosa, redirige a 'gestionPE.php'
            header("Location: gestionPE.php");
            exit(); // Asegúrate de detener la ejecución después de la redirección
        } else {
            // En caso de error, muestra un mensaje de error
            echo "<script>alert('Error al actualizar el programa educativo.'); window.location.href = 'gestionPE.php';</script>";
            exit();
        }
    }

    // Eliminar un programa educativo
    public function eliminarPrograma($idPE) {
        $sql = "DELETE FROM programaeducativo WHERE idPE = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $idPE);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error al preparar la consulta: " . $this->db->error);
        }
    }

    // Obtener un programa educativo por su ID
    public function obtenerProgramaPorID($idPE) {
        $stmt = $this->db->prepare("SELECT * FROM programaeducativo WHERE idPE = ?");
        if ($stmt) {
            $stmt->bind_param('i', $idPE);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado->num_rows > 0) {
                $programa = $resultado->fetch_assoc();
                $stmt->close();
                return $programa;
            } else {
                $stmt->close();
                return null; // Si no se encuentra el programa
            }
        } else {
            die("Error al preparar la consulta: " . $this->db->error);
        }
    }

    //                              Función para restaurar base de datos desde archivo SQL      
    public function backDb($host, $user, $pass, $dbname, $tables = '*') {
        // Conectar a la base de datos
        $conn = new mysqli($host, $user, $pass, $dbname);
        
        // Verificar si la conexión es exitosa
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        // Si no se especifican tablas, obtener todas las tablas de la base de datos
        if ($tables == '*') {
            $tables = array();
            $sql = "SHOW TABLES";
            $query = $conn->query($sql);
            while ($row = $query->fetch_row()) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
    
        // Contenedor para el SQL de respaldo
        $outsql = '';
        
        // Generar el SQL para cada tabla
        foreach ($tables as $table) {
            // Obtener la estructura de la tabla
            $sql = "SHOW CREATE TABLE $table";
            $query = $conn->query($sql);
            $row = $query->fetch_row();
            
            // Escribir la estructura de la tabla en el archivo de respaldo
            $outsql .= "\n\n" . $row[1] . ";\n\n";
    
            // Obtener los datos de la tabla
            $sql = "SELECT * FROM $table";
            $query = $conn->query($sql);
            $columnCount = $query->field_count;
    
            // Insertar los datos de cada fila en el SQL
            while ($row = $query->fetch_row()) {
                $outsql .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $columnCount; $j++) {
                    // Si el valor es NULL, escribir "" (vacío)
                    if (isset($row[$j])) {
                        $outsql .= '"' . $row[$j] . '"';
                    } else {
                        $outsql .= '""';
                    }
                    if ($j < ($columnCount - 1)) {
                        $outsql .= ',';
                    }
                }
                $outsql .= ");\n";
            }
        }
    
        // Nombre del archivo de respaldo
        $backup_file_name = __DIR__ . '/../backups/' . $dbname . '_respaldo.sql';
        //$backup_file_name = $dbname . '_respaldo.sql';
    
        // Guardar el SQL generado en el archivo
        $fileHandler = fopen($backup_file_name, 'w+');
        fwrite($fileHandler, $outsql);
        fclose($fileHandler);
    
        // Forzar la descarga del archivo SQL generado
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($backup_file_name));
        ob_clean();
        flush();
        readfile($backup_file_name);
    
        // Eliminar el archivo de respaldo después de la descarga
        exec('rm ' . $backup_file_name);
    }

    /*                Restaurar Base de datos                */
    // Método para restaurar la base de datos desde el archivo SQL
    public function restoreDb($host, $user, $pass, $dbname, $file) {
        // Conexión a la base de datos
        $conn = new mysqli($host, $user, $pass, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Leer el archivo SQL
        $sql = file_get_contents($file);

        if ($sql === false) {
            die("Error al leer el archivo SQL.");
        }

        // Ejecutar el archivo SQL
        if ($conn->multi_query($sql)) {
            $resultado = "Base de datos restaurada con éxito.";
        } else {
            $resultado = "Error al restaurar la base de datos: " . $conn->error;
        }

        $conn->close();
        return $resultado;
    }

    /*                AVISOS                */
    public function obtenerAvisos() {
        $avisos = [];
        $query = "SELECT * FROM aviso";
        
        if ($resultado = $this->db->query($query)) {
            while ($fila = $resultado->fetch_assoc()) {
                $avisos[] = $fila;
            }
            $resultado->close();
        }
        
        return $avisos;
    }
    
    public function agregarAviso($titulo, $descripcion, $fecha) {
        // Verificar la conexión antes de realizar la consulta
        if ($this->db) {
            // Validar que todos los campos estén completos
            if (empty($titulo) || empty($descripcion) || empty($fecha)) {
                echo "<script>alert('Todos los campos son obligatorios.');</script>";
                return false; // Retorna false si algún campo está vacío
            }
    
            // Obtener el id del usuario autenticado desde la sesión
            $usuario_idU_A = $_SESSION['idU']; // El idU debe estar en la sesión
    
            // Consulta para insertar el aviso
            $query = "INSERT INTO aviso (titulo, descripcion, fecha, usuario_idU_A) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
    
            if ($stmt) {
                // Asignar los parámetros a la consulta preparada
                $stmt->bind_param("sssi", $titulo, $descripcion, $fecha, $usuario_idU_A);
    
                // Ejecutar la consulta
                if ($stmt->execute()) {
                    echo "<script>alert('Aviso agregado exitosamente'); window.location.href='gestionA.php';</script>";
                    $stmt->close();
                    return true; // Retorna true si se agrega el aviso exitosamente
                } else {
                    echo "<script>alert('Error al agregar el aviso');</script>";
                    $stmt->close();
                    return false; // Retorna false si hubo un error en la ejecución
                }
            } else {
                echo "<script>alert('Error al preparar la consulta');</script>";
                return false; // Retorna false si hubo un error al preparar la consulta
            }
        } else {
            echo "<script>alert('Error de conexión a la base de datos');</script>";
            return false; // Retorna false si hay un error de conexión
        }
    }

    // Modelo - Eliminar aviso
    public function eliminarAviso($idAviso) {
        $query = "DELETE FROM aviso WHERE idA = ?";
        $stmt = $this->db->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $idAviso);

            if ($stmt->execute()) {
                $stmt->close();
                // Genera el mensaje de éxito en JavaScript
                echo "<script>alert('Aviso eliminado exitosamente'); window.location.href='gestionA.php';</script>";
                return true;
            } else {
                $stmt->close();
                // Genera el mensaje de error en JavaScript
                echo "<script>alert('Error al eliminar el aviso'); window.location.href='gestionA.php';</script>";
                return false;
            }
        } else {
            // Genera el mensaje de error en JavaScript si falla la preparación de la consulta
            echo "<script>alert('Error al preparar la consulta'); window.location.href='gestionA.php';</script>";
            return false;
        }
    }

    // Modelo - Obtener aviso por ID
    public function avisoId($idAviso) {
        $stmt = $this->db->prepare("SELECT * FROM aviso WHERE idA = ?");
        $stmt->bind_param("i", $idAviso);  // "i" indica que el parámetro es un entero
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc(); // Devuelve el aviso como un array asociativo
        } else {
            // Si no se encuentra el aviso, muestra un mensaje en JavaScript
            echo "<script>alert('Aviso no encontrado'); window.location.href = 'gestionA.php';</script>";
            return null; // Si no se encuentra el aviso
        }
    }

    // Modelo - Actualizar aviso
    public function actualizarAviso($idA, $titulo, $descripcion) {
        $stmt = $this->db->prepare("UPDATE aviso SET titulo = ?, descripcion = ? WHERE idA = ?");
        $stmt->bind_param("ssi", $titulo, $descripcion, $idA);  // "ssi" indica string, string, integer
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Si se actualiza correctamente, muestra un mensaje de éxito en JavaScript
            echo "<script>alert('Aviso actualizado exitosamente'); window.location.href = 'gestionA.php';</script>";
            return true;  // Devuelve true si se actualizó al menos un registro
        } else {
            // Si no se actualiza, muestra un mensaje de error en JavaScript
            echo "<script>alert('Error al actualizar el aviso'); window.location.href = 'gestionA.php';</script>";
            return false;
        }
}
}
?>