USE reposteria;

CREATE TABLE `usuario` (
  `usercod` bigint(10) NOT NULL AUTO_INCREMENT,
  `useremail` varchar(80) DEFAULT NULL,
  `username` varchar(80) DEFAULT NULL,
  `userpswd` varchar(128) DEFAULT NULL,
  `userfching` datetime DEFAULT NULL,
  `userpswdest` char(3) DEFAULT NULL,
  `userpswdexp` datetime DEFAULT NULL,
  `userest` char(3) DEFAULT NULL,
  `useractcod` varchar(128) DEFAULT NULL,
  `userpswdchg` varchar(128) DEFAULT NULL,
  `usertipo` char(3) DEFAULT NULL COMMENT 'Tipo de Usuario, Normal, Consultor o Cliente',
  PRIMARY KEY (`usercod`),
  UNIQUE KEY `useremail_UNIQUE` (`useremail`),
  KEY `usertipo` (`usertipo`,`useremail`,`usercod`,`userest`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `roles` (
  `rolescod` varchar(15) NOT NULL,
  `rolesdsc` varchar(45) DEFAULT NULL,
  `rolesest` char(3) DEFAULT NULL,
  PRIMARY KEY (`rolescod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles_usuarios` (
  `usercod` bigint(10) NOT NULL,
  `rolescod` varchar(15) NOT NULL,
  `roleuserest` char(3) DEFAULT NULL,
  `roleuserfch` datetime DEFAULT NULL,
  `roleuserexp` datetime DEFAULT NULL,
  PRIMARY KEY (`usercod`,`rolescod`),
  KEY `rol_usuario_key_idx` (`rolescod`),
  CONSTRAINT `rol_usuario_key`
    FOREIGN KEY (`rolescod`) REFERENCES `roles` (`rolescod`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `usuario_rol_key`
    FOREIGN KEY (`usercod`) REFERENCES `usuario` (`usercod`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `funciones` (
  `fncod` varchar(255) NOT NULL,
  `fndsc` varchar(45) DEFAULT NULL,
  `fnest` char(3) DEFAULT NULL,
  `fntyp` char(3) DEFAULT NULL,
  PRIMARY KEY (`fncod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `funciones_roles` (
  `rolescod` varchar(15) NOT NULL,
  `fncod` varchar(255) NOT NULL,
  `fnrolest` char(3) DEFAULT NULL,
  `fnexp` datetime DEFAULT NULL,
  PRIMARY KEY (`rolescod`,`fncod`),
  KEY `rol_funcion_key_idx` (`fncod`),
  CONSTRAINT `funcion_rol_key`
    FOREIGN KEY (`rolescod`) REFERENCES `roles` (`rolescod`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `rol_funcion_key`
    FOREIGN KEY (`fncod`) REFERENCES `funciones` (`fncod`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bitacora` (
  `bitacoracod` int(11) NOT NULL AUTO_INCREMENT,
  `bitacorafch` datetime DEFAULT NULL,
  `bitprograma` varchar(255) DEFAULT NULL,
  `bitdescripcion` varchar(255) DEFAULT NULL,
  `bitobservacion` mediumtext,
  `bitTipo` char(3) DEFAULT NULL,
  `bitusuario` bigint(18) DEFAULT NULL,
  PRIMARY KEY (`bitacoracod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO roles (rolescod, rolesdsc, rolesest) VALUES ('ADM', 'Administrador', 'ACT');
INSERT INTO roles (rolescod, rolesdsc, rolesest) VALUES ('CLI', 'Cliente', 'ACT');

INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Mnt\\CatalogoList', 'Detalle de Productos', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Mnt\\CatalogoForm', 'Detalle de Productos', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Funciones\\Funciones', 'Lista de Funciones', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Funciones\\Funcion', 'Detalle de Funciones', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\FuncionesRoles\\FuncionesRoles', 'Lista de FuncionesRoles', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\FuncionesRoles\\FuncionRol', 'Detalle de FuncionesRoles', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Roles\\Roles', 'Lista de Roles', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Roles\\Rol', 'Detalle de Roles', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Usuarios\\Usuarios', 'Lista de Usuarios', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Usuarios\\Usuario', 'Detalle de Roles', 'ACT', 'FNC');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Compras\\ComprasList', 'Controllers\\Compras\\ComprasList', 'ACT', 'CTR');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Compras\\CompraForm', 'Controllers\\Compras\\CompraForm', 'ACT', 'CTR');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Compras\\ProveedoresList', 'Controllers\\Compras\\ProveedoresList', 'ACT', 'CTR');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Compras\\ProveedorForm', 'Controllers\\Compras\\ProveedorForm', 'ACT', 'CTR');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Historial\\TransaccionesList', 'Controllers\\Historial\\TransaccionesList', 'ACT', 'CTR');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\Historial\\TransaccionDetalle', 'Controllers\\Historial\\TransaccionDetalle', 'ACT', 'CTR');
INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES ('Controllers\\RolesUsuarios\\RolesUsuarios', 'Controllers\\RolesUsuarios\\RolesUsuarios', 'ACT', 'CTR');

INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Mnt\\CatalogoList', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Mnt\\CatalogoForm', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Funciones\\Funciones', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Funciones\\Funcion', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\FuncionesRoles\\FuncionesRoles', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\FuncionesRoles\\FuncionRol', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Roles\\Roles', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Roles\\Rol', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Usuarios\\Usuarios', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Usuarios\\Usuario', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Compras\\ComprasList', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Compras\\CompraForm', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Compras\\ProveedoresList', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Compras\\ProveedorForm', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Historial\\TransaccionesList', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\Historial\\TransaccionDetalle', 'ACT', '2027-08-01');
INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES ('ADM', 'Controllers\\RolesUsuarios\\RolesUsuarios', 'ACT', '2027-08-01');

INSERT INTO roles_usuarios (usercod, rolescod, roleuserest, roleuserfch, roleuserexp)
VALUES (1, 'ADM', 'ACT', '2026-08-01', '2027-08-01');

INSERT INTO roles_usuarios (usercod, rolescod, roleuserest, roleuserfch, roleuserexp)
VALUES (2, 'CLI', 'ACT', '2026-08-01', '2027-08-01');

INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp)
VALUES ('CLI', 'Controllers\\Mnt\\CatalogoList', 'ACT', '2031-08-03 20:31:37');