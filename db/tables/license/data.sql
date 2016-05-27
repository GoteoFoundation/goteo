-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 18-05-2011 a las 18:47:36
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
-- Volcar la base de datos para la tabla `license`
--

INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('agpl', 'Affero General Public License', 'GNU Affero General Public License', '', NULL, 2);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('apache', 'Apache License', 'Apache License', '', NULL, 10);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('balloon', 'Balloon Open Hardware License', 'Balloon Open Hardware License', '', NULL, 20);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('bsd', 'Berkeley Software Distribution', 'BSD (Berkeley Software Distribution)', 'open', NULL, 5);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('cc0', 'CC0 Universal', 'CC0 Universal', '', NULL, 25);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('ccby', 'CC - Reconocimiento', 'Creative Commons - Reconocimiento (by)', 'open', NULL, 12);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('ccbync', 'CC - Reconocimiento - NoComercial', 'Creative Commons - Reconocimiento - NoComercial (by-nc)', '', NULL, 13);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('ccbyncnd', 'CC - Reconocimiento - NoComercial - SinObraDerivada', 'Creative Commons - Reconocimiento - NoComercial - SinObraDerivada (by-nc-nd)', '', NULL, 15);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('ccbyncsa', 'CC - Reconocimiento - NoComercial - CompartirIgual', 'Creative Commons - Reconocimiento - NoComercial - CompartirIgual (by-nc-sa)', '', NULL, 14);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('ccbynd', 'CC - Reconocimiento - SinObraDerivada', 'Creative Commons - Reconocimiento - SinObraDerivada (by-nd)', '', NULL, 17);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('ccbysa', 'CC - Reconocimiento - CompartirIgual', 'Creative Commons - Reconocimiento - CompartirIgual (by-sa)', 'open', NULL, 16);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('fal', 'Free Art License', 'Free Art License', '', NULL, 11);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('fdl', 'Free Documentation License ', 'GNU Free Documentation License (FDL)', 'open', NULL, 4);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('freebsd', 'FreeBSD documenaciÃ³n', 'Licencia de DocumentaciÃ³n de FreeBSD', 'open', NULL, 6);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('gpl', 'General Public License', 'GNU General Public License (GPL) GPLv3', 'open', NULL, 1);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('lgpl', 'Lesser General Public License', 'GNU Lesser General Public License', 'open', NULL, 3);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('mit', 'MIT', 'MIT (or X11 license)', '', NULL, 8);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('mpl', 'Mozilla Public License', 'Mozilla Public License (MPL)', '', NULL, 7);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('odbl', 'Open Database License ', 'Open Database License (ODbL)', 'open', NULL, 22);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('odcby', 'Open Data Commons Attribution License', 'Open Data Commons Attribution License (ODC-by)', 'open', NULL, 23);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('oshw', 'Open Source Hardware', 'OSHW (Open Source Hardware)', 'open', NULL, 18);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('pd', 'Public domain', 'Public domain', '', NULL, 24);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('php', 'Licencia PHP', 'Licencia PHP', '', NULL, 9);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('tapr', 'Noncommercial Hardware License', 'TAPR Noncommercial Hardware License ("NCL")', '', NULL, 19);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('xoln', 'Red Abierta, Libre y Neutral', 'Red Abierta, Libre y Neutral', 'open', NULL, 21);
INSERT INTO `license` (`id`, `name`, `description`, `group`, `url`, `order`) VALUES('ppl', 'Licencia de Producción de Pares', 'La licencia de producción entre pares es un ejemplo de licencia Copyfarleft con la que únicamente aquellas personas, cooperativas o entidades sin ánimo de lucro pueden compartir y reutilizar la obra, pero no se permite el uso lucrativo por parte de entidades comerciales cuyo objetivo sea obtener beneficios económicos de la misma sin una reciprocidad explícita hacia el procomún.', 'open', 'http://p2pfoundation.net/Peer_Production_License', 26);
