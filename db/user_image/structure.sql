-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 24-04-2011 a las 23:17:16
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
-- Estructura de tabla para la tabla `user_image`
--

DROP TABLE IF EXISTS `user_image`;
CREATE TABLE IF NOT EXISTS `user_image` (
  `user_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`image_id`),
  KEY `user_FK` (`user_id`),
  KEY `image_FK` (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
