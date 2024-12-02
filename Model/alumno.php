<?php
require_once '../Model/conexionBD.php';

class Alumno {
    private $db;

    public function __construct() {
        $this->db = obtenerConexion();
    }

    // Crear material
    public function crearMaterial($categoria, $titulo, $descripcion, $usuarioId, $file, $url) {
        $fechaSubida = date('Y-m-d H:i:s'); // Fecha y hora actuales

        // Gestionar la subida del archivo (URL)
        // En la parte donde guardas el archivo
        if (isset($file) && $file['error'] == 0) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $file['name'];
            $fileDestination = '../../estancia/uploads/' . $fileName; // Ruta interna donde se guarda el archivo en el servidor

            // Mover el archivo a la carpeta 'uploads'
            if (move_uploaded_file($fileTmpPath, $fileDestination)) {
                $URL = '/estancia/uploads/' . $fileName; // Ruta accesible por la web
            } else {
                echo "<script>alert('Error al subir el archivo'); window.location.href='subirMaterial.php';</script>";
                return;
            }
        } else {
            $URL = !empty($url) ? $url : null;
        }

        // Llamamos al modelo para insertar el material en la base de datos
        $stmt = $this->db->prepare("INSERT INTO material (categoria, titulo, descripcion, fechaSubida, estado, URL, usuario_idU_M) VALUES (?, ?, ?, ?, 'pendiente', ?, ?)");
        
