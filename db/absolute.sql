-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-07-2011 a las 00:01:28
-- Versión del servidor: 5.1.49
-- Versión de PHP: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `goteo`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

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
INSERT INTO `acl` VALUES(39, '*', '*', '*', '/user/recover/*', 1, '2011-06-12 22:30:36');
INSERT INTO `acl` VALUES(40, '*', '*', '*', '/news/*', 1, '2011-06-19 13:35:41');
INSERT INTO `acl` VALUES(41, '*', '*', '*', '/community/*', 1, '2011-06-19 13:49:18');
INSERT INTO `acl` VALUES(42, '*', '*', '*', '/ws/*', 1, '2011-06-20 23:17:21');
INSERT INTO `acl` VALUES(43, '*', 'checker', '*', '/review/*', 1, '2011-06-21 17:18:51');
INSERT INTO `acl` VALUES(44, '*', '*', '*', '/contact/*', 1, '2011-06-30 00:22:59');
INSERT INTO `acl` VALUES(45, '*', 'user', 'goteo', '/project/edit/a9277be1c7e92eaa36ecae753231bfb1/', 1, '2011-07-11 22:03:12');
INSERT INTO `acl` VALUES(46, '*', 'user', 'goteo', '/project/delete/a9277be1c7e92eaa36ecae753231bfb1/', 1, '2011-07-11 22:03:12');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Blogs de nodo o proyecto' AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `blog`
--

