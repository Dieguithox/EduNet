

CREATE TABLE `aprobarmaterial` (
  `idAM` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID unico del registro de aprobacion',
  `titulo` varchar(50) NOT NULL COMMENT 'Titulo del material aprobado',
  `comentarios` varchar(255) NOT NULL COMMENT 'Comentarios del profesor sobre la aprobacion',
  `fechaAprobacion` datetime NOT NULL COMMENT 'Fecha y hora de la aprobacion',
  `profesor_idP_AM` int(11) DEFAULT NULL COMMENT 'ID del profesor que aprobo el material',
  PRIMARY KEY (`idAM`),
  KEY `aprobarm_profesor` (`profesor_idP_AM`),
  CONSTRAINT `aprobarm_profesor` FOREIGN KEY (`profesor_idP_AM`) REFERENCES `profesor` (`idP`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO aprobarmaterial VALUES("1","Introduccion a la Programacion","aprobado, buen trabajo","2024-12-01 06:16:24","1");
INSERT INTO aprobarmaterial VALUES("2","matematicas","falta conclusion ","2024-12-01 06:16:35","1");
INSERT INTO aprobarmaterial VALUES("3","matematicas","s","2024-12-01 07:10:57","1");


CREATE TABLE `aviso` (
  `idA` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID unico del aviso',
  `titulo` varchar(50) NOT NULL COMMENT 'Titulo del aviso',
  `descripcion` varchar(255) NOT NULL COMMENT 'Descripcion detallada del aviso',
  `fecha` date NOT NULL COMMENT 'Fecha en la que se hizo el aviso',
  `usuario_idU_A` int(11) DEFAULT NULL COMMENT 'ID del usuario que creo el aviso',
  PRIMARY KEY (`idA`),
  KEY `avisos_usuario` (`usuario_idU_A`),
  CONSTRAINT `avisos_usuario` FOREIGN KEY (`usuario_idU_A`) REFERENCES `usuario` (`idU`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO aviso VALUES("1","Curso de ingles","Este 25 de Diciembre curso gratis de Ingles.","2024-11-30","1");


CREATE TABLE `calificacion` (
  `materialId` int(11) NOT NULL COMMENT 'ID del material al que se le asigna la calificacion',
  `numeroC` int(11) NOT NULL COMMENT 'Numero de la calificacion (para evitar duplicados)',
  `calificacion` int(11) NOT NULL COMMENT 'Calificacion numerica otorgada al material',
  `fechaHora` datetime NOT NULL COMMENT 'Fecha y hora de la calificacion',
  `comentarios` varchar(255) NOT NULL COMMENT 'Comentarios adicionales sobre el material',
  `usuarioId` int(11) NOT NULL COMMENT 'ID del usuario que califico',
  PRIMARY KEY (`materialId`,`numeroC`),
  CONSTRAINT `material_calificacion` FOREIGN KEY (`materialId`) REFERENCES `material` (`idM`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO calificacion VALUES("1","1","5","2024-12-01 00:17:29","ta bueno","2");
INSERT INTO calificacion VALUES("2","1","1","2024-12-01 00:19:20","","2");


CREATE TABLE `material` (
  `idM` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID unico del material educativo',
  `categoria` varchar(50) NOT NULL COMMENT 'Tipo de archivo',
  `titulo` varchar(50) NOT NULL COMMENT 'Titulo del material',
  `descripcion` varchar(255) NOT NULL COMMENT 'Descripcion del material',
  `fechaSubida` date NOT NULL COMMENT 'Fecha en la que se subio el material',
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL COMMENT 'Estado de aprobacion del material',
  `URL` varchar(255) DEFAULT NULL COMMENT 'URL del archivo a subir',
  `usuario_idU_M` int(11) DEFAULT NULL COMMENT 'ID del usuario que subio el material',
  PRIMARY KEY (`idM`),
  KEY `usuario_material` (`usuario_idU_M`),
  CONSTRAINT `usuario_material` FOREIGN KEY (`usuario_idU_M`) REFERENCES `usuario` (`idU`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO material VALUES("1","Link","Introduccion a la Programacion","Apuntes basicos sobre programacion en Python.","2024-11-30","aprobado","https://argentinaenpython.com/quiero-aprender-python/aprenda-a-pensar-como-un-programador-con-python.pdf","2");
INSERT INTO material VALUES("2","Word","matematicas","descripcion","2024-12-01","pendiente","/estancia/uploads/ejercicios ultimo tema.xlsx","2");


CREATE TABLE `profesor` (
  `idP` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID unico del profesor',
  `usuario_idU_P` int(11) DEFAULT NULL COMMENT 'ID del usuario que corresponde al profesor',
  PRIMARY KEY (`idP`),
  UNIQUE KEY `usuario_idU_P` (`usuario_idU_P`),
  CONSTRAINT `profesor_usuario` FOREIGN KEY (`usuario_idU_P`) REFERENCES `usuario` (`idU`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO profesor VALUES("1","3");


CREATE TABLE `programaeducativo` (
  `idPE` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID unico del programa educativo',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre del programa educativo (ej. Ingenieria en Sistemas)',
  `descripcion` varchar(255) NOT NULL COMMENT 'Descripcion del programa educativo',
  `clave` varchar(3) NOT NULL COMMENT 'Clave del programa (ej. ITI para Ingenieria en Tecnologias de la Informacion)',
  PRIMARY KEY (`idPE`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO programaeducativo VALUES("1","Ingenieria en Biotecnologia","Programa enfocado en el desarrollo de tecnologias biologicas aplicadas en sectores de salud, agricultura e industria.","IBT");
INSERT INTO programaeducativo VALUES("2","Ingenieria Ambiental y Sustentabilidad","Formacion en la implementacion de soluciones sustentables para problemas ambientales.","IAS");
INSERT INTO programaeducativo VALUES("3","Ingenieria en Tecnologia Ambiental","Enfoque en tecnologias aplicadas para la proteccion y mejora del medio ambiente.","ITA");
INSERT INTO programaeducativo VALUES("4","Ingenieria en Tecnologias de la Informacion","Preparacion en el desarrollo y gestion de sistemas de informacion y tecnologia avanzada.","ITI");
INSERT INTO programaeducativo VALUES("5","Ingenieria en Tecnologias de la Informacion e Innovacion Digital","Formacion en TI con un enfoque en innovacion digital y transformacion tecnologica.","IDD");
INSERT INTO programaeducativo VALUES("6","Ingenieria en Electronica y Telecomunicaciones","Especializacion en diseno y desarrollo de sistemas electronicos y de telecomunicaciones.","IET");
INSERT INTO programaeducativo VALUES("7","Ingenieria en Sistemas Electronicos","Programa dedicado al diseno y optimizacion de sistemas electronicos en diversos sectores.","ISE");
INSERT INTO programaeducativo VALUES("8","Ingenieria Industrial","Enfoque en la optimizacion de procesos productivos y la gestion eficiente de recursos en empresas.","IIN");
INSERT INTO programaeducativo VALUES("9","Licenciatura en Administracion y Gestion Empresarial","Formacion de profesionistas en administracion y gestion de recursos en organizaciones.","LAE");
INSERT INTO programaeducativo VALUES("10","Licenciatura en Administracion","Preparacion para la gestion y administracion efectiva de empresas e instituciones.","LAD");
INSERT INTO programaeducativo VALUES("11","Ingenieria Financiera","Programa especializado en herramientas financieras avanzadas y la administracion de recursos y riesgos.","IFI");


CREATE TABLE `usuario` (
  `idU` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID unico del usuario',
  `usuario` varchar(50) NOT NULL COMMENT 'Nombre de usuario para inicio de sesion',
  `nombre` varchar(50) NOT NULL COMMENT 'Nombre del usuario',
  `apellido` varchar(50) NOT NULL COMMENT 'Apellido del usuario',
  `fecha_nacimiento` date NOT NULL COMMENT 'Fecha de nacimiento del usuario',
  `correo` varchar(100) NOT NULL COMMENT 'Correo electronico unico del usuario',
  `contrasena` varchar(255) NOT NULL COMMENT 'Contrasena del usuario, en formato encriptado',
  `tipoUsuario` enum('admin','docente','alumno') NOT NULL COMMENT 'Tipo de usuario (administrador, docente o alumno)',
  `fechaRegistro` date NOT NULL COMMENT 'Fecha en la que el usuario se registro',
  `programaE_idPE` int(11) DEFAULT NULL COMMENT 'ID del programa educativo al que pertenece (si aplica)',
  PRIMARY KEY (`idU`),
  UNIQUE KEY `correo` (`correo`),
  KEY `usuario_programaE` (`programaE_idPE`),
  CONSTRAINT `usuario_programaE` FOREIGN KEY (`programaE_idPE`) REFERENCES `programaeducativo` (`idPE`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO usuario VALUES("1","admin","admin","admin","2004-10-15","admin@1.com","root","admin","2024-11-30","");
INSERT INTO usuario VALUES("2","si","si","si","2004-10-15","alumno@1.com","si","alumno","2024-11-30","");
INSERT INTO usuario VALUES("3","no","no","no","2024-10-30","docente@1.com","no","docente","2024-11-30","");
