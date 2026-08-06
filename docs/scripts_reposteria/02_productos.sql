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

INSERT INTO productos
(nombre, id_categoria, descripcion, precio, stock, imagen, estado)
VALUES
('Pastel de Chocolate', 1, 'Pastel de chocolate con cobertura de ganache.', 650.00, 10, 'pastel_chocolate.jpg', 'Disponible'),

('Cheesecake de Fresa', 1, 'Cheesecake cremoso con salsa de fresa natural.', 720.00, 8, 'cheesecake_fresa.jpg', 'Disponible'),

('Cupcake de Vainilla', 2, 'Cupcake de vainilla con frosting de mantequilla.', 45.00, 50, 'cupcake_vainilla.jpg', 'Disponible'),

('Cupcake Red Velvet', 2, 'Cupcake Red Velvet con queso crema.', 55.00, 40, 'cupcake_redvelvet.jpg', 'Disponible'),

('Galletas con Chispas de Chocolate', 3, 'Paquete de 12 galletas artesanales.', 180.00, 25, 'galletas_chocolate.jpg', 'Disponible'),

('Brownie Clásico', 3, 'Brownie de chocolate con nueces.', 70.00, 30, 'brownie.jpg', 'Disponible'),

('Dona Glaseada', 4, 'Dona esponjosa con glaseado de azúcar.', 35.00, 60, 'dona_glaseada.jpg', 'Disponible'),

('Dona de Chocolate', 4, 'Dona cubierta de chocolate.', 40.00, 45, 'dona_chocolate.jpg', 'Disponible'),

('Macarons Surtidos', 5, 'Caja de 6 macarons de diferentes sabores.', 250.00, 15, 'macarons.jpg', 'Disponible'),

('Tarta de Limón', 1, 'Tarta de limón con merengue italiano.', 580.00, 0, 'tarta_limon.jpg', 'Agotado');