        // Ejecutar la consulta y verificar si se insertó correctamente
        if ($stmt->execute([$categoria, $titulo, $descripcion, $fechaSubida, $URL, $usuarioId])) {
            echo "<script>alert('Material subido exitosamente'); window.location.href='subirMaterial.php';</script>";
        } else {
            echo "<script>alert('Hubo un error al subir el material'); window.location.href='subirMaterial.php';</script>";
        }
    }

    // Obtener todos los materiales de un usuario
    public function listarMateriales($usuarioId) {
        $stmt = $this->db->prepare("SELECT idM, categoria, titulo, descripcion, estado, URL FROM material WHERE usuario_idU_M = ?");
        $stmt->execute([$usuarioId]);
        $resultado = $stmt->get_result();
        $materiales = $resultado->fetch_all(MYSQLI_ASSOC); // Devuelve todos los materiales de ese usuario
        $stmt->close();
        return $materiales;
    }            

    public function eliminarMaterial($idMaterial) {
        $stmt = $this->db->prepare("DELETE FROM material WHERE idM = ?");
        $resultado = $stmt->execute([$idMaterial]);
    
        if ($resultado) {
            // Eliminar material exitoso
            echo "<script>alert('Eliminación exitosa'); window.location.href='gestionMaterial.php';</script>";
        } else {
            // Error en la eliminación
            echo "<script>alert('Error al eliminar'); window.location.href='gestionMaterial.php';</script>";
        }
    }  

    public function obtenerMaterialPorId($idMaterial) {
        // Verificar la conexión antes de realizar la consulta
        if ($this->db) {
            // Consulta para obtener el material por ID
            $query = "
                SELECT m.idM,m.categoria,m.titulo,m.descripcion,m.fechaSubida,m.estado,m.URL,
                    CONCAT(u.nombre, ' ', u.apellido) AS autor
                    FROM material m
                    LEFT JOIN usuario u ON m.usuario_idU_M = u.idU
                    WHERE m.idM = ?
            ";
            $stmt = $this->db->prepare($query);
            if ($stmt) {
                // Bind de los parámetros
                $stmt->bind_param("i", $idMaterial); // Suponiendo que el ID es un número entero
                $stmt->execute();
                $resultado = $stmt->get_result();
    
                // Verificar si se encontró algún resultado
                if ($resultado->num_rows === 0) {
                    echo "<script>alert('No se encontró el material con el ID especificado.'); window.location.href='gestionMaterial.php';</script>";
                    return false;
                }
    
                $material = $resultado->fetch_assoc(); // Fetch sin argumentos
                $stmt->close();
    
                // Verificar que el array tenga todas las claves necesarias
                if (!isset($material['autor'])) {
                    echo "<script>alert('El material no contiene información del autor.'); window.location.href='gestionMaterial.php';</script>";
                    return false;
                }
    
                return $material;
            } else {
                echo "<script>alert('Error en la preparación de la consulta.'); window.location.href='gestionMaterial.php';</script>";
                return false;
            }
        } else {
            echo "<script>alert('Error de conexión a la base de datos.'); window.location.href='gestionMaterial.php';</script>";
            return false;
        }
    }    

    public function actualizarMaterial($idM, $categoria, $titulo, $descripcion, $fechaSubida, $URL) {
        // Gestionar la subida del archivo (URL)
        // En la parte donde actualizas el archivo
        if (isset($file) && $file['error'] == 0) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $file['name'];
            $fileDestination = '../../estancia/uploads/' . $fileName; // Ruta interna donde se guarda el archivo

            // Mover el archivo a la carpeta 'uploads'
            if (move_uploaded_file($fileTmpPath, $fileDestination)) {
                $URL = '/estancia/uploads/' . $fileName; // Ruta accesible por la web
            } else {
                echo "<script>alert('Error al subir el archivo'); window.location.href='editarMaterial.php?id=$idM';</script>";
                return;
            }
        }
        
        // Aquí forzamos el estado a "pendiente" al editar el material
        $estado = 'pendiente'; // Cambiar el estado a 'pendiente' al editar
        
        // Preparar la consulta SQL para actualizar los materiales
        $stmt = $this->db->prepare("UPDATE material SET categoria = ?, titulo = ?, descripcion = ?, fechaSubida = ?, URL = ?, estado = ? WHERE idM = ?");
        
        $resultado = $stmt->execute([$categoria, $titulo, $descripcion, $fechaSubida, $URL, $estado, $idM]);
        
        if ($resultado) {
            // Actualización exitosa
            echo "<script>alert('Material actualizado exitosamente.'); window.location.href='gestionMaterial.php';</script>";
        } else {
            // Error al actualizar
            echo "<script>alert('Error al actualizar material.'); window.location.href='editarMaterial.php?id=$idM';</script>";
        }
    }    

    /* Obtener comentarios de los materiales */
    public function obtenerComentarios($idMaterial) {
        // Usar la conexión que ya se ha establecido en el constructor
        $query = "SELECT comentarios FROM aprobarmaterial WHERE idAM = ?";
        $stmt = $this->db->prepare($query); // Usar $this->db aquí en lugar de llamar a conectarBD()
        $stmt->bind_param("i", $idMaterial);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($fila = $resultado->fetch_assoc()) {
            return $fila['comentarios'];
        } else {
            return "Sin comentarios";
        }
    }    
    
    public function obtenerAvisosA($limite = 5) {
        $query = "SELECT titulo, descripcion FROM aviso ORDER BY fecha DESC LIMIT ?";
        $stmt = $this->db->prepare($query);  // Preparar la consulta SQL
        $stmt->bind_param("i", $limite);     // Vincular el parámetro LIMIT como entero
        $stmt->execute();                    // Ejecutar la consulta
        $result = $stmt->get_result();       // Obtener el resultado de la consulta
    
        if ($result === false) {
            echo "Error en la consulta: " . $stmt->error;  // Error si la consulta falla
            return [];  // Devolver un array vacío en caso de error
        }
    
        $avisos = [];
        while ($row = $result->fetch_assoc()) {
            $avisos[] = $row;  // Agregar cada aviso al array
        }
    
        return $avisos;  // Retornar los avisos obtenidos
    }
    
    
    public function obtenerMaterialesAprobados() {
        // Consulta para obtener materiales aprobados junto con el autor (nombre y apellido)
        $sql = "
            SELECT m.idM, m.titulo, m.categoria, CONCAT(u.nombre, ' ', u.apellido) AS autor 
            FROM material m
            JOIN usuario u 
            ON m.usuario_idU_M = u.idU
            WHERE m.estado = 'aprobado'";
    
        $result = $this->db->query($sql);
        
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC); // Retorna los materiales aprobados con el autor (nombre y apellido)
        } else {
            return [];
        }
    }    

    /* Calificar Material */
    public function calificarMaterial($materialId, $calificacion, $comentarios, $usuarioId) {
        // Verificar la conexión antes de realizar la consulta
        if ($this->db) {
            // Verificar si el usuario ya ha calificado este material
            $queryVerificar = "SELECT * FROM Calificacion WHERE materialId = ? AND usuarioId = ?";
            $stmtVerificar = $this->db->prepare($queryVerificar);
            $stmtVerificar->bind_param("ii", $materialId, $usuarioId);
            $stmtVerificar->execute();
            $resultadoVerificacion = $stmtVerificar->get_result();
            
            if ($resultadoVerificacion->num_rows > 0) {
                // Si ya existe una calificación para este material por este usuario, la actualizamos
                $queryActualizar = "UPDATE Calificacion SET calificacion = ?, comentarios = ?, fechaHora = NOW() WHERE materialId = ? AND usuarioId = ?";
                $stmtActualizar = $this->db->prepare($queryActualizar);
                $stmtActualizar->bind_param("isii", $calificacion, $comentarios, $materialId, $usuarioId);
                $resultadoActualizar = $stmtActualizar->execute();
                
                if ($resultadoActualizar) {
                    return "<script>alert('Calificación actualizada con éxito');</script>";
                } else {
                    return "<script>alert('Error al actualizar la calificación');</script>";
                }
            } else {
                // Si no existe una calificación, insertamos una nueva
                // Obtener el siguiente número de calificación
                $query = "SELECT COALESCE(MAX(numeroC), 0) + 1 AS siguienteNumeroC FROM Calificacion WHERE materialId = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("i", $materialId);
                $stmt->execute();
                $stmt->bind_result($siguienteNumeroC);
                $stmt->fetch();
                $stmt->close();
    
                // Insertar nueva calificación
                $queryInsert = "INSERT INTO Calificacion (materialId, numeroC, calificacion, fechaHora, comentarios, usuarioId)
                                VALUES (?, ?, ?, NOW(), ?, ?)";
                $stmtInsert = $this->db->prepare($queryInsert);
                if ($stmtInsert) {
                    // Bind de los parámetros
                    $stmtInsert->bind_param("iiisi", $materialId, $siguienteNumeroC, $calificacion, $comentarios, $usuarioId);
                    $resultadoInsert = $stmtInsert->execute();
    
                    // Verificar si se insertó correctamente
                    if ($resultadoInsert) {
                        return "<script>alert('Calificación registrada con éxito');</script>";
                    } else {
                        return "<script>alert('Error al registrar la calificación');</script>";
                    }
                } else {
                    return "<script>alert('Error en la preparación de la consulta');</script>";
                }
            }
        } else {
            return "<script>alert('Error de conexión a la base de datos');</script>";
        }
    }    
    
    public function obtenerCalificacionPorUsuarioYMaterial($usuarioId, $materialId) {
        $query = "SELECT c.*, m.usuario_idU_M 
                FROM Calificacion c
                JOIN material m ON c.materialId = m.idM
                WHERE m.usuario_idU_M = ? AND c.materialId = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $usuarioId, $materialId); // Se pasan los parámetros
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // Función para obtener la calificación y comentarios de un usuario para un material
    public function obtenerCalificacionYComentarios($materialId, $usuarioId) {
        $sql = "SELECT calificacion, comentarios FROM calificacion WHERE materialId = ? AND usuarioId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $materialId, $usuarioId); // Vinculando los parámetros
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Si se encuentra una calificación previa, la devuelve
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        // Si no hay calificación previa, devuelve null
        return null;
    }

    // Promediar calificacion
    public function obtenerCalificacionPromedio($materialId) {
        $sql = "SELECT AVG(calificacion) AS promedio FROM calificacion WHERE materialId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $materialId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Si se encuentra un resultado, devuelve el promedio, sino 0
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return round($row['promedio'], 1); // Redondear el promedio a 1 decimal
        }

        return 0; // Si no hay calificaciones, devolver 0
    }

    //HISTORIAL ALUMNO
    public function obtenerIdPorNombre($nombreUsuario) {
        $sql = "SELECT idU FROM usuario WHERE usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $nombreUsuario); // Vinculamos el parámetro
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['idU'];
        } else {
            return null;
        }
    }    
    
    public function obtenerHistorialAlumno($usuarioId) {
        $sql = "SELECT 
                u.nombre AS Alumno, 
                u.apellido AS Apellido,
                m.titulo AS Titulo_Material,
                m.categoria AS Categoria,
                m.fechaSubida AS Fecha_Subida,
                m.estado AS Estado_Material,
                c.calificacion AS Calificacion,  -- Columna para la calificación
                c.comentarios AS Comentarios_Calificacion,  -- Columna para los comentarios de la calificación
                am.comentarios AS Comentarios_Aprobacion,  -- Columna para los comentarios de la aprobación
                am.fechaAprobacion AS Fecha_Aprobacion  -- Columna para la fecha de aprobación
            FROM material m
            JOIN usuario u ON u.idU = m.usuario_idU_M
            LEFT JOIN aprobarMaterial am ON am.idAM = m.idM  -- Unir con la tabla aprobarMaterial
            LEFT JOIN Calificacion c ON c.materialId = m.idM  -- Unir con la tabla Calificacion
            WHERE u.idU = ? 
            ORDER BY m.fechaSubida DESC";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $usuarioId); // Vinculamos el parámetro
        $stmt->execute();
        $result = $stmt->get_result();
    
        $historial = [];
        while ($row = $result->fetch_assoc()) {
            $historial[] = $row;
        }
    
        return $historial;
    }
}
?>