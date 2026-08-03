<?php

namespace Dao\Compras;

use Dao\Table;

class Compras extends Table
{
    public static function getCompras(
        string $partialFactura = "",
        string $estado = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ): array {
        $sqlstr = "SELECT c.id_compra, c.id_proveedor, p.nombre AS proveedor,
            c.fecha, c.numero_factura, c.subtotal, c.impuesto, c.total, c.estado
            FROM compras c
            INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor";
        $sqlstrCount = "SELECT COUNT(*) AS count
            FROM compras c
            INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor";
        $conditions = [];
        $params = [];

        if ($partialFactura !== "") {
            $conditions[] = "c.numero_factura LIKE :partialFactura";
            $params["partialFactura"] = "%" . $partialFactura . "%";
        }
        if ($estado !== "") {
            $conditions[] = "c.estado = :estado";
            $params["estado"] = $estado;
        }
        if (count($conditions) > 0) {
            $where = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $where;
            $sqlstrCount .= $where;
        }

        if (!in_array($orderBy, ["id_compra", "fecha", "numero_factura", "total", "estado", ""])) {
            throw new \Exception("El criterio de ordenamiento no es válido");
        }
        if ($orderBy !== "") {
            $sqlstr .= " ORDER BY c." . $orderBy;
            if ($orderDescending) {
                $sqlstr .= " DESC";
            }
        } else {
            $sqlstr .= " ORDER BY c.id_compra DESC";
        }

        $countRow = self::obtenerUnRegistro($sqlstrCount, $params);
        $total = intval($countRow["count"] ?? 0);
        $pagesCount = $itemsPerPage > 0 ? intval(ceil($total / $itemsPerPage)) : 1;
        if ($pagesCount > 0 && $page > $pagesCount - 1) {
            $page = $pagesCount - 1;
        }
        if ($page < 0) {
            $page = 0;
        }

        $offset = $page * $itemsPerPage;
        $sqlstr .= " LIMIT " . $offset . ", " . $itemsPerPage;

        return [
            "compras" => self::obtenerRegistros($sqlstr, $params),
            "total" => $total,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getById(int $id): array
    {
        $sqlstr = "SELECT c.*, p.nombre AS proveedor
            FROM compras c
            INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor
            WHERE c.id_compra = :id_compra;";
        $registro = self::obtenerUnRegistro($sqlstr, ["id_compra" => $id]);

        return is_array($registro) ? $registro : [];
    }

    public static function getDetalles(int $idCompra): array
    {
        $sqlstr = "SELECT d.id_detalle_compra, d.id_compra, d.id_producto,
            p.nombre AS producto, d.cantidad, d.costo_unitario, d.subtotal
            FROM detalle_compra d
            INNER JOIN productos p ON d.id_producto = p.id_producto
            WHERE d.id_compra = :id_compra
            ORDER BY d.id_detalle_compra ASC;";

        return self::obtenerRegistros($sqlstr, ["id_compra" => $idCompra]);
    }

    public static function getProductos(): array
    {
        $sqlstr = "SELECT id_producto, nombre, stock
            FROM productos
            ORDER BY nombre ASC;";

        return self::obtenerRegistros($sqlstr, []);
    }

    public static function create(
        int $idProveedor,
        string $numeroFactura,
        string $fecha
    ): int {
        $conn = \Dao\Dao::getConn();
        $sqlIns = "INSERT INTO compras
            (id_proveedor, fecha, numero_factura, subtotal, impuesto, total, estado)
            VALUES
            (:id_proveedor, :fecha, :numero_factura, 0, 0, 0, 'Borrador');";
        $query = $conn->prepare($sqlIns);
        $query->execute([
            "id_proveedor" => $idProveedor,
            "fecha" => $fecha,
            "numero_factura" => $numeroFactura
        ]);

        return intval($conn->lastInsertId());
    }

    public static function update(
        int $idCompra,
        int $idProveedor,
        string $numeroFactura,
        string $fecha
    ): bool {
        $sqlUpd = "UPDATE compras SET
            id_proveedor = :id_proveedor,
            numero_factura = :numero_factura,
            fecha = :fecha
            WHERE id_compra = :id_compra
              AND estado = 'Borrador';";

        return self::executeNonQuery($sqlUpd, [
            "id_proveedor" => $idProveedor,
            "numero_factura" => $numeroFactura,
            "fecha" => $fecha,
            "id_compra" => $idCompra
        ]);
    }

    public static function addDetalle(
        int $idCompra,
        int $idProducto,
        int $cantidad,
        float $costoUnitario
    ): bool {
        $compra = self::getById($idCompra);
        if (($compra["estado"] ?? "") !== "Borrador") {
            throw new \Exception("Solo se pueden modificar compras en borrador");
        }

        $sqlIns = "INSERT INTO detalle_compra
            (id_compra, id_producto, cantidad, costo_unitario, subtotal)
            VALUES
            (:id_compra, :id_producto, :cantidad, :costo_unitario, :subtotal);";
        $resultado = self::executeNonQuery($sqlIns, [
            "id_compra" => $idCompra,
            "id_producto" => $idProducto,
            "cantidad" => $cantidad,
            "costo_unitario" => $costoUnitario,
            "subtotal" => $cantidad * $costoUnitario
        ]);
        self::recalcularTotales($idCompra);

        return $resultado;
    }

    public static function deleteDetalle(int $idCompra, int $idDetalle): bool
    {
        $sqlDel = "DELETE d FROM detalle_compra d
            INNER JOIN compras c ON d.id_compra = c.id_compra
            WHERE d.id_detalle_compra = :id_detalle_compra
              AND d.id_compra = :id_compra
              AND c.estado = 'Borrador';";
        $resultado = self::executeNonQuery($sqlDel, [
            "id_detalle_compra" => $idDetalle,
            "id_compra" => $idCompra
        ]);
        self::recalcularTotales($idCompra);

        return $resultado;
    }

    public static function delete(int $idCompra): bool
    {
        $conn = \Dao\Dao::getConn();
        try {
            $conn->beginTransaction();
            $query = $conn->prepare("SELECT estado FROM compras WHERE id_compra = :id_compra FOR UPDATE;");
            $query->execute(["id_compra" => $idCompra]);
            $compra = $query->fetch(\PDO::FETCH_ASSOC);
            if (!$compra || $compra["estado"] !== "Borrador") {
                throw new \Exception("Solo se pueden eliminar compras en borrador");
            }

            $query = $conn->prepare("DELETE FROM detalle_compra WHERE id_compra = :id_compra;");
            $query->execute(["id_compra" => $idCompra]);
            $query = $conn->prepare("DELETE FROM compras WHERE id_compra = :id_compra;");
            $query->execute(["id_compra" => $idCompra]);
            $conn->commit();
            return true;
        } catch (\Throwable $ex) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $ex;
        }
    }

    public static function confirmar(int $idCompra): bool
    {
        $conn = \Dao\Dao::getConn();
        try {
            $conn->beginTransaction();
            $query = $conn->prepare("SELECT estado FROM compras WHERE id_compra = :id_compra FOR UPDATE;");
            $query->execute(["id_compra" => $idCompra]);
            $compra = $query->fetch(\PDO::FETCH_ASSOC);
            if (!$compra || $compra["estado"] !== "Borrador") {
                throw new \Exception("La compra ya fue procesada o no existe");
            }

            $query = $conn->prepare("SELECT id_producto, cantidad FROM detalle_compra WHERE id_compra = :id_compra;");
            $query->execute(["id_compra" => $idCompra]);
            $detalles = $query->fetchAll(\PDO::FETCH_ASSOC);
            if (count($detalles) === 0) {
                throw new \Exception("Debe agregar al menos un producto antes de confirmar");
            }

            $actualizar = $conn->prepare("UPDATE productos
                SET stock = stock + :cantidad, estado = 'Disponible'
                WHERE id_producto = :id_producto;");
            foreach ($detalles as $detalle) {
                $actualizar->execute([
                    "cantidad" => intval($detalle["cantidad"]),
                    "id_producto" => intval($detalle["id_producto"])
                ]);
                if ($actualizar->rowCount() !== 1) {
                    throw new \Exception("No se pudo actualizar el inventario de un producto");
                }
            }

            $query = $conn->prepare("UPDATE compras SET estado = 'Confirmada'
                WHERE id_compra = :id_compra AND estado = 'Borrador';");
            $query->execute(["id_compra" => $idCompra]);
            if ($query->rowCount() !== 1) {
                throw new \Exception("No se pudo confirmar la compra");
            }

            $conn->commit();
            return true;
        } catch (\Throwable $ex) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $ex;
        }
    }

    private static function recalcularTotales(int $idCompra): void
    {
        $row = self::obtenerUnRegistro(
            "SELECT COALESCE(SUM(subtotal), 0) AS subtotal
             FROM detalle_compra WHERE id_compra = :id_compra;",
            ["id_compra" => $idCompra]
        );
        $subtotal = floatval($row["subtotal"] ?? 0);
        $impuesto = round($subtotal * 0.15, 2);
        $total = $subtotal + $impuesto;

        self::executeNonQuery(
            "UPDATE compras SET subtotal = :subtotal, impuesto = :impuesto, total = :total
             WHERE id_compra = :id_compra AND estado = 'Borrador';",
            [
                "subtotal" => $subtotal,
                "impuesto" => $impuesto,
                "total" => $total,
                "id_compra" => $idCompra
            ]
        );
    }
}

?>
