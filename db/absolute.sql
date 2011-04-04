-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 04-04-2011 a las 15:40:36
-- Versión del servidor: 5.1.36
-- Versión de PHP: 5.3.0

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
CREATE TABLE IF NOT EXISTS `acl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `resource` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `privilege` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_FKIndex1` (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `acl`
--

INSERT INTO `acl` (`id`, `role_id`, `resource`, `action`, `privilege`) VALUES
(1, 3, 'User', 'edit', 1),
(2, 1, 'Project', '*', 1),
(3, 2, 'User', 'delete', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `charge`
--

DROP TABLE IF EXISTS `charge`;
CREATE TABLE IF NOT EXISTS `charge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invest` int(11) NOT NULL,
  `entity` varchar(50) NOT NULL,
  `code` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `result` varchar(8) NOT NULL COMMENT 'FAIL / SUCCESS',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Transacciones en banco o paypal' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `charge`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cost`
--

DROP TABLE IF EXISTS `cost`;
CREATE TABLE IF NOT EXISTS `cost` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `cost` varchar(256) NOT NULL,
  `description` tinytext,
  `type` varchar(50) NOT NULL DEFAULT 'task',
  `amount` int(5) DEFAULT '0',
  `required` tinyint(1) DEFAULT '0',
  `from` date DEFAULT NULL,
  `until` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Desglose de costes de proyectos' AUTO_INCREMENT=14 ;

--
-- Volcar la base de datos para la tabla `cost`
--

INSERT INTO `cost` (`id`, `project`, `cost`, `description`, `type`, `amount`, `required`, `from`, `until`) VALUES
(7, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'Para comprar madera', 'sdfg sdfg sdfg ', 'task', 1000, 1, '2011-03-31', '2011-03-31'),
(8, 'church-project-eko-one', 'Para comprar madera', 'sadf asdf asdf ', 'structure', 2000, 1, '2011-04-02', '2011-04-02'),
(9, 'church-project-eko-one', 'MÃ¡s madera!', 'MÃ¡s! MÃ¡s! MÃ¡s! MÃ¡s! MÃ¡s! MÃ¡s! MÃ¡s! MÃ¡s! ', 'structure', 1000, NULL, '2011-04-02', '2011-04-02'),
(10, 'church-project-eko-one', 'Para comprar clavos', 'Se comprarÃ¡n clavos del 5, del 10 y del 15', 'structure', 500, 1, '2011-04-02', '2011-04-02'),
(11, 'church-project-eko-one', 'Serrucho', 'Serrucho gordo', 'equip', 50, NULL, '2011-04-01', '2011-04-01'),
(13, 'church-project-eko-one', 'Y cuerda', 'Tambien nos hace falta muuuucha cuerda', 'structure', 1000, 1, '2011-04-03', '2011-04-03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest`
--

DROP TABLE IF EXISTS `invest`;
CREATE TABLE IF NOT EXISTS `invest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `amount` int(6) NOT NULL,
  `status` int(1) NOT NULL COMMENT '0 pendiente, 1 cobrado, 2 devuelto',
  `anonymous` tinyint(1) DEFAULT NULL,
  `resign` tinyint(1) DEFAULT NULL,
  `invested` date DEFAULT NULL,
  `charged` date DEFAULT NULL,
  `returned` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `invest`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lang`
--

DROP TABLE IF EXISTS `lang`;
CREATE TABLE IF NOT EXISTS `lang` (
  `id` varchar(2) NOT NULL COMMENT 'Código ISO-639',
  `name` varchar(20) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Idiomas';

--
-- Volcar la base de datos para la tabla `lang`
--

INSERT INTO `lang` (`id`, `name`, `active`) VALUES
('ca', 'Català', 0),
('de', 'Deutsch', 0),
('en', 'English', 0),
('es', 'Español', 1),
('eu', 'Euskara', 0),
('fr', 'Français', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `node`
--

DROP TABLE IF EXISTS `node`;
CREATE TABLE IF NOT EXISTS `node` (
  `id` varchar(50) NOT NULL,
  `name` varchar(256) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodos';

--
-- Volcar la base de datos para la tabla `node`
--

INSERT INTO `node` (`id`, `name`, `active`) VALUES
('goteo', 'Master node', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `id` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `status` int(1) NOT NULL,
  `progress` int(3) NOT NULL,
  `owner` varchar(50) NOT NULL COMMENT 'usuario que lo ha creado',
  `node` varchar(50) NOT NULL COMMENT 'nodo en el que se ha creado',
  `amount` int(6) DEFAULT NULL COMMENT 'acumulado actualmente',
  `created` date DEFAULT NULL,
  `updated` date DEFAULT NULL,
  `published` date DEFAULT NULL,
  `success` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  `contract_name` varchar(255) DEFAULT NULL,
  `contract_surname` varchar(255) DEFAULT NULL,
  `contract_nif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `contract_email` varchar(256) DEFAULT NULL,
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
  `resource` text,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos de la plataforma';

--
-- Volcar la base de datos para la tabla `project`
--

INSERT INTO `project` (`id`, `name`, `status`, `progress`, `owner`, `node`, `amount`, `created`, `updated`, `published`, `success`, `closed`, `contract_name`, `contract_surname`, `contract_nif`, `contract_email`, `phone`, `address`, `zipcode`, `location`, `country`, `image`, `description`, `motivation`, `about`, `goal`, `related`, `category`, `keywords`, `media`, `currently`, `project_location`, `resource`) VALUES
('28c0caa840fc9c642160b1e2774667ff', 'Mi proyecto 1', 1, 0, 'pepe', 'goteo', 0, '2011-04-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('6bb61e3b7bce0931da574d19d1d82c88', 'Mi proyecto 1', 1, 0, 'root', 'goteo', 0, '2011-03-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('7b0aa2ffa4d04499d7c743fde7acdceb', 'Como lo borrooooo???', 1, 4, 'pepa', 'goteo', 0, '2011-04-02', '2011-04-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'project.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
('984990664ca1a1a98522b2640b0fc535', 'Mi proyecto 2', 1, 1, 'root', 'goteo', 0, '2011-03-24', '2011-03-24', NULL, NULL, NULL, 'JuliÃ¡n', 'CÃ¡naves Bover', '43108914Z', 'julian.canaves@gmail.com', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('church-project-eko-one', 'Church project eko one', 2, 89, 'pepa', 'goteo', 0, '2011-04-02', '2011-04-03', NULL, NULL, NULL, 'Josefa', 'Perez Diez', 'X1234567L', 'example@example.com', '666999666', 'C/ De las piedras,1', '08023', 'Los cantos, SEVILLA', 'EspaÃ±a', 'project.jpg', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id laborum.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', NULL, 'asdf, asdf,asdf ,sdaf, asdf', 'http://www.youtube.com/watch?v=6TJbpCC7iPg', 2, 'Online', 'Tengo un martillo y una sierra'),
('e4ae82c6a3497c04d2338fe63961c92c', 'Mi proyecto 3', 1, 0, 'root', 'goteo', 0, '2011-03-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate great project of the wolrd united nations congregated to the end of time and space ship', 1, 0, 'root', 'goteo', NULL, NULL, '2011-04-01', NULL, NULL, NULL, 'JuliÃ¡n', 'CÃ¡naves Bover', '43108914Z', 'julian.canaves@gmail.com', '649085539', 'C/ Patata, 1', '07014', 'Palma de Mallorca', 'EspaÃ±a', 'project.jpg', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n\r\n', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', 'stuff', NULL, 'fasdfasdfasdfasdfasddf', 4, 'Internet', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project_category`
--

DROP TABLE IF EXISTS `project_category`;
CREATE TABLE IF NOT EXISTS `project_category` (
  `project` varchar(50) NOT NULL,
  `category` int(12) NOT NULL,
  UNIQUE KEY `project_category` (`project`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';

--
-- Volcar la base de datos para la tabla `project_category`
--

INSERT INTO `project_category` (`project`, `category`) VALUES
('church-project-eko-one', 1),
('church-project-eko-one', 2),
('church-project-eko-one', 3),
('church-project-eko-one', 4),
('church-project-eko-one', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purpose`
--

DROP TABLE IF EXISTS `purpose`;
CREATE TABLE IF NOT EXISTS `purpose` (
  `text` varchar(50) NOT NULL,
  `purpose` tinytext NOT NULL,
  PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Explicación del propósito de los textos';

--
-- Volcar la base de datos para la tabla `purpose`
--

INSERT INTO `purpose` (`text`, `purpose`) VALUES
('error sql guardar proyecto', 'La sentencia UPDATE para grabar los datos de un proyecto en la base de datos falla.'),
('explain project progress', 'Texto bajo el título Estado global de la información'),
('guide project contract information', 'Texto guía en el paso DATOS PERSONALES del formulario de proyecto'),
('guide project costs', 'Texto guía en el paso COSTES del formulario de proyecto'),
('guide project description', 'Texto guía en el paso DESCRIPCIÓN del formulario de proyecto'),
('guide project overview', 'Texto guía en el paso PREVISUALIZACIÓN del formulario de proyecto'),
('guide project rewards', 'Texto guía en el paso RETORNO del formulario de proyecto'),
('guide project success minprogress', 'Texto guide project success minprogress'),
('guide project success noerrors', 'Texto guide project success noerrors'),
('guide project success okfinish', 'Texto guide project success okfinish'),
('guide project support', 'Texto guía en el paso COLABORACIONES del formulario de proyecto'),
('guide project user information', 'Texto guide project user information'),
('guide user data', 'Texto guía en la edición de datos sensibles del usuario'),
('guide user information', 'Texto guía en el paso PERFIL del formulario de proyecto'),
('guide user register', 'Texto guía en el registro de usuario'),
('mandatory project field about', 'Texto mandatory project field about'),
('mandatory project field address', 'Texto mandatory project field address'),
('mandatory project field category', 'La categoría del proyecto es obligatoria'),
('mandatory project field contract email', 'El email del responsable del proyecto es obligatorio'),
('mandatory project field contract name', 'El nombre del responsable del proyecto es obligatorio'),
('mandatory project field contract nif', 'El nif del responsable del proyecto es obligatorio'),
('mandatory project field contract surname', 'El apellido del responsable del proyecto es obligatorio'),
('mandatory project field country', 'Texto mandatory project field country'),
('mandatory project field description', 'La descripción del proyecto es obligatorio'),
('mandatory project field goal', 'Texto mandatory project field goal'),
('mandatory project field image', 'Texto mandatory project field image'),
('mandatory project field location', 'La localización del proyecto es obligatoria'),
('mandatory project field motivation', 'Texto mandatory project field motivation'),
('mandatory project field name', 'El nombre del proyecto es obligatorio'),
('mandatory project field phone', 'Texto mandatory project field phone'),
('mandatory project field related', 'Texto mandatory project field related'),
('mandatory project field residence', 'El lugar de residencia del responsable del proyecto es obligatorio'),
('mandatory project field zipcode', 'Texto mandatory project field zipcode'),
('regular mandatory', 'Texto genérico para indicar campo obligatorio'),
('step 1', 'Paso 1, información del usuario'),
('step 2', 'Paso 2, información del responsable'),
('step 3', 'Paso 3, descripción del proyecto'),
('step 4', 'Paso 4, desglose de costes'),
('step 5', 'Paso 5, retornos'),
('step 6', 'Paso 6, colaboraciones'),
('step 7', 'paso 7, previsualización'),
('tooltip project about', 'Consejo para rellenar el campo qué es'),
('tooltip project address', 'Consejo para rellenar el address del responsable del proyecto'),
('tooltip project category', 'Consejo para rellenar la categoría del proyecto'),
('tooltip project contract_email', 'Consejo para rellenar el email del responsable del proyecto'),
('tooltip project contract_name', 'Consejo para rellenar el nombre del responsable del proyecto'),
('tooltip project contract_nif', 'Consejo para rellenar el nif del responsable del proyecto'),
('tooltip project contract_surname', 'Consejo para rellenar el apellido del responsable del proyecto'),
('tooltip project cost', 'Consejo para editar desgloses existentes'),
('tooltip project country', 'Consejo para rellenar el país del responsable del proyecto'),
('tooltip project currently', 'Consejo para rellenar el estado de desarrollo del proyecto'),
('tooltip project description', 'Consejo para rellenar la descripción del proyecto'),
('tooltip project goal', 'Consejo para rellenar el campo objetivos'),
('tooltip project image', 'Consejo para rellenar la imagen del proyecto'),
('tooltip project individual_reward', 'Consejo para editar retornos individuales existentes'),
('tooltip project keywords', 'Consejo para rellenar las palabras clave del proyecto'),
('tooltip project location', 'Consejo para rellenar el lugar de residencia del responsable del proyecto'),
('tooltip project media', 'Consejo para rellenar el media del proyecto'),
('tooltip project motivation', 'Consejo para rellenar el campo motivación'),
('tooltip project name', 'Consejo para rellenar el nombre del proyecto'),
('tooltip project ncost', 'Consejo para rellenar un nuevo desglose de costes'),
('tooltip project nindividual_reward', 'Consejo para rellenar un nuevo retorno individual'),
('tooltip project nsocial_reward', 'Consejo para rellenar un nuevo retorno colectivo'),
('tooltip project nsupport', 'Consejo para rellenar una nueva colaboración'),
('tooltip project phone', 'Consejo para rellenar el teléfono del responsable del proyecto'),
('tooltip project project_location', 'Consejo para rellenar la localización del proyecto'),
('tooltip project related', 'Consejo para rellenar el campo experiencia relacionada y equipo'),
('tooltip project resource', 'Consejo para rellenar el campo Cuenta con otros recursos?'),
('tooltip project social_reward', 'Consejo para editar retornos colectivos existentes'),
('tooltip project support', 'Consejo para editar colaboraciones existentes'),
('tooltip project zipcode', 'Consejo para rellenar el zipcode del responsable del proyecto'),
('tooltip user about', 'Consejo para rellenar el cuéntanos algo sobre tí'),
('tooltip user blog', 'Consejo para rellenar la web'),
('tooltip user contribution', 'Consejo para rellenar el qué podrías aportar en goteo'),
('tooltip user email', 'Consejo para rellenar el email de registro de usuario'),
('tooltip user facebook', 'Consejo para rellenar el facebook'),
('tooltip user image', 'Consejo para rellenar la imagen del usuario'),
('tooltip user interests', 'Consejo para rellenar tus intereses'),
('tooltip user keywords', 'Texto tooltip user keywords'),
('tooltip user linkedin', 'Consejo para rellenar el linkedin'),
('tooltip user name', 'Consejo para rellenar el nombre completo del usuario'),
('tooltip user twitter', 'Consejo para rellenar el twitter'),
('tooltip user user', 'Consejo para rellenar el nombre de usuario para login'),
('validate project field contract email', 'El email del responsable del proyecto debe ser correcto'),
('validate project value contract email', 'Texto validate project value contract email'),
('validate project value contract nif', 'El nif del responsable del proyecto debe ser correcto'),
('validate project value description', 'La descripción del proyecto debe se suficientemente extensa'),
('validate project value phone', 'El teléfono debe ser correcto'),
('validation project min costs', 'Mínimo de costes a desglosar en un proyecto'),
('validation project total cost', 'Texto validation project total cost'),
('validation project total costs', 'El coste óptimo no puede exceder demasiado al coste mínimo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reward`
--

DROP TABLE IF EXISTS `reward`;
CREATE TABLE IF NOT EXISTS `reward` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `reward` varchar(256) NOT NULL,
  `description` tinytext,
  `type` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `license` varchar(50) DEFAULT NULL,
  `amount` int(5) DEFAULT NULL,
  `units` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Retornos colectivos e individuales' AUTO_INCREMENT=22 ;

--
-- Volcar la base de datos para la tabla `reward`
--

INSERT INTO `reward` (`id`, `project`, `reward`, `description`, `type`, `icon`, `license`, `amount`, `units`) VALUES
(1, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'social', NULL, NULL, NULL, NULL),
(2, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'individual', NULL, NULL, 5, 1),
(3, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'social', NULL, NULL, NULL, NULL),
(4, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'individual', NULL, NULL, 12, 11),
(5, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'social', NULL, NULL, NULL, NULL),
(6, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'individual', NULL, NULL, 0, 0),
(7, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'social', NULL, NULL, NULL, NULL),
(8, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'individual', NULL, NULL, 0, 0),
(9, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'social', NULL, NULL, NULL, NULL),
(10, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'individual', NULL, NULL, 0, 0),
(11, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'social', NULL, NULL, NULL, NULL),
(12, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'individual', NULL, NULL, NULL, NULL),
(13, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'social', NULL, NULL, NULL, NULL),
(14, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', NULL, 'individual', NULL, NULL, NULL, NULL),
(15, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'asdf a', NULL, 'social', NULL, NULL, NULL, NULL),
(16, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'asdf a', NULL, 'social', NULL, NULL, NULL, NULL),
(17, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'asdf a', NULL, 'social', NULL, NULL, NULL, NULL),
(18, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'sdf asdf as', NULL, 'social', NULL, NULL, NULL, NULL),
(20, 'church-project-eko-one', 'The masterplan', 'Ofrezco a la gente los planos para que se hagan una igual.', 'social', NULL, '1', NULL, NULL),
(21, 'church-project-eko-one', 'Chapas', 'Tezxto: "Lo he clavado"', 'individual', NULL, NULL, 5, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Admin de Nodo'),
(3, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `support`
--

DROP TABLE IF EXISTS `support`;
CREATE TABLE IF NOT EXISTS `support` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `support` tinytext NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Colaboraciones' AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `support`
--

INSERT INTO `support` (`id`, `project`, `support`, `description`, `type`) VALUES
(1, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'Contar arroz', 'Contar mil sacos de arroz', 'task'),
(2, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'Clavar clavos', 'en las maderas', 'task');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `text`
--

DROP TABLE IF EXISTS `text`;
CREATE TABLE IF NOT EXISTS `text` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'es',
  `text` text NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Textos multi-idioma';

--
-- Volcar la base de datos para la tabla `text`
--

INSERT INTO `text` (`id`, `lang`, `text`) VALUES
('error sql guardar proyecto', 'es', 'El proyecto no se ha grabado correctamete. Por favor, revise los datos.'),
('explain project progress', 'es', 'Texto bajo el título Estado global de la información'),
('guide project contract information', 'es', 'Texto guía en el paso DATOS PERSONALES del formulario de proyecto.'),
('guide project costs', 'es', 'Texto guía en el paso COSTES del formulario de proyecto.'),
('guide project description', 'es', 'Texto guía en el paso DESCRIPCIÓN del formulario de proyecto.'),
('guide project overview', 'es', 'Texto guía en el paso PREVISUALIZACIÓN del formulario de proyecto.'),
('guide project rewards', 'es', 'Texto guía en el paso RETORNO del formulario de proyecto.'),
('guide project success minprogress', 'es', 'guide project success minprogress'),
('guide project success noerrors', 'es', 'guide project success noerrors'),
('guide project success okfinish', 'es', 'guide project success okfinish'),
('guide project support', 'es', 'Texto guía en el paso COLABORACIONES del formulario de proyecto.'),
('guide project user information', 'es', 'guide project user information'),
('guide user data', 'es', 'Texto guía en la edición de campos sensibles.'),
('guide user information', 'es', 'Texto guía en el paso PERFIL del formulario de proyecto.'),
('guide user register', 'es', 'Texto guía en el registro de un nuevo usuario.'),
('mandatory project field about', 'es', 'mandatory project field about'),
('mandatory project field address', 'es', 'mandatory project field address'),
('mandatory project field category', 'es', 'Es obligatorio elegir una CATEGORIA para el proyecto, paso 3: Descripción.'),
('mandatory project field contract email', 'es', 'Es obligatorio poner el EMAIL del responsable del proyecto, paso 2: Datos personales.'),
('mandatory project field contract name', 'es', 'Es obligatorio poner el NOMBRE del responsable del proyecto, paso 2: Datos personales.'),
('mandatory project field contract nif', 'es', 'Es obligatorio poner el NIF del responsable del proyecto, paso 2: Datos personales.'),
('mandatory project field contract surname', 'es', 'Es obligatorio poner los APELLIDOS del responsable del proyecto, paso 2: Datos personales.'),
('mandatory project field country', 'es', 'mandatory project field country'),
('mandatory project field description', 'es', 'Es obligatorio poner una DESCRIPCIÓN al proyecto, paso 3: Descripción.'),
('mandatory project field goal', 'es', 'mandatory project field goal'),
('mandatory project field image', 'es', 'mandatory project field image'),
('mandatory project field location', 'es', 'Es obligatorio poner la LOCALIZACIÓN del proyecto, paso 3: Descripción.'),
('mandatory project field motivation', 'es', 'mandatory project field motivation'),
('mandatory project field name', 'es', 'Es obligatorio poner un NOMBRE al proyecto, paso 3: Descripción.'),
('mandatory project field phone', 'es', 'mandatory project field phone'),
('mandatory project field related', 'es', 'mandatory project field related'),
('mandatory project field residence', 'es', 'Es obligatorio poner el LUGAR DE RESIDENCIA del responsable del proyecto, paso 2: Datos personales.'),
('mandatory project field zipcode', 'es', 'mandatory project field zipcode'),
('regular mandatory', 'es', 'campo obligatorio!'),
('step 1', 'es', 'PERFIL'),
('step 2', 'es', 'DATOS PERSONALES'),
('step 3', 'es', 'DESCRIPCIÓN'),
('step 4', 'es', 'COSTES'),
('step 5', 'es', 'RETORNO'),
('step 6', 'es', 'COLABORACIONES'),
('step 7', 'es', 'PREVISUALIZACIÓN'),
('tooltip project about', 'es', 'Consejo para rellenar el campo qué es'),
('tooltip project address', 'es', 'Consejo para rellenar el address del responsable del proyecto'),
('tooltip project category', 'es', 'Consejo para rellenar la categoría del proyecto'),
('tooltip project contract_email', 'es', 'Consejo para rellenar el email del responsable del proyecto'),
('tooltip project contract_name', 'es', 'Consejo para rellenar el nombre del responsable del proyecto'),
('tooltip project contract_nif', 'es', 'Consejo para rellenar el nif del responsable del proyecto'),
('tooltip project contract_surname', 'es', 'Consejo para rellenar el apellido del responsable del proyecto'),
('tooltip project cost', 'es', 'Consejo para editar desgloses existentes'),
('tooltip project country', 'es', 'Consejo para rellenar el país del responsable del proyecto'),
('tooltip project currently', 'es', 'Consejo para rellenar el estado de desarrollo del proyecto'),
('tooltip project description', 'es', 'Consejo para rellenar la descripción del proyecto'),
('tooltip project goal', 'es', 'Consejo para rellenar el campo objetivos'),
('tooltip project image', 'es', 'Consejo para rellenar la imagen del proyecto'),
('tooltip project individual_reward', 'es', 'Consejo para editar retornos individuales existentes'),
('tooltip project keywords', 'es', 'Consejo para rellenar las palabras clave del proyecto'),
('tooltip project location', 'es', 'Consejo para rellenar el lugar de residencia del responsable del proyecto'),
('tooltip project media', 'es', 'Consejo para rellenar el media del proyecto'),
('tooltip project motivation', 'es', 'Consejo para rellenar el campo motivación'),
('tooltip project name', 'es', 'Consejo para rellenar el nombre del proyecto'),
('tooltip project ncost', 'es', 'Consejo para rellenar un nuevo desglose de costes'),
('tooltip project nindividual_reward', 'es', 'Consejo para rellenar un nuevo retorno individual'),
('tooltip project nsocial_reward', 'es', 'Consejo para rellenar un nuevo retorno colectivo'),
('tooltip project nsupport', 'es', 'Consejo para rellenar una nueva colaboración'),
('tooltip project phone', 'es', 'Consejo para rellenar el teléfono del responsable del proyecto'),
('tooltip project project_location', 'es', 'Consejo para rellenar la localización del proyecto'),
('tooltip project related', 'es', 'Consejo para rellenar el campo experiencia relacionada y equipo'),
('tooltip project resource', 'es', 'Consejo para rellenar el campo Cuenta con otros recursos?'),
('tooltip project social_reward', 'es', 'Consejo para editar retornos colectivos existentes'),
('tooltip project support', 'es', 'Consejo para editar colaboraciones existentes'),
('tooltip project zipcode', 'es', 'Consejo para rellenar el zipcode del responsable del proyecto'),
('tooltip user about', 'es', 'Consejo para rellenar el cuéntanos algo sobre tí'),
('tooltip user blog', 'es', 'Consejo para rellenar la web'),
('tooltip user contribution', 'es', 'Consejo para rellenar el qué podrías aportar en goteo'),
('tooltip user email', 'es', 'Consejo para rellenar el email de registro de usuario'),
('tooltip user facebook', 'es', 'Consejo para rellenar el facebook'),
('tooltip user image', 'es', 'Consejo para rellenar la imagen del usuario'),
('tooltip user interests', 'es', 'Consejo para rellenar tus intereses'),
('tooltip user keywords', 'es', 'tooltip user keywords'),
('tooltip user linkedin', 'es', 'Consejo para rellenar el linkedin'),
('tooltip user name', 'es', 'Consejo para rellenar el nombre completo del usuario'),
('tooltip user twitter', 'es', 'Consejo para rellenar el twitter'),
('tooltip user user', 'es', 'Consejo para rellenar el nombre de usuario para login'),
('validate project value contract email', 'es', 'El EMAIL no es correcto, paso 2: Datos personales.'),
('validate project value contract nif', 'es', 'El NIF no es correcto, paso 2: Datos personales.'),
('validate project value description', 'es', 'La DESCRIPCIÓN del proyecto es demasiado corta, paso 3: Descripción.'),
('validate project value phone', 'es', 'El TELÉFONO no es correcto, paso 2: Datos personales.'),
('validation project min costs', 'es', 'Debe desglosar en al menos DOS COSTES, paso 4 costes.'),
('validation project total cost', 'es', 'validation project total cost'),
('validation project total costs', 'es', 'El coste óptimo no puede superar en más de un 40% al coste mínimo. Revisar el DESGLOSE DE COSTES, paso 4 costes.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(40) NOT NULL,
  `about` text,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `avatar` tinytext,
  `contribution` text,
  `blog` varchar(256) DEFAULT NULL,
  `twitter` varchar(256) DEFAULT NULL,
  `facebook` varchar(256) DEFAULT NULL,
  `linkedin` varchar(256) DEFAULT NULL,
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Usuarios';

--
-- Volcar la base de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `role_id`, `name`, `email`, `password`, `about`, `active`, `avatar`, `contribution`, `blog`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('pepa', 3, 'Pepa PÃ©rez', 'josefa@doukeshi.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Yo soy JOSEFAAAA!!!!!', 1, 'avatar.jpg', 'mucho arte', 'lajosefaisthebest.com', '@josefa', 'feisbuc.com/josefaaaaa', 'ein?', NULL, '2011-03-19 00:00:00', '2011-04-03 01:43:01'),
('pepe', 2, 'pepe', 'asdf', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2011-03-19 00:00:00', '2011-04-03 01:42:57'),
('root', 1, 'Super administrador', 'goteo@doukeshi.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Super administrador de la plataforma Goteo.org', 1, NULL, 'Super administrador de la plataforma Goteo.org', NULL, NULL, NULL, NULL, NULL, '2011-03-16 00:00:00', '2011-04-03 01:42:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_interest`
--

DROP TABLE IF EXISTS `user_interest`;
CREATE TABLE IF NOT EXISTS `user_interest` (
  `user` varchar(50) NOT NULL,
  `interest` int(12) NOT NULL,
  UNIQUE KEY `user_interest` (`user`,`interest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios';

--
-- Volcar la base de datos para la tabla `user_interest`
--

INSERT INTO `user_interest` (`user`, `interest`) VALUES
('pepa', 1),
('pepa', 2),
('pepa', 3),
('pepa', 4),
('pepa', 5),
('root', 1),
('root', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `worthcracy`
--

DROP TABLE IF EXISTS `worthcracy`;
CREATE TABLE IF NOT EXISTS `worthcracy` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `amount` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Niveles de meritocracia' AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `worthcracy`
--

INSERT INTO `worthcracy` (`id`, `name`, `amount`) VALUES
(1, 'Fan', 25),
(2, 'Patrocinador', 50),
(3, 'Apostador', 100),
(4, 'Abonado', 500),
(5, 'Visionario', 1000);
