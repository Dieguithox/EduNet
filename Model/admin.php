<?php
require_once '../Model/conexionBD.php';

class Admin {
    private $db;

    public function __construct() {
        $this->db = obtenerConexion(); // Obtener conexión en el constructor
    }

    /* Funcion para eliminar un usuario de la tabla profesor y usuario */
    public function eliminarUsuario($id) {
        $stmtDeleteProfesor = $this->db->prepare("DELETE FROM profesor WHERE usuario_idU_P = ?");
        $stmtDeleteProfesor->bind_param("i", $id);
        $stmtDeleteProfesor->execute();
    
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

    /* Funcion para listar usuarios con un limite de 20  */
    public function listarUsuarios($limit = 20, $offset = 0) {
        $stmt = $this->db->prepare("SELECT idU, usuario, nombre, apellido, correo, tipoUsuario, fechaRegistro, programaE_idPE FROM usuario LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuarios = $resultado->fetch_all(MYSQLI_ASSOC); // Devuelve solo una parte de los usuarios
        $stmt->close(); // Cerrar el statement
        return $usuarios;
    }    

    /* Funcion para obtener los usuario s por su ID */
    public function obtenerUsuarioPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE idU = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        return $usuario;
    }
    
    /* Funcion para actualizar  usuario */
    public function actualizarUsuario($id, $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE) {
        $stmt = $this->db->prepare("UPDATE usuario SET nombre = ?, apellido = ?, correo = ?, tipoUsuario = ?, programaE_idPE = ? WHERE idU = ?");
        $stmt->bind_param("sssssi", $nombre, $apellido, $correo, $tipoUsuario, $programaE_idPE, $id);
        return $stmt->execute();
    }
    
    /* Función para verificar si el correo ya existe en la base de datos */
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

    /* Funcion para registrar usuarios */
    public function registrar($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE = null) {
        // Verifica si el correo ya existe
        if ($this->correoExistente($correo)) {
            echo '<script>alert("El correo electrónico ya está registrado");</script>';
            return false;
        }
    
        $stmt = $this->db->prepare("INSERT INTO usuario (usuario, nombre, apellido, fecha_nacimiento, correo, contrasena, tipoUsuario, fechaRegistro, programaE_idPE) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");

        $stmt->bind_param("sssssssi", $usuario, $nombre, $apellido, $fechaNacimiento, $correo, $contrasena, $tipoUsuario, $programaE_idPE);
    
        if ($stmt->execute()) {
            echo '<script>alert("Registro exitoso"); window.location.href="gestionUsers.php";</script>';
            $stmt->close();
            return true;
        } else {
            echo '<script>alert("Error en el registro");</script>';
            $stmt->close();
            return false;
        }
    
        $stmt->close();
    }    

    /* Funcion para obtener los programas educativos */
    public function obtenerProgramasEducativos() {
        $stmt = $this->db->prepare("SELECT idPE, clave FROM programaeducativo"); // Selecciona también 'idPE'
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $programas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $programas;
        } else {
            return [];
        }
    }
    
