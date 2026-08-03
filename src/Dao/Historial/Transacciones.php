<?php

namespace Dao\Historial;

use Dao\Table;

class Transacciones extends Table
{
    private static function getBaseQuery(): string
    {
        return "SELECT CONVERT('COMPRA' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS tipo,
                c.id_compra AS id_transaccion,
                c.fecha,
                CONVERT(COALESCE(c.numero_factura, CONCAT('COMP-', c.id_compra)) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS referencia,
                CONVERT(CONCAT('Compra a proveedor: ', p.nombre) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS descripcion,
                c.total AS monto,
                CONVERT(c.estado USING utf8mb4) COLLATE utf8mb4_unicode_ci AS estado
            FROM compras c
            INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor

            UNION ALL

            SELECT CONVERT('PEDIDO' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS tipo,
                pe.id_pedido AS id_transaccion,
                pe.fecha,
                CONVERT(CONCAT('PED-', pe.id_pedido) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS referencia,
                CONVERT(CONCAT('Pedido de cliente: ', COALESCE(u.nombre, u.correo, 'Sin nombre')) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS descripcion,
                pe.total AS monto,
                CONVERT(pe.estado USING utf8mb4) COLLATE utf8mb4_unicode_ci AS estado
            FROM pedidos pe
            LEFT JOIN usuarios u ON pe.id_usuario = u.id_usuario

            UNION ALL

            SELECT CONVERT('PAGO' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS tipo,
                pa.id_pago AS id_transaccion,
                pa.fecha,
                CONVERT(COALESCE(NULLIF(pa.codigo_transaccion, ''), CONCAT('PAG-', pa.id_pago)) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS referencia,
                CONVERT(CONCAT('Pago de cliente: ', COALESCE(u.nombre, u.correo, 'Sin nombre')) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS descripcion,
                pa.total AS monto,
                CONVERT(pa.estado USING utf8mb4) COLLATE utf8mb4_unicode_ci AS estado
            FROM pagos pa
            LEFT JOIN usuarios u ON pa.id_usuario = u.id_usuario";
    }

    public static function getTransacciones(
        string $tipo = "",
        string $estado = "",
        string $fechaDesde = "",
        string $fechaHasta = "",
        string $referencia = "",
        int $page = 0,
        int $itemsPerPage = 10
    ): array {
        $baseQuery = self::getBaseQuery();
        $sqlstr = "SELECT * FROM (" . $baseQuery . ") transacciones";
        $sqlstrCount = "SELECT COUNT(*) AS count FROM (" . $baseQuery . ") transacciones";
        $conditions = [];
        $params = [];

        if ($tipo !== "") {
            $conditions[] = "tipo = :tipo";
            $params["tipo"] = $tipo;
        }
        if ($estado !== "") {
            $conditions[] = "estado = :estado";
            $params["estado"] = $estado;
        }
        if ($fechaDesde !== "") {
            $conditions[] = "fecha >= :fechaDesde";
            $params["fechaDesde"] = $fechaDesde . " 00:00:00";
        }
        if ($fechaHasta !== "") {
            $conditions[] = "fecha <= :fechaHasta";
            $params["fechaHasta"] = $fechaHasta . " 23:59:59";
        }
        if ($referencia !== "") {
            $conditions[] = "referencia LIKE :referencia";
            $params["referencia"] = "%" . $referencia . "%";
        }
        if (count($conditions) > 0) {
            $where = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $where;
            $sqlstrCount .= $where;
        }

        $sqlstr .= " ORDER BY fecha DESC, id_transaccion DESC";
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
            "transacciones" => self::obtenerRegistros($sqlstr, $params),
            "total" => $total,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getDetalle(string $tipo, int $id): array
    {
        switch ($tipo) {
            case "COMPRA":
                return self::getDetalleCompra($id);
            case "PEDIDO":
                return self::getDetallePedido($id);
            case "PAGO":
                return self::getDetallePago($id);
            default:
                throw new \Exception("Tipo de transacción no válido");
        }
    }

    private static function getDetalleCompra(int $id): array
    {
        $sqlstr = "SELECT c.id_compra AS id_transaccion, 'COMPRA' AS tipo,
            c.fecha, c.numero_factura AS referencia, p.nombre AS tercero,
            p.contacto, p.telefono, p.correo, c.subtotal, c.impuesto,
            c.total AS monto, c.estado
            FROM compras c
            INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor
            WHERE c.id_compra = :id;";
        $encabezado = self::obtenerUnRegistro($sqlstr, ["id" => $id]);
        if (!is_array($encabezado)) {
            return [];
        }

        $detalles = self::obtenerRegistros(
            "SELECT p.nombre AS concepto, d.cantidad, d.costo_unitario AS precio, d.subtotal
             FROM detalle_compra d
             INNER JOIN productos p ON d.id_producto = p.id_producto
             WHERE d.id_compra = :id
             ORDER BY d.id_detalle_compra ASC;",
            ["id" => $id]
        );

        return ["encabezado" => $encabezado, "detalles" => $detalles];
    }

    private static function getDetallePedido(int $id): array
    {
        $sqlstr = "SELECT pe.id_pedido AS id_transaccion, 'PEDIDO' AS tipo,
            pe.fecha, CONCAT('PED-', pe.id_pedido) AS referencia,
            CONCAT(u.nombre, ' ', u.apellido) AS tercero,
            '' AS contacto, '' AS telefono, u.correo,
            pe.total AS subtotal, 0 AS impuesto, pe.total AS monto, pe.estado
            FROM pedidos pe
            LEFT JOIN usuarios u ON pe.id_usuario = u.id_usuario
            WHERE pe.id_pedido = :id;";
        $encabezado = self::obtenerUnRegistro($sqlstr, ["id" => $id]);
        if (!is_array($encabezado)) {
            return [];
        }

        $detalles = self::obtenerRegistros(
            "SELECT p.nombre AS concepto, d.cantidad, d.precio, (d.cantidad * d.precio) AS subtotal
             FROM detalle_pedido d
             INNER JOIN productos p ON d.id_producto = p.id_producto
             WHERE d.id_pedido = :id
             ORDER BY d.id_detalle_pedido ASC;",
            ["id" => $id]
        );

        return ["encabezado" => $encabezado, "detalles" => $detalles];
    }

    private static function getDetallePago(int $id): array
    {
        $sqlstr = "SELECT pa.id_pago AS id_transaccion, 'PAGO' AS tipo,
            pa.fecha, COALESCE(NULLIF(pa.codigo_transaccion, ''), CONCAT('PAG-', pa.id_pago)) AS referencia,
            CONCAT(u.nombre, ' ', u.apellido) AS tercero,
            '' AS contacto, '' AS telefono, u.correo,
            pa.total AS subtotal, 0 AS impuesto, pa.total AS monto,
            pa.estado, pa.metodo_pago
            FROM pagos pa
            LEFT JOIN usuarios u ON pa.id_usuario = u.id_usuario
            WHERE pa.id_pago = :id;";
        $encabezado = self::obtenerUnRegistro($sqlstr, ["id" => $id]);
        if (!is_array($encabezado)) {
            return [];
        }

        return ["encabezado" => $encabezado, "detalles" => []];
    }
}

?>
