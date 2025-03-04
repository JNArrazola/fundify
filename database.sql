-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('donante', 'creador', 'admin', 'ong') NOT NULL DEFAULT 'donante',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de campañas
CREATE TABLE campanas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    meta DECIMAL(10,2) NOT NULL,
    monto_actual DECIMAL(10,2) NOT NULL DEFAULT 0,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado ENUM('activa', 'finalizada', 'cancelada') DEFAULT 'activa',
    destacado BOOLEAN DEFAULT false,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla de donaciones
CREATE TABLE donaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_campana INT NOT NULL,
    id_usuario INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_donacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_campana) REFERENCES campanas(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla de planes (para membresías u otros servicios)
CREATE TABLE planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    costo DECIMAL(10,2) NOT NULL,
    duracion INT NOT NULL COMMENT 'Duración en meses',
    descuento DECIMAL(5,2) DEFAULT 0
);

-- Tabla de membresías (relaciona usuarios con planes)
CREATE TABLE membresias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_plan INT NOT NULL,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_fin TIMESTAMP,
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_plan) REFERENCES planes(id)
);

-- Tabla de transacciones (para registrar todos los movimientos financieros)
CREATE TABLE transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_campana INT,
    monto DECIMAL(10,2) NOT NULL,
    tipo ENUM('donacion', 'membresia', 'publicidad'),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_campana) REFERENCES campanas(id)
);

-- Tabla de publicidad
CREATE TABLE publicidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    titulo VARCHAR(255),
    descripcion TEXT,
    url VARCHAR(255),
    fecha_inicio DATE,
    fecha_fin DATE,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

ALTER TABLE campanas ADD COLUMN b_logico TINYINT(1) NOT NULL DEFAULT 1;
