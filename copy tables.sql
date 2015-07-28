INSERT INTO prestamos.empleados
SELECT cveempleado, nombre, appaterno, apmaterno, idsindicato, fechaingreso, sexo, estatus, tiponomina 
FROM dbprestamos.catempleado;

INSERT INTO prestamos.pensionados
SELECT cvepensionado, cvepensionado, nombre, appaterno, apmaterno, idsindicato, fechaingreso, sexo, estatus, 
tiponomina, importepension FROM dbprestamos.catpensionado;

INSERT INTO prestamos.externos
SELECT idpersonaexterna, nombre, appaterno, apmaterno, CONCAT(calle, ' ', numero, ' ', colonia), curp, 
fechaalta, sexo, estatus FROM dbprestamos.catpersonaexterna;

INSERT INTO prestamos.solicitud
SELECT `idSolicitud`, `fecha`, `titular`, `antiguedad`, NULL, `idSindicato`, `aval1`, 
`antiguedadAval1`, NULL, `idSindicatoAval1`, `aval2`, `antiguedadAval2`, NULL, 
`idSindicatoAval2`, `importe`, `plazo`, `tasa`, `saldoAnterior`, 
`idContratoAnterior`, `importeDescuento`, `importePA`, `porcentajePA`, `importePAaval1`, `porcentajePAaval1`, 
`importePAaval2`, `porcentajePAaval2`, `fechaFirma`, `observacion`, `firma1`, `firma2`, `estatus`, `idUsuario`, 
`seguro` FROM `dbprestamos`.`solicitud`;

INSERT INTO prestamos.contrato
SELECT * FROM dbprestamos.contrato;

INSERT INTO prestamos.movimientos
SELECT * FROM dbprestamos.movimiento;

