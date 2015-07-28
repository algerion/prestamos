-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-07-2015 a las 22:43:06
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
  `id_registro` int(11) NOT NULL,
  `fechahora` datetime NOT NULL,
  `tabla` varchar(50) NOT NULL,
  `archivo` varchar(50) NOT NULL,
  `fechahora_archivo` datetime NOT NULL,
  `longitud_archivo` int(11) NOT NULL,
  `importe` decimal(11,2) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `estatus` int(15) NOT NULL,
  `observaciones` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catsindicatos`
--

DROP TABLE IF EXISTS `catsindicatos`;
CREATE TABLE IF NOT EXISTS `catsindicatos` (
  `cve_sindicato` int(11) NOT NULL COMMENT 'Clave del Sindicato',
  `sindicato` varchar(50) DEFAULT ' ' COMMENT 'Nombre del sindicato',
  `representante` varchar(150) DEFAULT ' ' COMMENT 'Nombre del representante del sindicato'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Catalogo de sindicatos';

--
-- Volcado de datos para la tabla `catsindicatos`
--

INSERT INTO `catsindicatos` (`cve_sindicato`, `sindicato`, `representante`) VALUES
(0, 'CONFIANZA', 'Representante'),
(1, '3 DE MARZO', 'C. HECTOR MAYORAL GUZMAN'),
(2, 'AUTONOMO', 'C. JUAN ARAGON MATIAS'),
(3, 'C.R.O.C.', 'C. EMETERIO GERONIMO SANTIAGO LOPEZ'),
(4, '12 DE SEPTIEMBRE', 'C. ANGEL ROBERTO CORTEZ RAMIREZ'),
(5, 'LIBRE', 'C. MARCELINO COACHE VERANO'),
(6, 'POLICIA', 'Representante'),
(7, 'TRANSITO', 'Representante'),
(8, 'PERSONAL OPERATIVO', 'Representante'),
(9, 'SINDICATO ADMVO', 'Representante'),
(10, '3 DE MARZO (ADMTVO.)', 'C. HECTOR MAYORAL GUZMAN'),
(11, 'OPERATIVO', 'Representante'),
(12, 'CONFIANZA', 'REPRESENTANTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contrato`
--

