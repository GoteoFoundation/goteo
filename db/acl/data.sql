-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 19-05-2011 a las 01:03:20
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
-- Volcar la base de datos para la tabla `acl`
--

INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(1, '*', '*', '*', '//', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(2, '*', '*', '*', '/image/*', 1, '2011-05-18 23:08:42');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(4, '*', '*', '*', '/admin/*', 0, '2011-05-18 16:45:40');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(5, '*', '*', '*', '/project/*', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(6, '*', 'superadmin', '*', '/admin/*', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(7, '*', '*', '*', '/user/edit/*', 0, '2011-05-18 16:49:36');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(8, '*', '*', '*', '/user/*', 1, '2011-05-18 20:59:54');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(9, '*', '*', '*', 'user/logout', 1, '2011-05-18 21:15:02');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(10, '*', '*', '*', '/search', 1, '2011-05-18 21:16:40');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(11, '*', 'user', '*', '/project/create', 0, '2011-05-18 21:46:44');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(12, '*', 'user', '*', '/dashboard/*', 1, '2011-05-18 21:48:43');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(13, '*', 'public', '*', '/invest/*', 0, '2011-05-18 22:30:23');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(14, '*', 'user', '*', '/message/*', 1, '2011-05-18 22:30:23');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(15, '*', '*', '*', '/user/logout', 1, '2011-05-18 22:33:27');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(16, '*', '*', '*', '/discover/*', 1, '2011-05-18 22:37:00');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(17, '*', '*', '*', '/project/create', 0, '2011-05-18 22:38:22');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(18, '*', '*', '*', '/project/edit/*', 0, '2011-05-18 22:38:22');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(19, '*', '*', '*', '/project/raw/*', 0, '2011-05-18 22:39:37');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(20, '*', 'root', '*', '/project/raw/*', 1, '2011-05-18 22:39:37');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(21, '*', 'superadmin', '*', '/project/edit/*', 1, '2011-05-18 22:43:08');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(22, '*', '*', '*', '/project/trash/*', 0, '2011-05-18 22:43:51');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(23, '*', 'superadmin', '*', '/project/trash/*', 1, '2011-05-18 22:44:37');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(24, '*', '*', '*', '/blog/*', 1, '2011-05-18 22:45:14');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(25, '*', '*', '*', '/faq/*', 1, '2011-05-18 22:49:01');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(26, '*', '*', '*', '/about/*', 1, '2011-05-18 22:49:01');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(27, '*', 'superadmin', '*', '/user/edit/*', 1, '2011-05-18 22:56:56');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(29, '*', 'user', '*', '/user/edit', 1, '2011-05-18 23:56:56');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(30, '*', 'user', '*', '/message/edit/*', 0, '2011-05-19 00:45:29');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(31, '*', 'user', '*', '/message/delete/*', 0, '2011-05-19 00:45:29');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(32, '*', 'superadmin', '*', '/message/edit/*', 1, '2011-05-19 00:56:55');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(33, '*', 'superadmin', '*', '/message/delete/*', 1, '2011-05-19 00:00:00');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(34, '*', 'user', '*', '/invest/*', 1, '2011-05-19 00:56:32');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(35, '*', 'public', '*', '/message/*', 0, '2011-05-19 00:56:32');
INSERT INTO `acl` (`id`, `node_id`, `role_id`, `user_id`, `url`, `allow`, `timestamp`) VALUES(36, '*', 'public', '*', '/user/edit/*', 0, '2011-05-19 01:00:18');
