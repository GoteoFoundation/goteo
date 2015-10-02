-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-07-2011 a las 00:10:53
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

--
-- Volcar la base de datos para la tabla `icon`
--

INSERT INTO `icon` (`id`, `name`, `description`, `group`, `order`) VALUES('code', 'CÃ³digo fuente', '', 'social', 0);
INSERT INTO `icon` (`id`, `name`, `description`, `group`, `order`) VALUES('design', 'DiseÃ±o', '', 'social', 0);
INSERT INTO `icon` (`id`, `name`, `description`, `group`, `order`) VALUES('file', 'Archivos digitales', '', NULL, 0);
INSERT INTO `icon` (`id`, `name`, `description`, `group`, `order`) VALUES('manual', 'Manuales', '', 'social', 0);
INSERT INTO `icon` (`id`, `name`, `description`, `group`, `order`) VALUES('money', 'Dinero', '', 'individual', 88);
INSERT INTO `icon` (`id`, `name`, `description`, `group`, `order`) VALUES('other', 'Otro', '', NULL, 99);
INSERT INTO `icon` (`id`, `name`, `description`, `group`, `order`) VALUES('product', 'Producto', '', 'individual', 0);
INSERT INTO `icon` (`id`, `name`, `description`, `group`, `order`) VALUES('service', 'Servicios', '', NULL, 0);
