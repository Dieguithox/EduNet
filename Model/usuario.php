<?php
require_once '../Model/conexionBD.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = obtenerConexion(); // Obtener conexión en el constructor
    }

    // Método para verificar si el correo ya está en uso
    public function correoExistente($correo) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuario WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close(); // Cerrar el statement
        return $count > 0; // Retorna true si el correo ya existe
    }

    public function registrar($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE) {
        // Verifica si el correo ya existe
        if ($this->correoExistente($correo)) {
            echo '<script>alert("El correo electrónico ya está registrado");</script>';
            return;
        }

        // Prepara la consulta para insertar datos
        $stmt = $this->db->prepare("INSERT INTO usuario (usuario, nombre, apellido, fecha_nacimiento, correo, contrasena, tipoUsuario, fechaRegistro, programaE_idPE) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");

        if (!$stmt) {
            echo '<script>alert("Error en la preparación de la consulta");</script>';
            return mysqli_error($this->db);
        }

        // Asignar los valores (sin encriptar la contraseña)
        $stmt->bind_param("sssssssi", $usuario, $nombre, $apellido, $fechaNacimiento, $correo, $contrasena, $tipoUsuario, $programaE_idPE);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo '<script>alert("Registro exitoso");</script>';
        } else {
            echo '<script>alert("Error en el registro");</script>';
            return $stmt->error;
        }

        $stmt->close(); // Cerrar el statement después de la ejecución
    }

    public function iniciarSesion($correo, $contrasena) {
        $query = $this->db->prepare("SELECT * FROM usuario WHERE correo = ?");
        $query->bind_param("s", $correo);
        $query->execute();
        $resultado = $query->get_result();
    
        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
    
            if ($contrasena === $usuario['contrasena']) {
                // Guardar el idU, tipo de usuario y correo en la sesión
                $_SESSION['idU'] = $usuario['idU'];  // Guardamos el idU
                $_SESSION['tipoUsuario'] = $usuario['tipoUsuario'];
                $_SESSION['usuario'] = $correo;
    
                // Mostrar mensaje de éxito y luego redirigir según el tipo de usuario
                return "Ingreso exitoso";
            } else {
                // Mostrar mensaje de contraseña incorrecta
                echo "<script>alert('Credenciales incorrectas');</script>";
                return;
            }
        } else {
            // Mostrar mensaje de usuario no encontrado
            echo "<script>alert('Credenciales incorrectas');</script>";
            return;
        }
    
        $query->close(); // Cerrar el statement
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

    public function __destruct() {
        $this->db->close(); // Cerrar la conexión al destruir el objeto
    }
}
?>