-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 08-07-2015 a las 19:39:48
-- Versión del servidor: 5.5.34
-- Versión de PHP: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `prestamos`
--
CREATE DATABASE IF NOT EXISTS `prestamos` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `prestamos`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

DROP TABLE IF EXISTS `movimientos`;
CREATE TABLE IF NOT EXISTS `movimientos` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) NOT NULL,
  `num_movto` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `movimiento` varchar(100) NOT NULL,
  `justificacion` varchar(500) NOT NULL,
  `cargo` decimal(12,2) NOT NULL,
  `abono` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_movimiento`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `movimientos`
--

TRUNCATE TABLE `movimientos`;
--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id_movimiento`, `id_contrato`, `num_movto`, `fecha`, `movimiento`, `justificacion`, `cargo`, `abono`) VALUES
(1, 1, 1, '2015-07-08', 'Prueba 1', 'Justificación prueba 1', '100.00', '0.00'),
(2, 2, 1, '2015-07-08', 'Prueba 2', 'Justificacion prueba 2', '0.00', '200.50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(32) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(45) NOT NULL,
  `acceso` varchar(32) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Truncar tablas antes de insertar `usuarios`
--

TRUNCATE TABLE `usuarios`;
--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `acceso`) VALUES
(1, 'prueba', '0e7881f0d44670fed326557fc047de90'),
(2, 'admin', '7a95bf926a0333f57705aeac07a362a2');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
