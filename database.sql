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
ALTER TABLE usuarios ADD COLUMN b_logico TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE usuarios ADD puntos INT DEFAULT 0;

CREATE TABLE recompensas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    puntos_requeridos INT NOT NULL,
    imagen VARCHAR(255),
    stock INT DEFAULT 1
);

INSERT INTO recompensas (nombre, descripcion, puntos_requeridos, imagen, stock) VALUES
('Tarjeta Spotify 3 Meses', 'Accede a Spotify Premium por 3 meses.', 1500, 'img/spotify.png', 10),
('Tarjeta Amazon $200MXN', 'Crédito en Amazon México.', 3000, 'img/amazon.png', 5),
('Audífonos Bluetooth', 'Audífonos inalámbricos.', 5000, 'img/audifonos.png', 3),
('Teclado Mecánico Básico', 'Teclado mecánico retroiluminado.', 6500, 'img/teclado.png', 2),
('Tarjeta Netflix 1 Mes', 'Acceso a Netflix durante un mes.', 1200, 'img/netflix.png', 8),
('Tarjeta Apple $300MXN', 'Crédito en iTunes/App Store.', 3500, 'img/apple.png', 4),
('Celular Económico', 'Smartphone básico Android.', 12000, 'img/celular.png', 1);

CREATE TABLE canjes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_recompensa INT NOT NULL,
    fecha DATETIME NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_recompensa) REFERENCES recompensas(id)
);

DROP TABLE canjes;

CREATE TABLE canjes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_recompensa INT NOT NULL,
    direccion VARCHAR(255),
    nombre_completo VARCHAR(100),
    telefono VARCHAR(20),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE usuarios 
  ADD COLUMN imagen VARCHAR(255) DEFAULT NULL,
  ADD COLUMN documento_validacion VARCHAR(255) DEFAULT NULL,
  ADD COLUMN metodo_pago TEXT DEFAULT NULL,
  ADD COLUMN verificada TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN contacto VARCHAR(100) DEFAULT NULL,
  ADD COLUMN direccion VARCHAR(255) DEFAULT NULL;

ALTER TABLE recompensas
ADD COLUMN tipo_entrega ENUM('virtual', 'fisico') NOT NULL DEFAULT 'fisico';

DELETE FROM recompensas;

INSERT INTO recompensas (nombre, descripcion, puntos_requeridos, imagen, stock, tipo_entrega) VALUES
('Tarjeta Spotify 3 Meses', 'Accede a Spotify Premium por 3 meses.', 1500, 'img/spotify.png', 10, "virtual"),
('Tarjeta Amazon $200MXN', 'Crédito en Amazon México.', 3000, 'img/amazon.png', 5, "virtual"),
('Audífonos Bluetooth', 'Audífonos inalámbricos.', 5000, 'img/audifonos.png', 3, "fisico"),
('Teclado Mecánico', 'Teclado mecánico retroiluminado.', 6500, 'img/teclado.png', 2, "fisico"),
('Tarjeta Netflix 1 Mes', 'Acceso a Netflix durante un mes.', 1200, 'img/netflix.png', 8, "virtual"),
('Tarjeta Apple $300MXN', 'Crédito en iTunes/App Store.', 3500, 'img/apple.png', 4, "virtual"),
('iPhone 15', 'iPhone 15 de la marca Apple.', 12000, 'img/celular.png', 1, "fisico");

