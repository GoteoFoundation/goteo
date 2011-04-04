-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 04-04-2011 a las 15:50:11
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

--
-- Volcar la base de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `role_id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `blog`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`) VALUES
('pepa', 3, 'Pepa PÃ©rez', 'josefa@doukeshi.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Yo soy JOSEFAAAA!!!!!', NULL, 1, 'avatar.jpg', 'mucho arte', 'lajosefaisthebest.com', '@josefa', 'feisbuc.com/josefaaaaa', 'ein?', NULL, '2011-03-19 00:00:00', '2011-04-03 01:43:01'),
('pepe', 2, 'pepe', 'asdf', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2011-03-19 00:00:00', '2011-04-03 01:42:57'),
('root', 1, 'Super administrador', 'goteo@doukeshi.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Super administrador de la plataforma Goteo.org', NULL, 1, NULL, 'Super administrador de la plataforma Goteo.org', NULL, NULL, NULL, NULL, NULL, '2011-03-16 00:00:00', '2011-04-03 01:42:53');
