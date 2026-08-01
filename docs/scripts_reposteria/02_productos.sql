CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255)
);

insert into categorias (nombre, descripcion) values
('Pasteles', 'Deliciosos pasteles para cualquier ocasión'),
('Galletas', 'Galletas frescas y crujientes'),
('Cupcakes', 'Cupcakes decorados y sabrosos'),
('Tartas', 'Tartas de frutas y chocolate'),
('Panadería', 'Pan fresco y artesanal');
use reposteria;

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    id_categoria INT NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255),
    estado ENUM('Disponible','Agotado') DEFAULT 'Disponible',

    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);