    /* Funcion para obtener todos los programas educativos */
    public function obtenerProgramas() {
        $sql = "SELECT * FROM programaeducativo";
        $resultado = $this->db->query($sql);
        if ($resultado) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    /* Funcion para registrar un nuevo programa educativo */
    public function registrarPrograma($nombre, $descripcion, $clave) {
        // Consulta SQL para insertar el nuevo programa
        $sql = "INSERT INTO programaeducativo (nombre, descripcion, clave) VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sss", $nombre, $descripcion, $clave);
            $stmt->execute();
            $stmt->close();
            
            header("Location: gestionPE.php");
            exit();
        } else {
            die("Error al preparar la consulta: " . $this->db->error);
        }
    }

    /* Funcion para actualizar un programa educativo */
    public function actualizarProgramaEducativo($idPE, $nombre, $descripcion, $clave) {
        $stmt = $this->db->prepare("UPDATE programaeducativo SET nombre = ?, descripcion = ?, clave = ? WHERE idPE = ?");
        
        if ($stmt) {
            $stmt->bind_param("sssi", $nombre, $descripcion, $clave, $idPE);
            $stmt->execute();
            $stmt->close();
            
            header("Location: gestionPE.php");
            exit();
        } else {
            echo "<script>alert('Error al actualizar el programa educativo.'); window.location.href = 'gestionPE.php';</script>";
            exit();
        }
    }

    /* Funcion para eliminar un programa educativo */
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

    /* Funcion para obtener un programa educativo por su ID */
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
                return null;
            }
        } else {
            die("Error al preparar la consulta: " . $this->db->error);
        }
    }

    /* Función para restaurar base de datos desde archivo SQL */  
    public function backDb($host, $user, $pass, $dbname, $tables = '*') {
        $conn = new mysqli($host, $user, $pass, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        // Obtener todas las tablas de la base de datos
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

        $outsql = '';
        
        foreach ($tables as $table) {
            $sql = "SHOW CREATE TABLE $table";
            $query = $conn->query($sql);
            $row = $query->fetch_row();
            
            $outsql .= "\n\n" . $row[1] . ";\n\n";
    
            // Obtener los datos de las tablas
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

    /* Funcion para restaurar la base de datos desde el archivo SQL */
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

    /* Funcion para obtener avisos */
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
    
    /* Funcion para agregar un aviso nuevo */
    public function agregarAviso($titulo, $descripcion, $fecha) {
        if ($this->db) {
            if (empty($titulo) || empty($descripcion) || empty($fecha)) {
                echo "<script>alert('Todos los campos son obligatorios.');</script>";
                return false;
            }
    
            $usuario_idU_A = $_SESSION['idU'];
    
            $query = "INSERT INTO aviso (titulo, descripcion, fecha, usuario_idU_A) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
    
            if ($stmt) {
                $stmt->bind_param("sssi", $titulo, $descripcion, $fecha, $usuario_idU_A);

                if ($stmt->execute()) {
                    echo "<script>alert('Aviso agregado exitosamente'); window.location.href='gestionA.php';</script>";
                    $stmt->close();
                    return true;
                } else {
                    echo "<script>alert('Error al agregar el aviso');</script>";
                    $stmt->close();
                    return false;
                }
            } else {
                echo "<script>alert('Error al preparar la consulta');</script>";
                return false;
            }
        } else {
            echo "<script>alert('Error de conexión a la base de datos');</script>";
            return false;
        }
    }

    /* Funcion para eliminar un aviso */
    public function eliminarAviso($idAviso) {
        $query = "DELETE FROM aviso WHERE idA = ?";
        $stmt = $this->db->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("i", $idAviso);

            if ($stmt->execute()) {
                $stmt->close();
                echo "<script>alert('Aviso eliminado exitosamente'); window.location.href='gestionA.php';</script>";
                return true;
            } else {
                $stmt->close();
                echo "<script>alert('Error al eliminar el aviso'); window.location.href='gestionA.php';</script>";
                return false;
            }
        } else {
            echo "<script>alert('Error al preparar la consulta'); window.location.href='gestionA.php';</script>";
            return false;
        }
    }

    /* Funcion para obtener el aviso por su ID */
    public function avisoId($idAviso) {
        $stmt = $this->db->prepare("SELECT * FROM aviso WHERE idA = ?");
        $stmt->bind_param("i", $idAviso);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            echo "<script>alert('Aviso no encontrado'); window.location.href = 'gestionA.php';</script>";
            return null;
        }
    }

    /* Funcion para actualizar avisos */
    public function actualizarAviso($idA, $titulo, $descripcion) {
        $stmt = $this->db->prepare("UPDATE aviso SET titulo = ?, descripcion = ? WHERE idA = ?");
        $stmt->bind_param("ssi", $titulo, $descripcion, $idA);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Aviso actualizado exitosamente'); window.location.href = 'gestionA.php';</script>";
            return true;
        } else {
            echo "<script>alert('Error al actualizar el aviso'); window.location.href = 'gestionA.php';</script>";
            return false;
        }
}
}
?>