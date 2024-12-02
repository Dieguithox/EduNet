DROP DATABASE IF EXISTS eduNet;
CREATE DATABASE IF NOT EXISTS eduNet;
USE eduNet;

/* mysql -u root -p < C:\Users\Pedro\Downloads\eduNet.sql */

/* Tabla: programaEducativo */
CREATE TABLE programaEducativo (
    idPE INT NOT NULL AUTO_INCREMENT COMMENT 'ID unico del programa educativo',
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre del programa educativo (ej. Ingenieria en Sistemas)',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripcion del programa educativo',
    clave VARCHAR(3) NOT NULL COMMENT 'Clave del programa (ej. ITI para Ingenieria en Tecnologias de la Informacion)',
    PRIMARY KEY (idPE)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

/* Tabla: usuario */
CREATE TABLE usuario (
    idU INT AUTO_INCREMENT COMMENT 'ID unico del usuario',
    usuario VARCHAR(50) NOT NULL COMMENT 'Nombre de usuario para inicio de sesion',
    nombre VARCHAR(50) NOT NULL COMMENT 'Nombre del usuario',
    apellido VARCHAR(50) NOT NULL COMMENT 'Apellido del usuario',
    fecha_nacimiento DATE NOT NULL COMMENT 'Fecha de nacimiento del usuario',
    correo VARCHAR(100) NOT NULL UNIQUE COMMENT 'Correo electronico unico del usuario',
    contrasena VARCHAR(255) NOT NULL COMMENT 'Contrasena del usuario, en formato encriptado',
    tipoUsuario ENUM('admin', 'docente', 'alumno') NOT NULL COMMENT 'Tipo de usuario (administrador, docente o alumno)',
    fechaRegistro DATE NOT NULL COMMENT 'Fecha en la que el usuario se registro',

    programaE_idPE INT NULL COMMENT 'ID del programa educativo al que pertenece (si aplica)',
    PRIMARY KEY (idU),
    CONSTRAINT usuario_programaE 
    FOREIGN KEY (programaE_idPE) REFERENCES programaEducativo(idPE) 
    ON DELETE SET NULL
	ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

/* Tabla: avisos */
CREATE TABLE aviso(
    idA INT NOT NULL AUTO_INCREMENT COMMENT 'ID unico del aviso',
    titulo VARCHAR(50) NOT NULL COMMENT 'Titulo del aviso',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripcion detallada del aviso',
    fecha DATE NOT NULL COMMENT 'Fecha en la que se hizo el aviso',

    usuario_idU_A INT NULL COMMENT 'ID del usuario que creo el aviso',
    PRIMARY KEY (idA),
    CONSTRAINT avisos_usuario 
    FOREIGN KEY (usuario_idU_A) REFERENCES usuario(idU) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

/* Tabla: material */
CREATE TABLE material (
    idM INT NOT NULL AUTO_INCREMENT COMMENT 'ID unico del material educativo',
    categoria VARCHAR(50) NOT NULL COMMENT 'Tipo de archivo',
    titulo VARCHAR(50) NOT NULL COMMENT 'Titulo del material',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripcion del material',
    fechaSubida DATE NOT NULL COMMENT 'Fecha en la que se subio el material',
    estado ENUM('pendiente', 'aprobado', 'rechazado') NOT NULL COMMENT 'Estado de aprobacion del material',
    URL VARCHAR(255) NULL COMMENT 'URL del archivo a subir',

    usuario_idU_M INT NULL COMMENT 'ID del usuario que subio el material',
    PRIMARY KEY (idM),
    CONSTRAINT usuario_material 
    FOREIGN KEY (usuario_idU_M) REFERENCES usuario(idU) 
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

/* Tabla: Calificacion */
CREATE TABLE Calificacion (
    materialId INT NOT NULL COMMENT 'ID del material al que se le asigna la calificacion',
    numeroC INT NOT NULL COMMENT 'Numero de la calificacion (para evitar duplicados)',
    calificacion INT NOT NULL COMMENT 'Calificacion numerica otorgada al material',
    fechaHora DATETIME NOT NULL COMMENT 'Fecha y hora de la calificacion',
    comentarios VARCHAR(255) NOT NULL COMMENT 'Comentarios adicionales sobre el material',
    usuarioId INT NOT NULL COMMENT 'ID del usuario que califico',

    PRIMARY KEY (materialId, numeroC),
    CONSTRAINT material_calificacion 
    FOREIGN KEY (materialId) REFERENCES material(idM) 
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

/* Tabla: profesor */
CREATE TABLE profesor (
    idP INT NOT NULL AUTO_INCREMENT COMMENT 'ID unico del profesor',
    usuario_idU_P INT NULL UNIQUE COMMENT 'ID del usuario que corresponde al profesor',
    
    PRIMARY KEY (idP),
    CONSTRAINT profesor_usuario 
    FOREIGN KEY (usuario_idU_P) REFERENCES usuario(idU) 
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

/* Tabla: aprobarMaterial */
CREATE TABLE aprobarMaterial (
    idAM INT NOT NULL AUTO_INCREMENT COMMENT 'ID unico del registro de aprobacion',
    titulo VARCHAR(50) NOT NULL COMMENT 'Titulo del material aprobado',
    comentarios VARCHAR(255) NOT NULL COMMENT 'Comentarios del profesor sobre la aprobacion',
    fechaAprobacion DATETIME NOT NULL COMMENT 'Fecha y hora de la aprobacion',

    profesor_idP_AM INT NULL COMMENT 'ID del profesor que aprobo el material',
    PRIMARY KEY (idAM),
    CONSTRAINT aprobarm_profesor 
    FOREIGN KEY (profesor_idP_AM) REFERENCES profesor(idP) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

/* Agregar automaticamente profesores */
DELIMITER //
CREATE TRIGGER insertarUsuarioDocente
AFTER INSERT ON usuario FOR EACH ROW
BEGIN
    /* Verifica si el tipo de usuario es 'docente' */
    IF NEW.tipoUsuario = 'docente' THEN
        /* Inserta el usuario en la tabla profesor */
        INSERT INTO profesor (usuario_idU_P) VALUES (NEW.idU);
    END IF;
END //
DELIMITER ;

 /* Actualiza la tabla profesor */
DELIMITER //
CREATE TRIGGER actualizarUsuarioDocente
AFTER UPDATE ON usuario FOR EACH ROW
BEGIN
    /* Si el usuario ya no es 'docente' y existe en la tabla profesor */
    IF OLD.tipoUsuario = 'docente' AND NEW.tipoUsuario != 'docente' THEN
        DELETE FROM profesor WHERE usuario_idU_P = OLD.idU;
    /* Si el usuario se convierte en 'docente' y aún no está en la tabla profesor */
    ELSEIF OLD.tipoUsuario != 'docente' AND NEW.tipoUsuario = 'docente' THEN
        INSERT INTO profesor (usuario_idU_P) VALUES (NEW.idU);
    END IF;
END //
DELIMITER ;

/* Elimina de la tabla profesor */
DELIMITER //
CREATE TRIGGER eliminarUsuarioDocente
AFTER DELETE ON usuario FOR EACH ROW
BEGIN
    /* Elimina el registro en la tabla profesor si existe */
    DELETE FROM profesor WHERE usuario_idU_P = OLD.idU;
END //
DELIMITER ;

INSERT INTO programaEducativo (nombre, descripcion, clave) VALUES 
('Ingenieria en Biotecnologia', 'Programa enfocado en el desarrollo de tecnologias biologicas aplicadas en sectores de salud, agricultura e industria.', 'IBT'),
('Ingenieria Ambiental y Sustentabilidad', 'Formacion en la implementacion de soluciones sustentables para problemas ambientales.', 'IAS'),
('Ingenieria en Tecnologia Ambiental', 'Enfoque en tecnologias aplicadas para la proteccion y mejora del medio ambiente.', 'ITA'),
('Ingenieria en Tecnologias de la Informacion', 'Preparacion en el desarrollo y gestion de sistemas de informacion y tecnologia avanzada.', 'ITI'),
('Ingenieria en Tecnologias de la Informacion e Innovacion Digital', 'Formacion en TI con un enfoque en innovacion digital y transformacion tecnologica.', 'IDD'),
('Ingenieria en Electronica y Telecomunicaciones', 'Especializacion en diseno y desarrollo de sistemas electronicos y de telecomunicaciones.', 'IET'),
('Ingenieria en Sistemas Electronicos', 'Programa dedicado al diseno y optimizacion de sistemas electronicos en diversos sectores.', 'ISE'),
('Ingenieria Industrial', 'Enfoque en la optimizacion de procesos productivos y la gestion eficiente de recursos en empresas.', 'IIN'),
('Licenciatura en Administracion y Gestion Empresarial', 'Formacion de profesionistas en administracion y gestion de recursos en organizaciones.', 'LAE'),
('Licenciatura en Administracion', 'Preparacion para la gestion y administracion efectiva de empresas e instituciones.', 'LAD'),
('Ingenieria Financiera', 'Programa especializado en herramientas financieras avanzadas y la administracion de recursos y riesgos.', 'IFI');

/*Agregar ADMIN*/
INSERT INTO usuario (usuario, nombre, apellido, fecha_nacimiento, correo, contrasena, tipoUsuario, fechaRegistro, programaE_idPE)
VALUES ('admin', 'admin', 'admin', '2004-10-15', 'admin@1.com', 'root', 'admin', CURDATE(), NULL);

/* Usuarios Tipo Alumno y Docente */
INSERT INTO usuario (usuario, nombre, apellido, fecha_nacimiento, correo, contrasena, tipoUsuario, fechaRegistro, programaE_idPE) VALUES 
('si', 'si', 'si', '2004-10-15', 'alumno@1.com', 'si', 'alumno', CURDATE(), NULL),
('no', 'no', 'no', '2024-10-30', 'docente@1.com', 'no', 'docente', CURDATE(), NULL);

/* Avisos de prueba */
INSERT INTO aviso (titulo, descripcion, fecha, usuario_idU_A)VALUES 
('Curso de ingles', 'Este 25 de Diciembre curso gratis de Ingles.', CURDATE(), 1);

/* Materiales de prueba */
INSERT INTO material (categoria, titulo, descripcion, fechaSubida, estado, URL, usuario_idU_M) VALUES 
('Link', 'Introduccion a la Programacion', 'Apuntes basicos sobre programacion en Python.', CURDATE(), 'pendiente', 'https://argentinaenpython.com/quiero-aprender-python/aprenda-a-pensar-como-un-programador-con-python.pdf', 2),
('Link', 'Ejercicios de Algebra Lineal', 'Conjunto de problemas resueltos de algebra lineal.', CURDATE(), 'pendiente', 'https://www.escom.ipn.mx/docs/oferta/matDidacticoISC2009/ALnl/Problemario_AlgLineal.pdf', 2);