<?php
require_once '../Model/conexionBD.php';

class Maestro {
    private $db;

    public function __construct() {
        $this->db = obtenerConexion(); // Obtener conexión en el constructor
    }
    
    /* Funcion para obtener el usuario por su ID */
    public function obtenerUsuarioPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE idU = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        return $usuario;
    }

    /* Funcion para obtener el ID del profesor desde la tabla usuario */
    public function obtenerProfesorIdPorUsuario($usuarioId) {
        $query = "SELECT p.idP FROM profesor p JOIN usuario u ON u.idU = p.usuario_idU_P WHERE u.idU = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['idP'];
        } else {
            return null;
        }
    }


    /* Funcion para obtener los materiales pendientes */
public function obtenerMaterialesPendientes() {
    $stmt = $this->db->prepare("
        SELECT m.idM, m.titulo, m.categoria, m.descripcion, m.fechaSubida, m.estado, m.URL, 
        CONCAT(u.nombre, ' ', u.apellido) AS autor
        FROM material m
        JOIN usuario u ON m.usuario_idU_M = u.idU
        WHERE m.estado = 'pendiente'
    ");
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

    /* Funcion para actualizar el estado del material */
    public function actualizarEstadoMaterial($idMaterial, $nuevoEstado) {
        $query = "UPDATE material SET estado = ? WHERE idM = ?";
        $stmt = $this->db->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param("si", $nuevoEstado, $idMaterial);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }    

    /* Funcion para aprobar el material */
    public function aprobarMaterial($idMaterial, $comentarios, $fechaAprobacion, $esRechazado = false) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $usuarioId = $_SESSION['idU'];
        $profesor_idP_AM = $this->obtenerProfesorIdPorUsuario($usuarioId);
        if (!$profesor_idP_AM) {
            echo "<script>alert('No se encontró un profesor asociado a este usuario.');</script>";
            return false;
        }
        if (empty($comentarios) || empty($fechaAprobacion) || empty($idMaterial)) {
            echo "<script>alert('Todos los campos son obligatorios.');</script>";
            return false;
        }
        // Obtener el título del material, sin importar si está "pendiente" o en cualquier otro estado
        $tituloQuery = "SELECT titulo FROM material WHERE idM = ?";
        $stmtTitulo = $this->db->prepare($tituloQuery);
        $stmtTitulo->bind_param("i", $idMaterial);
        $stmtTitulo->execute();
        $result = $stmtTitulo->get_result();
        if ($row = $result->fetch_assoc()) {
            $titulo = $row['titulo'];
        } else {
            echo "<script>alert('Material no encontrado.');</script>";
            return false;
        }
        $stmtTitulo->close();

        $sql = "INSERT INTO aprobarMaterial(titulo, comentarios, fechaAprobacion, profesor_idP_AM) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssi", $titulo, $comentarios, $fechaAprobacion, $profesor_idP_AM);
            if ($stmt->execute()) {
                // Cambiar el estado del material según el parámetro esRechazado
                $nuevoEstado = $esRechazado ? 'rechazado' : 'aprobado';
                $this->actualizarEstadoMaterial($idMaterial, $nuevoEstado);
                $mensaje = $esRechazado ? 'Material rechazado exitosamente.' : 'Material aprobado exitosamente.';
                echo "<script>alert('$mensaje'); window.location.href='aprobarMaterial.php';</script>";
                $stmt->close();
                return true;
            } else {
                echo "<script>alert('Error al guardar los comentarios del material.');</script>";
                $stmt->close();
                return false;
            }
        } else {
            echo "<script>alert('Error al preparar la consulta.');</script>";
            return false;
        }
    }    
}
?>