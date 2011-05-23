-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 20-05-2011 a las 00:34:36
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
-- Volcar la base de datos para la tabla `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('goteo', 'superadmin', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('goteo', 'user', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('pepa', 'user', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('pepe', 'user', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('pepo', 'user', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('root', 'admin', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('root', 'root', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('root', 'superadmin', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('root', 'user', '*');
INSERT INTO `user_role` (`user_id`, `role_id`, `node_id`) VALUES('goteo', 'admin', 'goteo');
