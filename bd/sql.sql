-- Crear base de datos
DROP DATABASE IF EXISTS parquimetros;
CREATE DATABASE IF NOT EXISTS parquimetros;
USE parquimetros;

-- Tabla usuarios
CREATE TABLE usuarios (
    rut VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido1 VARCHAR(50) NOT NULL,
    apellido2 VARCHAR(50) NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'operador') NOT NULL,
    numero INT,
    estado BOOLEAN DEFAULT TRUE,
    latitud DECIMAL(10, 8),
    longitud DECIMAL(11, 8),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla estacionamientos para registrar entradas y salidas
CREATE TABLE estacionamientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patente VARCHAR(7) NOT NULL,
    operador_rut VARCHAR(20) NOT NULL,  
    hora_ingreso DATETIME NOT NULL,
    hora_salida DATETIME,
    duracion INT,
    cobro INT,
    FOREIGN KEY (operador_rut) REFERENCES usuarios(rut)
);

-- Trigger para convertir la patente a mayúsculas antes de la inserción
DELIMITER //
CREATE TRIGGER before_insert_patente
BEFORE INSERT ON estacionamientos
FOR EACH ROW
BEGIN
    SET NEW.patente = UPPER(NEW.patente);
END;
//
DELIMITER ;

-- Insertar algunos usuarios de prueba
INSERT INTO usuarios (rut, nombre, apellido1, apellido2, contraseña, rol, latitud, longitud, numero) VALUES
('12345678-9', 'Mauricio', 'Olivos', 'Salazar', SHA2('1234', 256), 'administrador', -34.98571600, -71.23682000, 12345678),
('12121212-K', 'admin', 'Del', 'Sistema', SHA2('admin', 256), 'administrador', -34.98571600, -71.23682000, 12345678),
('98765432-1', 'Juan', 'Pérez', 'González', SHA2('123456', 256), 'operador', -34.98600000, -71.23700000, 98765432),
('12398798-2', 'Ana', 'Morales', 'Rojas', SHA2('123456', 256), 'operador', -34.98700000, -71.23800000, 12398798),
('32112312-3', 'Luis', 'Martínez', 'Soto', SHA2('123456', 256), 'operador', -34.98800000, -71.23900000, 32112312);
