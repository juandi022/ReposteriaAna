CREATE TABLE `nw202101`.`categorias` (
  `catid` BIGINT(8) NOT NULL AUTO_INCREMENT,
  `catnom` VARCHAR(45) NULL,
  `catest` CHAR(3) NULL DEFAULT 'ACT',
  PRIMARY KEY (`catid`));

INSERT INTO categorias (catnom, catest)
VALUES
('Pasteles', 'ACT'),
('Cupcakes', 'ACT'),
('Galletas', 'ACT'),
('Donas', 'ACT'),
('Panes', 'ACT'),
('Tartas', 'ACT'),
('Cheesecakes', 'ACT'),
('Brownies', 'ACT'),
('Postres', 'ACT'),
('Bebidas', 'ACT');