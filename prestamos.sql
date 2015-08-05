/*
SQLyog Community v10.1 
MySQL - 5.5.34 : Database - prestamos
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`prestamos` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `prestamos`;

/*Table structure for table `bitacora` */

DROP TABLE IF EXISTS `bitacora`;

CREATE TABLE `bitacora` (
  `id_registro` int(11) NOT NULL AUTO_INCREMENT,
  `fechahora` datetime NOT NULL,
  `tabla` varchar(50) NOT NULL,
  `archivo` varchar(50) NOT NULL,
  `fechahora_archivo` datetime NOT NULL,
  `longitud_archivo` int(11) NOT NULL,
  `importe` decimal(11,2) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `estatus` int(15) NOT NULL,
  `observaciones` varchar(500) NOT NULL,
  PRIMARY KEY (`id_registro`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `bitacora` */

/*Table structure for table `catsindicatos` */

DROP TABLE IF EXISTS `catsindicatos`;

CREATE TABLE `catsindicatos` (
  `cve_sindicato` int(11) NOT NULL COMMENT 'Clave del Sindicato',
  `sindicato` varchar(50) DEFAULT ' ' COMMENT 'Nombre del sindicato',
  `representante` varchar(150) DEFAULT ' ' COMMENT 'Nombre del representante del sindicato',
  PRIMARY KEY (`cve_sindicato`),
  UNIQUE KEY `cve_sindicato` (`cve_sindicato`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Catalogo de sindicatos';

/*Data for the table `catsindicatos` */

insert  into `catsindicatos`(`cve_sindicato`,`sindicato`,`representante`) values (1,'CONFIANZA','Representante'),(2,'3 DE MARZO','C. HECTOR MAYORAL GUZMAN'),(3,'AUTONOMO','C. JUAN ARAGON MATIAS'),(4,'C.R.O.C.','C. EMETERIO GERONIMO SANTIAGO LOPEZ'),(5,'12 DE SEPTIEMBRE','C. ANGEL ROBERTO CORTEZ RAMIREZ'),(6,'LIBRE','C. MARCELINO COACHE VERANO'),(7,'POLICIA','Representante'),(8,'TRANSITO','Representante'),(9,'PERSONAL OPERATIVO','Representante'),(10,'SINDICATO ADMVO','Representante'),(11,'3 DE MARZO (ADMTVO.)','C. HECTOR MAYORAL GUZMAN'),(12,'OPERATIVO','Representante'),(13,'CONFIANZA','REPRESENTANTE');

/*Table structure for table `contrato` */

DROP TABLE IF EXISTS `contrato`;

CREATE TABLE `contrato` (
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

/*Data for the table `contrato` */

/*Table structure for table `descuento` */

DROP TABLE IF EXISTS `descuento`;

CREATE TABLE `descuento` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `descuento` */

/*Table structure for table `descuento_detalle` */

DROP TABLE IF EXISTS `descuento_detalle`;

CREATE TABLE `descuento_detalle` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
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
  `aplicaravales` int(11) NOT NULL,
  PRIMARY KEY (`id_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `descuento_detalle` */

/*Table structure for table `descuentos_fijos` */

DROP TABLE IF EXISTS `descuentos_fijos`;

CREATE TABLE `descuentos_fijos` (
  `numero` int(5) NOT NULL,
  `concepto` int(2) NOT NULL,
  `periodos` int(3) NOT NULL,
  `pagados` int(3) NOT NULL,
  `importe` decimal(11,2) NOT NULL,
  `porcentaje` decimal(11,2) NOT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `descuentos_fijos` */

/*Table structure for table `empleados` */

DROP TABLE IF EXISTS `empleados`;

CREATE TABLE `empleados` (
  `numero` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `paterno` varchar(50) NOT NULL,
  `materno` varchar(50) NOT NULL,
  `sindicato` int(11) NOT NULL,
  `fec_ingre` date NOT NULL,
  `sexo` varchar(1) DEFAULT NULL,
  `status` varchar(1) NOT NULL,
  `tipo_nomi` varchar(1) NOT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `empleados` */

/*Table structure for table `estatus` */

DROP TABLE IF EXISTS `estatus`;

CREATE TABLE `estatus` (
  `id_estatus` int(11) NOT NULL AUTO_INCREMENT,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id_estatus`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `estatus` */

insert  into `estatus`(`id_estatus`,`estatus`) values (-1,'error'),(1,'generado'),(2,'enviado'),(3,'recibido'),(4,'aplicado');

/*Table structure for table `estatus_empleado` */

DROP TABLE IF EXISTS `estatus_empleado`;

CREATE TABLE `estatus_empleado` (
  `id_estatus_empl` int(11) NOT NULL AUTO_INCREMENT,
  `estatus` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_estatus_empl`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `estatus_empleado` */

insert  into `estatus_empleado`(`id_estatus_empl`,`estatus`) values (1,'BAJA TEMPORAL'),(2,'ACTIVO'),(3,'BAJA DEFINITIVA');

/*Table structure for table `estatus_prestamo` */

DROP TABLE IF EXISTS `estatus_prestamo`;

CREATE TABLE `estatus_prestamo` (
  `id_estatus_p` varchar(1) NOT NULL,
  `estatus_p` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_estatus_p`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `estatus_prestamo` */

insert  into `estatus_prestamo`(`id_estatus_p`,`estatus_p`) values ('A','Autorizado'),('C','Cancelado'),('S','Solicitado');

/*Table structure for table `externos` */

DROP TABLE IF EXISTS `externos`;

CREATE TABLE `externos` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `paterno` varchar(50) DEFAULT NULL,
  `materno` varchar(50) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `curp` varchar(18) DEFAULT NULL,
  `fec_ingre` datetime DEFAULT NULL,
  `sexo` varchar(1) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `externos` */

/*Table structure for table `movimientos` */

DROP TABLE IF EXISTS `movimientos`;

CREATE TABLE `movimientos` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) NOT NULL,
  `creacion` datetime NOT NULL,
  `id_tipo_movto` int(11) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `cargo` decimal(11,2) NOT NULL,
  `abono` decimal(11,2) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `aplicacion` datetime DEFAULT NULL,
  `id_descuento` int(11) DEFAULT NULL,
  `activo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_movimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `movimientos` */

/*Table structure for table `parametros` */

DROP TABLE IF EXISTS `parametros`;

CREATE TABLE `parametros` (
  `llave` varchar(20) NOT NULL,
  `valor` varchar(250) NOT NULL,
  PRIMARY KEY (`llave`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametros` */

insert  into `parametros`(`llave`,`valor`) values ('ftp_pass','nomin4'),('ftp_server','192.168.0.5'),('ftp_user','nomina');

/*Table structure for table `pensionados` */

DROP TABLE IF EXISTS `pensionados`;

CREATE TABLE `pensionados` (
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
  `importe_pension` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `pensionados` */

/*Table structure for table `solicitud` */

DROP TABLE IF EXISTS `solicitud`;

CREATE TABLE `solicitud` (
  `id_solicitud` int(11) NOT NULL AUTO_INCREMENT,
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
  `seguro` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id_solicitud`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `solicitud` */

/*Table structure for table `tipo_empleado` */

DROP TABLE IF EXISTS `tipo_empleado`;

CREATE TABLE `tipo_empleado` (
  `tipo_empleado` varchar(1) NOT NULL,
  `texto` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`tipo_empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `tipo_empleado` */

insert  into `tipo_empleado`(`tipo_empleado`,`texto`) values ('A','ACTIVO'),('C','CONFIANZA'),('E','EXTERNO'),('J','JUBILADO'),('S','SINDICALIZADO');

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id_usuario` int(32) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(45) NOT NULL,
  `acceso` varchar(32) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id_usuario`,`usuario`,`acceso`) values (1,'prueba','0e7881f0d44670fed326557fc047de90'),(2,'admin','7a95bf926a0333f57705aeac07a362a2');

/*Table structure for table `sujetos` */

DROP TABLE IF EXISTS `sujetos`;

/*!50001 DROP VIEW IF EXISTS `sujetos` */;
/*!50001 DROP TABLE IF EXISTS `sujetos` */;

/*!50001 CREATE TABLE  `sujetos`(
 `numero` int(11) ,
 `nombre` varchar(152) ,
 `fec_ingre` datetime ,
 `sindicato` bigint(20) ,
 `tipo` varchar(1) ,
 `status` varchar(1) 
)*/;

/*View structure for view sujetos */

/*!50001 DROP TABLE IF EXISTS `sujetos` */;
/*!50001 DROP VIEW IF EXISTS `sujetos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sujetos` AS select `empleados`.`numero` AS `numero`,concat(`empleados`.`nombre`,' ',`empleados`.`paterno`,' ',`empleados`.`materno`) AS `nombre`,`empleados`.`fec_ingre` AS `fec_ingre`,`empleados`.`sindicato` AS `sindicato`,'A' AS `tipo`,`empleados`.`status` AS `status` from `empleados` union select `pensionados`.`numero` AS `numero`,concat(`pensionados`.`nombre`,' ',`pensionados`.`paterno`,' ',`pensionados`.`materno`) AS `nombre`,`pensionados`.`fec_ingre` AS `fec_ingre`,`pensionados`.`sindicato` AS `sindicato`,'J' AS `tipo`,`pensionados`.`status` AS `status` from `pensionados` union select `externos`.`numero` AS `numero`,concat(`externos`.`nombre`,' ',`externos`.`paterno`,' ',`externos`.`materno`) AS `nombre`,`externos`.`fec_ingre` AS `fec_ingre`,0 AS `0`,'E' AS `tipo`,`externos`.`status` AS `status` from `externos` */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
