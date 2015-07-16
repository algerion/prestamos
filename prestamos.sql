-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-07-2015 a las 22:41:37
-- Versión del servidor: 5.6.24
-- Versión de PHP: 5.6.8

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
-- Estructura de tabla para la tabla `bitacora`
--

DROP TABLE IF EXISTS `bitacora`;
CREATE TABLE IF NOT EXISTS `bitacora` (
  `id_regisrto` int(11) NOT NULL,
  `fechahora` date NOT NULL,
  `tabla` varchar(15) NOT NULL,
  `archivo` varchar(15) NOT NULL,
  `fechahora_archivo` date NOT NULL,
  `longitud_archivo` int(1) NOT NULL,
  `importe` varchar(1) NOT NULL,
  `id_usuario` int(1) NOT NULL,
  `estatus` varchar(15) NOT NULL,
  `observaciones` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catsindicato`
--

DROP TABLE IF EXISTS `catsindicatos`;
CREATE TABLE IF NOT EXISTS `catsindicatos` (
  `cve_sindicato` varchar(5) DEFAULT '0' COMMENT 'Clave del Sindicato',
  `sindicato` varchar(50) DEFAULT ' ' COMMENT 'Nombre del sindicato',
  `representante` varchar(150) DEFAULT ' ' COMMENT 'Nombre del representante del sindicato'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Catalogo de sindicatos';

--
-- Volcado de datos para la tabla `catsindicato`
--

INSERT INTO `catsindicatos` (`sindicato`, `cve_sindicato`, `representante`) VALUES
('CONFIANZA', '0', 'Representante'),
('3 DE MARZO', '1', 'C. HECTOR MAYORAL GUZMAN'),
('AUTONOMO', '2', 'C. JUAN ARAGON MATIAS'),
('C.R.O.C.', '3', 'C. EMETERIO GERONIMO SANTIAGO LOPEZ'),
('12 DE SEPTIEMBRE', '4', 'C. ANGEL ROBERTO CORTEZ RAMIREZ'),
('LIBRE', '5', 'C. MARCELINO COACHE VERANO'),
('POLICIA', '6', 'Representante'),
('TRANSITO', '7', 'Representante'),
('PERSONAL OPERATIVO', '8', 'Representante'),
('SINDICATO ADMVO', '9', 'Representante'),
('3 DE MARZO (ADMTVO.)', '10', 'C. HECTOR MAYORAL GUZMAN'),
('OPERATIVO', '11', 'Representante'),
('CONFIANZA', '12', 'REPRESENTANTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuento`
--

DROP TABLE IF EXISTS `descuento`;
CREATE TABLE IF NOT EXISTS `descuento` (
  `id_descuento` int(11) NOT NULL,
  `origen` varchar(1) NOT NULL,
  `creado` datetime NOT NULL,
  `modificado` datetime NOT NULL,
  `creador` int(11) NOT NULL,
  `modificador` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL,
  `observaciones` text NOT NULL,
  `tipo` varchar(1) NOT NULL,
  `pago` varchar(1) NOT NULL,
  `periodo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `tipo_nomi` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Estructura de tabla para la tabla `estatus`
--

DROP TABLE IF EXISTS `estatus`;
CREATE TABLE IF NOT EXISTS `estatus` (
  `id_estatus` int(11) NOT NULL,
  `estatus` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `estatus`
--

INSERT INTO `estatus` (`id_estatus`, `estatus`) VALUES
(-1, 'error'),
(1, 'generado'),
(2, 'enviado'),
(3, 'recibido'),
(4, 'aplicado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

DROP TABLE IF EXISTS `movimientos`;
CREATE TABLE IF NOT EXISTS `movimientos` (
  `id_movimiento` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `num_movto` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `movimiento` varchar(100) NOT NULL,
  `justificacion` varchar(500) NOT NULL,
  `cargo` decimal(12,2) NOT NULL,
  `abono` decimal(12,2) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

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
  `valor` varchar(250) NOT NULL
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
-- Estructura de tabla para la tabla `pensionados`
--

DROP TABLE IF EXISTS `pensionados`;
CREATE TABLE IF NOT EXISTS `pensionados` (
  `número` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `paterno` varchar(15) NOT NULL,
  `materno` varchar(15) NOT NULL,
  `sindicato` varchar(2) NOT NULL,
  `fecha_ingre` date NOT NULL,
  `estatus` varchar(1) NOT NULL,
  `tipo_nomi` int(1) NOT NULL
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
  `id_usuario` int(32) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `acceso` varchar(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `acceso`) VALUES
(1, 'prueba', '0e7881f0d44670fed326557fc047de90'),
(2, 'admin', '7a95bf926a0333f57705aeac07a362a2');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `catsindicato`
--
ALTER TABLE `catsindicatos`
  ADD PRIMARY KEY (`cve_sindicato`), ADD UNIQUE KEY `cve_sindicato` (`cve_sindicato`);

--
-- Indices de la tabla `descuento`
--
ALTER TABLE `descuento`
  ADD PRIMARY KEY (`id_descuento`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`numero`);

--
-- Indices de la tabla `estatus`
--
ALTER TABLE `estatus`
  ADD PRIMARY KEY (`id_estatus`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id_movimiento`);

--
-- Indices de la tabla `parametros`
--
ALTER TABLE `parametros`
  ADD PRIMARY KEY (`llave`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
--
-- AUTO_INCREMENT de la tabla `descuento`
--
ALTER TABLE `descuento`
  MODIFY `id_descuento` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `estatus`
--
ALTER TABLE `estatus`
  MODIFY `id_estatus` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(32) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
