CREATE TABLE `productos` (
  `invPrdId` bigint(13) NOT NULL AUTO_INCREMENT,
  `invPrdBrCod` varchar(128) DEFAULT NULL,
  `invPrdCodInt` varchar(128) DEFAULT NULL,
  `invPrdDsc` varchar(128) DEFAULT NULL,
  `invPrdTip` char(3) DEFAULT NULL,
  `invPrdEst` char(3) DEFAULT NULL,
  `invPrdPadre` bigint(13) DEFAULT NULL,
  `invPrdFactor` int(11) DEFAULT NULL,
  `invPrdVnd` char(3) DEFAULT NULL,
  PRIMARY KEY (`invPrdId`),
  UNIQUE KEY `invPrdBrCod_UNIQUE` (`invPrdBrCod`),
  UNIQUE KEY `invPrdCodIng_UNIQUE` (`invPrdCodInt`)
) ENGINE=InnoDB;

INSERT INTO productos
(invPrdBrCod, invPrdCodInt, invPrdDsc, invPrdTip, invPrdEst, invPrdPadre, invPrdFactor, invPrdVnd)
VALUES
('770100000001', 'PRD001', 'Pastel de Chocolate', 'PRD', 'ACT', NULL, 1, 'SÍ'),
('770100000002', 'PRD002', 'Pastel de Vainilla', 'PRD', 'ACT', NULL, 1, 'SÍ'),
('770100000003', 'PRD003', 'Cheesecake de Fresa', 'PRD', 'ACT', NULL, 1, 'SÍ'),
('770100000004', 'PRD004', 'Tres Leches', 'PRD', 'ACT', NULL, 1, 'SÍ'),
('770100000005', 'PRD005', 'Cupcake de Chocolate', 'PRD', 'ACT', NULL, 1, 'SÍ'),
('770100000006', 'PRD006', 'Cupcake de Vainilla', 'PRD', 'ACT', NULL, 1, 'SÍ'),
('770100000007', 'PRD007', 'Galletas con Chispas de Chocolate', 'PRD', 'ACT', NULL, 1, 'SÍ'),
('770100000008', 'PRD008', 'Brownie', 'PRD', 'ACT', NULL, 1, 'SÍ'),
('770100000009', 'PRD009', 'Dona Glaseada', 'PRD', 'ACT', NULL, 1, 'SÍ');
/*
`invPrdId` bigint(13) NOT NULL AUTO_INCREMENT, Codigo autonumerico
  `invPrdBrCod` varchar(128) DEFAULT NULL,  Codigo de Barras
  `invPrdCodInt` varchar(128) DEFAULT NULL, Codigo interno institucional
  `invPrdDsc` varchar(128) DEFAULT NULL, Nombre
  `invPrdTip` char(3) DEFAULT NULL, Tipo de Producto
  `invPrdEst` char(3) DEFAULT NULL, Estado del Producto
  `invPrdPadre` bigint(13) DEFAULT NULL,  Codigo invPrdID del padre
  `invPrdFactor` int(11) DEFAULT NULL,
  `invPrdVnd` char(3) DEFAULT NULL,

  Caja de 24 Cajas de 100 Unds  1 null 0  NO   1   FRACCION 24 2
  Caja de 100 Unds 2    1  24             SI   24 0  24 23  1 FRACCION 100
  Unidad           3    2  100            SI   2400  100  99


*/