DROP TABLE IF EXISTS `contrato`;
CREATE TABLE IF NOT EXISTS `contrato` (
  `id_contrato` int(11) DEFAULT NULL,
  `id_solicitud` int(11) DEFAULT NULL,
  `creado` date DEFAULT NULL,
  `entrega_cheque` date DEFAULT NULL,
  `num_cheque` varchar(35) DEFAULT NULL,
  `observacion` varchar(250) DEFAULT NULL,
  `estatus` varchar(1) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `entrega_real` date DEFAULT NULL,
  `autorizado` date DEFAULT NULL,
  `congelado` int(11) DEFAULT NULL,
  `seguro` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Estructura de tabla para la tabla `descuentos_fijos`
--

DROP TABLE IF EXISTS `descuentos_fijos`;
CREATE TABLE IF NOT EXISTS `descuentos_fijos` (
  `numero` int(5) NOT NULL,
  `concepto` int(2) NOT NULL,
  `periodos` int(3) NOT NULL,
  `pagados` int(3) NOT NULL,
  `importe` decimal(11,2) NOT NULL,
  `porcentaje` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuento_detalle`
--

DROP TABLE IF EXISTS `descuento_detalle`;
CREATE TABLE IF NOT EXISTS `descuento_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_descuento` int(11) NOT NULL,
  `num_empleado` int(11) NOT NULL,
  `clavecon` int(11) NOT NULL,
  `importe` decimal(11,2) NOT NULL,
  `periodo` int(11) NOT NULL,
  `periodos` int(11) NOT NULL,
  `contrato` int(11) NOT NULL,
  `tipo_nomina` varchar(1) NOT NULL,
  `nomina` int(11) NOT NULL,
  `aplicado` int(11) NOT NULL,
  `aval1` int(11) NOT NULL,
  `aval2` int(11) NOT NULL,
  `nota` int(11) NOT NULL,
  `aplicaravales` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

DROP TABLE IF EXISTS `empleados`;
CREATE TABLE IF NOT EXISTS `empleados` (
  `numero` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `paterno` varchar(50) NOT NULL,
  `materno` varchar(50) NOT NULL,
  `sindicato` int(11) NOT NULL,
  `fec_ingre` date NOT NULL,
  `sexo` varchar(1) DEFAULT NULL,
  `status` varchar(1) NOT NULL,
  `tipo_nomi` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

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
-- Estructura de tabla para la tabla `estatus_empleado`
--

DROP TABLE IF EXISTS `estatus_empleado`;
CREATE TABLE IF NOT EXISTS `estatus_empleado` (
  `id_estatus_empl` int(11) NOT NULL,
  `estatus` varchar(50) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `estatus_empleado`
--

INSERT INTO `estatus_empleado` (`id_estatus_empl`, `estatus`) VALUES
(1, 'BAJA TEMPORAL'),
(2, 'ACTIVO'),
(3, 'BAJA DEFINITIVA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `externos`
--

DROP TABLE IF EXISTS `externos`;
CREATE TABLE IF NOT EXISTS `externos` (
  `numero` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `paterno` varchar(50) DEFAULT NULL,
  `materno` varchar(50) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `curp` varchar(18) DEFAULT NULL,
  `fec_ingre` datetime DEFAULT NULL,
  `sexo` varchar(1) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

DROP TABLE IF EXISTS `movimientos`;
CREATE TABLE IF NOT EXISTS `movimientos` (
  `id_movimiento` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `creacion` datetime NOT NULL,
  `id_tipo_movto` int(11) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `cargo` decimal(11,2) NOT NULL,
  `abono` decimal(11,2) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `aplicacion` datetime DEFAULT NULL,
  `id_descuento` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
('ftp_server', '192.168.0.5'),
('ftp_user', 'nomina');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pensionados`
--

DROP TABLE IF EXISTS `pensionados`;
CREATE TABLE IF NOT EXISTS `pensionados` (
  `numero` int(11) NOT NULL,
  `num_empleado` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `paterno` varchar(50) NOT NULL,
  `materno` varchar(50) NOT NULL,
  `sindicato` int(11) NOT NULL,
  `fec_ingre` datetime NOT NULL,
  `sexo` varchar(1) NOT NULL,
  `status` varchar(1) NOT NULL,
  `tipo_nomi` varchar(1) NOT NULL,
  `importe_pension` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

DROP TABLE IF EXISTS `solicitud`;
CREATE TABLE IF NOT EXISTS `solicitud` (
  `id_solicitud` int(11) NOT NULL,
  `creada` datetime DEFAULT NULL,
  `titular` int(11) NOT NULL,
  `antiguedad` decimal(11,2) NOT NULL,
  `tipo_empleado` varchar(1) DEFAULT NULL,
  `cve_sindicato` int(11) NOT NULL,
  `aval1` int(11) DEFAULT NULL,
  `antig_aval1` decimal(11,2) DEFAULT NULL,
  `tipo_aval1` varchar(1) DEFAULT NULL,
  `cve_sind_aval1` int(11) DEFAULT NULL,
  `aval2` int(11) DEFAULT NULL,
  `antig_aval2` decimal(11,2) DEFAULT NULL,
  `tipo_aval2` varchar(1) DEFAULT NULL,
  `cve_sind_aval2` int(11) DEFAULT NULL,
  `importe` decimal(11,2) NOT NULL,
  `plazo` int(11) DEFAULT NULL,
  `tasa` decimal(11,2) DEFAULT NULL,
  `saldo_anterior` decimal(11,2) DEFAULT NULL,
  `id_contrato_ant` int(11) DEFAULT NULL,
  `descuento` decimal(11,2) DEFAULT NULL,
  `importe_pa_tit` decimal(11,2) DEFAULT NULL,
  `porcentaje_pa_tit` decimal(11,2) DEFAULT NULL,
  `importe_pa_aval1` decimal(11,2) DEFAULT NULL,
  `porcentaje_pa_aval1` decimal(11,2) DEFAULT NULL,
  `importe_pa_aval2` decimal(11,2) DEFAULT NULL,
  `porcentaje_pa_aval2` decimal(11,2) DEFAULT NULL,
  `firma` datetime DEFAULT NULL,
  `observacion` varchar(250) NOT NULL,
  `firma1` varchar(75) DEFAULT NULL,
  `firma2` varchar(75) DEFAULT NULL,
  `estatus` varchar(1) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `seguro` decimal(11,2) DEFAULT NULL
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
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_registro`);

--
-- Indices de la tabla `catsindicatos`
--
ALTER TABLE `catsindicatos`
  ADD PRIMARY KEY (`cve_sindicato`), ADD UNIQUE KEY `cve_sindicato` (`cve_sindicato`);

--
-- Indices de la tabla `descuento`
--
ALTER TABLE `descuento`
  ADD PRIMARY KEY (`id_descuento`);

--
-- Indices de la tabla `descuentos_fijos`
--
ALTER TABLE `descuentos_fijos`
  ADD PRIMARY KEY (`numero`);

--
-- Indices de la tabla `descuento_detalle`
--
ALTER TABLE `descuento_detalle`
  ADD PRIMARY KEY (`id_detalle`);

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
-- Indices de la tabla `estatus_empleado`
--
ALTER TABLE `estatus_empleado`
  ADD PRIMARY KEY (`id_estatus_empl`);

--
-- Indices de la tabla `externos`
--
ALTER TABLE `externos`
  ADD PRIMARY KEY (`numero`);

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
-- Indices de la tabla `pensionados`
--
ALTER TABLE `pensionados`
  ADD PRIMARY KEY (`numero`);

--
-- Indices de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD PRIMARY KEY (`id_solicitud`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `descuento`
--
ALTER TABLE `descuento`
  MODIFY `id_descuento` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `descuento_detalle`
--
ALTER TABLE `descuento_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `estatus`
--
ALTER TABLE `estatus`
  MODIFY `id_estatus` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `estatus_empleado`
--
ALTER TABLE `estatus_empleado`
  MODIFY `id_estatus_empl` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `externos`
--
ALTER TABLE `externos`
  MODIFY `numero` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(32) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
