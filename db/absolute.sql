-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-04-2011 a las 22:22:52
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
  `node_id` int(10) unsigned DEFAULT NULL,
  `user_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role_id` int(10) unsigned DEFAULT NULL,
  `url` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `allow` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_FK` (`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `acl_FKIndex3` (`node_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `acl`
--

INSERT INTO `acl` (`id`, `node_id`, `user_id`, `role_id`, `url`, `allow`) VALUES
(1, NULL, NULL, NULL, '*', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `charge`
--

DROP TABLE IF EXISTS `charge`;
CREATE TABLE IF NOT EXISTS `charge` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invest` int(11) NOT NULL,
  `entity` varchar(50) NOT NULL,
  `code` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `result` varchar(8) NOT NULL COMMENT 'FAIL / SUCCESS',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
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
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `cost` varchar(256) NOT NULL,
  `description` tinytext,
  `type` varchar(50) NOT NULL DEFAULT 'task',
  `amount` int(5) DEFAULT '0',
  `required` tinyint(1) DEFAULT '0',
  `from` date DEFAULT NULL,
  `until` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
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
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `amount` int(6) NOT NULL,
  `status` int(1) NOT NULL COMMENT '0 pendiente, 1 cobrado, 2 devuelto',
  `anonymous` tinyint(1) DEFAULT NULL,
  `resign` tinyint(1) DEFAULT NULL,
  `invested` date DEFAULT NULL,
  `charged` date DEFAULT NULL,
  `returned` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos' AUTO_INCREMENT=9 ;

--
-- Volcar la base de datos para la tabla `invest`
--

INSERT INTO `invest` (`id`, `user`, `project`, `amount`, `status`, `anonymous`, `resign`, `invested`, `charged`, `returned`) VALUES
(3, 'root', 'church-project-eko-one', 20, 0, NULL, 1, '2011-04-10', NULL, NULL),
(4, 'root', 'church-project-eko-one', 12, 0, NULL, NULL, '2011-04-10', NULL, NULL),
(5, 'root', 'church-project-eko-one', 12, 0, NULL, 1, '2011-04-10', NULL, NULL),
(6, 'root', 'church-project-eko-one', 200, 0, 1, NULL, '2011-04-10', NULL, NULL),
(7, 'root', 'the-ultimate-grat-project-of-the-wolrd-united-nati', 1000, 0, NULL, 1, '2011-04-10', NULL, NULL),
(8, 'root', 'church-project-eko-one', 500, 0, 1, 1, '2011-04-12', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest_reward`
--

DROP TABLE IF EXISTS `invest_reward`;
CREATE TABLE IF NOT EXISTS `invest_reward` (
  `invest` bigint(20) unsigned NOT NULL,
  `reward` bigint(20) unsigned NOT NULL,
  `fulfilled` date DEFAULT NULL,
  UNIQUE KEY `invest` (`invest`,`reward`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';

--
-- Volcar la base de datos para la tabla `invest_reward`
--

INSERT INTO `invest_reward` (`invest`, `reward`, `fulfilled`) VALUES
(4, 21, NULL),
(6, 21, NULL);

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
('ca', 'CatalÃ ', 0),
('de', 'Deutsch', 0),
('en', 'English', 0),
('es', 'EspaÃ±ol', 1),
('eu', 'Euskara', 0),
('fr', 'FranÃ§ais', 0);

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
  `comment` text COMMENT 'Comentario para los admin',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos de la plataforma';

--
-- Volcar la base de datos para la tabla `project`
--

INSERT INTO `project` (`id`, `name`, `status`, `progress`, `owner`, `node`, `amount`, `created`, `updated`, `published`, `success`, `closed`, `contract_name`, `contract_surname`, `contract_nif`, `contract_email`, `phone`, `address`, `zipcode`, `location`, `country`, `image`, `description`, `motivation`, `about`, `goal`, `related`, `category`, `keywords`, `media`, `currently`, `project_location`, `resource`, `comment`) VALUES
('07df45930e6021231194818767546459', 'Mi proyecto 5', 1, 23, 'root', 'goteo', 0, '2011-04-07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('0d4a2138545ba28049a3899b08606839', 'Mi proyecto 5', 1, 14, 'pepo', 'goteo', 0, '2011-04-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('1253504990db1d25db41930cd47f3de1', 'Mi proyecto 4', 1, 14, 'pepo', 'goteo', 0, '2011-04-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('20eee46bbcf72e3c5a8576f95e331ad4', 'Mi proyecto 8', 1, 23, 'root', 'goteo', 0, '2011-04-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('231011b3507732de39dd6e51b7a2889c', 'Mi proyecto 7', 1, 23, 'root', 'goteo', 0, '2011-04-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('28c0caa840fc9c642160b1e2774667ff', 'Mi proyecto 1', 1, 14, 'pepe', 'goteo', 0, '2011-04-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('36a6596809c6a726131d13309999a2da', '', 1, 11, 'pepo', 'goteo', 0, '2011-04-10', '2011-04-10', NULL, NULL, NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('4e2b587837e9ac00d45a4e70ab4737f9', '', 1, 0, 'root', 'goteo', 0, '2011-04-12', '2011-04-12', NULL, NULL, NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5a58d7e7fff0ce33dd0212f33f978fc3', '', 1, 11, 'pepo', 'goteo', 0, '2011-04-10', '2011-04-10', NULL, NULL, NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('611c6d7201b9334f4d428c0bc9def585', '', 1, 0, 'root', 'goteo', 0, '2011-04-12', '2011-04-12', NULL, NULL, NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('6bb61e3b7bce0931da574d19d1d82c88', 'Mi proyecto 1', 1, 23, 'root', 'goteo', 0, '2011-03-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('7b0aa2ffa4d04499d7c743fde7acdceb', 'Como lo borrooooo???', 1, 34, 'pepa', 'goteo', 0, '2011-04-02', '2011-04-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'project.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
('96e23f7431365f3f9c21b4f26f72f624', '', 1, 11, 'pepo', 'goteo', 0, '2011-04-10', '2011-04-10', NULL, NULL, NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('984990664ca1a1a98522b2640b0fc535', 'Mi proyecto 2', 1, 34, 'root', 'goteo', 0, '2011-03-24', '2011-03-24', NULL, NULL, NULL, 'JuliÃ¡n', 'CÃ¡naves Bover', '43108914Z', 'julian.canaves@gmail.com', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('a565092b772c29abc1b92f999af2f2fb', '', 1, 0, 'root', 'goteo', 0, '2011-04-12', '2011-04-12', NULL, NULL, NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ab94bff3dc5990573919eebc3d7c7f33', 'Mi proyecto 6', 1, 23, 'root', 'goteo', 0, '2011-04-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('church-project-eko-one', 'Church project eko one', 3, 94, 'pepa', 'goteo', 0, '2011-04-02', '2011-04-03', '2011-04-09', NULL, NULL, 'Josefa', 'Perez Diez', 'X1234567L', 'example@example.com', '666999666', 'C/ De las piedras,1', '08023', 'Los cantos, SEVILLA', 'EspaÃ±a', 'project.jpg', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id laborum.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', NULL, 'asdf, asdf,asdf ,sdaf, asdf', 'http://www.youtube.com/watch?v=6TJbpCC7iPg', 2, 'Online', 'Tengo un martillo y una sierra', NULL),
('e4ae82c6a3497c04d2338fe63961c92c', 'Mi proyecto 3', 1, 23, 'root', 'goteo', 0, '2011-03-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('the-ultimate-grat-project-of-the-wolrd-united-nati', 'The ultimate great project of the wolrd united nations congregated to the end of time and space ship', 2, 55, 'root', 'goteo', NULL, NULL, '2011-04-10', '2011-04-09', '2011-04-10', NULL, 'JuliÃ¡n', 'CÃ¡naves Bover', '43108914Z', 'julian.canaves@gmail.com', '649085539', 'C/ Patata, 1', '07014', 'Palma de Mallorca', 'EspaÃ±a', 'project.jpg', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n\r\n', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', 'The ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\nThe ultimate grat project of the wolrd united nations congregated to the end of time and space ship\r\n', 'stuff', NULL, 'fasdfasdfasdfasdfasddf', 4, 'Internet', NULL, NULL);

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
('error-register-email', 'La direcciÃ³n de correo es obligatoria.'),
('error-register-email-confirm', 'La comprobaciÃ³n de email no coincide.'),
('error-register-email-exists', 'El direcciÃ³n de correo ya corresponde a un usuario registrado.'),
('error-register-password-confirm', 'La comprobaciÃ³n de contraseÃ±a no coincide.'),
('error-register-pasword', 'La contraseÃ±a no puede estar vacÃ­a.'),
('error-register-short-password', 'La contraseÃ±a debe contener un mÃ­nimo de 8 caracteres.'),
('error-register-user-exists', 'El usuario ya existe.'),
('error-register-username', 'El nombre de usuario usuario es obligatorio.'),
('explain-project-progress', 'Texto bajo el tÃ­tulo Estado global de la informaciÃ³n'),
('guide-project-contract-information', 'Texto guÃ­a en el paso DATOS PERSONALES del formulario de proyecto'),
('guide-project-costs', 'Texto guÃ­a en el paso COSTES del formulario de proyecto'),
('guide-project-description', 'Texto guÃ­a en el paso DESCRIPCIÃ“N del formulario de proyecto'),
('guide-project-error-mandatories', 'Faltan campos obligatorios'),
('guide-project-overview', 'Texto guÃ­a en el paso PREVISUALIZACIÃ“N del formulario de proyecto'),
('guide-project-rewards', 'Texto guÃ­a en el paso RETORNO del formulario de proyecto'),
('guide-project-success-minprogress', 'Ha llegado al porcentaje mÃ­nimo'),
('guide-project-success-noerrors', 'Todos los campos obligatorios estan rellenados'),
('guide-project-success-okfinish', 'Puede enviar para valoraciÃ³n'),
('guide-project-support', 'Texto guÃ­a en el paso COLABORACIONES del formulario de proyecto'),
('guide-project-user-information', 'Texto guÃ­a en el paso PERFIL del formulario de proyecto'),
('guide-user-data', 'Texto guÃ­a en la ediciÃ³n de datos sensibles del usuario'),
('guide-user-information', 'Texto guÃ­a en la ediciÃ³n de informaciÃ³n del usuario'),
('guide-user-register', 'Texto guÃ­a en el registro de usuario'),
('mandatory-cost-field-description', 'Es obligatorio poner alguna descripciÃ³n'),
('mandatory-cost-field-name', 'Es obligatorio ponerle un nombre al coste'),
('mandatory-individual_reward-field-amount', 'Es obligatorio indicar el importe que otorga la recompensa'),
('mandatory-individual_reward-field-description', 'Es obligatorio poner alguna descripciÃ³n'),
('mandatory-individual_reward-field-name', 'Es obligatorio poner la recompensa'),
('mandatory-project-costs', 'MÃ­nimo de costes a desglosar en un proyecto'),
('mandatory-project-field-about', 'Es obligatorio explicar quÃ© es en la descripciÃ³n del proyecto'),
('mandatory-project-field-address', 'La direcciÃ³n del responsable del proyecto es obligatoria'),
('mandatory-project-field-category', 'La categorÃ­a del proyecto es obligatoria'),
('mandatory-project-field-contract-email', 'El email del responsable del proyecto es obligatorio'),
('mandatory-project-field-contract-name', 'El nombre del responsable del proyecto es obligatorio'),
('mandatory-project-field-contract-nif', 'El nif del responsable del proyecto es obligatorio'),
('mandatory-project-field-contract-surname', 'El apellido del responsable del proyecto es obligatorio'),
('mandatory-project-field-country', 'El paÃ­s del responsable del proyecto es obligatorio'),
('mandatory-project-field-description', 'La descripciÃ³n del proyecto es obligatorio'),
('mandatory-project-field-goal', 'Es obligatorio explicar los objetivos en la descripciÃ³n del proyecto'),
('mandatory-project-field-image', 'Es obligatorio poner una imagen al proyecto'),
('mandatory-project-field-location', 'La localizaciÃ³n del proyecto es obligatoria'),
('mandatory-project-field-media', 'Poner un vÃ­deo para mejorar la puntuaciÃ³n'),
('mandatory-project-field-motivation', 'Es obligatorio explicar la motivaciÃ³n en la descripciÃ³n del proyecto'),
('mandatory-project-field-name', 'El nombre del proyecto es obligatorio'),
('mandatory-project-field-phone', 'El telÃ©fono del responsable del proyecto es obligatorio'),
('mandatory-project-field-related', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripciÃ³n del proyecto'),
('mandatory-project-field-residence', 'El lugar de residencia del responsable del proyecto es obligatorio'),
('mandatory-project-field-resource', 'Es obligatorio especificar si cuentas con otros recursos'),
('mandatory-project-field-zipcode', 'El cÃ³digo postal del responsable del proyecto es obligatorio'),
('mandatory-social_reward-field-description', 'Es obligatorio poner alguna descripciÃ³n al retorno'),
('mandatory-social_reward-field-name', 'Es obligatorio poner el retorno'),
('mandatory-support-field-description', 'Es obligatorio poner alguna descripciÃ³n'),
('mandatory-support-field-name', 'Es obligatorio ponerle un nombre a la colaboraciÃ³n'),
('regular-mandatory', 'Texto genÃ©rico para indicar campo obligatorio'),
('step-1', 'Paso 1, informaciÃ³n del usuario'),
('step-2', 'Paso 2, informaciÃ³n del responsable'),
('step-3', 'Paso 3, descripciÃ³n del proyecto'),
('step-4', 'Paso 4, desglose de costes'),
('step-5', 'Paso 5, retornos'),
('step-6', 'Paso 6, colaboraciones'),
('step-7', 'paso 7, previsualizaciÃ³n'),
('tooltip-project-about', 'Consejo para rellenar el campo quÃ© es'),
('tooltip-project-address', 'Consejo para rellenar el address del responsable del proyecto'),
('tooltip-project-category', 'Consejo para seleccionar la categorÃ­a del proyecto'),
('tooltip-project-contract_email', 'Consejo para rellenar el email del responsable del proyecto'),
('tooltip-project-contract_name', 'Consejo para rellenar el nombre del responsable del proyecto'),
('tooltip-project-contract_nif', 'Consejo para rellenar el nif del responsable del proyecto'),
('tooltip-project-contract_surname', 'Consejo para rellenar el apellido del responsable del proyecto'),
('tooltip-project-cost', 'Consejo para editar desgloses existentes'),
('tooltip-project-country', 'Consejo para rellenar el paÃ­s del responsable del proyecto'),
('tooltip-project-currently', 'Consejo para rellenar el estado de desarrollo del proyecto'),
('tooltip-project-description', 'Consejo para rellenar la descripciÃ³n del proyecto'),
('tooltip-project-goal', 'Consejo para rellenar el campo objetivos'),
('tooltip-project-image', 'Consejo para rellenar la imagen del proyecto'),
('tooltip-project-individual_reward', 'Consejo para editar retornos individuales existentes'),
('tooltip-project-keywords', 'Consejo para rellenar las palabras clave del proyecto'),
('tooltip-project-location', 'Consejo para rellenar el lugar de residencia del responsable del proyecto'),
('tooltip-project-media', 'Consejo para rellenar el media del proyecto'),
('tooltip-project-motivation', 'Consejo para rellenar el campo motivaciÃ³n'),
('tooltip-project-name', 'Consejo para rellenar el nombre del proyecto'),
('tooltip-project-ncost', 'Consejo para rellenar un nuevo desglose de costes'),
('tooltip-project-nindividual_reward', 'Consejo para rellenar un nuevo retorno individual'),
('tooltip-project-nsocial_reward', 'Consejo para rellenar un nuevo retorno colectivo'),
('tooltip-project-nsupport', 'Consejo para rellenar una nueva colaboraciÃ³n'),
('tooltip-project-phone', 'Consejo para rellenar el telÃ©fono del responsable del proyecto'),
('tooltip-project-project_location', 'Consejo para rellenar la localizaciÃ³n del proyecto'),
('tooltip-project-related', 'Consejo para rellenar el campo experiencia relacionada y equipo'),
('tooltip-project-resource', 'Consejo para rellenar el campo Cuenta con otros recursos?'),
('tooltip-project-social_reward', 'Consejo para editar retornos colectivos existentes'),
('tooltip-project-support', 'Consejo para editar colaboraciones existentes'),
('tooltip-project-zipcode', 'Consejo para rellenar el zipcode del responsable del proyecto'),
('tooltip-user-about', 'Consejo para rellenar el cuÃ©ntanos algo sobre tÃ­'),
('tooltip-user-blog', 'Consejo para rellenar la web'),
('tooltip-user-contribution', 'Consejo para rellenar el quÃ© podrÃ­as aportar en goteo'),
('tooltip-user-email', 'Consejo para rellenar el email de registro de usuario'),
('tooltip-user-facebook', 'Consejo para rellenar el facebook'),
('tooltip-user-image', 'Consejo para rellenar la imagen del usuario'),
('tooltip-user-interests', 'Consejo para seleccionar tus intereses'),
('tooltip-user-keywords', 'Consejo para rellenar tus palabras clave'),
('tooltip-user-linkedin', 'Consejo para rellenar el linkedin'),
('tooltip-user-name', 'Consejo para rellenar el nombre completo del usuario'),
('tooltip-user-twitter', 'Consejo para rellenar el twitter'),
('tooltip-user-user', 'Consejo para rellenar el nombre de usuario para login'),
('validate-cost-field-dates', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuaciÃ³n'),
('validate-project-field-contract-email', 'El email del responsable del proyecto debe ser correcto'),
('validate-project-field-costs', 'Desglosar hasta 5 costes para mejorar la puntuaciÃ³n'),
('validate-project-field-currently', 'Indicar el estado del proyecto para mejorar la puntuaciÃ³n'),
('validate-project-individual_rewards', 'Indicar hasta 5 recompensas individuales para mejorar la puntuaciÃ³n'),
('validate-project-social_rewards', 'Indicar hasta 5 retornos colectivos para mejorar la puntuaciÃ³n'),
('validate-project-total-costs', 'El coste Ã³ptimo no puede exceder demasiado al coste mÃ­nimo'),
('validate-project-value contract-nif', 'El nif del responsable del proyecto debe ser correcto'),
('validate-project-value description', 'La descripciÃ³n del proyecto debe se suficientemente extensa'),
('validate-project-value phone', 'El telÃ©fono debe ser correcto'),
('validate-project-value-contract-email', 'Texto validate-project-value-contract-email'),
('validate-project-value-contract-nif', 'Texto validate-project-value-contract-nif'),
('validate-project-value-description', 'Texto validate-project-value-description'),
('validate-project-value-keywords', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuaciÃ³n'),
('validate-project-value-phone', 'Texto validate-project-value-phone'),
('validate-social_reward-license', 'Indicar una licencia para mejorar la puntuaciÃ³n'),
('validate-user-field-about', 'Si no ha puesto nada sobre el/ella '),
('validate-user-field-avatar', 'Si no ha puesto una imagen de perfil'),
('validate-user-field-contribution', 'Si no ha puesto quÃ© puede aportar a Goteo'),
('validate-user-field-facebook', 'Si no ha puesto su cuenta de facebook'),
('validate-user-field-interests', 'Si no ha seleccionado ningÃºn interÃ©s'),
('validate-user-field-keywords', 'Si no ha puesto ninguna palabra clave'),
('validate-user-field-name', 'Si no ha puesto el nombre completo'),
('validate-user-field-webs', 'Si no ha puesto ninguna web');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reward`
--

DROP TABLE IF EXISTS `reward`;
CREATE TABLE IF NOT EXISTS `reward` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `reward` varchar(256) NOT NULL,
  `description` tinytext,
  `type` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `license` varchar(50) DEFAULT NULL,
  `amount` int(5) DEFAULT NULL,
  `units` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
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
(2, 'Admin Nodo'),
(3, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `support`
--

DROP TABLE IF EXISTS `support`;
CREATE TABLE IF NOT EXISTS `support` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `support` tinytext NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Colaboraciones' AUTO_INCREMENT=15 ;

--
-- Volcar la base de datos para la tabla `support`
--

INSERT INTO `support` (`id`, `project`, `support`, `description`, `type`) VALUES
(1, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'Contar arroz', 'Contar mil sacos de arroz', 'task'),
(2, 'the-ultimate-grat-project-of-the-wolrd-united-nati', 'Clavar clavos', 'en las maderas', 'task'),
(5, 'church-project-eko-one', 'dsfg', 'dsfg', 'task'),
(6, 'church-project-eko-one', 'sdf sdaf', 'sdf sdf ', 'task'),
(7, 'church-project-eko-one', 'sdf ', 'asd f', 'task'),
(8, 'church-project-eko-one', 'sdf ', 'asd f', 'task'),
(9, 'church-project-eko-one', 'sdf', 'sdf', 'task'),
(10, 'church-project-eko-one', 'asdf', 'sdf', 'task'),
(11, 'church-project-eko-one', 'asdf', 'sdf', 'task'),
(12, 'church-project-eko-one', 'aaa1', 'aaaa11', 'task'),
(13, 'church-project-eko-one', 'aaa1', 'aaaa11', 'task'),
(14, 'church-project-eko-one', 'dsfg', 'sdfg', 'task');

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
('error-register-email', 'es', 'La direcciÃ³n de correo es obligatoria.'),
('error-register-email-confirm', 'es', 'La comprobaciÃ³n de email no coincide.'),
('error-register-email-exists', 'es', 'El direcciÃ³n de correo ya corresponde a un usuario registrado.'),
('error-register-password-confirm', 'es', 'La comprobaciÃ³n de contraseÃ±a no coincide.'),
('error-register-pasword', 'es', 'La contraseÃ±a no puede estar vacÃ­a.'),
('error-register-short-password', 'es', 'La contraseÃ±a debe contener un mÃ­nimo de 8 caracteres.'),
('error-register-user-exists', 'es', 'El usuario ya existe.'),
('error-register-username', 'es', 'El nombre de usuario usuario es obligatorio.'),
('explain-project-progress', 'es', 'Texto bajo el tÃ­tulo Estado global de la informaciÃ³n'),
('guide-project-contract-information', 'es', 'Texto guÃ­a en el paso DATOS PERSONALES del formulario de proyecto.'),
('guide-project-costs', 'es', 'Texto guÃ­a en el paso COSTES del formulario de proyecto.'),
('guide-project-description', 'es', 'Texto guÃ­a en el paso DESCRIPCIÃ“N del formulario de proyecto.'),
('guide-project-error-mandatories', 'es', 'Faltan campos obligatorios'),
('guide-project-overview', 'es', 'Texto guÃ­a en el paso PREVISUALIZACIÃ“N del formulario de proyecto.'),
('guide-project-rewards', 'es', 'Texto guÃ­a en el paso RETORNO del formulario de proyecto.'),
('guide-project-success-minprogress', 'es', 'Ha llegado al porcentaje mÃ­nimo'),
('guide-project-success-noerrors', 'es', 'Todos los campos obligatorios estan rellenados'),
('guide-project-success-okfinish', 'es', 'Puede enviar para valoraciÃ³n'),
('guide-project-support', 'es', 'Texto guÃ­a en el paso COLABORACIONES del formulario de proyecto.'),
('guide-project-user-information', 'es', 'Texto guÃ­a en el paso PERFIL del formulario de proyecto.'),
('guide-user-data', 'es', 'Texto guÃ­a en la ediciÃ³n de campos sensibles.'),
('guide-user-information', 'es', 'Texto guÃ­a en la ediciÃ³n de informaciÃ³n del usuario.'),
('guide-user-register', 'es', 'Texto guÃ­a en el registro de un nuevo usuario.'),
('mandatory-cost-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n'),
('mandatory-cost-field-name', 'es', 'Es obligatorio ponerle un nombre al coste'),
('mandatory-individual_reward-field-amount', 'es', 'Es obligatorio indicar el importe que otorga la recompensa'),
('mandatory-individual_reward-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n'),
('mandatory-individual_reward-field-name', 'es', 'Es obligatorio poner la recompensa'),
('mandatory-project-costs', 'es', 'Debe desglosar en al menos DOS COSTES, paso 4 costes.'),
('mandatory-project-field-about', 'es', 'Es obligatorio explicar quÃ© es en la descripciÃ³n del proyecto'),
('mandatory-project-field-address', 'es', 'La direcciÃ³n del responsable del proyecto es obligatoria'),
('mandatory-project-field-category', 'es', 'Es obligatorio elegir una CATEGORIA para el proyecto, paso 3: DescripciÃ³n.'),
('mandatory-project-field-contract-email', 'es', 'Es obligatorio poner el EMAIL del responsable del proyecto, paso 2: Datos personales.'),
('mandatory-project-field-contract-name', 'es', 'Es obligatorio poner el NOMBRE del responsable del proyecto, paso 2: Datos personales.'),
('mandatory-project-field-contract-nif', 'es', 'Es obligatorio poner el NIF del responsable del proyecto, paso 2: Datos personales.'),
('mandatory-project-field-contract-surname', 'es', 'Es obligatorio poner los APELLIDOS del responsable del proyecto, paso 2: Datos personales.'),
('mandatory-project-field-country', 'es', 'El paÃ­s del responsable del proyecto es obligatorio'),
('mandatory-project-field-description', 'es', 'Es obligatorio poner una DESCRIPCIÃ“N al proyecto, paso 3: DescripciÃ³n.'),
('mandatory-project-field-goal', 'es', 'Es obligatorio explicar los objetivos en la descripciÃ³n del proyecto'),
('mandatory-project-field-image', 'es', 'Es obligatorio poner una imagen al proyecto'),
('mandatory-project-field-location', 'es', 'Es obligatorio poner la LOCALIZACIÃ“N del proyecto, paso 3: DescripciÃ³n.'),
('mandatory-project-field-media', 'es', 'Poner un vÃ­deo para mejorar la puntuaciÃ³n'),
('mandatory-project-field-motivation', 'es', 'Es obligatorio explicar la motivaciÃ³n en la descripciÃ³n del proyecto'),
('mandatory-project-field-name', 'es', 'Es obligatorio poner un NOMBRE al proyecto, paso 3: DescripciÃ³n.'),
('mandatory-project-field-phone', 'es', 'El telÃ©fono del responsable del proyecto es obligatorio'),
('mandatory-project-field-related', 'es', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripciÃ³n del proyecto'),
('mandatory-project-field-residence', 'es', 'Es obligatorio poner el LUGAR DE RESIDENCIA del responsable del proyecto, paso 2: Datos personales.'),
('mandatory-project-field-resource', 'es', 'Es obligatorio especificar si cuentas con otros recursos'),
('mandatory-project-field-zipcode', 'es', 'El cÃ³digo postal del responsable del proyecto es obligatorio'),
('mandatory-social_reward-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n al retorno'),
('mandatory-social_reward-field-name', 'es', 'Es obligatorio poner el retorno'),
('mandatory-support-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n'),
('mandatory-support-field-name', 'es', 'Es obligatorio ponerle un nombre a la colaboraciÃ³n'),
('regular-mandatory', 'es', 'Campo obligatorio!'),
('step-1', 'es', 'PERFIL'),
('step-2', 'es', 'DATOS PERSONALES'),
('step-3', 'es', 'DESCRIPCIÃ“N'),
('step-4', 'es', 'COSTES'),
('step-5', 'es', 'RETORNO'),
('step-6', 'es', 'COLABORACIONES'),
('step-7', 'es', 'PREVISUALIZACIÃ“N'),
('tooltip-project-about', 'es', 'Consejo para rellenar el campo quÃ© es'),
('tooltip-project-address', 'es', 'Consejo para rellenar el address del responsable del proyecto'),
('tooltip-project-category', 'es', 'Consejo para seleccionar la categorÃ­a del proyecto'),
('tooltip-project-contract_email', 'es', 'Consejo para rellenar el email del responsable del proyecto'),
('tooltip-project-contract_name', 'es', 'Consejo para rellenar el nombre del responsable del proyecto'),
('tooltip-project-contract_nif', 'es', 'Consejo para rellenar el nif del responsable del proyecto'),
('tooltip-project-contract_surname', 'es', 'Consejo para rellenar el apellido del responsable del proyecto'),
('tooltip-project-cost', 'es', 'Consejo para editar desgloses existentes'),
('tooltip-project-country', 'es', 'Consejo para rellenar el paÃ­s del responsable del proyecto'),
('tooltip-project-currently', 'es', 'Consejo para rellenar el estado de desarrollo del proyecto'),
('tooltip-project-description', 'es', 'Consejo para rellenar la descripciÃ³n del proyecto'),
('tooltip-project-goal', 'es', 'Consejo para rellenar el campo objetivos'),
('tooltip-project-image', 'es', 'Consejo para rellenar la imagen del proyecto'),
('tooltip-project-individual_reward', 'es', 'Consejo para editar retornos individuales existentes'),
('tooltip-project-keywords', 'es', 'Consejo para rellenar las palabras clave del proyecto'),
('tooltip-project-location', 'es', 'Consejo para rellenar el lugar de residencia del responsable del proyecto'),
('tooltip-project-media', 'es', 'Consejo para rellenar el media del proyecto'),
('tooltip-project-motivation', 'es', 'Consejo para rellenar el campo motivaciÃ³n'),
('tooltip-project-name', 'es', 'Consejo para rellenar el nombre del proyecto'),
('tooltip-project-ncost', 'es', 'Consejo para rellenar un nuevo desglose de costes'),
('tooltip-project-nindividual_reward', 'es', 'Consejo para rellenar un nuevo retorno individual'),
('tooltip-project-nsocial_reward', 'es', 'Consejo para rellenar un nuevo retorno colectivo'),
('tooltip-project-nsupport', 'es', 'Consejo para rellenar una nueva colaboraciÃ³n'),
('tooltip-project-phone', 'es', 'Consejo para rellenar el telÃ©fono del responsable del proyecto'),
('tooltip-project-project_location', 'es', 'Consejo para rellenar la localizaciÃ³n del proyecto'),
('tooltip-project-related', 'es', 'Consejo para rellenar el campo experiencia relacionada y equipo'),
('tooltip-project-resource', 'es', 'Consejo para rellenar el campo Cuenta con otros recursos?'),
('tooltip-project-social_reward', 'es', 'Consejo para editar retornos colectivos existentes'),
('tooltip-project-support', 'es', 'Consejo para editar colaboraciones existentes'),
('tooltip-project-zipcode', 'es', 'Consejo para rellenar el zipcode del responsable del proyecto'),
('tooltip-user-about', 'es', 'Consejo para rellenar el cuÃ©ntanos algo sobre tÃ­'),
('tooltip-user-blog', 'es', 'Consejo para rellenar la web'),
('tooltip-user-contribution', 'es', 'Consejo para rellenar el quÃ© podrÃ­as aportar en goteo'),
('tooltip-user-email', 'es', 'Consejo para rellenar el email de registro de usuario'),
('tooltip-user-facebook', 'es', 'Consejo para rellenar el facebook'),
('tooltip-user-image', 'es', 'Consejo para rellenar la imagen del usuario'),
('tooltip-user-interests', 'es', 'Consejo para seleccionar tus intereses'),
('tooltip-user-keywords', 'es', 'Consejo para rellenar tus palabras clave'),
('tooltip-user-linkedin', 'es', 'Consejo para rellenar el linkedin'),
('tooltip-user-name', 'es', 'Consejo para rellenar el nombre completo del usuario'),
('tooltip-user-twitter', 'es', 'Consejo para rellenar el twitter'),
('tooltip-user-user', 'es', 'Consejo para rellenar el nombre de usuario para login'),
('validate-cost-field-dates', 'es', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuaciÃ³n'),
('validate-project-field-costs', 'es', 'Desglosar hasta 5 costes para mejorar la puntuaciÃ³n'),
('validate-project-field-currently', 'es', 'Indicar el estado del proyecto para mejorar la puntuaciÃ³n'),
('validate-project-individual_rewards', 'es', 'Indicar hasta 5 recompensas individuales para mejorar la puntuaciÃ³n'),
('validate-project-social_rewards', 'es', 'Indicar hasta 5 retornos colectivos para mejorar la puntuaciÃ³n'),
('validate-project-total-costs', 'es', 'El coste Ã³ptimo no puede superar en mÃ¡s de un 40% al coste mÃ­nimo. Revisar el DESGLOSE DE COSTES, paso 4 costes.'),
('validate-project-value contract-email', 'es', 'El EMAIL no es correcto, paso 2: Datos personales.'),
('validate-project-value contract-nif', 'es', 'El NIF no es correcto, paso 2: Datos personales.'),
('validate-project-value description', 'es', 'La DESCRIPCIÃ“N del proyecto es demasiado corta, paso 3: DescripciÃ³n.'),
('validate-project-value phone', 'es', 'El TELÃ‰FONO no es correcto, paso 2: Datos personales.'),
('validate-project-value-keywords', 'es', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuaciÃ³n'),
('validate-social_reward-license', 'es', 'Indicar una licencia para mejorar la puntuaciÃ³n'),
('validate-user-field-about', 'es', 'Cuenta algo sobre ti para mejorar la puntuaciÃ³n'),
('validate-user-field-avatar', 'es', 'Pon una imagen de perfil para mejorar la puntuaciÃ³n'),
('validate-user-field-contribution', 'es', 'Explica que podrias aportar en Goteo para mejorar la puntuaciÃ³n'),
('validate-user-field-facebook', 'es', 'Pon tu cuenta de facebook para mejorar la puntuaciÃ³n'),
('validate-user-field-interests', 'es', 'Selecciona algÃºn interÃ©s para mejorar la puntuaciÃ³n'),
('validate-user-field-keywords', 'es', 'Indica hasta 5 palabras clave que te definan para mejorar la puntuaciÃ³n'),
('validate-user-field-name', 'es', 'Pon tu nombre completo para mejorar la puntuaciÃ³n'),
('validate-user-field-webs', 'es', 'Pon tu pÃ¡gina web para mejorar la puntuaciÃ³n');

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
  `keywords` tinytext,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `avatar` tinytext,
  `contribution` text,
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

INSERT INTO `user` (`id`, `role_id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('pepa', 3, 'Pepa PÃ©rez', 'josefa@doukeshi.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Yo soy JOSEFAAAA!!!!!', NULL, 1, 'avatar.jpg', 'mucho arte', '@josefa', 'feisbuc.com/josefaaaaa', 'ein?', NULL, '2011-03-19 00:00:00', '2011-04-03 01:43:01'),
('pepe', 2, 'pepe', 'asdf', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2011-03-19 00:00:00', '2011-04-03 01:42:57'),
('pepo', 0, 'pepo', 'pepe@doukeshi.org', '51abb9636078defbf888d8457a7c76f85c8f114c', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '2011-04-10 02:05:52'),
('root', 1, 'Super administrador', 'goteo@doukeshi.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Super administrador de la plataforma Goteo.org', NULL, 1, NULL, 'Super administrador de la plataforma Goteo.org', NULL, NULL, NULL, NULL, '2011-03-16 00:00:00', '2011-04-03 01:42:53');

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
-- Estructura de tabla para la tabla `user_web`
--

DROP TABLE IF EXISTS `user_web`;
CREATE TABLE IF NOT EXISTS `user_web` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `url` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Webs de los usuarios' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `user_web`
--


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
