-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-08-2011 a las 13:22:13
-- Versión del servidor: 5.1.41
-- Versión de PHP: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `beta-goteo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl`
--

DROP TABLE IF EXISTS `acl`;
CREATE TABLE `acl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` varchar(50) NOT NULL,
  `role_id` varchar(50) DEFAULT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `allow` tinyint(1) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `role_FK` (`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `node_FK` (`node_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1092 ;

--
-- Volcar la base de datos para la tabla `acl`
--

INSERT INTO `acl` VALUES(1, '*', '*', '*', '//', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` VALUES(2, '*', '*', '*', '/image/*', 1, '2011-05-18 23:08:42');
INSERT INTO `acl` VALUES(3, '*', '*', '*', '/tpv/*', 1, '2011-05-31 20:55:42');
INSERT INTO `acl` VALUES(4, '*', '*', '*', '/admin/*', 0, '2011-05-18 16:45:40');
INSERT INTO `acl` VALUES(5, '*', '*', '*', '/project/*', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` VALUES(6, '*', 'superadmin', '*', '/admin/*', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` VALUES(7, '*', '*', '*', '/user/edit/*', 0, '2011-05-18 16:49:36');
INSERT INTO `acl` VALUES(8, '*', '*', '*', '/user/*', 1, '2011-05-18 20:59:54');
INSERT INTO `acl` VALUES(9, '*', '*', '*', 'user/logout', 1, '2011-05-18 21:15:02');
INSERT INTO `acl` VALUES(10, '*', '*', '*', '/search', 1, '2011-05-18 21:16:40');
INSERT INTO `acl` VALUES(11, '*', 'user', '*', '/project/create', 0, '2011-05-18 21:46:44');
INSERT INTO `acl` VALUES(12, '*', 'user', '*', '/dashboard/*', 1, '2011-05-18 21:48:43');
INSERT INTO `acl` VALUES(13, '*', 'public', '*', '/invest/*', 0, '2011-05-18 22:30:23');
INSERT INTO `acl` VALUES(14, '*', 'user', '*', '/message/*', 1, '2011-05-18 22:30:23');
INSERT INTO `acl` VALUES(15, '*', '*', '*', '/user/logout', 1, '2011-05-18 22:33:27');
INSERT INTO `acl` VALUES(16, '*', '*', '*', '/discover/*', 1, '2011-05-18 22:37:00');
INSERT INTO `acl` VALUES(17, '*', '*', '*', '/project/create', 0, '2011-05-18 22:38:22');
INSERT INTO `acl` VALUES(18, '*', '*', '*', '/project/edit/*', 0, '2011-05-18 22:38:22');
INSERT INTO `acl` VALUES(19, '*', '*', '*', '/project/raw/*', 0, '2011-05-18 22:39:37');
INSERT INTO `acl` VALUES(20, '*', 'root', '*', '/project/raw/*', 1, '2011-05-18 22:39:37');
INSERT INTO `acl` VALUES(21, '*', 'superadmin', '*', '/project/edit/*', 1, '2011-05-18 22:43:08');
INSERT INTO `acl` VALUES(22, '*', '*', '*', '/project/delete/*', 0, '2011-05-18 22:43:51');
INSERT INTO `acl` VALUES(23, '*', 'superadmin', '*', '/project/delete/*', 1, '2011-05-18 22:44:37');
INSERT INTO `acl` VALUES(24, '*', '*', '*', '/blog/*', 1, '2011-05-18 22:45:14');
INSERT INTO `acl` VALUES(25, '*', '*', '*', '/faq/*', 1, '2011-05-18 22:49:01');
INSERT INTO `acl` VALUES(26, '*', '*', '*', '/about/*', 1, '2011-05-18 22:49:01');
INSERT INTO `acl` VALUES(27, '*', 'superadmin', '*', '/user/edit/*', 1, '2011-05-18 22:56:56');
INSERT INTO `acl` VALUES(29, '*', 'user', '*', '/user/edit', 1, '2011-05-18 23:56:56');
INSERT INTO `acl` VALUES(30, '*', 'user', '*', '/message/edit/*', 0, '2011-05-19 00:45:29');
INSERT INTO `acl` VALUES(31, '*', 'user', '*', '/message/delete/*', 0, '2011-05-19 00:45:29');
INSERT INTO `acl` VALUES(32, '*', 'superadmin', '*', '/message/edit/*', 1, '2011-05-19 00:56:55');
INSERT INTO `acl` VALUES(33, '*', 'superadmin', '*', '/message/delete/*', 1, '2011-05-19 00:00:00');
INSERT INTO `acl` VALUES(34, '*', 'user', '*', '/invest/*', 1, '2011-05-19 00:56:32');
INSERT INTO `acl` VALUES(35, '*', 'public', '*', '/message/*', 0, '2011-05-19 00:56:32');
INSERT INTO `acl` VALUES(36, '*', 'public', '*', '/user/edit/*', 0, '2011-05-19 01:00:18');
INSERT INTO `acl` VALUES(37, '*', 'superadmin', '*', '/cron/*', 1, '2011-05-27 01:04:02');
INSERT INTO `acl` VALUES(38, '*', '*', '*', '/widget/*', 1, '2011-06-10 11:30:39');
INSERT INTO `acl` VALUES(39, '*', '*', '*', '/user/recover/*', 1, '2011-06-12 22:31:36');
INSERT INTO `acl` VALUES(40, '*', '*', '*', '/news/*', 1, '2011-06-19 13:36:34');
INSERT INTO `acl` VALUES(41, '*', '*', '*', '/community/*', 1, '2011-06-19 13:49:36');
INSERT INTO `acl` VALUES(42, '*', '*', '*', '/ws/*', 1, '2011-06-20 23:18:15');
INSERT INTO `acl` VALUES(43, '*', 'checker', '*', '/review/*', 1, '2011-06-21 17:18:51');
INSERT INTO `acl` VALUES(44, '*', '*', '*', '/contact/*', 1, '2011-06-30 00:24:00');
INSERT INTO `acl` VALUES(45, '*', '*', '*', '/service/*', 1, '2011-07-13 17:26:04');
INSERT INTO `acl` VALUES(46, '*', 'user', '*', '/preview/*', 1, '2011-07-22 16:43:20');
INSERT INTO `acl` VALUES(47, '*', 'translator', '*', '/translate/*', 1, '2011-07-24 12:47:50');
INSERT INTO `acl` VALUES(1004, '*', 'user', 'olivier', '/project/edit/3d72d03458ebd5797cc5fc1c014fc894/', 1, '2011-07-04 19:30:42');
INSERT INTO `acl` VALUES(1005, '*', 'user', 'olivier', '/project/delete/3d72d03458ebd5797cc5fc1c014fc894/', 1, '2011-07-04 19:30:42');
INSERT INTO `acl` VALUES(1006, '*', 'user', 'olivier', '/project/edit/2c667d6a62707f369bad654174116a1e/', 1, '2011-07-04 19:30:49');
INSERT INTO `acl` VALUES(1007, '*', 'user', 'olivier', '/project/delete/2c667d6a62707f369bad654174116a1e/', 1, '2011-07-04 19:30:49');
INSERT INTO `acl` VALUES(1010, '*', 'user', 'tintangibles', '/project/edit/hkp/', 1, '2011-07-05 00:23:32');
INSERT INTO `acl` VALUES(1011, '*', 'user', 'tintangibles', '/project/delete/hkp/', 1, '2011-07-05 00:23:32');
INSERT INTO `acl` VALUES(1036, '*', 'user', 'diegobus', '/project/edit/fe99373e968b0005e5c2406bc41a3528/', 1, '2011-07-12 11:28:02');
INSERT INTO `acl` VALUES(1037, '*', 'user', 'diegobus', '/project/delete/fe99373e968b0005e5c2406bc41a3528/', 1, '2011-07-12 11:28:02');
INSERT INTO `acl` VALUES(1038, '*', 'user', 'diegobus', '/project/edit/96ce377a34e515ab748a096093187d53/', 1, '2011-07-12 11:29:25');
INSERT INTO `acl` VALUES(1039, '*', 'user', 'diegobus', '/project/delete/96ce377a34e515ab748a096093187d53/', 1, '2011-07-12 11:29:25');
INSERT INTO `acl` VALUES(1054, '*', 'user', 'susana', '/project/edit/ae02e5caf5a19567007c6cb06a8c9d95/', 1, '2011-07-15 16:14:51');
INSERT INTO `acl` VALUES(1055, '*', 'user', 'susana', '/project/delete/ae02e5caf5a19567007c6cb06a8c9d95/', 1, '2011-07-15 16:14:51');
INSERT INTO `acl` VALUES(1056, '*', 'user', 'olivier', '/project/edit/8c069c398c3e3114e681ccfafa4a3fe0/', 1, '2011-07-15 19:04:21');
INSERT INTO `acl` VALUES(1057, '*', 'user', 'olivier', '/project/delete/8c069c398c3e3114e681ccfafa4a3fe0/', 1, '2011-07-15 19:04:21');
INSERT INTO `acl` VALUES(1064, '*', 'user', 'kaime', '/project/edit/f1dd9c1678c62273e21b67bff6e8b47a/', 1, '2011-07-20 18:52:26');
INSERT INTO `acl` VALUES(1065, '*', 'user', 'kaime', '/project/delete/f1dd9c1678c62273e21b67bff6e8b47a/', 1, '2011-07-20 18:52:26');
INSERT INTO `acl` VALUES(1066, '*', 'user', 'root', '/project/edit/8851739335520c5eeea01cd745d0442d/', 1, '2011-07-21 10:33:28');
INSERT INTO `acl` VALUES(1067, '*', 'user', 'root', '/project/delete/8851739335520c5eeea01cd745d0442d/', 1, '2011-07-21 10:33:28');
INSERT INTO `acl` VALUES(1068, '*', 'user', 'root', '/project/edit/984990664ca1a1a98522b2640b0fc535/', 1, '2011-07-21 11:12:18');
INSERT INTO `acl` VALUES(1069, '*', 'user', 'root', '/project/delete/984990664ca1a1a98522b2640b0fc535/', 1, '2011-07-21 11:12:18');
INSERT INTO `acl` VALUES(1072, '*', 'user', 'root', '/project/edit/e4ae82c6a3497c04d2338fe63961c92c/', 1, '2011-07-25 16:43:05');
INSERT INTO `acl` VALUES(1073, '*', 'user', 'root', '/project/delete/e4ae82c6a3497c04d2338fe63961c92c/', 1, '2011-07-25 16:43:05');
INSERT INTO `acl` VALUES(1076, '*', 'user', 'demo', '/project/edit/tweetometro/', 1, '2011-07-26 11:27:37');
INSERT INTO `acl` VALUES(1077, '*', 'user', 'demo', '/project/delete/tweetometro/', 1, '2011-07-26 11:27:37');
INSERT INTO `acl` VALUES(1084, '*', 'user', 'demo', '/project/edit/tutorial-goteo/', 1, '2011-07-27 19:28:28');
INSERT INTO `acl` VALUES(1085, '*', 'user', 'demo', '/project/delete/tutorial-goteo/', 1, '2011-07-27 19:28:28');
INSERT INTO `acl` VALUES(1086, '*', 'user', 'acomunes', '/project/edit/move-commons/', 1, '2011-07-28 11:28:33');
INSERT INTO `acl` VALUES(1087, '*', 'user', 'acomunes', '/project/delete/move-commons/', 1, '2011-07-28 11:28:33');
INSERT INTO `acl` VALUES(1088, '*', 'user', 'ivan', '/project/edit/f63dab04c0b63cb02d4d1a85aa738ee3/', 1, '2011-07-31 12:48:23');
INSERT INTO `acl` VALUES(1089, '*', 'user', 'ivan', '/project/delete/f63dab04c0b63cb02d4d1a85aa738ee3/', 1, '2011-07-31 12:48:23');
INSERT INTO `acl` VALUES(1090, '*', 'user', 'fbalague', '/project/edit/9925d72d4b0a4b0733abaeaa3e187581/', 1, '2011-08-02 09:40:00');
INSERT INTO `acl` VALUES(1091, '*', 'user', 'fbalague', '/project/delete/9925d72d4b0a4b0733abaeaa3e187581/', 1, '2011-08-02 09:40:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blog`
--

DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `owner` varchar(50) NOT NULL COMMENT 'la id del proyecto o nodo',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Blogs de nodo o proyecto' AUTO_INCREMENT=7 ;

--
-- Volcar la base de datos para la tabla `blog`
--

INSERT INTO `blog` VALUES(1, 'node', 'goteo', 1);
INSERT INTO `blog` VALUES(5, 'project', 'goteo', 1);
INSERT INTO `blog` VALUES(6, 'project', '2c667d6a62707f369bad654174116a1e', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campaign`
--

DROP TABLE IF EXISTS `campaign`;
CREATE TABLE `campaign` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `campaign`
--

INSERT INTO `campaign` VALUES(1, 'Campaña GIJ', 'Gabinete de iniciativa Joven');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `description` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos' AUTO_INCREMENT=15 ;

--
-- Volcar la base de datos para la tabla `category`
--

INSERT INTO `category` VALUES(2, 'Social', 'Proyectos que promueven el cambio social, la resolución de problemas en las relaciones humanas y el fortalecimiento del pueblo para conseguir un mayor bienestar.', 1);
INSERT INTO `category` VALUES(6, 'Comunicativo', 'Proyectos con el objetivo de informar, denunciar, comunicar... Estarían en este bloque el periodismo ciudadano, documentales, blogs, programas de radio...', 1);
INSERT INTO `category` VALUES(7, 'Tecnológico', 'Software, hardware, herramientas... ', 2);
INSERT INTO `category` VALUES(9, 'Comercial', 'Proyectos que aspiran claramente a convertirse en una empresa. ', 1);
INSERT INTO `category` VALUES(10, 'Educátivo', 'Proyectos donde el objetivo primordial es la educación o la formación a otros. ', 1);
INSERT INTO `category` VALUES(11, 'Cultural', 'Proyectos con objetivos artísticos, culturales... ', 1);
INSERT INTO `category` VALUES(13, 'Ecológico', 'Proyectos relacionados con el cuidado del medio ambiente.\r\n', 7);
INSERT INTO `category` VALUES(14, 'Científico', 'Estudios de alguna materia, proyectos que buscan respuestas, soluciones, explicaciones nuevas.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category_lang`
--

DROP TABLE IF EXISTS `category_lang`;
CREATE TABLE `category_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `category_lang`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post` bigint(20) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NOT NULL,
  `user` varchar(50) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Comentarios' AUTO_INCREMENT=13 ;

--
-- Volcar la base de datos para la tabla `comment`
--

INSERT INTO `comment` VALUES(12, 16, '2011-07-08 16:49:33', 'aqui debeíra haber una galería para poder publicar varias fotos ya que si quiero dar cuenta del proceso de construción de un objeto por ejemplo, necesitare publicar varias imagenes', 'olivier');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cost`
--

DROP TABLE IF EXISTS `cost`;
CREATE TABLE `cost` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `cost` varchar(256) DEFAULT NULL,
  `description` tinytext,
  `type` varchar(50) DEFAULT NULL,
  `amount` int(5) DEFAULT '0',
  `required` tinyint(1) DEFAULT '0',
  `from` date DEFAULT NULL,
  `until` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Desglose de costes de proyectos' AUTO_INCREMENT=189 ;

--
-- Volcar la base de datos para la tabla `cost`
--

INSERT INTO `cost` VALUES(40, 'a565092b772c29abc1b92f999af2f2fb', 'Diseño de interface', 'Honorarios para el diseñador web', 'task', 1200, 0, '2011-07-04', '2011-08-07');
INSERT INTO `cost` VALUES(41, 'a565092b772c29abc1b92f999af2f2fb', 'Programación del nuevo administrador y maquetación CSS', 'Honorarios para el programador.', 'task', 3500, 1, '2011-06-20', '2011-08-31');
INSERT INTO `cost` VALUES(42, 'a565092b772c29abc1b92f999af2f2fb', 'Coordinación y análisis de la estructura', '', 'task', 2500, 1, '2011-06-01', '2011-09-11');
INSERT INTO `cost` VALUES(43, 'a565092b772c29abc1b92f999af2f2fb', 'Difusión de la herramienta', 'Honorarios de un community manager', 'task', 1000, NULL, '2011-08-29', '2011-10-09');
INSERT INTO `cost` VALUES(63, 'fe99373e968b0005e5c2406bc41a3528', 'Nuevo coste', 'Descripción', 'task', 400, 1, '2011-07-21', '2011-08-05');
INSERT INTO `cost` VALUES(65, 'fe99373e968b0005e5c2406bc41a3528', 'Nuevo coste', 'Descripción', 'task', 300, 0, '2011-08-05', '2011-08-19');
INSERT INTO `cost` VALUES(66, '2c667d6a62707f369bad654174116a1e', 'comprar semillas', 'comprar semillas', 'structure', 50, 1, '2011-05-12', '2011-05-19');
INSERT INTO `cost` VALUES(67, '2c667d6a62707f369bad654174116a1e', 'diseño web', 'diseño y colorines', 'task', 2000, 0, '2011-05-12', '2011-05-19');
INSERT INTO `cost` VALUES(75, 'a565092b772c29abc1b92f999af2f2fb', 'Publicar el código bajo licencia libre', 'Honorarios destinados a documentar el código, empaquetarlo bien y publicarlo bajo una licencia libre', 'task', 1000, NULL, '2011-09-26', '2011-11-06');
INSERT INTO `cost` VALUES(80, 'pliegos', 'Nueva tarea', 'Necesito pilas', 'structure', 600, 0, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(87, '2c667d6a62707f369bad654174116a1e', 'Compra ordenadores', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat fermentum risus in pulvinar. Nunc a pretium lorem. Morbi semper orci ac nisl mattis accumsan. Integer a metus lacus. Aenean sagittis mattis mi, id pulvinar lectus sagittis vitae. Etiam', 'material', 2000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(90, 'todojunto-letterpress', 'Materiales', 'Busqueda y compra de materiales (tiporafías, tintas, componedores, etc)', 'material', 800, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(91, 'todojunto-letterpress', 'Diseño', 'Diseño del manual/fanzine', 'task', 240, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(92, 'todojunto-letterpress', 'Manual', 'Producción del manual-fanzine (500 copias)', 'task', 600, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(93, 'todojunto-letterpress', 'Tratamiento materiales', 'Limpieza y organización del material adquirido', 'task', 300, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(94, 'oh-oh-fase-2', 'Hardware robot', 'Desarrollo nueva iteracion del hardware para el robot', 'task', 2500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(95, 'oh-oh-fase-2', 'Software robot', 'Desarrollo libreria de software para control del robot', 'task', 1500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(96, 'oh-oh-fase-2', 'Ejemplos', 'Creacion ejemplos para la libreria', 'task', 1000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(97, 'oh-oh-fase-2', 'Plantilla documentación', 'Creacion de un template de documentacion para publicar los ejemplos', 'task', 1000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(99, 'urban-social-design-database', 'web', 'modificar web archtlas.com', 'task', 300, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(100, 'urban-social-design-database', 'community manager', 'community manager', 'task', 1500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(101, 'urban-social-design-database', 'programación widget', 'programación widget', 'task', 500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(102, 'urban-social-design-database', 'gestión y administración', 'gestión y administración', 'task', 1500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(103, 'urban-social-design-database', 'mantenimiento', 'mantenimiento', 'task', 700, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(104, 'urban-social-design-database', 'diseño grafico y/o audiovisual', 'diseño grafico y/o audiovisual', 'task', 500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(105, 'archinhand-architecture-in-your-hand', 'Programación', 'Enlace de modulos, Desarrollo interfaz usuario para cargar contenidos, sistema', 'task', 2100, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(106, 'archinhand-architecture-in-your-hand', 'Diseño', 'Pulir interfaz de la herramienta, testeo usabilidad, material grafico y audiovisual de comunicacion del proyecto', 'task', 1700, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(107, 'archinhand-architecture-in-your-hand', 'Gestión de contenidos', 'Permisos y licencias de uso, comisariado de contenidos fase beta', 'task', 1500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(108, 'archinhand-architecture-in-your-hand', 'Plan de empresa', 'Plan de empresa', 'task', 600, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(109, 'mi-barrio', 'Creación web proyecto', 'Creación web proyecto', 'task', 6000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(110, 'mi-barrio', 'Lanzamiento', 'Lanzamiento y gestión convocatoria (selección participantes)', 'task', 3000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(111, 'mi-barrio', 'Talleres', 'Diseño y realización Talleres de formación', 'task', 5000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(112, 'mi-barrio', 'Grabanción', 'Grabaciones (labor de acompañamiento a los participantes)', 'task', 3000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(113, 'mi-barrio', 'Edición', 'Edición material grabado', 'task', 5000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(114, 'mi-barrio', 'Online', 'Alojamineto web y difusión online', 'task', 3000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(115, 'hkp', 'Guión pieza a/v', 'Guión pieza a/v', 'task', 600, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(116, 'hkp', 'Edición pieza a/v', 'Edición pieza a/v', 'task', 1800, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(117, 'hkp', 'Post-producción pieza a/v', 'Post-producción pieza a/v', 'task', 450, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(118, 'hkp', 'Estampación DVD (1500)', 'Estampación DVD (1500)', 'task', 1200, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(119, 'hkp', 'Entrevistas y nuevos registros de vídeo', 'Entrevistas y nuevos registros de vídeo', 'task', 1000, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(120, 'hkp', 'Edición nuevas cápsulas', 'Edición nuevas cápsulas', 'task', 800, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(121, 'hkp', 'Compilación y redactado textos libro', 'Compilación y redactado textos libro', 'task', 800, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(122, 'hkp', 'Ilustraciones', 'Ilustraciones', 'task', 500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(123, 'hkp', 'Traducciones libro bilingüe castellano/catalán', 'Traducciones libro bilingüe castellano/catalán', 'task', 2500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(124, 'hkp', 'Diseño y maquetación libro', 'Diseño y maquetación libro', 'task', 1300, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(125, 'hkp', 'Imprenta (tiraje 1500)', 'Imprenta (tiraje 1500)', 'task', 6500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(126, 'hkp', 'Manipulación y retractilado pack libro+DVD', 'Manipulación y retractilado pack libro+DVD', 'task', 500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(127, 'hkp', 'Envíos distribución pack', 'Envíos distribución pack', 'task', 400, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(128, 'hkp', 'Mantenimiento y mejoras wiki', 'Mantenimiento y mejoras wiki', 'task', 600, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(129, 'hkp', 'Tareas administrativas y de gestión', 'Tareas administrativas y de gestión', 'task', 500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(130, 'move-commons', 'Plataforma', 'Continuar desarrollo de la plataforma MC (con subcategorías), maximizando usabilidad y con versión accesible', 'task', 3600, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(131, 'move-commons', 'Motor de búsquedas', 'Desarrollo del motor de búsquedas semánticas para MC', 'task', 2400, 0, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(132, 'move-commons', 'Diseño', 'Diseño de iconos, explicaciones gráficas, material visual explicativo', 'task', 1200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(133, 'move-commons', 'Redacción', 'Redacción (en castellano e inglés) de textos divulgativos y HOWTOs documentados para cada categoría', 'task', 1200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(134, 'nodo-movil', 'Material', 'compra de material para 1 Nodo Móvil', 'material', 350, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(135, 'nodo-movil', 'construcción', 'construcción del Nodo Móvil', 'task', 200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(136, 'nodo-movil', 'configuración', 'configuración del firmware', 'task', 200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(137, 'nodo-movil', 'Testing', 'pruebas testing 2 Nodos Móviles', 'task', 10, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(138, 'canal-alfa', 'Creación plataforma web', 'Creación plataforma web', 'task', 3200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(139, 'robocicla', 'Guión', 'Linea editorial, guion e History Board', 'task', 1000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(140, 'robocicla', 'Ilustraciones', 'Ilustraciones', 'task', 400, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(141, 'robocicla', 'Diseño', 'Diseño gráfico y maquetación', 'task', 400, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(142, 'robocicla', 'Diseño packaging', 'Diseño packaging', 'task', 100, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(143, 'robocicla', 'Espacio Digital', 'Espacio Digital', 'task', 100, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(146, '3d72d03458ebd5797cc5fc1c014fc894', 'Nueva tarea', '', 'task', 300, 0, '2011-07-11', '2011-07-11');
INSERT INTO `cost` VALUES(151, '96ce377a34e515ab748a096093187d53', 'Impresión en rotativa', 'Impresión de 3000 unidades de la 2da. edición de DINOU', 'task', 600, 1, '2011-07-20', '2011-07-22');
INSERT INTO `cost` VALUES(152, '96ce377a34e515ab748a096093187d53', 'Transporte', 'Transporte de los periódicos a los diferentes puntos de distribución.', 'task', 60, 0, '2011-07-23', '2011-07-23');
INSERT INTO `cost` VALUES(153, '96ce377a34e515ab748a096093187d53', 'Envio por correo', 'Envio a diferentes paises donde será distribuido sin costo', 'task', 120, 1, '2011-07-28', '2011-07-28');
INSERT INTO `cost` VALUES(158, 'pliegos', 'Nueva tarea', '', 'task', 0, 1, '2011-07-18', '2011-07-18');
INSERT INTO `cost` VALUES(161, '8c069c398c3e3114e681ccfafa4a3fe0', 'Nueva tarea', '', 'task', 0, 0, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(162, '8c069c398c3e3114e681ccfafa4a3fe0', 'Nueva tarea', '', 'structure', 1000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(173, 'f1dd9c1678c62273e21b67bff6e8b47a', 'Nueva tarea', 'Lorem ipsum', 'task', 1250, 1, '2011-08-20', '2011-09-22');
INSERT INTO `cost` VALUES(174, 'f1dd9c1678c62273e21b67bff6e8b47a', 'Nueva tarea 2', 'Dolor sit', 'material', 354, 0, '2011-08-30', '2011-10-13');
INSERT INTO `cost` VALUES(175, 'f1dd9c1678c62273e21b67bff6e8b47a', 'Nueva tarea', 'Conseq', 'structure', 400, 0, '2011-09-23', '2011-10-03');
INSERT INTO `cost` VALUES(176, 'f1dd9c1678c62273e21b67bff6e8b47a', 'Nueva tarea Más', 'Lorem Ipsum', 'task', 900, 1, '2011-09-12', '2011-09-18');
INSERT INTO `cost` VALUES(177, '8851739335520c5eeea01cd745d0442d', 'Nueva tarea', '', 'task', 0, 0, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(178, '8851739335520c5eeea01cd745d0442d', 'Nueva tarea', '', 'material', 0, 0, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(179, 'tweetometro', 'Diseño de interface ', 'Honorarios para el diseñador web', 'task', 1000, 0, '2011-10-22', '2011-11-05');
INSERT INTO `cost` VALUES(180, 'tweetometro', 'Programación del nuevo administrador y maquetación CSS ', 'Honorarios para el programador', 'task', 4100, 1, '2011-10-15', '2011-12-05');
INSERT INTO `cost` VALUES(181, 'tweetometro', 'Coordinación y análisis de la estructura ', 'Definición requerimientos, calendario, seguimiento, pruebas, etc.', 'task', 3000, 1, '2011-09-15', '2011-12-15');
INSERT INTO `cost` VALUES(182, 'tweetometro', 'Difusión de la herramienta ', 'Honorarios de difusión y community management', 'task', 1000, 0, '2011-12-07', '2012-01-07');
INSERT INTO `cost` VALUES(183, 'tweetometro', 'Publicar el código bajo licencia libre ', 'Honorarios destinados a documentar el código, empaquetarlo bien y publicarlo bajo una licencia libre', 'task', 800, 0, '2012-01-07', '2012-02-07');
INSERT INTO `cost` VALUES(184, 'goteo', 'Nueva tarea', '', 'task', 0, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(185, 'tutorial-goteo', 'Coste asociado a una tarea', 'Tareas donde invertir conocimientos y/o habilidades para desarrollar alguna parte del proyecto (desarrollo, diseño, coordinación, facilitación, etc). En este ejemplo sería un coste adicional para llevarlo a cabo.', 'task', 5000, 1, '2011-11-01', '2011-12-30');
INSERT INTO `cost` VALUES(186, 'tutorial-goteo', 'Coste asociado a una infraestructura', 'También podemos considerar y especificar una inversión en costes vinculada a un local, medio de transporte u otras infraestructuras básicas para llevar a cabo el proyecto. En este ejemplo sería un coste adicional para llevar a cabo el proyecto', 'structure', 1000, 0, '2011-11-01', '2011-11-15');
INSERT INTO `cost` VALUES(187, 'tutorial-goteo', 'Coste asociado a material', 'También podemos solicitar en préstamo o cesión materiales necesarios para el proyecto como herramientas, papelería, equipos informáticos, etc. En este ejemplo sería un coste adicional para llevar a cabo el proyecto.', 'material', 1000, 0, '2011-11-01', '2011-11-10');
INSERT INTO `cost` VALUES(188, 'f63dab04c0b63cb02d4d1a85aa738ee3', 'Nueva tarea', '', 'task', 0, 1, '2011-07-31', '2011-08-05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `criteria`
--

DROP TABLE IF EXISTS `criteria`;
CREATE TABLE `criteria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL DEFAULT 'node',
  `title` tinytext,
  `description` text,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Criterios de puntuación' AUTO_INCREMENT=28 ;

--
-- Volcar la base de datos para la tabla `criteria`
--

INSERT INTO `criteria` VALUES(5, 'project', 'Es original', 'donde va esta descripción? donde esta el tool tip?\r\n\r\nHola, este tooltip ira en el formulario de revision', 1);
INSERT INTO `criteria` VALUES(6, 'project', 'Es eficaz en su estrategia de comunicación', '', 2);
INSERT INTO `criteria` VALUES(7, 'project', 'Aporta información suficiente del proyecto', '', 3);
INSERT INTO `criteria` VALUES(8, 'project', 'Aporta productos, servicios o valores “deseables” para la comunidad', '', 4);
INSERT INTO `criteria` VALUES(9, 'project', 'Es afín a la cultura abierta', '', 5);
INSERT INTO `criteria` VALUES(10, 'project', 'Puede crecer, es escalable', '', 6);
INSERT INTO `criteria` VALUES(11, 'project', 'Son coherentes los recursos solicitados con los objetivos y el tiempo de desarrollo', '', 7);
INSERT INTO `criteria` VALUES(12, 'project', 'Riesgo proporcional al grado de benficios (sociales, culturales y/o económicos)', '', 8);
INSERT INTO `criteria` VALUES(13, 'owner', 'Posee buena reputación en su sector', '', 1);
INSERT INTO `criteria` VALUES(14, 'owner', 'Ha trabajado con organizaciones y colectivos con buena reputación', '', 2);
INSERT INTO `criteria` VALUES(15, 'owner', 'Aporta información sobre experiencias anteriores (éxitos y fracasos)', '', 3);
INSERT INTO `criteria` VALUES(16, 'owner', 'Tiene capacidades para llevar a cabo el proyecto', '', 4);
INSERT INTO `criteria` VALUES(17, 'owner', 'Cuenta con un equipo formado', '', 5);
INSERT INTO `criteria` VALUES(18, 'owner', 'Cuenta con una comunidad de seguidores', '', 6);
INSERT INTO `criteria` VALUES(19, 'owner', 'Tiene visibilidad en la red', '', 7);
INSERT INTO `criteria` VALUES(20, 'reward', 'Es viable (su coste está incluido en la producción del proyecto)', '', 1);
INSERT INTO `criteria` VALUES(21, 'reward', 'Puede tener efectos positivos, transformadores (sociales, culturales, empresariales)', '', 2);
INSERT INTO `criteria` VALUES(22, 'reward', 'Aporta conocimiento nuevo, de difícil acceso o en proceso de desaparecer', '', 3);
INSERT INTO `criteria` VALUES(23, 'reward', 'Aporta oportunidades de generar economía alrededor', '', 4);
INSERT INTO `criteria` VALUES(24, 'reward', 'Da libertad en el uso de sus resultados (es reproductible)', '', 5);
INSERT INTO `criteria` VALUES(25, 'reward', 'Ofrece un retorno atractivo (por original, por útil, por inspirador... )', '', 6);
INSERT INTO `criteria` VALUES(26, 'reward', 'Cuenta con actualizaciones', '', 7);
INSERT INTO `criteria` VALUES(27, 'reward', 'Integra a la comunidad (a los seguidores, cofinanciadores, a un grupo social)', '', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faq`
--

DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL DEFAULT 'node',
  `title` tinytext,
  `description` text,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Preguntas frecuentes' AUTO_INCREMENT=69 ;

--
-- Volcar la base de datos para la tabla `faq`
--

INSERT INTO `faq` VALUES(2, 'goteo', 'investors', '¿Qué sistemas de pago ofrece Goteo? ¿Son seguros?', 'Tienes dos opciones para hacer efectiva tu aportación al proyecto que quieres apoyar: mediante el sistema de pago por Internet Paypal o bien mediante tarjeta de crédito, utilizando una pasarela de pago de Caja Laboral, del Grupo Mondragon.  Ambas formas de pago son fáciles, rápidas de utilizar y totalmente seguras, usadas a diario por millones de usuarios en todo el mundo, y poseen todo tipo de medidas de seguridad para evitar robos de claves o suplantaciones de identidad. En ese sentido, Goteo tampoco tiene acceso a tus datos bancarios en ningún momento del proceso. ', 12);
INSERT INTO `faq` VALUES(3, 'goteo', 'node', '¿Qué tipo de proyectos caben aquí y cómo se valoran?', 'Goteo es una plataforma en la que se pueden proponer proyectos de muy diverso tipo. Los proyectos se organizan a través de secciones temáticas (ecología, tecnología, economía alternativa, arquitectura, ciencia, diseño, formación, emprendizaje, cultura libre, etc.), como una forma de generar sinergias, sumar conocimientos y atraer patrocinios y campañas específicas. Esas secciones temáticas cuentan con la complicidad, compromiso y asesoramiento de agentes expertos; personas que hacen las funciones de prescriptores, asesores e informadores y que conforman la red de conocimiento de Goteo.\r\n\r\nEn Goteo se priman las iniciativas que generan un beneficio social y que ayudan a construir comunidad a su alrededor. Buscamos proyectos con ADN abierto: sociales, culturales, educativos, tecnológicos que contribuyan al fortalecimiento de los bienes colectivos para el mayor número posible de personas.    ', 2);
INSERT INTO `faq` VALUES(4, 'goteo', 'investors', '¿Qué es el retorno colectivo de la inversión?', 'Goteo persigue especialmente la rentabilidad social de las inversiones efectuadas a través de la plataforma, buscando la viabilidad de los proyectos para sus impulsores pero también el retorno colectivo para la comunidad (en paralelo a contraprestaciones individuales, que varían en función del proyecto y de las cantidades aportadas).\r\n\r\nEstos retornos colectivos pueden ser de muy diverso tipo, y se fundamentan en compartir conocimiento y dar acceso libre al código fuente, a productos o recursos, a formación a través de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios económicamente sostenibles.', 8);
INSERT INTO `faq` VALUES(5, 'goteo', 'node', '¿Cuáles son los aspectos diferenciales de esta plataforma?', 'Goteo se distingue principalmente de otras plataformas por su apuesta diferencial y focalizada en proyectos de “código abierto” en un sentido amplio. Esto es, que compartan conocimiento, procesos, resultado, responsabilidad o beneficio, desde la filosofía del procomún. Porque Goteo pone el acento en la esfera pública, apoyando proyectos que favorezcan el empoderamiento colectivo y el bien común.\r\n\r\nEs crowdfunding para gente que busque la rentabilidad social de las inversiones efectuadas, haciendo viables los proyectos para sus promotores pero también el retorno colectivo para la comunidad (en paralelo a contraprestaciones individuales). Estos retornos colectivos pueden ser de muy diverso tipo y se fundamentan en compartir conocimiento y dar acceso libre al código fuente, a productos y recursos, a formación a través de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios económicamente sostenibles.\r\n\r\nOtra peculiaridad de Goteo es que permite recaudar fondos mínimos, por un lado, para arrancar los proyectos, y por otro fondos óptimos para fases posteriores, tal como explicamos un par de FAQs más arriba. También el hecho de que pueda funcionar y ser administrada de manera autónoma por nodos temáticos o geográficos distribuidos geográficamente, como detallamos otras FAQ más abajo. O que permita inversiones puntuales de entidades que deseen generar así una especie de bote, a repartir según el criterio de una comunidad de expertos entre proyectos de un nodo determinado.\r\n\r\n¿Más diferencias con otras plataformas? Ésta se aplica su propia receta y también está disponible con su propia licencia copyleft, así como su código fuente (para quien prefiera descargarse la herramienta y adaptarla a sus necesidades de crowdfunding). También estamos trabajando en otras especificaciones, como un panel de seguimiento detallado de fondos obtenidos, para aumentar la transparencia sobre dónde acaba lo recaudado, o una tarifa plana para micromecenas compulsivos. \r\n\r\nEstos retornos colectivos pueden ser de muy diverso tipo y se fundamentan en compartir conocimiento y dar acceso libre al código fuente, a productos y recursos, a formación a través de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios económicamente sostenibles.', 5);
INSERT INTO `faq` VALUES(6, 'goteo', 'node', '¿Por qué hay que elegir entre licencias abiertas?', 'Para asegurar el retorno colectivo, los proyectos que quieran financiarse y desarrollarse con la ayuda de Goteo deben pensarse, producirse y/o distribuirse desde la ética de lo libre y abierto.\r\nDeben acogerse a alguna de las numerosas licencias existentes, con diferentes especificaciones y/o restricciones, que permiten explícitamente la copia, distribución y/o modificación de parte o de la totalidad de cada proyecto. Desde Goteo se propone y se asesora sobre una serie de licencias como son Creative Commons (CC), General Public Licence (GPL), GNU Affero General Public Licence (AGPLv3), Open Database (ODBC), Licencia Red Abierta Libre Neutral (XOLN) o Open Hardware Licence (OHL).', 7);
INSERT INTO `faq` VALUES(7, 'goteo', 'node', '¿Qué ofrece Goteo a los miembros de la comunidad?', 'Goteo parte de los modelos actuales del crowdfunding, para articularse como una red social de agentes de ámbitos diversos, cuyo nexo común es su implicación en el fortalecimiento del procomún. Una plataforma de la cual puedan emerger comunidades (formadas por personas individuales y entidades públicas o privadas, inicialmente en el ámbito de España) de agentes promotores-productores, financiadores y/o colaboradores. Cada miembro de la red puede cumplir uno o varios de estos roles según el momento y el proyecto, obteniendo una serie de beneficios y contraprestaciones específicas en función de lo que elija hacer.', 9);
INSERT INTO `faq` VALUES(8, 'goteo', 'project', '¿Cuál es la cantidad mínima o máxima que un proyecto puede tener como objetivo de financiación?', 'Aspiramos a financiar proyectos con presupuestos de cierta envergadura. Tentativamente (siempre puede haber excepciones) desde un importe mínimo de unos 3.000€ hasta 150.000€ como máximo.', 6);
INSERT INTO `faq` VALUES(9, 'goteo', 'project', '¿Quién puede obtener financiación para su proyecto en Goteo?', 'Desde Goteo y su red de comunidades se realiza una selección entre todos los proyectos que se inscriben en la plataforma. Esa selección se establece en función de la temática, tipología y procedencia del proyecto; de su pertinencia y carácter innovador o diferencial; de una estimación del retorno colectivo que genera; y también de la competencia o experiencia de sus promotores. Buscamos proyectos desarrollados por agentes creativos que superen encasillamientos temáticos convencionales, como la cultura o el ocio. Iniciativas con un alto componente de innovación, con un buen potencial de incidencia social y/o económica, con capacidad de crecimiento y reproducción y, especialmente, componentes abiertos que ayuden a fomentar el procomún. Para que así puedan generar valor en el sentido más amplio de la palabra.\r\n\r\nCualquier creador o emprendedor a través de Goteo puede:\r\n\r\n- publicar un proyecto para obtener financiación y colaboraciones;\r\n- acceder a herramientas específicas de “social media” y publicar contenidos para difundir su trabajo en Internet;\r\n- compartir conocimiento con la comunidad sobre el proceso de producción de su proyecto, aumentando su reputación online y un mayor número de seguidores y cofinanciadores potenciales;\r\n- beneficiarse del conocimiento y recomendaciones de otros usuarios para mejorar o contrastar la producción de su proyecto;\r\n- testear su proyecto en la fase inicial, y así comprobar el interés que despierta en el público o grupo de destinatarios potencial;\r\n-formar parte de una red social dinámica y generadora de cambios, con impacto local y difusión internacional;\r\n- recibir asesoramiento para mejorar la comunicación pública del proyecto y crear un retorno digital que quedará disponible en la plataforma después de su producción.', 2);
INSERT INTO `faq` VALUES(10, 'goteo', 'project', '¿Hay un tiempo máximo de financiación?', 'Una vez se publica un proyecto en Goteo, se abre un plazo de 40 días para que se pueda recaudar la cantidad fijada como mínimo para su realización (mediante aportaciones financieras de 5€ en adelante). Además en ese plazo se pueden establecer diferentes compromisos de colaboración según las competencias o los recursos requeridos por cada proyecto. \r\nEn determinados casos, para alcanzar la cantidad óptima establecida, se invertirá en los proyectos desde una bolsa de inversión social propia de Goteo (antes el proyecto deberá alcanzar un nivel mínimo de financiación por parte de los usuarios).', 7);
INSERT INTO `faq` VALUES(11, 'goteo', 'project', '¿Cual es la fase siguiente después de 40 días?', 'Una vez se obtiene la cantidad mínima fijada es posible abrir un segundo plazo, también de 40 días, donde el impulsor o impulsores del proyecto van aportando información en tiempo real sobre el desarrollo del mismo. En ese segundo plazo, todas las aportaciones financieras se hacen efectivas aunque no se llegue a alcanzar la cantidad óptima. \r\nA lo largo de toda esa fase resulta fundamental la comunicación y dinamización de los proyectos para atraer a potenciales financiadores y colaboradores. También para conectar a distintos agentes de la comunidad y reforzar el papel de Goteo como punto de encuentro en torno al procomún. Se trata de contar de un modo ágil los avances de cada proyecto; de moverlo con efectividad en las redes sociales; y de acercarlo así a la comunidad destinataria de la iniciativa y su ámbito de influencia.', 8);
INSERT INTO `faq` VALUES(12, 'goteo', 'project', 'Temo que plagien mi idea o proyecto, ¿qué hace Goteo para impedir eso?', 'Antes de nada debes considerar que el crowfunding en general es un acto de difusión pública de ideas o proyectos en ciernes, que de ese modo se quieren llevar adelante con el apoyo de pequeñas donaciones a través de Internet. Por tanto, si no puedes contar lo que quieres hacer y hasta cierto punto cómo, tal vez la financiación de este tipo no es la mejor opción. Goteo, además, promueve la filosofía de que compartir el máximo de lo que uno crea no es incompatible con ser recompensado por ello, y en vez de eso aspira a generar un polo de atracción sobre retornos colectivos que más que plagios supongan reutilización de cosas útiles, inspiración, aprendizaje y progreso compartido para el conjunto de la sociedad. En ese sentido, existen por suerte desde hace tiempo toda una serie de mecanismos y licencias legales para preservar la autoría original y garantizar usos lícitos de las creaciones digitales, como Creative Commons o Safe Creative', 22);
INSERT INTO `faq` VALUES(13, 'goteo', 'investors', '¿Se pueden hacer aportaciones que no sean monetarias?', 'Otra de las apuestas de Goteo, a diferencia del resto de plataformas de financiación colectiva, es el valor que creemos hay que dar a las colaboraciones y aportaciones no monetarias. Esto es, habilidades o recursos materiales que otras personas puedan aportar durante el proceso de creación del proyecto, siempre desde el espíritu del beneficio mutuo. \r\nPor ese motivo, además de buscar microfinanciaciones, algunos proyectos solicitan adicionalmente pequeñas tareas, préstamos materiales o de infraestructura, que también necesitan pese a no ser imprescindibles para que el proyecte arranque, y que en ningún caso substituyen un posible encargo profesional.\r\n\r\nPor ejemplo, si el proyecto gira en torno a la creación de un servicio de Internet y requiere de un esfuerzo considerable en programación, esta tarea considerada imprescindible debe ser reflejada en los costes mínimos del proyecto. Pero en cambio si la página web de ese servicio puede mejorar gracias a usuarios que hagan de beta testers, o bien con traducciones puntuales, las personas que consideren que su impulsor está haciendo un buen trabajo de dinamización del proyecto y lo deseen pueden implicarse en esas tareas. Se establece así una relación donde todas las partes pueden obtener conocimiento, a la vez que ampliar su capacitación e identidad digital trabajando en creaciones colectivas, como sucede a diario en entornos colaborativos de Internet como la Wikipedia o el software libre.', 4);
INSERT INTO `faq` VALUES(14, 'goteo', 'investors', '¿Por qué dos rondas de 40 días?', 'Mientras que en el resto de plataformas de crowdfunding sólo hay periodo de recaudación por proyecto, en Goteo se establecen dos plazos para su financiación, de 40 días cada uno. En el primero las aportaciones económicas comprometidas sólo se hacen efectivas si se alcanza la cantidad mínima establecida. En el segundo, en el que el impulsor del proyecto debe aportar información en tiempo real sobre el proyecto, todas las aportaciones que se logre recaudar se hacen efectivas al final del plazo.\r\n\r\nEl principal motivo para plantear este sistema es que creemos que permite una visualización más clara de las necesidades mínimas para poner en marcha un proyecto, por un lado, y por otro optimizarlo, abrirlo a más gente y sobre todo trabajar en abierto informando de los avances con transparencia, algo que repercute directamente en obtener más visibilidad y apoyos.', 9);
INSERT INTO `faq` VALUES(16, 'goteo', 'investors', '¿Qué sucede si un proyecto NO logra la financiación mínima?', 'Si la suma de aportaciones económicas no llega al mínimo establecido por el impulsor del proyecto se considera que no ha recibido suficientes apoyos para llevarse a cabo, y en ese caso ninguno de los pagos previstos se hacen efectivos. El proyecto pasa a archivarse, para así dejar espacio y visibilidad a otro en la plataforma, y a efectos de quienes se hayan comprometido con una cantidad de dinero determinada, finalmente no se efectuará la transacción y por tanto no se realizará ningún movimiento en su cuenta corriente.\r\n\r\nSin embargo, en la segunda ronda de financiación, con proyectos que empiezan a producirse poque ya han logrado el mínimo operativo que necesitaban recaudar, cualquier aportación comprometida durante esa etapa se hará efectiva al final del plazo, pese a no llegar a alcanzar la segunda meta de financiación que han definido como óptima. ', 10);
INSERT INTO `faq` VALUES(22, 'goteo', 'project', '¿Como se presentan los resultados y retornos de los proyectos?', 'Ésa es la fase final, que cierra el círculo del compromiso entre ambas partes (impulsor/es y cofinanciadores del proyecto), con la presentación pública online de los resultados del proyecto financiado, una vez se han hecho efectivos los retornos colectivos e individuales acordados. Es una fase que hay que cuidar especialmente, para mantener las relaciones de confianza dentro de la comunidad y que el fortalecimiento del procomún que nos proponemos sea efectivo, con posterioridad al paso del proyecto por Goteo.', 24);
INSERT INTO `faq` VALUES(23, 'goteo', 'node', '¿Quién promueve Goteo?', 'El principal promotor e impulsor de Goteo es Platoniq, una organización internacional de productores culturales y desarrolladores de software, pionera en la producción y distribución de la cultura copyleft. Desde 2001, Platoniq lleva a cabo acciones y proyectos donde los usos sociales de las TIC y el trabajo en red son aplicados al fomento de la comunicación, la auto-formación y la organización ciudadana. Entre sus proyectos destacan: Burn Station, OpenServer, Banco Común de Conocimientos o S.O.S. Todos estos y otros muchos proyectos están disponibles en la plataforma de metodologías libres YouCoop.\r\n\r\nActualmente Platoniq lleva más de un año totalmente volcada en el desarrollo de Goteo, tras un estudio exhaustivo iniciado en 2009 sobre plataformas de crowdfunding a nivel internacional y un plan de viabilidad para llevar a la práctica las principales conclusiones y nociones de diseño derivados de dicho estudio. Para el desarrollo de Goteo, Platoniq cuenta con el apoyo de diversas entidades nacionales e internacionales, entre las que cabe destacar: EUTOKIA, Centro de Innovación Social de Bilbao (ColaBoraBora), Trànsit Projectes, Centro de Cultura Contemporánea de Barcelona (CCCBlab), Consell Nacional de la Cultura i de les Arts de Catalunya, Instituto de Cultura de Barcelona.\r\n\r\nDe cara a un funcionamiento del proyecto acorde con principios afines de abertura, neutralidad e independencia se ha puesto en marcha la Fundación Goteo, entidad sin ánimo de lucro que integra a todos los agentes comprometidos con el desarrollo del proyecto y asegura un funcionamiento transparente y responsable de su misión.\r\n\r\nActualmente Platoniq lleva más de un año totalmente volcada en el desarrollo de Goteo. Ha realizado un estudio exhaustivo sobre plataformas de crowdfunding a nivel internacional, un plan de viabilidad y ya está desarrollando el trabajo de programación de la plataforma.\r\n\r\nPara el desarrollo de Goteo, Platoniq cuenta con el apoyo de diversas entidades nacionales e internacionales, entre las que cabe destacar: \r\nEUTOKIA, Centro de Innovación Social de Bilbao (ColaBoraBora)\r\nTrànsit Projectes\r\nCCCB, Centro de Cultura Contemporánea de Barcelona (CCCBlab)\r\nConsell Nacional de la Cultura i de les Arts de Catalunya\r\nInstituto de Cultura de Barcelona\r\n\r\nDe cara a la puesta en marcha de Goteo se constituirá la Fundación Goteo, que integre a todos los agentes comprometidos con el desarrollo del proyecto y asegure un funcionamiento transparente y responsable.\r\n', 10);
INSERT INTO `faq` VALUES(24, 'goteo', 'investors', '¿Qué es la bolsa de inversión social?', 'Goteo dispone de una bolsa de inversión social con aportaciones de entidades públicas y privadas, mediante la cual éstas pueden potenciar la cofinanciación de proyectos coordinadas con la comunidad y la sociedad civil.\r\n\r\nAdemás de las aportaciones económicas individuales vinculadas a proyectos concretos, para favorecer y potenciar un espacio de co-responsabilidad en el crowdfunding, se ha establecido como otra apuesta por el procomún esta bolsa de inversión social con aportaciones provenientes de la administración pública, de empresas y de otras organizaciones privadas.\r\n\r\nEsta bolsa se administra de modo transparente y responsable desde la Fundación Goteo, nutriéndose a partir de distintos tipos de compromisos anuales y a través de campañas temporales específicas (mediante fórmulas preestablecidas o a partir acuerdos ad hoc).\r\n\r\nLos agentes donantes de la bolsa de inversión social pueden vincular sus aportaciones a proyectos concretos o bien a determinadas áreas temáticas, ámbitos geográfico o tipos de retorno y licencias. También vehicular de un modo innovador parte de sus competencias y programas, así como sus políticas de compromiso y responsabilidad social. Además obtienen otras ventajas, como son: participar en la generación de formas de conocimiento colectivo y proyectos socialmente innovadores, cercanía e interlocución directa con comunidades emergentes, visibilidad y reconocimiento vinculado a proyectos relacionados con el procomún, desgravaciones fiscales, etc.', 11);
INSERT INTO `faq` VALUES(25, 'goteo', 'project', '¿Por qué publicar un proyecto en Goteo?', 'Publicar proyectos en Goteo es acceder a un nuevo modelo colectivo de financiación y colaboraciones, aprovechando las posibilidades de Inernet: hace partícipe desde el principio a la comunidad potencial del proyecto, fomentando una relación cooperativa, activa y transparente. Goteo plantea una nueva vía de financiación e impulso a iniciativas creativas, acorde a las posibilidades que ofrecen los medios digitales. Una alternativa o un complemento a la financiación derivada de la administración pública o de la empresa privada, reactivando el papel co-responsable de la sociedad civil para apoyar proyectos que contribuyan al desarrollo social comunitario, desde la filosofía del procomún. Una manera de que personas y organizaciones pequeñas, con difícil acceso a recursos, puedan llevar a cabo con éxito proyectos sostenibles y perdurables en el tiempo.', 1);
INSERT INTO `faq` VALUES(26, 'goteo', 'node', '¿Por qué un importe mínimo y otro óptimo?', 'Ésa es otra de las diferencias con muchas de las plataformas y dinámicas de crowdfunding que hemos estudiado para diseñar Goteo. Creemos que se puede y se debe ir más lejos en la relación entre microfinanciadores e impulsores de proyectos, y que un sistema basado en la confianza y los resultados transparentes puede permitir rondas posteriores de financiación una vez se han logrado avances significativos en las creaciones.\r\nPor eso en Goteo se puede solicitar al principio el capital mínimo para llevar adelante la parte inicial o crítica de un proyecto, y una vez obtenido y en marcha el proceso de producción seguir recaudando fondos a medida que se comparten los avances con la comunidad, hasta así legar a un capital óptimo. Es un funcionamiento que nos parece lógico y posible, y queremos demostrarlo.   ', 3);
INSERT INTO `faq` VALUES(28, 'goteo', 'project', '¿Como gestionar las recompensas individuales?', 'A través del formulario de inscripción de proyectos podrás plantear y definir detalladamente los importes vinculados a cada recompensa, su tipología y cuántas unidades de cada tipo se ofrecen a los cofinanciadores.\r\nPosteriormente, cuando el proyecto haya obtenido su objetivo de financiación, desde el panel de control accederás a una serie de herramientas para gestionar los envíos y así entregar las recompensas prometidas.', 18);
INSERT INTO `faq` VALUES(30, 'goteo', 'node', '¿Por qué los proyectos abiertos tienen mas potencial?', 'Vale, esta FAQ es un poco retórica pero es que creemos firmemente que sí, que los proyectos abiertos tienen más potencial. Ahí van un par o tres de razones:\r\n\r\n- Porque tienen una misión más allá de los incentivos o recompensas individuales, aportando valor al sector en que se inscriben y posibilitando modelos innovadores.\r\n- Porque fomentan el aprendizaje y el emprendizaje de otros, permitiendo que se genere una economía alrededor (bajo las reglas de atribuir la autoría y que los derivados estén bajo la misma ética de lo abierto).\r\n- Porque construyen capital social, apoyándose y nutriendo redes de seguidores, prosumidores y divulgadores    que tienen más libertad para aportar y actuar como mejor les convenga.     ', 8);
INSERT INTO `faq` VALUES(31, 'goteo', 'project', '¿Qué tipo de proyectos NO se publican en Goteo?', 'Goteo no está orientado a ayudar en la financiación de proyectos cuya finalidad sea exclusivamente de lucro, ni tampoco a acoger procesos de venta de productos o servicios, ni campañas de sorteo, políticas o de recolección de fondos para iniciativas de beneficiencia.\r\n\r\nAl igual que la mayoría de plataformas de crowdfunding, tampoco es una herramienta para financiar ni impulsar proyectos relacionados con contenidos que puedan resultar inapropiados, delictivos o que atenten contra la dignidad de las personas.', 3);
INSERT INTO `faq` VALUES(32, 'goteo', 'investors', '¿Por qué debo invertir en los proyectos de esta plataforma?', 'Te invitamos a conocer y financiar los proyectos publicados en Goteo, que necesitan aportaciones económicas y apoyo en difusión y otras tareas, si quieres ayudar a preservar Internet y los bienes comunes en general como un territorio de innovación, creatividad y mejora constante para la sociedad. Las personas detrás de cada uno de estos proyectos están abriendo el “código fuente” de lo que saben y lo que planean hacer para que el máximo posible de personas se beneficien de ello, generando así nuevas oportunidades para el aprendizaje, la colaboración y la creatividad.\r\n\r\nPuedes ayudar mediante una cantidad económica adaptada a tus posibilidades, por la que recibirás una recompensa individual en función de cada proyecto, pero también difundiendo y apoyando con habilidades o recursos concretos al proyecto o proyectos que más te motiven. Tómate un rato para consultarlos con calma, ¡seguro que encuentras ése hecho a medida para ti del que puedes pasar a formar parte! (Si no es así escríbenos, danos pistas sobre otro tipo de proyectos que deberíamos ayudar a promocionar).            ', 1);
INSERT INTO `faq` VALUES(33, 'goteo', 'node', '¿Cómo es el “crowdfunding” o financiación colectiva mediante Goteo?', 'Goteo es una red social de producción, microfinanciación y distribución de recursos para el sector creativo, centrado en el desarrollo de proyectos sociales, culturales, educativos, tecnológicos que contribuyan al fortalecimiento del procomún (esto es, de los bienes colectivos).\r\n\r\nLa reciente proliferación de plataformas de crowdfunding en el mundo, tras la irrupción de Kickstarter.com en 2009 desde Estados Unidos (por ejemplo en España de la mano de Verkami o Lánzanos, por citar dos de las más populares), podría devenir en cierta saturación y desconcierto, sobre todo si tenemos en cuenta que hay países con poca tradición en la financiación social de proyectos. Por eso, consideramos que es fundamental apostar por la diferenciación y la especialización. En Goteo partimos de la ecuación que nos parece más potente del crowdfunding: financiación colectiva para el beneficio colectivo.\r\n\r\nGoteo es una herramienta digital que facilita y combina la financiación colectiva (crowdfunding económico o de recursos) y las colaboraciones no dinerarias (por competencias y microtareas) entre los usuarios de la plataforma.\r\n\r\nLa estrategia de Goteo para potenciar el procomún se basa en reproducir la herramienta en ámbitos temáticos (sectores creativos) y geográficos (comunidades autónomas o ciudades de gran concentración), dando servicio a organizaciones públicas o privadas para que dinamicen su propia plataforma dentro de su sector y competencias.    ', 1);
INSERT INTO `faq` VALUES(34, 'goteo', 'node', '¿Cómo funciona la financiación de 40 + 40?', 'Cada proyecto tiene un objetivo de financiación mínima, establecido por su impulsor o impulsores, y 40 días para lograr alcanzarlo. Finalizado ese primer plazo, existen dos posibilidades:\r\n\r\nPosibilidad A: Que no se haya recaudado objetivo de financiación mínimo. En este caso no se puede llevar a cabo ningún tipo de transacción monetaria y los compromisos de aportación de los micromecenas quedan anulados. El proyecto tampoco puede seguir en campaña desde la plataforma y debe ceder su espacio de atención en Goteo a otro proyecto.\r\n\r\nPosibilidad B: Que se haya alcanzado o superado el mínimo del objetivo de financiación mínimo. En ese caso se realiza el cargo en tarjeta de los compromisos de aportación y el destinatario asignado recibe el dinero recaudado. A partir de ese momento se abre un segundo plazo, también de 40 días, donde el impulsor o impulsores del proyecto deben ir aportando información en tiempo real sobre el desarrollo del mismo. En este segundo plazo, todas las aportaciones financieras se hacen efectivas aunque no se llegara al nivel óptimo definido, y el creador recibe el dinero recaudado al final de una segunda ronda de 40 días.', 4);
INSERT INTO `faq` VALUES(35, 'goteo', 'project', '¿Qué pasa si un proyecto llega al nivel optímo de financiación antes de acabar el primer plazo de 40 días? ', 'En esos casos consideramos que el proyecto está captando suficientes apoyos económicos, y una vez finalizado el primer plazo de recaudación comenzará el siguiente con esa cantidad extra ya reflejada en su cuenta y a todos los efectos. Esto es, haciéndose efectiva (junto al resto de lo que sume) una vez finalice la segunda ronda de financiación.', 9);
INSERT INTO `faq` VALUES(36, 'goteo', 'project', '¿En qué beneficia a mi proyecto usar licencias abiertas?', 'Las licencias abiertas no sólo permiten preservar la autoría original y el control sobre los usos derivados que se pueden hacer de las obras, sino que representan un verdadero mecanismo de replicación y difusión online, al tratarse de una fuente cada vez más rica y diversa de bienes comunes, donde muchas personas acuden en busca de inspiración, talento y recursos de todo tipo (educativos, artísticos, documentales, etc). \r\nPor todo ello, y teniendo siempre en cuenta que Goteo garantiza precisamente la remuneración por el esfuerzo de ideación y creación de los proyectos (que en paralelo pueden tener sus propios mecanismos de sostenibilidad económica), compartimos la opinión cada vez más generalizada de que las licencias abiertas no es que perjudiquen, sino que sólo pueden beneficiar a los impulsores de los proyectos. Y al resto de la sociedad.          ', 23);
INSERT INTO `faq` VALUES(37, 'goteo', 'project', '¿Cual es el proceso de revisión de proyectos? ', 'Una vez dado de alta un proyecto en Goteo pasa por un proceso de revisión que determinará si finalmente se puede publicar o no para obtener financiación y apoyos. Dicha revisión la lleva a cabo el equipo de Goteo en estrecha colaboración con una comunidad de expertos de diferentes ámbitos. Si se considera necesario, durante ese proceso se lleva a cabo una labor de asesoramiento para la capacitación del proyecto. Bien sobre la manera más eficaz de comunicar la iniciativa a través Goteo o de otros medios; bien sobre cómo configurarla o adaptarla a la filosofía del procomún y la cultura de código abierto (tipos de licencias, contraprestaciones colectivas, nuevos productos y servicios, etc). No obstante, hay que tener en cuenta que el propio formulario de inscripción de proyectos funciona con un asistente contextual, que ya guía en ese momento respecto a muchas de las cuestiones básicas que luego se revisan.\r\n\r\nToda esta fase de selección y refinamiento de proyectos, especialmente en estos inicios de Goteo en que nos encontramos, es fundamental para que la plataforma se desarrolle como un ecosistema diverso; un espacio de oportunidad y no de competencia; donde los intereses y los recursos se sumen y no se solapen. En ese sentido es necesario ser conscientes, por parte de todos y todas, respecto a la capacidad inicial de promoción de proyectos en Goteo y de la masa crítica necesaria para que sea una apuesta sostenible en el tiempo.', 4);
INSERT INTO `faq` VALUES(38, 'goteo', 'project', '¿Puedo tener más de un proyecto en campaña en Goteo? ', 'El sistema lo permite y por tanto sí, no hemos querido cerrar esa opción, pero por norma general lo importante y efectivo es centrarse en un sólo proyecto. Piensa que además Goteo es una especie de sistema 2x1, ya que cada proyecto dispone de dos fases de financiación y hay que estar por ellas y trabajar duro en difundir y avanzar en cada fase. Además en esta primera etapa en beta lo que valoramos es la diversidad, tanto de ideas como de personas, así que más de un proyecto simultáneo por persona o equipo se puede aceptar por ahora sólo excepcionalmente.', 21);
INSERT INTO `faq` VALUES(39, 'goteo', 'project', '¿Qué son las recompensas individuales y como pensarlas? ', 'En goteo hay dos tipos de recompensas, las colectivas y las individuales. Las recompensas o retornos colectivos los explicamos en la FAQ de abajo. Las recompensas individuales son las que el impulsor o impulsores del proyecto ofrecen a sus cofinanciadores a cambio de su aportación económica. Dichas contraprestaciones, que deben variar en función de las diferentes sumas de dinero que el proyecto aspire a obtener, son muy importantes para lograr la participación del mayor número y variedad de cofinanciadores posible.\r\n\r\nEs importante hacerlas atractivas y exclusivas, y calibrarlas bien, prometiendo bienes o servicios que sea viable ofrecer a cambio y no hipotequen la capacidad de producción del proyecto. También hay que tener en cuenta los costes económicos que implican, para así poderlos incorporar al importe mínimo que se solicita para poder llevar a cabo el proyecto.\r\n\r\nOtro aspecto importante es plantear tanto donaciones de pequeña cuantía como de sumas grandes, y que dentro de esa escala haya unas pocas opciones pero que estén bien diferenciadas y escaladas. ', 12);
INSERT INTO `faq` VALUES(40, 'goteo', 'project', '¿Cuanto tarda Goteo en darme una respuesta?', 'Al margen de mensajes puntuales para asesorar si es necesario al proyecto, como se indica arriba, tanto si es afirmativamente como debiendo rechazarlo para su publicación en Goteo, la respuesta no debería tardar más de 10 días.', 5);
INSERT INTO `faq` VALUES(41, 'goteo', 'project', '¿Como recibo las aportaciones de mis cofinanciadores?  ', 'Los mecenas realizan sus compromisos de aportación mediante Paypal o tarjeta de crédito, utilizando una pasarela de pago de Caja Laboral, del Grupo Mondragon.  Cuando tu proyecto alcance el mínimo del objetivo de financiación recibirás una notificación y deberás proporcionarnos tus datos bancarios. \r\nCuando acabe el primer plazo de recaudación de 40 días, recibirás una transferencia por el importe recaudado hasta dicha fecha, descontando la comisión de Goteo (8%) y los gastos bancarios de la operación.  Después de la segunda ronda de financiación de 40 días recibirás el dinero correspondiente al importe óptimo que hayas alcanzado durante ese plazo.', 14);
INSERT INTO `faq` VALUES(42, 'goteo', 'project', '¿Se cobra algún tipo de comisión? ', 'Sí, cuando un proyecto logra recaudar los fondos que solicita, acaba el plazo de financiación y se hacen efectivas las aportaciones, por cada una de ellas el banco aplica una comisión de entre 1.30% y 1.40% en concepto procesamiento de pago por cada tarjeta. En el caso de las aportaciones hechas a través de Paypal, la comisión es del 3% por cada transacción que se haga efectiva finalmente. Estas comisiones se descuentan del importe total que obtiene el proyecto, y por tanto se deben tener en cuenta a la hora de calcular su objetivo de financiación. \r\n\r\nLa otra comisión que se aplica al importe total obtenido es un 8% del propio sistema de Goteo, en concepto de trámites de gestión y como una de sus fuentes de sostenibildad económica.', 15);
INSERT INTO `faq` VALUES(43, 'goteo', 'investors', '¿Hay algún tipo de protocolo para posibles estafas por parte del impulsor del proyecto?', 'Existe un contrato oficial que suscriben el impulsor o impulsora del proyecto y la Fundación Goteo, mediante el cual el primero se compromete a llevar a cabo su propuesta en caso de que ésta finalmente sea financiada, y en cumplimiento de las especificaciones que ha detallado y hecho públicas en la plataforma. En caso contrario, por tanto, y tras agotar otras vías basadas en la relación de confianza y trato directo entre todos los agentes que implica Goteo, se pueden emprender acciones legales.\r\n', 13);
INSERT INTO `faq` VALUES(45, 'goteo', 'investors', '¿Cómo puedo saber que se inicia un proyecto al que he apoyado?', 'Tanto si tu apoyo ha sido monetario como si has participado activamente en la página del proyecto, recibirás un correo electrónico para informarte una vez su impulsor obtenga los fondos que le permiten ponerse a trabajar (o si por el contrario el proyecto se archiva, al no  haber alcanzado sus objetivos mínimos de financiación en el plazo establecido).\r\n\r\nSi el proyecto se inicia finalmente, recibirás un correo electrónico confirmando que se ha realizado el cargo en tu tarjeta.  Si tu aportación se ha efectuado durante la segunda ronda del proyecto, es decir cuando ya se ha iniciado, también recibirás una notificación confirmando el cargo en tu tarjeta, tras el plazo de 40 días.', 6);
INSERT INTO `faq` VALUES(46, 'goteo', 'investors', 'Si hago una aportación, ¿a qué información sobre mí accede el impulsor del proyecto?', 'La persona que impulsa el proyecto que hayas cofinanciado sólo podrá saber tu nombre de usuario en Goteo junto a la cantidad que has aportado y la recompensa individual que te corresponde a cambio. Nunca podrá acceder a datos personales sobre ti, que en ese sentido son confidenciales. De lo que sí dispone es de la posibilidad de enviarte mensajes de correo a través de Goteo, lo cual permite que os relacionéis de tú a tú hasta donde ambos consideréis oportuno.\r\n', 3);
INSERT INTO `faq` VALUES(47, 'goteo', 'investors', '¿Cómo puedo ayudar a la difusión de un proyecto?', 'Ese apoyo ya es muy importante, aunque no puedas cofinanciar económicamente o ayudar en alguna otra parte de un proyecto, moverlo por Internet y entre tus contactos puede lograr que reciba más atención y al final obtenga el mínimo requerido para salir adelante. Para ello, en la página de cada proyecto encontrarás un apartado que permite compartirlo en diferentes redes sociales, pegar su link directo donde quieras, incluso el código de un widget con un resumen visual de qué pretende esa iniciativa y cómo va su financiación, para ponerlo en tu blog o la web que quieras.', 7);
INSERT INTO `faq` VALUES(48, 'goteo', 'investors', '¿Cómo puedo participar en el proceso creativo de los proyectos?', 'Desde la plataforma de Goteo puedes consultar, sugerir y en general establecer una conversación con el impulsor del proyecto sobre sus necesidades financieras o llamadas a la colaboración. También puedes seguir y comentar el desarrollo de todo el proceso a través de las actualizaciones que hace en la página principal del proyecto. Y recibir por correo notificaciones sobre cualquier novedad o cambio relacionado con el proyecto que estás cofinanciando.', 5);
INSERT INTO `faq` VALUES(49, 'goteo', 'project', '¿Cómo puedo obtener la información de mis cofinanciadores?', 'Una vez lograda la financiación mínima para llevar adelante el proyecto, el panel de control del sistema muestra la información necesaria para ponerse en contacto con los usuarios que hayan realizado aportaciones.', 17);
INSERT INTO `faq` VALUES(50, 'goteo', 'project', '¿Debo publicar novedades sobre mi proyecto?', 'Deberías, sí. Las notificaciones regulares son una parte fundamental del proceso para lograr que el proyecto capte la atención y reciba apoyos. Debes tener un pequeño plan sobre qué cosas contar sobre ti o tu equipo, los orígenes y las virtudes del proyecto. Tal vez cosas que no has mencionado en el formulario, tal vez otra manera de explicarlas. La transparencia, dinamismo y empatía que apliques los 40 días de la primera fase de recaudación son fundamentales. La herramienta de goteo te permite hacerlo mediante mensajes, fotografías y vídeos, y también hacerlo llegar a diferentes canales y redes de Internet. ', 20);
INSERT INTO `faq` VALUES(51, 'goteo', 'nodes', '¿Qué son los nodos de Goteo?', 'Goteo aspira a crecer como una comunidad de nodos cuyo nexo de unión sea el interés por el fortalecimiento del procomún, articulados en torno a esta plataforma digital de crowdfunding y colaboración.\r\n\r\nTrabajamos mediante un sistema distribuido de nodos locales que aspiramos a ir haciendo crecer. Se trata de agentes legitimados que tienen un papel muy importante a nivel territorial, y que son capaces de producir con la ayuda de Goteo un impacto social en su área de influencia. El objetivo es tejer una red de confianza y con autonomía de actuación, que pueda gestionar y recaudar con independencia y agilidad fondos para proyectos de su área geográfica. Aportando proximidad y especificidad, por tanto, multiplicando así el efecto de la plataforma.', 1);
INSERT INTO `faq` VALUES(52, 'goteo', 'node', '¿Con qué redes cuenta Goteo para ayudar a que los proyectos se difundan?', '¡Nunca con las suficientes! Goteo parte de una red amplia y distribuida geográficamente que ha ido acompañando los avances de diseño de la herramienta, en nodos geográficos e institucionales de lugares como Bilbao, Madrid, Barcelona o Mallorca, pero también puntos de la red en ámbitos del procomún, el software libre, la innovación cultural o el activismo digital. Para sumarte o preguntar por redes concretas, pásate por nuestra lista de correo.', 6);
INSERT INTO `faq` VALUES(53, 'goteo', 'project', '¿Como gestionar los retornos colectivos?', 'Dependiendo de la naturaleza del proyecto. Si es código, te aconsejamos repositorios como Github. Si se trata de documentos, puedes compartir los borradores y versiones alfas en algún wiki o servicio de publicación online, o subirlos mediante FTP a alguna dirección desde donde se puedan descargar. Si se trata de un vídeo, te aconsejamos Vimeo o Youtube para publicarlo y apuntar tus links allí. En general, a la hora de plantearte espacios de la red donde poder trabajar y alojar contenidos vinculados a tu proyecto, para que supongan un retorno colectivo, elige plataformas lo más accesibles y abiertas que sea posible, para así maximizar su difusión y coherencia con el procomún. Si se trata de retornos que en vez de digitales son físicos o digamos que offline, piensa en los espacios públicos y tarta de aplicar los mismos principios de abertura y accesibilidad.\r\n\r\nMás consejos: usa regularmente el tablón de llamadas a colaboración y publica nuevas necesidades una vez el proyecto esté en marcha. Piensa que entre tus cofinanciadores se encuentran verdaderos talentos y agentes dispuestos a acompañar el proyecto. Valora por igual gente que te apoya financieramente como la labor de colaboradores voluntarios. No pierdas nunca de vista la comunidad: ése es el motor de Goteo. ¡Cada gota cuenta! Un dolar puede ser carburante, una idea o apoyo puede ser petróleo :) Integra a los colaboradores en los procesos de crecimiento de los proyectos, en posibles retornos económicos no previstos inicialmente. Un cofinanciador puede llega a ser el difusor que te hacía falta.', 19);
INSERT INTO `faq` VALUES(55, 'goteo', 'project', '¿Qué son los retornos colectivos y como pensarlos? ', 'Se trata en cierto modo de la gran apuesta de Goteo: generar retornos colectivos y bienes comunes compaginables con las recompensas individuales, pero en comparación a éstas sean generadoras de cambios mucho mayores. Recompensas orientadas al procomún, que permitan la reutilización, recombinación, generación de nuevos usos y valores sobre lo ya hecho.  Si te cuesta ver cómo generar algún tipo de retorno colectivo en tu proyecto, la guía del formulario de alta te puede ayudar a pensar en ello. Si no te parece suficiente, hablemos.', 13);
INSERT INTO `faq` VALUES(56, 'goteo', 'project', '¿Las necesidades del proyecto pueden ser modificadas una vez en campaña?', 'Sí. Lo único que no se puede editar es la cifra total objetivo de la financiación y las recompensas que se han definido para cada volumen de aportación. En cambio, no sólo es posible sino muy recomendable actualizar la información del proyecto regularmente desde el panel de control, tanto en la fase de búsqueda de financiación como una vez obtenido el mínimo, y por lo tanto con la producción ya en marcha. El feedback hacia cofinanciadores y colaboradores resulta primordial a la hora de llegar a los objetivos óptimos de financiación en esa segunda ronda de financiación, para la que recomendamos planificar bien las posibles tareas y fases del proyecto.', 11);
INSERT INTO `faq` VALUES(57, 'goteo', 'project', '¿Qué herramientas tengo para administrar mi proyecto?', 'Cada creador, dispone de un dashboard o panel de control desde donde administrar la información pública de su proyecto, a modo de centro de operaciones para dinamizarlo y gestionarlo. Desde ahí se pueden publicar actualizaciones, añadir fotos y vídeos, clasificar las aportaciones de los cofinanciadores y gestionar los envíos de las recompensas individuales.\r\n\r\nEse panel de control también permite ver toda la información detallada sobre cómo evoluciona la recaudación y los apoyos recibidos, y posteriormente comunicarse con los usuarios que hayan cofinanciado el proyecto.', 16);
INSERT INTO `faq` VALUES(58, 'goteo', 'investors', '¿Quién puede apoyar proyectos en Goteo?', 'Cualquier ciudadano, empresa o institución puede:\r\n\r\n- conocer, opinar y establecer conversaciones en línea sobre los planes, acciones y progresos llevados a cabo por los impulsores de los proyectos;\r\n- apoyarlos mediante una microdonación o una microcolaboración, es decir, haciendo donaciones financieras (a partir de 5 euros) o colaborando según las competencias y los medios de cada uno;\r\n- beneficiarse del acceso a los retornos colectivos digitales (contenidos, cursos o asesorías por Internet, herramientas digitales de código abierto, etc) que el creador ha decidido ofrecer a la comunidad en las condiciones que establecen las licencias que ha elegido.', 2);
INSERT INTO `faq` VALUES(59, 'goteo', 'project', '¿Cual es el compromiso del creador con Goteo y los Cofinanciadores?', 'Después de que el proyecto haya sido aceptado por la comunidad de Goteo y haya conseguido su financiación mínima, se formaliza el contrato que aparece desde el primer momento en tu panel de control como impulsor el proyecto. En él te comprometes a llevar a cabo el proyecto y a dar acceso a los contenidos o servicios que hayas definido como retorno colectivo para la comunidad.\r\n\r\nEn el caso de las recompensas individuales, la relación sólo pone en juego el compromiso entre el creador y sus cofinanciadores.', 10);
INSERT INTO `faq` VALUES(62, 'goteo', 'node', 'Si Goteo defiende el código abierto ¿se va abrir la plataforma?  ', 'Absolutamente. Se va a abrir de modo progresivo en dos sentidos: inicialmente mediante la posibilidad de generar un nodo alojado desde Goteo, con la posibilidad de que una comunidad local tenga su subdominio propio y capacidad para administrar y gestionar la cofinanciación de proyectos. Posteriormente, a partir de 2012 y cuando tengamos ya una versión estable bien testeada y la comunidad en torno a Goteo funcionando a buen rendimiento, liberaremos la totalidad del código. Para que todo aquel que quiera lo instale y utilice para sus propios propósitos de ayuda al procomún, al tiempo que más desarrolladores puedan contribuir a mejorar la herramienta.', 11);
INSERT INTO `faq` VALUES(64, 'goteo', 'project', '¿Puede un mismo proyecto estar en varias plataformas a la vez?', 'Creemos que sí, al menos por nuestra parte no hay problema :) De hecho ya se han dados casos y precisamente lo permite la modularidad de muchos proyectos (que pueden tener una derivada lúdica pero también productos o procesos con potencial educativo, de investigación, de márketing, de traspaso a otros soportes o formatos, etc). Para ello lo más recomendable es estudiar otras plataformas y qué tipo de proyectos apoyan, y considerar un calendario y desglose del proyecto en que no se solapen dos campañas coincidentes en dos sitios diferentes, puesto que puede generar confusión y recelo entre los potenciales cofinanciadores.', 25);
INSERT INTO `faq` VALUES(65, 'goteo', 'nodes', '¿Quién y cómo se puede formar un nodo?', 'Puede formar un nodo cualquier colectivo o entidad en una localidad o área concreta (de momento sólo en España), cuyos miembros compartan las ganas y capacidad operativa para atraer a personas y proyectos capaces de fortalecer el procomún, y puedan gestionar tanto la difusión como el manejo regular de administración de una plataforma de Goteo.\r\n\r\nPara formar el nodo debe darse previamente un período de familiarización con la herramienta y el funcionamiento del sistema, e idealmente capacidad para organizar un taller presencial con el equipo de Goteo en que se activen los actores clave e instituciones que pueden acompañar y hacer funcionar todo el proceso. No es fácil, pero sí divertido :) Si tienes interés junto a más gente para montar un nodo de Goteo en tu zona, escribidnos.', 2);
INSERT INTO `faq` VALUES(66, 'goteo', 'nodes', '¿Existen nodos temáticos?', 'No exactamente. A diferencia de los nodos, que aglutinan proyectos según grupos de personas e iniciativas de una zona geográfica, en Goteo los temas y ámbitos que tienen en común diferentes proyectos aglutinan detrás pequeñas comunidades virtuales especializadas. Esto es, expertos y personas apasionadas de un determinado tema que actúan desde cualquier ubicación geográfica como prescriptores, asesoran o difunden proyectos en fase de financiación vinculados con el arte, la tecnología, la educación, etc. ', 3);
INSERT INTO `faq` VALUES(67, 'goteo', 'nodes', '¿Qué ventajas y qué responsabilidades implica constituir un nodo de Goteo?', 'Montar y gestionar un nodo de Goteo supone una tarea intensa de dinamización y activación de oportunidades locales para aportar al procomún, generando cambios positivos tanto en la proximidad como en la distancia. En ese sentido, dota de visibilidad y reputación a las personas y/o organizaciones que lo impulsan. También implica tomar decisiones en el día a día de la gestión de Goteo, y respecto a los fondos que se recauden mediante la comisión de cada financiación obtenida por un proyecto, para lo que debe haber una mínima cohesión y criterios compartidos. Otro aspecto importante es estar en contacto periódico con el equipo impulsor de Goteo. ', 4);
INSERT INTO `faq` VALUES(68, 'goteo', 'nodes', '¿Hay algún nodo de Goteo cerca de donde estoy?', 'Si no lo hay debería haberlo! Pero ahora mismo estamos empezando y por tanto aún queda mucho por recorrer en ese sentido. De momento contamos con nodos que empiezan en Barcelona, Bilbao, Madrid y algún otro que se lo está pensando :)', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faq_lang`
--

DROP TABLE IF EXISTS `faq_lang`;
CREATE TABLE `faq_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `faq_lang`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `icon`
--

DROP TABLE IF EXISTS `icon`;
CREATE TABLE `icon` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` tinytext,
  `group` varchar(50) DEFAULT NULL COMMENT 'exclusivo para grupo',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Iconos para retorno/recompensa';

--
-- Volcar la base de datos para la tabla `icon`
--

INSERT INTO `icon` VALUES('code', 'Código fuente', 'Por código fuente entendemos programas y software en general.', 'social', 0);
INSERT INTO `icon` VALUES('design', 'Diseño', 'Los diseños pueden ser de planos o patrones, esquemas, esbozos, diagramas de flujo, etc.', 'social', 0);
INSERT INTO `icon` VALUES('file', 'Archivos digitales', 'Los archivos digitales pueden ser de música, vídeo, documentos de texto, etc.', '', 0);
INSERT INTO `icon` VALUES('manual', 'Manuales', 'Documentos prácticos detallando pasos, materiales formativos, bussiness plans, “how tos”, recetas, etc.', 'social', 0);
INSERT INTO `icon` VALUES('money', 'Dinero', 'Retornos económicos proporcionales a la inversión realizada, que se deben detallar en cantidad pero también forma de pago.', 'individual', 50);
INSERT INTO `icon` VALUES('other', 'Otro', 'Sorpréndenos con esta nueva tipología, realmente nos interesa :) ', '', 99);
INSERT INTO `icon` VALUES('product', 'Producto', 'Los productos pueden ser los que se han producido, en edición limitada, o fragmentos o obras derivadas del original.', 'individual', 0);
INSERT INTO `icon` VALUES('service', 'Servicios', 'Acciones y/o sesiones durante tiempo determinado para satisfacer una necesidad individual o de grupo: una formación, una ayuda técnica, un asesoramiento, etc.', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `icon_lang`
--

DROP TABLE IF EXISTS `icon_lang`;
CREATE TABLE `icon_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` tinytext,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `icon_lang`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `icon_license`
--

DROP TABLE IF EXISTS `icon_license`;
CREATE TABLE `icon_license` (
  `icon` varchar(50) NOT NULL,
  `license` varchar(50) NOT NULL,
  UNIQUE KEY `icon` (`icon`,`license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licencias para cada icono, solo social';

--
-- Volcar la base de datos para la tabla `icon_license`
--

INSERT INTO `icon_license` VALUES('code', 'agpl');
INSERT INTO `icon_license` VALUES('code', 'apache');
INSERT INTO `icon_license` VALUES('code', 'balloon');
INSERT INTO `icon_license` VALUES('code', 'bsd');
INSERT INTO `icon_license` VALUES('code', 'gpl');
INSERT INTO `icon_license` VALUES('code', 'lgpl');
INSERT INTO `icon_license` VALUES('code', 'mit');
INSERT INTO `icon_license` VALUES('code', 'mpl');
INSERT INTO `icon_license` VALUES('code', 'odbl');
INSERT INTO `icon_license` VALUES('code', 'odcby');
INSERT INTO `icon_license` VALUES('code', 'oshw');
INSERT INTO `icon_license` VALUES('code', 'pd');
INSERT INTO `icon_license` VALUES('code', 'php');
INSERT INTO `icon_license` VALUES('code', 'tapr');
INSERT INTO `icon_license` VALUES('code', 'xoln');
INSERT INTO `icon_license` VALUES('design', 'balloon');
INSERT INTO `icon_license` VALUES('design', 'cc0');
INSERT INTO `icon_license` VALUES('design', 'ccby');
INSERT INTO `icon_license` VALUES('design', 'ccbync');
INSERT INTO `icon_license` VALUES('design', 'ccbyncnd');
INSERT INTO `icon_license` VALUES('design', 'ccbyncsa');
INSERT INTO `icon_license` VALUES('design', 'ccbynd');
INSERT INTO `icon_license` VALUES('design', 'ccbysa');
INSERT INTO `icon_license` VALUES('design', 'fal');
INSERT INTO `icon_license` VALUES('design', 'fdl');
INSERT INTO `icon_license` VALUES('design', 'gpl');
INSERT INTO `icon_license` VALUES('design', 'oshw');
INSERT INTO `icon_license` VALUES('design', 'pd');
INSERT INTO `icon_license` VALUES('design', 'tapr');
INSERT INTO `icon_license` VALUES('file', 'cc0');
INSERT INTO `icon_license` VALUES('file', 'ccby');
INSERT INTO `icon_license` VALUES('file', 'ccbync');
INSERT INTO `icon_license` VALUES('file', 'ccbyncnd');
INSERT INTO `icon_license` VALUES('file', 'ccbyncsa');
INSERT INTO `icon_license` VALUES('file', 'ccbynd');
INSERT INTO `icon_license` VALUES('file', 'ccbysa');
INSERT INTO `icon_license` VALUES('file', 'fal');
INSERT INTO `icon_license` VALUES('manual', 'cc0');
INSERT INTO `icon_license` VALUES('manual', 'ccby');
INSERT INTO `icon_license` VALUES('manual', 'ccbync');
INSERT INTO `icon_license` VALUES('manual', 'ccbyncnd');
INSERT INTO `icon_license` VALUES('manual', 'ccbyncsa');
INSERT INTO `icon_license` VALUES('manual', 'ccbynd');
INSERT INTO `icon_license` VALUES('manual', 'ccbysa');
INSERT INTO `icon_license` VALUES('manual', 'fal');
INSERT INTO `icon_license` VALUES('manual', 'fdl');
INSERT INTO `icon_license` VALUES('manual', 'freebsd');
INSERT INTO `icon_license` VALUES('manual', 'pd');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=121 ;

--
-- Volcar la base de datos para la tabla `image`
--

INSERT INTO `image` VALUES(1, 'avatar.png', 'image/png', 1469);
INSERT INTO `image` VALUES(4, 'avatar_centella.jpg', 'image/jpeg', 9840);
INSERT INTO `image` VALUES(8, 'bike2.jpg', 'image/jpeg', 40080);
INSERT INTO `image` VALUES(14, 'bike2.jpg', 'image/jpeg', 40080);
INSERT INTO `image` VALUES(15, 'movecommons.jpg', 'image/jpeg', 93203);
INSERT INTO `image` VALUES(21, 'tweetometro_yeswecamp.png', 'image/png', 505172);
INSERT INTO `image` VALUES(23, 'cyberspace2.jpg', 'image/jpeg', 212765);
INSERT INTO `image` VALUES(24, 'oli-estats3.jpg', 'image/jpeg', 375360);
INSERT INTO `image` VALUES(26, 'p1100340.jpg', 'image/jpeg', 626094);
INSERT INTO `image` VALUES(27, 'firefox_wallpaper.png', 'image/png', 77932);
INSERT INTO `image` VALUES(28, 'navigating.jpg', 'image/jpeg', 41218);
INSERT INTO `image` VALUES(29, 'imagen-1.png', 'image/png', 608094);
INSERT INTO `image` VALUES(30, '1660.gif', 'image/gif', 11237);
INSERT INTO `image` VALUES(33, 'tallergoteobilbao_33.jpg', 'image/jpeg', 377859);
INSERT INTO `image` VALUES(35, 'tallergoteobilbao_01.jpg', 'image/jpeg', 419726);
INSERT INTO `image` VALUES(38, 'logocccblab.png', 'image/png', 3531);
INSERT INTO `image` VALUES(39, 'conca.png', 'image/png', 8681);
INSERT INTO `image` VALUES(40, 'logo-concagris.png', 'image/png', 8889);
INSERT INTO `image` VALUES(44, 'julio-grande.jpg', 'image/jpeg', 535172);
INSERT INTO `image` VALUES(45, 'acampadaplano.jpg', 'image/jpeg', 122038);
INSERT INTO `image` VALUES(54, 'todojuntoletterpress14.jpg', 'image/jpeg', 24444);
INSERT INTO `image` VALUES(55, 'oh_oh.jpg', 'image/jpeg', 13517);
INSERT INTO `image` VALUES(56, 'urbansocialdd.jpg', 'image/jpeg', 25883);
INSERT INTO `image` VALUES(57, 'avatar.jpg', 'image/jpeg', 4037);
INSERT INTO `image` VALUES(58, 'mibarriologo.jpg', 'image/jpeg', 10758);
INSERT INTO `image` VALUES(59, 'idgrf_hkp-fff.png', 'image/png', 11050);
INSERT INTO `image` VALUES(60, 'mc-logo-300-white.png', 'image/png', 12627);
INSERT INTO `image` VALUES(61, 'logo_nodo_movil.png', 'image/png', 22842);
INSERT INTO `image` VALUES(62, 'canal_alpha.jpg', 'image/jpeg', 16826);
INSERT INTO `image` VALUES(63, 'banner_robocicla.jpg', 'image/jpeg', 103545);
INSERT INTO `image` VALUES(64, 'tweetometro_admin_00.png', 'image/png', 220753);
INSERT INTO `image` VALUES(65, 'tweetometro_zoom3.png', 'image/png', 112127);
INSERT INTO `image` VALUES(66, 'error1.png', 'image/png', 65129);
INSERT INTO `image` VALUES(67, 'p1100340.jpg', 'image/jpeg', 626094);
INSERT INTO `image` VALUES(68, 'p1100343.jpg', 'image/jpeg', 548168);
INSERT INTO `image` VALUES(69, 'goteo.png', 'image/png', 13969);
INSERT INTO `image` VALUES(70, 'schulbaum_bcc_sevilla.jpg', 'image/jpeg', 46783);
INSERT INTO `image` VALUES(79, 'goteo.png', 'image/png', 17009);
INSERT INTO `image` VALUES(81, 'huevo-frito-bcc.jpg', 'image/jpeg', 115960);
INSERT INTO `image` VALUES(83, '01dinou-02.jpg', 'image/jpeg', 210253);
INSERT INTO `image` VALUES(84, '01dinou-03.jpg', 'image/jpeg', 256820);
INSERT INTO `image` VALUES(85, '01dinou-05.jpg', 'image/jpeg', 189845);
INSERT INTO `image` VALUES(86, '19j_barcelona_dinou1-6404.jpg', 'image/jpeg', 377019);
INSERT INTO `image` VALUES(87, '19j_barcelona_dinou1-6444.jpg', 'image/jpeg', 410839);
INSERT INTO `image` VALUES(88, '19j_barcelona_dinou1-6687.jpg', 'image/jpeg', 430793);
INSERT INTO `image` VALUES(89, '19j_barcelona_dinou1-6944.jpg', 'image/jpeg', 316046);
INSERT INTO `image` VALUES(92, 'logogoteo_reasonably_small.png', 'image/png', 8859);
INSERT INTO `image` VALUES(93, 'beta_tester_like_bugs_tshirt-p235188780416292059tr', 'image/jpeg', 25733);
INSERT INTO `image` VALUES(94, 'betatester.png', 'image/png', 164865);
INSERT INTO `image` VALUES(95, 'ici_olivier_lift.jpg', 'image/jpeg', 140490);
INSERT INTO `image` VALUES(96, 'schulbaum_ars2008.jpg', 'image/jpeg', 50385);
INSERT INTO `image` VALUES(98, 'jj.jpg', 'image/jpeg', 22898);
INSERT INTO `image` VALUES(99, 'jaume.jpg', 'image/jpeg', 26500);
INSERT INTO `image` VALUES(100, 'jaume.jpg', 'image/jpeg', 26500);
INSERT INTO `image` VALUES(101, 'conca.png', 'image/png', 7814);
INSERT INTO `image` VALUES(102, 'cccb.png', 'image/png', 6261);
INSERT INTO `image` VALUES(103, 'icub.png', 'image/png', 7267);
INSERT INTO `image` VALUES(104, 'brecht.jpg', 'image/jpeg', 9449);
INSERT INTO `image` VALUES(105, 'liquidbank.256.jpg', 'image/jpeg', 26305);
INSERT INTO `image` VALUES(106, '580.png', 'image/png', 263572);
INSERT INTO `image` VALUES(107, '581.png', 'image/png', 271830);
INSERT INTO `image` VALUES(108, '582.png', 'image/png', 204131);
INSERT INTO `image` VALUES(109, 'ici_mg_6454.jpg', 'image/jpeg', 497525);
INSERT INTO `image` VALUES(110, 'ici_mg_6498.jpg', 'image/jpeg', 433379);
INSERT INTO `image` VALUES(111, 'ici_mg_6478.jpg', 'image/jpeg', 412494);
INSERT INTO `image` VALUES(112, 'ici_mg_6478.jpg', 'image/jpeg', 412494);
INSERT INTO `image` VALUES(113, 'ici_mg_6494.jpg', 'image/jpeg', 565616);
INSERT INTO `image` VALUES(115, 'demo_goteo_1000_a.png', 'image/png', 369001);
INSERT INTO `image` VALUES(116, 'demo_goteo_1000_b.png', 'image/png', 393250);
INSERT INTO `image` VALUES(117, 'ivan1.jpg', 'image/jpeg', 329290);
INSERT INTO `image` VALUES(118, 'logogoteo_reasonably_small.png', 'image/png', 8859);
INSERT INTO `image` VALUES(119, 'schulbaum-ars2008.jpg', 'image/jpeg', 269182);
INSERT INTO `image` VALUES(120, 'francesc.jpg', 'image/jpeg', 11454);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `interest`
--

DROP TABLE IF EXISTS `interest`;
CREATE TABLE `interest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `description` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios' AUTO_INCREMENT=8 ;

--
-- Volcar la base de datos para la tabla `interest`
--

INSERT INTO `interest` VALUES(1, 'Educación', 'Educación', 1);
INSERT INTO `interest` VALUES(2, 'Economía solidaria', 'Economía solidaria', 1);
INSERT INTO `interest` VALUES(3, 'TICs', 'Tecnologías de Ia Información y Comunicación', 1);
INSERT INTO `interest` VALUES(4, 'Medio ambiente', 'Medio ambiente', 1);
INSERT INTO `interest` VALUES(5, 'Letras y editorial', 'Letras y editorial', 1);
INSERT INTO `interest` VALUES(6, 'Tecnologías', 'Tecnologías', 1);
INSERT INTO `interest` VALUES(7, 'Artes visuales y diseño', 'Hardware', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest`
--

DROP TABLE IF EXISTS `invest`;
CREATE TABLE `invest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `account` varchar(256) NOT NULL,
  `amount` int(6) NOT NULL,
  `status` int(1) NOT NULL COMMENT '0 pendiente, 1 cobrado, 2 devuelto',
  `anonymous` tinyint(1) DEFAULT NULL,
  `resign` tinyint(1) DEFAULT NULL,
  `invested` date DEFAULT NULL,
  `charged` date DEFAULT NULL,
  `returned` date DEFAULT NULL,
  `preapproval` varchar(256) DEFAULT NULL COMMENT 'PreapprovalKey',
  `payment` varchar(256) DEFAULT NULL COMMENT 'PayKey',
  `transaction` varchar(256) DEFAULT NULL COMMENT 'PaypalId',
  `method` varchar(20) NOT NULL COMMENT 'Metodo de pago',
  `admin` varchar(50) DEFAULT NULL COMMENT 'Admin que creó el aporte manual',
  `campaign` bigint(20) unsigned DEFAULT NULL COMMENT 'campaña de la que forma parte este dinero',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos' AUTO_INCREMENT=171 ;

--
-- Volcar la base de datos para la tabla `invest`
--

INSERT INTO `invest` VALUES(1, 'abenitez', 'hkp', 'albabenitez1983@gmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(2, 'abenitez', 'nodo-movil', 'albabenitez1983@gmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(3, 'ahernandez', 'oh-oh-fase-2', 'ahernandez@lossantos.org', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(4, 'ahernandez', 'goteo', 'ahernandez@lossantos.org', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(5, 'aollero', 'archinhand-architecture-in-your-hand', 'programaskreativos@gmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(6, 'aollero', 'hkp', 'programaskreativos@gmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(7, 'asanz', 'oh-oh-fase-2', 'asanzgr@hotmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(8, 'asanz', 'hkp', 'asanzgr@hotmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(9, 'aballesteros', 'archinhand-architecture-in-your-hand', 'geopetro10@yahoo.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(10, 'aballesteros', 'nodo-movil', 'geopetro10@yahoo.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(11, 'arecio', 'goteo', 'anto@filosomatika.net', 10, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(12, 'amunoz', 'nodo-movil', 'chonmube@gmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(13, 'amunoz', 'goteo', 'chonmube@gmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(14, 'aramos', 'nodo-movil', 'crdelcorral@hotmail.com', 10, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(15, 'ccriado', 'hkp', 'carlos@carloscriado.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(16, 'ccriado', 'goteo', 'carlos@carloscriado.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(17, 'cpinero', 'hkp', 'innovacion@energiaextremadura.org', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(18, 'cpinero', 'goteo', 'innovacion@energiaextremadura.org', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(19, 'ibelloso', 'archinhand-architecture-in-your-hand', 'ibellosobueso@yahoo.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(20, 'ibelloso', 'hkp', 'ibellosobueso@yahoo.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(21, 'lemontero', 'hkp', ' lernestomc@Yahoo.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(22, 'lemontero', 'goteo', ' lernestomc@Yahoo.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(23, 'mpalma', 'nodo-movil', 'marcela.palma@fundacionciudadania.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(24, 'mpalma', 'goteo', 'marcela.palma@fundacionciudadania.es', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(25, 'stena', 'goteo', 'sara.tena@aupex.org', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(26, 'vsantiago', 'oh-oh-fase-2', 'vstabares@gmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(27, 'vsantiago', 'nodo-movil', 'vstabares@gmail.com', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'root', 1);
INSERT INTO `invest` VALUES(28, 'rcasado', 'todojunto-letterpress', 'raul.casadogonzalez@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(29, 'rcasado', 'oh-oh-fase-2', 'raul.casadogonzalez@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(30, 'rcasado', 'move-commons', 'raul.casadogonzalez@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(31, 'rcasado', 'goteo', 'raul.casadogonzalez@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(32, 'nescala', 'todojunto-letterpress', 'nella.escala@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(33, 'nescala', 'robocicla', 'nella.escala@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(34, 'gpedranti', 'todojunto-letterpress', 'info@gabrielapedranti.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(35, 'gpedranti', 'robocicla', 'info@gabrielapedranti.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(36, 'ccarrera', 'todojunto-letterpress', 'candela.carrera@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(37, 'ccarrera', 'oh-oh-fase-2', 'candela.carrera@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(38, 'ccarrera', 'goteo', 'candela.carrera@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(39, 'afernandez', 'oh-oh-fase-2', 'ana@dispuesta.net', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(40, 'afernandez', 'urban-social-design-database', 'ana@dispuesta.net', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(41, 'afernandez', 'robocicla', 'ana@dispuesta.net', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(42, 'afernandez', 'goteo', 'ana@dispuesta.net', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(43, 'afolguera', 'oh-oh-fase-2', 'antonia@riereta.net', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(44, 'afolguera', 'hkp', 'antonia@riereta.net', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(45, 'afolguera', 'nodo-movil', 'antonia@riereta.net', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(46, 'afolguera', 'goteo', 'antonia@riereta.net', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(47, 'iromero', 'oh-oh-fase-2', 'ima_gina7@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(48, 'iromero', 'move-commons', 'ima_gina7@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(49, 'iromero', 'nodo-movil', 'ima_gina7@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(50, 'iromero', 'goteo', 'ima_gina7@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(51, 'rparramon', 'oh-oh-fase-2', 'rparramon@acvic.org', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(52, 'rparramon', 'move-commons', 'rparramon@acvic.org', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(53, 'rparramon', 'goteo', 'rparramon@acvic.org', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(54, 'tbadia', 'oh-oh-fase-2', 'tbadtod@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(55, 'tbadia', 'nodo-movil', 'tbadtod@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(56, 'tbadia', 'robocicla', 'tbadtod@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(57, 'tbadia', 'goteo', 'tbadtod@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(58, 'lstalling', 'oh-oh-fase-2', 'larsst@gmail.com ', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(59, 'lstalling', 'urban-social-design-database', 'larsst@gmail.com ', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(60, 'lstalling', 'goteo', 'larsst@gmail.com ', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(61, 'mramirez', 'oh-oh-fase-2', 'miquel.ramirez@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(62, 'mramirez', 'nodo-movil', 'miquel.ramirez@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(63, 'mramirez', 'goteo', 'miquel.ramirez@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(64, 'fcoddou', 'urban-social-design-database', 'flaviocoddou@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(65, 'fcoddou', 'archinhand-architecture-in-your-hand', 'flaviocoddou@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(66, 'fcoddou', 'hkp', 'flaviocoddou@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(67, 'fcoddou', 'robocicla', 'flaviocoddou@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(68, 'criera', 'urban-social-design-database', 'criera@transit.es', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(69, 'criera', 'archinhand-architecture-in-your-hand', 'criera@transit.es', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(70, 'criera', 'robocicla', 'criera@transit.es', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(71, 'gbento', 'urban-social-design-database', 'giselecultura@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(72, 'gbento', 'move-commons', 'giselecultura@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(73, 'gbento', 'goteo', 'giselecultura@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(74, 'ffreitas', 'urban-social-design-database', 'flavia.frr@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(75, 'ffreitas', 'move-commons', 'flavia.frr@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(76, 'ffreitas', 'goteo', 'flavia.frr@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(77, 'bsampayo', 'urban-social-design-database', 'blancasampayo@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(78, 'bsampayo', 'move-commons', 'blancasampayo@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(79, 'bsampayo', 'nodo-movil', 'blancasampayo@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(80, 'mpedroche', 'urban-social-design-database', 'info@mercedespedroche.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(81, 'mpedroche', 'nodo-movil', 'info@mercedespedroche.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(82, 'mpedroche', 'robocicla', 'info@mercedespedroche.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(83, 'maaban', 'urban-social-design-database', 'manuel.aban@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(84, 'maaban', 'nodo-movil', 'manuel.aban@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(85, 'maaban', 'goteo', 'manuel.aban@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(86, 'mgarcia', 'urban-social-design-database', 'miriamgsanz@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(87, 'mgarcia', 'nodo-movil', 'miriamgsanz@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(88, 'mgarcia', 'goteo', 'miriamgsanz@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(89, 'tguido', 'urban-social-design-database', 'tguido@transit.es', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(90, 'tguido', 'nodo-movil', 'tguido@transit.es', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(91, 'tguido', 'goteo', 'tguido@transit.es', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(92, 'blozano', 'urban-social-design-database', 'betanialozano@yahoo.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(93, 'blozano', 'nodo-movil', 'betanialozano@yahoo.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(94, 'blozano', 'goteo', 'betanialozano@yahoo.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(95, 'cphernandez', 'urban-social-design-database', 'patriciavergara83@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(96, 'cphernandez', 'robocicla', 'patriciavergara83@hotmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(97, 'cphernandez', 'goteo', 'patriciavergara83@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(98, 'cmartinez', 'urban-social-design-database', 'cayetana109@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(99, 'cmartinez', 'goteo', 'cayetana109@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(100, 'jlespina', 'urban-social-design-database', 'espinajl@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(101, 'jlespina', 'goteo', 'espinajl@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(102, 'amorales', 'urban-social-design-database', 'moralespartida@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(103, 'amorales', 'goteo', 'moralespartida@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(104, 'jmorer', 'urban-social-design-database', 'julia.morer@gmail.com', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(105, 'mmikirdistan', 'archinhand-architecture-in-your-hand', 'idensitat@idensitat.org', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(106, 'mmikirdistan', 'move-commons', 'idensitat@idensitat.org', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(107, 'mmikirdistan', 'nodo-movil', 'idensitat@idensitat.org', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(108, 'mmikirdistan', 'goteo', 'idensitat@idensitat.org', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(109, 'evandellos', 'archinhand-architecture-in-your-hand', 'emma.vandellos@esade.edu', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(110, 'evandellos', 'nodo-movil', 'emma.vandellos@esade.edu', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(111, 'fcingolani', 'archinhand-architecture-in-your-hand', 'fc@ecosistemaurbano.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(112, 'fcingolani', 'nodo-movil', 'fc@ecosistemaurbano.com', 15, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(113, 'pgonzalo', 'archinhand-architecture-in-your-hand', 'pilar.gonzalo@fulbrightmail.org', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(114, 'rsalas', 'hkp', 'robers_alas@yahoo.es', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(115, 'rsalas', 'nodo-movil', 'robers_alas@yahoo.es', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(116, 'smeschede', 'hkp', 'soren@hablarenarte.com', 15, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(117, 'amartinez', 'move-commons', 'martinezrubioo@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(118, 'amartinez', 'nodo-movil', 'martinezrubioo@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(119, 'amartinez', 'goteo', 'martinezrubioo@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(120, 'aceballos', 'move-commons', 'veyota79@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(121, 'aceballos', 'nodo-movil', 'veyota79@hotmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(122, 'aceballos', 'goteo', 'veyota79@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(123, 'esenabre', 'move-commons', 'esenabre@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(124, 'esenabre', 'nodo-movil', 'esenabre@gmail.com', 15, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(125, 'carlaboserman', 'move-commons', 'carlaboserman@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(126, 'carlaboserman', 'goteo', 'carlaboserman@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(127, 'mduran', 'move-commons', 'magdaduran@yahoo.es', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(128, 'mduran', 'goteo', 'magdaduran@yahoo.es', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(129, 'kventura', 'move-commons', 'dinkha@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(130, 'kventura', 'goteo', 'dinkha@hotmail.com', 15, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(131, 'dcabo', 'move-commons', 'david.cabo@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(132, 'dcabo', 'goteo', 'david.cabo@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(133, 'elopez', 'move-commons', 'elvirilay@hotmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(134, 'elopez', 'goteo', 'elvirilay@hotmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(135, 'eportillo', 'nodo-movil', 'portillo.esperanza@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(136, 'eportillo', 'robocicla', 'portillo.esperanza@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(137, 'eportillo', 'goteo', 'portillo.esperanza@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(138, 'mkekejian', 'nodo-movil', 'mkekejih@cajamadrid.es', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(139, 'mkekejian', 'robocicla', 'mkekejih@cajamadrid.es', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(140, 'mkekejian', 'goteo', 'mkekejih@cajamadrid.es', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(141, 'yriquelme', 'nodo-movil', 'yolandariquel@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(142, 'yriquelme', 'robocicla', 'yolandariquel@hotmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(143, 'yriquelme', 'goteo', 'yolandariquel@hotmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(144, 'jnora', 'nodo-movil', 'nora_julian@hotmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(145, 'jnora', 'robocicla', 'nora_julian@hotmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(146, 'fingrassia', 'nodo-movil', 'francoingrassia@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(147, 'fingrassia', 'robocicla', 'francoingrassia@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(148, 'jmatadero', 'nodo-movil', 'javi@mataderomadrid.org', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(149, 'jmatadero', 'robocicla', 'javi@mataderomadrid.org', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(150, 'gbossio', 'nodo-movil', 'gabrielabossio@gmail.com', 10, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(151, 'gbossio', 'goteo', 'gabrielabossio@gmail.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(152, 'gbezanilla', 'nodo-movil', 'gerardo@beusual.com', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(153, 'vtorre', 'nodo-movil', 'victortorrevaquero@yahoo.es', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(154, 'bramos', 'robocicla', 'beatriz@iniciativajovn.org', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(155, 'avigara', 'robocicla', 'ana.vigara@iniciativajoven.org', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(156, 'lcarretero', 'goteo', 'foto@luciacarretero.com', 5, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(157, 'sgrueso', 'goteo', 'stephanegrueso@gmail.com', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(158, 'emonivas', 'goteo', 'esther.monivas@gmail.com', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(159, 'lfernandez', 'goteo', 'laura@medialab-prado.es', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(160, 'arecio', 'goteo', 'anto@filosomatika.net', 20, 1, 0, 1, '2011-07-07', '2011-07-07', NULL, '', '', '', 'cash', 'root', 0);
INSERT INTO `invest` VALUES(161, 'stena', 'move-commons', 'sara.tena@aupex.org', 5, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'goteo', 1);
INSERT INTO `invest` VALUES(162, 'root', '2c667d6a62707f369bad654174116a1e', 'julian_1302552287_per@gmail.com', 200, 0, NULL, NULL, '2011-07-08', NULL, NULL, 'PA-0RD68626VK262300D', NULL, NULL, 'paypal', NULL, NULL);
INSERT INTO `invest` VALUES(163, 'olivier', 'fe99373e968b0005e5c2406bc41a3528', 'olivierschulbaum@platoniq.net', 45, 0, NULL, NULL, '2011-07-08', NULL, NULL, NULL, NULL, NULL, 'paypal', NULL, NULL);
INSERT INTO `invest` VALUES(164, 'olivier', 'fe99373e968b0005e5c2406bc41a3528', 'olivierschulbaum@platoniq.net', 150, 0, NULL, NULL, '2011-07-08', NULL, NULL, '1649499', NULL, NULL, 'tpv', NULL, NULL);
INSERT INTO `invest` VALUES(165, 'olivier', 'fe99373e968b0005e5c2406bc41a3528', 'olivierschulbaum@platoniq.net', 150, 0, NULL, NULL, '2011-07-08', NULL, NULL, '1656241', NULL, NULL, 'tpv', NULL, NULL);
INSERT INTO `invest` VALUES(166, 'olivier', 'fe99373e968b0005e5c2406bc41a3528', 'olivierschulbaum@platoniq.net', 150, 0, NULL, NULL, '2011-07-08', NULL, NULL, '1669479', NULL, NULL, 'tpv', NULL, NULL);
INSERT INTO `invest` VALUES(167, 'olivier', 'a565092b772c29abc1b92f999af2f2fb', 'olivierschulbaum@platoniq.net', 40, 0, NULL, NULL, '2011-07-15', NULL, NULL, '1676562', NULL, NULL, 'tpv', NULL, NULL);
INSERT INTO `invest` VALUES(168, 'root', 'a565092b772c29abc1b92f999af2f2fb', 'julian_1302552287_per@gmail.com', 100, 0, NULL, NULL, '2011-07-20', NULL, NULL, '1684300', NULL, NULL, 'tpv', NULL, NULL);
INSERT INTO `invest` VALUES(169, 'root', 'a565092b772c29abc1b92f999af2f2fb', 'julian_1302552287_per@gmail.com', 100, 0, NULL, NULL, '2011-07-20', NULL, NULL, '1694382', NULL, NULL, 'tpv', NULL, NULL);
INSERT INTO `invest` VALUES(170, 'goteo', 'tutorial-goteo', 'goteo@doukeshi.org', 30, 0, NULL, NULL, '2011-08-01', NULL, NULL, NULL, NULL, NULL, 'cash', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest_address`
--

DROP TABLE IF EXISTS `invest_address`;
CREATE TABLE `invest_address` (
  `invest` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dirección de entrega de recompensa';

--
-- Volcar la base de datos para la tabla `invest_address`
--

INSERT INTO `invest_address` VALUES(1, 'goteo', 'C/ Montalegre, 5', '08001', 'Barcelona', 'España');
INSERT INTO `invest_address` VALUES(2, 'root', 'blai', '08001', 'barcelona', 'España');
INSERT INTO `invest_address` VALUES(161, 'stena', '', '', '', '');
INSERT INTO `invest_address` VALUES(162, 'root', 'covadonga 7', '08007', 'Palma de Mallorca', 'España');
INSERT INTO `invest_address` VALUES(163, 'olivier', 'paris la nuit', '07880', 'moscou', 'russie');
INSERT INTO `invest_address` VALUES(164, 'olivier', 'calle paris', '0', 'moscow', 'russie');
INSERT INTO `invest_address` VALUES(165, 'olivier', 'calle paris', '0', 'moscow', 'russie');
INSERT INTO `invest_address` VALUES(166, 'olivier', 'calle paris', '0', 'moscow', 'russie');
INSERT INTO `invest_address` VALUES(167, 'olivier', '', '', 'Palma de Mallorca', 'España');
INSERT INTO `invest_address` VALUES(168, 'root', 'calle X', '05000', 'aiguafreda', 'España');
INSERT INTO `invest_address` VALUES(169, 'root', 'calle X', '05000', 'aiguafreda', 'España');
INSERT INTO `invest_address` VALUES(170, 'goteo', 'C/ Montealegre, 5', '8001', 'Barcelona', 'España');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest_reward`
--

DROP TABLE IF EXISTS `invest_reward`;
CREATE TABLE `invest_reward` (
  `invest` bigint(20) unsigned NOT NULL,
  `reward` bigint(20) unsigned NOT NULL,
  `fulfilled` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `invest` (`invest`,`reward`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';

--
-- Volcar la base de datos para la tabla `invest_reward`
--

INSERT INTO `invest_reward` VALUES(162, 153, 0);
INSERT INTO `invest_reward` VALUES(163, 66, 0);
INSERT INTO `invest_reward` VALUES(164, 66, 0);
INSERT INTO `invest_reward` VALUES(165, 66, 0);
INSERT INTO `invest_reward` VALUES(166, 66, 0);
INSERT INTO `invest_reward` VALUES(167, 59, 0);
INSERT INTO `invest_reward` VALUES(168, 59, 0);
INSERT INTO `invest_reward` VALUES(169, 59, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lang`
--

DROP TABLE IF EXISTS `lang`;
CREATE TABLE `lang` (
  `id` varchar(2) NOT NULL COMMENT 'Código ISO-639',
  `name` varchar(20) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Idiomas';

--
-- Volcar la base de datos para la tabla `lang`
--

INSERT INTO `lang` VALUES('ca', 'Català', 0);
INSERT INTO `lang` VALUES('de', 'Deutsch', 0);
INSERT INTO `lang` VALUES('en', 'English', 0);
INSERT INTO `lang` VALUES('es', 'Español', 1);
INSERT INTO `lang` VALUES('eu', 'Euskara', 0);
INSERT INTO `lang` VALUES('fr', 'Français', 0);
INSERT INTO `lang` VALUES('gl', 'Galego', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `license`
--

DROP TABLE IF EXISTS `license`;
CREATE TABLE `license` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` tinytext,
  `group` varchar(50) DEFAULT NULL COMMENT 'grupo de restriccion de menor a mayor',
  `url` varchar(256) DEFAULT NULL,
  `order` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licencias de distribucion';

--
-- Volcar la base de datos para la tabla `license`
--

INSERT INTO `license` VALUES('agpl', 'Affero General Public License', 'Licencia pública general de Affero para software libre que corra en servidores de red', '', 'http://www.affero.org/oagf.html', 2);
INSERT INTO `license` VALUES('apache', 'Apache License', 'Licencia Apache de software libre, que no exige que las obras derivadas se distribuyan usando la misma licencia ni como software libre', '', 'http://www.apache.org/licenses/LICENSE-2.0', 10);
INSERT INTO `license` VALUES('balloon', 'Balloon Open Hardware License', 'Licencia para hardware libre de los procesadores Balloon', '', 'http://balloonboard.org/licence.html', 20);
INSERT INTO `license` VALUES('bsd', 'Berkeley Software Distribution', 'Licencia de software libre permisiva, con pocas restricciones y que permite el uso del código fuente en software no libre', 'open', 'http://es.wikipedia.org/wiki/Licencia_BSD', 5);
INSERT INTO `license` VALUES('cc0', 'CC0 Universal (Dominio Público)', 'Obra dedicada al dominio público, mediante renuncia a todos los derechos de autoría sobre la misma', '', 'http://creativecommons.org/publicdomain/zero/1.0/deed.es', 25);
INSERT INTO `license` VALUES('ccby', 'CC - Reconocimiento', 'Licencia de bienes comunes creativos, con reconocimiento de autoría', 'open', 'http://creativecommons.org/licenses/by/2.0/deed.es_ES', 12);
INSERT INTO `license` VALUES('ccbync', 'CC - Reconocimiento - NoComercial', 'Licencia de bienes comunes creativos, con reconocimiento de autoría y sin que se pueda hacer uso comercial', '', 'http://creativecommons.org/licenses/by-nc/2.0/deed.es_ES', 13);
INSERT INTO `license` VALUES('ccbyncnd', 'CC - Reconocimiento - NoComercial - SinObraDerivada', 'Licencia de bienes comunes creativos, con reconocimiento de autoría, sin que se pueda hacer uso comercial ni otras obras derivadas', '', 'http://creativecommons.org/licenses/by-nc-nd/2.0/deed.es_ES', 15);
INSERT INTO `license` VALUES('ccbyncsa', 'CC - Reconocimiento - NoComercial - CompartirIgual', 'Licencia de bienes comunes creativos, con reconocimiento de autoría, sin que se pueda hacer uso comercial y a compartir en idénticas condiciones', '', 'http://creativecommons.org/licenses/by-nc-sa/3.0/deed.es_ES', 14);
INSERT INTO `license` VALUES('ccbynd', 'CC - Reconocimiento - SinObraDerivada', 'Licencia de bienes comunes creativos, con reconocimiento de autoría, sin que se puedan hacer obras derivadas ', '', 'http://creativecommons.org/licenses/by-nd/2.0/deed.es_ES', 17);
INSERT INTO `license` VALUES('ccbysa', 'CC - Reconocimiento - CompartirIgual', 'Licencia de bienes comunes creativos, con reconocimiento de autoría y a compartir en idénticas condiciones', 'open', 'http://creativecommons.org/licenses/by-sa/2.0/deed.es_ES', 16);
INSERT INTO `license` VALUES('fal', 'Free Art License', 'Licencia de arte libre', '', 'http://artlibre.org/licence/lal/es', 11);
INSERT INTO `license` VALUES('fdl', 'Free Documentation License ', 'Licencia de documentación libre de GNU, pudiendo ser ésta copiada, redistribuida, modificada e incluso vendida siempre y cuando se mantenga bajo los términos de esa misma licencia', 'open', 'http://www.gnu.org/copyleft/fdl.html', 4);
INSERT INTO `license` VALUES('freebsd', 'FreeBSD Documentation License', 'Licencia de documentación libre para el sistema operativo FreeBSD', 'open', 'http://www.freebsd.org/copyright/freebsd-doc-license.html', 6);
INSERT INTO `license` VALUES('gpl', 'General Public License', 'Licencia Pública General de GNU para la libre distribución, modificación y uso de software', 'open', 'http://www.gnu.org/licenses/gpl.html', 1);
INSERT INTO `license` VALUES('lgpl', 'Lesser General Public License', 'Licencia Pública General Reducida de GNU, para software libre que puede ser utilizado por un programa no-GPL, que a su vez puede ser software libre o no', 'open', 'http://www.gnu.org/copyleft/lesser.html', 3);
INSERT INTO `license` VALUES('mit', 'MIT / X11 License', 'Licencia tanto para software libre como para software no libre, que permite no liberar los cambios realizados sobre el programa original', '', 'http://es.wikipedia.org/wiki/MIT_License', 8);
INSERT INTO `license` VALUES('mpl', 'Mozilla Public License', 'Licencia pública de Mozilla de software libre, que posibilita la reutilización no libre del software, sin restringir la reutilización del código ni el relicenciamiento bajo la misma licencia', '', 'http://www.mozilla.org/MPL/', 7);
INSERT INTO `license` VALUES('odbl', 'Open Database License ', 'Licencia de base de datos abierta, que permite compartir, modificar y utilizar bases de datos en idénticas condiciones', 'open', 'http://www.opendatacommons.org/licenses/odbl/', 22);
INSERT INTO `license` VALUES('odcby', 'Open Data Commons Attribution License', 'Licencia de datos abierta, que permite compartir, modificar y utilizar los datos en idénticas condiciones atribuyendo la fuente original', 'open', 'http://www.opendatacommons.org/licenses/by/', 23);
INSERT INTO `license` VALUES('oshw', 'TAPR Open Hardware License', 'Licencia para obras de hardware libre', 'open', 'http://www.tapr.org/OHL', 18);
INSERT INTO `license` VALUES('pd', 'Dominio público', 'La obra puede ser libremente reproducida, distribuida, transmitida, usada, modificada, editada u objeto de cualquier otra forma de explotación para el propósito que sea, comercial o no.', '', 'http://creativecommons.org/licenses/publicdomain/deed.es', 24);
INSERT INTO `license` VALUES('php', 'PHP License', 'Licencia bajo la que se publica el lenguaje de programación PHP', '', 'http://www.php.net/license/', 9);
INSERT INTO `license` VALUES('tapr', 'TAPR Noncommercial Hardware License', 'Licencia para obras de hardware libre con limitación en su comercialización ', '', 'http://www.tapr.org/NCL.html', 19);
INSERT INTO `license` VALUES('xoln', 'Procomún de la XOLN', 'Licencia de red abierta, libre y neutral, como acuerdo de interconexión entre iguales promovido por Guifi.net', 'open', 'http://guifi.net/es/ProcomunXOLN', 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `license_lang`
--

DROP TABLE IF EXISTS `license_lang`;
CREATE TABLE `license_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` tinytext,
  `url` varchar(256) DEFAULT NULL,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `license_lang`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `thread` bigint(20) unsigned DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede modificar ni borrar',
  `closed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede responder',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Mensajes de usuarios en proyecto' AUTO_INCREMENT=110 ;

--
-- Volcar la base de datos para la tabla `message`
--

INSERT INTO `message` VALUES(27, 'diegobus', 'fe99373e968b0005e5c2406bc41a3528', NULL, '2011-06-01 12:21:22', 'Espacio taller: Donec ultrices libero in est tincidunt placerat tempor mi mattis. Vestibulum at aliquam lacus. Suspendisse condimentum metus vel arcu faucibus id volutpat arcu ultricies. Nulla pellentesque mi at dolor accumsan accumsan. ', 0, 0);
INSERT INTO `message` VALUES(52, 'diegobus', 'fe99373e968b0005e5c2406bc41a3528', NULL, '2011-06-17 13:25:47', 'Espacio taller: Donec ultrices libero in est tincidunt placerat tempor mi mattis. Vestibulum at aliquam lacus. Suspendisse condimentum metus vel arcu faucibus id volutpat arcu ultricies. Nulla pellentesque mi at dolor accumsan accumsan. ', 1, 0);
INSERT INTO `message` VALUES(54, 'olivier', 'fe99373e968b0005e5c2406bc41a3528', NULL, '2011-06-19 14:23:43', 'tengo un taller', 0, 0);
INSERT INTO `message` VALUES(75, 'esenabre', 'pliegos', NULL, '2011-07-04 18:26:39', 'Pilas: Dejadmelas :)', 1, 0);
INSERT INTO `message` VALUES(76, 'olivier', '2c667d6a62707f369bad654174116a1e', 56, '2011-07-04 19:32:15', 'gracisa por ofrecerme tu furgo', 0, 0);
INSERT INTO `message` VALUES(77, 'olivier', '2c667d6a62707f369bad654174116a1e', 57, '2011-08-01 14:30:46', '¿tienes pinceles azules?', 0, 0);
INSERT INTO `message` VALUES(78, 'goteo', 'a565092b772c29abc1b92f999af2f2fb', NULL, '2011-08-01 14:30:46', 'Beta-testers y difusores: Son bienvenidas la ayuda de difusión para que mucha gente conozca la  herramienta y participe en las campañas. También se necesitá en determinados momentos hacer pruebas masivas para ver la resistencia de la aplicación. ', 1, 0);
INSERT INTO `message` VALUES(79, 'goteo', 'a565092b772c29abc1b92f999af2f2fb', NULL, '2011-08-01 14:30:46', 'Servidores: Aunque contamos con un servidor, dependiendo del numero de vistas y usuarios de la herramienta, necesitaremos patrocinadores para hacer más fácil el mantenimiento del proyecto y que pueda continuar siendo de uso gratuito.', 1, 0);
INSERT INTO `message` VALUES(80, 'carlaboserman', 'robocicla', NULL, '2011-07-05 19:16:05', 'Traductor: Traductor@ (ingles / portugues / italiano / frances / griego)', 1, 0);
INSERT INTO `message` VALUES(81, 'efoglia', 'nodo-movil', NULL, '2011-08-01 14:30:46', 'Desarrolladores: Desarrolladores de Exo.cat, grupo Manet. Expertos en streaming y telefonía móvil..', 1, 0);
INSERT INTO `message` VALUES(82, 'efoglia', 'nodo-movil', NULL, '2011-08-01 14:30:46', 'Espacio de trabajo: Sala de hackeo / trabajo / reunión. para 10 personas.', 1, 0);
INSERT INTO `message` VALUES(83, 'efoglia', 'nodo-movil', NULL, '2011-08-01 14:30:46', 'Espacio público: Espacio público (accesible para trabajar ).', 1, 0);
INSERT INTO `message` VALUES(87, 'domenico', 'urban-social-design-database', NULL, '2011-07-05 22:55:49', 'Viajero: estudiante becario para viajar por europa y dar a conocer el proyecto', 1, 0);
INSERT INTO `message` VALUES(88, 'domenico', 'urban-social-design-database', NULL, '2011-07-05 22:55:49', 'film maker: film maker', 1, 0);
INSERT INTO `message` VALUES(89, 'domenico', 'urban-social-design-database', NULL, '2011-07-05 22:55:49', 'espacio: plaza de trabajo en oficina compartida', 1, 0);
INSERT INTO `message` VALUES(90, 'domenico', 'urban-social-design-database', NULL, '2011-07-05 22:55:49', 'camara video: camara video', 1, 0);
INSERT INTO `message` VALUES(91, 'domenico', 'urban-social-design-database', NULL, '2011-07-05 22:55:49', 'hosting profesional: hosting profesional', 1, 0);
INSERT INTO `message` VALUES(93, 'olivier', '2c667d6a62707f369bad654174116a1e', NULL, '2011-07-08 16:11:02', 'pincel: pinceles', 1, 0);
INSERT INTO `message` VALUES(94, 'olivier', '2c667d6a62707f369bad654174116a1e', NULL, '2011-07-08 16:11:02', 'alguin q sepa escribir: redactar textos', 1, 0);
INSERT INTO `message` VALUES(95, 'olivier', '2c667d6a62707f369bad654174116a1e', NULL, '2011-07-08 16:11:02', 'nueva colab editada desde el dahboard proyecto BROOKLYN: a ver si se ve', 1, 0);
INSERT INTO `message` VALUES(96, 'olivier', '2c667d6a62707f369bad654174116a1e', NULL, '2011-07-08 16:15:26', 'ayuda para poner en marcha este ajax!!!: ayuda para poner en marcha este ajax!!!', 1, 0);
INSERT INTO `message` VALUES(97, 'olivier', '2c667d6a62707f369bad654174116a1e', NULL, '2011-08-01 14:30:46', 'Nueva colaboración: ', 1, 0);
INSERT INTO `message` VALUES(98, 'olivier', '2c667d6a62707f369bad654174116a1e', NULL, '2011-07-08 16:11:02', 'Nueva tarea: una tarea guay', 1, 0);
INSERT INTO `message` VALUES(99, 'olivier', '2c667d6a62707f369bad654174116a1e', NULL, '2011-07-08 16:15:26', 'TAREA DESDE DASHBOAR: FUNCIONA', 1, 0);
INSERT INTO `message` VALUES(100, 'ebaraonap', 'archinhand-architecture-in-your-hand', NULL, '2011-07-13 19:57:50', 'Testers de la herramienta: Estudiantes de arquitectura', 1, 0);
INSERT INTO `message` VALUES(101, 'acomunes', 'move-commons', NULL, '2011-08-01 14:30:46', 'Diseñador gráfico: Diseñador gráfico', 1, 0);
INSERT INTO `message` VALUES(102, 'acomunes', 'move-commons', NULL, '2011-08-01 14:30:46', 'Traductores a múltiples idiomas (20h/c): Traductores a múltiples idiomas (20h/c)', 1, 0);
INSERT INTO `message` VALUES(103, 'acomunes', 'move-commons', NULL, '2011-07-13 19:58:15', 'Testers: Colectivos', 1, 0);
INSERT INTO `message` VALUES(106, 'demo', 'tweetometro', NULL, '2011-08-01 14:30:46', 'Beta-testers y difusores : Son bienvenidas la ayuda de difusión para que mucha gente conozca la herramienta y participe en las campañas. También se necesitará en determinados momentos hacer pruebas masivas para ver la resistencia de la aplicación. ', 1, 0);
INSERT INTO `message` VALUES(107, 'demo', 'tweetometro', NULL, '2011-08-01 14:30:46', 'Servidores: Aunque contamos con un servidor, dependiendo del numero de vistas y usuarios de la herramienta, necesitaremos "patrocinadores" en espacio web para hacer más fácil el mantenimiento del proyecto y que pueda continuar siendo de uso gratuito.', 1, 0);
INSERT INTO `message` VALUES(108, 'demo', 'tutorial-goteo', NULL, '2011-08-01 14:30:46', 'Colaboración en una tarea: Colaboración que requiera una habilidad para una tarea específica (traducciones, gestiones, difusión, etc), ya sea a distancia mediante ordenador o bien presencialmente.', 1, 0);
INSERT INTO `message` VALUES(109, 'demo', 'tutorial-goteo', NULL, '2011-08-01 14:30:46', 'Colaboración en un préstamo: Préstamo temporal de material necesario para el proyecto, o bien de cesión de infraestructuras o espacios por un periodo determinado. También puede implicar préstamos permanentes, o sea regalos :)', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `description` text COMMENT 'Entradilla',
  `url` tinytext NOT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Noticias en la cabecera' AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `news`
--

INSERT INTO `news` VALUES(1, '"El crowdfunding como caballo de Troya del procomún". Post en CCCBlab', '¿Cómo crecer en un ecosistema de tanta competencia en la atención global, tantas plataformas de crowdfunding aspirando a captar fondos de los usuarios? Especializándose en un objetivo concreto: Goteo permite financiar exclusivamente proyectos que ofrezcan algún tipo de retorno colectivo. Que estén regidos total o parcialmente por licencias copyleft, como las de Creative Commons o similares, y por tanto que se pueda aprender sobre cómo han sido hechos y también remezclarlos, incorporarlos a un proceso o producto diferente.', 'http://t.co/BQESb7T', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news_lang`
--

DROP TABLE IF EXISTS `news_lang`;
CREATE TABLE `news_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `url` tinytext,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `news_lang`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `node`
--

DROP TABLE IF EXISTS `node`;
CREATE TABLE `node` (
  `id` varchar(50) NOT NULL,
  `name` varchar(256) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodos';

--
-- Volcar la base de datos para la tabla `node`
--

INSERT INTO `node` VALUES('goteo', 'Master node', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `page`
--

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `description` text,
  `url` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Páginas institucionales';

--
-- Volcar la base de datos para la tabla `page`
--

INSERT INTO `page` VALUES('about', 'Sobre Goteo', 'Sobre Goteo', '/about');
INSERT INTO `page` VALUES('beta', 'Betatesting', 'Estamos en fase beta', '/about/beta');
INSERT INTO `page` VALUES('campaign', 'Servicio Campañas', 'Descripción de servicio de campañas', '/service/campaign');
INSERT INTO `page` VALUES('community', 'Comunidad Goteo', 'Contenido seccion comunidad', '/community');
INSERT INTO `page` VALUES('consulting', 'Servicio Consultoría', 'Descripción de servicio de consultoría', '/service/consulting');
INSERT INTO `page` VALUES('contact', 'Contacto', 'Pagina de contacto', '/about/contact');
INSERT INTO `page` VALUES('credits', 'Créditos', 'Créditos', '/about/credits');
INSERT INTO `page` VALUES('dashboard', 'Bienvenida', 'Texto de bienvenida en el dashboard', '/dashboard');
INSERT INTO `page` VALUES('howto', 'Instrucciones', 'Instrucciones para ser productor', '/about/howto');
INSERT INTO `page` VALUES('legal', 'Legales', 'Terminos y condiciones de uso', '/about/legal');
INSERT INTO `page` VALUES('news', 'Noticias', 'Pagina de noticias', '/news');
INSERT INTO `page` VALUES('service', 'Servicios', 'General de servicios', '/service');
INSERT INTO `page` VALUES('team', 'Equipo', 'Sobre la gente detrás de Goteo', '/about/team');
INSERT INTO `page` VALUES('workshop', 'Servicio Talleres', 'Descripción de servicio de talleres', '/service/workshop');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `page_node`
--

DROP TABLE IF EXISTS `page_node`;
CREATE TABLE `page_node` (
  `page` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `content` longtext,
  UNIQUE KEY `page` (`page`,`node`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contenidos de las paginas';

--
-- Volcar la base de datos para la tabla `page_node`
--

INSERT INTO `page_node` VALUES('about', 'goteo', 'es', '<p>\r\n	<span id="AMA22-1" style="font-weight: bold;">Qu&eacute; es Goteo</span></p>\r\n<p>\r\n	Existen cada vez m&aacute;s proyectos desarrollados por agentes creativos, que superan el &aacute;mbito convencional de la Cultura. Proyectos con un alto componente de innovaci&oacute;n, con un gran potencial de incidencia social y econ&oacute;mica, con capacidad de crecimiento y reproducci&oacute;n, para generar valor en el sentido m&aacute;s amplio de la palabra. Pero en el estado espa&ntilde;ol siguen faltando canales de comunicaci&oacute;n-relaci&oacute;n entre las personas creativas, los agentes sociales y culturales y posibles microdonantes e inversores. Seguimos supeditados a canales convencionales como la subvenci&oacute;n o el patrocinio, que necesitan ser repensados.<br />\r\n	Ese es el objetivo de GOTEO, constituirse en ese canal. Una plataforma en red, que ponga en relaci&oacute;n, de un modo eficiente y transparente, a diversos agentes p&uacute;blicos y privados con distintas funciones; que aglutine necesidades y posibles soluciones; y que facilite un cat&aacute;logo de fuentes de financiaci&oacute;n, infraestructuras y otros recursos.</p>\r\n');
INSERT INTO `page_node` VALUES('beta', 'goteo', 'ca', '<h3>\r\n	Explicaci&oacute;n</h3>\r\n<p>\r\n	Estamos en fase beta de testeo y los aportes son de prueba. Solamente usuarios betatesters pueden realizar aportes de prueba.</p>\r\n');
INSERT INTO `page_node` VALUES('beta', 'goteo', 'es', '<h3>\r\n	Explicaci&oacute;n</h3>\r\n<p>\r\n	Estamos en fase beta de testeo y los aportes son de prueba. Solamente usuarios betatesters pueden realizar aportes de prueba.</p>\r\n');
INSERT INTO `page_node` VALUES('community', 'goteo', 'es', 'Contenido seccion comunidad');
INSERT INTO `page_node` VALUES('contact', 'goteo', 'es', '<p>\r\n	Texto explicativo en la p&aacute;gina de contacto. Se gestiona desde las p&aacute;ginas institucionales</p>\r\n');
INSERT INTO `page_node` VALUES('credits', 'goteo', 'es', '<p>\r\n	Desarrollado por <a href="http://onliners-web.com" target="_blank" title="Onliners Web Development">Onliners Web Development</a></p>\r\n<p>\r\n	&nbsp;</p>\r\n');
INSERT INTO `page_node` VALUES('dashboard', 'goteo', 'es', '<p>\r\n	Hola %USER_NAME%,<br />\r\n	bienvenido a tu panel.</p>\r\n<p>\r\n	Desde aqu&iacute; podr&aacute;s administrar la informaci&oacute;n p&uacute;blica de tu perfil y de tu proyecto, a modo de centro de operaciones para dinamizarlo y gestionarlo. Se pueden publicar novedades sobre los avances del proyecto, a&ntilde;adir fotos y v&iacute;deos, clasificar las aportaciones de los cofinanciadores y gestionar los env&iacute;os de las recompensas individuales.<br />\r\n	<br />\r\n	Ese panel de control tambi&eacute;n permite ver toda la informaci&oacute;n detallada sobre c&oacute;mo evoluciona la recaudaci&oacute;n y los apoyos recibidos, y posteriormente comunicarse con los usuarios que hayan cofinanciado el proyecto.</p>\r\n');
INSERT INTO `page_node` VALUES('howto', 'goteo', 'es', '<h3>\r\n	4 condiciones y 2 requisitos para proponer un proyecto</h3>\r\n<p>\r\n	Goteo es una plataforma para apoyar proyectos de emprendedores, innovadores sociales y creativos que tengan entre sus objetivos, formato y/o resultado final, de forma total o significativa, alg&uacute;n tipo de retorno colectivo regido por una licencia libre o abierta (por ejemplo Creative Commons o GPL).</p>\r\n<p>\r\n	Esto es, proyectos con &quot;ADN abierto&quot; en los que se comparte informaci&oacute;n, conocimiento, contenidos digitales y/u otros recursos relacionados con la actividad para la que se busca financiaci&oacute;n.<br />\r\n	<br />\r\n	Goteo se gu&iacute;a por las siguientes condiciones y requisitos, que debes conocer si quieres proponer un proyecto para que opte a ser cofinanciado y recibir la ayuda de la comunidad de Goteo. Si necesitas m&aacute;s informaci&oacute;n sobre cualquiera de los siguientes puntos te recomendamos leer <a href="http://beta.goteo.org/faq#q25">nuestras FAQ</a>.</p>\r\n<p>\r\n	<strong>Condiciones</strong></p>\r\n<p>\r\n	<strong>1.</strong> Cuando mi proyecto ofrezca recompensas individuales a cambio de aportaciones econ&oacute;micas determinadas, deber&eacute; cumplir con el compromiso establecido con la plataforma y mis cofinanciadores en caso de obtener la financiaci&oacute;n m&iacute;nima solicitada.<br />\r\n	<br />\r\n	<strong>2</strong>. Deber&eacute; cumplir igualmente con el compromiso de publicar los retornos colectivos prometidos, enlaz&aacute;ndolos desde la plataforma Goteo bajo la licencia elegida en el momento de solicitar la financiaci&oacute;n, en cumplimiento de un contrato legal con la Fundaci&oacute;n Goteo.<br />\r\n	<br />\r\n	<strong>3.</strong> Solicitar&eacute; una cofinanciaci&oacute;n m&iacute;nima para llevar a cabo el proyecto y otra &oacute;ptima. La recaudaci&oacute;n de la cofinanciaci&oacute;n m&iacute;nima coincidir&aacute; con el inicio de la producci&oacute;n, sobre la que deber&eacute; ir informando peri&oacute;dicamente, lo que me permitir&aacute; emprender una segunda ronda de cofinanciaci&oacute;n hasta llegar a la financiaci&oacute;n &oacute;ptima. &nbsp;<br />\r\n	<br />\r\n	<strong>4</strong>. La finalidad del proyecto no es la venta encubierta de productos o servicios ya producidos, ni de financiar campa&ntilde;as de beneficencia, pol&iacute;ticas o de cualquier otro tipo, ni delictiva o para atentar contra la dignidad de las personas.</p>\r\n<p>\r\n	<strong>Requisitos</strong></p>\r\n<ul>\r\n	<li>\r\n		Soy mayor de 18 a&ntilde;os.</li>\r\n	<li>\r\n		Dispongo de una cuenta bancaria en Espa&ntilde;a.</li>\r\n</ul>\r\n<p>\r\n<form action="/project/create" method="post">\r\n<input class="checkbox" id="create_accept" name="confirm" type="checkbox" value="true" /> <label class="unselected" for="create_accept">He leido y entiendo las instrucciones para crear un proyecto en Goteo.</label><br />\r\n<button class="disabled" disabled="disabled" id="create_continue" name="action" type="submit" value="continue">Continuar</button>\r\n</form>\r\n</p>\r\n');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog` bigint(20) unsigned NOT NULL,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  `image` int(10) DEFAULT NULL,
  `date` date NOT NULL COMMENT 'fehca de publicacion',
  `order` int(11) DEFAULT '1',
  `allow` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Permite comentarios',
  `home` tinyint(1) DEFAULT '0' COMMENT 'para los de portada',
  `footer` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Para los del footer',
  `publish` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Publicado',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada' AUTO_INCREMENT=23 ;

--
-- Volcar la base de datos para la tabla `post`
--

INSERT INTO `post` VALUES(1, 1, '¿Porque goteo es diferente?', 'Goteo se distingue principalmente de otras plataformas de financiación colectiva por su apuesta diferencial y focalizada en proyectos de “código abierto” en un sentido amplio. Esto es, que compartan conocimiento, procesos, resultado, responsabilidad o beneficio, desde la filosofía del procomún. Porque Goteo pone el acento en la esfera pública, apoyando proyectos que favorezcan el empoderamiento colectivo y el bien común.\r\n\r\nEs crowdfunding para gente que busque la rentabilidad social de las inversiones efectuadas, haciendo viables los proyectos para sus promotores pero también el retorno colectivo para la comunidad (en paralelo a contraprestaciones individuales). Estos retornos colectivos pueden ser de muy diverso tipo y se fundamentan en compartir conocimiento y dar acceso libre al código fuente, a productos y recursos, a formación a través de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios económicamente sostenibles.\r\n\r\nOtra peculiaridad de Goteo es que permite recaudar fondos mínimos, por un lado, para arrancar los proyectos, y por otro fondos óptimos para fases posteriores, tal como explicamos un par de FAQs más arriba. También el hecho de que pueda funcionar y ser administrada de manera autónoma por nodos temáticos o geográficos distribuidos geográficamente, como detallamos otras FAQ más abajo. O que permita inversiones puntuales de entidades que deseen generar así una especie de bote, a repartir según el criterio de una comunidad de expertos entre proyectos de un nodo determinado.\r\n\r\n¿Más diferencias con otras plataformas? Ésta se aplica su propia receta y también está disponible con su propia licencia copyleft, así como su código fuente (para quien prefiera descargarse la herramienta y adaptarla a sus necesidades de crowdfunding). También estamos trabajando en otras especificaciones, como un panel de seguimiento detallado de fondos obtenidos, para aumentar la transparencia sobre dónde acaba lo recaudado, o una tarifa plana para micromecenas compulsivos.\r\n\r\nEstos retornos colectivos pueden ser de muy diverso tipo y se fundamentan en compartir conocimiento y dar acceso libre al código fuente, a productos y recursos, a formación a través de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios económicamente sostenibles.', 'http://vimeo.com/20597320', 0, '2011-06-08', 1, 1, 1, 1, 1);
INSERT INTO `post` VALUES(3, 1, 'Goteo es una comunidad de comunidades', 'Goteo es una comunidad de comunidades -cuyo nexo de unión es el interés por el fortalecimiento del procomún-, que se articulan en torno a una plataforma digital en internet. Un sistema distribuido de comunidades locales y/o temáticas, especialistas, legitimadas, con un importante calado social, referentes en su ámbito de actuación. Estas comunidades constituyen una red fundamental de nodos de confianza, que sirven para localizar lo digital, aportando proximidad y especificidad, multiplicando el efecto de la plataforma.', '[slideshare id=8175922&doc=cbbhondartzan2-110601120109-phpapp01]', 0, '2011-06-06', 2, 0, 1, 0, 1);
INSERT INTO `post` VALUES(4, 1, 'Goteo. Codiseño con la comunidad', 'Texto pendiente sobre el papel de los talleres en el desarollo de Goteo', '[slideshare id=8533502&doc=cbbhondartzan3-110707082157-phpapp02]', 33, '2011-06-06', 1, 0, 1, 0, 1);
INSERT INTO `post` VALUES(16, 6, 'Primera actualización desde el dashboard', 'aui viene el lorem ipsum famoso. Phasellus vulputate dolor sit amet nulla mollis quis ultricies ante lobortis. Fusce euismod hendrerit nunc vitae bibendum. Vestibulum a mauris non ipsum bibendum dignissim. Sed auctor ligula eget nunc laoreet consequat. Sed blandit faucibus fermentum. Morbi laoreet augue ut augue ullamcorper gravida tincidunt justo laoreet. Proin commodo, sem sit amet accumsan pharetra, felis magna pretium est, vitae dictum leo leo faucibus sem. Pellentesque malesuada condimentum lectus et placerat. Proin ullamcorper leo sed erat mattis dictum. Vestibulum consequat, neque vel ultrices lacinia, tortor nibh sodales quam, nec faucibus velit tortor id lacus. Integer posuere felis metus, ut tincidunt enim. Nulla mattis ante in justo aliquet cursus. Quisque commodo, tortor id porta sollicitudin, justo justo pharetra purus, sed consequat lectus enim sit amet lectus. Sed in ipsum eros, rutrum commodo dui. Nulla sem dolor, laoreet mollis hendrerit id, fermentum eget arcu.<br /><br />\r\n<br /><br />\r\nNulla vitae urna arcu. Integer ac lacus quis magna eleifend facilisis. Etiam vehicula condimentum odio id elementum. Nunc porta molestie viverra. Etiam eget justo leo. Vestibulum non urna a risus interdum malesuada in id velit. Etiam faucibus, sem vel convallis consequat, neque nunc consectetur elit, ut dapibus libero libero et mauris. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec non nibh purus, id feugiat ante. Donec vel velit leo, ac cursus risus. Pellentesque feugiat dui sit amet libero sagittis id tincidunt nulla tincidunt. Etiam libero diam, vulputate sit amet viverra a, mattis eget diam. Pellentesque habitan', '', 68, '2011-07-08', 1, 1, 0, 0, 1);
INSERT INTO `post` VALUES(17, 6, 'segunda publicación desde dashboard con video', 'Phasellus vulputate dolor sit amet nulla mollis quis ultricies ante lobortis. Fusce euismod hendrerit nunc vitae bibendum. Vestibulum a mauris non ipsum bibendum dignissim. Sed auctor ligula eget nunc laoreet consequat. Sed blandit faucibus fermentum. Morbi laoreet augue ut augue ullamcorper gravida tincidunt justo laoreet. Proin commodo, sem sit amet accumsan pharetra, felis magna pretium est, vitae dictum leo leo faucibus sem. Pellentesque malesuada condimentum lectus et placerat. Proin ullamcorper leo sed erat mattis dictum. Vestibulum consequat, neque vel ultrices lacinia, tortor nibh sodales quam, nec faucibus velit tortor id lacus. Integer posuere felis metus, ut tincidunt enim. Nulla mattis ante in justo aliquet cursus. Quisque commodo, tortor id porta sollicitudin, justo justo pharetra purus, sed consequat lectus enim sit amet lectus. Sed in ipsum eros, rutrum commodo dui. Nulla sem dolor, laoreet mollis hendrerit id, fermentum eget arcu.\r\n\r\nNulla vitae urna arcu. Integer ac lacus quis magna eleifend facilisis. Etiam vehicula condimentum odio id elementum. Nunc porta molestie viverra. Etiam eget justo leo. Vestibulum non urna a risus interdum malesuada in id velit. Etiam faucibus, sem vel convallis consequat, neque nunc consectetur elit, ut dapibus libero libero et mauris. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec non nibh purus, id feugiat ante. Donec vel velit leo, ac cursus risus. Pellentesque feugiat dui sit amet libero sagittis id tincidunt nulla tincidunt. Etiam libero diam, vulputate sit amet viverra a, mattis eget diam. Pellentesque habitan', 'http://vimeo.com/26125187', 0, '2011-07-08', 1, 1, 0, 0, 1);
INSERT INTO `post` VALUES(21, 1, 'El taller "Goteo, cultura de la financiación colectiva" sigue rodando', 'El taller "Goteo, cultura de la financiación colectiva (crowdfunding)" sigue rodando.\r\nEl taller Goteo ofrece información teórica sobre las diferentes tipologías de financiación colectiva, acercando de forma dinámica y atractiva al crowdfunding como estrategia de interés tanto para la financiación como para la difusión y generación de comunidad.\r\n\r\nPróxima parada: 24 de junio en Cáceres en el Centro de Conocimiento AldealabC3, edificio Embarcadero.\r\nCómo apuntarse: (plazas limitadas)\r\n609 519 493 (coordinadora Chon Muñoz)\r\n\r\nOrganiza:\r\nProyecto Fénix. Factoría de la Innovación/ Ayuntamiento de Cáceres / Concejalía de Innovación, Fomento y Desarrollo\r\nTecnológico.\r\n\r\nCon la colaboración del Gabinete del Iniciativa Joven de Extremadura.\r\n\r\nEl crowdfunding ha surgido muy recientemente en España y en pocos meses ha demostrado ser una eficaz alternativa de financiación de proyectos. Pero al mismo tiempo tiene efectos secundarios altamente interesantes para el ámbito cultural/social y de TICs, como la generación de comunidades de interés y la capacidad de generar expectativas de forma previa a la presentación final del proyecto.\r\n\r\nLa duración del taller es de cuatro horas, que se distribuyen en dos partes:\r\n\r\na) exposición del intensivo estudio realizado por Platoniq sobre diferentes tipologías de plataformas, diferenciando\r\nmicrofinanciación, financiación colectiva (crowdfunding) y préstamos p2p.\r\n\r\nb) parte práctica en la que se experimenta la financiación colectiva de una serie de proyectos reales. Esta parte práctica escenifica en modo "analógico" cómo funciona una plataforma de crowdfunding a través de dinámicas participativas.\r\n\r\nGracias a ellas es posible comprobar aspectos clave como pueden ser criterios de evaluación o cómo hacer atractivos proyectos. Para ello se han elaborado herramientas como barómetros de valoración de proyectos a partir de indicadores como el grado de innovación, nivel de proximidad, coherencia, etc., con los que interactuarán los participantes.\r\n\r\n-> Más sobre el taller:\r\n\r\nhttp://youcoop.org/es/goteo/p/5/goteo-workshop-cultura-de-la-microfinanciacion/\r\n\r\n-> Más sobre la cultura del crowdfunding en nuestro colaboratorio:\r\nYOUCOOP.ORG\r\n\r\nhttp://www.youcoop.org/goteo\r\n\r\n-> Más sobre contextos en los que ya se ha realizado el taller:\r\n\r\n1) CCCB. Barcelona:\r\n\r\nhttp://www.cccb.org/es/curs_o_conferencia-i_c_i_econom_a_distribuida-33719\r\n\r\nhttp://www.flickr.com/photos/cccb/sets/72157625471082121/\r\n\r\n2) MATADERO. Madrid:\r\n\r\nhttp://www.mataderomadrid.org/ficha/768/goteo-cultura-de-la-financiacion-colectiva.html\r\n\r\n3) DIA DE LA PERSONA EMPRENDEDORA. BILBAO:\r\n\r\nhttp://eutokia.org/2011/05/taller-goteo-platoniq-x-edicion-dia-de-la-persona-emprendedora-2011-05-05/\r\n\r\n4) ESCUELA DE VERANO DE LA RED DE TEATROS. ALMAGRO:\r\n\r\nhttp://www.redescueladeverano.es/2011/programa_de_cursos/ficha.php?id=55\r\n', '', 113, '2011-07-26', 1, 1, 0, 0, 1);
INSERT INTO `post` VALUES(22, 1, 'Artesanía DIY y micro producciones: makers "tradicionales"', 'Además del Open Hardware, hay otra movimiento desde abajo hacia arriba (bottom-up) que crece aunque lentamente: el mundo del hazlo tú mismo, hágalo usted mismo o bricolaje, abreviado como HTM, HUM (Do-It-Yourself o DIY en Inglés) y de las micro empresas de diseño de moda y de productos de artesanía. Hay muchas personas que diseñan y producen productos hechos a mano, ropa, bolsos y accesorios; la mayoría de ellos lo consideran como un pasatiempo, pero un número creciente de personas están tratando de ganarse la vida a través de él, ya sea solos empezando como hobby (DIY) o en pequeños grupos intentando montar pequeñas empresas de moda (micro producciones). \r\nNo es una nueva tendencia en realidad: la cultura DIY se remonta a los años 60 y 70, y la artesania ha existido siempre, aunque fue casi sustituida por las fábricas y por la fabricación a gran escala desde la Revolución Industrial (por lo menos en la mayoría de los países desarrollados).\r\n\r\nAunque a primera vista el mundo de la Artesanía DIY parece no estar muy relacionado con la cultura abierta, al menos tradicionalmente, está cada vez más aprendiendo y adoptando herramientas y procesos de ella, incluyendo nuevas tecnologías como el hardware (como el proyecto de Open Hardware Lilypad Arduino,por ejemplo). \r\n\r\nComo Tim O''Reilly informó en el 2008, el movimiento Open Source pone de relieve cómo las comunidades puedan compartir conocimientos y aprovecharlos y el mundo del DIY está adoptando esta actitud ahora mismo. Según él, el movimiento Maker no abarca sólo el DIY, sino que es la forma a través de la cual la informática (el computing) participa en el mundo físico en lugar de lo virtual, y esto será el gran negocio del mañana. El Open Hardware, la Artesanía DIY, las pequeñas empresas de moda, el Open Design convergen cada vez con mayor éxito en un mayor movimiento informal de Makers (o fabricadores), que consiste en todas las personas que aprenden a hacer y comparten este conocimiento en comunidades. Un número cada vez mayor de documentales, libros, revistas, tutoriales, conferencias sobre la gestión de proyectos de Artesanía DIY se ha hecho disponible desde hace algunos años. Tal vez una de las razones del éxito de este movimiento es la recesión economica, que ha empujado la línea entre lo que se produce en casa y lo que se compra en las tiendas. De todos modos, la venta de consultoría o de servicios de apoyo o de contenido es el primer modelo de negocio para la Artesanía DIY.\r\n\r\nhttp://www.youtube.com/v/zH2HWPfwpOw?fs=1&hl=it_IT\r\n\r\nLa piratería como un modelo de negocio común para el diseño de moda\r\n\r\nLos modelos de negocio de diseño de moda suelen adoptar una forma secreta, que tiene una conexión directa con la cultura abierta y que pueden ser útiles para la construcción de nuevos modelos de negocio para la Artesanía DIY: la piratería. Al igual que las empresas Shanzai en China, en realidad hay una mayor innovación y mayores ingresos económicos cuando todos los actores de un ecosistema de fabricación colaboran y comparten conocimientos y proyectos,  esto muestra que el Open Source y la piratería son de hecho un modelo de negocio viable.\r\n\r\nKal Raustiala y Sprigman Christopher describieron la importancia de la copia en el diseño de los ecosistemas de moda muy bien en sus artículo "The Piracy Paradox: Innovation and Intellectual Property in Fashion Design": de hecho no hay protección de derechos de autor o patentes en el diseño de moda, sólo hay la protección de las marcas. Esto significa que cualquier ropa o producto de moda puede ser copiado por completo, a excepción de la marca. La falta del derecho de autor en realidad acelera la creatividad y la innovación: un efecto secundario de una cultura de la copia es un más rápido establecer de las tendencias y la más rápida obsolescencia inducida, dando lugar a más ventas e ingresos, y a más creatividad y innovación (porque el ciclo de vida de un diseño de moda es cada vez más corto). Véase por ejemplo todo el sector del fast fashion de marcas como Zara y H&M, que se están beneficiando de esto, copiando famosos diseños de moda de alto nivel y fabricándolos a precios más bajos (para un mercado diferente que el de gama alta).\r\nIncluso Johanna Blakley en TEDxUSC 2010 explicó lo que todas las industrias creativas pueden aprender de la cultura libre de la moda (más información aquí). Otros recursos son Sprigman El podcast de Chris y el informe de David Bollier y Racine Laurie.\r\n\r\nhttp://www.youtube.com/watch?v=zL2FOrx41N0&feature=player_embedded\r\n\r\n\r\nEtsy y la larga cola de la artesanía user-generated o generada por el usuario\r\nEl mejor ejemplo que muestra las posibilidades de negocio del movimiento de Artesanía DIY es Etsy,un mercado social en linea (o social commerce) concebido por Rob Kalin, junto con Chris Maguire y Schoppik Haim a principios de 2005. Tiene ahora más de 6,2 millones de miembros (400.000 de ellos son también vendedores) y ofrece actualmente a la venta 6.500.000 artículos. Las ventas brutas iniciaron con $ 166.000 en 2005, fueron $ 180 600 000 en 2009 y en 2010 (hasta el septiembre) fueron de $ 206 millones. El 30 de enero 2008 se divulgó la noticia que Etsy había casi alcanzado el equilibrio (break-even), y que había recibido $ 27 millones en financiación de Serie D.\r\nhttp://blip.tv/play/oF6Y%2B3MC\r\n\r\nLa creación de un mercado de la larga cola de Artesanía DIY es el modelo de negocio principal de Etsy, que cobra un honorario de listado de 20 centavos por cada artículo y obtene un 3,5% de cada venta, con un promedio de las ventas de $ 15 o $ 20. Etsy también tiene otro ingreso a través de Showcase, el programa de publicidad que Etsy ha diseñado para sus vendedores. Con la compra de un anuncio de 24 horas en el Showcase, los vendedores de Etsy destacan sus productos en lugares de relieve del sitio para aumentar la fama de su tienda en Etsy y para aumentar las ventas. Los precios son de $ 15,00 para el Showcase principal y el Showcase Holiday, mientras que para los otros anuncios el precio es de $ 7,00. Por lo tanto, hay incluso dudas sobre si la actividad principal de Etsy es proporcionar un mercado para los productos hechos a mano o más bien sea un negocio de publicidad. Por otra parte, Etsy tiene su propio API y permite a los desarrolladores aprovechar los datos de la comunidad de Etsy, construyendo aplicaciones para web, escritorio y dispositivos móviles.\r\nEn 2007 se divulgó la noticia que Etsy estaba interesada en la ampliación de los negocios no en línea: Etsy empezó a ofrecer talleres abiertos a artesanos locales y desea prestar servicios de apoyo, tales como asesoramiento empresarial y de pequeños préstamos en futuro.\r\n\r\nHay también algunas críticas del modelo de negocio de Etsy, ya que parece que no represente realmente un modelo viable para los pequeños fabricantes. Sólo el 4% de los vendedores de Etsy son hombres, el vendedor promedio es una mujer de 35 años de edad y es a menudo una mujer casada con (o a punto de tener) niños pequeños, con ingresos más altos del promedio y una buena educación. Lo más probable es que Etsy atraiga a las mujeres que esperan compaginar con éxito un trabajo significativo con la maternidad. Desafortunadamente, es muy difícil ganarse la vida sólo con Etsy: muy pocos vendedores lo han hecho, y la comunidad lo confirma. De hecho, parece que Etsy ejerceza una presión a rebajar los precios, ya que todos los vendedores (que viven en diferentes ciudades y entonces con diferentes costes de vida) están en competencia directa y no se puede aumentar el volumen (que seria la respuesta habitual a estos problemas), porque los artículos son artesanales y no producidos en serie.\r\n\r\nMegan Auman de craftmba.com sugiere que Etsy no debe considerarse sólo como un mercado, sino como una de  incubadora de empresas que acelera el desarrollo exitoso de negocios basados en Artesanía DIY y Micro Producciones a través de una serie de recursos de apoyo empresarial y servicios. Etsy ofrece bajar las barreras de ingreso al mercado, pero al crecer el negocio, se debe pensar en ir dejando Etsy y tener una tienda de comercio electrónico diferente, un buen paso más para la construcción de una marca emergente (al igual que White Elephant vintage hizo,por ejemplo). Por otra parte, como hemos dicho antes, los precios en Etsy probablemente no aumentarán debido a la fuerte competencia, y esta es otra razón para salir de Etsy, cuando las habilidades y las ventas de un vendedor mejoran. \r\n\r\nThreadless, crowdsourcing el diseño sin dejar de fabricar el producto\r\nSi bien Threadless no puede ser categorizado estrictamente como Artesanía o DIY, tiene un modelo de negocio muy interesante que puede ser tomado como inspiración: el "crowdsourcing" del diseño para luego fabricar los productos. \r\n\r\nhttp://www.youtube.com/v/9VKRbmnqXR4?fs=1&hl=it_IT\r\n\r\nThreadless es una tienda de ropa en línea centrada en la comunidad fundada por Jake Nickell y Jacob DeHart, en el año 2000 con tan sólo $ 1,000. Ahora está dirigido por la empresa skinnyCorp de Chicago. Los miembros de la comunidad Threadless envian sus diseños de camisetas en línea y estan sometidos a una votación pública. Un pequeño porcentaje de todos los proyectos presentados son seleccionados para la impresión y se venden a través de una tienda en línea; los ganadores reciben un premio de $ 2,000 en efectivo, un certificado de regalo de 500 $ (que se puede cambiar por $ 200 en efectivo), así como un bono adicional de $ 500 por cada reimpresión. Incluso hay dos tiendas fisicas de Threadless: Threadless y Threadless Kids en Chicago. \r\nAnders Sundelin señaló que la producción de una demanda predeterminada mantiene los costos bajos y los márgenes altos, y como que los miembros de la comunidad ya forman parte de la demanda y ayudan a co-crearla, Threadless nunca produce sin vender camisetas: esta es la razón por la cual genera más de $ 17.000.000 en ventas anuales con un margen de beneficio del 35% con una creciente comunidad.  Por otra parte,Threadless tiene también un flujo de ingresos por suscripción a través de el club 12 (una edición limitada t-shirt de 12 meses) y tiene también un programa de Street Team con miembros afiliados que ganan puntos para futuras compras gracias a sus referencias de venta o al entregar una foto de ellos con una camiseta de Threadless.\r\n\r\nOpenwear: código abierto y colaboración para micro producciones\r\nSi la "piratería" (o por lo menos vamos a decir: el "copiar") es una práctica común en el mundo de la moda, y el "crowdsourcing" está encontrando su lugar en el también, un diseño de moda abierto (Open Fashion Design) es el modelo de negocio consiguiente. Uno de los principales problemas del mundo del diseño de moda, artesanato,  DIY o micro producciones, es que está demasiado fragmentado y de consecuencia el número de productos creados y vendidos por cada disenador es muy bajo: es necesario hacer que la comunidad sea más coherente y ayudar a todos los actores a ahorrar tiempo y dinero con una actividad común y colaborativa. Openwear.org, por ejemplo, es una nueva comunidad que ha creado algunos diseños de moda básicos y va a compartirlos con todos sus miembros, creando así una marca de moda totalmente abierta. De esta manera, nadie de los diseñadores tendrá que empezar de cero y se ahorrarán tiempo y recursos para el diseño de ropa nueva.\r\n\r\nhttp://vimeo.com/user4899256\r\n\r\nStitch Tomorrow: Microcréditos para el Desarrollo a través de diseño de moda\r\nStitch Tomorrow es una iniciativa de microcréditos liderada por jóvenes de Filipinas, con el fin de facilitar a los jóvenes desfavorecidos del sudeste asiático con sesiones de verano para que sean capaces de crear sus propias líneas de moda con ropa hecha de materiales reciclados. Stitch Tomorrow les ofrece la educación (sobre moda y  negocios), capital y recursos, diseño, servicios de consultoría de marketing y administración, la participación de los clientes en el diseño y proceso de negocio. Una vez que estos diseñadores de moda puedan trabajar de forma independiente, podrán poco a poco devolver el dinero a Stitch Tomorrow y el interés se utilizará para capacitar a otros adolescentes en el verano siguiente.\r\n\r\nhttp://www.youtube.com/v/TO-EXd4b2Y0?fs=1&hl=en_US\r\n\r\nSewing Cafes: lugares para la Artesanía DIY y las micro producciones\r\nAl igual que los hackerspaces para el movimiento Open Hardware, el movimiento de Artesaía  DIY también tiene sus propios lugares de fabricación, experimentación, creación de prototipos,  aprendizaje y  construcción de la comunidad: los Sewing Cafés (Cafés de costura). Por lo menos desde el año 2006  en ciudades como Nueva York, Los Ángeles y Boston (como Quilter’s West en West Concordia), han nacido cafés que alquilan por horas máquinas de coser, y ahora se pueden encontrar en muchos países de Europa. Uno es el Sweat Shop en París, donde los usuarios pueden comprar el acceso a una máquina de coser Singer (€ 6.00 por hora), otro es el Linkle en Berlín (€ 5.00 por hora). En el Reino Unido son algunos ejemplos Home Made London (£ 10.00 por hora), Make It Glasgow (£ 5.00 por una hora, £ 7.50 por dos o £ 10.00 por tres) y el Needlebugs Sewing Café en Manchester, con base en un espacio de arte con organización sin fines de lucro llamado Art Nexus Café.\r\nHay incluso un sewing café en Melbourne, Australia (el Thread Den) y un Localizador  de Sewing Cafes, aunque todavía está en construcción.\r\nEtsy incluso tiene un espacio de trabajo para su comunidad que proporciona equipos y materiales donados, donde los miembros se reúnen para hacer artículos, tomar y dar talleres, y asistir a eventos especiales. Se trata de una oficina permanente llamada Etsy Labs en Nueva York. El equipo de soporte al cliente, marketing / relaciones públicas, y comunicaciones de la empresa están basados allí. \r\n\r\nUna lección de la Artesanía DIY: el microcrédito como una herramienta para la creación de redes colaborativas\r\nLa necesidad de encontrar nuevos modelos de negocio se está manifestando de un modo cada vez más urgente en el mundo de la Artesanía  DIY y de las micro producciones, ya que cada fabricante tiene su propio modelo de negocio y lucha por encontrar un equilibrio en su crecimiento (que además es algo difícil de entender para ellos). Como Zoe Romano y Niessen Bertram de Openwear señalan en una entrevista (que pronto se publicará en openp2pdesign.org), muchos fabricantes de DIY y artesanato aún siguen el ritmo estacional de las colecciones, mientras que otros están experimentando modelos más flexibles. Algunas personas se dividen entre las diferentes actividades: son al mismo tiempo artesanos y maestros de artesanía, o bien diseñan y realizan su propia colección, pero, al mismo tiempo, trabajan también para terceras partes en diferentes puntos de la cadena de producción. Uno de los mayores problemas del movimiento de Artesanía DIY (especialmente en comparación con las comunidades de Open Source y Open Hardware) es la extrema fragmentación de la comunidad: en los proyectos abiertos las comunidades pueden ser pequeñas, pero definitivamente hay más gente colaborando junta en el mismo proyecto que en proyectos de Artesanía DIY. Es más fácil obtener ganancias con la larga cola del DIY que con un solo proyecto, y aquí podríamos utilizar el microcrédito como una herramienta para la construcción de la comunidad y para la construcción y gestión de redes de colaboración entre los diversos participantes.\r\nPor otra parte, según lo informado por Zoe Romano y Bertram Niessen, la escena B2B del movimiento de Artesanía DIY tiene un buen porcentaje de sus transacciones basadas en el trueque de bienes y servicios mientras que las transacciones con dinero son casi únicamente para la venta de los productos finales. \r\nDebemos entonces considerar también este aspecto, y pensar en iniciativas de microcrédito para adentro de la comunidad DIY y otras para iniciativas de microcrédito para las relaciones con el exterior. Podría tratarse incluso de una sola iniciativa de microcrédito, pero al final va a trabajar de una manera diferente dentro y fuera de la comunidad, y deberíamos utilizarla para crear un ecosistema de colaboración más fuerte.\r\n', 'http://www.youtube.com/watch?v=zH2HWPfwpOw', 0, '2011-08-01', 1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post_copy`
--

DROP TABLE IF EXISTS `post_copy`;
CREATE TABLE `post_copy` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog` bigint(20) unsigned NOT NULL,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  `image` int(10) DEFAULT NULL,
  `date` date NOT NULL COMMENT 'fehca de publicacion',
  `order` int(11) DEFAULT '1',
  `allow` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Permite comentarios',
  `home` tinyint(1) DEFAULT '0' COMMENT 'para los de portada',
  `footer` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Para los del footer',
  `publish` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Publicado',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Volcar la base de datos para la tabla `post_copy`
--

INSERT INTO `post_copy` VALUES(1, 1, '¿Porque goteo es diferente?', 'Goteo se distingue principalmente de otras plataformas de financiación colectiva por su apuesta diferencial y focalizada en proyectos de ⤽código abierto⤝ en un sentido amplio. Esto es, que compartan conocimiento, procesos, resultado, responsabilidad o beneficio, desde la filosofía del procomún. Porque Goteo pone el acento en la esfera pública, apoyando proyectos que favorezcan el empoderamiento colectivo y el bien común.\r\n\r\nEs crowdfunding para gente que busque la rentabilidad social de las inversiones efectuadas, haciendo viables los proyectos para sus promotores pero también el retorno colectivo para la comunidad (en paralelo a contraprestaciones individuales). Estos retornos colectivos pueden ser de muy diverso tipo y se fundamentan en compartir conocimiento y dar acceso libre al código fuente, a productos y recursos, a formación a través de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios económicamente sostenibles.\r\n\r\nOtra peculiaridad de Goteo es que permite recaudar fondos mínimos, por un lado, para arrancar los proyectos, y por otro fondos óptimos para fases posteriores, tal como explicamos un par de FAQs más arriba. También el hecho de que pueda funcionar y ser administrada de manera autónoma por nodos temáticos o geográficos distribuidos geográficamente, como detallamos otras FAQ más abajo. O que permita inversiones puntuales de entidades que deseen generar así una especie de bote, a repartir según el criterio de una comunidad de expertos entre proyectos de un nodo determinado.\r\n\r\n¿Más diferencias con otras plataformas? ÿsta se aplica su propia receta y también está disponible con su propia licencia copyleft, así como su código fuente (para quien prefiera descargarse la herramienta y adaptarla a sus necesidades de crowdfunding). También estamos trabajando en otras especificaciones, como un panel de seguimiento detallado de fondos obtenidos, para aumentar la transparencia sobre dónde acaba lo recaudado, o una tarifa plana para micromecenas compulsivos.\r\n\r\nEstos retornos colectivos pueden ser de muy diverso tipo y se fundamentan en compartir conocimiento y dar acceso libre al código fuente, a productos y recursos, a formación a través de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios económicamente sostenibles.', 'http://vimeo.com/20597320', 0, '2011-06-08', 1, 1, 1, 1, 1);
INSERT INTO `post_copy` VALUES(3, 1, 'Goteo es una comunidad de comunidades', 'Goteo es una comunidad de comunidades -cuyo nexo de unión es el interés por el fortalecimiento del procomún-, que se articulan en torno a una plataforma digital en internet. Un sistema distribuido de comunidades locales y/o temáticas, especialistas, legitimadas, con un importante calado social, referentes en su ámbito de actuación. Estas comunidades constituyen una red fundamental de nodos de confianza, que sirven para localizar lo digital, aportando proximidad y especificidad, multiplicando el efecto de la plataforma.', '[slideshare id=8175922&doc=cbbhondartzan2-110601120109-phpapp01]', 0, '2011-06-06', 2, 0, 1, 0, 1);
INSERT INTO `post_copy` VALUES(4, 1, 'Goteo. Codiseño con la comunidad', 'Texto pendiente sobre el papel de los talleres en el desarollo de Goteo', '[slideshare id=8533502&doc=cbbhondartzan3-110707082157-phpapp02]', 33, '2011-06-06', 1, 0, 1, 0, 1);
INSERT INTO `post_copy` VALUES(16, 6, 'Primera actualización desde el dashboard', 'aui viene el lorem ipsum famoso. Phasellus vulputate dolor sit amet nulla mollis quis ultricies ante lobortis. Fusce euismod hendrerit nunc vitae bibendum. Vestibulum a mauris non ipsum bibendum dignissim. Sed auctor ligula eget nunc laoreet consequat. Sed blandit faucibus fermentum. Morbi laoreet augue ut augue ullamcorper gravida tincidunt justo laoreet. Proin commodo, sem sit amet accumsan pharetra, felis magna pretium est, vitae dictum leo leo faucibus sem. Pellentesque malesuada condimentum lectus et placerat. Proin ullamcorper leo sed erat mattis dictum. Vestibulum consequat, neque vel ultrices lacinia, tortor nibh sodales quam, nec faucibus velit tortor id lacus. Integer posuere felis metus, ut tincidunt enim. Nulla mattis ante in justo aliquet cursus. Quisque commodo, tortor id porta sollicitudin, justo justo pharetra purus, sed consequat lectus enim sit amet lectus. Sed in ipsum eros, rutrum commodo dui. Nulla sem dolor, laoreet mollis hendrerit id, fermentum eget arcu.<br /><br />\r\n<br /><br />\r\nNulla vitae urna arcu. Integer ac lacus quis magna eleifend facilisis. Etiam vehicula condimentum odio id elementum. Nunc porta molestie viverra. Etiam eget justo leo. Vestibulum non urna a risus interdum malesuada in id velit. Etiam faucibus, sem vel convallis consequat, neque nunc consectetur elit, ut dapibus libero libero et mauris. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec non nibh purus, id feugiat ante. Donec vel velit leo, ac cursus risus. Pellentesque feugiat dui sit amet libero sagittis id tincidunt nulla tincidunt. Etiam libero diam, vulputate sit amet viverra a, mattis eget diam. Pellentesque habitan', '', 68, '2011-07-08', 1, 1, 0, 0, 1);
INSERT INTO `post_copy` VALUES(17, 6, 'segunda publicación desde dashboard con video', 'Phasellus vulputate dolor sit amet nulla mollis quis ultricies ante lobortis. Fusce euismod hendrerit nunc vitae bibendum. Vestibulum a mauris non ipsum bibendum dignissim. Sed auctor ligula eget nunc laoreet consequat. Sed blandit faucibus fermentum. Morbi laoreet augue ut augue ullamcorper gravida tincidunt justo laoreet. Proin commodo, sem sit amet accumsan pharetra, felis magna pretium est, vitae dictum leo leo faucibus sem. Pellentesque malesuada condimentum lectus et placerat. Proin ullamcorper leo sed erat mattis dictum. Vestibulum consequat, neque vel ultrices lacinia, tortor nibh sodales quam, nec faucibus velit tortor id lacus. Integer posuere felis metus, ut tincidunt enim. Nulla mattis ante in justo aliquet cursus. Quisque commodo, tortor id porta sollicitudin, justo justo pharetra purus, sed consequat lectus enim sit amet lectus. Sed in ipsum eros, rutrum commodo dui. Nulla sem dolor, laoreet mollis hendrerit id, fermentum eget arcu.\r\n\r\nNulla vitae urna arcu. Integer ac lacus quis magna eleifend facilisis. Etiam vehicula condimentum odio id elementum. Nunc porta molestie viverra. Etiam eget justo leo. Vestibulum non urna a risus interdum malesuada in id velit. Etiam faucibus, sem vel convallis consequat, neque nunc consectetur elit, ut dapibus libero libero et mauris. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec non nibh purus, id feugiat ante. Donec vel velit leo, ac cursus risus. Pellentesque feugiat dui sit amet libero sagittis id tincidunt nulla tincidunt. Etiam libero diam, vulputate sit amet viverra a, mattis eget diam. Pellentesque habitan', 'http://vimeo.com/26125187', 0, '2011-07-08', 1, 1, 0, 0, 1);
INSERT INTO `post_copy` VALUES(21, 1, 'El taller "Goteo, cultura de la financiación colectiva" sigue rodando', 'El taller "Goteo, cultura de la financiación colectiva (crowdfunding)" sigue rodando.\r\nEl taller Goteo ofrece información teórica sobre las diferentes tipologías de financiación colectiva, acercando de forma dinámica y atractiva al crowdfunding como estrategia de interés tanto para la financiación como para la difusión y generación de comunidad.\r\n\r\nPróxima parada: 24 de junio en Cáceres en el Centro de Conocimiento AldealabC3, edificio Embarcadero.\r\nCómo apuntarse: (plazas limitadas)\r\n609 519 493 (coordinadora Chon Muñoz)\r\n\r\nOrganiza:\r\nProyecto Fénix. Factoría de la Innovación/ Ayuntamiento de Cáceres / Concejalía de Innovación, Fomento y Desarrollo\r\nTecnológico.\r\n\r\nCon la colaboración del Gabinete del Iniciativa Joven de Extremadura.\r\n\r\nEl crowdfunding ha surgido muy recientemente en España y en pocos meses ha demostrado ser una eficaz alternativa de financiación de proyectos. Pero al mismo tiempo tiene efectos secundarios altamente interesantes para el ámbito cultural/social y de TICs, como la generación de comunidades de interés y la capacidad de generar expectativas de forma previa a la presentación final del proyecto.\r\n\r\nLa duración del taller es de cuatro horas, que se distribuyen en dos partes:\r\n\r\na) exposición del intensivo estudio realizado por Platoniq sobre diferentes tipologías de plataformas, diferenciando\r\nmicrofinanciación, financiación colectiva (crowdfunding) y préstamos p2p.\r\n\r\nb) parte práctica en la que se experimenta la financiación colectiva de una serie de proyectos reales. Esta parte práctica escenifica en modo "analógico" cómo funciona una plataforma de crowdfunding a través de dinámicas participativas.\r\n\r\nGracias a ellas es posible comprobar aspectos clave como pueden ser criterios de evaluación o cómo hacer atractivos proyectos. Para ello se han elaborado herramientas como barómetros de valoración de proyectos a partir de indicadores como el grado de innovación, nivel de proximidad, coherencia, etc., con los que interactuarán los participantes.\r\n\r\n-> Más sobre el taller:\r\n\r\nhttp://youcoop.org/es/goteo/p/5/goteo-workshop-cultura-de-la-microfinanciacion/\r\n\r\n-> Más sobre la cultura del crowdfunding en nuestro colaboratorio:\r\nYOUCOOP.ORG\r\n\r\nhttp://www.youcoop.org/goteo\r\n\r\n-> Más sobre contextos en los que ya se ha realizado el taller:\r\n\r\n1) CCCB. Barcelona:\r\n\r\nhttp://www.cccb.org/es/curs_o_conferencia-i_c_i_econom_a_distribuida-33719\r\n\r\nhttp://www.flickr.com/photos/cccb/sets/72157625471082121/\r\n\r\n2) MATADERO. Madrid:\r\n\r\nhttp://www.mataderomadrid.org/ficha/768/goteo-cultura-de-la-financiacion-colectiva.html\r\n\r\n3) DIA DE LA PERSONA EMPRENDEDORA. BILBAO:\r\n\r\nhttp://eutokia.org/2011/05/taller-goteo-platoniq-x-edicion-dia-de-la-persona-emprendedora-2011-05-05/\r\n\r\n4) ESCUELA DE VERANO DE LA RED DE TEATROS. ALMAGRO:\r\n\r\nhttp://www.redescueladeverano.es/2011/programa_de_cursos/ficha.php?id=55\r\n', '', 113, '2011-07-26', 1, 1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post_lang`
--

DROP TABLE IF EXISTS `post_lang`;
CREATE TABLE `post_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` longtext,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `post_lang`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post_tag`
--

DROP TABLE IF EXISTS `post_tag`;
CREATE TABLE `post_tag` (
  `post` bigint(20) unsigned NOT NULL,
  `tag` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`post`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags de las entradas';

--
-- Volcar la base de datos para la tabla `post_tag`
--

INSERT INTO `post_tag` VALUES(1, 3);
INSERT INTO `post_tag` VALUES(1, 7);
INSERT INTO `post_tag` VALUES(2, 1);
INSERT INTO `post_tag` VALUES(2, 7);
INSERT INTO `post_tag` VALUES(3, 1);
INSERT INTO `post_tag` VALUES(3, 2);
INSERT INTO `post_tag` VALUES(4, 1);
INSERT INTO `post_tag` VALUES(4, 6);
INSERT INTO `post_tag` VALUES(15, 2);
INSERT INTO `post_tag` VALUES(15, 3);
INSERT INTO `post_tag` VALUES(15, 6);
INSERT INTO `post_tag` VALUES(18, 3);
INSERT INTO `post_tag` VALUES(21, 2);
INSERT INTO `post_tag` VALUES(21, 3);
INSERT INTO `post_tag` VALUES(21, 6);
INSERT INTO `post_tag` VALUES(22, 2);
INSERT INTO `post_tag` VALUES(22, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` varchar(50) NOT NULL,
  `name` tinytext,
  `status` int(1) NOT NULL,
  `progress` int(3) NOT NULL,
  `owner` varchar(50) NOT NULL COMMENT 'usuario que lo ha creado',
  `node` varchar(50) NOT NULL COMMENT 'nodo en el que se ha creado',
  `amount` int(6) DEFAULT NULL COMMENT 'acumulado actualmente',
  `days` int(3) NOT NULL DEFAULT '0' COMMENT 'Dias restantes',
  `created` date DEFAULT NULL,
  `updated` date DEFAULT NULL,
  `published` date DEFAULT NULL,
  `success` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  `contract_name` varchar(255) DEFAULT NULL,
  `contract_nif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `phone` varchar(9) DEFAULT NULL COMMENT 'guardar sin espacios ni puntos',
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `image` varchar(256) DEFAULT NULL,
  `description` text,
  `motivation` text,
  `about` text,
  `goal` text,
  `related` text,
  `category` varchar(50) DEFAULT NULL,
  `keywords` tinytext COMMENT 'Separadas por comas',
  `media` varchar(256) DEFAULT NULL,
  `currently` int(1) DEFAULT NULL,
  `project_location` varchar(256) DEFAULT NULL,
  `scope` int(1) DEFAULT NULL COMMENT 'Ambito de alcance',
  `resource` text,
  `comment` text COMMENT 'Comentario para los admin',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos de la plataforma';

--
-- Volcar la base de datos para la tabla `project`
--

INSERT INTO `project` VALUES('2c667d6a62707f369bad654174116a1e', 'NO SLEEP TO BROOKLYN', 1, 70, 'olivier', 'goteo', 200, 23, '2011-05-12', '2011-07-25', '2011-07-08', NULL, NULL, '', '', '', '', '', '', '', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat fermentum risus in pulvinar. Nunc a pretium lorem. Morbi semper orci ac nisl mattis accumsan. Integer a metus lacus. Aenean sagittis mattis mi, id pulvinar lectus sagittis vitae. Etiam eget metus in lacus feugiat viverra. Vestibulum sed dui id tellus dictum hendrerit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat fermentum risus in pulvinar. Nunc a pretium lorem. Morbi semper orci ac nisl mattis accumsan. Integer a metus lacus. Aenean sagittis mattis mi, id pulvinar lectus sagittis vitae. Etiam eget metus in lacus feugiat viverra. Vestibulum sed dui id tellus dictum hendrerit. Duis id lacus odio, nec viverra libero. Donec varius, felis ac rhoncus molestie, turpis nulla ullamcorper ligula, sit amet elementum justo tortor sit amet dui. Sed in ultrices arcu. Suspendisse potenti. Nulla vestibulum consequat nulla ac dictum. Nulla sagittis nibh eu arcu pulvinar luctus. Praesent a nibh ac risus sollicitudin auctor. ', 'cyberpunk Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat fermentum risus in pulvinar. Nunc a pretium lorem. Morbi semper orci ac nisl mattis accumsan. Integer a metus lacus. Aenean sagittis mattis mi, id pulvinar lectus sagittis vitae. Etiam eget metus in lacus feugiat viverra. Vestibulum sed dui id tellus dictum hendrerit. Duis id lacus odio, nec viverra libero. Donec varius, felis ac rhoncus molestie, turpis nulla ullamcorper ligula, sit amet elementum justo tortor sit amet dui. Sed in ultrices arcu. Suspendisse potenti. Nulla vestibulum consequat nulla ac dictum. Nulla sagittis nibh eu arcu pulvinar luctus. Praesent a nibh ac risus sollicitudin auctor. ', 'una canción de los beastie. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat fermentum risus in pulvinar. Nunc a pretium lorem. Morbi semper orci ac nisl mattis accumsan. Integer a metus lacus. Aenean sagittis mattis mi, id pulvinar lectus sagittis vitae. Etiam eget metus in lacus feugiat viverra. Vestibulum sed dui id tellus dictum hendrerit. Duis id lacus odio, nec viverra libero. Donec varius, felis ac rhoncus molestie, turpis nulla ullamcorper ligula, sit amet elementum justo tortor sit amet dui. Sed in ultrices arcu. Suspendisse potenti. Nulla vestibulum consequat nulla ac dictum. Nulla sagittis nibh eu arcu pulvinar luctus. Praesent a nibh ac risus sollicitudin auctor. ', 'cyberpunk Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat fermentum risus in pulvinar. Nunc a pretium lorem. Morbi semper orci ac nisl mattis accumsan. Integer a metus lacus. Aenean sagittis mattis mi, id pulvinar lectus sagittis vitae. Etiam eget metus in lacus feugiat viverra. Vestibulum sed dui id tellus dictum hendrerit. Duis id lacus odio, nec viverra libero. Donec varius, felis ac rhoncus molestie, turpis nulla ullamcorper ligula, sit amet elementum justo tortor sit amet dui. Sed in ultrices arcu. Suspendisse potenti. Nulla vestibulum consequat nulla ac dictum. Nulla sagittis nibh eu arcu pulvinar luctus. Praesent a nibh ac risus sollicitudin auctor. ', 'un monton de gente', NULL, 'educación, copyleft, manchego', 'http://vimeo.com/24562056', 3, 'paris', 0, '', '');
INSERT INTO `project` VALUES('3d72d03458ebd5797cc5fc1c014fc894', 'Mi proyecto 2', 1, 56, 'olivier', 'goteo', 0, 0, '2011-07-04', '2011-08-01', NULL, NULL, NULL, 'Olivier Schulbaum', '', '667031530', '', '', 'Palma de Mallorca', 'España', '', 'Es lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u \r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir ujhdjhdjudjddduheyyvvvvvvdtdyuddudhudhdhdyduyddddddddddddddddd que cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la red', 'Es lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u \r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir ujhdjhdjudjddduheyyvvvvvvdtdyuddudhudhdhdyduyddddddddddddddddd que cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la red', 'Es lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u \r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir ujhdjhdjudjddduheyyvvvvvvdtdyuddudhudhdhdyduyddddddddddddddddd que cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la red', 'Es lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u \r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir ujhdjhdjudjddduheyyvvvvvvdtdyuddudhudhdhdyduyddddddddddddddddd que cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la red', 'Es lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u \r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir u\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir ujhdjhdjudjddduheyyvvvvvvdtdyuddudhudhdhdyduyddddddddddddddddd que cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la redque cuida la red', NULL, '', '', 0, 'Palma de Mallorca', 4, '', '');
INSERT INTO `project` VALUES('8851739335520c5eeea01cd745d0442d', 'Mi proyecto 1', 1, 41, 'root', 'goteo', 0, 0, '2011-07-21', '2011-07-31', NULL, NULL, NULL, 'Super administrador', '', '', '', '', '', 'EspaÃ±a', '', '', '', '', '', '', NULL, '', '', 2, '', 3, '', '');
INSERT INTO `project` VALUES('8c069c398c3e3114e681ccfafa4a3fe0', 'Mi proyecto 3', 0, 50, 'olivier', 'goteo', 0, 0, '2011-07-15', '2011-07-20', NULL, NULL, '2011-07-25', 'Olivier Schulbaum', '', '667031530', '', '', 'Palma de Mallorca', 'España', '', '', '', '', '', '', NULL, '', '', 0, 'Palma de Mallorca', 0, '', '');
INSERT INTO `project` VALUES('96ce377a34e515ab748a096093187d53', 'DINOU Publicació irregular', 1, 70, 'diegobus', 'goteo', 0, 0, '2011-07-12', '2011-08-01', NULL, NULL, NULL, 'diego', 'x8562415k', '658125454', 'c/ calle 98, 1º 2º', '08000', 'Barcelona', 'España', '', 'La idea de esta publicación surge de forma espontánea el martes 31 de mayo de 2011, con ella intentamos compilar a manera de instantáneas una serie de frases recogidas mediante entrevistas realizadas en la acampada de la Plaza Catalunya de Barcelona y diversas fuentes en internet.\r\n\r\nEl espectro es amplio, lo naif convive con lo intelectual, lo político con lo emocional, hemos seleccionado una serie de ideas que volaban como esporas por el aire. Esta aglomeración de conceptos no es más que un intento por llevar al papel las ganas de cambiar aspectos fundamentales de la realidad actual.', '', '', '', '', NULL, '', 'http://www.vimeo.com/25195643', 3, 'Barcelona', 1, 'Costes de concepción diseño y maquetación ya están cubiertos.', '');
INSERT INTO `project` VALUES('984990664ca1a1a98522b2640b0fc535', 'Mi proyecto 2', 0, 16, 'root', 'goteo', 0, 0, '2011-07-21', NULL, NULL, NULL, '2011-07-25', 'Super administrador', '', '', '', '', '', 'EspaÃ±a', NULL, '', '', '', '', '', NULL, '', NULL, NULL, '', NULL, '', '');
INSERT INTO `project` VALUES('9925d72d4b0a4b0733abaeaa3e187581', 'akoranga_Labs metodología de formación p2p', 1, 38, 'fbalague', 'goteo', 0, 0, '2011-08-02', '2011-08-02', NULL, NULL, NULL, 'Francesc Balagué', '46784299E', '655210167', 'Aribau, 64 entl. 1a', '08011', 'Barcelona', 'España', '', '', '', '', '', '', NULL, '', '', NULL, 'Barcelona', NULL, '', NULL);
INSERT INTO `project` VALUES('a565092b772c29abc1b92f999af2f2fb', 'Tweetometro', 0, 56, 'goteo', 'goteo', 240, 19, '2011-07-03', '2011-07-27', '2011-07-05', NULL, '2011-07-27', '', '', '', '', '', '', '', '', 'Plataforma experimental de votación mediante tweets. En alfa! Una aplicación para llegar a acuerdos, tomar decisiones colectivamente o elegir la mejor idea presentada, mediante twitter y sms (en desarrollo).', '', '', '', '', NULL, '', '', 0, '', 0, '', '');
INSERT INTO `project` VALUES('ae02e5caf5a19567007c6cb06a8c9d95', 'Mi proyecto 1', 0, 13, 'susana', 'goteo', 0, 0, '2011-07-15', NULL, NULL, NULL, '2011-07-25', 'Susana', '', '', '', '', '', 'EspaÃ±a', NULL, '', '', '', '', '', NULL, '', NULL, NULL, '', NULL, '', '');
INSERT INTO `project` VALUES('archinhand-architecture-in-your-hand', 'ARCHINHAND | Architecture in your Hand', 1, 52, 'ebaraonap', 'goteo', 50, 28, '2011-05-13', '2011-07-27', '2011-07-13', '0000-00-00', '2011-07-05', '', '', '', '', '', 'Barcelona', 'España', '', 'Archinhand es un proyecto editorial de difusión de contenidos sobre arquitectura y ciudad a través de dispositivos móviles.  \r\nLa subversión de las parcelas editoriales tradicionales, las nuevas formas de aprendizaje, la difusión de los límites entre espacio público y privado y la creciente implantación de dispositivos móviles, constituyen los puntos de partida del proyecto.', '', 'Archinhand enfoca el aprendizaje de la ciudad y los espacios mediante la experiencia directa. La información sobre arquitectura y ciudad vista como servicio no como producto, en conexión con el espacio circundante. El aprendizaje guiado por la curiosidad y no por un curriculum académico.\r\n\r\nLos contenidos se canalizan en tres líneas básicas de información: blogs, ciudad y libros. La estructura del proyecto permite enlazar los contenidos de las tres líneas ampliando la experiencia a la vez que se adapta a la lógica de comunicación móvil.\r\n\r\nURL proyecto:www.archinhand.com\r\n\r\nSector al que va dirigido:Arquitectos, Estudiantes de arquitectura en paises emergentes, urbanistas, viajeros con intereses en arquitectura\r\n', '', 'ANTECEDENTES:\r\n\r\n-En fase inicial el proyecto "Archinhand" fue ganador en el Urbanlabs 09 del CitiLab <bit.ly/dC2CiE>\r\n\r\nPROMOTOR EDITORIAL:\r\n\r\ndpr-barcelona promotora del proyecto es una editorial sobre arquitectura y ciudad en constante innovación en la transmisión de contenidos al publico: www.dpr-barcelona.com/\r\n\r\nLos promotores editoriales, Ethel Baraona Pohl y César Reyes son arquitectos. Cuentan con:\r\n\r\n-Experiencia de 7 años en el mundo editorial de arquitectura. Contactos y gestión de contenidos para editoriales y revistas especializadas.\r\n\r\nEn dpr-barcelona el networking y las plataformas de trabajo colaborativo constituyen la forma de trabajo habitual, a modo de ejemplo pueden citarse:\r\n\r\n-Publicación del primer libro de arquitectura "sin papel" [Piel.Skin 2007] <skinarchitecture.com/ >\r\n-Coordinación del lanzamiento simultaneo en 5 ciudades de "Alguien Dijo Participar" el 11 Septiembre de 2009 <bit.ly/n12ll>\r\n-Red de Colaboraciones y contactos directos en los blogs de arquitectura mas relevantes a nivel mundial tanto por trafico de visitas como por calidad de contenidos.\r\n-Amplia Base de datos sobre proyectos y experiencias en arquitectura fruto de una extensa red 2.0 de contactos, colaboradores y despachos de arquitectura en los cinco continentes.\r\n-Coordinación y realización de experiencias académicas usando plataformas on line y presenciales en colaboración con equipos como Ecosistema Urbano y radarq <bit.ly/bPqEDi>\r\n\r\nPor su trabajo editorial en red y constante búsqueda de innovación han sido invitados a eventos como:\r\n\r\n-Postopolis, México DF <postopolis.org/>\r\n-Bookcamp - Kosmopolis, Barcelona <bit.ly/cfwmoQ>\r\n-Mercado Atlántico de Creación Contemporánea (MACC), Santiago de Compostela <bit.ly/aHOFMQ>\r\n-HOY sistemas de Trabajo, Madrid <bit.ly/a05QOg>\r\n-KAM Workshops, Atenas <bit.ly/9idEg5>\r\n-Eme3, Barcelona <bit.ly/bkwDfE>\r\n\r\nActividad académica como lecturers o asesores:\r\nUniversitat Politecnica de Catalunya, Universitat de Barcelona, Arquiredes Motril, Esarq-UIC, Calgary University, International Campus Ultzama\r\n\r\nPROMOTORES TECNOLOGICOS:\r\n\r\nClaimSoluciones es el socio tecnológico involucrado en el proyecto. Es un estudio de comunicación que utiliza la Estrategia y la Producción Visual como herramientas principales. Desarrollan proyectos de comunicación en Web, diseño gráfico, producción audiovisual, below media. Esta formado por Sergio Jiménez y Jakob Renpening', '', 'editorial, arquitectura, ciudad, AR, blogs', '', 0, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('canal-alfa', 'Canal Alfa', 1, 47, 'geraldo', 'goteo', 0, 0, '2011-05-13', '2011-07-31', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Barcelona', 'España', '', 'Canalalpha es un proyecto que quiere aplicar el concepto del sample al vídeo. Entendiendo sample como una unidad mínima musical, muy expandida en la música electrónica desde los años 90. El proyecto quiere llevar el equivalente del sample al vídeo más allá del conocido termino “loop”, aplicándolo a unidades que puedan tener independencia dentro del mismo vídeo gracias a un canal de transparencia. De esta manera, un vídeo se entiende como la composición de sus partes (al estilo de capas) que actúan como entidades independientes que pueden apagarse o encenderse, ir a una velocidad diferente hacia delante o hacia atrás, tener tamaños distintos (incluso variables durante la reproducción) e incluso posicionamientos no estándares dentro de la composición (warping).\r\n\r\nEl marco de acción del proyecto se compone de estos 3 ejes que se formalizan en un portal web hasta ahora inexistente:\r\n\r\n1. Una base de datos abierta y libre (CC) de samples de vídeo - incluyendo un canal de transparencia - que representen unidades básicas que puedan ser utilizadas para crear composiciones.\r\n\r\n2. Herramientas que permiten extraer objetos de vídeos para esta base de datos, incluyendo la posibilidad de extraer partes de los vídeos existentes en los portales de vídeo tales como youtube. Los métodos para la extracción de samples de vídeos ya existentes se basan en técnicas de visión por computador: substracción de fondo (background substraction), detección de movimiento, umbral de color, umbral de intensidad. La intención es ir mas allá del ya conocido chroma-key, tan usado en televisión.\r\n\r\n3. Una plataforma para obras creadas con samples de vídeos de la base de datos pública de canalalpha entendidos como poemas visuales animados, interactivos y sujetos a una narrativa cambiante.', '', 'URL proyecto: www.canalalpha.net/\r\n\r\nSector al que va dirigido: artistas visuales', '', 'Gerald Kogler es especialista en diseño de sistemas interactivos y software libre. Desarrolla aplicaciones web y instalaciones interactivas de forma autónoma y dicta asignaturas de programación en diversas universidades de Barcelona. Forma parte de ZZZINC: zzzinc.net/\r\n\r\nProyectos destacados:\r\n- casastristes - Herramientas de visualización para una vivienda digna: casastristes.org/\r\n- Independent Robotic Community: mediainterventions.net/comunidad/\r\n- Museu de la Patum de Berga: museu.lapatum.cat/\r\n\r\nMarti Sanchez es investigador en inteligencia artificial en la Universidad Pompeu Fabra, departamento SPECS. Como artista de medios interactivos y audiovisuales colabora con KonicTheatr, compañía Sr. Serrano y Institut Fatima realizando numerosas instalaciones interactivas, una de ellas presentada en el centro ZKM y ganadora del premio ciudad de Barcelona (MurMuros de Konic Theatr).\r\n\r\nProyectos destacados:\r\n- Mixed Reality Robot Arena: www.youtube.com/watch?v=HDmjGJ9sqeI\r\n- Sr. Serrano // Contra.Natura: www.youtube.com/watch?v=3RNvxH5cKX0\r\n- Sr. Serrano // Artefact: www.youtube.com/watch?v=opMfQ-hbUD0', '', '', '', 0, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('e4ae82c6a3497c04d2338fe63961c92c', 'Mi proyecto 3', 0, 16, 'root', 'goteo', 0, 0, '2011-07-25', NULL, NULL, NULL, '2011-07-25', 'Super administrador', '', '', '', '', '', 'EspaÃ±a', NULL, '', '', '', '', '', NULL, '', NULL, NULL, '', NULL, '', '');
INSERT INTO `project` VALUES('f1dd9c1678c62273e21b67bff6e8b47a', 'Mi proyecto 1', 0, 59, 'kaime', 'goteo', 0, 0, '2011-07-20', '2011-07-20', NULL, NULL, '2011-07-25', 'Jaume Alemany', '43103776M', '666666666', 'Aquí', '07141', 'Marratxí (Pueblo)', 'España', '', '', 'ssss', 'sss', 's', 's', NULL, 'ssss', 'sss', 2, 'Marratxí (Pueblo)', 2, '', '');
INSERT INTO `project` VALUES('f63dab04c0b63cb02d4d1a85aa738ee3', 'Mi proyecto 1', 1, 31, 'ivan', 'goteo', 0, 0, '2011-07-31', '2011-07-31', NULL, NULL, NULL, 'Ivan Vergés', '', '', '', '', 'la garriga, barcelona', 'EspaÃ±a', '', '', '', '', '', '', NULL, '', '', NULL, 'la garriga, barcelona', NULL, '', '');
INSERT INTO `project` VALUES('fe99373e968b0005e5c2406bc41a3528', 'Fixie per tothom', 1, 78, 'diegobus', 'goteo', 495, 2, '2011-05-10', '2011-07-31', '2011-06-17', NULL, '2011-06-01', 'diego', 'x8562415k', '658125454', 'c/ calle 98, 1º 2º', '08000', 'barcelona', 'España', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla dolor quis elit cursus sit amet tincidunt elit ultrices. Mauris ultricies auctor velit vel tempor. Integer sollicitudin consequat ultrices. Fusce at ante sit amet augue dapibus mattis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam facilisis fermentum vestibulum. Mauris accumsan, ante nec aliquet porttitor, ipsum diam elementum elit, at volutpat nisl nibh in quam. Nunc aliquet arcu quis erat ultricies tristique. Nunc laoreet odio vitae quam porta tincidunt. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur diam augue, lacinia in eleifend id, mollis facilisis sem. Pellentesque suscipit dolor id nisl elementum in blandit turpis tempus. Mauris nec libero dolor, sed volutpat eros. Morbi hendrerit, tellus consequat dictum interdum, massa tellus ornare ante, vitae venenatis ipsum mi viverra urna. Proin varius pulvinar lobortis. Integer luctus tellus vel elit adipiscing feugiat. In hac habitasse platea dictumst. Fusce porta eros molestie orci dignissim mollis. Cras volutpat, turpis a tempus commodo, sapien sem porttitor eros, vitae dapibus nisl ante ut nisi. Pellentesque gravida vehicula ipsum id bibendum. ', 'Pellentesque gravida vehicula ipsum id bibendum. ', 'Proin varius pulvinar lobortis. Integer luctus tellus vel elit adipiscing feugiat. In hac habitasse platea dictumst. Fusce porta eros molestie orci dignissim mollis. Cras volutpat, turpis a tempus commodo, sapien sem porttitor eros, vitae dapibus nisl ante ut nisi. Pellentesque gravida vehicula ipsum id bibendum. ', 'Fusce at ante sit amet augue dapibus mattis. \r\n\r\nClass aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. \r\n\r\nNullam facilisis fermentum vestibulum. Mauris accumsan, ante nec aliquet porttitor, ipsum diam elementum elit, at volutpat nisl nibh in quam. Nunc aliquet arcu quis erat ultricies tristique. Nunc laoreet odio vitae quam porta tincidunt.\r\n\r\nPellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur diam augue, lacinia in eleifend id, mollis facilisis sem. Pellentesque suscipit dolor id nisl elementum in blandit turpis tempus. Mauris nec libero dolor, sed volutpat eros. Morbi hendrerit, tellus consequat dictum interdum, massa tellus ornare ante, vitae venenatis ipsu.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla dolor quis elit cursus sit amet tincidunt elit ultrices. Mauris ultricies auctor velit vel tempor. Integer sollicitudin consequat ultrices. Fusce at ante sit amet augue dapibus mattis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam facilisis fermentum vestibulum. Mauris accumsan, ante nec aliquet porttitor, ipsum diam elementum elit, at volutpat nisl nibh in quam. Nunc aliquet arcu quis erat ultricies tristique. Nunc laoreet odio vitae quam porta tincidunt. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur diam augue, lacinia in eleifend id, mollis facilisis sem. Pellentesque suscipit dolor id nisl elementum in blandit turpis tempus. Mauris nec libero dolor, sed volutpat eros.', NULL, 'Nunc, tristique', 'http://www.youtube.com/watch?v=RpNAofXemdo&feature=related', 1, 'barcelona', 2, 'Nulla pellentesque mi at dolor accumsan accumsan. Praesent arcu nibh, sollicitudin eu fringilla fringilla, rhoncus et mi. Sed sed pretium ipsum. Vestibulum non velit nibh, non dignissim eros. Phasellus varius sodales accumsan.', '');
INSERT INTO `project` VALUES('goteo', 'Goteo', 1, 45, 'goteo', 'goteo', 340, 0, '2011-05-13', '2011-08-02', '0000-00-00', '0000-00-00', '0000-00-00', 'Susana Noguero', 'G63306914', '654321987', 'C/ Montealegre, 5', '8001', 'Barcelona', 'España', '', '', '', '', '', '', '', '', '', 0, '', 0, '', '');
INSERT INTO `project` VALUES('hkp', 'Hkp.', 1, 53, 'tintangibles', 'goteo', 70, 0, '2011-05-13', '2011-07-31', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Barcelona', 'España', '', 'HKp es un proyecto audiovisual y editorial sobre los “hackers” de lo cotidiano. Documenta ejemplos de artefactos (objetos, herramientas, muebles, aparatos, máquinas, software, vehículos), a los que sus usuarios han dado un uso distinto del que tenían originariamente haciendo modificación o ensamblando partes de otros artefactos.\r\n\r\nToma el término hack del mundo del software que expresa un tipo de intervención que corrige o modifica el funcionamiento de un programa.\r\n\r\nHKp investiga en que medida esta manera de pensar y hacer tiene sus raíces en algo que forma parte de la naturaleza humana.\r\nUn propósito del proyecto es detectar y dar visibilidad a un tipo de creatividad que se produce en varios sectores: entorno rural, talleres, industria, hogares, hackers, prácticas artísticas, etc.\r\n\r\nRecomendamos ver algunos ejemplos de, como reciclar una cafetera para fundir plomo, llantas de coche como barbacoa, paraguas como falda impermeable, una rueda de bici como rueda para hilar, una estufa de leña a partir de un compresor de aire en el siguiente enlace:\r\nenlloc.net/hkp/w/index.php/Hacks', '', 'URL proyecto:enlloc.net/hkp/w/\r\n\r\nSector al que va dirigido:un propósito del proyecto es detectar y dar visibilidad a un tipo de creatividad que se produce en varios sectores: entorno rural, talleres, indústria, hogares, hackers, hacktivistas, prácticas artísticas, ...', '', 'Desde el TAG se han impulsado proyectos como:\r\n\r\nGERMINADOR de propuestas de creación colectiva - germinador.net\r\nDesde 2005\r\n\r\nPLANTER software de creación colectiva - planter.germinador.net\r\nDesarrollo de Wikipool y Telps\r\nCon Pimpampum (Dani Julià y Anna Fuster) y Jaume Nualart\r\n2007\r\n\r\nHKp apropiación creativa de los usuarios - enlloc.net/hkp/w\r\nCon Joan Montserrat\r\nprimera fase 2009\r\n\r\nSopadepedres - enlloc.net/sopadepedres\r\nActividades en Maçart (2006) y con el IES A.Deulofeu (2008)\r\n\r\nDesde el colectivo desde 1996 se han realizado intervenciones urbanas (dreceres urbanes, xMataró, ciutats de cordill), instalaciones (AMC, Argila, Cajacabeza), talleres (net.art, deriva urbana, copyleft, creación colectiva) y proyectos de net.art como:\r\nTrencaclosques (2007) www.enlloc.org/trencaclosques\r\nCONSTITUCIÓN editar/discutir (2004-2007)\r\nA-PAM (DEL NAS) (2004) i APAMESNOSEMAPA (2007) - Premio CanariasMediafest 2004 www.enlloc.org/apamesnosemapa\r\nBalcons que diuen no a la guerra (2003)\r\nTol tol tol (1999) - Premio Barcelona Möbius 1999', '', 'apropiación tecnológica, hack, arte, wiki, editorial', 'http://vimeo.com/16201682', 0, 'Barcelona', 2, '', '');
INSERT INTO `project` VALUES('mi-barrio', 'Mi barrio', 1, 48, 'itxaso', 'goteo', 0, 0, '2011-05-13', '2011-07-31', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Bilbao', 'España', '', 'Mi barrio propone, y supone, la creación de una serie de microdocumentales de construcción participativa por parte de los habitantes de los territorios en los que interviene, utilizando herramientas no invasivas y fáciles de utilizar; teléfonos móviles.\r\n\r\nMi barrio se constituye como un ejercicio documental participativo y plural que mapea la geografía nacional en busca de contenidos locales de interés general, fomentando la interacción social, la colaboración y la capacitación tecnológica\r\ny comunicativa de la ciudadanía.\r\n\r\nMi barrio presenta a los barrios y las periferias como entornos de innovación, investigación y cocreación que aprovecha el talento creativo, la diversidad sociocultural, la imaginación, la inventiva, e incluso los factores impredecibles, para reflejar las diferentes realidades que coexisten en los barrios y en las ciudades.', '', 'Sector al que va dirigido:Diferentes tramos de edades, etnias, procedencias de un mismo barrio', '', 'Describe qué experiencia tienes (proyectos anteriores relevantes, organizaciones con o en las que has trabajado, si has dado clases ...). Añade la información que te parezcan relevantes sobre tus capacidades, tus puntos fuertes y cómo los has aplicado en otros proyectos tuyos o de otros (incluye links a ser posible):\r\n\r\nOtros proyectos en los que he participado:\r\n- Proyecto Habla es un ejercicio documental participativo y formativo. Se trata de un ”dinamizador social” que pretende fomentar el empoderamiento de las comunidades más desfavorecidas, potenciando la participación e implicación ciudadana en dichos territorios. Este proyecto se ha realizado en colaboración con la ONGD Anesvad y se ha desarrollado entre los meses de agosto y noviembre de 2010 en Perú y Banglades.\r\nMás info; www.ubiqa.com/seguimos-creyendo-que-es-posible%E2%80%A6-y-vamos-a-contarlo/\r\nwww.proyectohabla.org/, www.anesvad.tv/\r\n\r\n- Aldea DOC\r\nAldea DOC surge como respuesta a la petición de la Concejalía de Innovación y e-Gobierno del Ayuntamiento de Cáceres para la realización de un proyecto participativo sobre la situación del barrio de Aldea Moret de Cáceres.\r\nAldea DOC cartografía las anomalías, las zonas de transición del barrio Aldea Moret, donde la precariedad convive con prácticas de ilegales, para fomentar la participación y la implicación ciudadana en el actual proceso de reconversión de la zona.\r\nwww.ubiqa.com/aldea-doc/', '', 'construción colaborativa, documental, capacitación tecnológica, identidad, territorio', 'http://vimeo.com/12013746', 0, '', 0, '', '');
INSERT INTO `project` VALUES('move-commons', 'Move Commons', 1, 55, 'acomunes', 'goteo', 95, 28, '2011-05-13', '2011-07-28', '2011-07-13', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Madrid', 'España', '', 'Move Commons consiste en una sencilla herramienta que permite que iniciativas, colectivos y ONGs puedan declarar los principios en los que se basan. Las características a las que atiende Move Commons para describir un proyecto son si éste es distribuido, si la participación de sus miembros es horizontal, si se trata de una inciativa sin ánimo de lucro y si el proyecto refuerza el procomún.\r\n\r\nExisten numerosas iniciativas promoviendo los bienes comunes en distintos campos. Sin embargo, sólo unas pocas han alcanzado masa crítica y pueden ser conocidas por una gran comunidad, mientras que la mayoría siguen marginadas e ignoradas. Move Commons (MC) es una herramienta que pretende potenciar la visibilidad y difusión de dichas iniciativas, "dibujando" la red de iniciativas y colectivos relacionados en cualquier lugar, facilitando el descubrimiento mutuo y el alcance de masa crítica en cada campo. Además, cualquier voluntario podrá comprender el enfoque del colectivo fácilmente y encontrar otros colectivos en su ciudad, de sus intereses, o de su campo en movecommons.org. Más implicaciones en movecommons.org/implications\r\n', '- Elige tu MC en movecommons.org/preview\r\n- Las implicaciones de MC movecommons.org/implications\r\n- El blog de MC movecommons.org/blog', 'MC es una herramienta para iniciativas, colectivos, ONGs y movimientos sociales para que declaren los principios básicos a los que están comprometidos. Sigue la misma mecánica de Creative Commons al "etiquetar" los trabajos culturales, proporcionando un sistema de auto-etiquetado estandarizado, usable, bottom-up, para cada iniciativa, con 4 iconos y algunas keywords. Todo está apoyado por contenido semántico para permitir búsquedas del tipo «qué iniciativas existen en Beirut que sean horizontales, sin ánimo de lucro, usando Creative Commons y relacionadas con "educación alternativa" y "adolescentes"» (o otros principios, palabras clave o lugares). Los cuatro principios/iconos que cada iniciativa puede mostrar son: Con/Sin ánimo de lucro; Reproducible/Exclusiva; Horizontal/Jerárquica; Reforzando los Comunes / Otros objetivos (explicados en movecommons.org/preview/ ).\r\nURL proyecto:movecommons.org\r\n\r\nSector al que va dirigido:Colectivos, ONGs, movimientos sociales, cooperativas, activistas, voluntarios', 'MC, aún en un estado alfa de desarrollo, pretende proporcionar una plataforma libre donde múltiples extensiones puedan ser implementadas, como un recomendador de iniciativas similares, mapeo geográfico de iniciativas, estadísticas y gráficas sobre los datos abiertos, visualización de las redes de colectivos, widgets para la web, etc.', 'Desde el año 2002 el colectivo Comunes viene desarrollando diferentes iniciativas y proyectos relacionados con los bienes comunes. Comunes se centra en facilitar el trabajo de otras iniciativas y colectivos a través de herramientas y recursos web. Algunos de nuestros proyectos: 1) ourproject.org: servicios web libres para proyectos sociales, actualmente con más de 800 proyectos de muy diversos campos; 2) Kune, kune.ourproject.org plataforma de creación de proyectos libres, en fase de desarrollo; 3) movecommons.org: herramienta web aquí descrita; y otras citadas en la web del colectivo comunes.org', '', 'herramienta, web, colectivos, ongs, movimientos sociales, activismo, voluntariado, creative commons, web semántica, bienes comunes, procomún', '', 0, 'Madrid', 0, '', '');
INSERT INTO `project` VALUES('nodo-movil', 'Nodo Móvil', 2, 52, 'efoglia', 'goteo', 255, 39, '2011-05-13', '2011-07-06', '2011-07-05', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Barcelona', 'España', '', 'Es una estación de transmisión libre, una infraestructura de telecomunicación inalámbrica móvil, que se puede usar en el entorno urbano y permite el mallado digital a través de redes ciudadanas. Cuenta con una capacidad de autonomía y configuración propia. Este Nodo Móvil puede construir una LAN (Local Area Network) en territorios irregulares, conectándose entre sí de forma independiente a las empresas de telecomunicación.\r\nSus aplicaciones son multidisciplinares: educación, activismo, arte, primeros auxilios, etc. ', '', 'Este proyecto suma flexibilidad y propone posibilidades de interconexión con los dispositivos digitales que habitan las ciudades contemporáneas. El Nodo Móvil es un dispositivo de altas prestaciones, permite la transmisión de datos con un ancho de banda potente.\r\nEl Nodo Móvil incorpora movilidad a la red guifi.net, la extiende y provoca nuevas prácticas sociales en el entorno urbano mezclando espacio público físico y digital.\r\n\r\nURL proyecto:www.proyectoliquido.com/Mobile_Node.htm\r\n\r\nSector al que va dirigido:Aplicaciones multidisciplinares: educación, activismo, arte, primeros auxilios, etc.', 'Actualmente tenemos 1 Nodo Móvil en pruebas y nos interesa construir (con este financiamiento) un segundo NM.\r\nLo importante del proyecto es mejorar el diseño y su rendimiento, de esta forma se podrá duplicar más facilmente y las ciudadanas podrán acceder a esta tecnología a bajo costo con réditos sociales importantes.\r\nLo vital en este momento es tener recursos para seguirlo desarrollando.\r\nEl proyecto tiene varias fases de implementación. Se busca incorporar sistemas diversos de mallado como tecnología GPS, microcontroladores, Bluetooth, etc.\r\nLugares: El proyecto tiene su base en Barcelona en donde actualmente se diseña, pero su conectividad es posible en casi todo el territorio catalán en donde se encuentre un nodo de guifi.net. Los otros lugares citados en el mapa son posibilidades que ya cuentan con interlocutores interesados.', 'La filosofía detrás del proyecto es la promovida por guifi.net a través de los últimos 6 años. Actualmente esta red ciudadana interconecta más de 11,000 nodos totalmente gestionados por los usuarios. \r\n\r\nAcá hay una idea general:\r\n\r\nwww.efrainfoglia.net/', '', 'Open City, Xarxa Oberta, Movilidad, Espacio Público, Portabilidad.', 'http://www.youtube.com/watch?v=BOryyEv9qMA', 0, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('oh-oh-fase-2', 'Oh_Oh fase 2', 1, 50, 'dcuartielles', 'goteo', 70, 0, '2011-05-13', '2011-07-05', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Malm�', 'Suecia', '', 'Oh_Oh es una plataforma robótica de bajo coste para ser usada en educación en secundaria. El primer prototipo fue financiado por el CCEMX como parte del proyecto "La Maquila del Faro de Oriente", que consistió en la creación de actividades educativas basadas en el uso de hardware y software libre para chicos/as de edades comprendidas entre 10 y 18 anos. La creación de esta plataforma ha atraído el interés de otras personas interesadas en la robótica a nivel educativo por lo que me he dado cuenta que seria interesante dedicar algo mas de esfuerzo a concretar la plataforma a un nivel que sea sencilla de reproducir.\r\n\r\nLa idea de este proyecto es hacer una nueva interacción del diseño de hardware así como finalizar la revisión del software con el que se cuenta en la actualidad para poder ofrecerlo de forma libre a aquellos interesados en la realización de sus propios robots.', '', 'URL del proyecto: http://code.google.com/p/arduino-compatible-robots/wiki/Oh_Oh\r\n\r\nSector al que va dirigido: sistema educativo, secundaria, profesores del area de tecnologia, centros de formacion profesional, clubes de tiempo libre', '', 'Anteriormente he hecho:\r\n\r\n- creador del proyecto (ahora empresa) de hardware libre www.arduino.cc\r\n- creador de la empresa www.1scale1.com, Malmo, Suecia\r\n- creador del estudio de diseño Aeswad, Malmo, Suecia webzone.k3.mah.se\r\n\r\nDoy clases en la Universidad de Malmo, Suecia, desde el 2001, soy director del laboratorio de prototipos de productos.', '', 'electronica, software, tutoriales, educacion, comunidad', '', 3, 'Malmö, Suecia', 0, '', '');
INSERT INTO `project` VALUES('pliegos', 'PliegOS', 2, 75, 'esenabre', 'goteo', 0, 38, '2011-06-15', '2011-07-31', '2011-07-04', NULL, NULL, 'Enric Senabre Hidalgo', '46649545W', '932215515', 'Moscou 16, 1º 1ª', '08005', 'Barcelona', 'España', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. Pellentesque molestie mollis quam a iaculis. Nunc feugiat consectetur mauris quis blandit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed imperdiet scelerisque ante, eu rhoncus augue dictum eu. Sed imperdiet imperdiet semper. Maecenas in dolor dolor, quis ullamcorper velit. Duis ut ligula tellus, eget luctus arcu. Phasellus volutpat euismod tortor, et dignissim nulla consectetur euismod. Nulla pretium laoreet arcu, vitae consectetur nisi imperdiet a. Morbi arcu lorem, ornare condimentum pulvinar non, mattis sed tortor.\r\n\r\nVivamus sollicitudin urna ac massa iaculis consectetur. Etiam aliquet tempor quam ac tempor. Morbi dictum diam et lacus faucibus sodales. Phasellus commodo purus quam. Sed interdum luctus posuere. Suspendisse vehicula justo a mi commodo interdum. Nunc malesuada bibendum quam, id blandit dolor volutpat ut. In. ', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. Pellentesque molestie mollis quam a iaculis. Nunc feugiat consectetur mauris quis blandit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed imperdiet scelerisque ante, eu rhoncus augue dictum eu. Sed imperdiet imperdiet semper.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. Pellentesque molestie mollis quam a iaculis. Nunc feugiat consectetur mauris quis blandit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed imperdiet scelerisque ante, eu rhoncus augue dictum eu. Sed imperdiet imperdiet semper.', '- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. \r\n- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. \r\n- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. \r\n- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. ', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. Pellentesque molestie mollis quam a iaculis. Nunc feugiat consectetur mauris quis blandit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed imperdiet scelerisque ante, eu rhoncus augue dictum eu. Sed imperdiet imperdiet semper.', NULL, 'salud, humor', 'http://www.youtube.com/watch?v=ab5pnqCF0Kc', 4, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('robocicla', 'Robocicla', 1, 50, 'carlaboserman', 'goteo', 145, 20, '2011-05-13', '2011-07-25', '2011-07-05', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Sevilla', 'España', '', 'Robocicla es una iniciativa Extremeña que mezcla arte, creatividad, reciclaje y tecnología.\r\nEs una herramienta pensada para que niñ@s, jóvenes, padres, madres y educador@s se diviertan y aprendan junt@s acerca del conocimiento y la cultura libres.\r\n\r\nA través de fichas de auto-construcción ilustradas, aprenderemos a confeccionar Robots usando tecnología reciclada.\r\nEstos Robots nos contarán cómo liberar, compartir y construir entre todos el conocimiento.\r\nEn www.robocicla.net podrás conocer experiencias de robociclaje y descargar todo el material didáctico para usarlo cuando quieras.\r\nEn los Robocicla_TALLERES aprenderemos\r\n1. Sobre la importancia de reciclar el material tecnológico, su impacto medioambiental en el mundo, y las posibilidades artísticas que nos ofrece el reciclaje.\r\n2. Desmontaje de Equipos, para separar lo que aún tiene vida útil, y lo que usaremos para construir nuestros robots. Papelera electrónica de reciclaje.\r\n3. Construiremos a Hackerina y conoceremos los principios de la Ética Hacker.\r\n4. Aprenderemos electrónica básica: incorporando leds, interruptores, y ventiladores a nuestros robots.\r\n5. Documentaremos el taller y seremos bloguers de Robocila.\r\n\r\nActualmente el equipo de robocicla trabaja en la elaboración de material didáctico para niñ@s, en forma de cuentojuegos ilustrados acerca de la historia de cada uno de los Robots, que serán publicados digital y analógicamente.\r\n\r\nNota: Mientras buscaba financiación colectiva, Robocicla empezo una gira de 20 talleres por todo el territorio extremeño promovida por el Consorcio IdenTic a través de la Red de Telecentros Extremeños', '', 'URL proyecto:www.robocicla.net\r\n\r\nSector al que va dirigido:comunidad educativa, niñ@s de todas las edades, frikis, instituciones, comunidad software libre / cultura libre', '', 'Carla Boserman // Licenciada en Bellas artes entre Sevilla y Atenas en la especialidad de Diseño Gráfico y Grabado. Mi experiencia profesional tiene que ver con la gestión creativo-cultural de proyectos de arte colaborativo, con especial atención al enfoque pedagógico y terapéutico de los proyectos.\r\nDibujo, creo y enredo. Y sobre todo, me muevo.\r\nTrabajo en la elaboración de diarios viajes ilustrados, aprendo cerámica y empiezo a formarme en el campo del arte terapia. Enamorada de Extremadura, donde he vivido y trabajado en los últimos tiempos, he podido desarrollar el proyecto La Siberia Mail Art www.siberiapostal.net haciendo de mis dibujos una herramienta de puesta de valor de un territorio y sus gentes.\r\n\r\n+ Breve CV\r\n\r\n2007 Ilustraciones/Colaboración en el proyecto Tecnopaisajes. TCS 2 Extremadura.\r\n2007 Primer premio Creativa 07 Consejería de Innovación Ciencia y Empresa Junta de Andalucía para el desarrollo del\r\nproyecto Pista Digital plataforma itinerante para la cultura. www.pistadigital.org\r\n2007 Premio INICIARTE de la Junta de Andalucía para la realización del proyecto Larache se mueve Festival entre las\r\ndos orillas. Sevilla y Larache (Marruecos).\r\n2008+2009 Conceptualización, diseño y dinamización editorial del Proyecto Robinsones Urbanos, un espacio\r\nciudadano digital y una herramienta para pacientes con Trastorno Bipolar, que cuenta con el apoyo de Ciudadanía Digital,\r\nConsejería de Innovación de la Junta de Andalucía. robinsonesurbanos.org\r\n2008 Invitada al Simposium Nomadism: Art and New Technologies. Theatre de la Villette (Paris).\r\n2008 Ilustraciones y documentación para el Taller de reciclaje del agua: Aguas Mil, CALA. Alburquerque (Badajoz).\r\n2008 Exposición de Pinturas para el evento Senegal se Mueve de la ONG AEXCRAM (Mérida)\r\n2009 Diseño de Postal para la conservación del patrimonio de Canido (Pontevedra).\r\n2009 Diseño de la Exposición: Miradas Cruzadas Sobre el Patrimonio Marroquí. Sala Diagonal 3 (Sevilla).\r\n2010 Ponencia sobre el proyecto La Siberia mail art, Escuela de Arte de Mérida.\r\n2010 Ilustraciones para el libro : Historia del encaje de bolillos en Extremadura.\r\n2010 PROCESO DE CONSTRUCCIÓN COLECTIVA - The Coffee Break 2010 (Junta de Extremadura)\r\nwww.thecooffebreak.biz\r\n2010 Taller de Creaciones Fantásticas//Reciclando Tecnología – Festival NTX (Los Santos de Maimona, Badajoz)\r\nwww.festivalntx.com/ntx2010/\r\n2010 Beca a la Creación Joven para el proyecto Taller de Creaciones Fantásticas 2.0 Robocicla.net\r\nConsejería de los jóvenes y del deporte-Junta de Extremadura. www.robocicla.net\r\n2010 Diseño Packing para el documental - La Esquina del tiempo -Galizuela/Badajoz de Carla Alonso.', '', 'cultura libre, reciclaje, software libre, pedagogía, creacion', 'http://www.youtube.com/watch?v=XNVCetMiUsY', 0, 'Sevilla', 0, '', '');
INSERT INTO `project` VALUES('todojunto-letterpress', 'Todojunto Letterpress', 1, 52, 'todojunto', 'goteo', 25, 0, '2011-05-13', '2011-07-25', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Barcelona', 'España', '', 'Todojunto cuenta actualmente con un taller de tipografía móvil que ha nacido de algunos elementos que hemos recuperado de una imprenta de barrio con la que trabajábamos en Barcelona. Todojunto Letterpress intenta poner en marcha un espacio para recuperar esta técnica de impresión, y utilizarlo como un espacio de aprendizaje y discusión sobre la tipografía en general, el diseño, y las técnicas de producción gráfica.\r\nBásicamente se nececitan mas juegos de tipografías de plomo y Madera, los muebles para guardarlas, y equipo que se pueda recuperar de otras imprentas que no lo usen mas. Hemos calculado que esta primera fase, para completar lo que ya tenemos en nuestro taller, necesita una inversión de 2000 euros, y horas de trabajo, en el proceso de clasificación, organización y limpieza de los materiales recuperados. Ofrecemos a cambio workshops, impresiones personalizadas para las personas hagan aportes económicos superiores a los 150€.\r\nTambién ofrecemos en retorno, una pequeña publicación con material pedagogico sobre la técnica, una especie de Manual/fanzine del taller de Todojunto letterpress.', '', 'URL del proyecto: www.todojunto.net\r\n\r\nSector al que va dirigido: Impresores amateurs, Estudiantes de diseño y tipografía, Profesores, interesados en la producción gráfica, artistas, Barcelona', '', 'Tenemos una parte del taller de tipos móviles en funcionamiento desde hace aproximandamente 8 meses, tiempo en el que hemos experimentado con esta técnica, somos un estudio de comunicación gráfica, esta es la dirección de nuestro sitio web www.todojunto.net\r\n\r\ntambién venimos de proyectos independintes de ilustración, comunicación y proyectos culturales:\r\n\r\nwww.jstk.org (Andrea Gómez y Ricardo Duque)\r\nwww.miuk.ws (Tiago Pina)\r\nwww.andreagomez.info (Andrea Gómez)\r\n\r\ny estamos vinculados con el proyecto de La Fanzinoteca Ambulant: www.fanzinoteca.net', '', 'Impresión, grafica, técnica, tipografías móviles, recuperación', 'http://vimeo.com/17760187', 1, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('tutorial-goteo', 'Tutorial Goteo', 3, 77, 'demo', 'goteo', 30, 34, '2011-07-26', '2011-07-27', '2011-07-27', NULL, NULL, 'Demo Goteo', '46649545W', '936924182', 'Moscou 16, 1º 1ª', '08005', 'Barcelona', 'España', '', 'Aquí se debe describir el proyecto con un mínimo de 80 palabras (con menos marcará error), explicándolo de modo que sea fácil de entender para cualquier persona. También hay que darle un enfoque atractivo y social, resumiendo sus características básicas. \r\n\r\nAdemás es donde linkar un vídeo en que se presente brevemente, algo que consideramos muy importante para poder difundirlo y que llegue al máximo posible de personas.\r\n\r\nEs lo primero con lo que cualquier usuario de la red se encontrará, así que se debe cuidar la redacción y evitar las faltas de ortografía. \r\n\r\nHay campos obligatorios como incluir un vídeo o subir imágenes. Esto es así porque los consideramos imprescindibles para empezar con éxito una campaña de recaudación de fondos mediante Goteo.\r\n\r\nPara este apartado y el resto de secciones del formulario de Goteo es posible utilizar código HTML, especialmente para links a páginas web y puntualmente el uso de cursivas o negritas (pero sin abusar :)\r\n\r\nOtras secciones importantes dentro del formulario del proyecto, que se solicitan cuando éste se da de alta, son las categorías y palabras clave que permiten buscarlo en la plataforma, el estado de desarrollo en que se encuentra y el alcance geográfico previsto.', 'Aquí recomendamos explicar de modo resumido qué motivos o circunstancias han llevado a idear el proyecto. Así podrá conectar con más personas motivadas por ese mismo tipo de intereses, problemáticas o gustos.', 'Aquí se debe describir brevemente el proyecto de modo conceptual, técnico o práctico. Por ejemplo detallando sus características de funcionamiento, o en qué partes consistirá. Debe ayudar a hacerse una idea sobre cómo será una vez acabado y todo lo que la gente podrá hacer con él.', 'Sección para enumerar las metas principales del proyecto, a corto y largo plazo, en todos los aspectos que se considere importante destacar. Se trata de otra oportunidad para contactar y conseguir el apoyo de gente que simpatice con esos objetivos.', 'Este apartado es muy importante para generar confianza en los cofinanciadores potenciales del proyecto, explicando la trayectoria y experiencia previa de la persona o grupo impulsor del proyecto. Qué experiencia tiene relacionada con la propuesta y con qué equipo de personas, recursos y/o infraestructuras cuenta.', NULL, 'Goteo', 'http://vimeo.com/26970224', 1, 'Barcelona', 4, 'La herramienta también permite indicar si se cuenta con recursos adicionales, a parte de los que se solicitan, para llevar a cabo el proyecto: fuentes de financiación, recursos propios o bien ya se ha hecho acopio de materiales. Puede suponer un aliciente para los potenciales cofinanciadores del proyecto.', '');
INSERT INTO `project` VALUES('tweetometro', 'Tweetometro', 3, 81, 'demo', 'goteo', 0, 34, '2011-07-26', '2011-07-27', '2011-07-27', NULL, NULL, 'Demo Goteo', '46649545W', '936924182', 'Moscou 16, 1º 1ª', '08005', 'Barcelona', 'España', '', 'Plataforma experimental de votación viral mediante tweets, actualmente en fase alfa. Una aplicación ágil y social para llegar a acuerdos, tomar decisiones colectivamente o elegir la mejor idea presentada. Al mismo tiempo sirve para activar y promover (micro)conversaciones focalizadas en temas concretos. \r\n\r\nSe trata de una aplicación ya en desarrollo pero que aún se puede optimizar para que generalice su uso. La queremos mejorar para que cualquiera la pueda utilizar, como software libre o servicio online, y que además funcione mediante SMS. \r\n\r\nEn una fase de pruebas con alta movilización social a través de Twitter con motivo del #15M, tras  20 días de participación abierta, tanto para emitir votos positivos como negativos sobre ocho propuestas planteadas, se recopilaron un total de 1.122 votos a través del hastag #tweetometro. De estos 1.045 han sido votos positivos y 77 negativos (estos últimos ponderando a la baja los porcentajes de las diferentes propuestas).', 'Tras testear la herramienta en una votación de proyectos tecnosociales en 2009 durante las jornadas <a href="http://www.urbanlabs.net/">UrbanLabs</a>, se abrió la posibilidad de seguir experimentando con ella para generar consenso y discusión en el contexto de las movilizaciones posteriores al #15M. Creemos que se demostró su utilidad pero también que necesita diferentes mejoras, y que así pueda utilizarla más gente.', 'La plataforma de Tweetometro consiste en una interficie que muestra resultados en tiempo real de determinadas palabras clave o hashtags enviados desde Twitter. Permite comparar de manera estadística su evolución y por tanto que la popular red social se convierta en una herramienta de voto. Ofrece de momento dos tipos de visualización (termómetros y porcentajes) y un administrador básico.', 'Los objetivos actuales del proyecto son mejorar la interfície para incorporar cuentas de Twitter, ampliar los formatos de visualización, hacer más usable la página de administración, implementar la posibilidad de emitir votos por SMS y en definitiva hacer una buena herramienta de software libre fácil de usar por cualquiera. ', 'Platoniq es una organización internacional de productores culturales y desarrolladores de software, pionera en la producción y distribución de cultura copyleft. Desde el año 2001, llevamos desarrollando proyectos relacionados con la cultura libre y TICs. Hemos producido software libre como http://burnstation.net cuyo código está disponible en para las distribuciones de GNU/Linux Ubuntu y Debian. \r\nTambién hemos desarrollado entre otras plataformas que dan un servicio gratuito para la creación de canales de radio por Internet como http://open-server.org o de intercambio de conocimientos entre la ciudadanía http://bancocomun.org o un tablón de anuncios audiovisuales de ofertas y demandas http://eseoese.org. Reproducidos y utilizados por muchos colectivos y entidades ciudadanas de Europa y Latinoamérica especialmente. \r\nHemos obtenido reconocimiento internacional por nuestro trabajo con premios y menciones internacionales como en la Transmediales de Berlín, Transxitio de Mexico DF, Unesco Digital Arts Awards, etc. \r\nContamos con servidores propios y  una red de colaboradores programadores, diseñadores y creativos para el desarrollo de nuestros proyectos. \r\n\r\n\r\n\r\n', NULL, 'Twitter, consenso, ', 'http://www.youtube.com/watch?v=O69x4oyuLzI', 3, 'Barcelona', 4, ' ', '');
INSERT INTO `project` VALUES('urban-social-design-database', 'Urban Social Design Database', 1, 50, 'domenico', 'goteo', 115, 20, '2011-05-13', '2011-07-31', '2011-07-05', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Madrid', 'España', '', 'Crear una base de datos digital de proyectos desarrollados por los estudiantes durante su carrera universitaria. Ofrecer un nuevo espacio de conexión y dialogo entre el mundo académico y la ciudadanía.\r\n\r\nTodo el material almacenado en la base de dados será de acceso público y distribuido con licencia del tipo Creative Commons.\r\n\r\nEl marco general del proyecto se basa sobre el concepto de Urban Social Design entendido como el diseño de ambientes, espacios y dinámicas con el fin de mejorar las relaciones sociales, generando las condiciones para la interacción y la auto-organización entre las personas y su medio ambiente. ', '', 'URL proyecto: www.archtlas.com\r\n\r\nSector al que va dirigido: estudiantes, jóvenes profesionales, ciudadanos, activadores urbanos', '', 'El proyecto esta directamente asociado a una serie de cursos on-line que (temporalmente) llamamos Urban Social Design Institute (ecosistemaurbano.tv/tag/urban-social-design/).\r\n\r\nLa plataforma web que se quiere utilizar para el proyecto ya esta funcionando desde unos meses: www.archtlas.com\r\n\r\nEl principal promotor del proyecto es la agencia Ecosistema Urbano.\r\n\r\nEcosistema Urbano es una sociedad de profesionales que entienden la ciudad como fenómeno complejo, situándose en un punto intermedio entre arquitectura, ingeniería, urbanismo y sociología. Este ámbito de interés lo denominamos “sostenibilidad urbana creativa”, desde donde intentamos transformar la realidad contemporánea a través de innovación, creatividad y sobre todo acción.\r\n\r\nSus integrantes principales han sido formados entre distintas universidades europeas (Madrid, Londres, Bruselas, Roma, Paris) y proceden de entornos urbanos diversos. Ejercen la docencia como profesores visitantes, impartiendo talleres y conferencias en las principales escuelas e instituciones internacionales (Harvard, Yale, UCLA, Cornell, Iberoamericana, RIBA, Copenague, Munich, Paris, Milán, Shanghai…).Desde 2000, su trabajo ha sido premiado nacional e internacionalmente en más de 30 ocasiones.\r\n\r\nEn 2005 recibieron el European Acknowledgement Award otorgado por la Holcim Foundation for Sustainable Construction (Ginebra, 2005). En 2006, el premio de la Architectural Association and the Environments, Ecology and Sustainability Research Cluster (Londres, 2006). En 2007 fueron nominados para el premio europeo Mies Van Der Rohe Award “Arquitecto Europeo Emergente” y galardonados como oficina emergente con el premio “AR AWARD for emerging architecture” (London) entre 400 participantes de todo el mundo. En 2008 recibieron el primer premio GENERACIÓN PRÓXIMA de la Fundación Próxima Arquía y en 2009 el Silver Award Europe de la Holcim Foundation for Sustainable Construction entre más de 500 equipos, siendo más tarde nominados como finalistas a nivel mundial.\r\n\r\nEn los últimos años su trabajo se ha difundido en más de 90 medios de 30 países (prensa nacional e internacional, programas de televisión y publicaciones especializadas) y han sido expuestos en numerosas galerías, museos e instituciones (Bienal de Venecia, "Le Sommer Environnement" en París, Spazio FMG de Milán, Seul Design Olimpics, Louisiana Museum of Modern Art de Copenague, Boston Society of Architects, Matadero Madrid, Circulo de Bellas Artes de Madrid, COAM, COAC,...). En la actualidad exponen en el Design Museum de Londres dentro de la muestra "sustainable futures"y preparan una exposición monográfica sobre su trabajo en el Deutsches Architektur Zentrum de Berlin.\r\n\r\nActualmente el equipo está involucrado en proyectos I+D sobre el futuro urbano "ciudades eco-tecno-lógicas" Proyecto CETICA, financiado por el Ministerio Industria dentro del programa CENIT. En paralelo, desarrollan una labor de difusión a través de nuevas tecnologías de la información, donde han generado una plataforma de comunicación que crea redes sociales y gestiona canales de difusión en internet sobre sostenibilidad urbana creativa (www.ecosistemaurbano.org).\r\n\r\nEn la actualidad trabajan en propuestas de transformación urbana para diferentes ciudades y sus proyectos más recientes incluyen el diseño de un espacio público de experimentación interactivo para la Expo Universal de Shanghai 2010 y la propuesta urbana Plaza Ecópolis de Rivas en la periferia de Madrid.', '', 'diseño, proyectos, base de datos, difusión, educación', '', 0, '', 0, '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project_category`
--

DROP TABLE IF EXISTS `project_category`;
CREATE TABLE `project_category` (
  `project` varchar(50) NOT NULL,
  `category` int(12) NOT NULL,
  UNIQUE KEY `project_category` (`project`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';

--
-- Volcar la base de datos para la tabla `project_category`
--

INSERT INTO `project_category` VALUES('2c667d6a62707f369bad654174116a1e', 2);
INSERT INTO `project_category` VALUES('2c667d6a62707f369bad654174116a1e', 7);
INSERT INTO `project_category` VALUES('2c667d6a62707f369bad654174116a1e', 9);
INSERT INTO `project_category` VALUES('2c667d6a62707f369bad654174116a1e', 14);
INSERT INTO `project_category` VALUES('8851739335520c5eeea01cd745d0442d', 6);
INSERT INTO `project_category` VALUES('96ce377a34e515ab748a096093187d53', 2);
INSERT INTO `project_category` VALUES('96ce377a34e515ab748a096093187d53', 6);
INSERT INTO `project_category` VALUES('a565092b772c29abc1b92f999af2f2fb', 6);
INSERT INTO `project_category` VALUES('archinhand-architecture-in-your-hand', 7);
INSERT INTO `project_category` VALUES('archinhand-architecture-in-your-hand', 10);
INSERT INTO `project_category` VALUES('f1dd9c1678c62273e21b67bff6e8b47a', 6);
INSERT INTO `project_category` VALUES('f1dd9c1678c62273e21b67bff6e8b47a', 9);
INSERT INTO `project_category` VALUES('f1dd9c1678c62273e21b67bff6e8b47a', 10);
INSERT INTO `project_category` VALUES('f1dd9c1678c62273e21b67bff6e8b47a', 11);
INSERT INTO `project_category` VALUES('f1dd9c1678c62273e21b67bff6e8b47a', 14);
INSERT INTO `project_category` VALUES('fe99373e968b0005e5c2406bc41a3528', 6);
INSERT INTO `project_category` VALUES('move-commons', 2);
INSERT INTO `project_category` VALUES('move-commons', 7);
INSERT INTO `project_category` VALUES('oh-oh-fase-2', 7);
INSERT INTO `project_category` VALUES('oh-oh-fase-2', 10);
INSERT INTO `project_category` VALUES('pliegos', 6);
INSERT INTO `project_category` VALUES('pliegos', 11);
INSERT INTO `project_category` VALUES('pliegos', 13);
INSERT INTO `project_category` VALUES('robocicla', 7);
INSERT INTO `project_category` VALUES('robocicla', 10);
INSERT INTO `project_category` VALUES('robocicla', 11);
INSERT INTO `project_category` VALUES('tutorial-goteo', 6);
INSERT INTO `project_category` VALUES('tutorial-goteo', 10);
INSERT INTO `project_category` VALUES('tweetometro', 6);
INSERT INTO `project_category` VALUES('tweetometro', 7);
INSERT INTO `project_category` VALUES('urban-social-design-database', 7);
INSERT INTO `project_category` VALUES('urban-social-design-database', 10);
INSERT INTO `project_category` VALUES('urban-social-design-database', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project_image`
--

DROP TABLE IF EXISTS `project_image`;
CREATE TABLE `project_image` (
  `project` varchar(50) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`project`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `project_image`
--

INSERT INTO `project_image` VALUES('2c667d6a62707f369bad654174116a1e', 15);
INSERT INTO `project_image` VALUES('2c667d6a62707f369bad654174116a1e', 66);
INSERT INTO `project_image` VALUES('96ce377a34e515ab748a096093187d53', 83);
INSERT INTO `project_image` VALUES('96ce377a34e515ab748a096093187d53', 84);
INSERT INTO `project_image` VALUES('96ce377a34e515ab748a096093187d53', 85);
INSERT INTO `project_image` VALUES('96ce377a34e515ab748a096093187d53', 86);
INSERT INTO `project_image` VALUES('96ce377a34e515ab748a096093187d53', 87);
INSERT INTO `project_image` VALUES('96ce377a34e515ab748a096093187d53', 88);
INSERT INTO `project_image` VALUES('96ce377a34e515ab748a096093187d53', 89);
INSERT INTO `project_image` VALUES('a565092b772c29abc1b92f999af2f2fb', 21);
INSERT INTO `project_image` VALUES('a565092b772c29abc1b92f999af2f2fb', 64);
INSERT INTO `project_image` VALUES('a565092b772c29abc1b92f999af2f2fb', 65);
INSERT INTO `project_image` VALUES('archinhand-architecture-in-your-hand', 57);
INSERT INTO `project_image` VALUES('canal-alfa', 62);
INSERT INTO `project_image` VALUES('fe99373e968b0005e5c2406bc41a3528', 14);
INSERT INTO `project_image` VALUES('fe99373e968b0005e5c2406bc41a3528', 81);
INSERT INTO `project_image` VALUES('hkp', 59);
INSERT INTO `project_image` VALUES('mi-barrio', 58);
INSERT INTO `project_image` VALUES('move-commons', 60);
INSERT INTO `project_image` VALUES('nodo-movil', 61);
INSERT INTO `project_image` VALUES('oh-oh-fase-2', 55);
INSERT INTO `project_image` VALUES('pliegos', 28);
INSERT INTO `project_image` VALUES('robocicla', 63);
INSERT INTO `project_image` VALUES('todojunto-letterpress', 54);
INSERT INTO `project_image` VALUES('tutorial-goteo', 115);
INSERT INTO `project_image` VALUES('tutorial-goteo', 116);
INSERT INTO `project_image` VALUES('tweetometro', 106);
INSERT INTO `project_image` VALUES('tweetometro', 107);
INSERT INTO `project_image` VALUES('tweetometro', 108);
INSERT INTO `project_image` VALUES('urban-social-design-database', 56);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promote`
--

DROP TABLE IF EXISTS `promote`;
CREATE TABLE `promote` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `title` tinytext,
  `description` text,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_node` (`node`,`project`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Proyectos destacados' AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `promote`
--

INSERT INTO `promote` VALUES(1, 'goteo', 'tutorial-goteo', 'Tutorial Goteo', 'Proyecto piloto que explica cada campo necesario en un proyecto, con un vídeo tutorial.', 2);
INSERT INTO `promote` VALUES(2, 'goteo', 'tweetometro', 'Demo proyecto', 'Ejemplo detallado con todos los campos a tener en cuenta para proponer un proyecto en Goteo.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promote_lang`
--

DROP TABLE IF EXISTS `promote_lang`;
CREATE TABLE `promote_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `promote_lang`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purpose`
--

DROP TABLE IF EXISTS `purpose`;
CREATE TABLE `purpose` (
  `text` varchar(50) NOT NULL,
  `purpose` tinytext NOT NULL,
  `html` tinyint(1) DEFAULT NULL COMMENT 'Si el texto lleva formato html',
  `group` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Agrupacion de uso',
  PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Explicación del propósito de los textos';

--
-- Volcar la base de datos para la tabla `purpose`
--

INSERT INTO `purpose` VALUES('blog-coments-header', 'Comentarios', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-comments', 'Comentarios', NULL, 'general');
INSERT INTO `purpose` VALUES('blog-comments_no_allowed', 'No se permiten comentarios en  esta entrada', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-comments_no_comments', 'No hay comentarios en esta entrada', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-main-header', 'Goteo blog', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-no_comments', 'Sin comentarios', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-no_posts', 'No se ha publicado ninguna entrada de actualizacion', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-send_comment-button', 'Enviar', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-send_comment-header', 'Escribe tu comentario', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-side-last_comments', 'Últimos comentarios', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-side-last_posts', 'Últimas entradas', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-side-tags', 'Categorías', NULL, 'blog');
INSERT INTO `purpose` VALUES('contact-email-field', 'Email', NULL, 'contact');
INSERT INTO `purpose` VALUES('contact-message-field', 'Mensaje', NULL, 'contact');
INSERT INTO `purpose` VALUES('contact-send_message-button', 'Enviar', NULL, 'contact');
INSERT INTO `purpose` VALUES('contact-send_message-header', 'Envíanos un mensaje', NULL, 'contact');
INSERT INTO `purpose` VALUES('contact-subject-field', 'Asunto', NULL, 'contact');
INSERT INTO `purpose` VALUES('costs-field-amount', 'Valor', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-cost', 'Coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-dates', 'Fechas', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-date_from', 'Desde', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-date_until', 'Hasta', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-description', 'Descripción', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-required_cost', 'Este coste es', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-required_cost-no', 'Secundario', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-required_cost-yes', 'Imprescindible', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-resoure', 'Otros recursos', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-schedule', 'Agenda', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-type', 'Tipo', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-fields-main-title', 'Desglose de costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-fields-metter-title', 'Totales', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-fields-resources-title', 'Recurso', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-main-header', 'Proyecto/Costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('discover-banner-header', 'Por categoria, lugar o retorno,\r\nencuentra el proyecto con el que más te identificas', 1, 'banners');
INSERT INTO `purpose` VALUES('discover-group-all-header', 'Proyectos en campaña', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-group-outdate-header', 'Proyectos a punto de caducar', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-group-popular-header', 'Proyectos más populares', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-group-recent-header', 'Proyectos recientes', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-group-success-header', 'Proyectos exitosos', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-results-empty', 'No hemos encontrado ningún proyecto que cumpla los criterios de búsqueda', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-results-header', 'Resultado de búsqueda', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-button', 'Buscar', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bycategory-all', 'TODAS', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bycategory-header', 'Por categoría:', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bycontent-header', 'Por contenido:', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bylocation-all', 'TODOS', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bylocation-header', 'Por lugar:', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-byreward-all', 'TODOS', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-byreward-header', 'Por retorno:', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-header', 'Busca un proyecto', NULL, 'discover');
INSERT INTO `purpose` VALUES('error-contact-email-empty', 'No has puesto tu email', NULL, 'contact');
INSERT INTO `purpose` VALUES('error-contact-email-invalid', 'El email que has puesto no es válido', NULL, 'contact');
INSERT INTO `purpose` VALUES('error-contact-message-empty', 'No has puesto ningún mensaje', NULL, 'contact');
INSERT INTO `purpose` VALUES('error-contact-subject-empty', 'No has puesto el asunto', NULL, 'contact');
INSERT INTO `purpose` VALUES('error-image-name', 'Texto error-image-name', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-size', 'Texto error-image-size', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-size-too-large', 'Texto error-image-size-too-large', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-tmp', 'Texto error-image-tmp', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-type', 'Texto error-image-type', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-type-not-allowed', 'Texto tipos de imagen permitidos', NULL, 'general');
INSERT INTO `purpose` VALUES('error-register-email', 'La dirección de correo es obligatoria.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-email-confirm', 'La comprobación de email no coincide.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-email-exists', 'El dirección de correo ya corresponde a un usuario registrado.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-invalid-password', 'La contraseña no es valida.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-password-confirm', 'La comprobación de contraseña no coincide.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-pasword', 'La contraseña no puede estar vacía.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-pasword-empty', 'Texto error-register-pasword-empty', NULL, 'general');
INSERT INTO `purpose` VALUES('error-register-short-password', 'La contraseña debe contener un mínimo de 8 caracteres.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-user-exists', 'El usuario ya existe.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-username', 'El nombre de usuario usuario es obligatorio.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-user-email-confirm', 'Texto error-user-email-confirm', NULL, 'general');
INSERT INTO `purpose` VALUES('error-user-email-empty', 'Texto error-user-email-empty', NULL, 'general');
INSERT INTO `purpose` VALUES('error-user-email-exists', 'Texto error-user-email-exists', NULL, 'general');
INSERT INTO `purpose` VALUES('error-user-email-invalid', 'Texto error-user-email-invalid', NULL, 'general');
INSERT INTO `purpose` VALUES('error-user-email-token-invalid', 'Texto error-user-email-token-invalid', NULL, 'general');
INSERT INTO `purpose` VALUES('error-user-password-confirm', 'Texto error-user-password-confirm', NULL, 'general');
INSERT INTO `purpose` VALUES('error-user-password-empty', 'Texto error-user-password-empty', NULL, 'general');
INSERT INTO `purpose` VALUES('error-user-password-invalid', 'Texto error-user-password-invalid', NULL, 'general');
INSERT INTO `purpose` VALUES('error-user-wrong-password', 'Texto error-user-wrong-password', NULL, 'general');
INSERT INTO `purpose` VALUES('explain-project-progress', 'Texto bajo el título Estado global de la información', NULL, 'general');
INSERT INTO `purpose` VALUES('faq-ask-question', '¿No has podido resolver tu duda?\r\n Envía un mensaje con tu pregunta.', NULL, 'faq');
INSERT INTO `purpose` VALUES('footer-header-categories', 'Categorías', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-header-projects', 'Proyectos', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-header-resources', 'Recursos', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-header-services', 'Servicios', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-header-social', 'Síganos', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-header-sponsors', 'Apoyos institucionales', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-platoniq-iniciative', 'Una iniciativa de:', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-service-campaign', 'Campañas', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-service-consulting', 'Consultoría', NULL, 'footer');
INSERT INTO `purpose` VALUES('footer-service-workshop', 'Talleres', NULL, 'footer');
INSERT INTO `purpose` VALUES('form-accept-button', 'Aceptar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-add-button', 'Añadir', NULL, 'form');
INSERT INTO `purpose` VALUES('form-apply-button', 'Aplicar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-edit-button', 'Editar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-footer-errors_title', 'Errores', NULL, 'form');
INSERT INTO `purpose` VALUES('form-navigation_bar-header', 'Ir a', NULL, 'form');
INSERT INTO `purpose` VALUES('form-next-button', 'Siguiente', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project-info_status-title', 'Estado global de la información', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project-progress-title', 'Estado del progreso', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project-status-title', 'Estado del proyecto', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-campaing', 'En campaña', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-cancel', 'Desechado', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-cancelled', 'Cancelado', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-edit', 'Editándose', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-expired', 'Caducado', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-fulfilled', 'Retorno cumplido', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-review', 'Pendiente valoración', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-success', 'Financiado', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_waitfor-campaing', 'Difunde tu proyecto y mucha suerte con los aportes.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-cancel', 'Lo hemos desechado, puedes intentar otra idea o concepto', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-edit', 'Cuando lo tengas listo mandalo a revisión. Necesitas llegar a un mínimo de información en el formulario.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-expired', 'No lo conseguiste, mejóralo e intentalo de nuevo!', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-fulfilled', 'Has cumplido con los retornos! Gracias por tu participación.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-review', 'Espera que te digamos algo. Lo publicaremos o te diremos cómo mejorarlo.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-success', 'Has conseguido el mínimo o mas en aportes. Ahora hablamos de dinero.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-remove-button', 'Quitar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-self_review-button', 'Corregir', NULL, 'form');
INSERT INTO `purpose` VALUES('form-send_review-button', 'Enviar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-upload-button', 'Subir', NULL, 'form');
INSERT INTO `purpose` VALUES('guide-blog-posting', 'Texto guide-blog-posting', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-dashboard-user-access', 'Texto guide-dashboard-user-access', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('guide-dashboard-user-personal', 'Texto guide-dashboard-user-personal', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('guide-dashboard-user-profile', 'Texto guide-dashboard-user-profile', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('guide-project-comment', 'Texto guide-project-comment', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-contract-information', 'Texto guide-project-contract-information', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-costs', 'Texto guía en el paso COSTES del formulario de proyecto', NULL, 'costs');
INSERT INTO `purpose` VALUES('guide-project-description', 'Texto guide-project-description', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-error-mandatories', 'Faltan campos obligatorios', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-overview', 'Texto guía en el paso DESCRIPCIÓN del formulario de proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('guide-project-preview', 'Texto guía en el paso PREVISUALIZACIÓN del formulario de proyecto', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-rewards', 'Texto guía en el paso RETORNO del formulario de proyecto', NULL, 'rewards');
INSERT INTO `purpose` VALUES('guide-project-success-minprogress', 'Ha llegado al porcentaje mínimo', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-success-noerrors', 'Todos los campos obligatorios estan rellenados', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-success-okfinish', 'Puede enviar para valoración', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-support', 'Texto guide-project-support', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-supports', 'Texto guía en el paso COLABORACIONES del formulario de proyecto', NULL, 'supports');
INSERT INTO `purpose` VALUES('guide-project-updates', 'Texto guide-project-updates', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-user-information', 'Texto guía en el paso PERFIL del formulario de proyecto', NULL, 'profile');
INSERT INTO `purpose` VALUES('guide-user-data', 'Texto guía en la edición de datos sensibles del usuario', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('guide-user-information', 'Texto guía en la edición de información del usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('guide-user-register', 'Texto guía en el registro de usuario', NULL, 'register');
INSERT INTO `purpose` VALUES('home-posts-header', 'Como funciona goteo', NULL, 'home');
INSERT INTO `purpose` VALUES('home-promotes-header', 'Proyectos destacados', NULL, 'home');
INSERT INTO `purpose` VALUES('invest-abitmore', 'Por %s(cantidad) más serías %s(nivel)', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-address-field', 'Dirección:', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-country-field', 'País:', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-header', 'Dónde quieres recibir la recompensa', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-location-field', 'Ciudad:', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-zipcode-field', 'Código postal:', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-amount', 'Cantidad', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-amount-tooltip', 'Introduce la cantidad con la que apoyarás al proyecto', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-anonymous', 'Quiero que mi aporte sea anónimo', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-individual-header', 'Elige tu recompensa entre estas opciones', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-next_step', 'Paso siguiente', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-payment-email', 'Introduce tu email o cuenta de Paypal', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-payment_method-header', 'Elige el método de pago', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-resign', 'Renuncio a una recompensa individual, solo quiero ayudar al proyecto', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-social-header', 'Con los retornos colectivos ganamos todos', NULL, 'invest');
INSERT INTO `purpose` VALUES('login-access-button', 'Entrar', NULL, 'login');
INSERT INTO `purpose` VALUES('login-access-header', 'Usuario registrado', NULL, 'login');
INSERT INTO `purpose` VALUES('login-access-password-field', 'Contraseña', NULL, 'login');
INSERT INTO `purpose` VALUES('login-access-username-field', 'Nombre de usuario', NULL, 'login');
INSERT INTO `purpose` VALUES('login-banner-header', 'Accede a la comunidad goteo\r\n100% Abierto', 1, 'banners');
INSERT INTO `purpose` VALUES('login-fail', 'Login failed', NULL, 'login');
INSERT INTO `purpose` VALUES('login-oneclick-header', 'Accede con un solo click', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-button', 'Recuperar', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-email-field', 'Email de la cuenta', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-header', 'Recuperar contraseña', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-link', 'Recuperar contraseña', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-username-field', 'Nombre de usuario', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-button', 'Registrar', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-confirm-field', 'Confirmar email', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-confirm_password-field', 'Confirmar contraseña', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-email-field', 'Email', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-header', 'Nuevo usuario', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-password-field', 'Contraseña', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-username-field', 'Nombre de usuario', NULL, 'login');
INSERT INTO `purpose` VALUES('mandatory-cost-field-amount', 'Texto obligatorio cantidad', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-cost-field-description', 'Es obligatorio poner una descripción al coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-cost-field-name', 'Es obligatorio ponerle un nombre al coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-cost-field-task_dates', 'Es obligatorio especificar las fechas de tarea', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-cost-field-type', 'Texto mandatory-cost-field-type', NULL, 'general');
INSERT INTO `purpose` VALUES('mandatory-individual_reward-field-amount', 'Es obligatorio indicar el importe que otorga la recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-individual_reward-field-description', 'Es obligatorio poner alguna descripción', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-individual_reward-field-icon', 'Es obligatorio seleccionar el tipo de recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-individual_reward-field-name', 'Es obligatorio poner el retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-project-costs', 'Mínimo de costes a desglosar en un proyecto', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-project-field-about', 'Es obligatorio explicar qué es en la descripción del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-address', 'La dirección del responsable del proyecto es obligatoria', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-category', 'La categoría del proyecto es obligatoria', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-contract-email', 'El email del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-contract-name', 'El nombre del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-contract-nif', 'El nif del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-contract-surname', 'El apellido del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-country', 'El país del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-description', 'La descripción del proyecto es obligatorio', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-goal', 'Es obligatorio explicar los objetivos en la descripción del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-image', 'Es obligatorio poner una imagen al proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-location', 'La localización del proyecto es obligatoria', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-media', 'Poner un vídeo para mejorar la puntuación', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-motivation', 'Es obligatorio explicar la motivación en la descripción del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-name', 'El nombre del proyecto es obligatorio', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-phone', 'El teléfono del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-related', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripción del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-residence', 'El lugar de residencia del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-resource', 'Es obligatorio especificar si cuentas con otros recursos', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-project-field-zipcode', 'El código postal del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-resource', 'Es obligatorio especificar si cuentas con otros recursos', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-project-total-costs', 'Es obligatorio especificar algún coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-register-field-email', 'Texto mandatory-register-field-email', NULL, 'general');
INSERT INTO `purpose` VALUES('mandatory-social_reward-field-description', 'Es obligatorio poner alguna descripción al retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-social_reward-field-icon', 'Es obligatorio seleccionar el tipo de retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-social_reward-field-name', 'Es obligatorio poner el retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-support-field-description', 'Es obligatorio poner alguna descripción', NULL, 'supports');
INSERT INTO `purpose` VALUES('mandatory-support-field-name', 'Es obligatorio ponerle un nombre a la colaboración', NULL, 'supports');
INSERT INTO `purpose` VALUES('open-banner-header', 'OPEN', 1, 'banners');
INSERT INTO `purpose` VALUES('overview-field-about', 'Qué es', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-categories', 'Categorías', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-currently', 'Estado actual', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-description', 'Resumen breve', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-goal', 'Objetivos', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-image_gallery', 'Imágenes actuales', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-image_upload', 'Subir una imagen', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-keywords', 'Palabras clave', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-media', 'Vídeo', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-media_preview', 'Vista previa', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-motivation', 'Motivación', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-name', 'Nombre del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-currently_avanzado', 'Avanzado', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-currently_finalizado', 'Finalizado', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-currently_inicial', 'Inicial', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-currently_medio', 'Medio', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-scope_global', 'Global', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-scope_local', 'Local', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-scope_nacional', 'Nacional', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-scope_regional', 'Regional', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-project_location', 'Ubicación', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-related', 'Experiencia relacionada y equipo', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-scope', 'Ambito de alcance', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-fields-images-title', 'Imágenes del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-main-header', 'Proyecto/Descripción', NULL, 'overview');
INSERT INTO `purpose` VALUES('personal-field-address', 'Dirección', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-contract_name', 'Nombre y apellidos', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-contract_nif', 'NIF', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-country', 'País', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-location', 'Localidad', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-phone', 'Teléfono', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-zipcode', 'Código postal', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-main-header', 'Usuario/Datos personales', NULL, 'personal');
INSERT INTO `purpose` VALUES('preview-main-header', 'Proyecto/Previsualización', NULL, 'preview');
INSERT INTO `purpose` VALUES('preview-send-comment', 'Notas adicionales para el administrador', NULL, 'preview');
INSERT INTO `purpose` VALUES('profile-about-header', 'Sobre mi', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-field-about', 'Cuéntanos algo sobre ti', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-avatar_current', 'Tu imagen actual', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-avatar_upload', 'Subir una imagen', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-contribution', 'Qué podrías aportar a Goteo', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-interests', 'Tus intereses', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-keywords', 'Palabras clave', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-location', 'Dónde estas', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-name', 'Alias', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-url', 'Url', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-websites', 'Mis webs', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-fields-image-title', 'Tu imagen', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-fields-social-title', 'Perfiles sociales', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-interests-header', 'Mis intereses', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-invest_on-header', 'Proyectos que apoyo', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-invest_on-title', 'Cofinancia', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-keywords-header', 'Mis palabras clave', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-last_worth-title', 'Fecha', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-location-header', 'Mi ubicación', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-main-header', 'Usuario/Perfil', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-my_investors-header', 'Mis cofinanciadores', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-my_projects-header', 'Mis proyectos', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-my_worth-header', 'Mi posición en goteo', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-name-header', 'Perfil de ', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-sharing_interests-header', 'Compartiendo intereses', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-social-header', 'Social', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-webs-header', 'Mis webs', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-widget-button', 'Ver perfil', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-widget-user-header', 'Usuario', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-worth-title', 'Me aporta:', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-worthcracy-title', 'Posición', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('project-collaborations-supertitle', 'Necesidades no económicas:', NULL, 'project');
INSERT INTO `purpose` VALUES('project-collaborations-title', 'Se busca', NULL, 'project');
INSERT INTO `purpose` VALUES('project-form-header', 'Formulario', NULL, 'form');
INSERT INTO `purpose` VALUES('project-invest-continue', 'Vas a pre-autorizar el pago', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-fail', 'Algo ha fallado, por favor inténtalo de nuevo', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-guest', 'Invitado (no olvides registrarte)', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-ok', 'Ya eres cofinanciador de este proyecto', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-start', 'Estás a un paso de ser cofinanciador de este proyecto', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-total', 'Total de aportaciones', NULL, 'general');
INSERT INTO `purpose` VALUES('project-menu-home', 'Proyecto', NULL, 'project');
INSERT INTO `purpose` VALUES('project-menu-messages', 'Mensajes', NULL, 'project');
INSERT INTO `purpose` VALUES('project-menu-needs', 'Necesidades', NULL, 'project');
INSERT INTO `purpose` VALUES('project-menu-supporters', 'Cofinanciadores', NULL, 'project');
INSERT INTO `purpose` VALUES('project-menu-updates', 'Actualizaciones', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-answer_it', 'Responder', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-send_direct-header', 'Envía un mensaje al autor', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-send_message-button', 'Enviar', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-send_message-header', 'Escribe tu mensaje', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-send_message-your_answer', 'Escribe tu respuesta aquí', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-header', 'Retorno', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-individual_reward-limited', 'Recompensa limitada', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-individual_reward-title', 'Recompensas individuales', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-individual_reward-units_left', 'Quedan %s(variable) unidades', 1, 'project');
INSERT INTO `purpose` VALUES('project-rewards-social_reward-title', 'Retorno colectivo', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-supertitle', 'Que ofrezco a cambio?', NULL, 'project');
INSERT INTO `purpose` VALUES('project-share-header', 'Comparte este proyecto', NULL, 'project');
INSERT INTO `purpose` VALUES('project-side-investors-header', 'Ya han aportado', NULL, 'project');
INSERT INTO `purpose` VALUES('project-spread-header', 'Difunde este proyecto', NULL, 'project');
INSERT INTO `purpose` VALUES('project-spread-widget', 'Widget del proyecto', NULL, 'project');
INSERT INTO `purpose` VALUES('project-support-supertitle', 'Necesidades económicas:', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-categories-title', 'Categorias', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-days', 'Quedan', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-got', 'Obtenido', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-investment', 'Financiacion', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-investors', 'Cofinanciadores', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-minimum', 'Mínimo', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-optimum', 'Óptimo', NULL, 'project');
INSERT INTO `purpose` VALUES('regular-admin_board', 'Panel admin', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-ask', 'Preguntar', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-collaborate', 'Colabora', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-community', 'Comunidad', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-create', 'Crea un proyecto', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-days', 'días', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-delete', 'Borrar', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-discover', 'Descubre proyectos', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-edit', 'Editar', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-facebook', 'Facebook', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-faq', 'Preguntas frecuentes', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-first', 'Primera', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-footer-contact', 'Contacto', NULL, 'footer');
INSERT INTO `purpose` VALUES('regular-footer-legal', 'Términos legales', NULL, 'footer');
INSERT INTO `purpose` VALUES('regular-google', 'Google+', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-gotit_mark', 'Financiado!', NULL, 'widget');
INSERT INTO `purpose` VALUES('regular-go_up', 'Subir', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-header-about', 'Sobre Goteo', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-header-blog', 'Blog', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-header-contact', 'Texto regular-header-contact', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-header-faq', 'FAQ', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-header-legal', 'Texto regular-header-legal', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-hello', 'Hola', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-home', 'Inicio', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-identica', 'Identica', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-im', 'Soy', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-invest', 'Aportar', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-investing', 'Aportando', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-invest_it', 'Apóyalo', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-keepiton', 'Aun puedes seguir aportando', NULL, 'widget');
INSERT INTO `purpose` VALUES('regular-last', '', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-license', 'Licencia', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-linkedin', 'LinkedIn', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-login', 'Accede', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-logout', 'Cerrar sesión', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-looks_for', 'busca:', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-main-header', 'Goteo.org', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-mandatory', 'Texto genérico para indicar campo obligatorio', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-menu', 'Menu', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-more_info', '+ info', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-news', 'Noticias', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-new_project', 'Proyecto nuevo', NULL, 'project');
INSERT INTO `purpose` VALUES('regular-onrun_mark', 'En marcha!', NULL, 'widget');
INSERT INTO `purpose` VALUES('regular-projects', 'proyectos', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-read_more', 'Leer más', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-review_board', 'Panel revisor', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-search', 'Buscar', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-see_all', 'Ver todos', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-see_blog', 'Ver blog', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-see_details', 'Ver detalles', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-see_more', 'Ver más', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-share-facebook', 'Goteo en Facebook', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-share-rss', 'RSS/BLOG', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-share-twitter', 'Síguenos en Twitter', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-share_this', 'Compartir en:', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-sorry', 'Lo sentimos', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-success_mark', 'Exitoso!', NULL, 'widget');
INSERT INTO `purpose` VALUES('regular-thanks', 'Gracias', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-total', 'Total', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-translate_board', 'Panel traductor', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-twitter', 'Twitter', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-view_project', 'Ver proyecto', NULL, 'general');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-amount', 'Cantidad mínima', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-description', 'Description', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-other', 'Especificar el tipo de recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-reward', 'Recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-type', 'Tipo', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-units', 'Unidades', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-description', 'Descripción', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-license', 'Licencia', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-other', 'Especificar el tipo de retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-reward', 'Retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-type', 'Tipo', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-fields-individual_reward-title', 'Recompensas individuales', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-fields-social_reward-title', 'Retornos colectivos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-main-header', 'Proyecto/Retornos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('social-account-facebook', 'Pagina Goteo Facebook', NULL, 'social');
INSERT INTO `purpose` VALUES('social-account-google', 'Pagina Goteo Google', NULL, 'social');
INSERT INTO `purpose` VALUES('social-account-identica', 'Pagina Goteo Identica', NULL, 'social');
INSERT INTO `purpose` VALUES('social-account-linkedin', 'Pagina Goteo Linkedin', NULL, 'social');
INSERT INTO `purpose` VALUES('social-account-twitter', 'Pagina Goteo Twitter', NULL, 'social');
INSERT INTO `purpose` VALUES('step-1', 'Perfil', NULL, 'profile');
INSERT INTO `purpose` VALUES('step-2', 'Datos personales', NULL, 'personal');
INSERT INTO `purpose` VALUES('step-3', 'Descripción', NULL, 'overview');
INSERT INTO `purpose` VALUES('step-4', 'Costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('step-5', 'Retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('step-6', 'Colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('step-7', 'Previsualización', NULL, 'preview');
INSERT INTO `purpose` VALUES('step-costs', 'Paso 4, desglose de costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('step-overview', 'Paso 3, descripción del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('step-preview', 'paso 7, previsualización', NULL, 'preview');
INSERT INTO `purpose` VALUES('step-rewards', 'Paso 5, retornos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('step-supports', 'Paso 6, colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('step-userPersonal', 'Paso 2, información del responsable', NULL, 'personal');
INSERT INTO `purpose` VALUES('step-userProfile', 'Paso 1, información del usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('subject-change-email', 'Asunto del mail al cambiar el email', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('subject-register', 'Asunto del email al registrarse', NULL, 'register');
INSERT INTO `purpose` VALUES('supports-field-description', 'Descripción', NULL, 'supports');
INSERT INTO `purpose` VALUES('supports-field-support', 'Resumen', NULL, 'supports');
INSERT INTO `purpose` VALUES('supports-field-type', 'Tipo', NULL, 'supports');
INSERT INTO `purpose` VALUES('supports-fields-support-title', 'Colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('supports-main-header', 'Proyecto/Colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-dashboard-user-access_data', 'Texto tooltip-dashboard-user-access_data', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('tooltip-dashboard-user-change_email', 'Texto tooltip-dashboard-user-change_email', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('tooltip-dashboard-user-change_password', 'Texto tooltip-dashboard-user-change_password', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('tooltip-dashboard-user-confirm_email', 'Texto tooltip-dashboard-user-confirm_email', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('tooltip-dashboard-user-confirm_password', 'Texto tooltip-dashboard-user-confirm_password', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('tooltip-dashboard-user-new_email', 'Texto tooltip-dashboard-user-new_email', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('tooltip-dashboard-user-new_password', 'Texto tooltip-dashboard-user-new_password', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('tooltip-dashboard-user-user_password', 'Texto tooltip-dashboard-user-user_password', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('tooltip-project-about', 'Consejo para rellenar el campo qué es', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-address', 'Consejo para rellenar el address del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-category', 'Consejo para seleccionar la categoría del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-comment', 'Tooltip campo comentario', NULL, 'preview');
INSERT INTO `purpose` VALUES('tooltip-project-contract_email', 'Consejo para rellenar el email del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-contract_name', 'Consejo para rellenar el nombre del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-contract_nif', 'Consejo para rellenar el nif del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-contract_surname', 'Consejo para rellenar el apellido del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-cost', 'Consejo para editar desgloses existentes', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-cost-amount', 'Texto tooltip cantidad coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-cost-cost', 'Texto tooltip nombre coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-cost-dates', 'Texto tooltip fechas costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-cost-description', 'Texto tooltip descripcion costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-cost-from', 'Texto tooltip fecha desde costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-cost-required', 'Texto tooltip algun coste requerido', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-cost-type', 'Texto tooltip tipo de coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-cost-type-Infraestructura', 'Texto tooltip-project-cost-type-Infraestructura', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-project-cost-type-material', 'Tooltip coste tipo material', NULL, 'types');
INSERT INTO `purpose` VALUES('tooltip-project-cost-type-structure', 'Tooltip coste tipo infraestructura', NULL, 'types');
INSERT INTO `purpose` VALUES('tooltip-project-cost-type-Tarea', 'Texto tooltip-project-cost-type-Tarea', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-project-cost-type-task', 'Tooltip coste tipo tarea', NULL, 'types');
INSERT INTO `purpose` VALUES('tooltip-project-cost-until', 'Texto tooltip fecha coste hasta', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-costs', 'Texto tooltip desglose de costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-country', 'Consejo para rellenar el país del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-currently', 'Consejo para rellenar el estado de desarrollo del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-description', 'Consejo para rellenar la descripción del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-goal', 'Consejo para rellenar el campo objetivos', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-image', 'Consejo para rellenar la imagen del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-image_upload', 'Texto tooltip subir imagen proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward', 'Consejo para editar retornos individuales existentes', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-amount', 'Texto tooltip cantidad para recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-description', 'Texto tooltip descripcion recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-icon-other', 'Tooltip especificar el tipo Otro de recompensa individual', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-reward', 'Texto tooltip nombre recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-type', 'Texto tooltip tipo de recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-units', 'Texto tooltip unidades de recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_rewards', 'Texto tooltip recompensas individuales', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-keywords', 'Consejo para rellenar las palabras clave del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-location', 'Consejo para rellenar el lugar de residencia del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-media', 'Consejo para rellenar el media del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-motivation', 'Consejo para rellenar el campo motivación', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-name', 'Consejo para rellenar el nombre del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-ncost', 'Consejo para rellenar un nuevo desglose de costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-nindividual_reward', 'Consejo para rellenar un nuevo retorno individual', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-nsocial_reward', 'Consejo para rellenar un nuevo retorno colectivo', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-nsupport', 'Consejo para rellenar una nueva colaboración', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-phone', 'Consejo para rellenar el teléfono del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-project_location', 'Consejo para rellenar la localización del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-related', 'Consejo para rellenar el campo experiencia relacionada y equipo', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-resource', 'Consejo para rellenar el campo Cuenta con otros recursos?', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-schedule', 'Texto tooltip agenda del proyeecto', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-scope', 'Texto tooltip-project-scope', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward', 'Consejo para editar retornos colectivos existentes', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-description', 'Texto tooltip descripcion retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-icon-other', 'Tooltip especificar el tipo Otro de retorno colectivo', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-license', 'Texto tooltip licencia retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-reward', 'Texto tooltip nombre retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-type', 'Texto tooltip tipo retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_rewards', 'Texto tooltip retornos colectivos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-support', 'Consejo para editar colaboraciones existentes', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-support-description', 'Texto tooltip descripcion colaboracion', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-support-support', 'Texto tooltip nombre colaboracion', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-support-type', 'Texto tooltip tipo colaboracion', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-support-type-lend', 'Tooltip colaboracion tipo prestamo', NULL, 'types');
INSERT INTO `purpose` VALUES('tooltip-project-support-type-PrÃ©stamo', 'Texto tooltip-project-support-type-PrÃ©stamo', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-project-support-type-Tarea', 'Texto tooltip-project-support-type-Tarea', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-project-support-type-task', 'Tooltip colaboracion tipo tarea', NULL, 'types');
INSERT INTO `purpose` VALUES('tooltip-project-supports', 'Texto tooltip colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-totals', 'Texto tooltip costes totales', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-zipcode', 'Consejo para rellenar el zipcode del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-updates-allow_comments', 'Texto tooltip-updates-allow_comments', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-updates-date', 'Texto tooltip-updates-date', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-updates-footer', 'Texto tooltip-updates-footer', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-home', 'Texto tooltip-updates-home', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-updates-image', 'Texto tooltip-updates-image', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-updates-image_upload', 'Texto tooltip-updates-image_upload', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-updates-media', 'Texto tooltip-updates-media', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-updates-publish', 'Texto tooltip-updates-publish', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-tags', 'Texto tooltip-updates-tags', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-updates-text', 'Texto tooltip-updates-text', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-updates-title', 'Texto tooltip-updates-title', NULL, 'project');
INSERT INTO `purpose` VALUES('tooltip-user-about', 'Consejo para rellenar el cuéntanos algo sobre ti', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-avatar_upload', 'Texto tooltip subir imagen usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-blog', 'Consejo para rellenar la web', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-contribution', 'Consejo para rellenar el qué podrías aportar en goteo', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-email', 'Consejo para rellenar el email de registro de usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-facebook', 'Consejo para rellenar el facebook', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-google', 'Tooltip para rellenar el Google+', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-identica', 'Texto tooltip-user-identica', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-image', 'Consejo para rellenar la imagen del usuario', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-user-interests', 'Consejo para seleccionar tus intereses', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-keywords', 'Consejo para rellenar tus palabras clave', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-linkedin', 'Consejo para rellenar el linkedin', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-location', 'Texto tooltip lugar de residencia del usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-name', 'Consejo para rellenar el nombre completo del usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-twitter', 'Consejo para rellenar el twitter', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-user', 'Consejo para rellenar el nombre de usuario para login', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-webs', 'Texto tooltip webs del usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('translate-home-guide', 'Mensaje portada panel traductor', NULL, 'translate');
INSERT INTO `purpose` VALUES('user-account-inactive', 'La cuenta esta desactivada', NULL, 'general');
INSERT INTO `purpose` VALUES('user-activate-already-active', 'La cuenta de usuario ya esta activada', NULL, 'register');
INSERT INTO `purpose` VALUES('user-activate-fail', 'Texto user-activate-fail', NULL, 'general');
INSERT INTO `purpose` VALUES('user-activate-success', 'La cuenta de usuario se ha activado correctamente', NULL, 'register');
INSERT INTO `purpose` VALUES('user-changeemail-fail', 'Texto user-changeemail-fail', NULL, 'general');
INSERT INTO `purpose` VALUES('user-changeemail-success', 'El email se ha cambiado con exito', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('user-register-success', 'El usuario se ha registrado correctamente', NULL, 'register');
INSERT INTO `purpose` VALUES('validate-cost-field-dates', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuación', NULL, 'costs');
INSERT INTO `purpose` VALUES('validate-project-costs-any_error', 'Hay algún error en el desglose de costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('validate-project-field-about', 'La explicacion del proyecto es demasiado corta', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-field-costs', 'Desglosar hasta 5 costes para mejorar la puntuación', NULL, 'costs');
INSERT INTO `purpose` VALUES('validate-project-field-currently', 'Indicar el estado del proyecto para mejorar la puntuación', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-field-description', 'La descripcion del proyecto es demasiado corta', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-individual_rewards', 'Hay que poner alguna recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('validate-project-individual_rewards-any_error', 'Hay algún error en las recompensas individuales', NULL, 'rewards');
INSERT INTO `purpose` VALUES('validate-project-social_rewards', 'Hay que poner algún retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('validate-project-social_rewards-any_error', 'Hay algún error en los retornos colectivos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('validate-project-total-costs', 'El coste óptimo no puede exceder demasiado al coste mínimo', NULL, 'costs');
INSERT INTO `purpose` VALUES('validate-project-userProfile-any_error', 'Hay algún error en las webs', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-project-userProfile-web', 'Mejor poner alguna web', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-project-value-contract-email', 'El email no es correcto', NULL, 'register');
INSERT INTO `purpose` VALUES('validate-project-value-contract-nif', 'El nif del responsable del proyecto debe ser correcto', NULL, 'personal');
INSERT INTO `purpose` VALUES('validate-project-value-description', 'La descripción del proyecto debe se suficientemente extensa', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-value-keywords', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuación', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-value-phone', 'El teléfono debe ser correcto', NULL, 'personal');
INSERT INTO `purpose` VALUES('validate-register-value-email', 'El email introducido no es valido', NULL, 'register');
INSERT INTO `purpose` VALUES('validate-social_reward-license', 'Indicar una licencia para mejorar la puntuación', NULL, 'rewards');
INSERT INTO `purpose` VALUES('validate-user-field-about', 'Si no ha puesto nada sobre el/ella ', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-avatar', 'Si no ha puesto una imagen de perfil', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-contribution', 'Si no ha puesto qué puede aportar a Goteo', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-facebook', 'Si no ha puesto su cuenta de facebook', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-interests', 'Si no ha seleccionado ningún interés', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-keywords', 'Si no ha puesto ninguna palabra clave', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-linkedin', 'El campo linkedin no es valido', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-location', 'El lugar de residencia del usuario no es valido', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-name', 'Si no ha puesto el nombre completo', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-twitter', 'El twitter del usuario no es valido', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-web', 'Debes poner la url de la web', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-webs', 'Si no ha puesto ninguna web', NULL, 'profile');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `to_checker` text,
  `to_owner` text,
  `score` int(2) NOT NULL DEFAULT '0',
  `max` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Revision para evaluacion de proyecto' AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `review`
--

INSERT INTO `review` VALUES(3, 'pliegos', 1, 'x', 'x', 0, 23);
INSERT INTO `review` VALUES(4, 'nodo-movil', 1, 'aqui iria el comentario del admin para el revisor. Esto es un text para ver si sfonciona. Asigno esta revisión a Enric, que la deberia ver en su dashboar en Panel de revisor', 'aqui iria el comentario del admin para el procuctor', 0, 23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `review_comment`
--

DROP TABLE IF EXISTS `review_comment`;
CREATE TABLE `review_comment` (
  `review` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `evaluation` text,
  `recommendation` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review`,`user`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Comentarios de revision';

--
-- Volcar la base de datos para la tabla `review_comment`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `review_score`
--

DROP TABLE IF EXISTS `review_score`;
CREATE TABLE `review_score` (
  `review` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `criteria` bigint(20) unsigned NOT NULL,
  `score` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`review`,`user`,`criteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Puntuacion por citerio';

--
-- Volcar la base de datos para la tabla `review_score`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reward`
--

DROP TABLE IF EXISTS `reward`;
CREATE TABLE `reward` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `reward` varchar(256) DEFAULT NULL,
  `description` tinytext,
  `type` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `other` tinytext COMMENT 'Otro tipo de recompensa',
  `license` varchar(50) DEFAULT NULL,
  `amount` int(5) DEFAULT NULL,
  `units` int(5) DEFAULT NULL,
  `fulsocial` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Retorno colectivo cumplido',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Retornos colectivos e individuales' AUTO_INCREMENT=232 ;

--
-- Volcar la base de datos para la tabla `reward`
--

INSERT INTO `reward` VALUES(57, 'a565092b772c29abc1b92f999af2f2fb', 'Acceso y utilización libre de la aplicación web', 'La aplicación será de libre uso. El usuario únicamente tendrá que registrarse para organizar campañas (numero limitado a 3). Para cada campaña se obtendrá automáticamente un espacio único en el servidor para la visualización ', 'social', 'file', '', 'ccbyncsa', 0, 0, 0);
INSERT INTO `reward` VALUES(59, 'a565092b772c29abc1b92f999af2f2fb', 'Asesoría para futuros administradores de la aplicación', 'Te asesoraremos en el uso de la aplicación respecto a la instrucciones, la parte de twitter, etc.', 'individual', 'money', '', '', 40, 75, 0);
INSERT INTO `reward` VALUES(64, 'fe99373e968b0005e5c2406bc41a3528', '01 retorno colectivo', 'Phasellus varius sodales accumsan.', 'social', 'manual', NULL, 'ccbyncsa', NULL, NULL, 0);
INSERT INTO `reward` VALUES(65, 'fe99373e968b0005e5c2406bc41a3528', '02 retorno colectivo', 'Nulla pellentesque mi at dolor accumsan accumsan. Praesent arcu nibh, sollicitudin eu fringilla fringilla, rhoncus et mi. Sed sed pretium ipsum. Vestibulum non velit nibh, non dignissim eros. Phasellus varius sodales accumsan.', 'social', 'code', NULL, 'gpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(66, 'fe99373e968b0005e5c2406bc41a3528', '01 recompensa individual', 'Suspendisse condimentum metus vel arcu faucibus id volutpat arcu ultricies. Nulla pellentesque mi at dolor accumsan accumsan. Praesent arcu nibh, sollicitudin eu fringilla fringilla, rhoncus et mi. Sed sed pretium ipsum. Vestibulum non velit nibh, non dig', 'individual', 'product', '', '', 20, 30, 0);
INSERT INTO `reward` VALUES(73, '2c667d6a62707f369bad654174116a1e', 'codigo GPL', 'código', 'social', 'file', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(82, '2c667d6a62707f369bad654174116a1e', 'Planos placa arduino', '50 placas al mejor postor', 'social', 'design', NULL, 'oshw', NULL, NULL, 0);
INSERT INTO `reward` VALUES(90, 'a565092b772c29abc1b92f999af2f2fb', 'El código de Twittometro', 'Estará disponible el código de la aplicación para poder usarlo y mejorarlo, siempre bajo el mismo tipo de licencia libre.', 'social', 'code', NULL, 'agpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(92, 'pliegos', 'Licencia CC', 'Ok lo que digo :)', 'social', 'design', NULL, 'gpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(95, 'pliegos', 'Premios!', 'Ole ole', 'individual', 'other', NULL, NULL, 10, 2, 0);
INSERT INTO `reward` VALUES(109, '2c667d6a62707f369bad654174116a1e', 'Patrones para hacer una camiseta', 'Patrones para hacer una camiseta', 'social', 'design', NULL, 'ccby', NULL, NULL, 0);
INSERT INTO `reward` VALUES(118, 'pliegos', 'Nueva recompensa individual', '', 'individual', 'money', NULL, NULL, 10, 0, 0);
INSERT INTO `reward` VALUES(122, 'todojunto-letterpress', 'Devuelvo en horas de formación o ayuda a otros proyectos', 'Workshops en el taller letterpress.  12 horas', 'social', 'other', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(125, 'todojunto-letterpress', 'Devuelvo en manuales (HOW TO)', 'Manual/fanzine explicando los fundamentos de la impresión con tipografías móviles.', 'social', 'manual', NULL, 'ccby', 0, 0, 0);
INSERT INTO `reward` VALUES(127, 'oh-oh-fase-2', 'Devuelvo en manuales (HOW TO)', 'Manual para replicar el robot, fabricarlo y comercializarlo, manuales para dar cursos con el', 'social', 'manual', NULL, 'ccby', 0, 0, 0);
INSERT INTO `reward` VALUES(131, 'oh-oh-fase-2', 'Devuelvo el dinero', 'Hacemos donaciones a otros proyectos de software y hardware libre', 'social', 'other', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(132, 'urban-social-design-database', 'Devuelvo en archivos digitales', 'Los proyectos almacenados serán de libre uso para todos.', 'social', 'file', NULL, 'ccbysa', 0, 0, 0);
INSERT INTO `reward` VALUES(133, 'archinhand-architecture-in-your-hand', 'Devuelvo el dinero', '10 % a otros proyectos de Goteo', 'social', 'other', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(134, 'archinhand-architecture-in-your-hand', 'Devuelvo el código fuente', 'Código fuente', 'social', 'code', NULL, 'ccbyncsa', 0, 0, 0);
INSERT INTO `reward` VALUES(135, 'archinhand-architecture-in-your-hand', 'Devuelvo en horas de formación o ayuda a otros proyectos', 'Devuelvo en horas de formación o ayuda a otros proyectos.  12 horas', 'social', 'service', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(136, 'mi-barrio', 'Devuelvo el código fuente', 'Manuales express de grabación, videos resultado del proyecto y documentación de los procesos', 'social', 'code', NULL, 'ccbysa', 0, 0, 0);
INSERT INTO `reward` VALUES(137, 'mi-barrio', 'Devuelvo en horas de formación o ayuda a otros proyectos', 'Talleres de formación ciudadana ', 'social', 'service', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(138, 'goteo', 'Prueba TAPR', '', 'social', 'file', NULL, 'tapr', 0, 0, 0);
INSERT INTO `reward` VALUES(139, 'goteo', 'Prueba OH', '', 'social', 'file', NULL, 'oshw', 0, 0, 0);
INSERT INTO `reward` VALUES(140, 'goteo', 'Prueba ODC', '', 'social', 'design', '', 'ccbysa', 0, 0, 0);
INSERT INTO `reward` VALUES(141, 'hkp', 'Devuelvo en productos', 'Libro HKp  1500 unidades', 'social', 'manual', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(142, 'hkp', 'Devuelvo en productos', 'DVD HKp  1500 unidades', 'social', 'file', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(143, 'hkp', 'Contenidos copyleft', 'todos los contenidos son copyleft o libres reutilizables, wiki es plataforma participativa puede usarse en talleres u otros proyectos', 'social', 'other', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(144, 'hkp', 'Libro + DVD', 'En caso de conseguir publicar el libro y el DVD (o uno de ellos) se podría enviar el pack a quienes hayan hecho contribuciones', 'individual', 'product', NULL, '', 15, 1500, 0);
INSERT INTO `reward` VALUES(145, 'move-commons', 'Devuelvo el código fuente   ', 'Material gráfico, código de plataforma+buscador (AGPL) y HOWTOs/categoría ', 'social', 'code', NULL, 'agpl', 0, 0, 0);
INSERT INTO `reward` VALUES(146, 'move-commons', 'Otros', 'Buscador de iniciativas y facilitar construcción de servicios sobre la plataforma', 'social', 'other', NULL, 'ccby', 0, 0, 0);
INSERT INTO `reward` VALUES(147, 'nodo-movil', 'Código fuente', 'Devuelvo el código fuente', 'social', 'code', NULL, 'xoln', 0, 0, 0);
INSERT INTO `reward` VALUES(148, 'nodo-movil', 'Formación', 'Devuelvo en horas de formación o ayuda a otros proyectos. 10 horas', 'social', 'service', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(149, 'canal-alfa', 'Devuelvo el código fuente ', 'La plataforma web y las aplicaciones creadas serán publicadas como GPL.', 'social', 'code', NULL, 'gpl', 0, 0, 0);
INSERT INTO `reward` VALUES(151, 'canal-alfa', 'Devuelvo en archivos digitales   ', 'Todo el contenido publicado por los usuarios formará parte de un archivo de dominio público.', 'social', 'file', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(152, 'robocicla', 'Nuevo retorno colectivo', 'Material Didáctico en Código Abierto , Asesoría y Herramientas pedagógicas en torno a la cultura libre para niñ@s', 'social', 'manual', NULL, 'ccbyncsa', 0, 0, 0);
INSERT INTO `reward` VALUES(153, '2c667d6a62707f369bad654174116a1e', 'CD', 'CD super calidad ﻿Duis pulvinar rhoncus ligula vel iaculis. Mauris porttitor ipsum ac libero interdum molestie. Fusce lacus mi, mattis at ultrices nec, cursus hendrerit lectus. Proin tempor lacus nec nisl vestibulum elementum. ', 'individual', 'product', NULL, '', 10, 0, 0);
INSERT INTO `reward` VALUES(155, '3d72d03458ebd5797cc5fc1c014fc894', 'cd', 'un super cd', 'individual', 'product', NULL, '', 10, 100, 0);
INSERT INTO `reward` VALUES(156, '3d72d03458ebd5797cc5fc1c014fc894', 'una camiseta', '100', 'individual', 'product', NULL, '', 10, 10, 0);
INSERT INTO `reward` VALUES(157, '3d72d03458ebd5797cc5fc1c014fc894', 'si no esta publicad la edición fonciona', '', 'individual', 'product', NULL, '', 10, 0, 0);
INSERT INTO `reward` VALUES(158, '2c667d6a62707f369bad654174116a1e', 'aqui hay un problema veras', 'aqui hay un problema veras', 'individual', 'file', NULL, '', 10, 0, 0);
INSERT INTO `reward` VALUES(160, '28c0caa840fc9c642160b1e2774667ff', '1', '', 'social', '', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(170, '3d72d03458ebd5797cc5fc1c014fc894', 'producto', 'dd', 'individual', 'product', NULL, '', 50, 5, 0);
INSERT INTO `reward` VALUES(171, '3d72d03458ebd5797cc5fc1c014fc894', 'Nuevo retorno colectivo', '', 'social', 'other', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(176, 'goteo', 'Nuevo retorno colectivo', '', 'social', '', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(177, 'goteo', 'Nuevo retorno colectivo', '', 'social', '', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(178, 'goteo', 'Nuevo retorno colectivo', '', 'social', '', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(180, '96ce377a34e515ab748a096093187d53', 'Descarga PDF del periódico ', 'Descarga PDF del periódico para reproducción parcial o total', 'social', 'file', '', 'ccbync', 0, 0, 0);
INSERT INTO `reward` VALUES(183, '96ce377a34e515ab748a096093187d53', '2 ediciones del periódico', 'Enviamos las 2 ediciones impresas', 'individual', 'product', '', '', 20, 200, 0);
INSERT INTO `reward` VALUES(191, '96ce377a34e515ab748a096093187d53', 'test', 'test', 'individual', 'service', '', '', 200, 3, 0);
INSERT INTO `reward` VALUES(192, '8c069c398c3e3114e681ccfafa4a3fe0', 'Nuevo retorno colectivo', '', 'social', 'file', '', 'ccbyncsa', 0, 0, 0);
INSERT INTO `reward` VALUES(198, '8c069c398c3e3114e681ccfafa4a3fe0', 'Nueva recompensa individual', '', 'individual', '', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(213, 'f1dd9c1678c62273e21b67bff6e8b47a', 'Nueva recompensa individual', 'Nueva recompensa individual', 'individual', 'file', '', '', 5, 0, 0);
INSERT INTO `reward` VALUES(214, 'f1dd9c1678c62273e21b67bff6e8b47a', 'Nueva recompensa individual', '', 'individual', 'file', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(215, 'f1dd9c1678c62273e21b67bff6e8b47a', 'Nuevo retorno colectivo', 'huh', 'social', 'code', '', 'lgpl', 0, 0, 0);
INSERT INTO `reward` VALUES(221, '8851739335520c5eeea01cd745d0442d', 'Nuevo retorno colectivo', '', 'social', 'design', '', 'oshw', 0, 0, 0);
INSERT INTO `reward` VALUES(222, '8851739335520c5eeea01cd745d0442d', 'Nueva recompensa individual', '', 'individual', 'other', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(223, 'fe99373e968b0005e5c2406bc41a3528', 'Nueva recompensa individual', 'disco', 'individual', 'file', '', '', 100, 10, 0);
INSERT INTO `reward` VALUES(224, '95d51f5b90955d5370b7e7f8045e2368', 'Nuevo retorno colectivo', '', 'social', 'code', '', 'gpl', 0, 0, 0);
INSERT INTO `reward` VALUES(225, 'tweetometro', 'Acceso y utilización libre de la aplicación web', 'La aplicación será de libre uso. El usuario únicamente tendrá que registrarse para organizar campañas (numero limitado a 3). Para cada campaña se obtendrá automáticamente un espacio único en el servidor para la visualización.', 'social', 'service', '', '', 0, 0, 0);
INSERT INTO `reward` VALUES(226, 'tweetometro', 'El código de Twittometro', 'Estará disponible el código de la aplicación para poder usarlo y mejorarlo, siempre bajo el mismo tipo de licencia libre.', 'social', 'code', '', 'agpl', 0, 0, 0);
INSERT INTO `reward` VALUES(227, 'tweetometro', 'Asesoría para futuros administradores de la aplicación', 'Te asesoraremos presencialmente o por videoconferencia en el uso de la aplicación respecto a las instrucciones, la parte de Twitter, etc. para que puedas gestionar una cuenta instalada.', 'individual', 'service', '', '', 100, 20, 0);
INSERT INTO `reward` VALUES(229, 'tweetometro', 'Instalación premium para una campaña concreta', 'Ofrecemos una edición limitada de la recompensa anterior (asesoría de administración) más la instalación de diseño personalizado de un Tweetometro en base a los objetivos que tengas, listo para usarse. Alojado con URL propia y posibilida', 'individual', 'product', '', '', 1000, 4, 0);
INSERT INTO `reward` VALUES(230, 'tutorial-goteo', 'Un retorno colectivo (o más :)', 'Aquí se debe definir la licencia asignar al proyecto, en función de su formato y/o del grado de abertura del mismo (o de alguna de sus partes). Esta parte es muy importante, ya que Goteo es crowdfunding para proyectos que amplíen el procomún.', 'social', 'file', '', 'ccbyncsa', 0, 0, 0);
INSERT INTO `reward` VALUES(231, 'tutorial-goteo', 'Recompensa individual mínima', 'Aquí se debe especificar alguna recompensa para quien apoye el proyecto, vinculada a una cantidad de dinero concreta. Pueden ser archivos digitales, productos, servicios, incluso dinero. Deben ser productos/servicios atractivos o ingeniosos.', 'individual', 'product', '', '', 25, 40, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `role`
--

INSERT INTO `role` VALUES('admin', 'Administrador de Nodo');
INSERT INTO `role` VALUES('checker', 'Revisor de proyectos');
INSERT INTO `role` VALUES('root', 'Super administrador');
INSERT INTO `role` VALUES('superadmin', 'Super administrador de Goteo');
INSERT INTO `role` VALUES('translator', 'Traductor de contenidos');
INSERT INTO `role` VALUES('user', 'Usuario mediocre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsor`
--

DROP TABLE IF EXISTS `sponsor`;
CREATE TABLE `sponsor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` int(10) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Patrocinadores' AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `sponsor`
--

INSERT INTO `sponsor` VALUES(1, 'CCCB', 'http://www.cccb.org/lab', 102, 2);
INSERT INTO `sponsor` VALUES(2, 'CONCA', 'http://www.conca.cat/', 101, 1);
INSERT INTO `sponsor` VALUES(3, 'ICUB', 'http://barcelonacultura.bcn.cat/', 103, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `support`
--

DROP TABLE IF EXISTS `support`;
CREATE TABLE `support` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `support` tinytext,
  `description` text,
  `type` varchar(50) DEFAULT NULL,
  `thread` bigint(20) unsigned DEFAULT NULL COMMENT 'De la tabla message',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Colaboraciones' AUTO_INCREMENT=107 ;

--
-- Volcar la base de datos para la tabla `support`
--

INSERT INTO `support` VALUES(19, 'a565092b772c29abc1b92f999af2f2fb', 'Beta-testers y difusores', 'Son bienvenidas la ayuda de difusión para que mucha gente conozca la  herramienta y participe en las campañas. También se necesitá en determinados momentos hacer pruebas masivas para ver la resistencia de la aplicación. ', 'lend', 78);
INSERT INTO `support` VALUES(20, 'a565092b772c29abc1b92f999af2f2fb', 'Servidores', 'Aunque contamos con un servidor, dependiendo del numero de vistas y usuarios de la herramienta, necesitaremos patrocinadores para hacer más fácil el mantenimiento del proyecto y que pueda continuar siendo de uso gratuito.', 'lend', 79);
INSERT INTO `support` VALUES(23, 'fe99373e968b0005e5c2406bc41a3528', 'Espacio taller', 'Donec ultrices libero in est tincidunt placerat tempor mi mattis. Vestibulum at aliquam lacus. Suspendisse condimentum metus vel arcu faucibus id volutpat arcu ultricies. Nulla pellentesque mi at dolor accumsan accumsan. ', 'lend', 52);
INSERT INTO `support` VALUES(28, '2c667d6a62707f369bad654174116a1e', 'pincel', 'pinceles', 'lend', 93);
INSERT INTO `support` VALUES(29, '2c667d6a62707f369bad654174116a1e', 'alguin q sepa escribir', 'redactar textos', 'task', 94);
INSERT INTO `support` VALUES(34, '2c667d6a62707f369bad654174116a1e', 'nueva colab editada desde el dahboard proyecto BROOKLYN', 'a ver si se ve', 'task', 95);
INSERT INTO `support` VALUES(38, 'pliegos', 'Pilas', 'Dejadmelas :)', 'lend', 75);
INSERT INTO `support` VALUES(43, '2c667d6a62707f369bad654174116a1e', 'ayuda para poner en marcha este ajax!!!', 'ayuda para poner en marcha este ajax!!!', 'task', 96);
INSERT INTO `support` VALUES(48, '2c667d6a62707f369bad654174116a1e', 'Nueva tarea', 'una tarea guay', 'task', 98);
INSERT INTO `support` VALUES(53, 'todojunto-letterpress', 'Furgoneta', 'Furgoneta o transporte para mover los materiales conseguidos. Para 4 horas más o menos.', 'lend', NULL);
INSERT INTO `support` VALUES(54, 'urban-social-design-database', 'Viajero', 'estudiante becario para viajar por europa y dar a conocer el proyecto', 'task', 87);
INSERT INTO `support` VALUES(55, 'urban-social-design-database', 'film maker', 'film maker', 'task', 88);
INSERT INTO `support` VALUES(56, 'urban-social-design-database', 'espacio', 'plaza de trabajo en oficina compartida', 'lend', 89);
INSERT INTO `support` VALUES(57, 'urban-social-design-database', 'camara video', 'camara video', 'lend', 90);
INSERT INTO `support` VALUES(58, 'urban-social-design-database', 'hosting profesional', 'hosting profesional', 'lend', 91);
INSERT INTO `support` VALUES(59, 'archinhand-architecture-in-your-hand', 'Testers de la herramienta', 'Estudiantes de arquitectura', 'task', 100);
INSERT INTO `support` VALUES(60, 'mi-barrio', 'Diseñadores', 'Diseñadores web, espertos en redes sociales', 'task', 0);
INSERT INTO `support` VALUES(61, 'mi-barrio', 'Aulas para talleres', 'Aulas para talleres', 'lend', 0);
INSERT INTO `support` VALUES(62, 'move-commons', 'Diseñador gráfico', 'Diseñador gráfico', 'task', 101);
INSERT INTO `support` VALUES(63, 'move-commons', 'Traductores a múltiples idiomas (20h/c)', 'Traductores a múltiples idiomas (20h/c)', 'task', 102);
INSERT INTO `support` VALUES(64, 'move-commons', 'Testers', 'Colectivos', 'task', 103);
INSERT INTO `support` VALUES(65, 'nodo-movil', 'Desarrolladores', 'Desarrolladores de Exo.cat, grupo Manet. Expertos en streaming y telefonía móvil..', 'task', 81);
INSERT INTO `support` VALUES(66, 'nodo-movil', 'Espacio de trabajo', 'Sala de hackeo / trabajo / reunión. para 10 personas.', 'lend', 82);
INSERT INTO `support` VALUES(67, 'nodo-movil', 'Espacio público', 'Espacio público (accesible para trabajar ).', 'lend', 83);
INSERT INTO `support` VALUES(68, 'canal-alfa', 'Programación extractor', 'Programación herramientas para la extración de videos', 'task', 0);
INSERT INTO `support` VALUES(69, 'canal-alfa', 'Investigación', 'Investigación de algoritmos para el reconocimiento automatizado del contenido de vídeos', 'task', 0);
INSERT INTO `support` VALUES(70, 'canal-alfa', 'Programación editor', 'Programación editor de vídeo online', 'task', 0);
INSERT INTO `support` VALUES(71, 'robocicla', 'Traductor', 'Traductor@ (ingles / portugues / italiano / frances / griego)', 'task', 80);
INSERT INTO `support` VALUES(72, '2c667d6a62707f369bad654174116a1e', 'TAREA DESDE DASHBOAR', 'FUNCIONA', 'task', 99);
INSERT INTO `support` VALUES(74, '3d72d03458ebd5797cc5fc1c014fc894', 'Nueva colaboración', '', 'lend', 0);
INSERT INTO `support` VALUES(75, '96ce377a34e515ab748a096093187d53', 'Nueva colaboración', '', 'task', 0);
INSERT INTO `support` VALUES(76, 'goteo', 'Nueva', 'dsf asdf asdf asdf sadf asdf f', 'task', 0);
INSERT INTO `support` VALUES(81, 'hkp', 'Nueva colaboración', '', 'task', 0);
INSERT INTO `support` VALUES(85, '8c069c398c3e3114e681ccfafa4a3fe0', 'Nueva colaboración', '', 'task', 0);
INSERT INTO `support` VALUES(86, '28c0caa840fc9c642160b1e2774667ff', 'Nueva colaboración', '', 'task', 0);
INSERT INTO `support` VALUES(97, 'f1dd9c1678c62273e21b67bff6e8b47a', 'Nueva colaboración', '', 'lend', 0);
INSERT INTO `support` VALUES(98, '8851739335520c5eeea01cd745d0442d', 'Nueva colaboración', '', 'task', 0);
INSERT INTO `support` VALUES(99, '8851739335520c5eeea01cd745d0442d', 'Nueva colaboración', '', 'lend', 0);
INSERT INTO `support` VALUES(100, 'fe99373e968b0005e5c2406bc41a3528', 'Furgoneta transporte', 'Furgoneta transporte', 'task', 0);
INSERT INTO `support` VALUES(101, 'tweetometro', 'Beta-testers y difusores ', 'Son bienvenidas la ayuda de difusión para que mucha gente conozca la herramienta y participe en las campañas. También se necesitará en determinados momentos hacer pruebas masivas para ver la resistencia de la aplicación. ', 'task', 106);
INSERT INTO `support` VALUES(102, 'tweetometro', 'Servidores', 'Aunque contamos con un servidor, dependiendo del numero de vistas y usuarios de la herramienta, necesitaremos "patrocinadores" en espacio web para hacer más fácil el mantenimiento del proyecto y que pueda continuar siendo de uso gratuito.', 'lend', 107);
INSERT INTO `support` VALUES(104, 'tutorial-goteo', 'Colaboración en una tarea', 'Colaboración que requiera una habilidad para una tarea específica (traducciones, gestiones, difusión, etc), ya sea a distancia mediante ordenador o bien presencialmente.', 'task', 108);
INSERT INTO `support` VALUES(105, 'tutorial-goteo', 'Colaboración en un préstamo', 'Préstamo temporal de material necesario para el proyecto, o bien de cesión de infraestructuras o espacios por un periodo determinado. También puede implicar préstamos permanentes, o sea regalos :)', 'lend', 109);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `blog` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tags de blogs (de nodo)' AUTO_INCREMENT=11 ;

--
-- Volcar la base de datos para la tabla `tag`
--

INSERT INTO `tag` VALUES(2, 'Proyectos', 1);
INSERT INTO `tag` VALUES(3, 'Goteo', 1);
INSERT INTO `tag` VALUES(7, 'Recursos', 1);
INSERT INTO `tag` VALUES(8, 'Casos de exíto', 1);
INSERT INTO `tag` VALUES(9, 'Convocatorías', 1);
INSERT INTO `tag` VALUES(10, 'Eventos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tag_lang`
--

DROP TABLE IF EXISTS `tag_lang`;
CREATE TABLE `tag_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `tag_lang`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `text`
--

DROP TABLE IF EXISTS `text`;
CREATE TABLE `text` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'es',
  `text` text NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Textos multi-idioma';

--
-- Volcar la base de datos para la tabla `text`
--

INSERT INTO `text` VALUES('blog-coments-header', 'ca', 'Comentaris');
INSERT INTO `text` VALUES('blog-no_posts', 'es', 'No se ha publicado ninguna entrada de actualizacion');
INSERT INTO `text` VALUES('blog-read_more', 'es', 'Leer más');
INSERT INTO `text` VALUES('blog-side-tags', 'es', 'Categorías de proyecto');
INSERT INTO `text` VALUES('costs-field-required_cost-no', 'es', 'Adicional');
INSERT INTO `text` VALUES('costs-fields-metter-title', 'es', 'Visualización de costes');
INSERT INTO `text` VALUES('costs-main-header', 'es', 'Aspectos económicos');
INSERT INTO `text` VALUES('discover-banner-header', 'es', 'Por categoria, lugar o retorno,<br /><span class="red">encuentra el proyecto</span> con el que más te identificas');
INSERT INTO `text` VALUES('discover-group-all-header', 'es', 'En campaña');
INSERT INTO `text` VALUES('error-contact-subject-empty', 'es', 'No has puesto el asunto');
INSERT INTO `text` VALUES('discover-group-outdate-header', 'es', 'A punto de caducar');
INSERT INTO `text` VALUES('discover-group-popular-header', 'es', 'Más populares');
INSERT INTO `text` VALUES('discover-group-recent-header', 'es', 'Recientes');
INSERT INTO `text` VALUES('discover-group-success-header', 'es', 'Exitosos');
INSERT INTO `text` VALUES('error-image-name', 'es', 'Error en el nombre del archivo');
INSERT INTO `text` VALUES('error-image-size', 'es', 'Error en el tamaño del archivo');
INSERT INTO `text` VALUES('error-image-size-too-large', 'es', 'La imagen es demasiado grande');
INSERT INTO `text` VALUES('error-image-tmp', 'es', 'Error al cargar el archivo');
INSERT INTO `text` VALUES('error-image-type', 'es', 'Solo se permiten imágenes jpg, png y gif');
INSERT INTO `text` VALUES('error-image-type-not-allowed', 'es', 'Texto tipos de imagen permitidos');
INSERT INTO `text` VALUES('error-register-email', 'es', 'La dirección de correo es obligatoria.');
INSERT INTO `text` VALUES('error-register-email-confirm', 'es', 'La comprobación de email no coincide.');
INSERT INTO `text` VALUES('error-register-email-exists', 'es', 'El dirección de correo ya corresponde a un usuario registrado.');
INSERT INTO `text` VALUES('error-register-invalid-password', 'es', 'La contraseña no es valida.');
INSERT INTO `text` VALUES('error-register-password-confirm', 'es', 'La comprobación de contraseña no coincide.');
INSERT INTO `text` VALUES('error-register-pasword', 'es', 'La contraseña no puede estar vacía.');
INSERT INTO `text` VALUES('error-register-pasword-empty', 'es', 'No has puesto contraseña');
INSERT INTO `text` VALUES('error-register-short-password', 'es', 'La contraseña debe contener un mínimo de 8 caracteres.');
INSERT INTO `text` VALUES('error-register-user-exists', 'es', 'Este nombre de usuario ya está registrado.');
INSERT INTO `text` VALUES('error-register-username', 'es', 'El nombre de usuario es obligatorio.');
INSERT INTO `text` VALUES('error-user-email-confirm', 'es', 'La confirmación de email no es igual que el email');
INSERT INTO `text` VALUES('error-user-email-empty', 'es', 'No puedes dejar el email vacio');
INSERT INTO `text` VALUES('error-user-email-exists', 'es', 'Ya hay un usuario registrado con este email');
INSERT INTO `text` VALUES('error-user-email-invalid', 'es', 'El email que has puesto no es válido');
INSERT INTO `text` VALUES('error-user-email-token-invalid', 'es', 'El código no es correcto');
INSERT INTO `text` VALUES('error-user-password-confirm', 'es', 'La confirmación de contraseña no es igual a la contraseña');
INSERT INTO `text` VALUES('error-user-password-empty', 'es', 'No has puesto la contraseña');
INSERT INTO `text` VALUES('error-user-password-invalid', 'es', 'La contraseña es demasiado corta, debe tener al menos 6 caracteres');
INSERT INTO `text` VALUES('error-user-wrong-password', 'es', 'La contraseña no es correcta');
INSERT INTO `text` VALUES('explain-project-progress', 'es', 'Nivel de información completada: a medida que cumplimentes los datos puedes ver en este gráfico una evaluación automatizada del proyecto. Una vez nos lo envíes, el porcentaje final para decidir si publicarlo o no dependerá de su valoración por parte de la comunidad de Goteo.');
INSERT INTO `text` VALUES('footer-header-social', 'es', 'Síguenos');
INSERT INTO `text` VALUES('form-project-progress-title', 'es', 'Evaluación de datos');
INSERT INTO `text` VALUES('form-project_status-review', 'es', 'Pendiente de valoración');
INSERT INTO `text` VALUES('guide-dashboard-user-profile', 'es', 'Tanto si quieres presentar un proyecto como incorporarte como cofinanciador/a, para formar parte de la comunidad de Goteo te recomendamos que pongas atención en tu texto de presentación, que añadas links relevantes sobre lo que haces y subas una imagen de perfil con la que te identifiques.');
INSERT INTO `text` VALUES('guide-project-comment', 'es', 'guide-project-comment');
INSERT INTO `text` VALUES('guide-project-contract-information', 'es', '<strong>A partir de este paso sólo debes cumplimentar los datos si quieres que tu proyecto sea cofinanciado y apoyado mediante Goteo. </strong><br><br>La información de este apartado es necesaria para contactarte en caso de que obtengas la financiación requerida, y que así se pueda efectuar el ingreso.');
INSERT INTO `text` VALUES('guide-project-costs', 'es', '<strong>En esta sección debes elaborar un pequeño presupuesto basado en los costes que calcules va a tener la realización del proyecto.</strong><br /><br /><br />\r\nDebes especificar según tareas, infraestructura o materiales. Intenta ser realista en los costes y explicar brevemente por qué necesitas cubrir cada uno de ellos. Verás que diferenciamos entre costes imprescindibles y costes adicionales, donde los primeros deben suponer más de la mitad del total a cofinanciar.');
INSERT INTO `text` VALUES('guide-project-description', 'es', '<strong>Éste es el apartado donde explicar con detalle los aspectos conceptuales del proyecto. </strong><br><br>Es lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir un vídeo o subir imágenes. Esto es así porque los consideramos imprescindibles para empezar con éxito una campaña de recaudación de fondos mediante Goteo.');
INSERT INTO `text` VALUES('guide-project-error-mandatories', 'es', 'Faltan campos obligatorios');
INSERT INTO `text` VALUES('guide-project-overview', 'es', 'Este copie dónde aparece?? > Éste es el apartado donde explicar con detalle los aspectos conceptuales del proyecto. <br><br>Es lo primero con lo que cualquier usuario de la red se encontrará, así que cuida la redacción y evita las faltas de ortografía. Verás que hay campos obligatorios como incluir un vídeo o subir imágenes. Esto es así porque los consideramos imprescindibles para empezar con éxito una campaña de recaudación de fondos mediante Goteo.');
INSERT INTO `text` VALUES('guide-project-preview', 'es', '<strong>Éste es un resumen de toda la información sobre el proyecto.</strong><br><br> Repasa los puntos de cada apartado para ver si puedes mejorar algo, o bien envía el proyecto para su valoración si ya están cumplimentados todos los campos obligatorios, para que así pueda ser valorado por el equipo y la comunidad de Goteo. Una vez lo envíes ya no se podrán introducir cambios. Ten en cuenta que sólo podemos seleccionar unos cuantos proyectos al mes para garantizar la atención y la difusión de las propuestas que se hacen públicas. Próximamente recibirás un mensaje con toda la información, que te indicará los pasos a seguir y recomendaciones para que tu proyecto pueda alcanzar la meta propuesta. ');
INSERT INTO `text` VALUES('guide-project-rewards', 'es', '<strong>En este apartado debes establecer qué ofrece el proyecto a cambio a sus cofinanciadores, y también sus retornos colectivos.</strong><br>\r\nAdemás de las recompensas individuales para cada importe de cofinanciación, aquí debes definir qué tipo de licencia asignar al proyecto, en función de su formato y/o del grado de abertura del mismo (o de alguna de sus partes). Esta parte es muy importante, ya que Goteo es una plataforma de crowdfunding para proyectos basados en la filosofía del código abierto y que fortalezcan el procomún.');
INSERT INTO `text` VALUES('guide-project-success-minprogress', 'es', 'Ha llegado al porcentaje mínimo');
INSERT INTO `text` VALUES('guide-project-success-noerrors', 'es', 'Todos los campos obligatorios estan rellenados');
INSERT INTO `text` VALUES('guide-project-success-okfinish', 'es', 'Puede enviar para revisión');
INSERT INTO `text` VALUES('guide-project-support', 'es', 'guide-project-support');
INSERT INTO `text` VALUES('guide-project-supports', 'es', '<strong>En este apartado puedes especificar qué otras ayudas, a parte de financiación, se necesitan para llevar a cabo el proyecto.</strong><br><br> Pueden ser tareas o acciones a cargo de otras personas (traducciones, gestiones, difusión, etc), o bien préstamos específicos (de material, transporte, hardware, etc).');
INSERT INTO `text` VALUES('guide-project-updates', 'es', 'guide-project-updates');
INSERT INTO `text` VALUES('guide-project-user-information', 'es', '<strong>En este apartado debes introducir los datos para la información pública de tu perfil de usuario. </strong><br><br>Tanto si quieres presentar un proyecto como incorporarte como cofinanciador/a, para formar parte de la comunidad de Goteo te recomendamos que pongas atención en tu texto de presentación, que añadas links relevantes sobre lo que haces y subas una imagen de perfil con la que te identifiques.');
INSERT INTO `text` VALUES('guide-user-data', 'es', 'Texto guía en la edición de campos sensibles.');
INSERT INTO `text` VALUES('guide-user-information', 'es', '2.Texto guía en la edición de información del usuario. ???????');
INSERT INTO `text` VALUES('guide-user-register', 'es', 'Texto guía en el registro de un nuevo usuario.');
INSERT INTO `text` VALUES('home-promotes-header', 'es', 'Destacados');
INSERT INTO `text` VALUES('invest-abitmore', 'es', 'Por %s más serías %s');
INSERT INTO `text` VALUES('login-acces-password-field', 'es', 'Contraseña');
INSERT INTO `text` VALUES('login-acces-username-field', 'es', 'Usuario');
INSERT INTO `text` VALUES('login-banner-header', 'es', 'Accede a la comunidad goteo<br /><span class="greenblue">100% abierto</span>');
INSERT INTO `text` VALUES('mandatory-cost-field-amount', 'es', 'Es obligatorio asignar un importe a los costes');
INSERT INTO `text` VALUES('mandatory-cost-field-description', 'es', 'Es obligatorio poner alguna descripción a los costes');
INSERT INTO `text` VALUES('mandatory-cost-field-name', 'es', 'Es obligatorio ponerle un nombre al coste');
INSERT INTO `text` VALUES('mandatory-cost-field-type', 'es', 'Es obligatorio seleccionar el tipo de coste');
INSERT INTO `text` VALUES('mandatory-individual_reward-field-amount', 'es', 'Es obligatorio indicar el importe que otorga la recompensa');
INSERT INTO `text` VALUES('mandatory-individual_reward-field-description', 'es', 'Es obligatorio poner alguna descripción');
INSERT INTO `text` VALUES('mandatory-individual_reward-field-name', 'es', 'Es obligatorio poner la recompensa');
INSERT INTO `text` VALUES('mandatory-project-costs', 'es', 'Debe desglosar en al menos dos costes.');
INSERT INTO `text` VALUES('mandatory-project-field-about', 'es', 'Es obligatorio explicar las características básicas del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-address', 'es', 'La dirección del responsable del proyecto es obligatoria');
INSERT INTO `text` VALUES('mandatory-project-field-category', 'es', 'Es obligatorio elegir al menos una categoria para el proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-contract-email', 'es', 'Es obligatorio poner el email del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-contract-name', 'es', 'Es obligatorio poner el nombre del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-contract-nif', 'es', 'Es obligatorio poner el documento de identificacción del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-contract-surname', 'es', 'Es obligatorio poner los apellidos del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-country', 'es', 'El país del responsable del proyecto es obligatorio');
INSERT INTO `text` VALUES('mandatory-project-field-description', 'es', 'Es obligatorio resumir el proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-goal', 'es', 'Es obligatorio explicar los objetivos en la descripción del proyecto');
INSERT INTO `text` VALUES('mandatory-project-field-image', 'es', 'Es obligatorio vincular una imagen como mínimo al proyecto. ');
INSERT INTO `text` VALUES('mandatory-project-field-location', 'es', 'Es obligatorio poner el alcance potencial del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-media', 'es', 'Poner un vídeo para mejorar la puntuación');
INSERT INTO `text` VALUES('mandatory-project-field-motivation', 'es', 'Es obligatorio explicar la motivación en la descripción del proyecto');
INSERT INTO `text` VALUES('mandatory-project-field-name', 'es', 'Es obligatorio poner un NOMBRE al proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-phone', 'es', 'El teléfono del responsable del proyecto es obligatorio');
INSERT INTO `text` VALUES('mandatory-project-field-related', 'es', 'Es obligatorio explicar la experiencia relacionada y/o el equipo con que se cuenta en la descripción del proyecto');
INSERT INTO `text` VALUES('mandatory-project-field-residence', 'es', 'Es obligatorio poner el lugar de residencia del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-resource', 'es', 'Es obligatorio especificar si cuentas con otros recursos');
INSERT INTO `text` VALUES('mandatory-project-field-zipcode', 'es', 'El código postal del responsable del proyecto es obligatorio');
INSERT INTO `text` VALUES('mandatory-register-field-email', 'es', 'Tienes que poner un email');
INSERT INTO `text` VALUES('mandatory-social_reward-field-description', 'es', 'Es obligatorio poner alguna descripción al retorno');
INSERT INTO `text` VALUES('mandatory-social_reward-field-name', 'es', 'Es obligatorio poner el retorno');
INSERT INTO `text` VALUES('mandatory-support-field-description', 'es', 'Es obligatorio poner alguna descripción');
INSERT INTO `text` VALUES('mandatory-support-field-name', 'es', 'Es obligatorio ponerle un nombre a la colaboración');
INSERT INTO `text` VALUES('open-banner-header', 'es', '<div class="modpo-open">OPEN</div><div class="modpo-percent">100% ABIERTO</div><div class="modpo-whyopen">¿Por qué abierto?</div>');
INSERT INTO `text` VALUES('overview-field-about', 'es', 'Características básicas');
INSERT INTO `text` VALUES('overview-field-description', 'es', 'Breve descripción ');
INSERT INTO `text` VALUES('overview-field-image_gallery', 'es', 'Imágenes actuales');
INSERT INTO `text` VALUES('overview-field-keywords', 'es', 'Palabras clave del proyecto');
INSERT INTO `text` VALUES('overview-field-media', 'es', 'Vídeo de presentación');
INSERT INTO `text` VALUES('overview-field-related', 'es', 'Experiencia previa y equipo');
INSERT INTO `text` VALUES('overview-field-scope', 'es', 'Alcance del proyecto');
INSERT INTO `text` VALUES('overview-fields-images-title', 'es', 'Imágenes del proyecto');
INSERT INTO `text` VALUES('overview-main-header', 'es', 'Descripción del proyecto');
INSERT INTO `text` VALUES('personal-main-header', 'es', 'Datos personales');
INSERT INTO `text` VALUES('preview-main-header', 'es', 'Previsualización de datos');
INSERT INTO `text` VALUES('profile-field-contribution', 'es', 'Qué puedes aportar a Goteo');
INSERT INTO `text` VALUES('profile-field-interests', 'es', 'Qué tipo de proyecto te motiva más');
INSERT INTO `text` VALUES('profile-field-keywords', 'es', 'Temas que te interesan');
INSERT INTO `text` VALUES('profile-field-location', 'es', 'Lugar de residencia habitual');
INSERT INTO `text` VALUES('profile-field-name', 'es', 'Nombre de usuario/a');
INSERT INTO `text` VALUES('profile-field-websites', 'es', 'Mis páginas web');
INSERT INTO `text` VALUES('profile-fields-image-title', 'es', 'Imagen de perfil');
INSERT INTO `text` VALUES('profile-interests-header', 'es', 'Me interesan proyectos con fin...');
INSERT INTO `text` VALUES('profile-main-header', 'es', 'Datos de perfil');
INSERT INTO `text` VALUES('profile-my_worth-header', 'es', 'Mi rol en goteo');
INSERT INTO `text` VALUES('profile-profile-my_projects-header-header', 'es', 'profile-profile-my_projects-header-header');
INSERT INTO `text` VALUES('project-menu-updates', 'es', 'Novedades');
INSERT INTO `text` VALUES('project-rewards-individual_reward-units_left', 'es', 'Quedan <span class="left">%s</span> unidades');
INSERT INTO `text` VALUES('regular-last', 'es', 'Última');
INSERT INTO `text` VALUES('regular-mandatory', 'es', 'Campo obligatorio!');
INSERT INTO `text` VALUES('regular-news', 'es', 'Noticias:');
INSERT INTO `text` VALUES('regular-twitter', 'es', 'Twitter');
INSERT INTO `text` VALUES('rewards-field-individual_reward-amount', 'es', 'Importe financiado');
INSERT INTO `text` VALUES('rewards-field-individual_reward-description', 'es', 'Descripción');
INSERT INTO `text` VALUES('rewards-field-individual_reward-type', 'es', 'Tipo de recompensa');
INSERT INTO `text` VALUES('rewards-field-social_reward-license', 'es', 'Opciones de licencia');
INSERT INTO `text` VALUES('rewards-field-social_reward-type', 'es', 'Tipo de retorno');
INSERT INTO `text` VALUES('rewards-main-header', 'es', 'Retornos y recompensas');
INSERT INTO `text` VALUES('social-account-identica', 'es', 'http://identi.ca/goteofunding');
INSERT INTO `text` VALUES('social-account-twitter', 'es', 'http://twitter.com/goteofunding');
INSERT INTO `text` VALUES('step-1', 'es', 'Perfil');
INSERT INTO `text` VALUES('step-2', 'es', 'Datos personales');
INSERT INTO `text` VALUES('step-3', 'es', 'Descripción');
INSERT INTO `text` VALUES('step-4', 'es', 'Costes');
INSERT INTO `text` VALUES('step-5', 'es', 'Retorno');
INSERT INTO `text` VALUES('step-6', 'es', 'Colaboraciones');
INSERT INTO `text` VALUES('step-7', 'es', 'Previsualización');
INSERT INTO `text` VALUES('step-costs', 'es', 'Paso 4: Proyecto / Costes');
INSERT INTO `text` VALUES('step-overview', 'es', 'Paso 3: Descripción del proyecto');
INSERT INTO `text` VALUES('step-preview', 'es', 'Proyecto / Previsualizaciíon');
INSERT INTO `text` VALUES('step-rewards', 'es', 'Paso 5: Proyecto / Retornos');
INSERT INTO `text` VALUES('step-supports', 'es', 'Paso 6: Proyecto / Colaboraciones');
INSERT INTO `text` VALUES('step-userPersonal', 'es', 'Paso 2: Datos personales');
INSERT INTO `text` VALUES('step-userProfile', 'es', 'Paso 1: Usuario / Perfil');
INSERT INTO `text` VALUES('subject-change-email', 'es', 'Asunto del mail al cambiar el email');
INSERT INTO `text` VALUES('subject-register', 'es', 'Confirmación de usuario en Goteo');
INSERT INTO `text` VALUES('supports-field-type', 'es', 'Tipo de ayuda');
INSERT INTO `text` VALUES('supports-main-header', 'es', 'Solicitud de colaboraciones');
INSERT INTO `text` VALUES('tooltip-project-about', 'es', 'Describe brevemente el proyecto de modo conceptual, técnico o práctico. Por ejemplo detallando sus características de funcionamiento, o en qué partes consistirá. Piensa en cómo será una vez acabado y todo lo que la gente podrá hacer con él.');
INSERT INTO `text` VALUES('tooltip-project-address', 'es', 'Dirección postal de tu residencia habitual.');
INSERT INTO `text` VALUES('tooltip-project-category', 'es', 'Selecciona tantas categorías como creas necesario para describir el proyecto, basándote en todo lo que has descrito arriba. Mediante estas palabras clave lo podremos hacer llegar a diferentes usuarios de Goteo.');
INSERT INTO `text` VALUES('tooltip-project-comment', 'es', '¿Tienes dudas o comentarios para que las lea el administrador de Goteo? Éste es lugar para explicar alguna parte de lo que has escrito de la que no estás seguro,  para proponer mejoras, etc.');
INSERT INTO `text` VALUES('tooltip-project-contract_email', 'es', 'P2-Consejo-?? Consejo para rellenar el email del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-contract_name', 'es', 'Deben ser tu nombre y apellidos reales. Ten en cuenta que figurarás como responsable del proyecto.');
INSERT INTO `text` VALUES('tooltip-project-contract_nif', 'es', 'Tu número de NIF con cifras y letra.');
INSERT INTO `text` VALUES('tooltip-project-contract_surname', 'es', 'P2-Consejo-5  Consejo para rellenar el apellido del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-cost', 'es', '?? Consejo para editar desgloses existentes');
INSERT INTO `text` VALUES('tooltip-project-cost-amount', 'es', 'Especifica el importe en euros de lo que consideras que implica este coste. No utilices puntos para la cifras de miles ok?');
INSERT INTO `text` VALUES('tooltip-project-cost-cost', 'es', 'Introduce un título lo más descriptivo posible de este coste.');
INSERT INTO `text` VALUES('tooltip-project-cost-dates', 'es', 'Indica entre qué fechas calculas que se va a llevar a cabo esa tarea o cubrir ese coste. Planifícalo empezando no antes de dos meses a partir de ahora, pues hay que considerar el plazo para revisar la propuesta, publicarla si es seleccionada y los 40 días de la primera financiación.');
INSERT INTO `text` VALUES('tooltip-project-cost-description', 'es', 'Explica brevemente en qué consiste este coste.');
INSERT INTO `text` VALUES('tooltip-project-cost-from', 'es', '7 Texto tooltip fecha desde costes');
INSERT INTO `text` VALUES('tooltip-project-cost-required', 'es', 'Este punto es muy importante: en cada coste que introduzcas tienes que marcar si es imprescindible o bien secundario. Todos los costes marcados como imprescindibles se sumarán dando el valor del importe de financiación mínimo para el proyecto. La suma de los costes adicionales, en cambio, se podrá obtener durante la segunda ronda de financiación, después de haber obtenido los fondos imprescindibles.');
INSERT INTO `text` VALUES('tooltip-project-cost-type', 'es', 'Aquí debes especificar el tipo de coste: vinculado a una tarea (algo que requiere la habilidad o conocimientos de alguien), la obtención de material (consumibles, materias primas) o bien infraestructura (espacios, equipos, mobiliario).');
INSERT INTO `text` VALUES('tooltip-project-cost-type-material', 'es', 'Materiales necesarios para el proyecto como herramientas, papelería, equipos informáticos, etc.');
INSERT INTO `text` VALUES('tooltip-project-cost-type-structure', 'es', 'Inversión en costes vinculados a un local, medio de transporte u otras infraestructuras básicas para llevar a cabo el proyecto.  ');
INSERT INTO `text` VALUES('tooltip-project-cost-type-task', 'es', 'Tareas donde invertir conocimientos y/o habilidades para desarrollar alguna parte del proyecto.');
INSERT INTO `text` VALUES('tooltip-project-cost-until', 'es', '???10 Texto tooltip fecha coste hasta');
INSERT INTO `text` VALUES('tooltip-project-costs', 'es', 'Cuanto más precisión en el desglose mejor valorará Goteo la información general del proyecto. ');
INSERT INTO `text` VALUES('tooltip-project-country', 'es', 'País de residencia habitual.');
INSERT INTO `text` VALUES('tooltip-project-currently', 'es', 'Indica en qué fase se encuentra el proyecto actualmente respecto a su proceso de creación o ejecución.');
INSERT INTO `text` VALUES('tooltip-project-description', 'es', 'Describe el proyecto con un mínimo de 80 palabras (con menos marcará error). Explícalo de modo que sea fácil de entender para cualquier persona. Intenta darle un enfoque atractivo y social, resumiendo sus características básicas.');
INSERT INTO `text` VALUES('tooltip-project-goal', 'es', 'Enumera las metas principales del proyecto, a corto y largo plazo, en todos los aspectos que consideres importante destacar. Se trata de otra oportunidad para contactar y conseguir el apoyo de gente que simpatice con esos objetivos.');
INSERT INTO `text` VALUES('tooltip-project-image', 'es', 'Pueden ser esquemas, pantallazos, fotografías, ilustraciones, storyboards, etc. (su licencia de autoría debe ser compatible con la que selecciones en el apartado 5). Te recomendamos que sean diversas y de buena resolución. Puedes subir tantas como quieras!');
INSERT INTO `text` VALUES('tooltip-project-image_upload', 'es', 'BORRAR');
INSERT INTO `text` VALUES('tooltip-project-individual_reward', 'es', '2 Consejo para editar retornos individuales existentes');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-amount', 'es', 'Importe a cambio del cual se puede obtener este tipo de recompensa. ');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-description', 'es', 'Explica brevemente en qué consistirá la recompensa para quienes cofinancien con este importe el proyecto.');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-icon-other', 'es', 'Especifica brevemente en qué consistirá este otro tipo de recompensa individual.');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-reward', 'es', 'Intenta que el título sea lo más descriptivo posible. Recuerda que puedes añadir más recompensas a continuación.');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-type', 'es', 'Selecciona el tipo de recompensa que el proyecto puede ofrecer a la gente que aporta esta cantidad.');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-units', 'es', 'Cantidad limitada de ítems que se pueden ofrecer individualizadamente a cambio de ese importe. Calcula que la suma total de todas las recompensas individuales del proyecto se acerquen al presupuesto mínimo de financiación que has establecido. También la posibilidad de incorporar las recompensas previas a medida que suba el importe, puedes empezar diciendo "Todo lo anterior más..."  ');
INSERT INTO `text` VALUES('tooltip-project-individual_rewards', 'es', 'Aquí debes especificar la recompensa para quien apoye el proyecto, vinculada a una cantidad de dinero concreta. Elige bien lo que ofreces, intenta que sean productos/servicios atractivos o ingeniosos pero que no generen gastos extra de producción. Si no hay más remedio que tener esos gastos extra, calcula lo que cuesta producir esa recompensa (tiempo, materiales, envíos, etc) y oferta un número limitado. Piensa que tendrás que cumplir con todos esos compromisos al final de la producción del proyecto. ');
INSERT INTO `text` VALUES('tooltip-project-keywords', 'es', 'A medida que introduzcas texto el sistema te sugerirá palabras clave que ya han escrito otros usuarios. Estas categorías ayudan a vincular tu proyecto con personas afines.');
INSERT INTO `text` VALUES('tooltip-project-location', 'es', 'Localidad donde se encuentra tu residencia habitual.');
INSERT INTO `text` VALUES('tooltip-project-media', 'es', 'Copia y engancha la dirección URL de un vídeo de presentación del proyecto, publicado previamente en Youtube o Vimeo. Esta parte es fundamental para atraer la atención de potenciales cofinanciadores y colaboradores, y cuanto más original sea mejor. Te recomendamos que tenga una duración de entre 2 y 4 minutos. ');
INSERT INTO `text` VALUES('tooltip-project-motivation', 'es', 'Explica qué motivos o circunstancias han llevado a idear el proyecto. Así podrá conectar con más personas motivadas por ese mismo tipo de intereses, problemáticas o gustos.');
INSERT INTO `text` VALUES('tooltip-project-name', 'es', 'Escoge un nombre para el proyecto (ni demasiado corto, ni demasiado largo :) que permita hacerse una idea mínima de para qué sirve o en qué consiste. Piensa que va a aparecer en muchos sitios de la web!');
INSERT INTO `text` VALUES('tooltip-project-ncost', 'es', '??? Consejo para rellenar un nuevo desglose de costes.');
INSERT INTO `text` VALUES('tooltip-project-nindividual_reward', 'es', '8 Consejo para rellenar un nuevo retorno individual');
INSERT INTO `text` VALUES('tooltip-project-nsocial_reward', 'es', '9 Consejo para rellenar un nuevo retorno colectivo');
INSERT INTO `text` VALUES('tooltip-project-nsupport', 'es', 'Consejo para rellenar una nueva colaboración');
INSERT INTO `text` VALUES('tooltip-project-phone', 'es', 'Número de teléfono móvil o fijo, con su prefijo de marcado.');
INSERT INTO `text` VALUES('tooltip-project-project_location', 'es', 'Indica el lugar donde se desarrollará el proyecto, en qué población o poblaciones se encuentra su impulsor o impulsores principales.');
INSERT INTO `text` VALUES('tooltip-project-related', 'es', 'Resume tu trayectoria o la del grupo impulsor del proyecto. ¿Qué experiencia tiene relacionada con la propuesta? ¿Con qué equipo de personas, recursos y/o infraestructuras cuenta? ');
INSERT INTO `text` VALUES('tooltip-project-resource', 'es', 'Indica aquí si cuentas con recursos adicionales, a parte de los que solicitas, para llevar a cabo el proyecto: fuentes de financiación, recursos propios o bien ya has hecho acopio de materiales. Puede suponer un aliciente para los potenciales cofinanciadores del proyecto.');
INSERT INTO `text` VALUES('tooltip-project-schedule', 'es', '??? Texto tooltip agenda del proyeecto');
INSERT INTO `text` VALUES('tooltip-project-scope', 'es', 'Indica el impacto geográfico que aspira a tener el proyecto (cada categoría incluye la anterior). ');
INSERT INTO `text` VALUES('tooltip-project-social_reward', 'es', '10 Consejo para editar retornos colectivos existentes');
INSERT INTO `text` VALUES('tooltip-project-social_reward-description', 'es', 'Explica en brevemente el tipo de retorno colectivo que ofrecerá o permitirá el proyecto.');
INSERT INTO `text` VALUES('tooltip-project-social_reward-icon-other', 'es', 'Especifica brevemente en qué consistirá este otro tipo de retorno colectivo.');
INSERT INTO `text` VALUES('tooltip-project-social_reward-license', 'es', 'Aquí debes seleccionar una licencia de entre cada una del grupo que se muestran. Te recomendamos leerlas con calma antes de decidir, pero piensa que un aspecto importante para Goteo es que los proyectos dispongan de licencias lo más abiertas posible.');
INSERT INTO `text` VALUES('tooltip-project-social_reward-reward', 'es', 'Intenta que el título sea lo más descriptivo posible. Recuerda que puedes añadir más recompensas a continuación.');
INSERT INTO `text` VALUES('tooltip-project-social_reward-type', 'es', 'Especifica el tipo de retorno: ARCHIVOS DIGITALES como música, vídeo, documentos de texto, etc. CÓDIGO FUENTE de software informático. DISEÑOS de  planos o patrones. MANUALES en forma de kits, bussiness plans, “how tos” o recetas. SERVICIOS como talleres, cursos, asesorías, acceso a websites, bases de datos online. ');
INSERT INTO `text` VALUES('tooltip-project-social_rewards', 'es', 'Define el tipo de retorno o retornos del proyecto a los que se podrá acceder abiertamente, y la licencia que los debe regular. ');
INSERT INTO `text` VALUES('tooltip-project-support', 'es', 'Consejo para editar colaboraciones existentes');
INSERT INTO `text` VALUES('tooltip-project-support-description', 'es', 'Explica brevemente en qué consiste la ayuda que necesita el proyecto, para facilitar que la gente la reconozca y se anime a colaborar. \r\n');
INSERT INTO `text` VALUES('tooltip-project-support-support', 'es', 'Título descriptivo sobre la colaboración necesaria.');
INSERT INTO `text` VALUES('tooltip-project-support-type', 'es', 'Selecciona si el proyecto necesita ayuda en tareas concretas  o bien préstamos (de material, infraestructura, etc).  ');
INSERT INTO `text` VALUES('tooltip-project-support-type-lend', 'es', 'Préstamo temporal de material necesario para el proyecto, o bien de cesión de infraestructuras o espacios por un periodo determinado. También puede implicar préstamos permanentes, o sea regalos :)');
INSERT INTO `text` VALUES('tooltip-project-support-type-task', 'es', 'Colaboración que requiera una habilidad para una tarea específica, ya sea a distancia mediante ordenador o bien presencialmente.');
INSERT INTO `text` VALUES('tooltip-project-supports', 'es', 'Es importante que proyectos en Goteo puedan recibir otras ayudas a parte de aportaciones económicas. Hay gente que a lo mejor no pueda ayudar económicamente pero sí con su talento.');
INSERT INTO `text` VALUES('tooltip-project-totals', 'es', 'Este gráfico muestra la suma de costes imprescindibles (mínimos para realizar el proyecto) y la suma de costes secundarios (importe óptimo) para las dos rondas de financiación. La primera ronda es de 40 días, para conseguir el importe mínimo imprescindible. Sólo si se consigue ese volumen de financiación se puede optar a la segunda ronda, de 40 días más, para llegar al presupuesto óptimo. A diferencia de la primera, en la segunda ronda se obtiene todo lo recaudado (aunque no se haya llegado al mínimo). ');
INSERT INTO `text` VALUES('tooltip-project-zipcode', 'es', 'Código postal de tu residencia habitual.');
INSERT INTO `text` VALUES('tooltip-updates-allow_comments', 'es', 'tooltip-updates-allow_comments');
INSERT INTO `text` VALUES('tooltip-updates-date', 'es', 'tooltip-updates-date');
INSERT INTO `text` VALUES('tooltip-updates-image', 'es', 'tooltip-updates-image');
INSERT INTO `text` VALUES('tooltip-updates-image_upload', 'es', 'tooltip-updates-image_upload');
INSERT INTO `text` VALUES('tooltip-updates-media', 'es', 'tooltip-updates-media');
INSERT INTO `text` VALUES('tooltip-updates-text', 'es', 'tooltip-updates-text');
INSERT INTO `text` VALUES('tooltip-updates-title', 'es', 'tooltip-updates-title');
INSERT INTO `text` VALUES('tooltip-user-about', 'es', 'Como red social Goteo pretende ayudar a difundir y financiar proyectos interesantes entre el máximo de gente posible. Para eso es importante la información que puedas compartir sobre tus habilidades o experiencia (profesional, académica, aficiones, etc).\r\n');
INSERT INTO `text` VALUES('tooltip-user-avatar_upload', 'es', 'Texto tooltip subir imagen usuario');
INSERT INTO `text` VALUES('tooltip-user-blog', 'es', 'Consejo para rellenar la web ?????');
INSERT INTO `text` VALUES('tooltip-user-contribution', 'es', 'Explica brevemente tus habilidades o los ámbitos en que podrías ayudar a un proyecto (traduciendo, difundiendo, testeando, programando, enseñando, etc).');
INSERT INTO `text` VALUES('tooltip-user-email', 'es', '????? Consejo para rellenar el email de registro de usuario');
INSERT INTO `text` VALUES('tooltip-user-facebook', 'es', 'Esta red social puede ayudar a que difundas proyectos de Goteo que te interesan entre amigos y familiares.');
INSERT INTO `text` VALUES('tooltip-user-google', 'es', 'La red social de Google+ es muy nueva pero también puedes indicar tu usuario si ya la usas :)');
INSERT INTO `text` VALUES('tooltip-user-identica', 'es', 'Este canal puede ayudar a que difundas proyectos de Goteo entre la comunidad afín al software libre.');
INSERT INTO `text` VALUES('tooltip-user-image', 'es', 'No es obligatorio que pongas una fotografía en tu perfil, pero ayuda a que los demás usuarios te identifiquen.');
INSERT INTO `text` VALUES('tooltip-user-interests', 'es', 'Indica el tipo de proyectos que pueden conectar con tus intereses para cofinanciarlos y/o aportar con otros recursos, conocimientos o habilidades. Estas categorías son transversales, puedes seleccionar más de una.');
INSERT INTO `text` VALUES('tooltip-user-keywords', 'es', 'A medida que introduzcas texto el sistema te sugerirá palabras clave que ya han escrito otros usuarios. Estas categorías ayudan a vincular tu perfil con otras personas y con proyectos concretos.');
INSERT INTO `text` VALUES('tooltip-user-linkedin', 'es', 'Esta red social puede ayudar a que difundas proyectos de Goteo que te interesan entre contactos profesionales.');
INSERT INTO `text` VALUES('tooltip-user-location', 'es', 'Este dato es importante para poderte vincular con un grupo local de Goteo.');
INSERT INTO `text` VALUES('tooltip-user-name', 'es', 'Tu nombre o nickname dentro de Goteo. Lo puedes cambiar siempre que quieras (ojo: no es lo mismo que el login de acceso, que ya no se puede cambiar).');
INSERT INTO `text` VALUES('tooltip-user-twitter', 'es', 'Esta red social puede ayudar a que difundas proyectos de Goteo de manera ágil y viral.');
INSERT INTO `text` VALUES('tooltip-user-user', 'es', '8 Consejo para rellenar el nombre de usuario para login');
INSERT INTO `text` VALUES('tooltip-user-webs', 'es', 'Indica las direcciones URL de páginas personales o de otro tipo vinculadas a ti.');
INSERT INTO `text` VALUES('translate-home-guide', 'ca', 'Message pour le traducteur');
INSERT INTO `text` VALUES('translate-home-guide', 'es', 'Message pour le traducteur');
INSERT INTO `text` VALUES('translate-home-guide', 'fr', 'Message pour le traducteur');
INSERT INTO `text` VALUES('user-account-inactive', 'es', 'La cuenta esta desactivada');
INSERT INTO `text` VALUES('user-activate-already-active', 'es', 'La cuenta de usuario ya esta activada');
INSERT INTO `text` VALUES('user-activate-fail', 'es', 'Error al activar la cuenta de usuario');
INSERT INTO `text` VALUES('user-activate-success', 'es', 'La cuenta de usuario se ha activado correctamente');
INSERT INTO `text` VALUES('user-changeemail-fail', 'es', 'Error al cambiar el email');
INSERT INTO `text` VALUES('user-changeemail-success', 'es', 'El email se ha cambiado con exito');
INSERT INTO `text` VALUES('user-register-success', 'es', 'El usuario se ha registrado correctamente');
INSERT INTO `text` VALUES('validate-cost-field-dates', 'es', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-project-costs-any_error', 'es', 'Falta alguna información en el desglose de costes');
INSERT INTO `text` VALUES('validate-project-field-about', 'es', 'La explicacion del proyecto es demasiado corta');
INSERT INTO `text` VALUES('validate-project-field-costs', 'es', 'Desglosar hasta 5 costes para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-project-field-currently', 'es', 'Indicar el estado del proyecto para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-project-field-description', 'es', 'La descripcion del proyecto es demasiado corta');
INSERT INTO `text` VALUES('validate-project-field-motivation', 'es', 'validate-project-field-motivation');
INSERT INTO `text` VALUES('validate-project-individual_rewards', 'es', 'Indicar hasta 5 recompensas individuales para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-project-individual_rewards-any_error', 'es', 'Falta alguna información sobre recompensas individuales');
INSERT INTO `text` VALUES('validate-project-social_rewards', 'es', 'Te recomendamos indicar un mínimo de 5 retornos colectivos.');
INSERT INTO `text` VALUES('validate-project-social_rewards-any_error', 'es', 'Falta alguna información sobre retornos colectivos');
INSERT INTO `text` VALUES('validate-project-total-costs', 'es', 'El coste óptimo no puede superar en más de un 40% al coste mínimo. Revisar el DESGLOSE DE COSTES.');
INSERT INTO `text` VALUES('validate-project-value-contract-email', 'es', 'El EMAIL no es correcto.');
INSERT INTO `text` VALUES('validate-project-value-contract-nif', 'es', 'El NIF no es correcto.');
INSERT INTO `text` VALUES('validate-project-value-description', 'es', 'La DESCRIPCIÓN del proyecto es demasiado corta.');
INSERT INTO `text` VALUES('validate-project-value-keywords', 'es', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-project-value-phone', 'es', 'El TELÉFONO no es correcto.');
INSERT INTO `text` VALUES('validate-register-value-email', 'es', 'El email introducido no es valido');
INSERT INTO `text` VALUES('validate-social_reward-license', 'es', 'Indicar una licencia para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-user-field-about', 'es', 'Cuenta algo sobre ti para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-user-field-avatar', 'es', 'Pon una imagen de perfil para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-user-field-contribution', 'es', 'Explica que podrias aportar en Goteo para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-user-field-facebook', 'es', 'Pon tu cuenta de facebook para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-user-field-interests', 'es', 'Selecciona algún interés para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-user-field-keywords', 'es', 'Indica hasta 5 palabras clave que te definan para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-user-field-linkedin', 'es', 'El campo linkedin no es valido');
INSERT INTO `text` VALUES('validate-user-field-location', 'es', 'El lugar de residencia del usuario no es valido');
INSERT INTO `text` VALUES('validate-user-field-name', 'es', 'Pon tu nombre completo para mejorar la puntuación');
INSERT INTO `text` VALUES('validate-user-field-twitter', 'es', 'El twitter del usuario no es valido');
INSERT INTO `text` VALUES('validate-user-field-webs', 'es', 'Pon tu página web para mejorar la puntuación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `about` text,
  `keywords` tinytext,
  `active` tinyint(1) NOT NULL,
  `avatar` int(11) DEFAULT NULL,
  `contribution` text,
  `twitter` tinytext,
  `facebook` tinytext,
  `google` tinytext,
  `identica` tinytext,
  `linkedin` tinytext,
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token` tinytext NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se ve publicamente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `user`
--

INSERT INTO `user` VALUES('aballesteros', 'Andrés Ballesteros', 'Cáceres', 'geopetro10@yahoo.es', '975b5f25e95d2afd7c22b50342ddffc5edba89a1', 'Abvmedioambiente', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('abenitez', 'Alba Benitez', 'Cáceres', 'albabenitez1983@gmail.com', 'ccb39ea899a2dbc60867cfec2495ffcbfb2eb42d', 'Autónoma-Sector cultural', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('aceballos', 'Ana Ceballos', 'Madrid', 'veyota79@hotmail.com', 'db5c3952834c9f1a64f4086e94afa40013dd3a5b', 'Gestor Cultural', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('acomunes', 'Asociación comunes', 'Madrid', 'comunes@ourproject.org', '4f9a95a11c313960c234b429e4822bbcc6725fcc', '', '', 1, 0, '', 'movecommons', '', '', '', '', 0, '0000-00-00 00:00:00', '2011-08-01 14:30:47', '9886c568ed0a405d16b93c1a989df1d3¬comunes@ourproject.org', 0);
INSERT INTO `user` VALUES('afernandez', 'Ana Fdez Osorio', 'Barcelona', 'ana@dispuesta.net', 'bc4b56d5b2fb0ddb27e4ba97ed8483c2b96b9b43', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('afolguera', 'Antonia Folguera', 'Barcelona', 'antonia@riereta.net', '232083fdb8d80ac58b79ab55b75d302e54b5038b', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:57:13', '', 0);
INSERT INTO `user` VALUES('agallastegi', 'Asier Gallastegi', 'Bilbao', 'asier.gallastegi@gmail.com', 'ddbc0e3ef9225b50a936adc43e833f238b13680d', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('ahernandez', 'Alejandro Hernández Renner', 'Cáceres', 'ahernandez@lossantos.org', '79dd1d71cb13c723ac077e636c47d14720498b4c', 'Director Fundación Maimona', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('airiarte', 'Arantza Iriarte', 'Bilbao', 'airiarte@landspain.com', '7d2da1a0e004ba1f56454288d22efe27f121de6c', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('amartinez', 'Alfonso Martínez', 'Barcelona', 'martinezrubioo@gmail.com', 'b9ee4c65c2de3f007eef8e0cd9e39d2ef49aa17b', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('amorales', 'Ana Morales', 'Madrid', 'moralespartida@gmail.com', 'e8396d7fc730a83111aa251a80d44e4295d15d85', 'Trabajo en temas culturales', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('amunoz', 'Ascensión Muñoz Benítez', 'Cáceres', 'chonmube@gmail.com', '18542a1914f496a4c66739483a45620edfc40a65', 'Autónoma-Gestora cultural', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-07-05 01:54:33', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('aollero', 'Ana  Ollero', 'Cáceres', 'programaskreativos@gmail.com', 'a1d29ffc56898468565d89a267fa13b40d396278', 'Programas creativos', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('aramos', 'Avelino Ramos Casado', 'Cáceres', 'crdelcorral@hotmail.com', '54a53a49e8c51e869cdc0f8f5513d48ec8e3e149', 'Sertumon', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('arecio', 'Anto Recio', 'Cáceres', 'anto@filosomatika.net', '31d6347ed7f1809654680fb6f1bb5bed2ab07408', 'Fundecyt', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('asanz', 'Ana Sánz Grados', 'Cáceres', 'asanzgr@hotmail.com', 'a32009cc0d01b5a96aced6cff6cf4be61e8fbd7c', 'Asociación de Mujeres Malvaluna', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('avigara', 'Ana Vigara', 'Madrid', 'ana.vigara@iniciativajoven.org', 'b093c07f7a1c81bb216456e7cceb0520a632437f', 'Técnico de proyectos', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('blozano', 'Betania Lozano', 'Madrid', 'betanialozano@yahoo.com', '5ddcbe4a9c36ba3bd05ea0eb8d4f09ed422f5139', 'Gestora Cultura. Co-directora de la Muestra IN-SONORA', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('bramos', 'Beatriz Ramos', 'Madrid', 'beatriz@iniciativajovn.org', '7e6d5e13cc911119538fd2a00e5900e37df9a711', 'Técnico de proyectos', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('bsampayo', 'Blanca Sampayo', 'Madrid', 'blancasampayo@gmail.com', 'bf3d17a170f915f2f0df09f33ecdb8ec4883648d', 'Estoy haciendo un Master de Gestión Cultural, y un curso de Innovación Abierta en Gestión Cultural,', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('caagon', 'Carla A. Agon', 'Barcelona', 'carla.a.agon@gmail.com', '9fa69b1368644376888dd1d0b3e68db230f80c1d', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('carlaboserman', 'Carla Boserman', 'Sevilla', 'carlaboserman@gmail.com', 'df9b92ece4cebf81da2789d7a55d949fe1bb5a69', '', '', 1, 0, '', '', '', NULL, '', '', 1, '0000-00-00 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('ccarrera', 'Candela Carrera', 'Barcelona', 'candela.carrera@gmail.com', 'dd7d71c995d705193cf659f5019eca491f7ad8ef', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:57:13', '', 0);
INSERT INTO `user` VALUES('ccriado', 'Carlos Criado', 'Cáceres', 'carlos@carloscriado.es', 'dfce3f8d5e9722b20d20fe9fca259927d37e6856', 'FotoExtremadura', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('cmartinez', 'Cayetana Martinez', 'Madrid', 'cayetana109@gmail.com', '21470b6c5d5fd7c86af61904cfa25095184b0693', 'gestión cultural. Trabajo con varias asociaciones culturales y de emprendedores y querría conocer más de estas nuevas formas de financiación y organización.', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('cphernandez', 'Claudia Patricia Hernández', 'Madrid', 'patriciavergara83@hotmail.com', '556be5beee418fda6e1601867ef3fd3395b80f7f', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('cpinero', 'Carlos Piñero Medina', 'Cáceres', 'innovacion@energiaextremadura.org', '2a22c481b606f2758f06762925c06e6cf4b5ca6e', 'Cluster de la Energía de Extremadura. Dpto. de I+D+i y formación. ', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-07-05 01:54:45', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('criera', 'Cristina Riera ', 'Barcelona', 'criera@transit.es', 'a37535b1bb821dbc5495ddd55ddd04b9b4f485ed', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('dcabo', 'David Cabo', 'Madrid', 'david.cabo@gmail.com', '2e0b97b278aaadfdda6c1c5963212f28c9e59796', 'Ayudando a ONG Pro Bono Publico', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('dcuartielles', 'David Cuartielles', 'Malmö', 'dcuartielles@gmail.com', 'bbd0e343e48cdc7dcfc5515641e9c6b32e4e03af', '', '', 1, 0, '', '', '', NULL, '', '', 0, '0000-00-00 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('demo', 'Demo Goteo', 'Barcelona', 'mansalva@gmail.com', 'fde58f16e55f6e2afa8994488d16e9417ab642e1', 'Usuario dummie de pruebas y ejemplos para Goteo', 'crowdfunding, cultura, arte, hacks', 1, 105, 'Ejemplos y demos de contenidos en general', 'http://twitter.com/goteofunding', '', '', 'http://identi.ca/goteofunding', '', 0, '2011-07-25 15:47:10', '2011-07-26 20:13:44', '9a828863d7e88aee80a25b2ad52d3428', 0);
INSERT INTO `user` VALUES('diegobus', 'diegobus', 'Barcelona', 'diegobus@pelousse.com', '4de99ca64d7a4003ac8724323da63e20b413db15', 'test. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla dolor quis elit cursus sit amet tincidunt elit ultrices. Mauris ultricies auctor velit vel tempor. Integer sollicitudin consequat ultrices. Fusce at ante sit amet augue dapibus mattis.', 'Política, diseño', 1, 104, 'Mauris fringilla dolor quis elit cursus sit amet tincidunt elit ultrices. Mauris ultricies auctor velit vel tempor. Integer sollicitudin consequat ultrices. Fusce at ante sit amet augue dapibus mattis.', 'http://twitter.com/#!/dinoupublicacio', 'http://facebook.com', '', 'http://identi.ca', '', 0, '2011-05-10 18:32:15', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('domenico', 'Domenico Di Siena', 'Madrid', 'domenico@ecosistemaurbano.com', 'af915aa406c73c1e7a22ace8e7417ce02e222679', '', '', 1, 0, '', 'urbsocialdesign', '', '', '', '', 0, '0000-00-00 00:00:00', '2011-07-25 14:47:02', '', 0);
INSERT INTO `user` VALUES('ebai', 'Eva Bai', 'Bilbao', 'eva.alija@gmail.com', '7bf8881c975d50142a8f44768fda76101f86aab5', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('ebaraonap', 'Ethel Baraona Pohl', 'Barcelona', 'tusojos8@yahoo.com', '4483c14c633775292d0b9271cdec409a61387788', '', '', 1, 0, '', 'archinhand', '', '', '', '', 0, '0000-00-00 00:00:00', '2011-07-13 12:31:08', '', 0);
INSERT INTO `user` VALUES('ebarrenetxea', 'Eukene Barrenetxea', 'Bilbao', 'eukenebarrenetxea@gmail.com', '92afc493fbd0f74e207bf2fd60636f33edd11db3', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('efoglia', 'Efraín Foglia', 'Barcelona', 'mexmafia@gmail.com', '9f1b78537a645a62e8404b774b0b69b8529e90c6', '', '', 1, 0, '', 'EfrainFoglia', '', NULL, '', '', 0, '0000-00-00 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('elopez', 'Elvira López', 'Madrid', 'elvirilay@hotmail.com', '1d1d375939c74c8f755355f731c9eed3b4ee4297', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('emartinena', 'Esteban Martinena', 'Cáceres', 'orensbruli@gmail.com', 'f0175a18e42135b86232c00ab1db27acb6b224be', 'Fotógrafo Agencia EFE', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('emonivas', 'Esther Moñivas', 'Madrid', 'esther.monivas@gmail.com', '516d9ae4faac6f1425ef5fb0327d6bbb7948ea0b', 'Codirectora de la ONG Acción C', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-07-05 01:55:04', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('emuruzabal', 'Eneko Muruzabal', 'Bilbao', 'info@diseinugile.com', 'ccce002e3264bcfaf344858defd99e661d1943d6', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('eportillo', 'Esperanza Portillo', 'Madrid', 'portillo.esperanza@gmail.com', '549777e061b8dbadd683ca5293444f4095beaabf', 'Estudiante de master en gestión cultural', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('esenabre', 'esenabre', 'Barcelona', 'esenabre@gmail.com', '60ffeee026521957cfd8585edf4b07e1541f640c', 'Livingg la vida loca', 'salud', 1, 27, 'Este campo va fuera', 'http://twitter.com/esenabre', 'http://www.facebook.com/esenabre', '', '', 'http://www.linkedin.com/in/esenabre', 1, '2011-06-15 11:33:12', '2011-07-18 11:40:36', '60109fe996530911ff113b9b1b55382f', 0);
INSERT INTO `user` VALUES('evandellos', 'Emma Vandellós ', 'Barcelona', 'emma.vandellos@esade.edu', 'f4ba05222b034f22339c7fb0ef299b925f02dcc8', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('fandres', 'Fernando Andres', 'Santurtzi', 'fandres@virgen-del-mar.com', '7c37d99852bcd2ccce0a976f94d8035e79a463d5', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('fbalague', 'Francesc Balagué', 'Barcelona', 'fbalague@gmail.com', 'b0f76f462dcfaadc135dec520456f03b558a80c8', 'Maestro y psicopedagogo, doctor en multimedia educativa y investigador en el uso de las TIC aplicadas a la educación. A partir de mi gran afición por viajar, también estoy muy interesado en el viaje como forma de aprendizaje. La fotografía siempre me acompaña.', 'educación, desarrollo, formación, web2.0, p2p, ', 1, 120, 'Tengo alguna experiencia en la gestión de proyectos, en coordinación y dinamización de grupos online y offline, y en uso de herramientas de la web social.\r\n\r\nPuedo ayudar a traducir, difundir, testear y enseñar...', 'http://twitter.com/fbalague', 'https://www.facebook.com/fbalague', 'https://profiles.google.com/u/0/109018548498585760210', 'http://identi.ca/fbalague', 'http://es.linkedin.com/in/francescbalague', 0, '2011-08-02 08:22:49', '2011-08-02 08:43:59', '6d7049579e73fcb03f0b6c4262d4af0e', 0);
INSERT INTO `user` VALUES('fcingolani', 'Francesco Cingolani', 'Madrid', 'fc@ecosistemaurbano.com', '2c6abab20f50218be59a8d2d62c100a9dc9d3a08', 'Arquitecto. Soy responsable de Urban Social Design Experience, un proyecto de networked learning promovido por la asociación Urban Social Design', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('fcoddou', 'Flavio Coddou', 'Barcelona', 'flaviocoddou@gmail.com', '3e61f6f52ca6136091ced69b745d79fd88e58f71', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('ffreitas', 'Flavia Freitas', 'Barcelona', 'flavia.frr@gmail.com', '023434d501234549299f47c90b37249b8544b7b5', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('fingrassia', 'Franco Ingrassia', 'Rosario', 'francoingrassia@gmail.com', '3e990f6eee34056154296953598b61d10d9c55f3', 'Psicólogo. Involucrado en el proceso de constitución de un Laboratorio del Procomún en la ciudad de Rosario (Argentina)', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('gbento', 'Gisele Bento', 'Barcelona', 'giselecultura@gmail.com', '1d0bee13a33f3ce2fcc7b8c83596c903c9d5a42d', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('gbezanilla', 'Gerardo Bezanilla', 'Madrid', 'gerardo@beusual.com', 'bda58288e44e1011c2cf94ebe4582662f9876497', 'Diseñador, Editor, Agente Cultural', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('gbossio', 'Gabriela Bossio', 'Madrid', 'gabrielabossio@gmail.com', '9e832ab9a645262404ca0e44b6616aca990b5fec', 'Directora Creativa y Gestora cultural de La casa del Árbol', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('geraldo', 'Gerald Kogler y Marti Sanchez', 'Barcelona', 'geraldo@servus.at', '409bca035255a1d86114e2f2e74476375fdb11f4', '', '', 1, 0, '', '', '', '', '', '', 0, '0000-00-00 00:00:00', '2011-07-31 13:38:17', '', 0);
INSERT INTO `user` VALUES('gnarros', 'Germán Narros Lluch', 'Cáceres', 'german@caceresentumano.com', '5a6eb69bae6b03a73d6956552e21ea0ce515e878', 'caceresentumano.com', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('goteo', 'Goteo', 'Barcelona', 'goteo@doukeshi.org', 'f64dd0d8c9276d87c6d0ae24c5d12571c62ecf16', 'Este perfil representa al equipo que administra la plataforma Goteo, desde el cual nos comunicamos con todos los usarios.', '', 1, 118, '', 'http://twitter.com/#!/goteofunding', '', '', 'http://identi.ca/goteofunding', '', 1, '2011-05-20 01:44:15', '2011-08-01 19:51:55', '8eefe6e871cebe24248cc47d4b23d152goteo@doukeshi.org', 0);
INSERT INTO `user` VALUES('gpedranti', 'Gabriela Pedranti', 'Barcelona', 'info@gabrielapedranti.com', '1d2a5c9df4290c12a5f5962e3d9fda38c85c072d', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('hcastro', 'Helder Castro', 'Bilbao', 'helder_r_castro@hotmail.com', 'e10a36f30ce2a39a2e1998f9317ae06362a634c5', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('ibartolome', 'Iñaki Bartolomé', 'Bilbao', 'ibartolome@ideable.net', '6940fc52c08a9e4695cd2c929cca05eefdd69c99', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('ibelloso', 'Isabel Belloso Bueno', 'Cáceres', 'ibellosobueso@yahoo.es', '00aff3cc9ed53bd857a28d4f0e209e5fac70ca5d', 'Fundación Cáceres Capital', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('infoq', 'info-q', 'Bilbao', 'info@info-q.com', 'dddbd151b3e56407b313757a21dbf63b8559dc30', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('iromero', 'Imma Romero', 'Barcelona', 'ima_gina7@hotmail.com', 'ff5e6b74613597bfeaa59b91f9accc1d238cfefb', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('itxaso', 'Úbiqa, tecnología, ideas y comunicación', 'Bilbao', 'itxaso@ubiqa.com', '82d3d0eaff77053c18a71ff4725dd9ffc712cce3', '', '', 1, 0, '', 'ubiqarama', '', '', '', '', 0, '0000-00-00 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('ivan', 'Ivan Vergés', 'la garriga, barcelona', 'ivan@microstudi.net', '052f55137ef0403ccb0a041b86c5f62ed6fc03db', '', 'music, teatre, circ, open source technologies', 1, 117, '', 'http://twitter.com/ivanverges', 'http://facebook.com/ivanverges', 'https://plus.google.com/105872229634982547278', 'http://identi.ca/ivanverges', '', 0, '2011-07-31 11:21:10', '2011-08-01 14:30:47', '5e681eadece1fe6c6da4b77d3eea43de', 0);
INSERT INTO `user` VALUES('jcanaves', 'jcanaves', '', 'jcanaves@doukeshi.org', '7c4a8d09ca3762af61e59520943dc26494f8941b', '', '', 1, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2011-07-26 19:31:51', '2011-08-01 14:30:47', '2f5224d8d03f0efa9a10b6c80564d3be', 0);
INSERT INTO `user` VALUES('jclindo', 'Juan Carlos Lindo Sanguino', 'Cáceres', 'juancarlos@identic.es', '1629be88c941712f3b027b1bccdae4dd338ea852', 'Consorcio Identic', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('jcosta', 'Joaquin Costa', 'Bilbao', 'jcosta@eohonline.es', 'ccf5b869a0e1fd1b8f34c095cd7fe210bab8036a', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('jfernandez', 'Jesús Fenández Perianes', 'Cáceres', 'interinofernandez@hotmail.com', 'e36b93e47ad4a17dbe34fa2a8a53e7d57a44670d', 'COlectivo PEriferias ', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('jlespina', 'José Luis Espina', 'Barcelona', 'espinajl@gmail.com', '7bf9458b0c0549d310203f27c34f441ac074eb5a', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('jmatadero', 'Javi de Matadero', 'Madrid', 'javi@mataderomadrid.org', 'cb218e4b9fbbb89ec5804ec32ca1067e3679b311', 'Matadero', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('jmorer', 'Julia  Morer ', 'Madrid', 'julia.morer@gmail.com', '7b63000191f052ebb77324fe9940af29e9ad1765', 'Edición y Diseño de Proyectos Culturales', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('jmorquecho', 'Jonatan Morquecho', 'Bilbao', 'jmorquecho@gmail.com', 'bcccd101cfae58fb3f4937f1cc1dd5c5f71db5eb', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('jnora', 'Julián Nora', 'Madrid', 'nora_julian@hotmail.com', '2eda3d6278dc18275918ea9ef18c0bdfe1c22a47', 'Ing. Simulacion ', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('jromero', 'Jessica Romero', 'Madrid', 'jessicaromero@gmail.com', 'e3d79a6764ec0796ff75f8b5c9184cc7bac0d37a', 'Periodista y productora cultural', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('kaime', 'kaime', '', 'kaime@kaime.info', 'e04820372e7f2ebb2d76987433579219b11c2ba5', '', '', 1, 100, '', '', '', '', '', '', 0, '2011-07-20 15:00:26', '2011-07-20 19:07:17', '888fe62c210ad795dcf0da748b326e42', 0);
INSERT INTO `user` VALUES('kventura', 'Kenia Ventura', 'Barcelona', 'dinkha@hotmail.com', '4e79b02993b50bddf2709ee5e764dfddc1c7661b', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('lamorrortu', 'Lander Amorrortu', 'Bilbao', 'lander.amorrortu@agla4D.com', '51dbe34a01e87ee7ec986c07b7ad315893f95a56', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('lcarretero', 'Lucía Carretero', 'Barcelona', 'foto@luciacarretero.com', '29afdee54be1bbdf89242d5eb3cacf0c11b680f3', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('lemontero', 'Luis Ernesto Montero', 'Cáceres', ' lernestomc@Yahoo.es', 'b0e4f357e65a33966d2089b1d899f4245f05ad25', 'Artista', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('lfernandez', 'Laura Fernández', 'Madrid', 'laura@medialab-prado.es', '6823d091412f0850015e9a0e4983b48063e7a813', 'Responsable de programa cultural en Medialab-Prado', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('lstalling', 'Lars Stalling', 'Barcelona', 'larsst@gmail.com ', 'dbe4f8a3757235145f8ed0fde23e433fe5650872', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('maaban', 'Manuel Ángel Abán', 'Madrid', 'manuel.aban@gmail.com', 'd068342a85c48d2f95ea7661b2b0a5e77c56dc5c', 'Codirector de una ONG ', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mamanovell', 'M. Ángel Manovell', 'Bilbao', 'info@dinamik-ideas.com', '6a151226b509f2fa7c3207ee17ee21f41bec0198', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mcidriain', 'Monika Cidriain', 'San Sebastián', 'cidriain@yahoo.es', '934a3519f310edf49a5dfb10a752a72aa8e0600e', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mduran', 'Magdalena Duran', 'Barcelona', 'magdaduran@yahoo.es', '5d5d2e7dfde93f13cc12abdacc10d7f2083f1c39', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('mgarcia', 'Miriam García Sanz', 'Madrid', 'miriamgsanz@gmail.com', 'b982ca393b19498409bc749abefc585e14dc149f', 'Gestora cultural', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mgoikolea', 'Marta Goikolea', 'Bilbao', 'mgoikolea@gmail.com', 'e4f15436b31af59798f0e0fe6eafa544e8f4f575', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('mgoni', 'Marta Goñi', 'Bilbao', 'caoroneltapi@yahoo.es', 'c3bf14f58a8005742e7ef31c0549a6f8fce3f154', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-07-05 01:55:30', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mkekejian', 'Maral Kekejian ', 'Madrid', 'mkekejih@cajamadrid.es', '6bab1571a22fa60c8022a6dfcf397f97b0743279', 'Artes Escénicas LCE', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mmendez', 'Miguel Méndez Pérez', 'Cáceres', 'elmiguemende@gmail.com ', '127d53ca624ad91c26a7ffa17aff20bdb644e235', 'Técnico AUPEX+gestor cultural+animador 2.0', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mmikirdistan', 'Maral Mikirdistan', 'Barcelona', 'idensitat@idensitat.org', 'c63a6cb1c60e71b3a9f0d90a02ee392ff1a779ce', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('mpalma', 'Marcela Palma Barrera', 'Cáceres', 'marcela.palma@fundacionciudadania.es', 'c6f3a28e89b2b5f6e6f191eb533676f6cea595bf', 'Fundación Ciudadanía', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mpedro', 'Matxalen de Pedro', 'Bilbao', 'matxalendplarrea@hotmail.com', '2b83df476cffcf3f7a1085cba5cc54c3d2a136b8', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('mpedroche', 'Mercedes Pedroche', 'Madrid', 'info@mercedespedroche.com', '570d6dc480ffbe1388e1bedce96e5a0e57e502b8', 'Coreógrafa, bailarina', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mramirez', 'Miguel Ramírez', 'Barcelona', 'miquel.ramirez@gmail.com', 'f223dbfb37ae5507cdb7ced8d338e05a686bf25a', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('mraposo', 'mikelraposo', 'Bilbao', 'mikelraposo@gmail.com', 'ac65d55fbb459e454d3e4c623f3d85b6ed111484', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('msoms', 'Miriam Soms Trillo', 'Cáceres', 'msoms@lacajanegra.net', 'bae498009ce55c4bed9b1111748a53e97ad5a8ae', 'La Caja Negra', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('nescala', 'Nella Escala', 'Barcelona', 'nella.escala@gmail.com', '4ee7c6e03aaa371e7f3ee8dcaedd0d1f23a374da', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('olivier', 'Olivier', 'Palma de Mallorca', 'olivierschulbaum@platoniq.net', 'f8c021907c74267ee80964d52ae181577fd095f2', 'Cofundador de Platoniq, organización catalana de innovadores sociales y desarrolladores de software', 'crowdfunding, copyleft, educación, innovación_social', 1, 119, '', 'platoniq', 'http://www.facebook.com/olivier.schulbaum', 'https://plus.google.com/116589329737135075630/', 'http://identi.ca/platoniq', 'http://www.linkedin.com/profile/view?id=98955103&locale=es_ES&trk=tab_pro', 4, '2011-06-01 11:19:29', '2011-08-01 20:27:11', 'bde8aad344ca0a7b6558a7b35b229f90', 0);
INSERT INTO `user` VALUES('pereztoril', 'Javier Pérez-Toril Galán', 'Cáceres', 'pereztoril@gmail.com', '4fca9db1555c0fcec59343a2ae8c60055510cdae', 'Empresa Jptsolutions', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('pgonzalo', 'Pilar Gonzalo', 'Madrid', 'pilar.gonzalo@fulbrightmail.org', '2675dc74abf73a42520c10bcfad2cdca66a6f1c0', 'Museo Reina Sofía', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('rcasado', 'Raul Casado', 'Barcelona', 'raul.casadogonzalez@gmail.com', '060d5b91ab484ba5bbd937c3cf23bd864ccba3d0', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('rfernandez', 'Rosa Fernandez', 'Bilbao', 'info@colaborabora.org', '2a35b80f14ace4402da74188d325ecacb1f19496', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('ricardo-amaste', 'ricardo_amaste', '', 'ricardo@amaste.com', '2a79a112f89107cd6ff1eb81695e6ff88a3fe368', '', '', 1, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2011-07-26 20:04:00', '2011-08-01 14:30:47', '46c49eb03871ba254b6ee3f04e9049c7', 0);
INSERT INTO `user` VALUES('root', 'Super administrador', '', 'julian_1302552287_per@gmail.com', 'f64dd0d8c9276d87c6d0ae24c5d12571c62ecf16', '', '', 1, 91, '', '', '', '', '', '', 3, '2011-07-20 20:40:14', '2011-07-20 20:40:14', '61aa85ea9169c68babfa5b8bdb44097bjulian_1302552287_per@gmail.com', 0);
INSERT INTO `user` VALUES('rparramon', 'Ramon Parramon', 'Barcelona', 'rparramon@acvic.org', '98de9c0d4367d83c6b18bac6e0010972261aa094', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('rsalas', 'Roberto Salas', 'Madrid', 'robers_alas@yahoo.es', '86a131eb71ceceddb26bc889895cf24ef96602e1', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:57:13', '', 0);
INSERT INTO `user` VALUES('rsteckelbach', 'Roswitha Steckelbach', 'Bilbao', 'roswira@yahoo.es', '18e4cfb2ef66c87404fee367d4126a72c653db23', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('rtorres', 'Ricardo Torres', 'Bilbao', 'ricardotorres2@telefonica.net', '55f237586f9bef5fa246fafe308ebdecf7aac227', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('sgrueso', 'Stéphane Grueso', 'Madrid', 'stephanegrueso@gmail.com', '2364b268a16831f859c5d17b7eda329fd39700a9', 'Cineasta documental', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('smeschede', 'Sören Meschede', 'Madrid', 'soren@hablarenarte.com', '0b63ad3f4d744a320cf437f1ffecb4c24a74f74f', 'Gestor Cultural', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('snogales', 'Silverio Nogales Pajuelo', 'Cáceres', 'sinogales@gmail.com', 'fa72c318a30d73163c9f63a240577ed0fb9afa7e', 'Gerente Ciudad de la Salud', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('soritxori', 'Soraia', 'Bilbao', 'soritxori@hotmail.com', '75042acd59a7e20f0c2b6213a5f86aaccce992f3', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);
INSERT INTO `user` VALUES('stena', 'Sara Tena Medina', 'Cáceres', 'sara.tena@aupex.org', 'e0be7b9f868042ae4e89136b9a84996597ba5b44', 'Asociación de Universidades Populares de Extremadura', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('susana', 'Susana', '', 'sux@platoniq.net', '1dc93474b69c5a0493be3a4f67c6551c1cb99d2d', '', '', 1, NULL, '', NULL, NULL, NULL, NULL, NULL, 0, '2011-07-15 16:02:08', '2011-08-01 14:30:47', 'a99f8a7e5095f0227d26d569fbf962a3', 0);
INSERT INTO `user` VALUES('tbadia', 'Tere Badia', 'Barcelona', 'tbadtod@gmail.com', '770b0c2422f238ff6662d2411900c6e57f130e22', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('tguido', 'Tomás Guido', 'Madrid', 'tguido@transit.es', '325dce990aec945b7d843814911ae7811670dbee', 'Coordino la oficina en Madrid la empresa Trànsit Projectes. www.transit.es', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('tintangibles', 'Taller d''intangibles', 'Barcelona', 'dvd@enlloc.org', '43d79ce4a41095a5adf6d315a989ee8a349c168b', '', '', 1, 0, '', 'HKpWiki', '', '', '', '', 0, '0000-00-00 00:00:00', '2011-07-14 16:39:10', '', 0);
INSERT INTO `user` VALUES('todojunto', 'Todojunto', 'Barcelona', 'hola@todojunto.net', '9da21b8b0a1ef24359212199b4335534a805acb7', '', '', 1, 0, '', '', '', '', '', '', 0, '0000-00-00 00:00:00', '2011-07-20 16:10:30', '', 0);
INSERT INTO `user` VALUES('vsantiago', 'Victor Santiago', 'Cáceres', 'vstabares@gmail.com', '62ff5323c5bb06d521efb2f1f185389728d715a2', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('vtorre', 'Víctor Torre', 'Madrid', 'victortorrevaquero@yahoo.es', '09723c5bf0daf4c5c5b127ba269edc841327238c', 'Coordinador Teatro Sol y Tierra', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-08-01 14:30:47', '', 0);
INSERT INTO `user` VALUES('yriquelme', 'Yolanda Riquelme ', 'Madrid', 'yolandariquel@hotmail.com', 'add40e3392132f436c194831fa81de1dd3b62962', '', '', 1, 0, '', '', '', NULL, '', '', 1, '2011-05-04 00:00:00', '2011-07-15 17:06:10', '', 0);
INSERT INTO `user` VALUES('zaramari', 'Zaramari (Gorka, Maria)', 'Bilbao', 'info@zaramari.com', 'd21582038b6f6288e2f089b21c93e550dc038bd1', '', '', 1, 0, '', '', '', NULL, '', '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_image`
--

DROP TABLE IF EXISTS `user_image`;
CREATE TABLE `user_image` (
  `user` varchar(50) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `user_image`
--

INSERT INTO `user_image` VALUES('demo', 105);
INSERT INTO `user_image` VALUES('diegobus', 8);
INSERT INTO `user_image` VALUES('diegobus', 104);
INSERT INTO `user_image` VALUES('esenabre', 27);
INSERT INTO `user_image` VALUES('fbalague', 120);
INSERT INTO `user_image` VALUES('goteo', 69);
INSERT INTO `user_image` VALUES('goteo', 118);
INSERT INTO `user_image` VALUES('ivan', 117);
INSERT INTO `user_image` VALUES('kaime', 98);
INSERT INTO `user_image` VALUES('kaime', 99);
INSERT INTO `user_image` VALUES('kaime', 100);
INSERT INTO `user_image` VALUES('olivier', 70);
INSERT INTO `user_image` VALUES('olivier', 95);
INSERT INTO `user_image` VALUES('olivier', 96);
INSERT INTO `user_image` VALUES('olivier', 119);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_interest`
--

DROP TABLE IF EXISTS `user_interest`;
CREATE TABLE `user_interest` (
  `user` varchar(50) NOT NULL,
  `interest` int(12) NOT NULL,
  UNIQUE KEY `user_interest` (`user`,`interest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios';

--
-- Volcar la base de datos para la tabla `user_interest`
--

INSERT INTO `user_interest` VALUES('demo', 2);
INSERT INTO `user_interest` VALUES('demo', 6);
INSERT INTO `user_interest` VALUES('demo', 7);
INSERT INTO `user_interest` VALUES('diegobus', 6);
INSERT INTO `user_interest` VALUES('diegobus', 14);
INSERT INTO `user_interest` VALUES('esenabre', 7);
INSERT INTO `user_interest` VALUES('fbalague', 2);
INSERT INTO `user_interest` VALUES('fbalague', 6);
INSERT INTO `user_interest` VALUES('fbalague', 7);
INSERT INTO `user_interest` VALUES('fbalague', 10);
INSERT INTO `user_interest` VALUES('ivan', 2);
INSERT INTO `user_interest` VALUES('ivan', 7);
INSERT INTO `user_interest` VALUES('ivan', 11);
INSERT INTO `user_interest` VALUES('ivan', 13);
INSERT INTO `user_interest` VALUES('ivan', 14);
INSERT INTO `user_interest` VALUES('olivier', 2);
INSERT INTO `user_interest` VALUES('olivier', 9);
INSERT INTO `user_interest` VALUES('olivier', 10);
INSERT INTO `user_interest` VALUES('olivier', 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_personal`
--

DROP TABLE IF EXISTS `user_personal`;
CREATE TABLE `user_personal` (
  `user` varchar(50) NOT NULL,
  `contract_name` varchar(255) DEFAULT NULL,
  `contract_surname` varchar(255) DEFAULT NULL,
  `contract_nif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `contract_email` varchar(256) DEFAULT NULL,
  `phone` varchar(9) DEFAULT NULL COMMENT 'guardar sin espacios ni puntos',
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos personales de usuario';

--
-- Volcar la base de datos para la tabla `user_personal`
--

INSERT INTO `user_personal` VALUES('acomunes', '', '', '', NULL, '', '', '', 'Madrid', 'España');
INSERT INTO `user_personal` VALUES('dcuartielles', '', '', '', NULL, '', '', '', 'Malm�', 'Suecia');
INSERT INTO `user_personal` VALUES('demo', 'Demo Goteo', '', '46649545W', NULL, '936924182', 'Moscou 16, 1º 1ª', '08005', 'Barcelona', 'España');
INSERT INTO `user_personal` VALUES('diegobus', 'diego', NULL, 'x8562415k', NULL, '658125454', 'c/ calle 98, 1º 2º', '08000', 'Barcelona', 'España');
INSERT INTO `user_personal` VALUES('domenico', '', '', '', NULL, '', '', '', 'Madrid', 'España');
INSERT INTO `user_personal` VALUES('ebaraonap', '', '', '', NULL, '', '', '', 'Barcelona', 'España');
INSERT INTO `user_personal` VALUES('efoglia', '', '', '', NULL, '', '', '', 'Barcelona', 'España');
INSERT INTO `user_personal` VALUES('esenabre', 'Enric Senabre Hidalgo', '', '46649545W', NULL, '932215515', 'Moscou 16, 1º 1ª', '08005', 'Barcelona', 'España');
INSERT INTO `user_personal` VALUES('fbalague', 'Francesc Balagué', NULL, '46784299E', NULL, '655210167', 'Aribau, 64 entl. 1a', '08011', 'Barcelona', 'España');
INSERT INTO `user_personal` VALUES('geraldo', '', '', '', NULL, '', '', '', 'Barcelona', 'España');
INSERT INTO `user_personal` VALUES('goteo', 'Susana Noguero', '', 'G63306914', NULL, '654321987', 'C/ Montealegre, 5', '8001', 'Barcelona', 'España');
INSERT INTO `user_personal` VALUES('itxaso', '', '', '', NULL, '', '', '', 'Bilbao', 'España');
INSERT INTO `user_personal` VALUES('ivan', 'Ivan Vergés', '', '', NULL, '', '', '', 'la garriga, barcelona', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('kaime', 'Jaume Alemany', '', '43103776M', NULL, '666666666', 'Aquí', '07141', 'Marratxí (Pueblo)', 'España');
INSERT INTO `user_personal` VALUES('olivier', 'Olivier Schulbaum', '', '', NULL, '667031530', '', '', 'Palma de Mallorca', 'España');
INSERT INTO `user_personal` VALUES('root', 'Super administrador', '', '', NULL, '', '', '', '', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('tintangibles', '', '', '', NULL, '', '', '', 'Barcelona', 'España');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_review`
--

DROP TABLE IF EXISTS `user_review`;
CREATE TABLE `user_review` (
  `user` varchar(50) NOT NULL,
  `review` bigint(20) unsigned NOT NULL,
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la revision',
  PRIMARY KEY (`user`,`review`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de revision a usuario';

--
-- Volcar la base de datos para la tabla `user_review`
--

INSERT INTO `user_review` VALUES('esenabre', 4, 0);
INSERT INTO `user_review` VALUES('olivier', 4, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `user_id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `node_id` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `role_FK` (`role_id`),
  KEY `node_FK` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `user_role`
--

INSERT INTO `user_role` VALUES('esenabre', 'checker', '');
INSERT INTO `user_role` VALUES('esenabre', 'translator', '');
INSERT INTO `user_role` VALUES('olivier', 'checker', '');
INSERT INTO `user_role` VALUES('aballesteros', 'user', '*');
INSERT INTO `user_role` VALUES('abenitez', 'user', '*');
INSERT INTO `user_role` VALUES('aceballos', 'user', '*');
INSERT INTO `user_role` VALUES('acomunes', 'user', '*');
INSERT INTO `user_role` VALUES('afernandez', 'user', '*');
INSERT INTO `user_role` VALUES('afolguera', 'user', '*');
INSERT INTO `user_role` VALUES('agallastegi', 'user', '*');
INSERT INTO `user_role` VALUES('ahernandez', 'user', '*');
INSERT INTO `user_role` VALUES('airiarte', 'user', '*');
INSERT INTO `user_role` VALUES('amartinez', 'user', '*');
INSERT INTO `user_role` VALUES('amorales', 'user', '*');
INSERT INTO `user_role` VALUES('amuñoz', 'user', '*');
INSERT INTO `user_role` VALUES('aollero', 'user', '*');
INSERT INTO `user_role` VALUES('aramos', 'user', '*');
INSERT INTO `user_role` VALUES('arecio', 'user', '*');
INSERT INTO `user_role` VALUES('asanz', 'user', '*');
INSERT INTO `user_role` VALUES('avigara', 'user', '*');
INSERT INTO `user_role` VALUES('blozano', 'user', '*');
INSERT INTO `user_role` VALUES('bramos', 'user', '*');
INSERT INTO `user_role` VALUES('bsampayo', 'user', '*');
INSERT INTO `user_role` VALUES('caagon', 'user', '*');
INSERT INTO `user_role` VALUES('carlaboserman', 'user', '*');
INSERT INTO `user_role` VALUES('ccarrera', 'user', '*');
INSERT INTO `user_role` VALUES('ccriado', 'user', '*');
INSERT INTO `user_role` VALUES('cmartinez', 'user', '*');
INSERT INTO `user_role` VALUES('cphernandez', 'user', '*');
INSERT INTO `user_role` VALUES('cpiñero', 'user', '*');
INSERT INTO `user_role` VALUES('criera', 'user', '*');
INSERT INTO `user_role` VALUES('dcabo', 'user', '*');
INSERT INTO `user_role` VALUES('dcuartielles', 'user', '*');
INSERT INTO `user_role` VALUES('demo', 'user', '*');
INSERT INTO `user_role` VALUES('diegobus', 'user', '*');
INSERT INTO `user_role` VALUES('domenico', 'user', '*');
INSERT INTO `user_role` VALUES('ebai', 'user', '*');
INSERT INTO `user_role` VALUES('ebaraonap', 'user', '*');
INSERT INTO `user_role` VALUES('ebarrenetxea', 'user', '*');
INSERT INTO `user_role` VALUES('efoglia', 'user', '*');
INSERT INTO `user_role` VALUES('elopez', 'user', '*');
INSERT INTO `user_role` VALUES('emartinena', 'user', '*');
INSERT INTO `user_role` VALUES('emoñivas', 'user', '*');
INSERT INTO `user_role` VALUES('emuruzabal', 'user', '*');
INSERT INTO `user_role` VALUES('eportillo', 'user', '*');
INSERT INTO `user_role` VALUES('esenabre', 'user', '*');
INSERT INTO `user_role` VALUES('evandellos', 'user', '*');
INSERT INTO `user_role` VALUES('fandres', 'user', '*');
INSERT INTO `user_role` VALUES('fbalague', 'user', '*');
INSERT INTO `user_role` VALUES('fcingolani', 'user', '*');
INSERT INTO `user_role` VALUES('fcoddou', 'user', '*');
INSERT INTO `user_role` VALUES('ffreitas', 'user', '*');
INSERT INTO `user_role` VALUES('fingrassia', 'user', '*');
INSERT INTO `user_role` VALUES('gbento', 'user', '*');
INSERT INTO `user_role` VALUES('gbezanilla', 'user', '*');
INSERT INTO `user_role` VALUES('gbossio', 'user', '*');
INSERT INTO `user_role` VALUES('geraldo', 'user', '*');
INSERT INTO `user_role` VALUES('gnarros', 'user', '*');
INSERT INTO `user_role` VALUES('goteo', 'checker', '*');
INSERT INTO `user_role` VALUES('goteo', 'superadmin', '*');
INSERT INTO `user_role` VALUES('goteo', 'translator', '*');
INSERT INTO `user_role` VALUES('goteo', 'user', '*');
INSERT INTO `user_role` VALUES('gpedranti', 'user', '*');
INSERT INTO `user_role` VALUES('hcastro', 'user', '*');
INSERT INTO `user_role` VALUES('ibartolome', 'user', '*');
INSERT INTO `user_role` VALUES('ibelloso', 'user', '*');
INSERT INTO `user_role` VALUES('infoq', 'user', '*');
INSERT INTO `user_role` VALUES('iromero', 'user', '*');
INSERT INTO `user_role` VALUES('itxaso', 'user', '*');
INSERT INTO `user_role` VALUES('ivan', 'user', '*');
INSERT INTO `user_role` VALUES('jcanaves', 'user', '*');
INSERT INTO `user_role` VALUES('jclindo', 'user', '*');
INSERT INTO `user_role` VALUES('jcosta', 'user', '*');
INSERT INTO `user_role` VALUES('jfernandez', 'user', '*');
INSERT INTO `user_role` VALUES('jlespina', 'user', '*');
INSERT INTO `user_role` VALUES('jmatadero', 'user', '*');
INSERT INTO `user_role` VALUES('jmorer', 'user', '*');
INSERT INTO `user_role` VALUES('jmorquecho', 'user', '*');
INSERT INTO `user_role` VALUES('jnora', 'user', '*');
INSERT INTO `user_role` VALUES('jromero', 'user', '*');
INSERT INTO `user_role` VALUES('kaime', 'user', '*');
INSERT INTO `user_role` VALUES('kventura', 'user', '*');
INSERT INTO `user_role` VALUES('lamorrortu', 'user', '*');
INSERT INTO `user_role` VALUES('lcarretero', 'user', '*');
INSERT INTO `user_role` VALUES('lemontero', 'user', '*');
INSERT INTO `user_role` VALUES('lfernandez', 'user', '*');
INSERT INTO `user_role` VALUES('lstalling', 'user', '*');
INSERT INTO `user_role` VALUES('maaban', 'user', '*');
INSERT INTO `user_role` VALUES('mamanovell', 'user', '*');
INSERT INTO `user_role` VALUES('mcidriain', 'user', '*');
INSERT INTO `user_role` VALUES('mduran', 'user', '*');
INSERT INTO `user_role` VALUES('mgarcia', 'user', '*');
INSERT INTO `user_role` VALUES('mgoikolea', 'user', '*');
INSERT INTO `user_role` VALUES('mgoñi', 'user', '*');
INSERT INTO `user_role` VALUES('mkekejian', 'user', '*');
INSERT INTO `user_role` VALUES('mmendez', 'user', '*');
INSERT INTO `user_role` VALUES('mmikirdistan', 'user', '*');
INSERT INTO `user_role` VALUES('mpalma', 'user', '*');
INSERT INTO `user_role` VALUES('mpedro', 'user', '*');
INSERT INTO `user_role` VALUES('mpedroche', 'user', '*');
INSERT INTO `user_role` VALUES('mramirez', 'user', '*');
INSERT INTO `user_role` VALUES('mraposo', 'user', '*');
INSERT INTO `user_role` VALUES('msoms', 'user', '*');
INSERT INTO `user_role` VALUES('nescala', 'user', '*');
INSERT INTO `user_role` VALUES('olivier', 'user', '*');
INSERT INTO `user_role` VALUES('pereztoril', 'user', '*');
INSERT INTO `user_role` VALUES('pgonzalo', 'user', '*');
INSERT INTO `user_role` VALUES('rcasado', 'user', '*');
INSERT INTO `user_role` VALUES('rfernandez', 'user', '*');
INSERT INTO `user_role` VALUES('ricardo-amaste', 'user', '*');
INSERT INTO `user_role` VALUES('root', 'admin', '*');
INSERT INTO `user_role` VALUES('root', 'checker', '*');
INSERT INTO `user_role` VALUES('root', 'root', '*');
INSERT INTO `user_role` VALUES('root', 'superadmin', '*');
INSERT INTO `user_role` VALUES('root', 'translator', '*');
INSERT INTO `user_role` VALUES('root', 'user', '*');
INSERT INTO `user_role` VALUES('rparramon', 'user', '*');
INSERT INTO `user_role` VALUES('rsalas', 'user', '*');
INSERT INTO `user_role` VALUES('rsteckelbach', 'user', '*');
INSERT INTO `user_role` VALUES('rtorres', 'user', '*');
INSERT INTO `user_role` VALUES('sgrueso', 'user', '*');
INSERT INTO `user_role` VALUES('smeschede', 'user', '*');
INSERT INTO `user_role` VALUES('snogales', 'user', '*');
INSERT INTO `user_role` VALUES('soritxori', 'user', '*');
INSERT INTO `user_role` VALUES('stena', 'user', '*');
INSERT INTO `user_role` VALUES('susana', 'user', '*');
INSERT INTO `user_role` VALUES('tbadia', 'user', '*');
INSERT INTO `user_role` VALUES('tguido', 'user', '*');
INSERT INTO `user_role` VALUES('tintangibles', 'user', '*');
INSERT INTO `user_role` VALUES('todojunto', 'user', '*');
INSERT INTO `user_role` VALUES('vsantiago', 'user', '*');
INSERT INTO `user_role` VALUES('vtorre', 'user', '*');
INSERT INTO `user_role` VALUES('yriquelme', 'user', '*');
INSERT INTO `user_role` VALUES('zaramari', 'user', '*');
INSERT INTO `user_role` VALUES('goteo', 'admin', 'goteo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_web`
--

DROP TABLE IF EXISTS `user_web`;
CREATE TABLE `user_web` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `url` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Webs de los usuarios' AUTO_INCREMENT=40 ;

--
-- Volcar la base de datos para la tabla `user_web`
--

INSERT INTO `user_web` VALUES(1, 'todojunto', 'www.todojunto.net');
INSERT INTO `user_web` VALUES(3, 'domenico', 'www.archtlas.com');
INSERT INTO `user_web` VALUES(4, 'ebaraonap', 'http://www.archinhand.com/');
INSERT INTO `user_web` VALUES(5, 'geraldo', 'http://www.canalalpha.net/');
INSERT INTO `user_web` VALUES(6, 'carlaboserman', 'http://www.robocicla.net');
INSERT INTO `user_web` VALUES(7, 'acomunes', 'http://movecommons.org');
INSERT INTO `user_web` VALUES(8, 'efoglia', 'http://www.proyectoliquido.com/Mobile_Node.htm');
INSERT INTO `user_web` VALUES(9, 'tintangibles', 'http://enlloc.net/hkp/w/');
INSERT INTO `user_web` VALUES(22, 'goteo', 'http://www.youcoop.org');
INSERT INTO `user_web` VALUES(25, 'olivier', 'http://www.youcoop.org/');
INSERT INTO `user_web` VALUES(27, 'esenabre', 'http://www.estigmergia.net/wiki/Especial:CambiosRecientes');
INSERT INTO `user_web` VALUES(30, 'diegobus', 'http://www.pelousse.com');
INSERT INTO `user_web` VALUES(34, 'root', 'http://');
INSERT INTO `user_web` VALUES(35, 'demo', 'http://tweetometro.org');
INSERT INTO `user_web` VALUES(36, 'demo', 'http://burnstation.net');
INSERT INTO `user_web` VALUES(37, 'demo', 'http://open-server.org');
INSERT INTO `user_web` VALUES(38, 'ivan', 'http://microstudi.net');
INSERT INTO `user_web` VALUES(39, 'fbalague', 'http://www.akoranga.org');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `worthcracy`
--

DROP TABLE IF EXISTS `worthcracy`;
CREATE TABLE `worthcracy` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `amount` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Niveles de meritocracia' AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `worthcracy`
--

INSERT INTO `worthcracy` VALUES(1, 'Fan', 5);
INSERT INTO `worthcracy` VALUES(2, 'Patrocinador', 50);
INSERT INTO `worthcracy` VALUES(3, 'Apostador', 100);
INSERT INTO `worthcracy` VALUES(4, 'Abonado', 500);
INSERT INTO `worthcracy` VALUES(5, 'Visionario', 1000);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
