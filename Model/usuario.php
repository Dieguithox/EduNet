<?php
require_once '../Model/conexionBD.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = obtenerConexion();
    }

    /* Funcion para verificar si el correo ya está existe en la base de datos */
    public function correoExistente($correo) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuario WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    /* Funcion para registrar un usuario nuevo */
    public function registrar($nombre, $apellido, $fechaNacimiento, $correo, $usuario, $contrasena, $tipoUsuario, $programaE_idPE) {
        // Verifica si el correo ya existe
        if ($this->correoExistente($correo)) {
            echo '<script>alert("El correo electrónico ya está registrado");</script>';
            return;
        }

        $stmt = $this->db->prepare("INSERT INTO usuario (usuario, nombre, apellido, fecha_nacimiento, correo, contrasena, tipoUsuario, fechaRegistro, programaE_idPE) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");

        if (!$stmt) {
            echo '<script>alert("Error en la preparación de la consulta");</script>';
            return mysqli_error($this->db);
        }

        // Asignar los valores (posible incriptacion a futuro con sql)
        $stmt->bind_param("sssssssi", $usuario, $nombre, $apellido, $fechaNacimiento, $correo, $contrasena, $tipoUsuario, $programaE_idPE);

        if ($stmt->execute()) {
            echo '<script>alert("Registro exitoso");</script>';
        } else {
            echo '<script>alert("Error en el registro");</script>';
            return $stmt->error;
        }

        $stmt->close();
    }

    /* Funcion para poder iniciar sesion*/
    public function iniciarSesion($correo, $contrasena) {
        $query = $this->db->prepare("SELECT * FROM usuario WHERE correo = ?");
        $query->bind_param("s", $correo);
        $query->execute();
        $resultado = $query->get_result();
    
        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
    
            if ($contrasena === $usuario['contrasena']) {
                // Guardar el idU, tipo de usuario y correo en la sesión
                $_SESSION['idU'] = $usuario['idU'];
                $_SESSION['tipoUsuario'] = $usuario['tipoUsuario'];
                $_SESSION['usuario'] = $correo;
    
                return "Ingreso exitoso";
            } else {
                echo "<script>alert('Credenciales incorrectas');</script>";
                return;
            }
        } else {
            echo "<script>alert('Credenciales incorrectas');</script>";
            return;
        }
    
        $query->close();
    }    

    /* Funcion para obtener programas educativos para el registro */
    public function obtenerProgramasEducativos() {
        $stmt = $this->db->prepare("SELECT idPE, clave FROM programaeducativo");
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $programas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $programas;
        } else {
            return [];
        }
    }

    public function __destruct() {
        $this->db->close();
    }
}
?>