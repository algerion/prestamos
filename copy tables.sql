INSERT INTO prestamos.empleados
SELECT cveempleado, nombre, appaterno, apmaterno, idsindicato, fechaingreso, sexo, estatus, tiponomina FROM dbprestamos.catempleado;

INSERT INTO prestamos.pensionados
SELECT cvepensionado, cvepensionado, nombre, appaterno, apmaterno, idsindicato, fechaingreso, sexo, estatus, tiponomina, importepension FROM dbprestamos.catpensionado;

INSERT INTO prestamos.externos
SELECT idpersonaexterna, nombre, appaterno, apmaterno, CONCAT(calle, ' ', numero, ' ', colonia), curp, fechaalta, sexo, estatus FROM dbprestamos.catpersonaexterna;
