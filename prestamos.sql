-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-07-2015 a las 21:11:06
-- Versión del servidor: 5.6.16
-- Versión de PHP: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `prestamos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catsindicato`
--

DROP TABLE IF EXISTS `catsindicato`;
CREATE TABLE IF NOT EXISTS `catsindicato` (
  `idSindicato` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de la tabla \r\n',
  `sindicato` varchar(50) DEFAULT ' ' COMMENT 'nombre del sindicato\r\n',
  `cveSindicato` varchar(5) DEFAULT '0' COMMENT 'Clave del Sindicato\r\n',
  `representante` varchar(150) DEFAULT ' ' COMMENT 'nombre del representante del sindicato',
  PRIMARY KEY (`idSindicato`),
  UNIQUE KEY `idSindicato` (`idSindicato`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='catÃ¡logo de Sindicatos			\r\n' AUTO_INCREMENT=14 ;

--
-- Volcado de datos para la tabla `catsindicato`
--

INSERT INTO `catsindicato` (`idSindicato`, `sindicato`, `cveSindicato`, `representante`) VALUES
(1, 'CONFIANZA', '0', 'Representante'),
(2, '3 DE MARZO', '1', 'C. HECTOR MAYORAL GUZMAN'),
(3, 'AUTONOMO', '2', 'C. JUAN ARAGON MATIAS'),
(4, 'C.R.O.C.', '3', 'C. EMETERIO GERONIMO SANTIAGO LOPEZ'),
(5, '12 DE SEPTIEMBRE', '4', 'C. ANGEL ROBERTO CORTEZ RAMIREZ'),
(6, 'LIBRE', '5', 'C. MARCELINO COACHE VERANO'),
(7, 'POLICIA', '6', 'Representante'),
(8, 'TRANSITO', '7', 'Representante'),
(9, 'PERSONAL OPERATIVO', '8', 'Representante'),
(10, 'SINDICATO ADMVO', '9', 'Representante'),
(11, '3 DE MARZO (ADMTVO.)', '10', 'C. HECTOR MAYORAL GUZMAN'),
(12, 'OPERATIVO', '11', 'Representante'),
(13, 'CONFIANZA', '12', 'REPRESENTANTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuento`
--

DROP TABLE IF EXISTS `descuento`;
CREATE TABLE IF NOT EXISTS `descuento` (
  `id_descuento` int(11) NOT NULL AUTO_INCREMENT,
  `origen` varchar(1) NOT NULL,
  `creado` datetime NOT NULL,
  `modificado` datetime NOT NULL,
  `creador` int(11) NOT NULL,
  `modificador` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL,
  `observaciones` text NOT NULL,
  `tipo` varchar(1) NOT NULL,
  `pago` varchar(1) NOT NULL,
  `periodo` int(11) NOT NULL,
  PRIMARY KEY (`id_descuento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

DROP TABLE IF EXISTS `empleados`;
CREATE TABLE IF NOT EXISTS `empleados` (
  `numero` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `paterno` varchar(15) NOT NULL,
  `materno` varchar(15) NOT NULL,
  `sindicato` varchar(2) NOT NULL,
  `fec_ingre` date NOT NULL,
  `status` varchar(1) NOT NULL,
  `tipo_nomi` int(1) NOT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`numero`, `nombre`, `paterno`, `materno`, `sindicato`, `fec_ingre`, `status`, `tipo_nomi`) VALUES
(3505, 'PEDRO MIGUEL', 'RUIZ', 'ORDAZ', '2', '1989-09-01', '0', 5),
(7148, 'BRUNO', 'HERNANDEZ', 'VASQUEZ', '1', '1994-06-13', '0', 5),
(8144, 'SALOMON', 'MATIAS', 'ANDRES', '1', '1988-11-21', '1', 5),
(8165, 'CIPRIANO', 'MENDEZ', 'LOPEZ', '1', '1969-10-07', '0', 5),
(44131, 'CARLOS ALEJANDRO', 'SANTIAGO', 'LOPEZ', '1', '2004-08-24', '0', 5),
(44493, 'FERNANDO', 'RODRIGUEZ', 'SANCHEZ', '1', '2006-09-25', '1', 5),
(45692, 'ORLANDO ALEJANDRO', 'VILLANUEVA', 'MORALES', '1', '2010-02-15', '1', 5),
(45693, 'EMETERIO', 'MILLAN', 'ANSELMO', '1', '2010-02-08', '1', 5),
(45717, 'JORGE', 'GARCIA', 'GUZMAN', '1', '2010-02-15', '1', 5),
(47610, 'MARLENNE MARGARITA', 'ORTIZ', 'GARCIA', '5', '2010-02-08', '1', 1),
(47832, 'ANTONIO', 'PEREZ', 'NIÑO', '0', '2010-01-20', '1', 1),
(47835, 'NANCY', 'SIERRA', 'BALBUENA', '0', '2010-02-02', '1', 1),
(47839, 'ANGEL', 'CHAVEZ', 'LOPEZ', '12', '2010-01-01', '1', 1),
(47842, 'JOSE LUIS', 'CASTILLO', 'COLMENARES', '12', '2010-02-01', '1', 1),
(47843, 'MARIA TERESA', 'CRUZ', 'MARTINEZ', '12', '2010-02-01', '1', 1),
(47850, 'MARIA JANETT', 'CABRERA', 'RAMOS', '12', '2010-02-01', '1', 1),
(47851, 'ERICK CIRENIO', 'OLIVERA', 'VENEGAS', '0', '2010-02-01', '1', 1),
(47852, 'JAIME LUIS', 'CASTELLANOS', 'FIGUEROA', '0', '2010-01-01', '1', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estatus`
--

DROP TABLE IF EXISTS `estatus`;
CREATE TABLE IF NOT EXISTS `estatus` (
  `id_estatus` int(11) NOT NULL AUTO_INCREMENT,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id_estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `estatus`
--

INSERT INTO `estatus` (`id_estatus`, `estatus`) VALUES
(1, 'generado'),
(2, 'emviado'),
(3, 'recibido'),
(4, 'aplicado');

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
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id_movimiento`, `id_contrato`, `num_movto`, `fecha`, `movimiento`, `justificacion`, `cargo`, `abono`) VALUES
(1, 1, 1, '2015-07-08', 'Prueba 1', 'Justificación prueba 1', '100.00', '0.00'),
(2, 2, 1, '2015-07-08', 'Prueba 2', 'Justificacion prueba 2', '0.00', '200.50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros`
--

DROP TABLE IF EXISTS `parametros`;
CREATE TABLE IF NOT EXISTS `parametros` (
  `llave` varchar(20) NOT NULL,
  `valor` varchar(250) NOT NULL,
  PRIMARY KEY (`llave`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `parametros`
--

INSERT INTO `parametros` (`llave`, `valor`) VALUES
('ftp_pass', 'nomin4'),
('ftp_server', '192.168.201.250'),
('ftp_user', 'nomina');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pdfijarh`
--

DROP TABLE IF EXISTS `pdfijarh`;
CREATE TABLE IF NOT EXISTS `pdfijarh` (
  `numero` int(5) NOT NULL,
  `concepto` int(2) NOT NULL,
  `periodos` int(3) NOT NULL,
  `pagados` int(3) NOT NULL,
  `importe` decimal(12,0) NOT NULL,
  `porcentaje` decimal(8,0) NOT NULL,
  `numbene` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

DROP TABLE IF EXISTS `solicitud`;
CREATE TABLE IF NOT EXISTS `solicitud` (
  `numero` int(5) NOT NULL,
  `upres` varchar(5) NOT NULL,
  `clavecon` varchar(2) NOT NULL,
  `importe` int(9) NOT NULL,
  `observ` varchar(7) NOT NULL,
  `periodo` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `acceso`) VALUES
(1, 'prueba', '0e7881f0d44670fed326557fc047de90'),
(2, 'admin', '7a95bf926a0333f57705aeac07a362a2');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