INSERT INTO `blog` VALUES(1, 'node', 'goteo', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `campaign`
--

INSERT INTO `campaign` VALUES(1, 'CampaÃ±a GIJ', 'Gabinete de iniciativa Joven');
INSERT INTO `campaign` VALUES(2, 'Julian''s eleven', 'Los once proyectos estrella de JuliÃ¡n');

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

INSERT INTO `category` VALUES(1, 'EducaciÃ³n', 'EducaciÃ³n', 1);
INSERT INTO `category` VALUES(2, 'Sociales', 'Proyectos que promueven el cambio social, la resoluciÃ³n de problemas en las relaciones humanas y el fortalecimiento del pueblo para conseguir un mayor bienestar.', 1);
INSERT INTO `category` VALUES(3, 'Empresa abierta', 'Empresa abierta', 7);
INSERT INTO `category` VALUES(4, 'FormaciÃ³n tÃ©cnica', 'FormaciÃ³n tÃ©cnica', 4);
INSERT INTO `category` VALUES(5, 'Desarrollo', 'Desarrollo', 5);
INSERT INTO `category` VALUES(6, 'Comunicadores', 'Proyectos con el objetivo de informar, denunciar, comunicar... EstarÃ­an en este bloque el periodismo ciudadano, documentales, blogs, programas de radio...', 1);
INSERT INTO `category` VALUES(7, 'TecnolÃ³gicos', 'Software, hardware, herramientas... ', 1);
INSERT INTO `category` VALUES(9, 'Emprendedores', 'Proyectos que aspiran a convertirse en una empresa. ', 1);
INSERT INTO `category` VALUES(10, 'DidÃ¡cticos', 'Proyectos donde el objetivo primordial es la educaciÃ³n o la formaciÃ³n a otros. ', 1);
INSERT INTO `category` VALUES(11, 'Creativos', 'Proyectos con objetivos artÃ­sticos, culturales... ', 1);
INSERT INTO `category` VALUES(13, 'EcolÃ³gicos', 'Proyectos relacionados con el cuidado del medio ambiente.\r\n', 1);
INSERT INTO `category` VALUES(14, 'Investigadores', 'Estudios profundos de alguna materia, proyectos que buscan respuestas, soluciones, explicaciones nuevas.', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Comentarios' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `comment`
--


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Desglose de costes de proyectos' AUTO_INCREMENT=145 ;

--
-- Volcar la base de datos para la tabla `cost`
--

INSERT INTO `cost` VALUES(40, 'a565092b772c29abc1b92f999af2f2fb', 'DiseÃ±o de interface', 'Honorarios para el diseÃ±ador web', 'task', 1200, 0, '2011-07-04', '2011-08-07');
INSERT INTO `cost` VALUES(41, 'a565092b772c29abc1b92f999af2f2fb', 'ProgramaciÃ³n del nuevo administrador y maquetaciÃ³n CSS', 'Honorarios para el programador.', 'structure', 3500, 1, '2011-06-20', '2011-08-31');
INSERT INTO `cost` VALUES(42, 'a565092b772c29abc1b92f999af2f2fb', 'CoordinaciÃ³n y anÃ¡lisis de la estructura', '', 'structure', 2500, 1, '2011-06-01', '2011-09-11');
INSERT INTO `cost` VALUES(43, 'a565092b772c29abc1b92f999af2f2fb', 'DifusiÃ³n de la herramienta', 'Honorarios de un community manager', 'task', 1000, NULL, '2011-08-29', '2011-10-09');
INSERT INTO `cost` VALUES(61, 'fe99373e968b0005e5c2406bc41a3528', 'Nuevo coste', '', 'task', 1000, 1, '2011-05-12', '2011-05-12');
INSERT INTO `cost` VALUES(63, 'fe99373e968b0005e5c2406bc41a3528', 'Nuevo coste', '', 'task', 400, NULL, '2011-05-12', '2011-05-12');
INSERT INTO `cost` VALUES(65, 'fe99373e968b0005e5c2406bc41a3528', 'Nuevo coste', '', 'task', 500, 0, '2011-05-12', '2011-05-12');
INSERT INTO `cost` VALUES(66, '2c667d6a62707f369bad654174116a1e', 'comprar semillas', 'comprar semillas', 'structure', 50, 1, '2011-05-12', '2011-05-19');
INSERT INTO `cost` VALUES(67, '2c667d6a62707f369bad654174116a1e', 'diseÃ±o web', 'diseÃ±o y colorines', 'task', 2000, 0, '2011-05-12', '2011-05-19');
INSERT INTO `cost` VALUES(75, 'a565092b772c29abc1b92f999af2f2fb', 'Publicar el cÃ³digo bajo licencia libre', 'Honorarios destinados a documentar el cÃ³digo, empaquetarlo bien y publicarlo bajo una licencia libre', 'task', 1000, NULL, '2011-09-26', '2011-11-06');
INSERT INTO `cost` VALUES(80, 'pliegos', 'Nueva tarea', 'Necesito pilas', 'structure', 600, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(87, '2c667d6a62707f369bad654174116a1e', 'Nueva tarea', NULL, 'task', NULL, NULL, NULL, NULL);
INSERT INTO `cost` VALUES(90, 'todojunto-letterpress', 'Materiales', 'Busqueda y compra de materiales (tiporafÃ­as, tintas, componedores, etc)', 'material', 800, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(91, 'todojunto-letterpress', 'DiseÃ±o', 'DiseÃ±o del manual/fanzine', 'task', 240, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(92, 'todojunto-letterpress', 'Manual', 'ProducciÃ³n del manual-fanzine (500 copias)', 'task', 600, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(93, 'todojunto-letterpress', 'Tratamiento materiales', 'Limpieza y organizaciÃ³n del material adquirido', 'task', 300, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(94, 'oh-oh-fase-2', 'Hardware robot', 'Desarrollo nueva iteracion del hardware para el robot', 'task', 2500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(95, 'oh-oh-fase-2', 'Software robot', 'Desarrollo libreria de software para control del robot', 'task', 1500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(96, 'oh-oh-fase-2', 'Ejemplos', 'Creacion ejemplos para la libreria', 'task', 1000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(97, 'oh-oh-fase-2', 'Plantilla documentaciÃ³n', 'Creacion de un template de documentacion para publicar los ejemplos', 'task', 1000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(98, '8851739335520c5eeea01cd745d0442d', 'Nueva tarea', '', 'task', 50, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(99, 'urban-social-design-database', 'web', 'modificar web archtlas.com', 'task', 300, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(100, 'urban-social-design-database', 'community manager', 'community manager', 'task', 1500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(101, 'urban-social-design-database', 'programaciÃ³n widget', 'programaciÃ³n widget', 'task', 500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(102, 'urban-social-design-database', 'gestiÃ³n y administraciÃ³n', 'gestiÃ³n y administraciÃ³n', 'task', 1500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(103, 'urban-social-design-database', 'mantenimiento', 'mantenimiento', 'task', 700, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(104, 'urban-social-design-database', 'diseÃ±o grafico y/o audiovisual', 'diseÃ±o grafico y/o audiovisual', 'task', 500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(105, 'archinhand-architecture-in-your-hand', 'ProgramaciÃ³n', 'Enlace de modulos, Desarrollo interfaz usuario para cargar contenidos, sistema', 'task', 2100, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(106, 'archinhand-architecture-in-your-hand', 'DiseÃ±o', 'Pulir interfaz de la herramienta, testeo usabilidad, material grafico y audiovisual de comunicacion del proyecto', 'task', 1700, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(107, 'archinhand-architecture-in-your-hand', 'GestiÃ³n de contenidos', 'Permisos y licencias de uso, comisariado de contenidos fase beta', 'task', 1500, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(108, 'archinhand-architecture-in-your-hand', 'Plan de empresa', 'Plan de empresa', 'task', 600, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(109, 'mi-barrio', 'CreaciÃ³n web proyecto', 'CreaciÃ³n web proyecto', 'task', 6000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(110, 'mi-barrio', 'Lanzamiento', 'Lanzamiento y gestiÃ³n convocatoria (selecciÃ³n participantes)', 'task', 3000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(111, 'mi-barrio', 'Talleres', 'DiseÃ±o y realizaciÃ³n Talleres de formaciÃ³n', 'task', 5000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(112, 'mi-barrio', 'GrabanciÃ³n', 'Grabaciones (labor de acompaÃ±amiento a los participantes)', 'task', 3000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(113, 'mi-barrio', 'EdiciÃ³n', 'EdiciÃ³n material grabado', 'task', 5000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(114, 'mi-barrio', 'Online', 'Alojamineto web y difusiÃ³n online', 'task', 3000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(115, 'hkp', 'GuiÃ³n pieza a/v', 'GuiÃ³n pieza a/v', 'task', 600, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(116, 'hkp', 'EdiciÃ³n pieza a/v', 'EdiciÃ³n pieza a/v', 'task', 1800, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(117, 'hkp', 'Post-producciÃ³n pieza a/v', 'Post-producciÃ³n pieza a/v', 'task', 450, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(118, 'hkp', 'EstampaciÃ³n DVD (1500)', 'EstampaciÃ³n DVD (1500)', 'task', 1200, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(119, 'hkp', 'Entrevistas y nuevos registros de vÃ­deo', 'Entrevistas y nuevos registros de vÃ­deo', 'task', 1000, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(120, 'hkp', 'EdiciÃ³n nuevas cÃ¡psulas', 'EdiciÃ³n nuevas cÃ¡psulas', 'task', 800, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(121, 'hkp', 'CompilaciÃ³n y redactado textos libro', 'CompilaciÃ³n y redactado textos libro', 'task', 800, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(122, 'hkp', 'Ilustraciones', 'Ilustraciones', 'task', 500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(123, 'hkp', 'Traducciones libro bilingÃ¼e castellano/catalÃ¡n', 'Traducciones libro bilingÃ¼e castellano/catalÃ¡n', 'task', 2500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(124, 'hkp', 'DiseÃ±o y maquetaciÃ³n libro', 'DiseÃ±o y maquetaciÃ³n libro', 'task', 1300, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(125, 'hkp', 'Imprenta (tiraje 1500)', 'Imprenta (tiraje 1500)', 'task', 6500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(126, 'hkp', 'ManipulaciÃ³n y retractilado pack libro+DVD', 'ManipulaciÃ³n y retractilado pack libro+DVD', 'task', 500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(127, 'hkp', 'EnvÃ­os distribuciÃ³n pack', 'EnvÃ­os distribuciÃ³n pack', 'task', 400, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(128, 'hkp', 'Mantenimiento y mejoras wiki', 'Mantenimiento y mejoras wiki', 'task', 600, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(129, 'hkp', 'Tareas administrativas y de gestiÃ³n', 'Tareas administrativas y de gestiÃ³n', 'task', 500, 1, '2011-07-05', '2011-07-05');
INSERT INTO `cost` VALUES(130, 'move-commons', 'Plataforma', 'Continuar desarrollo de la plataforma MC (con subcategorÃ­as), maximizando usabilidad y con versiÃ³n accesible', 'task', 3600, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(131, 'move-commons', 'Motor de bÃºsquedas', 'Desarrollo del motor de bÃºsquedas semÃ¡nticas para MC', 'task', 2400, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(132, 'move-commons', 'DiseÃ±o', 'DiseÃ±o de iconos, explicaciones grÃ¡ficas, material visual explicativo', 'task', 1200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(133, 'move-commons', 'RedacciÃ³n', 'RedacciÃ³n (en castellano e inglÃ©s) de textos divulgativos y HOWTOs documentados para cada categorÃ­a', 'task', 1200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(134, 'nodo-movil', 'Material', 'compra de material para 1 Nodo MÃ³vil', 'material', 350, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(135, 'nodo-movil', 'construcciÃ³n', 'construcciÃ³n del Nodo MÃ³vil', 'task', 200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(136, 'nodo-movil', 'configuraciÃ³n', 'configuraciÃ³n del firmware', 'task', 200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(137, 'nodo-movil', 'Testing', 'pruebas testing 2 Nodos MÃ³viles', 'task', 10, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(138, 'canal-alfa', 'CreaciÃ³n plataforma web', 'CreaciÃ³n plataforma web', 'task', 3200, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(139, 'robocicla', 'GuiÃ³n', 'Linea editorial, guion e History Board', 'task', 1000, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(140, 'robocicla', 'Ilustraciones', 'Ilustraciones', 'task', 400, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(141, 'robocicla', 'DiseÃ±o', 'DiseÃ±o grÃ¡fico y maquetaciÃ³n', 'task', 400, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(142, 'robocicla', 'DiseÃ±o packaging', 'DiseÃ±o packaging', 'task', 100, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(143, 'robocicla', 'Espacio Digital', 'Espacio Digital', 'task', 100, 1, '0000-00-00', '0000-00-00');
INSERT INTO `cost` VALUES(144, 'a9277be1c7e92eaa36ecae753231bfb1', 'Nueva tarea', NULL, 'task', NULL, 1, '2011-07-11', '2011-07-11');

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

INSERT INTO `criteria` VALUES(5, 'project', 'Es original', '', 1);
INSERT INTO `criteria` VALUES(6, 'project', 'Es eficaz en su estrategia de comunicaciÃ³n', '', 2);
INSERT INTO `criteria` VALUES(7, 'project', 'Aporta informaciÃ³n suficiente del proyecto', '', 3);
INSERT INTO `criteria` VALUES(8, 'project', 'Aporta productos, servicios o valores â€œdeseablesâ€ para la comunidad', '', 4);
INSERT INTO `criteria` VALUES(9, 'project', 'Es afÃ­n a la cultura abierta', '', 5);
INSERT INTO `criteria` VALUES(10, 'project', 'Puede crecer, es escalable', '', 6);
INSERT INTO `criteria` VALUES(11, 'project', 'Son coherentes los recursos solicitados con los objetivos y el tiempo de desarrollo', '', 7);
INSERT INTO `criteria` VALUES(12, 'project', 'Riesgo proporcional al grado de benficios (sociales, culturales y/o econÃ³micos)', '', 8);
INSERT INTO `criteria` VALUES(13, 'owner', 'Posee buena reputaciÃ³n en su sector', '', 1);
INSERT INTO `criteria` VALUES(14, 'owner', 'Ha trabajado con organizaciones y colectivos con buena reputaciÃ³n', '', 2);
INSERT INTO `criteria` VALUES(15, 'owner', 'Aporta informaciÃ³n sobre experiencias anteriores (Ã©xitos y fracasos)', '', 3);
INSERT INTO `criteria` VALUES(16, 'owner', 'Tiene capacidades para llevar a cabo el proyecto', '', 4);
INSERT INTO `criteria` VALUES(17, 'owner', 'Cuenta con un equipo formado', '', 5);
INSERT INTO `criteria` VALUES(18, 'owner', 'Cuenta con una comunidad de seguidores', '', 6);
INSERT INTO `criteria` VALUES(19, 'owner', 'Tiene visibilidad en la red', '', 7);
INSERT INTO `criteria` VALUES(20, 'reward', 'Es viable (su coste estÃ¡ incluido en la producciÃ³n del proyecto)', '', 1);
INSERT INTO `criteria` VALUES(21, 'reward', 'Puede tener efectos positivos, transformadores (sociales, culturales, empresariales)', '', 2);
INSERT INTO `criteria` VALUES(22, 'reward', 'Aporta conocimiento nuevo, de difÃ­cil acceso o en proceso de desaparecer', '', 3);
INSERT INTO `criteria` VALUES(23, 'reward', 'Aporta oportunidades de generar economÃ­a alrededor', '', 4);
INSERT INTO `criteria` VALUES(24, 'reward', 'Da libertad en el uso de sus resultados (es reproductible)', '', 5);
INSERT INTO `criteria` VALUES(25, 'reward', 'Ofrece un retorno atractivo (por original, por Ãºtil, por inspirador... )', '', 6);
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Preguntas frecuentes' AUTO_INCREMENT=60 ;

--
-- Volcar la base de datos para la tabla `faq`
--

INSERT INTO `faq` VALUES(2, 'goteo', 'investors', 'Â¿Que sistema de pago ofrece Goteo?', 'Tienes 2 opciones para hacer efectiva tu aportaciÃ³n al proyecto que quieres apoyar: a travÃ©s PayPal o a travÃ©s de la pasarela de pago de Caja Laboral (Mondragon)\r\nSAMPLE VOLANDA PayPal es una forma fÃ¡cil y segura de realizar tus compras en Internet. Es un sistema usado por mÃ¡s de 230 millones de usuarios en todo el mundo. Es rÃ¡pido, sencillo y flexible: tÃº eliges cÃ³mo apoyar un proyecto, con saldo de PayPal, tarjeta de dÃ©bito, tarjeta de crÃ©dito o cuenta bancaria.\r\n<br> Si todavÃ­a no tienes una cuenta en PayPal puedes crearte una. Es GRATIS y Ãºnicamente necesitas un correo electrÃ³nico y una contraseÃ±a. Tardas 2 minutos en crearla y ya la tienes para siempre para utilizarla en Goteo y en otros mÃ¡s de 100.000 sitios Web en todo el mundo como Ebay, Vueling, etc...SAMPLE VOLANDA\r\n<br> La segunda opciÃ³n es a travÃ©s de la pasarela de pago de Caja Laboral (Mondragon)', 11);
INSERT INTO `faq` VALUES(3, 'goteo', 'node', 'Â¿Que tipo de proyectos caben en Goteo y como se valoran?', 'Goteo es una plataforma en la que se proponen proyectos de muy diverso tipo. Los proyectos se organizan a travÃ©s de secciones temÃ¡ticas (ecologÃ­a, tecnologÃ­a, economÃ­a alternativa, arquitectura, ciencia, diseÃ±o, educaciÃ³n, emprendizaje, cultura libre etc.), una forma de generar sinergias, sumar conocimientos y atraer patrocinios y campaÃ±as especÃ­ficas. Estas secciones temÃ¡ticas cuentan con la complicidad, el compromiso y el asesoramiento de agentes expertos (know how); personas que hacen las funciones de prescriptores, asesores o informadores y que conforman la red de conocimiento de Goteo. \r\n<br>En Goteo se primarÃ¡n las iniciativas que generan un\r\nbeneficio social y que ayuden a construir comunidad\r\nSE buscan proyectos con ADN abierto, proyectos sociales, culturales, educativos, tecnolÃ³gicos que contribuyan al fortalecimiento del procomÃºn', 2);
INSERT INTO `faq` VALUES(4, 'goteo', 'investors', 'Retorno colectivo de la inversiÃ³n', 'Goteo persigue la rentabilidad social de las inversiones efectuadas a travÃ©s de la plataforma, buscando la viabilidad de los proyectos para sus promotor*s y el retorno colectivo para la comunidad (mÃ¡s allÃ¡ o ademÃ¡s de las contraprestaciones individuales).\r\n<br>\r\nEstos retornos colectivos pueden ser de muy diverso tipo y se fundamentan en compartir conocimiento y dar acceso libre al cÃ³digo fuente, a productos y recursos, a formaciÃ³n a travÃ©s de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios econÃ³micamente sostenibles.', 7);
INSERT INTO `faq` VALUES(5, 'goteo', 'node', 'Â¿Cuales son los aspectos fundamentales y diferenciales de Goteo?', 'Goteo se distingue principalmente de otras plataformas por su apuesta diferencial y focalizada en proyectos de cÃ³digo abierto, que comparten conocimiento, procesos, resultado, responsabilidad y benÃ©fico, desde la filosofÃ­a del procomÃºn. Goteo pone el acento en la misiÃ³n pÃºblica, apoyando proyectos que favorezcan el empoderamiento colectivo y el bien comÃºn. \r\nGoteo persigue la rentabilidad social de las inversiones efectuadas a travÃ©s de la plataforma, buscando la viabilidad de los proyectos para sus promotor*s y el retorno colectivo para la comunidad (mÃ¡s allÃ¡ o ademÃ¡s de las contraprestaciones individuales).\r\n\r\nEstos retornos colectivos pueden ser de muy diverso tipo y se fundamentan en compartir conocimiento y dar acceso libre al cÃ³digo fuente, a productos y recursos, a formaciÃ³n a travÃ©s de manuales y/o talleres, a archivos y contenidos que puedan copiarse, reutilizarse y remezclarse, etc. Todo ello con el objetivo de crear comunidad, capital social y que otras personas puedan generar oportunidades derivadas, productos y servicios econÃ³micamente sostenibles.', 5);
INSERT INTO `faq` VALUES(6, 'goteo', 'node', 'Â¿Por quÃ© apostar por licencias abiertas?', 'Para asegurar el retorno colectivo, los proyectos que quieran formar parte de Goteo deben pensarse, producirse y/o distribuirse desde la Ã©tica de lo libre y abierto. Deben acogerse a alguna de las numerosas licencias existentes, con diferentes especificaciones y/o restricciones, que permiten explÃ­citamente la libre copia, distribuciÃ³n y/o modificaciÃ³n de parte o de la totalidad de cada proyecto. \r\n\r\nDesde Goteo se proponen (y se asesora sobre) una serie de licencias bajo las que regular los proyectos: Creative Commons (CC), General Public Licence (GPL), GNU Affero General Public Licence (AGPLv3), Open Batabase (ODBC), Licencia Red Abierta Libre Neutral (XOLN), Open Hardware Licence (OHL).', 8);
INSERT INTO `faq` VALUES(7, 'goteo', 'node', 'Â¿QuÃ© ofrece Goteo a los miembros de la comunidad?', 'Goteo parte de los modelos actuales del crowdfunding, para articularse como una red social de agentes de Ã¡mbitos diversos, cuyo nexo comÃºn es su implicaciÃ³n en el fortalecimiento del procomÃºn. Una plataforma de la que emerja una comunidad -formada por personas individuales y entidades pÃºblicas y privadas (en principio en el Ã¡mbito de EspaÃ±a)-, de agentes promotores-productores, financiadores y/o colaboradores. Cada miembro de la comunidad puede cumplir uno o varios de estos roles segÃºn el momento-proyecto, obteniendo una serie de beneficios y contraprestaciones especÃ­ficas', 10);
INSERT INTO `faq` VALUES(8, 'goteo', 'project', 'Â¿CuÃ¡l es la cantidad mÃ­nima o mÃ¡xima que un proyecto puede tener como objetivo de financiaciÃ³n?', 'Se aspira a financiar proyectos con un presupuesto de cierta envergadura (en principio, desde un importe mÃ­nimo de 3.000â‚¬, hasta 150.000â‚¬', 7);
INSERT INTO `faq` VALUES(9, 'goteo', 'project', 'Â¿QuiÃ©n puede obtener financiaciÃ³n para su proyecto en Goteo?', 'Desde Goteo o desde la red de comunidades se realiza una selecciÃ³n de proyectos en funciÃ³n de su temÃ¡tica, tipologÃ­a y/o procedencia; de la pertinencia y carÃ¡cter innovador o diferencial; de una estimaciÃ³n del retorno colectivo que genera; y/o de la competencia de sus promotores-productores. \r\nSE BUSCAN proyectos desarrollados por agentes creativos, que superan el Ã¡mbito convencional de la Cultura.\r\nProyectos con un alto componente de innovaciÃ³n, con un gran potencial de incidencia social y/o econÃ³mica, con capacidad de crecimiento y reproducciÃ³n, para generar valor en el sentido mÃ¡s amplio de la palabra.<br>\r\n<br>Cualquier creador o emprendedor residente en EspaÃ±a a travÃ©s de Goteo podrÃ¡:\r\n<br>- publicar un proyecto para buscar financiaciÃ³n y colaboraciones;\r\n<br>- acceder a herramientas especÃ­ficas de â€œsocial mediaâ€ para difundir su trabajo en Internet\r\n<br>- publicar contenidos digitales que ayuden a promocionar su trabajo;\r\n<br>- compartir con la comunidad conocimiento sobre su especialidad o sobre el proceso de\r\nproducciÃ³n de su proyecto, proporcionÃ¡ndole una buena reputaciÃ³n (karma) y un mayor\r\nnÃºmero de seguidores y micromecenas potenciales;\r\n<br>- beneficiarse del conocimiento y recomendaciones de otros usuarios para mejorar o\r\ncontrastar la producciÃ³n de su proyecto;\r\n<br>- testear su proyecto en la fase inicial para comprobar el interÃ©s que despierta en el pÃºblico\r\npotencial;\r\n<br>- formar parte de una red social estatal con impacto local y difusiÃ³n internacional;\r\n<br>- ser asesorado para mejorar la comunicaciÃ³n pÃºblica del proyecto y la elecciÃ³n del retorno digital que quedarÃ¡ disponible en la plataforma despuÃ©s de la producciÃ³n del proyecto.', 2);
INSERT INTO `faq` VALUES(10, 'goteo', 'project', 'Â¿Hay un tiempo mÃ¡ximo de financiaciÃ³n?', 'Una vez que un proyecto se publica en Goteo, se abre un plazo de 40 dÃ­as para que se pueda recaudar la cantidad fijada como mÃ­nimo para su realizaciÃ³n (mediante aportaciones financieras desde 5â‚¬ en adelante). AdemÃ¡s en este plazo se establecen compromisos de colaboraciÃ³n segÃºn las competencias o los recursos requeridos por cada proyecto. \r\nEn determinados casos, para alcanzar la cantidad optÃ­ma establecida, se invertirÃ¡ en los proyectos desde la Bolsa de inversiÃ³n social Goteo (para ello, el proyecto deberÃ¡ alcanzar el nivel minimÃ³ de financiaciÃ³n por parte de la sociedad civil).', 8);
INSERT INTO `faq` VALUES(11, 'goteo', 'project', 'Â¿Cual es la fase siguiente despuÃ©s de 40 dÃ­as?', 'Una vez que se alcanza la cantidad mÃ­nima fijada, es posible abrir un segundo plazo, tambiÃ©n de 40 dÃ­as, donde el agente promotor-productor va aportando informaciÃ³n en tiempo real sobre el desarrollo del proyecto. En este segundo plazo, todas las aportaciones financieras se hacen efectivas hasta llegar al optimÃ³.\r\nA lo largo de todo esta fase, resulta fundamental la comunicaciÃ³n y dinamizaciÃ³n de los proyectos para atraer a posibles personas financiadoras y colaboradoras; pero tambiÃ©n, para conectar a los distintos agentes y fortalecer la idea de comunidad y el papel de Goteo como punto de encuentro entrono al procomÃºn. Se trata de contar de un modo atractivo cada proyecto; de moverlo de un modo efectivo en las redes sociales; y de acercarlo a la comunidad afectada por el propio proyecto y al Ã¡mbito de influencia del propio agente promotor-productor.', 9);
INSERT INTO `faq` VALUES(12, 'goteo', 'project', 'Tengo miedo de que me plagien mi idea o proyecto, Â¿quÃ© hace Goteo para impedir esto?', 'Partner con creative commons y safecreative. http://es.safecreative.net/faqs/', 23);
INSERT INTO `faq` VALUES(13, 'goteo', 'investors', 'Â¿Se pueden hacer aportaciones no Ãºnicamente monetarias?', 'Una gran apuesta de Goteo y un diferencial notable con las otras plataformas de crowdfunding, es el valor de las colaboraciones y aporataciones no monetarias, en forma de servicios o recursos materiales que puedan surgir en el proceso, siempre desde el espiritu del beneficio mutuo. El proyecto a la hora de presentarse en Goteo lista tareas y posibles prestamos materiales deseados que suman a sus necesidades financieras, pero que no son impresindibles para que el proyecte arranque y de ninguna manera sustituyen un posible encargo profesional. si el proyecto va de una web y necesita programaciÃ³n, esta tarea, considerada imprecindible debe ser reflejada en los costes del proyecto, a cambio si el proyecto necesita beta testers, traducciones que encuentren volontarios que consideren que el proyecto esta haciendo bien el trabajo de actualizaciÃ³n del proyecto y publicaciÃ³n de avances, esta relaciÃ³n no interesada puede tener lugar.', 3);
INSERT INTO `faq` VALUES(14, 'goteo', 'investors', 'Â¿Por quÃ© 2 rondas de 40 dÃ­as?', 'Se establecen dos plazos para la financiaciÃ³n, de un mÃ¡ximo de 40 dÃ­as cada uno (en otras plataformas habitualmente sÃ³lo hay uno). El primero, en el que las aportaciones comprometidas sÃ³lo se hacen efectivas al alcanzar una cantidad mÃ­nima establecida. Y el segundo, en el que se aporta informaciÃ³n en tiempo real sobre el proyecto y todas las aportaciones se hacen efectivas', 8);
INSERT INTO `faq` VALUES(16, 'goteo', 'investors', 'Â¿QuÃ© ocurre si un proyecto NO consigue la financiaciÃ³n minima?', 'SAMPLE Si un creador expone que necesita una cantidad determinada para realizar su proyecto y no llega a alcanzarla se considera que no podrÃ¡ desarrollar el proyecto y dar las recompensas. Si esto sucede los pagos no serÃ¡n efectivos. Â¿Por quÃ©?<br>\r\nâ€¢Permite a la gente correr menos riesgos. Ya que si no se consigue la cantidad total no se espera que se realice el proyecto.<br>\r\nâ€¢Permite a la gente intentar todo tipo de proyectos sin ningÃºn tipo de riesgo.<br>\r\nâ€¢MotivaciÃ³n. Si la gente quiere que un proyecto se lleve a cabo estos se moverÃ¡n para promocionarlo y conseguir los apoyos suficientes. SAMPLE', 9);
INSERT INTO `faq` VALUES(21, 'goteo', 'nodes', 'nodos', 'nodos', 1);
INSERT INTO `faq` VALUES(22, 'goteo', 'project', 'Â¿Como se presentan los resultados y retornos de los proyectos?', 'Esta es la fase final, que cierra el cÃ­rculo del compromiso entre las partes, con la presentaciÃ³n pÃºblica on-line de los resultados del proyecto financiado, haciÃ©ndose efectivos los retornos colectivos e individuales acordados. Es una fase que hay que cuidar especialmente, para mantener las relaciones de confianza dentro de la comunidad y que el fortalecimiento del procomÃºn que nos proponemos, sea cierto.', 25);
INSERT INTO `faq` VALUES(23, 'goteo', 'node', 'Â¿QuiÃ©n promueve Goteo?', 'El promotor de Goteo es Platoniq, una organizaciÃ³n internacional de productores culturales y desarrolladores de software, pionera en la producciÃ³n y distribuciÃ³n de la cultura copyleft. Desde el aÃ±o 2001, llevan a cabo acciones y proyectos donde los usos sociales de las NTICs y el trabajo en red son aplicados al fomento de la comunicaciÃ³n, la auto-formaciÃ³n y la organizaciÃ³n ciudadana. Entre sus proyectos destacan: Burn Station, OpenServer, Banco ComÃºn de Conocimientos o S.O.S. Todos estos y otros muchos proyectos estÃ¡n disponibles en la plataforma de metodologÃ­as libres YOUCOOP (http://www.youcoop.org).\r\n\r\nActualmente Platoniq lleva mÃ¡s de un aÃ±o totalmente volcada en el desarrollo de Goteo. Ha realizado un estudio exhaustivo sobre plataformas de crowdfunding a nivel internacional, un plan de viabilidad y ya estÃ¡ desarrollando el trabajo de programaciÃ³n de la plataforma.\r\n\r\nPara el desarrollo de Goteo, Platoniq cuenta con el apoyo de diversas entidades nacionales e internacionales, entre las que cabe destacar: \r\nEUTOKIA, Centro de InnovaciÃ³n Social de Bilbao (ColaBoraBora)\r\nTrÃ nsit Projectes\r\nCCCB, Centro de Cultura ContemporÃ¡nea de Barcelona (CCCBlab)\r\nConsell Nacional de la Cultura i de les Arts de Catalunya\r\nInstituto de Cultura de Barcelona\r\n\r\nDe cara a la puesta en marcha de Goteo se constituirÃ¡ la FundaciÃ³n Goteo, que integre a todos los agentes comprometidos con el desarrollo del proyecto y asegure un funcionamiento transparente y responsable.\r\n', 11);
INSERT INTO `faq` VALUES(24, 'goteo', 'investors', 'Bolsa de inversiÃ³n social', 'Se crea una bolsa de inversiÃ³n social con aportaciones de entidades pÃºblicas y privadas, con el que co-financiar proyectos apoyados por la ciudadanÃ­a.<br>\r\nAdemÃ¡s de las aportaciones individuales desde la sociedad civil vinculadas a proyectos concretos, para favorecer y potenciar el espacio de co-responsabilidad, se crearÃ¡ el Bolsa de inversiÃ³n social Goteo, con aportaciones provenientes de la administraciÃ³n pÃºblica y de empresas y otras organizaciones privadas, con el que co-financiar proyectos apoyados por la ciudadanÃ­a. \r\n<br><br>\r\nEsta bolsa se administrarÃ¡ de modo transparente y responsable desde la FundaciÃ³n Goteo y se nutrirÃ¡ a partir de distintos tipos de compromisos anuales y a travÃ©s de campaÃ±as temporales especÃ­ficas (desde fÃ³rmulas preestablecidas o a partir acuerdos ad hoc). \r\n<br><br>\r\nLos agentes inversores podrÃ¡n vincular sus aportaciones a determinadas Ã¡reas temÃ¡ticas, Ã¡mbito geogrÃ¡fico y/o tipo de retorno y licencias; y vehicular de un modo innovador parte de sus competencias y programas, y sus polÃ­ticas de compromiso responsabilidad social. AdemÃ¡s obtendrÃ¡n otras ventajas como: participar de la generaciÃ³n de formas de conocimiento colectivo y proyectos socialmente innovadores, cercanÃ­a e interlocuciÃ³n directa con comunidades emergentes, visibilidad y reconocimiento vinculado a proyectos relacionados con el procomÃºn, desgravaciones fiscales, etc.', 10);
INSERT INTO `faq` VALUES(25, 'goteo', 'project', 'Â¿Por quÃ© publicar un proyecto en Goteo?', 'Publicar proyectos en Goteo es acceder a un nuevo modelo colectivo de financiaciÃ³n y colaboraciones, aprovechando las posibilidades de las NTICs; haciendo participe desde el principio de un modo eficaz a la comunidad potencial del proyecto, fomentando una relaciÃ³n cooperativa, activa, estrecha y transparente.\r\nGoteo plantea una nueva vÃ­a de financiaciÃ³n acorde a las posibilidades que ofrecen los medios digitales. Una alternativa o un complemento a la financiaciÃ³n derivada de la administraciÃ³n pÃºblica y/o de la empresa privada, reactivando el papel co-responsable de la sociedad civil, en el desarrollo autÃ³nomo de iniciativas que contribuyan al desarrollo social comunitario, desde la filosofÃ­a del procomÃºn. Una manera de que personas y organizaciones pequeÃ±as, con un acceso difÃ­cil a los recursos, puedan llevar a cabo con Ã©xito, proyectos sostenibles y perdurables en el tiempo. ', 1);
INSERT INTO `faq` VALUES(26, 'goteo', 'node', 'Â¿Por quÃ© minimo y optimo?', 'DefiniciÃ³n general', 3);
INSERT INTO `faq` VALUES(28, 'goteo', 'project', 'Â¿Como gestionar las recompensas individuales?', 'ESFUERZO PARA QUE SEA POSIBLE A TRAVES DEL FORMULARIO (integrar costes retornos en costes) y el DASHBOARD (serie de herramientas para poder gestionar envios.\r\n(KREANDU) Si tu proyecto ha alcanzado o superado el objetivo de financiaciÃ³n podrÃ¡s acceder a los datos de tus mecenas para entrar en contacto con ellos y entregarles las recompensas. Si no lo ha alcanzado no accederÃ¡s a dicha informaciÃ³n, puesto que el proyecto no saldrÃ¡ adelante, los mecenas no habrÃ¡n pagado nada y no estarÃ¡s obligado a entregar las recompensas.', 19);
INSERT INTO `faq` VALUES(29, 'goteo', 'node', 'Â¿A que se refiere Goteo con Capital riego?', 'Crowdfunding compatible con crowdprofits?', 6);
INSERT INTO `faq` VALUES(30, 'goteo', 'node', 'Â¿Por quÃ© los proyectos abiertos tienen mas potencial?', '- Porque tienen una misiÃ³n mÃ¡s allÃ¡ de los incentivos o recompensas individuales . Aportan valor al sector cultural mismo. Crean modelos innovadores. \r\n<br><br>\r\n- Fomentan el aprendizaje y el emprendizaje de otros ( permiten que se genere una economÃ­a alrededor, bajo las reglas de atribuir la autorÃ­a y que los derivados estÃ©n bajo la misma Ã©tica de lo abierto)\r\n<br><br>\r\n- Porque construyen capital social apoyandose y nutriendo una comunidad de seguidores, prosumidores y divulgadores', 9);
INSERT INTO `faq` VALUES(31, 'goteo', 'project', 'Â¿QuÃ© tipo de proyectos NO se publican en Goteo?', '(INJOINET) Las normas de la plataforma no permiten crear proyectos relacionados con la pornografÃ­a, la venta de armas, loterÃ­as, concursos o sorteos de cualquier Ã­ndole, ademÃ¡s de, obviamente, proyectos que impliquen cualquier actividad ilegal. (INJOINET)\r\n(VERKAMI)...pretende ser una plataforma para impulsar proyectos creativos e innovadores. No se trata de financiar negocios tradicionales a cambio de beneficios, ni de recaudar dinero para obras de caridad a cambio de nada. Tampoco se trata de rifas, subastas, etc.(VERKAMI)\r\n(KREANDU) solicitar donaciones o recaudar fondos para causas benÃ©ficas\r\n	â€¢	obtener fondos para campaÃ±as polÃ­ticas\r\n	â€¢	sorteos, loterÃ­as o rifas\r\n', 3);
INSERT INTO `faq` VALUES(32, 'goteo', 'investors', 'Â¿Por quÃ© invertir en los proyectos de esta plataforma?', 'XXXXXXXXXXXXX', 1);
INSERT INTO `faq` VALUES(33, 'goteo', 'node', 'Â¿CÃ³mo entendemos el â€œcrowdfundingâ€ o financiaciÃ³n colectiva en Goteo?', 'Goteo es una red social de producciÃ³n, microfinanciaciÃ³n y distribuciÃ³n de recursos para el sector creativo, para el desarrollo de proyectos sociales, culturales, educativos, tecnolÃ³gicos que contribuyan al fortalecimiento del procomÃºn.<br>\r\nTras el Ã©xito de plataformas de crowdfunding en el mundo (la mÃ¡s famosa www.kickstarter.com, creada en abril 2009 en EE.UU.), estÃ¡n proliferando muchas plataformas similares (por ejemplo en EspaÃ±a: Verkami, LÃ¡nzanos, etc.). Esta multiplicaciÃ³n podrÃ­a devenir en saturaciÃ³n y desconcierto, sobre todo si tenemos en cuenta que en EspaÃ±a no tenemos costumbre de ser partÃ­cipes de la financiaciÃ³n de proyectos. Por eso, consideramos que es fundamental apostar por la diferenciaciÃ³n y la especializaciÃ³n.\r\nEn Goteo partimos de la equaciÃ³n mas potente del crowdfunding es: 1+1= + que la suma de las partes. FinanciaciÃ³n colectiva para el beneficio colectivo. \r\n<br>Goteo ofrece una plataforma digital que facilita y combina la financiaciÃ³n colectiva (crowdfunding) y las colaboraciones no dinerarias (competencias, microtareas) entre los usuarios de la plataforma. <br>La estrategia de expansiÃ³n de Goteo se\r\nbasa en reproducir la herramienta por sectores/temÃ¡ticas o localidades (comunidades autÃ³nomas o ciudades de gran concentraciÃ³n cultural) dando un servicio a organizaciones\r\npÃºblicas o privadas para que dinamicen su plataforma Goteo dentro de su sector y competencias\r\n\r\n', 1);
INSERT INTO `faq` VALUES(34, 'goteo', 'node', 'Â¿CÃ³mo funciona la financiaciÃ³n de 40 + 40?', 'BASADO EN VERKAMI Cada proyecto tiene un objetivo de financiaciÃ³n, establecido por el creador, y 40 dÃ­as para conseguir la financiaciÃ³n minima. Finalizado el primer plazo existen dos escenarios:<br>\r\n	1.	Que no se haya recaudado el minimÃ³ del objetivo de financiaciÃ³n. En este caso no hay ningÃºn tipo de transacciÃ³n monetaria y los compromisos de aportaciÃ³n de los mecenas quedan anulados. El proyecto no sigue en campaÃ±a<br>\r\n	2.	Que se haya llegado o superado el minimÃ³ del objetivo de financiaciÃ³n. En este caso se realiza el cargo en tarjeta de los compromisos de aportaciÃ³n y el creador recibe el dinero recaudado. A partir de este momento, se abre un segundo plazo, tambiÃ©n de 40 dÃ­as, donde el agente promotor-productor va aportando informaciÃ³n en tiempo real sobre el desarrollo del proyecto. En este segundo plazo, todas las aportaciones financieras se hacen efectivas hasta llegar al optimo. El creador recibe el dinero recaudado al final de esta segunda ronda de 40 dÃ­as.\r\n', 4);
INSERT INTO `faq` VALUES(35, 'goteo', 'project', 'Â¿QuÃ© pasa si un proyecto llega al nivel optÃ­mo de financiaciÃ³n antes de acabar el primer plazo de 40 dÃ­as? ', 'XXXX', 10);
INSERT INTO `faq` VALUES(36, 'goteo', 'project', 'Â¿ComÃ³ me beneficÃ­a usar licencias abiertas para mi proyecto?', '', 24);
INSERT INTO `faq` VALUES(37, 'goteo', 'project', 'Â¿Cual es el proceso de revisiÃ³n de proyectos? ', 'Comunidades experto\r\nPrevio a la publicaciÃ³n de cada proyecto, si es necesario, se realiza una labor de asesoramiento para la capacitaciÃ³n del proyecto sobre la manera mÃ¡s eficaz de comunicar los proyectos tanto en Goteo como a travÃ©s de otros medios; y sobre cÃ³mo configurarlos o adaptarlos segÃºn la filosofÃ­a del procomÃºn y la cultura de cÃ³digo abierto (tipos de licencias, contraprestaciones colectivas, nuevos productos y servicios, etc.). AQUI HABLAR DEL FORMULARIO WIZZARD\r\n<br>\r\nEsta fase de selecciÃ³n y sofisticaciÃ³n es fundamental para que la plataforma sea un ecosistema diverso; un espacio de oportunidad y no de competencia; donde los intereses y los recursos se sumen y no se solapen. Es necesario tener una conciencia clara respecto a la capacidad efectiva de promociÃ³n de proyectos en Goteo y de la masa crÃ­tica necesaria.\r\n', 4);
INSERT INTO `faq` VALUES(38, 'goteo', 'project', 'Â¿Puedo tener mÃ¡s de un proyecto en campaÃ±a en Goteo? ', 'XXXX', 22);
INSERT INTO `faq` VALUES(39, 'goteo', 'project', 'Â¿QuÃ© son las recompensas individuales y como pensarlas? ', 'En goteo hay 2 tipos de recompensas, las individuales y las colectivas.\r\nLas recompensas individuales son las que ofreces a tus cofinanciadores SAMPLE VERKAMI ...a cambio de su aportaciÃ³n. Las recompensas son uno de los factores determinantes del Ã©xito de tu proyecto. De lo atractivas que sean para tus posibles mecenas dependerÃ¡ que obtengas mÃ¡s o menos aportaciones.â€¨Te recomendamos que no establezcas demasiados niveles de aportaciÃ³n, que pueden confundir y hacer dudar a los usuarios.â€¨Te sugerimos tener recompensas para todos los bolsillos. Recuerda que diez personas aportando 25â‚¬ son igual de importantes que una que aporta 250â‚¬.â€¨Las recompensas deben tener un valor adecuado al momento de la aportaciÃ³n. Piensa que los mecenas te estÃ¡n ayudando con tu proyecto y que deben conseguir un producto a mejor precio que el de mercado o un servicio o experiencia exclusivo, que no se pueda conseguir si no es aportando al proyecto.â€¨ SAMPLE VERKAMI\r\nTen en cuenta el coste de las recompensas a la hora de calcular los costes del proyecto. ', 13);
INSERT INTO `faq` VALUES(40, 'goteo', 'project', 'Â¿Cuanto tarda Goteo en darme una respuesta?', 'Â¿15 dÃ­as?', 6);
INSERT INTO `faq` VALUES(41, 'goteo', 'project', 'Â¿Como recibo las aportaciones de mis cofinanciadores? â€¨', 'Los mecenas realizan sus compromisos de aportaciÃ³n mediante Paypal o tarjeta de crÃ©dito utilizando la pasarela de pago de Caja Laboral (Grupo Mondragon).â€¨ Cuando tu proyecto alcance el minimo del objetivo de financiaciÃ³n recibirÃ¡s una notificaciÃ³n y deberÃ¡s proporcionarnos tus datos bancarios. Cuando acabe el primer plazo de recaudaciÃ³n de 40 dÃ­as, recibirÃ¡s una transferencia por el importe recaudado hasta la dicha fecha, descontando la comisiÃ³n de Goteo (8%) y los gastos bancarios.â€¨ Despues del segundo plazo de 40 dÃ­as recibiras el dinero hasta alcanzar la cantidad corespondiente al optimÃ³.', 15);
INSERT INTO `faq` VALUES(42, 'goteo', 'project', 'Â¿El banco cobra algÃºn tipo de comisiÃ³n?', 'SAMPLE VERKAMI SÃ­. Cuando un proyecto acaba con Ã©xito y se hacen efectivas las aportaciones de los mecenas, el banco carga una comisiÃ³n de entre 1.30% y 1.45% de procesamiento de pago por cada tarjeta. SAMPLE VERKAMI  <br>\r\nEn el caso de las aportaciones hechas a traves de Paypal, la comisiÃ³n es del ???3%???\r\nEstas comisiÃ³nes van a cargo del creador y tiene que tenerla en cuenta a la hora de calcular su objetivo de financiaciÃ³n.â€¨ ', 16);
INSERT INTO `faq` VALUES(43, 'goteo', 'investors', 'Â¿Es seguro el sistema de pagos?', 'SAMPLE LANZANOS Existen dos modalidades de pagos, el sistema Paypal, que es uno de los provedores de pagos mÃ¡s seguros y utilizados de internet, y la pasarela de pagos de Caja Laboral (Mondragon) que posee todo tipo de medidas de seguridad para evitar robos de claves o suplantaciones de identidad. SAMPLE LANZANOS\r\nGoteo no tiene acceso a tus datos bancarios en ningÃºn momento del proceso.â€¨\r\n', 12);
INSERT INTO `faq` VALUES(45, 'goteo', 'investors', 'Â¿Se me avisa si un proyecto al que he apoyado ha finalizado? ', 'SI- Tanto si tu aportaciÃ³n es monetariÃ¡ o si has colaborado activamente en el proyecto, recibirÃ¡s un correo electrÃ³nico para informarte, tanto si ha llegado al minimÃ³ y se pone en marcha como si no. TambiÃ©n recibirÃ¡s un correo electrÃ³nico con la confirmaciÃ³n de que se ha realizado el cargo en tu tarjeta.â€¨ Si tu aportaciÃ³n se ha efectuado durante la segunda ronda del proyecto, tambien recibiras notificaciÃ³n y la confirmaciÃ³n de que se ha realizado el cargo en tu tarjeta en cuanto se acabe el plazo de 40 dÃ­as.', 5);
INSERT INTO `faq` VALUES(46, 'goteo', 'investors', 'Â¿Si hago una aportaciÃ³n, a quÃ© informaciÃ³n accede el creador del proyecto?', ' SAMPLE VERKAMI Â¿Si hago una aportaciÃ³n, quÃ© informaciÃ³n recibe el creador del proyecto sobre mi?  â€¨Tu nombre de usuario y la cantidad que has aportado junto con la recompensa que has elegido. Aunque el creador no tiene tu email, tiene la posibilidad de enviarte mensajes a tu correo a travÃ©s de Goteo. Mediante esta comunicaciÃ³n directa y sin intermediarios, podrÃ¡ tenerte al tanto de todas las actualizaciones del proyecto. â€¨SAMPLE VERKAMI ', 2);
INSERT INTO `faq` VALUES(47, 'goteo', 'investors', 'Â¿CÃ³mo puedo ayudar a la difusiÃ³n de un proyecto?', 'SAMPLE VERKAMI En la pÃ¡gina del proyecto, en el apartado â€œComparte el proyecto con tus amigosâ€, tienes diversas herramientas para hacer la difusiÃ³n fÃ¡cilmente en otras webs o redes sociales.\r\n	â—¦	Puedes utilizar el widget del proyecto en tu blog o pÃ¡gina web para mostrar un resumen del proyecto junto con las actualizaciones en tiempo real de la cantidad recaudada, el nÃºmero de mecenas y los dÃ­as que faltan todavÃ­a para hacer aportaciones.\r\n	â—¦	TambiÃ©n encontrarÃ¡s botones para compartir el proyecto en facebook y twitter.\r\nSAMPLE VERKAMI', 6);
INSERT INTO `faq` VALUES(48, 'goteo', 'investors', 'Â¿CÃ³mo puedo participar en el proceso creativo de los proyectos?', 'â€¨Puedes hacer preguntas, sugerencias y responder al creador sobre el proyecto y sus necesidades financieras y llamadas a colaboraciÃ³n\r\nPuedes seguir y comentar el desarrollo de todo el proceso a traves de las actualizaciones del proyecto. A parte recibirÃ¡s por email o a travÃ©s de tu dashboard avisos de los progresos, posibles cambios y actualizaciones que el creador vaya introduciendo. SAMPLE VERKAMI ', 4);
INSERT INTO `faq` VALUES(49, 'goteo', 'project', 'Â¿CÃ³mo puedo obtener la informaciÃ³n de mis cofinanciadores?', 'SAMPLE LANZANOS Â¿CÃ³mo puedo obtener la informaciÃ³n de los usuarios que han donado a mi proyecto? Cuando un proyecto alcanza su objetivo econÃ³mico el creador obtendrÃ¡ la informaciÃ³n necesaria para ponerse en contacto con los financiadores. SAMPLE LANZANOS', 18);
INSERT INTO `faq` VALUES(50, 'goteo', 'project', 'Â¿Por quÃ© es mejor publicar actualizaciones de mi proyecto?', 'SAMPLE LANZANOS Los creadores de proyectos podrÃ¡n realizar actualizaciones para que los usuarios puedan seguir la evoluciÃ³n de los proyectos dÃ­a a dÃ­a. En estas actualizaciones se pueden incluir textos, fotografÃ­as, etc. SAMPLE LANZANOS <br>\r\nEsas notificaciones son una parte fundamental del proceso de hacer el proyecto mas empatico...\r\n', 21);
INSERT INTO `faq` VALUES(51, 'goteo', 'nodes', 'Â¿QuÃ© son los nodos?', 'Goteo es una comunidad de nodos -cuyo nexo de uniÃ³n es el interÃ©s por el fortalecimiento del procomÃºn-, que se articulan en torno a una plataforma digital en internet. Un sistema distribuido de nodos locales (know place), agentes legitimados, con un importante calado social, referentes en su Ã¡mbito de actuaciÃ³n. Una red de nodos de confianza, que sirven para localizar lo digital, aportando proximidad y especificidad, multiplicando el efecto de la plataforma.', 1);
INSERT INTO `faq` VALUES(52, 'goteo', 'node', 'Â¿Con quÃ© redes cuenta Goteo para ayudar a que los proyectos se difundan?', '', 7);
INSERT INTO `faq` VALUES(53, 'goteo', 'project', 'Â¿Como gestionar los retornos colectivos?', 'Dependiendo de la naturaleza del proyecto. Si es codigo, ta aconsejamos github, si es manuales o planos, comparte los drafts y versiones alfas. Usa el tablon de llamadas a colaboraciÃ³n y publica nuevas necesidades una ves el proyecto este en marcha. Entre tus cofinanciadores se encuentran verdaderos talentos y agentes dispuestas a acompaÃ±ar el proyecto. Valora igualmente gente que te apoyan financieramente como la labor de colabaoradores voluntarios. No pierdas nunca de vista la comunidad. Esa es el motor de goteo. Cada gota cuenta, 1 dolar puede ser carburante, 1 idea o apoyo puede ser petrolio! Integra a los colaboradores en los procesos de crecimiento de los proyectos, en posibles retornos economicos, no inicialmente previstos. un cofinanciador puede llega a ser un mini distribuidor, incluso el difusor que te hacÃ­a falta. Agradecelo', 20);
INSERT INTO `faq` VALUES(55, 'goteo', 'project', 'Â¿QuÃ© son los retornos colectivos y como pensarlos? ', 'la gran apuesta, el valor de goteo. si te cuesta pensar en como introducir \\"procomÃºn\\" en tu sector y la guÃ­a del formulario no es bastante explicita, hablemos', 14);
INSERT INTO `faq` VALUES(56, 'goteo', 'project', 'Â¿Las necesidades del proyecto pueden ser modificadas una vez en campaÃ±a?', 'SÃ­. Lo Ãºnico que no puedes editar es el objetivo de financiaciÃ³n y las recompensas. A cambio te invitamos a hacer uso de tu dashboard para dinamizar y actualizar regularmente tu proyecto tanto en la fase de busqueda de financiaciÃ³n como doblemente despues de haber obtenido el minimo y ponerte en marcha. Este feedback hacia tus cofinanciadores y colaboradores es primordial a la hora de llegar al optÃ­mo. Pero te recomendamos de planificar bien las posibles tareas y objetivos del proyecto en este periodo de rapid prototyping que son 40 dÃ­as', 12);
INSERT INTO `faq` VALUES(57, 'goteo', 'project', 'Â¿QuÃ© herramientas tengo para administrar mi proyecto?', 'Cada creador, dispone de un DASHBOARD (pÃ¡gina privada de administraciÃ³n) que le sirve de centro de operaciones para dinamizar y gestionar su proyecto. Publicar actualizaciones, aÃ±adir fotos y vÃ­deos, clasificar las aportaciones de los cofinanciadores, gestionar los envios de las recompensas individuales. <br>\r\nSAMPLE VERKAMI AdemÃ¡s, tienes toda la informaciÃ³n actualizada sobre el estado de tu proyecto, con la cantidad recaudada, niveles de aportaciones y nÃºmero de mecenas.â€¨TambiÃ©n puedes acceder al listado de mecenas, donde tienes detallado quiÃ©n y cuÃ¡nto ha aportado. Esto te permitirÃ¡ enviarles mensajes, ya sea a todos en general o personalizados, segÃºn tus necesidades. SAMPLE VERKAMI', 17);
INSERT INTO `faq` VALUES(58, 'goteo', 'investors', 'Â¿QuiÃ©n puede aportar financiaciÃ³n a proyectos en Goteo?', 'Cualquier ciudadano, empresa o instituciÃ³n en el mundo puede:<br>\r\n- conocer, opinar y establecer conversaciones en linea sobre los proyectos llevados a cabo\r\npor creadores y emprendedores culturales de EspaÃ±a;\r\n<br>- apoyarlos mediante una (micro)donaciÃ³n o una (micro)colaboraciÃ³n, es decir haciendo\r\ndonaciones financieras (desde 5 euros) o colaborando segÃºn las competencias y los\r\nmedios de cada uno;\r\n<br>- beneficiarse del acceso a los retornos digitales (contenidos, cursos o asesorÃ­as por Internet, herramientas digitales de cÃ³digo abierto, etc) que el creador ha decidido ofrecer a la comunidad en las condiciones que dictan las licencias creative commons que Ã©ste ha\r\nelegido', 1);
INSERT INTO `faq` VALUES(59, 'goteo', 'project', 'Â¿Cual es el compromiso del creador con Goteo y los Cofinanciadores?', 'Cuando el proyecto es aceptado por la comunidad de Goteo, en el momento de conseguir la financiaciÃ³n minÃ­ma, se formaliza el contrato disponible desde el primer momento en su dashboard, donde el autor se compromete a llevar a cabo el proyecto y dar acceso a los contenidos o servicios que haya definido como retorno colectivo (a la comunidad) requerida.<br><br>En el caso de las recompensas individuales, la relaciÃ³n solo pone en juego el compromiso entre el creador y sus cofinanciadores', 11);

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

INSERT INTO `icon` VALUES('code', 'CÃ³digo fuente', '', 'social', 0);
INSERT INTO `icon` VALUES('design', 'DiseÃ±o', '', 'social', 0);
INSERT INTO `icon` VALUES('file', 'Archivos digitales', '', NULL, 0);
INSERT INTO `icon` VALUES('manual', 'Manuales', '', 'social', 0);
INSERT INTO `icon` VALUES('money', 'Dinero', '', 'individual', 0);
INSERT INTO `icon` VALUES('other', 'Otro', '', NULL, 99);
INSERT INTO `icon` VALUES('product', 'Producto', '', 'individual', 0);
INSERT INTO `icon` VALUES('service', 'Servicios', '', NULL, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos' AUTO_INCREMENT=162 ;

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
INSERT INTO `invest` VALUES(161, 'root', '8851739335520c5eeea01cd745d0442d', 'julian_1302552287_per@gmail.com', 50, 1, NULL, 1, '2011-07-07', '2011-07-07', NULL, NULL, NULL, NULL, 'cash', 'goteo', 2);

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

INSERT INTO `invest_address` VALUES(1, 'abenitez', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(2, 'abenitez', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(3, 'ahernandez', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(4, 'ahernandez', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(5, 'aollero', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(6, 'aollero', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(7, 'asanz', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(8, 'asanz', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(9, 'aballesteros', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(10, 'aballesteros', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(11, 'arecio', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(12, 'amunoz', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(13, 'amunoz', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(14, 'aramos', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(15, 'ccriado', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(16, 'ccriado', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(17, 'cpinero', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(18, 'cpinero', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(19, 'ibelloso', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(20, 'ibelloso', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(21, 'lemontero', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(22, 'lemontero', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(23, 'mpalma', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(24, 'mpalma', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(25, 'stena', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(26, 'vsantiago', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(27, 'vsantiago', NULL, NULL, NULL, NULL);
INSERT INTO `invest_address` VALUES(161, 'root', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest_reward`
--

DROP TABLE IF EXISTS `invest_reward`;
CREATE TABLE `invest_reward` (
  `invest` bigint(20) unsigned NOT NULL,
  `reward` bigint(20) unsigned NOT NULL,
  `fulfilled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Recompensa individual cumplida',
  UNIQUE KEY `invest` (`invest`,`reward`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';

--
-- Volcar la base de datos para la tabla `invest_reward`
--


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

INSERT INTO `lang` VALUES('ca', 'CatalÃ ', 0);
INSERT INTO `lang` VALUES('de', 'Deutsch', 0);
INSERT INTO `lang` VALUES('en', 'English', 0);
INSERT INTO `lang` VALUES('es', 'EspaÃ±ol', 1);
INSERT INTO `lang` VALUES('eu', 'Euskara', 0);
INSERT INTO `lang` VALUES('fr', 'FranÃ§ais', 0);

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

INSERT INTO `license` VALUES('agpl', 'Affero General Public License', 'GNU Affero General Public License', '', 'http://www.affero.org/oagf.html', 1);
INSERT INTO `license` VALUES('apache', 'Apache License', 'Apache License', '', 'http://www.apache.org/licenses/LICENSE-2.0', 10);
INSERT INTO `license` VALUES('balloon', 'Balloon Open Hardware License', 'Balloon Open Hardware License', '', 'http://balloonboard.org/licence.html', 20);
INSERT INTO `license` VALUES('bsd', 'Berkeley Software Distribution', 'BSD (Berkeley Software Distribution)', 'open', 'http://es.wikipedia.org/wiki/Licencia_BSD', 4);
INSERT INTO `license` VALUES('cc0', 'CC0 Universal', 'CC0 Universal', '', 'http://creativecommons.org/publicdomain/zero/1.0/', 25);
INSERT INTO `license` VALUES('ccby', 'CC - Reconocimiento', 'Creative Commons - Reconocimiento (by)', 'open', 'http://creativecommons.org/licenses/by/2.0/', 12);
INSERT INTO `license` VALUES('ccbync', 'CC - Reconocimiento - NoComercial', 'Creative Commons - Reconocimiento - NoComercial (by-nc)', '', 'http://creativecommons.org/licenses/by-nc/2.0/', 13);
INSERT INTO `license` VALUES('ccbyncnd', 'CC - Reconocimiento - NoComercial - SinObraDerivada', 'Creative Commons - Reconocimiento - NoComercial - SinObraDerivada (by-nc-nd)', '', 'http://creativecommons.org/licenses/by-nc-nd/2.0/', 15);
INSERT INTO `license` VALUES('ccbyncsa', 'CC - Reconocimiento - NoComercial - CompartirIgual', 'Creative Commons - Reconocimiento - NoComercial - CompartirIgual (by-nc-sa)', '', 'http://creativecommons.org/licenses/by-nc-sa/3.0/', 14);
INSERT INTO `license` VALUES('ccbynd', 'CC - Reconocimiento - SinObraDerivada', 'Creative Commons - Reconocimiento - SinObraDerivada (by-nd)', '', 'http://creativecommons.org/licenses/by-nd/2.0/', 18);
INSERT INTO `license` VALUES('ccbysa', 'CC - Reconocimiento - CompartirIgual', 'Creative Commons - Reconocimiento - CompartirIgual (by-sa)', 'open', 'http://creativecommons.org/licenses/by-sa/2.0/', 16);
INSERT INTO `license` VALUES('fal', 'Free Art License', 'Free Art License', '', 'http://artlibre.org/licence/lal/en', 11);
INSERT INTO `license` VALUES('fdl', 'Free Documentation License ', 'GNU Free Documentation License (FDL)', 'open', 'http://www.gnu.org/licenses/fdl.html', 3);
INSERT INTO `license` VALUES('freebsd', 'FreeBSD documenaciÃ³n', 'Licencia de DocumentaciÃ³n de FreeBSD', 'open', 'http://www.freebsd.org/about.html', 6);
INSERT INTO `license` VALUES('gpl', 'General Public License', 'GNU General Public License (GPL) GPLv3', 'open', 'http://www.gnu.org/licenses/gpl.html', 5);
INSERT INTO `license` VALUES('lgpl', 'Lesser General Public License', 'GNU Lesser General Public License', 'open', 'http://www.gnu.org/copyleft/lesser.html', 2);
INSERT INTO `license` VALUES('mit', 'MIT or X11', 'MIT (or X11 license)', '', 'http://es.wikipedia.org/wiki/MIT_License', 8);
INSERT INTO `license` VALUES('mpl', 'Mozilla Public License', 'Mozilla Public License (MPL)', '', 'http://www.mozilla.org/MPL/', 7);
INSERT INTO `license` VALUES('odbl', 'Open Database License ', 'Open Database License (ODbL)', 'open', 'http://www.opendatacommons.org/licenses/odbl/', 22);
INSERT INTO `license` VALUES('odcby', 'Open Data Commons Attribution License', 'Open Data Commons Attribution License (ODC-by)', 'open', 'http://www.opendatacommons.org/licenses/by/', 23);
INSERT INTO `license` VALUES('oshw', 'Open Source Hardware', 'OSHW (Open Source Hardware)', 'open', 'http://en.wikipedia.org/wiki/Open-source_hardware', 17);
INSERT INTO `license` VALUES('pd', 'Public domain', 'Public domain', '', 'http://creativecommons.org/licenses/publicdomain/', 24);
INSERT INTO `license` VALUES('php', 'Licencia PHP', 'Licencia PHP', '', 'http://www.php.net/license/', 9);
INSERT INTO `license` VALUES('tapr', 'Noncommercial Hardware License', 'TAPR Noncommercial Hardware License ("NCL")', '', 'http://www.tapr.org/ohl.html', 19);
INSERT INTO `license` VALUES('xoln', 'Red Abierta, Libre y Neutral', 'Red Abierta, Libre y Neutral', 'open', 'http://guifi.net/es/ProcomunXOLN', 21);

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
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede modificar ni  borrar',
  `closed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede responder',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Mensajes de usuarios en proyecto' AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `message`
--

INSERT INTO `message` VALUES(2, 'esenabre', 'pliegos', NULL, '2011-07-07 02:39:20', 'Pilas: Dejadmelas :)', 1, 0);
INSERT INTO `message` VALUES(3, 'efoglia', 'nodo-movil', NULL, '2011-07-11 10:32:47', 'Desarrolladores: Desarrolladores de Exo.cat, grupo Manet. Expertos en streaming y telefonÃ­a mÃ³vil..', 1, 0);
INSERT INTO `message` VALUES(4, 'efoglia', 'nodo-movil', NULL, '2011-07-11 10:32:47', 'Espacio de trabajo: Sala de hackeo / trabajo / reuniÃ³n. para 10 personas.', 1, 0);
INSERT INTO `message` VALUES(5, 'efoglia', 'nodo-movil', NULL, '2011-07-11 10:32:47', 'Espacio pÃºblico: Espacio pÃºblico (accesible para trabajar ).', 1, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Noticias en la cabecera' AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `news`
--

INSERT INTO `news` VALUES(2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ornare vehicula nisi in tempor. Sed', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ornare vehicula nisi in tempor. Sed Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ornare vehicula nisi in tempor. SedLorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ornare vehicula nisi in tempor. SedLorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ornare vehicula nisi in tempor. Sed', 'http://google.es', 2);

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

INSERT INTO `page` VALUES('about', 'About', 'Sobre Goteo', '/about');
INSERT INTO `page` VALUES('community', 'Comunidad Goteo', 'Contenido seccion comunidad', '/community');
INSERT INTO `page` VALUES('contact', 'Contacto', 'Mensaje de contacto', '/about/contact');
INSERT INTO `page` VALUES('credits', 'CrÃ©ditos', 'CrÃ©ditos', '/about/credits');
INSERT INTO `page` VALUES('dashboard', 'Bienvenida', 'Texto de bienvenida en el dashboard', '/dashboard');
INSERT INTO `page` VALUES('howto', 'Instrucciones', 'Instrucciones para ser productor', '/about/howto');
INSERT INTO `page` VALUES('legal', 'Legales', 'Terminos y condiciones de uso', '/about/legal');
INSERT INTO `page` VALUES('news', 'Noticias', 'Pagina de noticias', '/news');
INSERT INTO `page` VALUES('review', 'Revision', 'Texto explicativo el panel revisor', '/review');
INSERT INTO `page` VALUES('team', 'Equipo', 'Sobre la gente detrÃ¡s de Goteo', '/about/team');

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
INSERT INTO `page_node` VALUES('beta', 'goteo', 'es', '<h3>\r\n	Explicaci&oacute;n</h3>\r\n<p>\r\n	Estamos en fase beta de testeo y los aportes son de prueba. Solamente usuarios betatesters pueden realizar aportes de prueba.</p>\r\n');
INSERT INTO `page_node` VALUES('community', 'goteo', 'es', 'Contenido seccion comunidad');
INSERT INTO `page_node` VALUES('contact', 'goteo', 'es', '<p>\r\n	Texto explicativo en la p&aacute;gina de contacto. Se gestiona desde las p&aacute;ginas institucionales</p>\r\n');
INSERT INTO `page_node` VALUES('credits', 'goteo', 'es', '<p>\r\n	Desarrollado por <a href="http://onliners-web.com" target="_blank" title="Onliners Web Development">Onliners Web Development</a></p>\r\n<p>\r\n	&nbsp;</p>\r\n');
INSERT INTO `page_node` VALUES('dashboard', 'goteo', 'es', '<p>\r\n	Hola %USER_NAME%,<br />\r\n	bienvenido a tu panel.</p>\r\n<p>\r\n	Desde aqu&iacute; podr&aacute;s blahblahbvlah en <a href="/dashboard/profile/personal">LINK</a>, asdfasd asdfasdf en LINK y qwerqw qwer qwer en LINK</p>\r\n');
INSERT INTO `page_node` VALUES('howto', 'goteo', 'es', '<h3>\r\n	Instrucciones para crear un proyecto.</h3>\r\n<p>\r\n	Blablablabla</p>\r\n<p>\r\n	Recuerda que necesistas ser un usuario registrado y logueado.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<input class="checkbox" id="create_accept" name="guidelines_accept" type="checkbox" value="1" /> <label class="unselected" for="create_accept">He leido y entiendo las instrucciones para crear un proyecto en Goteo.</label></p>\r\n<p>\r\n	<a class="button-positive disabled" disabled="disabled" href="/project/create">Continuar</a></p>\r\n');
INSERT INTO `page_node` VALUES('news', 'goteo', 'es', '<p>\r\n	Contenido de la pagina noticias</p>\r\n');
INSERT INTO `page_node` VALUES('review', 'goteo', 'es', '<p>\r\n	Hola %USER_NAME%,<br />\r\n	bienvenido a tu panel de revisor.</p>\r\n<p>\r\n	Desde aqu&iacute; podr&aacute;s blahblahbvlah en <a href="/review/activity/summary">LINK</a>, asdfasd asdfasdf en LINK y qwerqw qwer qwer en LINK</p>\r\n');

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
  `footer` tinyint(1) DEFAULT '0' COMMENT 'Para los del footer',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada' AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `post`
--

INSERT INTO `post` VALUES(1, 1, 'Â¿Porque goteo es diferente?', 'Aqui el video que explica los diferenciales con las otras plataformas. Goteo se distingue principalmente de otras plataformas por su apuesta diferencial y focalizada en proyectos de cÃ³digo abierto1, que comparten conocimiento, procesos, resultado, responsabilidad y benÃ©fico, desde la filosofÃ­a del procomÃºn. Goteo pone el acento en la misiÃ³n pÃºblica, apoyando proyectos que favorezcan el empoderamiento colectivo y el bien comÃºn.', 'http://vimeo.com/20597320', 0, '2011-06-01', 1, 1, 1, 0);
INSERT INTO `post` VALUES(2, 1, 'asdasdf', 'asdf asdf asd', NULL, NULL, '0000-00-00', 1, 1, NULL, 1);
INSERT INTO `post` VALUES(3, 1, 'aaaaaaaaaaaaaaaaaaaaa', 'aaaaaaaaaaaaaaaaaaaaaaaaaaa', '', 74, '0000-00-00', 3, 0, 1, 1);
INSERT INTO `post` VALUES(4, 1, 'df g sdfg sdfg sdfg ', NULL, NULL, NULL, '0000-00-00', 2, 1, 1, NULL);

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

INSERT INTO `project` VALUES('2c667d6a62707f369bad654174116a1e', 'NO SLEEP TO BROOKLYN', 3, 67, 'olivier', 'goteo', 10, 8, '2011-05-12', '2011-07-04', '2011-06-09', NULL, NULL, '', '', '', '', '', '', '', '', 'te has despertado en NYC?', 'cyberpunk', 'una canciÃ³n de los beastie', 'cyberpunk', 'un monton de gente', NULL, 'educaciÃ³n, copyleft, manchego', 'http://vimeo.com/15153640', 3, 'paris', NULL, '', '');
INSERT INTO `project` VALUES('3d72d03458ebd5797cc5fc1c014fc894', 'Mi proyecto 2', 1, 28, 'olivier', 'goteo', 0, 0, '2011-07-04', NULL, NULL, NULL, NULL, 'Olivier Schulbaum', '', '667031530', '', '', 'Palma de Mallorca', 'EspaÃ±a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Palma de Mallorca', NULL, NULL, NULL);
INSERT INTO `project` VALUES('8851739335520c5eeea01cd745d0442d', 'Pruebas Julian', 1, 36, 'root', 'goteo', 50, 0, '2011-07-05', '2011-07-08', NULL, NULL, NULL, 'Super administrador', '', '', '', '', '', 'EspaÃ±a', '', '', '', '', '', '', NULL, '', '', 0, '', 0, '', '');
INSERT INTO `project` VALUES('a565092b772c29abc1b92f999af2f2fb', 'Tweetometro', 3, 52, 'goteo', 'goteo', 0, 34, '2011-07-03', '2011-07-05', '2011-07-05', NULL, NULL, '', '', '', '', '', '', '', '65', 'Plataforma experimental de votaciÃ³n mediante tweets. En alfa! Una aplicaciÃ³n para llegar a acuerdos, tomar decisiones colectivamente o elegir la mejor idea presentada, mediante twitter y sms (en desarrollo).', '', '', '', '', NULL, '', '', 0, '', 0, '', '');
INSERT INTO `project` VALUES('a9277be1c7e92eaa36ecae753231bfb1', 'Mi proyecto 3', 1, 42, 'goteo', 'goteo', 0, 0, '2011-07-11', '2011-07-12', NULL, NULL, NULL, 'Goteo Platoniq', '', '', '', '', 'mhkjghsdkj fkj sadkjf', 'EspaÃƒÂ±a', '', '', '', '', '', '', NULL, '', '', 1, 'mhkjghsdkj fkj sadkjf', 4, '', NULL);
INSERT INTO `project` VALUES('archinhand-architecture-in-your-hand', 'ARCHINHAND | Architecture in your Hand', 1, 52, 'ebaraonap', 'goteo', 50, 39, '2011-05-13', '2011-07-05', '2011-07-05', '0000-00-00', '2011-07-05', '', '', '', '', '', 'Barcelona', 'EspaÃ±a', '', 'Archinhand es un proyecto editorial de difusiÃ³n de contenidos sobre arquitectura y ciudad a travÃ©s de dispositivos mÃ³viles.\r\nLa subversiÃ³n de las parcelas editoriales tradicionales, las nuevas formas de aprendizaje, la difusiÃ³n de los lÃ­mites entre espacio pÃºblico y privado y la creciente implantaciÃ³n de dispositivos mÃ³viles, constituyen los puntos de partida del proyecto.', '', '\r\nArchinhand enfoca el aprendizaje de la ciudad y los espacios mediante la experiencia directa. La informaciÃ³n sobre arquitectura y ciudad vista como servicio no como producto, en conexiÃ³n con el espacio circundante. El aprendizaje guiado por la curiosidad y no por un curriculum acadÃ©mico.\r\n\r\nLos contenidos se canalizan en tres lÃ­neas bÃ¡sicas de informaciÃ³n: blogs, ciudad y libros. La estructura del proyecto permite enlazar los contenidos de las tres lÃ­neas ampliando la experiencia a la vez que se adapta a la lÃ³gica de comunicaciÃ³n mÃ³vil.\r\n\r\nURL proyecto:www.archinhand.com\r\n\r\nSector al que va dirigido:Arquitectos, Estudiantes de arquitectura en paises emergentes, urbanistas, viajeros con intereses en arquitectura\r\n', '', 'ANTECEDENTES:\r\n\r\n-En fase inicial el proyecto "Archinhand" fue ganador en el Urbanlabs 09 del CitiLab <bit.ly/dC2CiE>\r\n\r\nPROMOTOR EDITORIAL:\r\n\r\ndpr-barcelona promotora del proyecto es una editorial sobre arquitectura y ciudad en constante innovaciÃ³n en la transmisiÃ³n de contenidos al publico: www.dpr-barcelona.com/\r\n\r\nLos promotores editoriales, Ethel Baraona Pohl y CÃ©sar Reyes son arquitectos. Cuentan con:\r\n\r\n-Experiencia de 7 aÃ±os en el mundo editorial de arquitectura. Contactos y gestiÃ³n de contenidos para editoriales y revistas especializadas.\r\n\r\nEn dpr-barcelona el networking y las plataformas de trabajo colaborativo constituyen la forma de trabajo habitual, a modo de ejemplo pueden citarse:\r\n\r\n-PublicaciÃ³n del primer libro de arquitectura "sin papel" [Piel.Skin 2007] <skinarchitecture.com/ >\r\n-CoordinaciÃ³n del lanzamiento simultaneo en 5 ciudades de "Alguien Dijo Participar" el 11 Septiembre de 2009 <bit.ly/n12ll>\r\n-Red de Colaboraciones y contactos directos en los blogs de arquitectura mas relevantes a nivel mundial tanto por trafico de visitas como por calidad de contenidos.\r\n-Amplia Base de datos sobre proyectos y experiencias en arquitectura fruto de una extensa red 2.0 de contactos, colaboradores y despachos de arquitectura en los cinco continentes.\r\n-CoordinaciÃ³n y realizaciÃ³n de experiencias acadÃ©micas usando plataformas on line y presenciales en colaboraciÃ³n con equipos como Ecosistema Urbano y radarq <bit.ly/bPqEDi>\r\n\r\nPor su trabajo editorial en red y constante bÃºsqueda de innovaciÃ³n han sido invitados a eventos como:\r\n\r\n-Postopolis, MÃ©xico DF <postopolis.org/>\r\n-Bookcamp - Kosmopolis, Barcelona <bit.ly/cfwmoQ>\r\n-Mercado AtlÃ¡ntico de CreaciÃ³n ContemporÃ¡nea (MACC), Santiago de Compostela <bit.ly/aHOFMQ>\r\n-HOY sistemas de Trabajo, Madrid <bit.ly/a05QOg>\r\n-KAM Workshops, Atenas <bit.ly/9idEg5>\r\n-Eme3, Barcelona <bit.ly/bkwDfE>\r\n\r\nActividad acadÃ©mica como lecturers o asesores:\r\nUniversitat Politecnica de Catalunya, Universitat de Barcelona, Arquiredes Motril, Esarq-UIC, Calgary University, International Campus Ultzama\r\n\r\nPROMOTORES TECNOLOGICOS:\r\n\r\nClaimSoluciones es el socio tecnolÃ³gico involucrado en el proyecto. Es un estudio de comunicaciÃ³n que utiliza la Estrategia y la ProducciÃ³n Visual como herramientas principales. Desarrollan proyectos de comunicaciÃ³n en Web, diseÃ±o grÃ¡fico, producciÃ³n audiovisual, below media. Esta formado por Sergio JimÃ©nez y Jakob Renpening', '', 'editorial, arquitectura, ciudad, AR, blogs', '', 0, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('canal-alfa', 'Canal Alfa', 1, 47, 'geraldo', 'goteo', 0, 0, '2011-05-13', '2011-07-05', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Barcelona', 'EspaÃ±a', '', 'Canalalpha es un proyecto que quiere aplicar el concepto del sample al vÃ­deo. Entendiendo sample como una unidad mÃ­nima musical, muy expandida en la mÃºsica electrÃ³nica desde los aÃ±os 90. El proyecto quiere llevar el equivalente del sample al vÃ­deo mÃ¡s allÃ¡ del conocido termino â€œloopâ€, aplicÃ¡ndolo a unidades que puedan tener independencia dentro del mismo vÃ­deo gracias a un canal de transparencia. De esta manera, un vÃ­deo se entiende como la composiciÃ³n de sus partes (al estilo de capas) que actÃºan como entidades independientes que pueden apagarse o encenderse, ir a una velocidad diferente hacia delante o hacia atrÃ¡s, tener tamaÃ±os distintos (incluso variables durante la reproducciÃ³n) e incluso posicionamientos no estÃ¡ndares dentro de la composiciÃ³n (warping).\r\n\r\nEl marco de acciÃ³n del proyecto se compone de estos 3 ejes que se formalizan en un portal web hasta ahora inexistente:\r\n\r\n1. Una base de datos abierta y libre (CC) de samples de vÃ­deo - incluyendo un canal de transparencia - que representen unidades bÃ¡sicas que puedan ser utilizadas para crear composiciones.\r\n\r\n2. Herramientas que permiten extraer objetos de vÃ­deos para esta base de datos, incluyendo la posibilidad de extraer partes de los vÃ­deos existentes en los portales de vÃ­deo tales como youtube. Los mÃ©todos para la extracciÃ³n de samples de vÃ­deos ya existentes se basan en tÃ©cnicas de visiÃ³n por computador: substracciÃ³n de fondo (background substraction), detecciÃ³n de movimiento, umbral de color, umbral de intensidad. La intenciÃ³n es ir mas allÃ¡ del ya conocido chroma-key, tan usado en televisiÃ³n.\r\n\r\n3. Una plataforma para obras creadas con samples de vÃ­deos de la base de datos pÃºblica de canalalpha entendidos como poemas visuales animados, interactivos y sujetos a una narrativa cambiante.', '', 'URL proyecto: www.canalalpha.net/\r\n\r\nSector al que va dirigido: artistas visuales', '', 'Gerald Kogler es especialista en diseÃ±o de sistemas interactivos y software libre. Desarrolla aplicaciones web y instalaciones interactivas de forma autÃ³noma y dicta asignaturas de programaciÃ³n en diversas universidades de Barcelona. Forma parte de ZZZINC: zzzinc.net/\r\n\r\nProyectos destacados:\r\n- casastristes - Herramientas de visualizaciÃ³n para una vivienda digna: casastristes.org/\r\n- Independent Robotic Community: mediainterventions.net/comunidad/\r\n- Museu de la Patum de Berga: museu.lapatum.cat/\r\n\r\nMarti Sanchez es investigador en inteligencia artificial en la Universidad Pompeu Fabra, departamento SPECS. Como artista de medios interactivos y audiovisuales colabora con KonicTheatr, compaÃ±Ã­a Sr. Serrano y Institut Fatima realizando numerosas instalaciones interactivas, una de ellas presentada en el centro ZKM y ganadora del premio ciudad de Barcelona (MurMuros de Konic Theatr).\r\n\r\nProyectos destacados:\r\n- Mixed Reality Robot Arena: www.youtube.com/watch?v=HDmjGJ9sqeI\r\n- Sr. Serrano // Contra.Natura: www.youtube.com/watch?v=3RNvxH5cKX0\r\n- Sr. Serrano // Artefact: www.youtube.com/watch?v=opMfQ-hbUD0', '', '', '', 0, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('fe99373e968b0005e5c2406bc41a3528', 'Fixie per tothom', 3, 70, 'diegobus', 'goteo', 10, 16, '2011-05-10', '2011-07-06', '2011-06-17', NULL, '2011-06-01', 'diego', 'x8562415k', '658125454', 'c/ calle 98, 1Âº 2Âº', '08000', 'barcelona', 'EspaÃ±a', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla dolor quis elit cursus sit amet tincidunt elit ultrices. Mauris ultricies auctor velit vel tempor. Integer sollicitudin consequat ultrices. Fusce at ante sit amet augue dapibus mattis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam facilisis fermentum vestibulum. Mauris accumsan, ante nec aliquet porttitor, ipsum diam elementum elit, at volutpat nisl nibh in quam. Nunc aliquet arcu quis erat ultricies tristique. Nunc laoreet odio vitae quam porta tincidunt. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur diam augue, lacinia in eleifend id, mollis facilisis sem. Pellentesque suscipit dolor id nisl elementum in blandit turpis tempus. Mauris nec libero dolor, sed volutpat eros. Morbi hendrerit, tellus consequat dictum interdum, massa tellus ornare ante, vitae venenatis ipsum mi viverra urna. Proin varius pulvinar lobortis. Integer luctus tellus vel elit adipiscing feugiat. In hac habitasse platea dictumst. Fusce porta eros molestie orci dignissim mollis. Cras volutpat, turpis a tempus commodo, sapien sem porttitor eros, vitae dapibus nisl ante ut nisi. Pellentesque gravida vehicula ipsum id bibendum. ', 'Pellentesque gravida vehicula ipsum id bibendum. ', 'Proin varius pulvinar lobortis. Integer luctus tellus vel elit adipiscing feugiat. In hac habitasse platea dictumst. Fusce porta eros molestie orci dignissim mollis. Cras volutpat, turpis a tempus commodo, sapien sem porttitor eros, vitae dapibus nisl ante ut nisi. Pellentesque gravida vehicula ipsum id bibendum. ', 'Fusce at ante sit amet augue dapibus mattis. \r\n\r\nClass aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. \r\n\r\nNullam facilisis fermentum vestibulum. Mauris accumsan, ante nec aliquet porttitor, ipsum diam elementum elit, at volutpat nisl nibh in quam. Nunc aliquet arcu quis erat ultricies tristique. Nunc laoreet odio vitae quam porta tincidunt.\r\n\r\nPellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur diam augue, lacinia in eleifend id, mollis facilisis sem. Pellentesque suscipit dolor id nisl elementum in blandit turpis tempus. Mauris nec libero dolor, sed volutpat eros. Morbi hendrerit, tellus consequat dictum interdum, massa tellus ornare ante, vitae venenatis ipsu.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla dolor quis elit cursus sit amet tincidunt elit ultrices. Mauris ultricies auctor velit vel tempor. Integer sollicitudin consequat ultrices. Fusce at ante sit amet augue dapibus mattis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam facilisis fermentum vestibulum. Mauris accumsan, ante nec aliquet porttitor, ipsum diam elementum elit, at volutpat nisl nibh in quam. Nunc aliquet arcu quis erat ultricies tristique. Nunc laoreet odio vitae quam porta tincidunt. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur diam augue, lacinia in eleifend id, mollis facilisis sem. Pellentesque suscipit dolor id nisl elementum in blandit turpis tempus. Mauris nec libero dolor, sed volutpat eros.', NULL, 'Nunc, tristique', 'http://www.youtube.com/watch?v=RpNAofXemdo&feature=related', 1, 'barcelona', 0, 'Nulla pellentesque mi at dolor accumsan accumsan. Praesent arcu nibh, sollicitudin eu fringilla fringilla, rhoncus et mi. Sed sed pretium ipsum. Vestibulum non velit nibh, non dignissim eros. Phasellus varius sodales accumsan.', '');
INSERT INTO `project` VALUES('goteo', 'Goteo', 1, 47, 'goteo', 'goteo', 340, 0, '2011-05-13', '2011-07-07', '0000-00-00', '0000-00-00', '0000-00-00', 'Susana Noguero', 'G63306914', '654321987', 'C/ Montealegre, 5', '8001', 'Barcelona', 'EspaÃ±a', '', '', '', '', '', '', '', '', 'http://www.youtube.com/watch?v=h5aRPhsPaWU', 0, '', 0, '', '');
INSERT INTO `project` VALUES('hkp', 'Hkp.', 1, 53, 'tintangibles', 'goteo', 70, 0, '2011-05-13', '2011-07-05', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Barcelona', 'EspaÃ±a', '', 'HKp es un proyecto audiovisual y editorial sobre los â€œhackersâ€ de lo cotidiano. Documenta ejemplos de artefactos (objetos, herramientas, muebles, aparatos, mÃ¡quinas, software, vehÃ­culos), a los que sus usuarios han dado un uso distinto del que tenÃ­an originariamente haciendo modificaciÃ³n o ensamblando partes de otros artefactos.\r\n\r\nToma el tÃ©rmino hack del mundo del software que expresa un tipo de intervenciÃ³n que corrige o modifica el funcionamiento de un programa.\r\n\r\nHKp investiga en que medida esta manera de pensar y hacer tiene sus raÃ­ces en algo que forma parte de la naturaleza humana.\r\nUn propÃ³sito del proyecto es detectar y dar visibilidad a un tipo de creatividad que se produce en varios sectores: entorno rural, talleres, industria, hogares, hackers, prÃ¡cticas artÃ­sticas, etc.\r\n\r\nRecomendamos ver algunos ejemplos de, como reciclar una cafetera para fundir plomo, llantas de coche como barbacoa, paraguas como falda impermeable, una rueda de bici como rueda para hilar, una estufa de leÃ±a a partir de un compresor de aire en el siguiente enlace:\r\nenlloc.net/hkp/w/index.php/Hacks', '', 'URL proyecto:enlloc.net/hkp/w/\r\n\r\nSector al que va dirigido:un propÃ³sito del proyecto es detectar y dar visibilidad a un tipo de creatividad que se produce en varios sectores: entorno rural, talleres, indÃºstria, hogares, hackers, hacktivistas, prÃ¡cticas artÃ­sticas, ...', '', 'Desde el TAG se han impulsado proyectos como:\r\n\r\nGERMINADOR de propuestas de creaciÃ³n colectiva - germinador.net\r\nDesde 2005\r\n\r\nPLANTER software de creaciÃ³n colectiva - planter.germinador.net\r\nDesarrollo de Wikipool y Telps\r\nCon Pimpampum (Dani JuliÃ  y Anna Fuster) y Jaume Nualart\r\n2007\r\n\r\nHKp apropiaciÃ³n creativa de los usuarios - enlloc.net/hkp/w\r\nCon Joan Montserrat\r\nprimera fase 2009\r\n\r\nSopadepedres - enlloc.net/sopadepedres\r\nActividades en MaÃ§art (2006) y con el IES A.Deulofeu (2008)\r\n\r\nDesde el colectivo desde 1996 se han realizado intervenciones urbanas (dreceres urbanes, xMatarÃ³, ciutats de cordill), instalaciones (AMC, Argila, Cajacabeza), talleres (net.art, deriva urbana, copyleft, creaciÃ³n colectiva) y proyectos de net.art como:\r\nTrencaclosques (2007) www.enlloc.org/trencaclosques\r\nCONSTITUCIÃ“N editar/discutir (2004-2007)\r\nA-PAM (DEL NAS) (2004) i APAMESNOSEMAPA (2007) - Premio CanariasMediafest 2004 www.enlloc.org/apamesnosemapa\r\nBalcons que diuen no a la guerra (2003)\r\nTol tol tol (1999) - Premio Barcelona MÃ¶bius 1999', '', 'apropiaciÃ³n tecnolÃ³gica, hack, arte, wiki, editorial', 'http://vimeo.com/16201682', 0, 'Barcelona', 2, '', '');
INSERT INTO `project` VALUES('mi-barrio', 'Mi barrio', 1, 47, 'itxaso', 'goteo', 0, 0, '2011-05-13', '2011-07-05', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Bilbao', 'EspaÃ±a', '', 'Mi barrio propone, y supone, la creaciÃ³n de una serie de microdocumentales de construcciÃ³n participativa por parte de los habitantes de los territorios en los que interviene, utilizando herramientas no invasivas y fÃ¡ciles de utilizar; telÃ©fonos mÃ³viles.\r\n\r\nMi barrio se constituye como un ejercicio documental participativo y plural que mapea la geografÃ­a nacional en busca de contenidos locales de interÃ©s general, fomentando la interacciÃ³n social, la colaboraciÃ³n y la capacitaciÃ³n tecnolÃ³gica\r\ny comunicativa de la ciudadanÃ­a.\r\n\r\nMi barrio presenta a los barrios y las periferias como entornos de innovaciÃ³n, investigaciÃ³n y cocreaciÃ³n que aprovecha el talento creativo, la diversidad sociocultural, la imaginaciÃ³n, la inventiva, e incluso los factores impredecibles, para reflejar las diferentes realidades que coexisten en los barrios y en las ciudades.', '', 'Sector al que va dirigido:Diferentes tramos de edades, etnias, procedencias de un mismo barrio', '', 'Describe quÃ© experiencia tienes (proyectos anteriores relevantes, organizaciones con o en las que has trabajado, si has dado clases ...). AÃ±ade la informaciÃ³n que te parezcan relevantes sobre tus capacidades, tus puntos fuertes y cÃ³mo los has aplicado en otros proyectos tuyos o de otros (incluye links a ser posible):\r\n\r\nOtros proyectos en los que he participado:\r\n- Proyecto Habla es un ejercicio documental participativo y formativo. Se trata de un â€dinamizador socialâ€ que pretende fomentar el empoderamiento de las comunidades mÃ¡s desfavorecidas, potenciando la participaciÃ³n e implicaciÃ³n ciudadana en dichos territorios. Este proyecto se ha realizado en colaboraciÃ³n con la ONGD Anesvad y se ha desarrollado entre los meses de agosto y noviembre de 2010 en PerÃº y Banglades.\r\nMÃ¡s info; www.ubiqa.com/seguimos-creyendo-que-es-posible%E2%80%A6-y-vamos-a-contarlo/\r\nwww.proyectohabla.org/, www.anesvad.tv/\r\n\r\n- Aldea DOC\r\nAldea DOC surge como respuesta a la peticiÃ³n de la ConcejalÃ­a de InnovaciÃ³n y e-Gobierno del Ayuntamiento de CÃ¡ceres para la realizaciÃ³n de un proyecto participativo sobre la situaciÃ³n del barrio de Aldea Moret de CÃ¡ceres.\r\nAldea DOC cartografÃ­a las anomalÃ­as, las zonas de transiciÃ³n del barrio Aldea Moret, donde la precariedad convive con prÃ¡cticas de ilegales, para fomentar la participaciÃ³n y la implicaciÃ³n ciudadana en el actual proceso de reconversiÃ³n de la zona.\r\nwww.ubiqa.com/aldea-doc/', '', 'construciÃ³n colaborativa, documental, capacitaciÃ³n tecnolÃ³gica, identidad, territorio', 'http://vimeo.com/12013746', 0, '', 0, '', '');
INSERT INTO `project` VALUES('move-commons', 'Move Commons', 1, 56, 'acomunes', 'goteo', 90, 39, '2011-05-13', '2011-07-06', '2011-07-05', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Madrid', 'EspaÃ±a', '', 'Move Commons consiste en una sencilla herramienta que permite que iniciativas, colectivos y ONGs puedan declarar los principios en los que se basan. Las caracterÃ­sticas a las que atiende Move Commons para describir un proyecto son si Ã©ste es distribuido, si la participaciÃ³n de sus miembros es horizontal, si se trata de una inciativa sin Ã¡nimo de lucro y si el proyecto refuerza el procomÃºn.\r\n\r\nExisten numerosas iniciativas promoviendo los bienes comunes en distintos campos. Sin embargo, sÃ³lo unas pocas han alcanzado masa crÃ­tica y pueden ser conocidas por una gran comunidad, mientras que la mayorÃ­a siguen marginadas e ignoradas. Move Commons (MC) es una herramienta que pretende potenciar la visibilidad y difusiÃ³n de dichas iniciativas, "dibujando" la red de iniciativas y colectivos relacionados en cualquier lugar, facilitando el descubrimiento mutuo y el alcance de masa crÃ­tica en cada campo. AdemÃ¡s, cualquier voluntario podrÃ¡ comprender el enfoque del colectivo fÃ¡cilmente y encontrar otros colectivos en su ciudad, de sus intereses, o de su campo en movecommons.org. MÃ¡s implicaciones en movecommons.org/implications\r\n', '- Elige tu MC en movecommons.org/preview\r\n- Las implicaciones de MC movecommons.org/implications\r\n- El blog de MC movecommons.org/blog', 'MC es una herramienta para iniciativas, colectivos, ONGs y movimientos sociales para que declaren los principios bÃ¡sicos a los que estÃ¡n comprometidos. Sigue la misma mecÃ¡nica de Creative Commons al "etiquetar" los trabajos culturales, proporcionando un sistema de auto-etiquetado estandarizado, usable, bottom-up, para cada iniciativa, con 4 iconos y algunas keywords. Todo estÃ¡ apoyado por contenido semÃ¡ntico para permitir bÃºsquedas del tipo Â«quÃ© iniciativas existen en Beirut que sean horizontales, sin Ã¡nimo de lucro, usando Creative Commons y relacionadas con "educaciÃ³n alternativa" y "adolescentes"Â» (o otros principios, palabras clave o lugares). Los cuatro principios/iconos que cada iniciativa puede mostrar son: Con/Sin Ã¡nimo de lucro; Reproducible/Exclusiva; Horizontal/JerÃ¡rquica; Reforzando los Comunes / Otros objetivos (explicados en movecommons.org/preview/ ).\r\nURL proyecto:movecommons.org\r\n\r\nSector al que va dirigido:Colectivos, ONGs, movimientos sociales, cooperativas, activistas, voluntarios', 'MC, aÃºn en un estado alfa de desarrollo, pretende proporcionar una plataforma libre donde mÃºltiples extensiones puedan ser implementadas, como un recomendador de iniciativas similares, mapeo geogrÃ¡fico de iniciativas, estadÃ­sticas y grÃ¡ficas sobre los datos abiertos, visualizaciÃ³n de las redes de colectivos, widgets para la web, etc.', 'Desde el aÃ±o 2002 el colectivo Comunes viene desarrollando diferentes iniciativas y proyectos relacionados con los bienes comunes. Comunes se centra en facilitar el trabajo de otras iniciativas y colectivos a travÃ©s de herramientas y recursos web. Algunos de nuestros proyectos: 1) ourproject.org: servicios web libres para proyectos sociales, actualmente con mÃ¡s de 800 proyectos de muy diversos campos; 2) Kune, kune.ourproject.org plataforma de creaciÃ³n de proyectos libres, en fase de desarrollo; 3) movecommons.org: herramienta web aquÃ­ descrita; y otras citadas en la web del colectivo comunes.org', '', 'herramienta, web, colectivos, ongs, movimientos sociales, activismo, voluntariado, creative commons, web semÃ¡ntica, bienes comunes, procomÃºn', '', 0, 'Madrid', 0, '', '');
INSERT INTO `project` VALUES('nodo-movil', 'Nodo MÃ³vil', 3, 52, 'efoglia', 'goteo', 255, 40, '2011-05-13', '2011-07-06', '2011-07-11', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Barcelona', 'EspaÃ±a', '', 'Es una estaciÃ³n de transmisiÃ³n libre, una infraestructura de telecomunicaciÃ³n inalÃ¡mbrica mÃ³vil, que se puede usar en el entorno urbano y permite el mallado digital a travÃ©s de redes ciudadanas. Cuenta con una capacidad de autonomÃ­a y configuraciÃ³n propia. Este Nodo MÃ³vil puede construir una LAN (Local Area Network) en territorios irregulares, conectÃ¡ndose entre sÃ­ de forma independiente a las empresas de telecomunicaciÃ³n.\r\nSus aplicaciones son multidisciplinares: educaciÃ³n, activismo, arte, primeros auxilios, etc. ', '', 'Este proyecto suma flexibilidad y propone posibilidades de interconexiÃ³n con los dispositivos digitales que habitan las ciudades contemporÃ¡neas. El Nodo MÃ³vil es un dispositivo de altas prestaciones, permite la transmisiÃ³n de datos con un ancho de banda potente.\r\nEl Nodo MÃ³vil incorpora movilidad a la red guifi.net, la extiende y provoca nuevas prÃ¡cticas sociales en el entorno urbano mezclando espacio pÃºblico fÃ­sico y digital.\r\n\r\nURL proyecto:www.proyectoliquido.com/Mobile_Node.htm\r\n\r\nSector al que va dirigido:Aplicaciones multidisciplinares: educaciÃ³n, activismo, arte, primeros auxilios, etc.', 'Actualmente tenemos 1 Nodo MÃ³vil en pruebas y nos interesa construir (con este financiamiento) un segundo NM.\r\nLo importante del proyecto es mejorar el diseÃ±o y su rendimiento, de esta forma se podrÃ¡ duplicar mÃ¡s facilmente y las ciudadanas podrÃ¡n acceder a esta tecnologÃ­a a bajo costo con rÃ©ditos sociales importantes.\r\nLo vital en este momento es tener recursos para seguirlo desarrollando.\r\nEl proyecto tiene varias fases de implementaciÃ³n. Se busca incorporar sistemas diversos de mallado como tecnologÃ­a GPS, microcontroladores, Bluetooth, etc.\r\nLugares: El proyecto tiene su base en Barcelona en donde actualmente se diseÃ±a, pero su conectividad es posible en casi todo el territorio catalÃ¡n en donde se encuentre un nodo de guifi.net. Los otros lugares citados en el mapa son posibilidades que ya cuentan con interlocutores interesados.', 'La filosofÃ­a detrÃ¡s del proyecto es la promovida por guifi.net a travÃ©s de los Ãºltimos 6 aÃ±os. Actualmente esta red ciudadana interconecta mÃ¡s de 11,000 nodos totalmente gestionados por los usuarios. \r\n\r\nAcÃ¡ hay una idea general:\r\n\r\nwww.efrainfoglia.net/', '', 'Open City, Xarxa Oberta, Movilidad, Espacio PÃºblico, Portabilidad.', 'http://www.youtube.com/watch?v=BOryyEv9qMA', 0, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('oh-oh-fase-2', 'Oh_Oh fase 2', 1, 50, 'dcuartielles', 'goteo', 70, 0, '2011-05-13', '2011-07-05', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Malmï¿½', 'Suecia', '', 'Oh_Oh es una plataforma robÃ³tica de bajo coste para ser usada en educaciÃ³n en secundaria. El primer prototipo fue financiado por el CCEMX como parte del proyecto "La Maquila del Faro de Oriente", que consistiÃ³ en la creaciÃ³n de actividades educativas basadas en el uso de hardware y software libre para chicos/as de edades comprendidas entre 10 y 18 anos. La creaciÃ³n de esta plataforma ha atraÃ­do el interÃ©s de otras personas interesadas en la robÃ³tica a nivel educativo por lo que me he dado cuenta que seria interesante dedicar algo mas de esfuerzo a concretar la plataforma a un nivel que sea sencilla de reproducir.\r\n\r\nLa idea de este proyecto es hacer una nueva interacciÃ³n del diseÃ±o de hardware asÃ­ como finalizar la revisiÃ³n del software con el que se cuenta en la actualidad para poder ofrecerlo de forma libre a aquellos interesados en la realizaciÃ³n de sus propios robots.', '', 'URL del proyecto: http://code.google.com/p/arduino-compatible-robots/wiki/Oh_Oh\r\n\r\nSector al que va dirigido: sistema educativo, secundaria, profesores del area de tecnologia, centros de formacion profesional, clubes de tiempo libre', '', 'Anteriormente he hecho:\r\n\r\n- creador del proyecto (ahora empresa) de hardware libre www.arduino.cc\r\n- creador de la empresa www.1scale1.com, Malmo, Suecia\r\n- creador del estudio de diseÃ±o Aeswad, Malmo, Suecia webzone.k3.mah.se\r\n\r\nDoy clases en la Universidad de Malmo, Suecia, desde el 2001, soy director del laboratorio de prototipos de productos.', '', 'electronica, software, tutoriales, educacion, comunidad', '', 3, 'MalmÃ¶, Suecia', 0, '', '');
INSERT INTO `project` VALUES('pliegos', 'PliegOS', 3, 70, 'esenabre', 'goteo', 200000040, 36, '2011-06-15', '2011-07-07', '2011-07-07', NULL, NULL, 'Enric Senabre Hidalgo', '46649545W', '932215515', 'Moscou 16, 1Âº 1Âª', '08005', 'Barcelona', 'EspaÃ±a', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. Pellentesque molestie mollis quam a iaculis. Nunc feugiat consectetur mauris quis blandit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed imperdiet scelerisque ante, eu rhoncus augue dictum eu. Sed imperdiet imperdiet semper. Maecenas in dolor dolor, quis ullamcorper velit. Duis ut ligula tellus, eget luctus arcu. Phasellus volutpat euismod tortor, et dignissim nulla consectetur euismod. Nulla pretium laoreet arcu, vitae consectetur nisi imperdiet a. Morbi arcu lorem, ornare condimentum pulvinar non, mattis sed tortor.\r\n\r\nVivamus sollicitudin urna ac massa iaculis consectetur. Etiam aliquet tempor quam ac tempor. Morbi dictum diam et lacus faucibus sodales. Phasellus commodo purus quam. Sed interdum luctus posuere. Suspendisse vehicula justo a mi commodo interdum. Nunc malesuada bibendum quam, id blandit dolor volutpat ut. In. ', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. Pellentesque molestie mollis quam a iaculis. Nunc feugiat consectetur mauris quis blandit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed imperdiet scelerisque ante, eu rhoncus augue dictum eu. Sed imperdiet imperdiet semper.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. Pellentesque molestie mollis quam a iaculis. Nunc feugiat consectetur mauris quis blandit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed imperdiet scelerisque ante, eu rhoncus augue dictum eu. Sed imperdiet imperdiet semper.', '- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. \r\n- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. \r\n- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. \r\n- Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. ', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac magna in odio congue suscipit. Ut arcu augue, tempus in facilisis eu, elementum ut risus. Pellentesque molestie mollis quam a iaculis. Nunc feugiat consectetur mauris quis blandit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed imperdiet scelerisque ante, eu rhoncus augue dictum eu. Sed imperdiet imperdiet semper.', NULL, 'salud, humor', 'http://www.youtube.com/watch?v=ab5pnqCF0Kc', 4, 'Barcelona', NULL, '', NULL);
INSERT INTO `project` VALUES('robocicla', 'Robocicla', 3, 50, 'carlaboserman', 'goteo', 145, 34, '2011-05-13', '2011-07-05', '2011-07-05', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Sevilla', 'EspaÃ±a', '', 'Robocicla es una iniciativa ExtremeÃ±a que mezcla arte, creatividad, reciclaje y tecnologÃ­a.\r\nEs una herramienta pensada para que niÃ±@s, jÃ³venes, padres, madres y educador@s se diviertan y aprendan junt@s acerca del conocimiento y la cultura libres.\r\n\r\nA travÃ©s de fichas de auto-construcciÃ³n ilustradas, aprenderemos a confeccionar Robots usando tecnologÃ­a reciclada.\r\nEstos Robots nos contarÃ¡n cÃ³mo liberar, compartir y construir entre todos el conocimiento.\r\nEn www.robocicla.net podrÃ¡s conocer experiencias de robociclaje y descargar todo el material didÃ¡ctico para usarlo cuando quieras.\r\nEn los Robocicla_TALLERES aprenderemos\r\n1. Sobre la importancia de reciclar el material tecnolÃ³gico, su impacto medioambiental en el mundo, y las posibilidades artÃ­sticas que nos ofrece el reciclaje.\r\n2. Desmontaje de Equipos, para separar lo que aÃºn tiene vida Ãºtil, y lo que usaremos para construir nuestros robots. Papelera electrÃ³nica de reciclaje.\r\n3. Construiremos a Hackerina y conoceremos los principios de la Ã‰tica Hacker.\r\n4. Aprenderemos electrÃ³nica bÃ¡sica: incorporando leds, interruptores, y ventiladores a nuestros robots.\r\n5. Documentaremos el taller y seremos bloguers de Robocila.\r\n\r\nActualmente el equipo de robocicla trabaja en la elaboraciÃ³n de material didÃ¡ctico para niÃ±@s, en forma de cuentojuegos ilustrados acerca de la historia de cada uno de los Robots, que serÃ¡n publicados digital y analÃ³gicamente.\r\n\r\nNota: Mientras buscaba financiaciÃ³n colectiva, Robocicla empezo una gira de 20 talleres por todo el territorio extremeÃ±o promovida por el Consorcio IdenTic a travÃ©s de la Red de Telecentros ExtremeÃ±os', '', 'URL proyecto:www.robocicla.net\r\n\r\nSector al que va dirigido:comunidad educativa, niÃ±@s de todas las edades, frikis, instituciones, comunidad software libre / cultura libre', '', 'Carla Boserman // Licenciada en Bellas artes entre Sevilla y Atenas en la especialidad de DiseÃ±o GrÃ¡fico y Grabado. Mi experiencia profesional tiene que ver con la gestiÃ³n creativo-cultural de proyectos de arte colaborativo, con especial atenciÃ³n al enfoque pedagÃ³gico y terapÃ©utico de los proyectos.\r\nDibujo, creo y enredo. Y sobre todo, me muevo.\r\nTrabajo en la elaboraciÃ³n de diarios viajes ilustrados, aprendo cerÃ¡mica y empiezo a formarme en el campo del arte terapia. Enamorada de Extremadura, donde he vivido y trabajado en los Ãºltimos tiempos, he podido desarrollar el proyecto La Siberia Mail Art www.siberiapostal.net haciendo de mis dibujos una herramienta de puesta de valor de un territorio y sus gentes.\r\n\r\n+ Breve CV\r\n\r\n2007 Ilustraciones/ColaboraciÃ³n en el proyecto Tecnopaisajes. TCS 2 Extremadura.\r\n2007 Primer premio Creativa 07 ConsejerÃ­a de InnovaciÃ³n Ciencia y Empresa Junta de AndalucÃ­a para el desarrollo del\r\nproyecto Pista Digital plataforma itinerante para la cultura. www.pistadigital.org\r\n2007 Premio INICIARTE de la Junta de AndalucÃ­a para la realizaciÃ³n del proyecto Larache se mueve Festival entre las\r\ndos orillas. Sevilla y Larache (Marruecos).\r\n2008+2009 ConceptualizaciÃ³n, diseÃ±o y dinamizaciÃ³n editorial del Proyecto Robinsones Urbanos, un espacio\r\nciudadano digital y una herramienta para pacientes con Trastorno Bipolar, que cuenta con el apoyo de CiudadanÃ­a Digital,\r\nConsejerÃ­a de InnovaciÃ³n de la Junta de AndalucÃ­a. robinsonesurbanos.org\r\n2008 Invitada al Simposium Nomadism: Art and New Technologies. Theatre de la Villette (Paris).\r\n2008 Ilustraciones y documentaciÃ³n para el Taller de reciclaje del agua: Aguas Mil, CALA. Alburquerque (Badajoz).\r\n2008 ExposiciÃ³n de Pinturas para el evento Senegal se Mueve de la ONG AEXCRAM (MÃ©rida)\r\n2009 DiseÃ±o de Postal para la conservaciÃ³n del patrimonio de Canido (Pontevedra).\r\n2009 DiseÃ±o de la ExposiciÃ³n: Miradas Cruzadas Sobre el Patrimonio MarroquÃ­. Sala Diagonal 3 (Sevilla).\r\n2010 Ponencia sobre el proyecto La Siberia mail art, Escuela de Arte de MÃ©rida.\r\n2010 Ilustraciones para el libro : Historia del encaje de bolillos en Extremadura.\r\n2010 PROCESO DE CONSTRUCCIÃ“N COLECTIVA - The Coffee Break 2010 (Junta de Extremadura)\r\nwww.thecooffebreak.biz\r\n2010 Taller de Creaciones FantÃ¡sticas//Reciclando TecnologÃ­a â€“ Festival NTX (Los Santos de Maimona, Badajoz)\r\nwww.festivalntx.com/ntx2010/\r\n2010 Beca a la CreaciÃ³n Joven para el proyecto Taller de Creaciones FantÃ¡sticas 2.0 Robocicla.net\r\nConsejerÃ­a de los jÃ³venes y del deporte-Junta de Extremadura. www.robocicla.net\r\n2010 DiseÃ±o Packing para el documental - La Esquina del tiempo -Galizuela/Badajoz de Carla Alonso.', '', 'cultura libre, reciclaje, software libre, pedagogÃ­a, creacion', 'http://www.youtube.com/watch?v=XNVCetMiUsY', 0, 'Sevilla', 0, '', '');
INSERT INTO `project` VALUES('todojunto-letterpress', 'Todojunto Letterpress', 1, 52, 'todojunto', 'goteo', 25, 0, '2011-05-13', '2011-07-05', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Barcelona', 'EspaÃ±a', '', 'Todojunto cuenta actualmente con un taller de tipografÃ­a mÃ³vil que ha nacido de algunos elementos que hemos recuperado de una imprenta de barrio con la que trabajÃ¡bamos en Barcelona. Todojunto Letterpress intenta poner en marcha un espacio para recuperar esta tÃ©cnica de impresiÃ³n, y utilizarlo como un espacio de aprendizaje y discusiÃ³n sobre la tipografÃ­a en general, el diseÃ±o, y las tÃ©cnicas de producciÃ³n grÃ¡fica.\r\nBÃ¡sicamente se nececitan mas juegos de tipografÃ­as de plomo y Madera, los muebles para guardarlas, y equipo que se pueda recuperar de otras imprentas que no lo usen mas. Hemos calculado que esta primera fase, para completar lo que ya tenemos en nuestro taller, necesita una inversiÃ³n de 2000 euros, y horas de trabajo, en el proceso de clasificaciÃ³n, organizaciÃ³n y limpieza de los materiales recuperados. Ofrecemos a cambio workshops, impresiones personalizadas para las personas hagan aportes econÃ³micos superiores a los 150â‚¬.\r\nTambiÃ©n ofrecemos en retorno, una pequeÃ±a publicaciÃ³n con material pedagogico sobre la tÃ©cnica, una especie de Manual/fanzine del taller de Todojunto letterpress.', '', 'URL del proyecto: www.todojunto.net\r\n\r\nSector al que va dirigido: Impresores amateurs, Estudiantes de diseÃ±o y tipografÃ­a, Profesores, interesados en la producciÃ³n grÃ¡fica, artistas, Barcelona', '', 'Tenemos una parte del taller de tipos mÃ³viles en funcionamiento desde hace aproximandamente 8 meses, tiempo en el que hemos experimentado con esta tÃ©cnica, somos un estudio de comunicaciÃ³n grÃ¡fica, esta es la direcciÃ³n de nuestro sitio web www.todojunto.net\r\n\r\ntambiÃ©n venimos de proyectos independintes de ilustraciÃ³n, comunicaciÃ³n y proyectos culturales:\r\n\r\nwww.jstk.org (Andrea GÃ³mez y Ricardo Duque)\r\nwww.miuk.ws (Tiago Pina)\r\nwww.andreagomez.info (Andrea GÃ³mez)\r\n\r\ny estamos vinculados con el proyecto de La Fanzinoteca Ambulant: www.fanzinoteca.net', '', 'ImpresiÃ³n, grafica, tÃ©cnica, tipografÃ­as mÃ³viles, recuperaciÃ³n', 'http://vimeo.com/17760187', 1, 'Barcelona', 0, '', '');
INSERT INTO `project` VALUES('urban-social-design-database', 'Urban Social Design Database', 3, 48, 'domenico', 'goteo', 115, 34, '2011-05-13', '2011-07-05', '2011-07-05', '0000-00-00', '0000-00-00', '', '', '', '', '', 'Madrid', 'EspaÃ±a', '', 'Crear una base de datos digital de proyectos desarrollados por los estudiantes durante su carrera universitaria. Ofrecer un nuevo espacio de conexiÃ³n y dialogo entre el mundo acadÃ©mico y la ciudadanÃ­a.\r\n\r\nTodo el material almacenado en la base de dados serÃ¡ de acceso pÃºblico y distribuido con licencia del tipo Creative Commons.\r\n\r\nEl marco general del proyecto se basa sobre el concepto de Urban Social Design entendido como el diseÃ±o de ambientes, espacios y dinÃ¡micas con el fin de mejorar las relaciones sociales, generando las condiciones para la interacciÃ³n y la auto-organizaciÃ³n entre las personas y su medio ambiente. ', '', 'URL proyecto: www.archtlas.com\r\n\r\nSector al que va dirigido: estudiantes, jÃ³venes profesionales, ciudadanos, activadores urbanos', '', 'El proyecto esta directamente asociado a una serie de cursos on-line que (temporalmente) llamamos Urban Social Design Institute (ecosistemaurbano.tv/tag/urban-social-design/).\r\n\r\nLa plataforma web que se quiere utilizar para el proyecto ya esta funcionando desde unos meses: www.archtlas.com\r\n\r\nEl principal promotor del proyecto es la agencia Ecosistema Urbano.\r\n\r\nEcosistema Urbano es una sociedad de profesionales que entienden la ciudad como fenÃ³meno complejo, situÃ¡ndose en un punto intermedio entre arquitectura, ingenierÃ­a, urbanismo y sociologÃ­a. Este Ã¡mbito de interÃ©s lo denominamos â€œsostenibilidad urbana creativaâ€, desde donde intentamos transformar la realidad contemporÃ¡nea a travÃ©s de innovaciÃ³n, creatividad y sobre todo acciÃ³n.\r\n\r\nSus integrantes principales han sido formados entre distintas universidades europeas (Madrid, Londres, Bruselas, Roma, Paris) y proceden de entornos urbanos diversos. Ejercen la docencia como profesores visitantes, impartiendo talleres y conferencias en las principales escuelas e instituciones internacionales (Harvard, Yale, UCLA, Cornell, Iberoamericana, RIBA, Copenague, Munich, Paris, MilÃ¡n, Shanghaiâ€¦).Desde 2000, su trabajo ha sido premiado nacional e internacionalmente en mÃ¡s de 30 ocasiones.\r\n\r\nEn 2005 recibieron el European Acknowledgement Award otorgado por la Holcim Foundation for Sustainable Construction (Ginebra, 2005). En 2006, el premio de la Architectural Association and the Environments, Ecology and Sustainability Research Cluster (Londres, 2006). En 2007 fueron nominados para el premio europeo Mies Van Der Rohe Award â€œArquitecto Europeo Emergenteâ€ y galardonados como oficina emergente con el premio â€œAR AWARD for emerging architectureâ€ (London) entre 400 participantes de todo el mundo. En 2008 recibieron el primer premio GENERACIÃ“N PRÃ“XIMA de la FundaciÃ³n PrÃ³xima ArquÃ­a y en 2009 el Silver Award Europe de la Holcim Foundation for Sustainable Construction entre mÃ¡s de 500 equipos, siendo mÃ¡s tarde nominados como finalistas a nivel mundial.\r\n\r\nEn los Ãºltimos aÃ±os su trabajo se ha difundido en mÃ¡s de 90 medios de 30 paÃ­ses (prensa nacional e internacional, programas de televisiÃ³n y publicaciones especializadas) y han sido expuestos en numerosas galerÃ­as, museos e instituciones (Bienal de Venecia, "Le Sommer Environnement" en ParÃ­s, Spazio FMG de MilÃ¡n, Seul Design Olimpics, Louisiana Museum of Modern Art de Copenague, Boston Society of Architects, Matadero Madrid, Circulo de Bellas Artes de Madrid, COAM, COAC,...). En la actualidad exponen en el Design Museum de Londres dentro de la muestra "sustainable futures"y preparan una exposiciÃ³n monogrÃ¡fica sobre su trabajo en el Deutsches Architektur Zentrum de Berlin.\r\n\r\nActualmente el equipo estÃ¡ involucrado en proyectos I+D sobre el futuro urbano "ciudades eco-tecno-lÃ³gicas" Proyecto CETICA, financiado por el Ministerio Industria dentro del programa CENIT. En paralelo, desarrollan una labor de difusiÃ³n a travÃ©s de nuevas tecnologÃ­as de la informaciÃ³n, donde han generado una plataforma de comunicaciÃ³n que crea redes sociales y gestiona canales de difusiÃ³n en internet sobre sostenibilidad urbana creativa (www.ecosistemaurbano.org).\r\n\r\nEn la actualidad trabajan en propuestas de transformaciÃ³n urbana para diferentes ciudades y sus proyectos mÃ¡s recientes incluyen el diseÃ±o de un espacio pÃºblico de experimentaciÃ³n interactivo para la Expo Universal de Shanghai 2010 y la propuesta urbana Plaza EcÃ³polis de Rivas en la periferia de Madrid.', '', 'diseÃ±o, proyectos, base de datos, difusiÃ³n, educaciÃ³n', '', 0, '', 0, '', '');

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
INSERT INTO `project_category` VALUES('a565092b772c29abc1b92f999af2f2fb', 6);
INSERT INTO `project_category` VALUES('archinhand-architecture-in-your-hand', 7);
INSERT INTO `project_category` VALUES('archinhand-architecture-in-your-hand', 10);
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
INSERT INTO `project_image` VALUES('a565092b772c29abc1b92f999af2f2fb', 21);
INSERT INTO `project_image` VALUES('a565092b772c29abc1b92f999af2f2fb', 64);
INSERT INTO `project_image` VALUES('a565092b772c29abc1b92f999af2f2fb', 65);
INSERT INTO `project_image` VALUES('archinhand-architecture-in-your-hand', 57);
INSERT INTO `project_image` VALUES('canal-alfa', 62);
INSERT INTO `project_image` VALUES('fe99373e968b0005e5c2406bc41a3528', 14);
INSERT INTO `project_image` VALUES('goteo', 66);
INSERT INTO `project_image` VALUES('goteo', 67);
INSERT INTO `project_image` VALUES('hkp', 59);
INSERT INTO `project_image` VALUES('mi-barrio', 58);
INSERT INTO `project_image` VALUES('move-commons', 60);
INSERT INTO `project_image` VALUES('nodo-movil', 61);
INSERT INTO `project_image` VALUES('oh-oh-fase-2', 55);
INSERT INTO `project_image` VALUES('pliegos', 28);
INSERT INTO `project_image` VALUES('robocicla', 63);
INSERT INTO `project_image` VALUES('todojunto-letterpress', 54);
INSERT INTO `project_image` VALUES('urban-social-design-database', 56);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promote`
--

DROP TABLE IF EXISTS `promote`;
CREATE TABLE `promote` (
  `node` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `title` tinytext,
  `description` text,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  UNIQUE KEY `project_node` (`node`,`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos destacados';

--
-- Volcar la base de datos para la tabla `promote`
--

INSERT INTO `promote` VALUES('goteo', 'nodo-movil', 'Nodomovil', 'Tiwene muchos cofinanciadores', 2);
INSERT INTO `promote` VALUES('goteo', 'pliegos', 'Pliegos', 'Repliegandose', 1);

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
INSERT INTO `purpose` VALUES('blog-comments', 'Comentarios', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-comments_no_allowed', 'No se permiten comentarios en  esta entrada', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-comments_no_comments', 'No hay comentarios en esta entrada', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-main-header', 'Goteo blog', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-no_comments', 'Sin comentarios', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-no_posts', 'No se ha publicado ninguna entrada de actualizacion', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-read_more', 'Lee mas', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-send_comment-button', 'Enviar', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-send_comment-header', 'Escribe tu comentario', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-side-last_comments', 'Ãšltimos comentarios', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-side-last_posts', 'Ãšltimas entradas', NULL, 'blog');
INSERT INTO `purpose` VALUES('blog-side-tags', 'CategorÃ­as', NULL, 'blog');
INSERT INTO `purpose` VALUES('contact-email-field', 'Email', NULL, 'contact');
INSERT INTO `purpose` VALUES('contact-message-field', 'Mensaje', NULL, 'contact');
INSERT INTO `purpose` VALUES('contact-send_message-button', 'Enviar', NULL, 'contact');
INSERT INTO `purpose` VALUES('contact-send_message-header', 'EnvÃ­anos un mensaje', NULL, 'contact');
INSERT INTO `purpose` VALUES('contact-subject-field', 'Asunto', NULL, 'contact');
INSERT INTO `purpose` VALUES('costs-field-amount', 'Valor', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-cost', 'Coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-dates', 'Fechas', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-date_from', 'Desde', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-date_until', 'Hasta', NULL, 'costs');
INSERT INTO `purpose` VALUES('costs-field-description', 'DescripciÃ³n', NULL, 'costs');
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
INSERT INTO `purpose` VALUES('discover-group--header', 'Texto discover-group--header', NULL, 'general');
INSERT INTO `purpose` VALUES('discover-group-all-header', 'Proyectos en campaÃ±a', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-group-outdate-header', 'Proyectos a punto de caducar', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-group-popular-header', 'Proyectos mÃ¡s populares', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-group-recent-header', 'Proyectos recientes', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-group-success-header', 'Proyectos exitosos', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-header-supertitle', 'Por categoria, lugar o retorno,', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-header-title', 'encuentra el proyecto con el que mÃ¡s te identificas', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-results-empty', 'No hemos encontrado ningÃºn proyecto que cumpla los criterios de bÃºsqueda', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-results-header', 'Resultado de bÃºsqueda', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-button', 'Buscar', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bycategory-all', 'TODAS', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bycategory-header', 'Por categorÃ­a:', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bycontent-header', 'Por contenido:', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bylocation-all', 'TODOS', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-bylocation-header', 'Por lugar:', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-byreward-all', 'TODOS', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-byreward-header', 'Por retorno:', NULL, 'discover');
INSERT INTO `purpose` VALUES('discover-searcher-header', 'Busca un proyecto', NULL, 'discover');
INSERT INTO `purpose` VALUES('error-contact-email-empty', 'No has puesto tu email', NULL, 'contact');
INSERT INTO `purpose` VALUES('error-contact-email-invalid', 'El email que has puesto no es vÃ¡lido', NULL, 'contact');
INSERT INTO `purpose` VALUES('error-contact-message-empty', 'No has puesto ningÃºn mensaje', NULL, 'contact');
INSERT INTO `purpose` VALUES('error-contact-subject-empty', 'No has puesto el asunto', NULL, 'contact');
INSERT INTO `purpose` VALUES('error-image-name', 'Texto error-image-name', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-size', 'Texto error-image-size', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-size-too-large', 'Texto error-image-size-too-large', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-tmp', 'Texto error-image-tmp', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-type', 'Texto error-image-type', NULL, 'general');
INSERT INTO `purpose` VALUES('error-image-type-not-allowed', 'Texto tipos de imagen permitidos', NULL, 'general');
INSERT INTO `purpose` VALUES('error-register-email', 'La direcciÃ³n de correo es obligatoria.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-email-confirm', 'La comprobaciÃ³n de email no coincide.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-email-exists', 'El direcciÃ³n de correo ya corresponde a un usuario registrado.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-invalid-password', 'La contraseÃ±a no es valida.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-password-confirm', 'La comprobaciÃ³n de contraseÃ±a no coincide.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-pasword', 'La contraseÃ±a no puede estar vacÃ­a.', NULL, 'register');
INSERT INTO `purpose` VALUES('error-register-pasword-empty', 'Texto error-register-pasword-empty', NULL, 'general');
INSERT INTO `purpose` VALUES('error-register-short-password', 'La contraseÃ±a debe contener un mÃ­nimo de 8 caracteres.', NULL, 'register');
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
INSERT INTO `purpose` VALUES('explain-project-progress', 'Texto bajo el tÃ­tulo Estado global de la informaciÃ³n', NULL, 'general');
INSERT INTO `purpose` VALUES('faq-ask-question', 'Â¿No has podido resolver tu duda?\r\n EnvÃ­a un mensaje con tu pregunta.', NULL, 'faq');
INSERT INTO `purpose` VALUES('form-accept-button', 'Aceptar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-add-button', 'AÃ±adir', NULL, 'form');
INSERT INTO `purpose` VALUES('form-apply-button', 'Aplicar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-edit-button', 'Editar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-navigation_bar-header', 'Ir a', NULL, 'form');
INSERT INTO `purpose` VALUES('form-next-button', 'Siguiente', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project-info_status-title', 'Estado global de la informaciÃ³n', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project-progress-title', 'Estado del progreso', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project-status-title', 'Estado del proyecto', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-campaing', 'En campaÃ±a', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-cancel', 'Desechado', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-cancelled', 'Cancelado', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-edit', 'EditÃ¡ndose', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-expired', 'Caducado', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-fulfilled', 'Retorno cumplido', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-review', 'Pendiente valoraciÃ³n', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_status-success', 'Financiado', NULL, 'form');
INSERT INTO `purpose` VALUES('form-project_waitfor-campaing', 'Difunde tu proyecto y mucha suerte con los aportes.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-cancel', 'Lo hemos desechado, puedes intentar otra idea o concepto', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-edit', 'Cuando lo tengas listo mandalo a revisiÃ³n. Necesitas llegar a un mÃ­nimo de informaciÃ³n en el formulario.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-expired', 'No lo conseguiste, mejÃ³ralo e intentalo de nuevo!', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-fulfilled', 'Has cumplido con los retornos! Gracias por tu participaciÃ³n.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-review', 'Espera que te digamos algo. Lo publicaremos o te diremos cÃ³mo mejorarlo.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-project_waitfor-success', 'Has conseguido el mÃ­nimo o mas en aportes. Ahora hablamos de dinero.', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('form-remove-button', 'Quitar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-self_review-button', 'Corregir', NULL, 'form');
INSERT INTO `purpose` VALUES('form-send_review-button', 'Enviar', NULL, 'form');
INSERT INTO `purpose` VALUES('form-upload-button', 'Upload', NULL, 'form');
INSERT INTO `purpose` VALUES('guide-blog-posting', 'Texto guide-blog-posting', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-comment', 'Texto guide-project-comment', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-contract-information', 'Texto guide-project-contract-information', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-costs', 'Texto guÃ­a en el paso COSTES del formulario de proyecto', NULL, 'costs');
INSERT INTO `purpose` VALUES('guide-project-description', 'Texto guide-project-description', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-error-mandatories', 'Faltan campos obligatorios', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-overview', 'Texto guÃ­a en el paso DESCRIPCIÃ“N del formulario de proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('guide-project-preview', 'Texto guÃ­a en el paso PREVISUALIZACIÃ“N del formulario de proyecto', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-rewards', 'Texto guÃ­a en el paso RETORNO del formulario de proyecto', NULL, 'rewards');
INSERT INTO `purpose` VALUES('guide-project-success-minprogress', 'Ha llegado al porcentaje mÃ­nimo', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-success-noerrors', 'Todos los campos obligatorios estan rellenados', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-success-okfinish', 'Puede enviar para valoraciÃ³n', NULL, 'preview');
INSERT INTO `purpose` VALUES('guide-project-support', 'Texto guide-project-support', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-supports', 'Texto guÃ­a en el paso COLABORACIONES del formulario de proyecto', NULL, 'supports');
INSERT INTO `purpose` VALUES('guide-project-updates', 'Texto guide-project-updates', NULL, 'general');
INSERT INTO `purpose` VALUES('guide-project-user-information', 'Texto guÃ­a en el paso PERFIL del formulario de proyecto', NULL, 'profile');
INSERT INTO `purpose` VALUES('guide-user-data', 'Texto guÃ­a en la ediciÃ³n de datos sensibles del usuario', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('guide-user-information', 'Texto guÃ­a en la ediciÃ³n de informaciÃ³n del usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('guide-user-register', 'Texto guÃ­a en el registro de usuario', NULL, 'register');
INSERT INTO `purpose` VALUES('home-banner-header', 'Accede a la comunidad goteo', NULL, 'home');
INSERT INTO `purpose` VALUES('home-banner-strong', '100% Abierto', NULL, 'home');
INSERT INTO `purpose` VALUES('home-posts-header', 'Como funciona goteo', NULL, 'home');
INSERT INTO `purpose` VALUES('home-promotes-header', 'Proyectos destacados', NULL, 'home');
INSERT INTO `purpose` VALUES('invest-abitmore', 'Por %s(cantidad) mÃ¡s serÃ­as %s(nivel)', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-address-field', 'DirecciÃ³n:', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-country-field', 'PaÃ­s:', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-header', 'DÃ³nde quieres recibir la recompensa', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-location-field', 'Ciudad:', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-address-zipcode-field', 'CÃ³digo postal:', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-amount', 'Cantidad', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-amount-tooltip', 'Introduce la cantidad con la que apoyarÃ¡s al proyecto', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-anonymous', 'Quiero que mi aporte sea anÃ³nimo', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-individual-header', 'Elige tu recompensa entre estas opciones', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-next_step', 'Paso siguiente', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-payment-email', 'Introduce tu email o cuenta de Paypal', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-payment_method-header', 'Elige el mÃ©todo de pago', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-resign', 'Renuncio a una recompensa individual, solo quiero ayudar al proyecto', NULL, 'invest');
INSERT INTO `purpose` VALUES('invest-social-header', 'Con los retornos colectivos ganamos todos', NULL, 'invest');
INSERT INTO `purpose` VALUES('login-access-button', 'Entrar', NULL, 'login');
INSERT INTO `purpose` VALUES('login-access-header', 'Usuario registrado', NULL, 'login');
INSERT INTO `purpose` VALUES('login-access-password-field', 'ContraseÃ±a', NULL, 'login');
INSERT INTO `purpose` VALUES('login-access-username-field', 'Nombre de usuario', NULL, 'login');
INSERT INTO `purpose` VALUES('login-fail', 'Login failed', NULL, 'login');
INSERT INTO `purpose` VALUES('login-oneclick-header', 'Accede con un solo click', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-button', 'Recuperar', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-email-field', 'Email de la cuenta', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-header', 'Recuperar contraseÃ±a', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-link', 'Recuperar contraseÃ±a', NULL, 'login');
INSERT INTO `purpose` VALUES('login-recover-username-field', 'Nombre de usuario', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-button', 'Registrar', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-confirm-field', 'Confirmar email', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-confirm_password-field', 'Confirmar contraseÃ±a', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-email-field', 'Email', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-header', 'Nuevo usuario', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-password-field', 'ContraseÃ±a', NULL, 'login');
INSERT INTO `purpose` VALUES('login-register-username-field', 'Nombre de usuario', NULL, 'login');
INSERT INTO `purpose` VALUES('mandatory-cost-field-amount', 'Texto obligatorio cantidad', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-cost-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-cost-field-name', 'Es obligatorio ponerle un nombre al coste', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-cost-field-type', 'Texto mandatory-cost-field-type', NULL, 'general');
INSERT INTO `purpose` VALUES('mandatory-individual_reward-field-amount', 'Es obligatorio indicar el importe que otorga la recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-individual_reward-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-individual_reward-field-name', 'Es obligatorio poner la recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-project-costs', 'MÃ­nimo de costes a desglosar en un proyecto', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-project-field-about', 'Es obligatorio explicar quÃ© es en la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-address', 'La direcciÃ³n del responsable del proyecto es obligatoria', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-category', 'La categorÃ­a del proyecto es obligatoria', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-contract-email', 'El email del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-contract-name', 'El nombre del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-contract-nif', 'El nif del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-contract-surname', 'El apellido del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-country', 'El paÃ­s del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-description', 'La descripciÃ³n del proyecto es obligatorio', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-goal', 'Es obligatorio explicar los objetivos en la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-image', 'Es obligatorio poner una imagen al proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-location', 'La localizaciÃ³n del proyecto es obligatoria', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-media', 'Poner un vÃ­deo para mejorar la puntuaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-motivation', 'Es obligatorio explicar la motivaciÃ³n en la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-name', 'El nombre del proyecto es obligatorio', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-phone', 'El telÃ©fono del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-related', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('mandatory-project-field-residence', 'El lugar de residencia del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-project-field-resource', 'Es obligatorio especificar si cuentas con otros recursos', NULL, 'costs');
INSERT INTO `purpose` VALUES('mandatory-project-field-zipcode', 'El cÃ³digo postal del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` VALUES('mandatory-register-field-email', 'Texto mandatory-register-field-email', NULL, 'general');
INSERT INTO `purpose` VALUES('mandatory-social_reward-field-description', 'Es obligatorio poner alguna descripciÃ³n al retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-social_reward-field-name', 'Es obligatorio poner el retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('mandatory-support-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL, 'supports');
INSERT INTO `purpose` VALUES('mandatory-support-field-name', 'Es obligatorio ponerle un nombre a la colaboraciÃ³n', NULL, 'supports');
INSERT INTO `purpose` VALUES('overview-field-about', 'QuÃ© es', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-categories', 'CategorÃ­as', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-currently', 'Estado actual', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-description', 'Resumen breve', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-goal', 'Objetivos', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-image_gallery', 'Imagenes actuales', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-image_upload', 'Subir una imagen', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-keywords', 'Palabras clave', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-media', 'VÃ­deo', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-media_preview', 'Vista previa', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-motivation', 'MotivaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-name', 'Nombre del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-currently_avanzado', 'Avanzado', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-currently_finalizado', 'Finalizado', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-currently_inicial', 'Inicial', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-currently_medio', 'Medio', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-scope_global', 'Global', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-scope_local', 'Local', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-scope_nacional', 'Nacional', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-options-scope_regional', 'Regional', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-project_location', 'UbicaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-related', 'Experiencia relacionada y equipo', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-field-scope', 'Ambito de alcance', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-fields-images-title', 'Imagenes del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('overview-main-header', 'Proyecto/DescripciÃ³n', NULL, 'overview');
INSERT INTO `purpose` VALUES('personal-field-address', 'DirecciÃ³n', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-contract_name', 'Nombre y apellidos', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-contract_nif', 'NIF', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-country', 'PaÃ­s', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-location', 'Localidad', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-phone', 'TelÃ©fono', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-field-zipcode', 'CÃ³digo postal', NULL, 'personal');
INSERT INTO `purpose` VALUES('personal-main-header', 'Usuario/Datos personales', NULL, 'personal');
INSERT INTO `purpose` VALUES('preview-main-header', 'Proyecto/PrevisualizaciÃ³n', NULL, 'preview');
INSERT INTO `purpose` VALUES('preview-send-comment', 'Notas adicionales para el administrador', NULL, 'preview');
INSERT INTO `purpose` VALUES('profile-about-header', 'Sobre mi', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-field-about', 'CuÃ©ntanos algo sobre ti', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-avatar_current', 'Tu imagen actual', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-avatar_upload', 'Subir una imagen', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-contribution', 'QuÃ© podrÃ­as aportar a Goteo', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-interests', 'Tus intereses', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-keywords', 'Palabras clave', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-location', 'DÃ³nde estas', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-name', 'Alias', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-field-websites', 'Mis webs', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-fields-image-title', 'Tu imagen', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-fields-social-title', 'Perfiles sociales', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-interests-header', 'Mis intereses', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-invest_on-header', 'Proyectos que apoyo', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-invest_on-title', 'Cofinancia', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-keywords-header', 'Mis palabras clave', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-last_worth-title', 'Fecha', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-location-header', 'Mi ubicaciÃ³n', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-main-header', 'Usuario/Perfil', NULL, 'profile');
INSERT INTO `purpose` VALUES('profile-my_investors-header', 'Mis cofinanciadores', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-my_projects-header', 'Mis proyectos', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-my_worth-header', 'Mi posiciÃ³n en goteo', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-name-header', 'Perfil de ', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-sharing_interests-header', 'Compartiendo intereses', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-social-header', 'Social', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-webs-header', 'Mis webs', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-widget-button', 'Ver perfil', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-widget-user-header', 'Usuario', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-worth-title', 'Aporta', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('profile-worthcracy-title', 'PosiciÃ³n', NULL, 'public_profile');
INSERT INTO `purpose` VALUES('project-collaborations-supertitle', 'Necesidades no econÃ³micas', NULL, 'project');
INSERT INTO `purpose` VALUES('project-collaborations-title', 'Se busca', NULL, 'project');
INSERT INTO `purpose` VALUES('project-form-header', 'Formulario', NULL, 'form');
INSERT INTO `purpose` VALUES('project-invest-continue', 'Vas a pre-autorizar el pago', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-fail', 'Algo ha fallado, por favor intÃ©ntalo de nuevo', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-guest', 'Invitado (no olvides registrarte)', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-ok', 'Ya eres cofinanciador de este proyecto', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-start', 'EstÃ¡s a un paso de ser cofinanciador de este proyecto', NULL, 'invest');
INSERT INTO `purpose` VALUES('project-invest-total', 'Total de aportaciones', NULL, 'general');
INSERT INTO `purpose` VALUES('project-menu-home', 'Proyecto', NULL, 'project');
INSERT INTO `purpose` VALUES('project-menu-messages', 'Mensajes', NULL, 'project');
INSERT INTO `purpose` VALUES('project-menu-needs', 'Necesidades', NULL, 'project');
INSERT INTO `purpose` VALUES('project-menu-supporters', 'Cofinanciadores', NULL, 'project');
INSERT INTO `purpose` VALUES('project-menu-updates', 'Actualizaciones', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-answer_it', 'Responder', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-send_direct-header', 'EnvÃ­a un mensaje al autor', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-send_message-button', 'Enviar', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-send_message-header', 'Escribe tu mensaje', NULL, 'project');
INSERT INTO `purpose` VALUES('project-messages-send_message-your_answer', 'Escribe tu respuesta aquí', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-header', 'Retornos', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-individual_reward-limited', 'Recompensa limitada', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-individual_reward-title', 'Recompensas individuales', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-individual_reward-units_left', 'Quedan %s(variable) unidades', 1, 'project');
INSERT INTO `purpose` VALUES('project-rewards-social_reward-title', 'Retorno colectivo', NULL, 'project');
INSERT INTO `purpose` VALUES('project-rewards-supertitle', 'Que ofrezco a cambio?', NULL, 'project');
INSERT INTO `purpose` VALUES('project-share-header', 'Comparte este proyecto', NULL, 'project');
INSERT INTO `purpose` VALUES('project-side-investors-header', 'Ya han aportado', NULL, 'project');
INSERT INTO `purpose` VALUES('project-spread-header', 'Difunde este proyecto', NULL, 'project');
INSERT INTO `purpose` VALUES('project-spread-widget', 'Widget del proyecto', NULL, 'project');
INSERT INTO `purpose` VALUES('project-support-supertitle', 'Necesidades econÃ³micas', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-categories-title', 'Categorias', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-days', 'Quedan', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-got', 'Obtenido', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-investment', 'Financiacion', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-investors', 'Cofinanciadores', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-minimum', 'MÃ­nimo', NULL, 'project');
INSERT INTO `purpose` VALUES('project-view-metter-optimum', 'Ã“ptimo', NULL, 'project');
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
INSERT INTO `purpose` VALUES('regular-idnetica', 'Texto regular-idnetica', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-im', 'Soy', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-invest', 'Aportar', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-investing', 'Aportando', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-invest_it', 'ApÃ³yalo', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-keepiton', 'Aun puedes seguir aportando', NULL, 'widget');
INSERT INTO `purpose` VALUES('regular-last', 'Ãšltima', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-license', 'Licencia', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-linkedin', 'LinkedIn', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-login', 'Accede', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-logout', 'Cerrar sesiÃ³n', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-main-header', 'Goteo.org', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-mandatory', 'Texto genÃ©rico para indicar campo obligatorio', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-menu', 'Menu', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-more_info', '+ info', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-news', 'Noticias', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-new_project', 'Proyecto nuevo', NULL, 'project');
INSERT INTO `purpose` VALUES('regular-onrun_mark', 'En marcha!', NULL, 'widget');
INSERT INTO `purpose` VALUES('regular-projects', 'proyectos', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-read_more', 'Leer mÃ¡s', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-review_board', 'Panel revisor', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-search', 'Buscar', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-see_all', 'Ver todos', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-see_blog', 'Ver blog', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-see_more', 'Ver mÃ¡s', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-share-facebook', 'Goteo en Facebook', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-share-rss', 'RSS/BLOG', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-share-twitter', 'SÃ­guenos en Twitter', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-share_this', 'Compartir en:', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-sorry', 'Lo sentimos', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-success_mark', 'Exitoso!', NULL, 'widget');
INSERT INTO `purpose` VALUES('regular-thanks', 'Gracias', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-total', 'Total', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-twitter', 'Twitter', NULL, 'general');
INSERT INTO `purpose` VALUES('regular-view_project', 'Ver proyecto', NULL, 'general');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-amount', 'Cantidad mÃ­nima', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-description', 'Description', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-other', 'Especificar el tipo de recompensa', NULL, 'general');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-reward', 'Recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-type', 'Tipo', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-individual_reward-units', 'Unidades', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-description', 'DescripciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-license', 'Licencia', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-other', 'Especificar el tipo de retorno', NULL, 'general');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-reward', 'Retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-field-social_reward-type', 'Tipo', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-fields-individual_reward-title', 'Recompensas individuales', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-fields-social_reward-title', 'Retornos colectivos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('rewards-main-header', 'Proyecto/Retornos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('step-1', 'Perfil', NULL, 'profile');
INSERT INTO `purpose` VALUES('step-2', 'Datos personales', NULL, 'personal');
INSERT INTO `purpose` VALUES('step-3', 'DescripciÃ³n', NULL, 'overview');
INSERT INTO `purpose` VALUES('step-4', 'Costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('step-5', 'Retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('step-6', 'Colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('step-7', 'PrevisualizaciÃ³n', NULL, 'preview');
INSERT INTO `purpose` VALUES('step-costs', 'Paso 4, desglose de costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('step-overview', 'Paso 3, descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('step-preview', 'paso 7, previsualizaciÃ³n', NULL, 'preview');
INSERT INTO `purpose` VALUES('step-rewards', 'Paso 5, retornos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('step-supports', 'Paso 6, colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('step-userPersonal', 'Paso 2, informaciÃ³n del responsable', NULL, 'personal');
INSERT INTO `purpose` VALUES('step-userProfile', 'Paso 1, informaciÃ³n del usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('subject-change-email', 'Asunto del mail al cambiar el email', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('subject-register', 'Asunto del email al registrarse', NULL, 'register');
INSERT INTO `purpose` VALUES('supports-field-description', 'DescripciÃ³n', NULL, 'supports');
INSERT INTO `purpose` VALUES('supports-field-support', 'Resumen', NULL, 'supports');
INSERT INTO `purpose` VALUES('supports-field-type', 'Tipo', NULL, 'supports');
INSERT INTO `purpose` VALUES('supports-fields-support-title', 'Colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('supports-main-header', 'Proyecto/Colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-about', 'Consejo para rellenar el campo quÃ© es', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-address', 'Consejo para rellenar el address del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-category', 'Consejo para seleccionar la categorÃ­a del proyecto', NULL, 'overview');
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
INSERT INTO `purpose` VALUES('tooltip-project-cost-until', 'Texto tooltip fecha coste hasta', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-costs', 'Texto tooltip desglose de costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-country', 'Consejo para rellenar el paÃ­s del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-currently', 'Consejo para rellenar el estado de desarrollo del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-description', 'Consejo para rellenar la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-goal', 'Consejo para rellenar el campo objetivos', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-image', 'Consejo para rellenar la imagen del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-image_upload', 'Texto tooltip subir imagen proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward', 'Consejo para editar retornos individuales existentes', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-amount', 'Texto tooltip cantidad para recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-description', 'Texto tooltip descripcion recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-reward', 'Texto tooltip nombre recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-type', 'Texto tooltip tipo de recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_reward-units', 'Texto tooltip unidades de recompensa', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-individual_rewards', 'Texto tooltip recompensas individuales', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-keywords', 'Consejo para rellenar las palabras clave del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-location', 'Consejo para rellenar el lugar de residencia del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-media', 'Consejo para rellenar el media del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-motivation', 'Consejo para rellenar el campo motivaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-name', 'Consejo para rellenar el nombre del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-ncost', 'Consejo para rellenar un nuevo desglose de costes', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-nindividual_reward', 'Consejo para rellenar un nuevo retorno individual', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-nsocial_reward', 'Consejo para rellenar un nuevo retorno colectivo', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-nsupport', 'Consejo para rellenar una nueva colaboraciÃ³n', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-phone', 'Consejo para rellenar el telÃ©fono del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-project-project_location', 'Consejo para rellenar la localizaciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-related', 'Consejo para rellenar el campo experiencia relacionada y equipo', NULL, 'overview');
INSERT INTO `purpose` VALUES('tooltip-project-resource', 'Consejo para rellenar el campo Cuenta con otros recursos?', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-schedule', 'Texto tooltip agenda del proyeecto', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-scope', 'Texto tooltip-project-scope', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward', 'Consejo para editar retornos colectivos existentes', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-description', 'Texto tooltip descripcion retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-license', 'Texto tooltip licencia retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-reward', 'Texto tooltip nombre retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_reward-type', 'Texto tooltip tipo retorno', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-social_rewards', 'Texto tooltip retornos colectivos', NULL, 'rewards');
INSERT INTO `purpose` VALUES('tooltip-project-support', 'Consejo para editar colaboraciones existentes', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-support-description', 'Texto tooltip descripcion colaboracion', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-support-support', 'Texto tooltip nombre colaboracion', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-support-type', 'Texto tooltip tipo colaboracion', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-supports', 'Texto tooltip colaboraciones', NULL, 'supports');
INSERT INTO `purpose` VALUES('tooltip-project-totals', 'Texto tooltip costes totales', NULL, 'costs');
INSERT INTO `purpose` VALUES('tooltip-project-zipcode', 'Consejo para rellenar el zipcode del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` VALUES('tooltip-updates-allow_comments', 'Texto tooltip-updates-allow_comments', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-date', 'Texto tooltip-updates-date', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-footer', 'Texto tooltip-updates-footer', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-home', 'Texto tooltip-updates-home', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-image', 'Texto tooltip-updates-image', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-image_upload', 'Texto tooltip-updates-image_upload', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-media', 'Texto tooltip-updates-media', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-tags', 'Texto tooltip-updates-tags', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-text', 'Texto tooltip-updates-text', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-updates-title', 'Texto tooltip-updates-title', NULL, 'general');
INSERT INTO `purpose` VALUES('tooltip-user-about', 'Consejo para rellenar el cuÃ©ntanos algo sobre ti', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-avatar_upload', 'Texto tooltip subir imagen usuario', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-blog', 'Consejo para rellenar la web', NULL, 'profile');
INSERT INTO `purpose` VALUES('tooltip-user-contribution', 'Consejo para rellenar el quÃ© podrÃ­as aportar en goteo', NULL, 'profile');
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
INSERT INTO `purpose` VALUES('user-account-inactive', 'La cuenta esta desactivada', NULL, 'general');
INSERT INTO `purpose` VALUES('user-activate-already-active', 'La cuenta de usuario ya esta activada', NULL, 'register');
INSERT INTO `purpose` VALUES('user-activate-fail', 'Texto user-activate-fail', NULL, 'general');
INSERT INTO `purpose` VALUES('user-activate-success', 'La cuenta de usuario se ha activado correctamente', NULL, 'register');
INSERT INTO `purpose` VALUES('user-changeemail-fail', 'Texto user-changeemail-fail', NULL, 'general');
INSERT INTO `purpose` VALUES('user-changeemail-success', 'El email se ha cambiado con exito', NULL, 'dashboard');
INSERT INTO `purpose` VALUES('user-register-success', 'El usuario se ha registrado correctamente', NULL, 'register');
INSERT INTO `purpose` VALUES('validate-cost-field-dates', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuaciÃ³n', NULL, 'costs');
INSERT INTO `purpose` VALUES('validate-project-field-about', 'La explicacion del proyecto es demasiado corta', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-field-costs', 'Desglosar hasta 5 costes para mejorar la puntuaciÃ³n', NULL, 'costs');
INSERT INTO `purpose` VALUES('validate-project-field-currently', 'Indicar el estado del proyecto para mejorar la puntuaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-field-description', 'La descripcion del proyecto es demasiado corta', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-individual_rewards', 'Indicar hasta 5 recompensas individuales para mejorar la puntuaciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` VALUES('validate-project-social_rewards', 'Indicar hasta 5 retornos colectivos para mejorar la puntuaciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` VALUES('validate-project-total-costs', 'El coste Ã³ptimo no puede exceder demasiado al coste mÃ­nimo', NULL, 'costs');
INSERT INTO `purpose` VALUES('validate-project-value-contract-email', 'El email no es correcto', NULL, 'register');
INSERT INTO `purpose` VALUES('validate-project-value-contract-nif', 'El nif del responsable del proyecto debe ser correcto', NULL, 'personal');
INSERT INTO `purpose` VALUES('validate-project-value-description', 'La descripciÃ³n del proyecto debe se suficientemente extensa', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-value-keywords', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` VALUES('validate-project-value-phone', 'El telÃ©fono debe ser correcto', NULL, 'personal');
INSERT INTO `purpose` VALUES('validate-register-value-email', 'El email introducido no es valido', NULL, 'register');
INSERT INTO `purpose` VALUES('validate-social_reward-license', 'Indicar una licencia para mejorar la puntuaciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` VALUES('validate-user-field-about', 'Si no ha puesto nada sobre el/ella ', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-avatar', 'Si no ha puesto una imagen de perfil', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-contribution', 'Si no ha puesto quÃ© puede aportar a Goteo', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-facebook', 'Si no ha puesto su cuenta de facebook', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-interests', 'Si no ha seleccionado ningÃºn interÃ©s', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-keywords', 'Si no ha puesto ninguna palabra clave', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-linkedin', 'El campo linkedin no es valido', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-location', 'El lugar de residencia del usuario no es valido', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-name', 'Si no ha puesto el nombre completo', NULL, 'profile');
INSERT INTO `purpose` VALUES('validate-user-field-twitter', 'El twitter del usuario no es valido', NULL, 'profile');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Revision para evaluacion de proyecto' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `review`
--


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
  `fulsocial` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Retorno colectivo  cumplido',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Retornos colectivos e individuales' AUTO_INCREMENT=164 ;

--
-- Volcar la base de datos para la tabla `reward`
--

INSERT INTO `reward` VALUES(57, 'a565092b772c29abc1b92f999af2f2fb', 'Acceso y utilizaciÃ³n libre de la aplicaciÃ³n web', 'La aplicaciÃ³n serÃ¡ de libre uso. El usuario Ãºnicamente tendrÃ¡ que registrarse para organizar campaÃ±as (numero limitado a 3). Para cada campaÃ±a se obtendrÃ¡ automÃ¡ticamente un espacio Ãºnico en el servidor para la visualizaciÃ³n ', 'social', 'manual', NULL, 'agpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(59, 'a565092b772c29abc1b92f999af2f2fb', 'AsesorÃ­a para futuros administradores de la aplicaciÃ³n', 'Te asesoraremos en el uso de la aplicaciÃ³n respecto a la instrucciones, la parte de twitter, etc.', 'individual', 'money', NULL, NULL, 40, 75, 0);
INSERT INTO `reward` VALUES(64, 'fe99373e968b0005e5c2406bc41a3528', '01 retorno colectivo', 'Phasellus varius sodales accumsan.', 'social', 'manual', NULL, 'ccbyncsa', NULL, NULL, 0);
INSERT INTO `reward` VALUES(65, 'fe99373e968b0005e5c2406bc41a3528', '02 retorno colectivo', 'Nulla pellentesque mi at dolor accumsan accumsan. Praesent arcu nibh, sollicitudin eu fringilla fringilla, rhoncus et mi. Sed sed pretium ipsum. Vestibulum non velit nibh, non dignissim eros. Phasellus varius sodales accumsan.', 'social', 'code', NULL, 'gpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(66, 'fe99373e968b0005e5c2406bc41a3528', '01 recompensa individual', 'Suspendisse condimentum metus vel arcu faucibus id volutpat arcu ultricies. Nulla pellentesque mi at dolor accumsan accumsan. Praesent arcu nibh, sollicitudin eu fringilla fringilla, rhoncus et mi. Sed sed pretium ipsum. Vestibulum non velit nibh, non dig', 'individual', 'product', NULL, NULL, 20, 30, 0);
INSERT INTO `reward` VALUES(73, '2c667d6a62707f369bad654174116a1e', 'codigo GPL', 'cÃ³digo', 'social', 'file', NULL, 'lgpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(74, '2c667d6a62707f369bad654174116a1e', 'CD', 'CD audio', 'individual', 'product', NULL, NULL, 15, 10, 0);
INSERT INTO `reward` VALUES(81, '2c667d6a62707f369bad654174116a1e', 'Nueva recompensa individual', 'devuelvo en 4 aÃ±os al 5 %', 'individual', 'money', NULL, NULL, 1000, 0, 0);
INSERT INTO `reward` VALUES(82, '2c667d6a62707f369bad654174116a1e', 'Planos placa arduino', '50 placas al mejor postor', 'social', 'design', NULL, 'oshw', NULL, NULL, 0);
INSERT INTO `reward` VALUES(90, 'a565092b772c29abc1b92f999af2f2fb', 'El cÃ³digo de Twittometro', 'EstarÃ¡ disponible el cÃ³digo de la aplicaciÃ³n para poder usarlo y mejorarlo, siempre bajo el mismo tipo de licencia libre.', 'social', 'code', NULL, 'agpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(92, 'pliegos', 'Licencia CC', 'Ok lo que digo :)', 'social', 'design', NULL, 'gpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(95, 'pliegos', 'Premios!', 'Ole ole', 'individual', 'other', NULL, NULL, 10, 2, 0);
INSERT INTO `reward` VALUES(109, '2c667d6a62707f369bad654174116a1e', 'Patrones para hacer una camiseta', 'Patrones para hacer una camiseta', 'social', 'design', NULL, 'ccby', NULL, NULL, 0);
INSERT INTO `reward` VALUES(118, 'pliegos', 'Nueva recompensa individual', '', 'individual', 'money', NULL, NULL, 10, 0, 0);
INSERT INTO `reward` VALUES(122, 'todojunto-letterpress', 'Devuelvo en horas de formaciÃ³n o ayuda a otros proyectos', 'Workshops en el taller letterpress.  12 horas', 'social', 'service', NULL, 'pd', 0, 0, 0);
INSERT INTO `reward` VALUES(125, 'todojunto-letterpress', 'Devuelvo en manuales (HOW TO)', 'Manual/fanzine explicando los fundamentos de la impresiÃ³n con tipografÃ­as mÃ³viles.', 'social', 'manual', NULL, 'ccby', 0, 0, 0);
INSERT INTO `reward` VALUES(127, 'oh-oh-fase-2', 'Devuelvo en manuales (HOW TO)', 'Manual para replicar el robot, fabricarlo y comercializarlo, manuales para dar cursos con el', 'social', 'manual', NULL, 'ccby', 0, 0, 0);
INSERT INTO `reward` VALUES(130, 'todojunto-letterpress', 'Devuelvo en productos', 'Entregando series de productos impresos en la mÃ¡quina letterpress. 600 unidades', 'social', 'service', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(131, 'oh-oh-fase-2', 'Devuelvo el dinero', 'Hacemos donaciones a otros proyectos de software y hardware libre', 'social', 'other', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(132, 'urban-social-design-database', 'Devuelvo en archivos digitales', 'Los proyectos almacenados serÃ¡n de libre uso para todos.', 'social', 'file', NULL, 'ccbysa', 0, 0, 0);
INSERT INTO `reward` VALUES(133, 'archinhand-architecture-in-your-hand', 'Devuelvo el dinero', '10 % a otros proyectos de Goteo', 'social', 'other', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(134, 'archinhand-architecture-in-your-hand', 'Devuelvo el cÃ³digo fuente', 'CÃ³digo fuente', 'social', 'code', NULL, 'ccbyncsa', 0, 0, 0);
INSERT INTO `reward` VALUES(135, 'archinhand-architecture-in-your-hand', 'Devuelvo en horas de formaciÃ³n o ayuda a otros proyectos', 'Devuelvo en horas de formaciÃ³n o ayuda a otros proyectos.  12 horas', 'social', 'service', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(136, 'mi-barrio', 'Devuelvo el cÃ³digo fuente', 'Manuales express de grabaciÃ³n, videos resultado del proyecto y documentaciÃ³n de los procesos', 'social', 'code', NULL, 'ccbysa', 0, 0, 0);
INSERT INTO `reward` VALUES(137, 'mi-barrio', 'Devuelvo en horas de formaciÃ³n o ayuda a otros proyectos', 'Talleres de formaciÃ³n ciudadana ', 'social', 'service', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(138, 'goteo', 'Prueba TAPR', '', 'social', 'design', NULL, NULL, 0, 0, 0);
INSERT INTO `reward` VALUES(139, 'goteo', 'Prueba OH', '', 'social', 'file', NULL, 'oshw', 0, 0, 0);
INSERT INTO `reward` VALUES(140, 'goteo', 'Prueba ODC', '', 'social', 'file', NULL, NULL, 0, 0, 0);
INSERT INTO `reward` VALUES(141, 'hkp', 'Devuelvo en productos', 'Libro HKp  1500 unidades', 'social', 'manual', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(142, 'hkp', 'Devuelvo en productos', 'DVD HKp  1500 unidades', 'social', 'file', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(143, 'hkp', 'Contenidos copyleft', 'todos los contenidos son copyleft o libres reutilizables, wiki es plataforma participativa puede usarse en talleres u otros proyectos', 'social', 'other', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(144, 'hkp', 'Libro + DVD', 'En caso de conseguir publicar el libro y el DVD (o uno de ellos) se podrÃ­a enviar el pack a quienes hayan hecho contribuciones', 'individual', 'product', NULL, '', 15, 1500, 0);
INSERT INTO `reward` VALUES(145, 'move-commons', 'Devuelvo el cÃ³digo fuente   ', 'Material grÃ¡fico, cÃ³digo de plataforma+buscador (AGPL) y HOWTOs/categorÃ­a ', 'social', 'code', NULL, 'agpl', 0, 0, 0);
INSERT INTO `reward` VALUES(146, 'move-commons', 'Otros', 'Buscador de iniciativas y facilitar construcciÃ³n de servicios sobre la plataforma', 'social', 'other', NULL, 'ccby', 0, 0, 0);
INSERT INTO `reward` VALUES(147, 'nodo-movil', 'CÃ³digo fuente', 'Devuelvo el cÃ³digo fuente', 'social', 'code', NULL, 'xoln', 0, 0, 0);
INSERT INTO `reward` VALUES(148, 'nodo-movil', 'FormaciÃ³n', 'Devuelvo en horas de formaciÃ³n o ayuda a otros proyectos. 10 horas', 'social', 'service', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(149, 'canal-alfa', 'Devuelvo el cÃ³digo fuente ', 'La plataforma web y las aplicaciones creadas serÃ¡n publicadas como GPL.', 'social', 'code', NULL, 'gpl', 0, 0, 0);
INSERT INTO `reward` VALUES(151, 'canal-alfa', 'Devuelvo en archivos digitales   ', 'Todo el contenido publicado por los usuarios formarÃ¡ parte de un archivo de dominio pÃºblico.', 'social', 'file', NULL, '', 0, 0, 0);
INSERT INTO `reward` VALUES(152, 'robocicla', 'Nuevo retorno colectivo', 'Material DidÃ¡ctico en CÃ³digo Abierto , AsesorÃ­a y Herramientas pedagÃ³gicas en torno a la cultura libre para niÃ±@s', 'social', 'manual', NULL, 'ccbyncsa', 0, 0, 0);
INSERT INTO `reward` VALUES(157, '8851739335520c5eeea01cd745d0442d', 'Nombre del retorno 1', 'Descripcion del retorno 1', 'social', 'code', NULL, 'agpl', NULL, NULL, 0);
INSERT INTO `reward` VALUES(158, '8851739335520c5eeea01cd745d0442d', 'Nombre retorno 2', 'Descripcion del retorno 2', 'social', 'code', NULL, 'tapr', NULL, NULL, 0);
INSERT INTO `reward` VALUES(159, '8851739335520c5eeea01cd745d0442d', 'Nombre retorno 3', 'Descripcion retorno 3', 'social', 'manual', NULL, 'freebsd', NULL, NULL, 0);
INSERT INTO `reward` VALUES(160, '8851739335520c5eeea01cd745d0442d', 'Nueva recompensa individual', '', 'individual', 'other', NULL, NULL, 0, 0, 0);
INSERT INTO `reward` VALUES(163, 'a9277be1c7e92eaa36ecae753231bfb1', 'Nuevo retorno colectivo', NULL, 'social', '', NULL, '', NULL, NULL, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Patrocinadores' AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `sponsor`
--

INSERT INTO `sponsor` VALUES(1, 'CCCB', 'http://www.cccb.org/lab', 38, 2);
INSERT INTO `sponsor` VALUES(2, 'CONCA', 'http://www.conca.cat/', 40, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Colaboraciones' AUTO_INCREMENT=72 ;

--
-- Volcar la base de datos para la tabla `support`
--

INSERT INTO `support` VALUES(19, 'a565092b772c29abc1b92f999af2f2fb', 'Beta-testers y difusores', 'Son bienvenidas la ayuda de difusiÃ³n para que mucha gente conozca la  herramienta y participe en las campaÃ±as. TambiÃ©n se necesitÃ¡ en determinados momentos hacer pruebas masivas para ver la resistencia de la aplicaciÃ³n. ', 'lend', 78);
INSERT INTO `support` VALUES(20, 'a565092b772c29abc1b92f999af2f2fb', 'Servidores', 'Aunque contamos con un servidor, dependiendo del numero de vistas y usuarios de la herramienta, necesitaremos patrocinadores para hacer mÃ¡s fÃ¡cil el mantenimiento del proyecto y que pueda continuar siendo de uso gratuito.', 'lend', 79);
INSERT INTO `support` VALUES(23, 'fe99373e968b0005e5c2406bc41a3528', 'Espacio taller', 'Donec ultrices libero in est tincidunt placerat tempor mi mattis. Vestibulum at aliquam lacus. Suspendisse condimentum metus vel arcu faucibus id volutpat arcu ultricies. Nulla pellentesque mi at dolor accumsan accumsan. ', 'lend', 52);
INSERT INTO `support` VALUES(28, '2c667d6a62707f369bad654174116a1e', 'pincel', 'pinceles', 'lend', 57);
INSERT INTO `support` VALUES(29, '2c667d6a62707f369bad654174116a1e', 'alguin q sepa escribir', 'redactar textos', 'task', 58);
INSERT INTO `support` VALUES(34, '2c667d6a62707f369bad654174116a1e', 'nueva colab editada desde el dahboard proyecto BROOKLYN', 'a ver si se ve', 'task', 59);
INSERT INTO `support` VALUES(38, 'pliegos', 'Pilas', 'Dejadmelas :)', 'lend', 2);
INSERT INTO `support` VALUES(43, '2c667d6a62707f369bad654174116a1e', 'ayuda para poner en marcha este ajax!!!', 'ayuda para poner en marcha este ajax!!!', 'task', 60);
INSERT INTO `support` VALUES(44, '2c667d6a62707f369bad654174116a1e', 'Nueva colaboraciÃ³n', '', 'task', NULL);
INSERT INTO `support` VALUES(48, '2c667d6a62707f369bad654174116a1e', 'Nueva tarea', 'una tarea guay', 'task', NULL);
INSERT INTO `support` VALUES(53, 'todojunto-letterpress', 'Furgoneta', 'Furgoneta o transporte para mover los materiales conseguidos. Para 4 horas mÃ¡s o menos.', 'lend', NULL);
INSERT INTO `support` VALUES(54, 'urban-social-design-database', 'Viajero', 'estudiante becario para viajar por europa y dar a conocer el proyecto', 'task', 87);
INSERT INTO `support` VALUES(55, 'urban-social-design-database', 'film maker', 'film maker', 'task', 88);
INSERT INTO `support` VALUES(56, 'urban-social-design-database', 'espacio', 'plaza de trabajo en oficina compartida', 'lend', 89);
INSERT INTO `support` VALUES(57, 'urban-social-design-database', 'camara video', 'camara video', 'lend', 90);
INSERT INTO `support` VALUES(58, 'urban-social-design-database', 'hosting profesional', 'hosting profesional', 'lend', 91);
INSERT INTO `support` VALUES(59, 'archinhand-architecture-in-your-hand', 'Testers de la herramienta', 'Estudiantes de arquitectura', 'task', 92);
INSERT INTO `support` VALUES(60, 'mi-barrio', 'DiseÃ±adores', 'DiseÃ±adores web, espertos en redes sociales', 'task', 0);
INSERT INTO `support` VALUES(61, 'mi-barrio', 'Aulas para talleres', 'Aulas para talleres', 'lend', 0);
INSERT INTO `support` VALUES(62, 'move-commons', 'DiseÃ±ador grÃ¡fico', 'DiseÃ±ador grÃ¡fico', 'task', 84);
INSERT INTO `support` VALUES(63, 'move-commons', 'Traductores a mÃºltiples idiomas (20h/c)', 'Traductores a mÃºltiples idiomas (20h/c)', 'task', 85);
INSERT INTO `support` VALUES(64, 'move-commons', 'Testers', 'Colectivos', 'task', 86);
INSERT INTO `support` VALUES(65, 'nodo-movil', 'Desarrolladores', 'Desarrolladores de Exo.cat, grupo Manet. Expertos en streaming y telefonÃ­a mÃ³vil..', 'task', 3);
INSERT INTO `support` VALUES(66, 'nodo-movil', 'Espacio de trabajo', 'Sala de hackeo / trabajo / reuniÃ³n. para 10 personas.', 'lend', 4);
INSERT INTO `support` VALUES(67, 'nodo-movil', 'Espacio pÃºblico', 'Espacio pÃºblico (accesible para trabajar ).', 'lend', 5);
INSERT INTO `support` VALUES(68, 'canal-alfa', 'ProgramaciÃ³n extractor', 'ProgramaciÃ³n herramientas para la extraciÃ³n de videos', 'task', 0);
INSERT INTO `support` VALUES(69, 'canal-alfa', 'InvestigaciÃ³n', 'InvestigaciÃ³n de algoritmos para el reconocimiento automatizado del contenido de vÃ­deos', 'task', 0);
INSERT INTO `support` VALUES(70, 'canal-alfa', 'ProgramaciÃ³n editor', 'ProgramaciÃ³n editor de vÃ­deo online', 'task', 0);
INSERT INTO `support` VALUES(71, 'robocicla', 'Traductor', 'Traductor@ (ingles / portugues / italiano / frances / griego)', 'task', 80);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tags de blogs (de nodo)' AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `tag`
--

INSERT INTO `tag` VALUES(1, 'Plataforma Goteo', 1);
INSERT INTO `tag` VALUES(3, 'Goteo', 1);

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

INSERT INTO `text` VALUES('error-image-name', 'es', 'Error en el nombre del archivo');
INSERT INTO `text` VALUES('error-image-size', 'es', 'Error en el tamaÃ±o del archivo');
INSERT INTO `text` VALUES('error-image-size-too-large', 'es', 'La imagen es demasiado grande');
INSERT INTO `text` VALUES('error-image-tmp', 'es', 'Error al cargar el archivo');
INSERT INTO `text` VALUES('error-image-type', 'es', 'Solo se permiten imÃ¡genes jpg, png y gif');
INSERT INTO `text` VALUES('error-image-type-not-allowed', 'es', 'Texto tipos de imagen permitidos');
INSERT INTO `text` VALUES('error-register-email', 'es', 'La direcciÃ³n de correo es obligatoria.');
INSERT INTO `text` VALUES('error-register-email-confirm', 'es', 'La comprobaciÃ³n de email no coincide.');
INSERT INTO `text` VALUES('error-register-email-exists', 'es', 'El direcciÃ³n de correo ya corresponde a un usuario registrado.');
INSERT INTO `text` VALUES('error-register-invalid-password', 'es', 'La contraseÃ±a no es valida.');
INSERT INTO `text` VALUES('error-register-password-confirm', 'es', 'La comprobaciÃ³n de contraseÃ±a no coincide.');
INSERT INTO `text` VALUES('error-register-pasword', 'es', 'La contraseÃ±a no puede estar vacÃ­a.');
INSERT INTO `text` VALUES('error-register-pasword-empty', 'es', 'No has puesto contraseÃ±a');
INSERT INTO `text` VALUES('error-register-short-password', 'es', 'La contraseÃ±a debe contener un mÃ­nimo de 8 caracteres.');
INSERT INTO `text` VALUES('error-register-user-exists', 'es', 'Este nombre de usuario ya estÃ¡ registrado.');
INSERT INTO `text` VALUES('error-register-username', 'es', 'El nombre de usuario usuario es obligatorio.');
INSERT INTO `text` VALUES('error-user-email-confirm', 'es', 'La confirmaciÃ³n de email no es igual que el email');
INSERT INTO `text` VALUES('error-user-email-empty', 'es', 'No puedes dejar el email vacio');
INSERT INTO `text` VALUES('error-user-email-exists', 'es', 'Ya hay un usuario registrado con este email');
INSERT INTO `text` VALUES('error-user-email-invalid', 'es', 'El email que has puesto no es vÃ¡lido');
INSERT INTO `text` VALUES('error-user-email-token-invalid', 'es', 'El cÃ³digo no es correcto');
INSERT INTO `text` VALUES('error-user-password-confirm', 'es', 'La confirmaciÃ³n de contraseÃ±a no es igual a la contraseÃ±a');
INSERT INTO `text` VALUES('error-user-password-empty', 'es', 'No has puesto la contraseÃ±a');
INSERT INTO `text` VALUES('error-user-password-invalid', 'es', 'La contraseÃ±a es demasiado corta, debe tener al menos 6 caracteres');
INSERT INTO `text` VALUES('error-user-wrong-password', 'es', 'La contraseÃ±a no es correcta');
INSERT INTO `text` VALUES('explain-project-progress', 'es', 'Nivel de informaciÃ³n completada + evaluaciÃ³n automÃ¡tica que realiza Goteo segÃºn las opciones seleccionadas y la informaciÃ³n publicada por el usuario.');
INSERT INTO `text` VALUES('form-project_status-cancelled', 'es', 'form-project_status-cancelled');
INSERT INTO `text` VALUES('guide-blog-posting', 'es', 'guide-blog-posting');
INSERT INTO `text` VALUES('guide-project-comment', 'es', 'guide-project-comment');
INSERT INTO `text` VALUES('guide-project-contract-information', 'es', 'guide-project-contract-information');
INSERT INTO `text` VALUES('guide-project-costs', 'es', 'GUIA-PROYECTO-COSTES <br>EdiciÃ³n en proceso<br>\r\n\r\nTe pedimos que hagas un pequeÃ±o presupuesto, desglosando los costes que vas a tener. Se puede elegir entre tareas, infraestructura y materiales. Como verÃ¡s hay un recuadro que pone "imprescindible", si lo marcas el importe irÃ¡ a la columna de mÃ­nimo, si no lo marcas irÃ¡ a la columna de optimo. Cuando tu proyecto entre en campaÃ±a tendrÃ¡s 40 dÃ­as para obtener el mÃ­nimo y otros 40 para conseguir el resto. Se realista en los costes y explica brevemente porque necesitas cada uno de los costes. No hay problema que sÃ³lo haya un coste pero explÃ­calo.  \r\nLa claridad y la coherencia entre lo que pides, el tiempo para desarrollarlo y los resultados darÃ¡n mÃ¡s confianza al proyecto.  Todos estos campos puntuan 1 punto, cuanto mÃ¡s precisiÃ³n (desglose y agenda) mÃ¡s se puntua aunque no es obligatorio hacerlo. \r\n');
INSERT INTO `text` VALUES('guide-project-description', 'es', 'guide-project-description');
INSERT INTO `text` VALUES('guide-project-error-mandatories', 'es', 'Faltan campos obligatorios');
INSERT INTO `text` VALUES('guide-project-overview', 'es', 'GUIA- DESCRIPCIÃ“N-PROYECTO <br>\r\n\r\n(Probando ediciÃ³n)<br>\r\n\r\nEstÃ¡ informaciÃ³n es la cara y los ojos  que se encontrarÃ¡ el visitante al llegar a tu proyecto. Cuida bien la redacciÃ³n y la ortografÃ­a, trata de ser breve pero dando informaciÃ³n relevante y aÃ±adiendo links a webs externas para que quien quiera, pueda profundizar sobre tus otros proyectos, este que presentas en particular, o sobre ti mismo. Insiste en las caracterÃ­sticas que convierte a tu proyecto en algo especial y diferente del resto. Habla de los beneficios sociales, econÃ³micos posibles que puede tener. Y que no se te olvide aÃ±adir un vÃ­deo, ya sea una auto-entrevista,  un demo, o un clip promocional, pocos proyectos consiguen financiaciÃ³n si no tienen un vÃ­deo corto y con chispa que resume tus ideas y presenta a la persona que hay detrÃ¡s. ');
INSERT INTO `text` VALUES('guide-project-preview', 'es', 'Puedes repasar los puntos marcados en rojo y mejorar el porcentaje o enviar el\r\ndefinitivamente el proyecto para ser valorado por el equipo Goteo.\r\nRecibirÃ¡s una comunicaciÃ³n con toda la informaciÃ³n e indicarÃ¡ los pasos a seguir y\r\nrecomendaciones para que tu proyecto pueda alcanzar exitosamente la meta\r\npropuesta.');
INSERT INTO `text` VALUES('guide-project-rewards', 'es', 'Texto guÃ­a en el paso RETORNO del formulario de proyecto.');
INSERT INTO `text` VALUES('guide-project-success-minprogress', 'es', 'Ha llegado al porcentaje mÃ­nimo');
INSERT INTO `text` VALUES('guide-project-success-noerrors', 'es', 'Todos los campos obligatorios estan rellenados');
INSERT INTO `text` VALUES('guide-project-success-okfinish', 'es', 'Puede enviar para revisiÃ³n');
INSERT INTO `text` VALUES('guide-project-support', 'es', 'guide-project-support');
INSERT INTO `text` VALUES('guide-project-supports', 'es', 'Texto guÃ­a en el paso COLABORACIONES del formulario de proyecto.');
INSERT INTO `text` VALUES('guide-project-updates', 'es', 'guide-project-updates');
INSERT INTO `text` VALUES('guide-project-user-information', 'es', '(1.Texto guÃ­a en el paso USUARIO/PERFIL)<br>\r\nEn este apartado se encuentra la informaciÃ³n personal que es pÃºblica en tu perfil de usuario.  <br>\r\nSi presentas un proyecto deberÃ¡s esmerarte en el texto sobre ti, no olvides aÃ±adir links relevantes sobre tu trayectoria para que quien estÃ© interesado en tu proyecto te conozca mejor o poner una imagen. Cada campo que rellenes puntÃºa.');
INSERT INTO `text` VALUES('guide-user-data', 'es', 'Texto guÃ­a en la ediciÃ³n de campos sensibles.');
INSERT INTO `text` VALUES('guide-user-information', 'es', '2.Texto guÃ­a en la ediciÃ³n de informaciÃ³n del usuario. ???????');
INSERT INTO `text` VALUES('guide-user-register', 'es', 'Texto guÃ­a en el registro de un nuevo usuario.');
INSERT INTO `text` VALUES('invest-abitmore', 'es', 'Por %s mÃ¡s serÃ­as %s');
INSERT INTO `text` VALUES('login-acces-password-field', 'es', 'ContraseÃ±a');
INSERT INTO `text` VALUES('login-acces-username-field', 'es', 'Usuario');
INSERT INTO `text` VALUES('mandatory-cost-field-amount', 'es', 'Es obligatorio ponerle un importe a los costes');
INSERT INTO `text` VALUES('mandatory-cost-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n a los costes');
INSERT INTO `text` VALUES('mandatory-cost-field-name', 'es', 'Es obligatorio ponerle un nombre al coste');
INSERT INTO `text` VALUES('mandatory-cost-field-type', 'es', 'Es obligatorio seleccionar el tipo de coste');
INSERT INTO `text` VALUES('mandatory-individual_reward-field-amount', 'es', 'Es obligatorio indicar el importe que otorga la recompensa');
INSERT INTO `text` VALUES('mandatory-individual_reward-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n');
INSERT INTO `text` VALUES('mandatory-individual_reward-field-name', 'es', 'Es obligatorio poner la recompensa');
INSERT INTO `text` VALUES('mandatory-project-costs', 'es', 'Debe desglosar en al menos dos costes.');
INSERT INTO `text` VALUES('mandatory-project-field-about', 'es', 'Es obligatorio explicar quÃ© es en la descripciÃ³n del proyecto');
INSERT INTO `text` VALUES('mandatory-project-field-address', 'es', 'La direcciÃ³n del responsable del proyecto es obligatoria');
INSERT INTO `text` VALUES('mandatory-project-field-category', 'es', 'Es obligatorio elegir al menos una categoria para el proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-contract-email', 'es', 'Es obligatorio poner el email del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-contract-name', 'es', 'Es obligatorio poner el nombre del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-contract-nif', 'es', 'Es obligatorio poner el documento de identificacciÃ³n del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-contract-surname', 'es', 'Es obligatorio poner los apellidos del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-country', 'es', 'El paÃ­s del responsable del proyecto es obligatorio');
INSERT INTO `text` VALUES('mandatory-project-field-description', 'es', 'Es obligatorio poner una descripciÃ³n al proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-goal', 'es', 'Es obligatorio explicar los objetivos en la descripciÃ³n del proyecto');
INSERT INTO `text` VALUES('mandatory-project-field-image', 'es', 'Es obligatorio poner una imagen al proyecto');
INSERT INTO `text` VALUES('mandatory-project-field-location', 'es', 'Es obligatorio poner la localizaciÃ³n donde se llevarÃ¡ a cabo el proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-media', 'es', 'Poner un vÃ­deo para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('mandatory-project-field-motivation', 'es', 'Es obligatorio explicar la motivaciÃ³n en la descripciÃ³n del proyecto');
INSERT INTO `text` VALUES('mandatory-project-field-name', 'es', 'Es obligatorio poner un NOMBRE al proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-phone', 'es', 'El telÃ©fono del responsable del proyecto es obligatorio');
INSERT INTO `text` VALUES('mandatory-project-field-related', 'es', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripciÃ³n del proyecto');
INSERT INTO `text` VALUES('mandatory-project-field-residence', 'es', 'Es obligatorio poner el lugar de residencia del responsable del proyecto.');
INSERT INTO `text` VALUES('mandatory-project-field-resource', 'es', 'Es obligatorio especificar si cuentas con otros recursos');
INSERT INTO `text` VALUES('mandatory-project-field-zipcode', 'es', 'El cÃ³digo postal del responsable del proyecto es obligatorio');
INSERT INTO `text` VALUES('mandatory-register-field-email', 'es', 'Tienes que poner un email');
INSERT INTO `text` VALUES('mandatory-social_reward-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n al retorno');
INSERT INTO `text` VALUES('mandatory-social_reward-field-name', 'es', 'Es obligatorio poner el retorno');
INSERT INTO `text` VALUES('mandatory-support-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n');
INSERT INTO `text` VALUES('mandatory-support-field-name', 'es', 'Es obligatorio ponerle un nombre a la colaboraciÃ³n');
INSERT INTO `text` VALUES('profile-profile-my_projects-header-header', 'es', 'profile-profile-my_projects-header-header');
INSERT INTO `text` VALUES('project-messages-send_message-your_answer', 'es', 'Escribe tu respuesta aquÃ­');
INSERT INTO `text` VALUES('project-rewards-individual_reward-units_left', 'es', 'Quedan <span class="left">%s</span> unidades');
INSERT INTO `text` VALUES('regular-days', 'es', 'dÃ­as');
INSERT INTO `text` VALUES('regular-idnetica', 'es', 'regular-idnetica');
INSERT INTO `text` VALUES('regular-mandatory', 'es', 'Campo obligatorio!');
INSERT INTO `text` VALUES('step-2', 'es', 'Datos personales');
INSERT INTO `text` VALUES('step-3', 'es', 'DescripciÃ³n');
INSERT INTO `text` VALUES('step-4', 'es', 'Costes');
INSERT INTO `text` VALUES('step-5', 'es', 'Retorno');
INSERT INTO `text` VALUES('step-6', 'es', 'Colaboraciones');
INSERT INTO `text` VALUES('step-7', 'es', 'PrevisualizaciÃ³n');
INSERT INTO `text` VALUES('step-costs', 'es', 'Proyecto / Costes');
INSERT INTO `text` VALUES('step-overview', 'es', 'Proyecto / DescripciÃ³n');
INSERT INTO `text` VALUES('step-preview', 'es', 'Proyecto / PrevisualizaciÃ­on');
INSERT INTO `text` VALUES('step-rewards', 'es', 'Proyecto / Retornos');
INSERT INTO `text` VALUES('step-supports', 'es', 'Proyecto / Colaboraciones');
INSERT INTO `text` VALUES('step-userPersonal', 'es', 'Usuario / Datos personales');
INSERT INTO `text` VALUES('step-userProfile', 'es', '4.Usuario / Perfil');
INSERT INTO `text` VALUES('subject-change-email', 'es', 'Asunto del mail al cambiar el email');
INSERT INTO `text` VALUES('subject-register', 'es', 'Asunto del email al registrarse');
INSERT INTO `text` VALUES('tooltip-individual_reward-social_reward-icon', 'es', 'Texto tooltip tipo de recompensa');
INSERT INTO `text` VALUES('tooltip-project-about', 'es', '6 Consejo para rellenar el campo quÃ© es');
INSERT INTO `text` VALUES('tooltip-project-address', 'es', 'P2-Consejo-5 Consejo para rellenar el address del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-category', 'es', '10 Consejo para seleccionar la categorÃ­a del proyecto');
INSERT INTO `text` VALUES('tooltip-project-comment', 'es', 'Tooltip campo comentario');
INSERT INTO `text` VALUES('tooltip-project-contract_email', 'es', 'P2-Consejo-?? Consejo para rellenar el email del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-contract_name', 'es', 'P2-Consejo-2  Consejo para rellenar el nombre del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-contract_nif', 'es', 'P2-Consejo-3  Consejo para rellenar el nif del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-contract_surname', 'es', 'P2-Consejo-5  Consejo para rellenar el apellido del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-cost', 'es', '?? Consejo para editar desgloses existentes');
INSERT INTO `text` VALUES('tooltip-project-cost-amount', 'es', 'NO FUNCIONA 5 Texto tooltip cantidad coste');
INSERT INTO `text` VALUES('tooltip-project-cost-cost', 'es', '2 Texto tooltip nombre coste');
INSERT INTO `text` VALUES('tooltip-project-cost-dates', 'es', '5 Texto tooltip fechas costes');
INSERT INTO `text` VALUES('tooltip-project-cost-description', 'es', '4 Texto tooltip descripcion costes');
INSERT INTO `text` VALUES('tooltip-project-cost-from', 'es', '7 Texto tooltip fecha desde costes');
INSERT INTO `text` VALUES('tooltip-project-cost-required', 'es', '6 Imprescindible o Secundario');
INSERT INTO `text` VALUES('tooltip-project-cost-type', 'es', '3 Texto tooltip tipo de coste');
INSERT INTO `text` VALUES('tooltip-project-cost-until', 'es', '???10 Texto tooltip fecha coste hasta');
INSERT INTO `text` VALUES('tooltip-project-costs', 'es', '7 Agenda?? Texto tooltip desglose de costes.');
INSERT INTO `text` VALUES('tooltip-project-country', 'es', 'P2-Consejo-8  Consejo para rellenar el paÃ­s del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-currently', 'es', '13 Consejo para rellenar el estado de desarrollo del proyecto');
INSERT INTO `text` VALUES('tooltip-project-description', 'es', '5 Describe el proyecto con un mÃ­nimo de 150 palabras, menos palabras te marcara error.\r\nDescribelo de manera que sea facil de entender para cualquier persona. Intenta darle un enfoque atractivo y social. No escribas un texto demasiado largo en este campo, si lo haces la gente no leerÃ¡ el resto de informaciÃ³n.');
INSERT INTO `text` VALUES('tooltip-project-goal', 'es', '8 Consejo para rellenar el campo objetivos');
INSERT INTO `text` VALUES('tooltip-project-image', 'es', '3 Consejo para rellenar la imagen del proyecto');
INSERT INTO `text` VALUES('tooltip-project-image_upload', 'es', '4 Texto tooltip subir imagen proyecto');
INSERT INTO `text` VALUES('tooltip-project-individual_reward', 'es', 'Consejo para editar retornos individuales existentes');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-amount', 'es', 'Texto tooltip cantidad para recompensa');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-description', 'es', 'Texto tooltip descripcion recompensa');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-reward', 'es', 'Texto tooltip nombre recompensa');
INSERT INTO `text` VALUES('tooltip-project-individual_reward-units', 'es', 'Texto tooltip unidades de recompensa');
INSERT INTO `text` VALUES('tooltip-project-individual_rewards', 'es', 'Texto tooltip recompensas individuales');
INSERT INTO `text` VALUES('tooltip-project-keywords', 'es', '11 Consejo para rellenar las palabras clave del proyecto');
INSERT INTO `text` VALUES('tooltip-project-location', 'es', 'P2-Consejo-7  Consejo para rellenar el lugar de residencia del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-media', 'es', '12 Consejo para rellenar el media del proyecto');
INSERT INTO `text` VALUES('tooltip-project-motivation', 'es', '7 Consejo para rellenar el campo motivaciÃ³n');
INSERT INTO `text` VALUES('tooltip-project-name', 'es', '2 Consejo para rellenar el nombre del proyecto');
INSERT INTO `text` VALUES('tooltip-project-ncost', 'es', '??? Consejo para rellenar un nuevo desglose de costes.');
INSERT INTO `text` VALUES('tooltip-project-nindividual_reward', 'es', 'Consejo para rellenar un nuevo retorno individual');
INSERT INTO `text` VALUES('tooltip-project-nsocial_reward', 'es', 'Consejo para rellenar un nuevo retorno colectivo');
INSERT INTO `text` VALUES('tooltip-project-nsupport', 'es', 'Consejo para rellenar una nueva colaboraciÃ³n');
INSERT INTO `text` VALUES('tooltip-project-phone', 'es', 'P2-Consejo-4  Consejo para rellenar el telÃ©fono del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-project-project_location', 'es', '12 XXXXX donde esta Consejo para rellenar la localizaciÃ³n del proyecto');
INSERT INTO `text` VALUES('tooltip-project-related', 'es', '9 Consejo para rellenar el campo experiencia relacionada y equipo');
INSERT INTO `text` VALUES('tooltip-project-resource', 'es', '9 Indica si tienes otras fuentes de financiaciÃ³n, recursos propios o ya has hecho acopio de materiales. ');
INSERT INTO `text` VALUES('tooltip-project-schedule', 'es', '??? Texto tooltip agenda del proyeecto');
INSERT INTO `text` VALUES('tooltip-project-scope', 'es', 'tooltip-project-scope');
INSERT INTO `text` VALUES('tooltip-project-social_reward', 'es', 'Consejo para editar retornos colectivos existentes');
INSERT INTO `text` VALUES('tooltip-project-social_reward-description', 'es', 'Texto tooltip descripcion retorno');
INSERT INTO `text` VALUES('tooltip-project-social_reward-icon', 'es', 'Aqui se tienen que definir los difrentes tipos de retornos. \r\nDISEÃ‘O: Nos referimos a diseÃ±o de planos o plantilla \r\nCODÃGO: Codigo informatico\r\nARCHIVOS DIGITALES: Musica, video, media, libro, pdf \r\nMANUALES: kits o todo\\''s. Business plans, Receta. InformaciÃ³n escrita\r\nSERVICIOS: Talleres, cursos, websites, database en vivo o online. precisar en que contexto y donde ');
INSERT INTO `text` VALUES('tooltip-project-social_reward-license', 'es', 'Texto tooltip licencia retorno');
INSERT INTO `text` VALUES('tooltip-project-social_reward-reward', 'es', 'Texto tooltip nombre retorno');
INSERT INTO `text` VALUES('tooltip-project-social_rewards', 'es', 'Texto tooltip retornos colectivos');
INSERT INTO `text` VALUES('tooltip-project-support', 'es', 'Consejo para editar colaboraciones existentes');
INSERT INTO `text` VALUES('tooltip-project-support-description', 'es', 'Texto tooltip descripcion colaboracion');
INSERT INTO `text` VALUES('tooltip-project-support-support', 'es', 'Texto tooltip nombre colaboracion');
INSERT INTO `text` VALUES('tooltip-project-support-type', 'es', 'Texto tooltip tipo colaboracion');
INSERT INTO `text` VALUES('tooltip-project-supports', 'es', 'Texto tooltip colaboraciones');
INSERT INTO `text` VALUES('tooltip-project-totals', 'es', '8 Este grÃ¡fico sitÃºa la suma de tus costes imprescindibles (mÃ­nimos para poder realizar el proyecto) y la suma de los costes imprescindibles y secundarios, que darÃ­an el presupuesto Ã³ptimo para que desarrolles el proyecto. Esto significa que hay 2 rondas de financiaciÃ³n. La primera de 45 dÃ­as para conseguir el MÃ­nimo, sÃ³lo si has conseguido ese volumen de financiaciÃ³n colectiva podrÃ¡s optar a la segunda ronda de otros 45 dÃ­as para llegar al presupuesto Ã“ptimo.');
INSERT INTO `text` VALUES('tooltip-project-zipcode', 'es', 'P2-Consejo-6  Consejo para rellenar el zipcode del responsable del proyecto');
INSERT INTO `text` VALUES('tooltip-updates-allow_comments', 'es', 'tooltip-updates-allow_comments');
INSERT INTO `text` VALUES('tooltip-updates-date', 'es', 'tooltip-updates-date');
INSERT INTO `text` VALUES('tooltip-updates-home', 'es', 'tooltip-updates-home');
INSERT INTO `text` VALUES('tooltip-updates-image', 'es', 'tooltip-updates-image');
INSERT INTO `text` VALUES('tooltip-updates-image_upload', 'es', 'tooltip-updates-image_upload');
INSERT INTO `text` VALUES('tooltip-updates-media', 'es', 'tooltip-updates-media');
INSERT INTO `text` VALUES('tooltip-updates-tags', 'es', 'tooltip-updates-tags');
INSERT INTO `text` VALUES('tooltip-updates-text', 'es', 'tooltip-updates-text');
INSERT INTO `text` VALUES('tooltip-updates-title', 'es', 'tooltip-updates-title');
INSERT INTO `text` VALUES('tooltip-user-about', 'es', '5 Consejo para rellenar el cuÃ©ntanos algo sobre ti');
INSERT INTO `text` VALUES('tooltip-user-avatar_upload', 'es', 'Texto tooltip subir imagen usuario');
INSERT INTO `text` VALUES('tooltip-user-blog', 'es', 'Consejo para rellenar la web ?????');
INSERT INTO `text` VALUES('tooltip-user-contribution', 'es', '8 Consejo para rellenar el quÃ© podrÃ­as aportar en goteo xxx');
INSERT INTO `text` VALUES('tooltip-user-email', 'es', '????? Consejo para rellenar el email de registro de usuario');
INSERT INTO `text` VALUES('tooltip-user-facebook', 'es', '10 Consejo para rellenar el facebook');
INSERT INTO `text` VALUES('tooltip-user-identica', 'es', 'tooltip-user-identica');
INSERT INTO `text` VALUES('tooltip-user-image', 'es', 'P2-Consejo-10  Consejo para rellenar la imagen del usuario');
INSERT INTO `text` VALUES('tooltip-user-interests', 'es', '6 Consejo para seleccionar tus intereses');
INSERT INTO `text` VALUES('tooltip-user-keywords', 'es', '7 Consejo para rellenar tus palabras clave');
INSERT INTO `text` VALUES('tooltip-user-linkedin', 'es', '12 Consejo para rellenar el linkedin');
INSERT INTO `text` VALUES('tooltip-user-location', 'es', 'Texto tooltip lugar de residencia del usuario');
INSERT INTO `text` VALUES('tooltip-user-name', 'es', '2 Consejo para rellenar el nombre completo del usuario');
INSERT INTO `text` VALUES('tooltip-user-twitter', 'es', '11 Consejo para rellenar el twitter');
INSERT INTO `text` VALUES('tooltip-user-user', 'es', '8 Consejo para rellenar el nombre de usuario para login');
INSERT INTO `text` VALUES('tooltip-user-webs', 'es', 'Texto tooltip webs del usuario');
INSERT INTO `text` VALUES('user-account-inactive', 'es', 'La cuenta esta desactivada');
INSERT INTO `text` VALUES('user-activate-already-active', 'es', 'La cuenta de usuario ya esta activada');
INSERT INTO `text` VALUES('user-activate-fail', 'es', 'Error al activar la cuenta de usuario');
INSERT INTO `text` VALUES('user-activate-success', 'es', 'La cuenta de usuario se ha activado correctamente');
INSERT INTO `text` VALUES('user-changeemail-fail', 'es', 'Error al cambiar el email');
INSERT INTO `text` VALUES('user-changeemail-success', 'es', 'El email se ha cambiado con exito');
INSERT INTO `text` VALUES('user-register-success', 'es', 'El usuario se ha registrado correctamente');
INSERT INTO `text` VALUES('validate-cost-field-dates', 'es', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-project-field-about', 'es', 'La explicacion del proyecto es demasiado corta');
INSERT INTO `text` VALUES('validate-project-field-costs', 'es', 'Desglosar hasta 5 costes para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-project-field-currently', 'es', 'Indicar el estado del proyecto para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-project-field-description', 'es', 'La descripcion del proyecto es demasiado corta');
INSERT INTO `text` VALUES('validate-project-field-motivation', 'es', 'validate-project-field-motivation');
INSERT INTO `text` VALUES('validate-project-individual_rewards', 'es', 'Indicar hasta 5 recompensas individuales para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-project-social_rewards', 'es', 'Indicar hasta 5 retornos colectivos para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-project-total-costs', 'es', 'El coste Ã³ptimo no puede superar en mÃ¡s de un 40% al coste mÃ­nimo. Revisar el DESGLOSE DE COSTES.');
INSERT INTO `text` VALUES('validate-project-value-contract-email', 'es', 'El EMAIL no es correcto.');
INSERT INTO `text` VALUES('validate-project-value-contract-nif', 'es', 'El NIF no es correcto.');
INSERT INTO `text` VALUES('validate-project-value-description', 'es', 'La DESCRIPCIÃ“N del proyecto es demasiado corta.');
INSERT INTO `text` VALUES('validate-project-value-keywords', 'es', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-project-value-phone', 'es', 'El TELÃ‰FONO no es correcto.');
INSERT INTO `text` VALUES('validate-register-value-email', 'es', 'El email introducido no es valido');
INSERT INTO `text` VALUES('validate-social_reward-license', 'es', 'Indicar una licencia para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-user-field-about', 'es', 'Cuenta algo sobre ti para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-user-field-avatar', 'es', 'Pon una imagen de perfil para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-user-field-contribution', 'es', 'Explica que podrias aportar en Goteo para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-user-field-facebook', 'es', 'Pon tu cuenta de facebook para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-user-field-interests', 'es', 'Selecciona algÃºn interÃ©s para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-user-field-keywords', 'es', 'Indica hasta 5 palabras clave que te definan para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-user-field-linkedin', 'es', 'El campo linkedin no es valido');
INSERT INTO `text` VALUES('validate-user-field-location', 'es', 'El lugar de residencia del usuario no es valido');
INSERT INTO `text` VALUES('validate-user-field-name', 'es', 'Pon tu nombre completo para mejorar la puntuaciÃ³n');
INSERT INTO `text` VALUES('validate-user-field-twitter', 'es', 'El twitter del usuario no es valido');
INSERT INTO `text` VALUES('validate-user-field-webs', 'es', 'Pon tu pÃ¡gina web para mejorar la puntuaciÃ³n');

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
  `identica` tinytext,
  `facebook` tinytext,
  `google` tinytext,
  `linkedin` tinytext,
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `user`
--

INSERT INTO `user` VALUES('aballesteros', 'AndrÃ©s Ballesteros', 'CÃ¡ceres', 'geopetro10@yahoo.es', '975b5f25e95d2afd7c22b50342ddffc5edba89a1', 'Abvmedioambiente', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('abenitez', 'Alba Benitez', 'CÃ¡ceres', 'albabenitez1983@gmail.com', 'ccb39ea899a2dbc60867cfec2495ffcbfb2eb42d', 'AutÃ³noma-Sector cultural', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 02:40:12', '');
INSERT INTO `user` VALUES('aceballos', 'Ana Ceballos', 'Madrid', 'veyota79@hotmail.com', 'db5c3952834c9f1a64f4086e94afa40013dd3a5b', 'Gestor Cultural', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('acomunes', 'AsociaciÃ³n comunes', 'Madrid', 'comunes@ourproject.org', '6627f75c127c59b0d0827429d456c95545d056e7', '', '', 1, 0, '', 'movecommons', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-05 16:16:34', '');
INSERT INTO `user` VALUES('afernandez', 'Ana Fdez Osorio', 'Barcelona', 'ana@dispuesta.net', 'bc4b56d5b2fb0ddb27e4ba97ed8483c2b96b9b43', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('afolguera', 'Antonia Folguera', 'Barcelona', 'antonia@riereta.net', '232083fdb8d80ac58b79ab55b75d302e54b5038b', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('agallastegi', 'Asier Gallastegi', 'Bilbao', 'asier.gallastegi@gmail.com', 'ddbc0e3ef9225b50a936adc43e833f238b13680d', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('ahernandez', 'Alejandro HernÃ¡ndez Renner', 'CÃ¡ceres', 'ahernandez@lossantos.org', '79dd1d71cb13c723ac077e636c47d14720498b4c', 'Director FundaciÃ³n Maimona', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 11:10:37', '');
INSERT INTO `user` VALUES('airiarte', 'Arantza Iriarte', 'Bilbao', 'airiarte@landspain.com', '7d2da1a0e004ba1f56454288d22efe27f121de6c', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('amartinez', 'Alfonso MartÃ­nez', 'Barcelona', 'martinezrubioo@gmail.com', 'b9ee4c65c2de3f007eef8e0cd9e39d2ef49aa17b', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 11:10:37', '');
INSERT INTO `user` VALUES('amorales', 'Ana Morales', 'Madrid', 'moralespartida@gmail.com', 'e8396d7fc730a83111aa251a80d44e4295d15d85', 'Trabajo en temas culturales', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('amunoz', 'AscensiÃ³n MuÃ±oz BenÃ­tez', 'CÃ¡ceres', 'chonmube@gmail.com', '18542a1914f496a4c66739483a45620edfc40a65', 'AutÃ³noma-Gestora cultural', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-07-05 01:54:33', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('aollero', 'Ana  Ollero', 'CÃ¡ceres', 'programaskreativos@gmail.com', 'a1d29ffc56898468565d89a267fa13b40d396278', 'Programas creativos', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('aramos', 'Avelino Ramos Casado', 'CÃ¡ceres', 'crdelcorral@hotmail.com', '54a53a49e8c51e869cdc0f8f5513d48ec8e3e149', 'Sertumon', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('arecio', 'Anto Recio', 'CÃ¡ceres', 'anto@filosomatika.net', '31d6347ed7f1809654680fb6f1bb5bed2ab07408', 'Fundecyt', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('asanz', 'Ana SÃ¡nz Grados', 'CÃ¡ceres', 'asanzgr@hotmail.com', 'a32009cc0d01b5a96aced6cff6cf4be61e8fbd7c', 'AsociaciÃ³n de Mujeres Malvaluna', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('avigara', 'Ana Vigara', 'Madrid', 'ana.vigara@iniciativajoven.org', 'b093c07f7a1c81bb216456e7cceb0520a632437f', 'TÃ©cnico de proyectos', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 02:39:57', '');
INSERT INTO `user` VALUES('blozano', 'Betania Lozano', 'Madrid', 'betanialozano@yahoo.com', '5ddcbe4a9c36ba3bd05ea0eb8d4f09ed422f5139', 'Gestora Cultura. Co-directora de la Muestra IN-SONORA', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('bramos', 'Beatriz Ramos', 'Madrid', 'beatriz@iniciativajovn.org', '7e6d5e13cc911119538fd2a00e5900e37df9a711', 'TÃ©cnico de proyectos', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('bsampayo', 'Blanca Sampayo', 'Madrid', 'blancasampayo@gmail.com', 'bf3d17a170f915f2f0df09f33ecdb8ec4883648d', 'Estoy haciendo un Master de GestiÃ³n Cultural, y un curso de InnovaciÃ³n Abierta en GestiÃ³n Cultural,', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('caagon', 'Carla A. Agon', 'Barcelona', 'carla.a.agon@gmail.com', '9fa69b1368644376888dd1d0b3e68db230f80c1d', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('carlaboserman', 'Carla Boserman', 'Sevilla', 'carlaboserman@gmail.com', 'df9b92ece4cebf81da2789d7a55d949fe1bb5a69', '', '', 1, 0, '', '', '', '', NULL, '', 1, '0000-00-00 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('ccarrera', 'Candela Carrera', 'Barcelona', 'candela.carrera@gmail.com', 'dd7d71c995d705193cf659f5019eca491f7ad8ef', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('ccriado', 'Carlos Criado', 'CÃ¡ceres', 'carlos@carloscriado.es', 'dfce3f8d5e9722b20d20fe9fca259927d37e6856', 'FotoExtremadura', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('cmartinez', 'Cayetana Martinez', 'Madrid', 'cayetana109@gmail.com', '21470b6c5d5fd7c86af61904cfa25095184b0693', 'gestiÃ³n cultural. Trabajo con varias asociaciones culturales y de emprendedores y querrÃ­a conocer mÃ¡s de estas nuevas formas de financiaciÃ³n y organizaciÃ³n.', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('cphernandez', 'Claudia Patricia HernÃ¡ndez', 'Madrid', 'patriciavergara83@hotmail.com', '556be5beee418fda6e1601867ef3fd3395b80f7f', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('cpinero', 'Carlos PiÃ±ero Medina', 'CÃ¡ceres', 'innovacion@energiaextremadura.org', '2a22c481b606f2758f06762925c06e6cf4b5ca6e', 'Cluster de la EnergÃ­a de Extremadura. Dpto. de I+D+i y formaciÃ³n. ', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-07-05 01:54:45', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('criera', 'Cristina Riera ', 'Barcelona', 'criera@transit.es', 'a37535b1bb821dbc5495ddd55ddd04b9b4f485ed', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:08', '');
INSERT INTO `user` VALUES('dcabo', 'David Cabo', 'Madrid', 'david.cabo@gmail.com', '2e0b97b278aaadfdda6c1c5963212f28c9e59796', 'Ayudando a ONG Pro Bono Publico', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('dcuartielles', 'David Cuartielles', 'MalmÃ¶', 'dcuartielles@gmail.com', 'bbd0e343e48cdc7dcfc5515641e9c6b32e4e03af', '', '', 1, 0, '', '', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-05 11:30:37', '');
INSERT INTO `user` VALUES('diegobus', 'diegobus', '', 'diegobus@pelousse.com', '4de99ca64d7a4003ac8724323da63e20b413db15', 'test. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla dolor quis elit cursus sit amet tincidunt elit ultrices. Mauris ultricies auctor velit vel tempor. Integer sollicitudin consequat ultrices. Fusce at ante sit amet augue dapibus mattis.', 'test, test2', 1, 8, 'Mauris fringilla dolor quis elit cursus sit amet tincidunt elit ultrices. Mauris ultricies auctor velit vel tempor. Integer sollicitudin consequat ultrices. Fusce at ante sit amet augue dapibus mattis.', '', '', '', NULL, '', 0, '2011-05-10 18:32:15', '2011-07-04 18:48:21', '');
INSERT INTO `user` VALUES('domenico', 'Domenico Di Siena', 'Madrid', 'domenico@ecosistemaurbano.com', 'af915aa406c73c1e7a22ace8e7417ce02e222679', '', '', 1, 0, '', 'urbsocialdesign', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-05 12:35:25', '');
INSERT INTO `user` VALUES('ebai', 'Eva Bai', 'Bilbao', 'eva.alija@gmail.com', '7bf8881c975d50142a8f44768fda76101f86aab5', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('ebaraonap', 'Ethel Baraona Pohl', 'Barcelona', 'tusojos8@yahoo.com', '4483c14c633775292d0b9271cdec409a61387788', '', '', 1, 0, '', 'archinhand', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-05 12:47:30', '');
INSERT INTO `user` VALUES('ebarrenetxea', 'Eukene Barrenetxea', 'Bilbao', 'eukenebarrenetxea@gmail.com', '92afc493fbd0f74e207bf2fd60636f33edd11db3', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('efoglia', 'EfraÃ­n Foglia', 'Barcelona', 'mexmafia@gmail.com', '9f1b78537a645a62e8404b774b0b69b8529e90c6', '', '', 1, 0, '', 'EfrainFoglia', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-05 16:47:13', '');
INSERT INTO `user` VALUES('elopez', 'Elvira LÃ³pez', 'Madrid', 'elvirilay@hotmail.com', '1d1d375939c74c8f755355f731c9eed3b4ee4297', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('emartinena', 'Esteban Martinena', 'CÃ¡ceres', 'orensbruli@gmail.com', 'f0175a18e42135b86232c00ab1db27acb6b224be', 'FotÃ³grafo Agencia EFE', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('emonivas', 'Esther MoÃ±ivas', 'Madrid', 'esther.monivas@gmail.com', '516d9ae4faac6f1425ef5fb0327d6bbb7948ea0b', 'Codirectora de la ONG AcciÃ³n C', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-07-05 01:55:04', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('emuruzabal', 'Eneko Muruzabal', 'Bilbao', 'info@diseinugile.com', 'ccce002e3264bcfaf344858defd99e661d1943d6', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('eportillo', 'Esperanza Portillo', 'Madrid', 'portillo.esperanza@gmail.com', '549777e061b8dbadd683ca5293444f4095beaabf', 'Estudiante de master en gestiÃ³n cultural', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('esenabre', 'esenabre', 'Barcelona', 'esenabre@gmail.com', '60ffeee026521957cfd8585edf4b07e1541f640c', 'Livingg la vida loca', 'salud', 1, 27, 'Este campo va fuera', 'http://twitter.com/esenabre', 'http://www.facebook.com/esenabre', '', NULL, 'http://www.linkedin.com/in/esenabre', 1, '2011-06-15 11:33:12', '2011-07-07 21:52:09', '60109fe996530911ff113b9b1b55382f');
INSERT INTO `user` VALUES('evandellos', 'Emma VandellÃ³s ', 'Barcelona', 'emma.vandellos@esade.edu', 'f4ba05222b034f22339c7fb0ef299b925f02dcc8', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:08', '');
INSERT INTO `user` VALUES('fandres', 'Fernando Andres', 'Santurtzi', 'fandres@virgen-del-mar.com', '7c37d99852bcd2ccce0a976f94d8035e79a463d5', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('fcingolani', 'Francesco Cingolani', 'Madrid', 'fc@ecosistemaurbano.com', '2c6abab20f50218be59a8d2d62c100a9dc9d3a08', 'Arquitecto. Soy responsable de Urban Social Design Experience, un proyecto de networked learning promovido por la asociaciÃ³n Urban Social Design', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:08', '');
INSERT INTO `user` VALUES('fcoddou', 'Flavio Coddou', 'Barcelona', 'flaviocoddou@gmail.com', '3e61f6f52ca6136091ced69b745d79fd88e58f71', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:08', '');
INSERT INTO `user` VALUES('ffreitas', 'Flavia Freitas', 'Barcelona', 'flavia.frr@gmail.com', '023434d501234549299f47c90b37249b8544b7b5', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('fingrassia', 'Franco Ingrassia', 'Rosario', 'francoingrassia@gmail.com', '3e990f6eee34056154296953598b61d10d9c55f3', 'PsicÃ³logo. Involucrado en el proceso de constituciÃ³n de un Laboratorio del ProcomÃºn en la ciudad de Rosario (Argentina)', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('gbento', 'Gisele Bento', 'Barcelona', 'giselecultura@gmail.com', '1d0bee13a33f3ce2fcc7b8c83596c903c9d5a42d', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('gbezanilla', 'Gerardo Bezanilla', 'Madrid', 'gerardo@beusual.com', 'bda58288e44e1011c2cf94ebe4582662f9876497', 'DiseÃ±ador, Editor, Agente Cultural', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('gbossio', 'Gabriela Bossio', 'Madrid', 'gabrielabossio@gmail.com', '9e832ab9a645262404ca0e44b6616aca990b5fec', 'Directora Creativa y Gestora cultural de La casa del Ãrbol', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('geraldo', 'Gerald Kogler y Marti Sanchez', 'Barcelona', 'geraldo@servus.at', '409bca035255a1d86114e2f2e74476375fdb11f4', '', '', 1, 0, '', '', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-04 18:49:38', '');
INSERT INTO `user` VALUES('gnarros', 'GermÃ¡n Narros Lluch', 'CÃ¡ceres', 'german@caceresentumano.com', '5a6eb69bae6b03a73d6956552e21ea0ce515e878', 'caceresentumano.com', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('goteo', 'Goteo Platoniq', 'mhkjghsdkj fkj sadkjf', 'goteo@doukeshi.org', 'b57a92c9a6501f4542d670f2a13e98287fc596ca', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'EconomÃ­a distribuida, Comunidades / Redes, InnovaciÃ³n social, TICs, CreaciÃ³n de redes, Prosumidores, Monedas complementarias, Redes sociales', 1, 7, '* BÃºsqueda y anÃ¡lisis de experiencias y herramientas que sirvan de modelo.\r\n* AsesorÃ­as tÃ©cnicas (seguridad en la red), legales y administrativas.\r\n* Crear las bases para desarrollar un prototipo.', 'platoniq', 'platoniq', 'facebook', 'google', 'platoniq', 0, '2011-05-20 01:44:15', '2011-07-10 15:11:11', '8eefe6e871cebe24248cc47d4b23d152goteo@doukeshi.org');
INSERT INTO `user` VALUES('gpedranti', 'Gabriela Pedranti', 'Barcelona', 'info@gabrielapedranti.com', '1d2a5c9df4290c12a5f5962e3d9fda38c85c072d', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('hcastro', 'Helder Castro', 'Bilbao', 'helder_r_castro@hotmail.com', 'e10a36f30ce2a39a2e1998f9317ae06362a634c5', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('ibartolome', 'IÃ±aki BartolomÃ©', 'Bilbao', 'ibartolome@ideable.net', '6940fc52c08a9e4695cd2c929cca05eefdd69c99', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('ibelloso', 'Isabel Belloso Bueno', 'CÃ¡ceres', 'ibellosobueso@yahoo.es', '00aff3cc9ed53bd857a28d4f0e209e5fac70ca5d', 'FundaciÃ³n CÃ¡ceres Capital', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('infoq', 'info-q', 'Bilbao', 'info@info-q.com', 'dddbd151b3e56407b313757a21dbf63b8559dc30', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('iromero', 'Imma Romero', 'Barcelona', 'ima_gina7@hotmail.com', 'ff5e6b74613597bfeaa59b91f9accc1d238cfefb', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('itxaso', 'Ãšbiqa, tecnologÃ­a, ideas y comunicaciÃ³n', 'Bilbao', 'itxaso@ubiqa.com', '82d3d0eaff77053c18a71ff4725dd9ffc712cce3', '', '', 1, 0, '', 'ubiqarama', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-05 13:20:46', '');
INSERT INTO `user` VALUES('jclindo', 'Juan Carlos Lindo Sanguino', 'CÃ¡ceres', 'juancarlos@identic.es', '1629be88c941712f3b027b1bccdae4dd338ea852', 'Consorcio Identic', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('jcosta', 'Joaquin Costa', 'Bilbao', 'jcosta@eohonline.es', 'ccf5b869a0e1fd1b8f34c095cd7fe210bab8036a', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('jfernandez', 'JesÃºs FenÃ¡ndez Perianes', 'CÃ¡ceres', 'interinofernandez@hotmail.com', 'e36b93e47ad4a17dbe34fa2a8a53e7d57a44670d', 'COlectivo PEriferias ', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('jlespina', 'JosÃ© Luis Espina', 'Barcelona', 'espinajl@gmail.com', '7bf9458b0c0549d310203f27c34f441ac074eb5a', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('jmatadero', 'Javi de Matadero', 'Madrid', 'javi@mataderomadrid.org', 'cb218e4b9fbbb89ec5804ec32ca1067e3679b311', 'Matadero', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('jmorer', 'Julia  Morer ', 'Madrid', 'julia.morer@gmail.com', '7b63000191f052ebb77324fe9940af29e9ad1765', 'EdiciÃ³n y DiseÃ±o de Proyectos Culturales', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:10', '');
INSERT INTO `user` VALUES('jmorquecho', 'Jonatan Morquecho', 'Bilbao', 'jmorquecho@gmail.com', 'bcccd101cfae58fb3f4937f1cc1dd5c5f71db5eb', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('jnora', 'JuliÃ¡n Nora', 'Madrid', 'nora_julian@hotmail.com', '2eda3d6278dc18275918ea9ef18c0bdfe1c22a47', 'Ing. Simulacion ', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('jromero', 'Jessica Romero', 'Madrid', 'jessicaromero@gmail.com', 'e3d79a6764ec0796ff75f8b5c9184cc7bac0d37a', 'Periodista y productora cultural', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('kventura', 'Kenia Ventura', 'Barcelona', 'dinkha@hotmail.com', '4e79b02993b50bddf2709ee5e764dfddc1c7661b', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('lamorrortu', 'Lander Amorrortu', 'Bilbao', 'lander.amorrortu@agla4D.com', '51dbe34a01e87ee7ec986c07b7ad315893f95a56', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('lcarretero', 'LucÃ­a Carretero', 'Barcelona', 'foto@luciacarretero.com', '29afdee54be1bbdf89242d5eb3cacf0c11b680f3', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('lemontero', 'Luis Ernesto Montero', 'CÃ¡ceres', ' lernestomc@Yahoo.es', 'b0e4f357e65a33966d2089b1d899f4245f05ad25', 'Artista', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('lfernandez', 'Laura FernÃ¡ndez', 'Madrid', 'laura@medialab-prado.es', '6823d091412f0850015e9a0e4983b48063e7a813', 'Responsable de programa cultural en Medialab-Prado', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('lstalling', 'Lars Stalling', 'Barcelona', 'larsst@gmail.com ', 'dbe4f8a3757235145f8ed0fde23e433fe5650872', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('maaban', 'Manuel Ãngel AbÃ¡n', 'Madrid', 'manuel.aban@gmail.com', 'd068342a85c48d2f95ea7661b2b0a5e77c56dc5c', 'Codirector de una ONG ', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('mamanovell', 'M. Ãngel Manovell', 'Bilbao', 'info@dinamik-ideas.com', '6a151226b509f2fa7c3207ee17ee21f41bec0198', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('mcidriain', 'Monika Cidriain', 'San SebastiÃ¡n', 'cidriain@yahoo.es', '934a3519f310edf49a5dfb10a752a72aa8e0600e', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('mduran', 'Magdalena Duran', 'Barcelona', 'magdaduran@yahoo.es', '5d5d2e7dfde93f13cc12abdacc10d7f2083f1c39', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('mgarcia', 'Miriam GarcÃ­a Sanz', 'Madrid', 'miriamgsanz@gmail.com', 'b982ca393b19498409bc749abefc585e14dc149f', 'Gestora cultural', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('mgoikolea', 'Marta Goikolea', 'Bilbao', 'mgoikolea@gmail.com', 'e4f15436b31af59798f0e0fe6eafa544e8f4f575', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('mgoni', 'Marta GoÃ±i', 'Bilbao', 'caoroneltapi@yahoo.es', 'c3bf14f58a8005742e7ef31c0549a6f8fce3f154', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-07-05 01:55:30', '2011-07-05 01:55:30', '');
INSERT INTO `user` VALUES('mkekejian', 'Maral Kekejian ', 'Madrid', 'mkekejih@cajamadrid.es', '6bab1571a22fa60c8022a6dfcf397f97b0743279', 'Artes EscÃ©nicas LCE', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('mmendez', 'Miguel MÃ©ndez PÃ©rez', 'CÃ¡ceres', 'elmiguemende@gmail.com ', '127d53ca624ad91c26a7ffa17aff20bdb644e235', 'TÃ©cnico AUPEX+gestor cultural+animador 2.0', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('mmikirdistan', 'Maral Mikirdistan', 'Barcelona', 'idensitat@idensitat.org', 'c63a6cb1c60e71b3a9f0d90a02ee392ff1a779ce', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('mpalma', 'Marcela Palma Barrera', 'CÃ¡ceres', 'marcela.palma@fundacionciudadania.es', 'c6f3a28e89b2b5f6e6f191eb533676f6cea595bf', 'FundaciÃ³n CiudadanÃ­a', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('mpedro', 'Matxalen de Pedro', 'Bilbao', 'matxalendplarrea@hotmail.com', '2b83df476cffcf3f7a1085cba5cc54c3d2a136b8', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('mpedroche', 'Mercedes Pedroche', 'Madrid', 'info@mercedespedroche.com', '570d6dc480ffbe1388e1bedce96e5a0e57e502b8', 'CoreÃ³grafa, bailarina', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('mramirez', 'Miguel RamÃ­rez', 'Barcelona', 'miquel.ramirez@gmail.com', 'f223dbfb37ae5507cdb7ced8d338e05a686bf25a', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('mraposo', 'mikelraposo', 'Bilbao', 'mikelraposo@gmail.com', 'ac65d55fbb459e454d3e4c623f3d85b6ed111484', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('msoms', 'Miriam Soms Trillo', 'CÃ¡ceres', 'msoms@lacajanegra.net', 'bae498009ce55c4bed9b1111748a53e97ad5a8ae', 'La Caja Negra', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('nescala', 'Nella Escala', 'Barcelona', 'nella.escala@gmail.com', '4ee7c6e03aaa371e7f3ee8dcaedd0d1f23a374da', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('olivier', 'Olivier', 'Palma de Mallorca', 'olivierschulbaum@platoniq.net', 'f8c021907c74267ee80964d52ae181577fd095f2', 'muchas cosas bonitas', 'crowdfunding, copyleft, educaciÃ³n, innovaciÃ³n_social', 1, 22, 'un monton de NRJ', 'platoniq', 'http://www.facebook.com/olivier.schulbaum', 'http://identi.ca/platoniq', NULL, 'http://www.linkedin.com/profile/view?id=98955103&locale=es_ES&trk=tab_pro', 0, '2011-06-01 11:19:29', '2011-07-04 16:00:03', 'bde8aad344ca0a7b6558a7b35b229f90');
INSERT INTO `user` VALUES('pepe', 'Jose', 'aqui , no lo ves?', 'pepe_1303727124_per@gmail.com', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Poca cosa hay que contar', 'keyword1, keyword2, keyword3, keyword4, keyword5, keyword6', 1, 72, 'Ideas geniales!', 'twitter', 'user_facebook', 'identica', NULL, 'linkedin', 0, '2011-07-04 16:45:21', '2011-07-08 21:41:52', '');
INSERT INTO `user` VALUES('pereztoril', 'Javier PÃ©rez-Toril GalÃ¡n', 'CÃ¡ceres', 'pereztoril@gmail.com', '4fca9db1555c0fcec59343a2ae8c60055510cdae', 'Empresa Jptsolutions', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('pgonzalo', 'Pilar Gonzalo', 'Madrid', 'pilar.gonzalo@fulbrightmail.org', '2675dc74abf73a42520c10bcfad2cdca66a6f1c0', 'Museo Reina SofÃ­a', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:08', '');
INSERT INTO `user` VALUES('rcasado', 'Raul Casado', 'Barcelona', 'raul.casadogonzalez@gmail.com', '060d5b91ab484ba5bbd937c3cf23bd864ccba3d0', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('rfernandez', 'Rosa Fernandez', 'Bilbao', 'info@colaborabora.org', '2a35b80f14ace4402da74188d325ecacb1f19496', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('root', 'Super administrador', '', 'julian_1302552287_per@gmail.com', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', '', '', 1, NULL, '', '', '', '', NULL, '', 2, '2011-07-04 16:46:11', '2011-07-07 21:52:09', '61aa85ea9169c68babfa5b8bdb44097bjulian_1302552287_per@gmail.com');
INSERT INTO `user` VALUES('rparramon', 'Ramon Parramon', 'Barcelona', 'rparramon@acvic.org', '98de9c0d4367d83c6b18bac6e0010972261aa094', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('rsalas', 'Roberto Salas', 'Madrid', 'robers_alas@yahoo.es', '86a131eb71ceceddb26bc889895cf24ef96602e1', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:08', '');
INSERT INTO `user` VALUES('rsteckelbach', 'Roswitha Steckelbach', 'Bilbao', 'roswira@yahoo.es', '18e4cfb2ef66c87404fee367d4126a72c653db23', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('rtorres', 'Ricardo Torres', 'Bilbao', 'ricardotorres2@telefonica.net', '55f237586f9bef5fa246fafe308ebdecf7aac227', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('sgrueso', 'StÃ©phane Grueso', 'Madrid', 'stephanegrueso@gmail.com', '2364b268a16831f859c5d17b7eda329fd39700a9', 'Cineasta documental', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('smeschede', 'SÃ¶ren Meschede', 'Madrid', 'soren@hablarenarte.com', '0b63ad3f4d744a320cf437f1ffecb4c24a74f74f', 'Gestor Cultural', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:08', '');
INSERT INTO `user` VALUES('snogales', 'Silverio Nogales Pajuelo', 'CÃ¡ceres', 'sinogales@gmail.com', 'fa72c318a30d73163c9f63a240577ed0fb9afa7e', 'Gerente Ciudad de la Salud', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('soritxori', 'Soraia', 'Bilbao', 'soritxori@hotmail.com', '75042acd59a7e20f0c2b6213a5f86aaccce992f3', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');
INSERT INTO `user` VALUES('stena', 'Sara Tena Medina', 'CÃ¡ceres', 'sara.tena@aupex.org', 'e0be7b9f868042ae4e89136b9a84996597ba5b44', 'AsociaciÃ³n de Universidades Populares de Extremadura', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('tbadia', 'Tere Badia', 'Barcelona', 'tbadtod@gmail.com', '770b0c2422f238ff6662d2411900c6e57f130e22', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('tguido', 'TomÃ¡s Guido', 'Madrid', 'tguido@transit.es', '325dce990aec945b7d843814911ae7811670dbee', 'Coordino la oficina en Madrid la empresa TrÃ nsit Projectes. www.transit.es', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:11', '');
INSERT INTO `user` VALUES('tintangibles', 'Taller d''intangibles', 'Barcelona', 'dvd@enlloc.org', '43d79ce4a41095a5adf6d315a989ee8a349c168b', '', '', 1, 0, '', 'HKpWiki', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-05 15:45:39', '');
INSERT INTO `user` VALUES('todojunto', 'Todojunto', 'Barcelona', 'hola@todojunto.net', '9da21b8b0a1ef24359212199b4335534a805acb7', '', '', 1, 0, '', '', '', '', NULL, '', 0, '0000-00-00 00:00:00', '2011-07-04 18:49:38', '');
INSERT INTO `user` VALUES('vsantiago', 'Victor Santiago', 'CÃ¡ceres', 'vstabares@gmail.com', '62ff5323c5bb06d521efb2f1f185389728d715a2', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 16:36:40', '');
INSERT INTO `user` VALUES('vtorre', 'VÃ­ctor Torre', 'Madrid', 'victortorrevaquero@yahoo.es', '09723c5bf0daf4c5c5b127ba269edc841327238c', 'Coordinador Teatro Sol y Tierra', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 21:52:09', '');
INSERT INTO `user` VALUES('yriquelme', 'Yolanda Riquelme ', 'Madrid', 'yolandariquel@hotmail.com', 'add40e3392132f436c194831fa81de1dd3b62962', '', '', 1, 0, '', '', '', '', NULL, '', 1, '2011-05-04 00:00:00', '2011-07-07 19:27:12', '');
INSERT INTO `user` VALUES('zaramari', 'Zaramari (Gorka, Maria)', 'Bilbao', 'info@zaramari.com', 'd21582038b6f6288e2f089b21c93e550dc038bd1', '', '', 1, 0, '', '', '', '', NULL, '', 0, '2011-05-04 00:00:00', '2011-07-04 18:50:34', '');

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

INSERT INTO `user_image` VALUES('diegobus', 8);
INSERT INTO `user_image` VALUES('esenabre', 27);
INSERT INTO `user_image` VALUES('goteo', 7);
INSERT INTO `user_image` VALUES('olivier', 22);
INSERT INTO `user_image` VALUES('pepe', 4);
INSERT INTO `user_image` VALUES('pepe', 68);
INSERT INTO `user_image` VALUES('pepe', 72);
INSERT INTO `user_image` VALUES('root', 30);

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

INSERT INTO `user_interest` VALUES('esenabre', 7);
INSERT INTO `user_interest` VALUES('goteo', 2);
INSERT INTO `user_interest` VALUES('goteo', 7);
INSERT INTO `user_interest` VALUES('olivier', 2);
INSERT INTO `user_interest` VALUES('olivier', 6);
INSERT INTO `user_interest` VALUES('pepe', 2);
INSERT INTO `user_interest` VALUES('pepe', 6);

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

INSERT INTO `user_personal` VALUES('acomunes', '', NULL, '', NULL, '', '', '', 'Madrid', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('dcuartielles', '', NULL, '', NULL, '', '', '', 'Malmï¿½', 'Suecia');
INSERT INTO `user_personal` VALUES('diegobus', 'diego', 'bustamante', 'x8562415k', 'diego@mail.com', '658125454', 'c/ calle 98, 1Âº 2Âº', '08000', 'Barcelona', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('domenico', '', NULL, '', NULL, '', '', '', 'Madrid', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('ebaraonap', '', NULL, '', NULL, '', '', '', 'Barcelona', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('efoglia', '', NULL, '', NULL, '', '', '', 'Barcelona', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('esenabre', 'Enric Senabre Hidalgo', NULL, '46649545W', NULL, '932215515', 'Moscou 16, 1Âº 1Âª', '08005', 'Barcelona', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('geraldo', '', NULL, '', NULL, '', '', '', 'Barcelona', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('goteo', 'Goteo Platoniq', NULL, '', NULL, '', '', '', 'mhkjghsdkj fkj sadkjf', 'EspaÃƒÂ±a');
INSERT INTO `user_personal` VALUES('itxaso', '', NULL, '', NULL, '', '', '', 'Bilbao', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('olivier', 'Olivier Schulbaum', NULL, '', NULL, '667031530', '', '', '', '');
INSERT INTO `user_personal` VALUES('pepe', 'eric cantona', NULL, '7777777777', NULL, '65552584', 'manchester 2004', '333333', 'liverpool', 'england');
INSERT INTO `user_personal` VALUES('root', 'Super administrador', NULL, '', NULL, '', '', '', '', 'EspaÃ±a');
INSERT INTO `user_personal` VALUES('tintangibles', '', NULL, '', NULL, '', '', '', 'Barcelona', 'EspaÃ±a');

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
INSERT INTO `user_role` VALUES('goteo', 'user', '*');
INSERT INTO `user_role` VALUES('gpedranti', 'user', '*');
INSERT INTO `user_role` VALUES('hcastro', 'user', '*');
INSERT INTO `user_role` VALUES('ibartolome', 'user', '*');
INSERT INTO `user_role` VALUES('ibelloso', 'user', '*');
INSERT INTO `user_role` VALUES('infoq', 'user', '*');
INSERT INTO `user_role` VALUES('iromero', 'user', '*');
INSERT INTO `user_role` VALUES('itxaso', 'user', '*');
INSERT INTO `user_role` VALUES('jclindo', 'user', '*');
INSERT INTO `user_role` VALUES('jcosta', 'user', '*');
INSERT INTO `user_role` VALUES('jfernandez', 'user', '*');
INSERT INTO `user_role` VALUES('jlespina', 'user', '*');
INSERT INTO `user_role` VALUES('jmatadero', 'user', '*');
INSERT INTO `user_role` VALUES('jmorer', 'user', '*');
INSERT INTO `user_role` VALUES('jmorquecho', 'user', '*');
INSERT INTO `user_role` VALUES('jnora', 'user', '*');
INSERT INTO `user_role` VALUES('jromero', 'user', '*');
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
INSERT INTO `user_role` VALUES('pepe', 'user', '*');
INSERT INTO `user_role` VALUES('pereztoril', 'user', '*');
INSERT INTO `user_role` VALUES('pgonzalo', 'user', '*');
INSERT INTO `user_role` VALUES('rcasado', 'user', '*');
INSERT INTO `user_role` VALUES('rfernandez', 'user', '*');
INSERT INTO `user_role` VALUES('root', 'admin', '*');
INSERT INTO `user_role` VALUES('root', 'checker', '*');
INSERT INTO `user_role` VALUES('root', 'root', '*');
INSERT INTO `user_role` VALUES('root', 'superadmin', '*');
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Webs de los usuarios' AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `user_web`
--

INSERT INTO `user_web` VALUES(1, 'goteo', 'http://');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `__interest`
--

DROP TABLE IF EXISTS `__interest`;
CREATE TABLE `__interest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `description` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios' AUTO_INCREMENT=8 ;

--
-- Volcar la base de datos para la tabla `__interest`
--

INSERT INTO `__interest` VALUES(1, 'EducaciÃ³n', 'EducaciÃ³n', 5);
INSERT INTO `__interest` VALUES(2, 'EconomÃ­a solidaria', 'EconomÃ­a solidaria', 7);
INSERT INTO `__interest` VALUES(3, 'Empresa abierta', 'Empresa abierta', 1);
INSERT INTO `__interest` VALUES(4, 'FormaciÃ³n tÃ©cnica', 'FormaciÃ³n tÃ©cnica', 4);
INSERT INTO `__interest` VALUES(5, 'Desarrollo', 'Desarrollo', 6);
INSERT INTO `__interest` VALUES(6, 'Software', 'Software', 2);
INSERT INTO `__interest` VALUES(7, 'Hardware', 'Hardware', 3);
