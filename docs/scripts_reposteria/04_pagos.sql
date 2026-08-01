CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    usercod BIGINT(10) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    estado ENUM('Pendiente','Pagado','Cancelado') DEFAULT 'Pendiente',
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    codigo_transaccion VARCHAR(100),

    CONSTRAINT fk_pago_usuario
        FOREIGN KEY (usercod)
        REFERENCES usuario(usercod)
);