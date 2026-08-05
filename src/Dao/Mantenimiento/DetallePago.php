<?php

namespace Dao\Mantenimiento;

use Dao\Table;

class DetallePago extends Table
{
    public static function addDetalle(
        int $idPago,
        int $idProducto,
        int $cantidad,
        float $precio
    ): bool {

        $sqlstr = "INSERT INTO detalle_pago
            (id_pago, id_producto, cantidad, precio)
            VALUES
            (:id_pago, :id_producto, :cantidad, :precio);";

        return self::executeNonQuery(
            $sqlstr,
            [
                "id_pago" => $idPago,
                "id_producto" => $idProducto,
                "cantidad" => $cantidad,
                "precio" => $precio
            ]
        );
    }

    public static function getDetallePago(int $idPago): array
    {
        $sqlstr = "SELECT
                        dp.*,
                        p.nombre
                    FROM detalle_pago dp
                    INNER JOIN productos p
                        ON dp.id_producto = p.id_producto
                    WHERE dp.id_pago = :id_pago;";

        return self::obtenerRegistros(
            $sqlstr,
            [
                "id_pago" => $idPago
            ]
        );
    }

    public static function getDetalleById(int $idDetalle): array
    {
        $sqlstr = "SELECT *
                   FROM detalle_pago
                   WHERE id_detalle = :id_detalle
                   LIMIT 1;";

        return self::obtenerUnRegistro(
            $sqlstr,
            [
                "id_detalle" => $idDetalle
            ]
        ) ?? [];
    }

    public static function deleteDetalle(int $idDetalle): bool
    {
        $sqlstr = "DELETE
                   FROM detalle_pago
                   WHERE id_detalle = :id_detalle;";

        return self::executeNonQuery(
            $sqlstr,
            [
                "id_detalle" => $idDetalle
            ]
        );
    }

    public static function deleteByPago(int $idPago): bool
    {
        $sqlstr = "DELETE
                   FROM detalle_pago
                   WHERE id_pago = :id_pago;";

        return self::executeNonQuery(
            $sqlstr,
            [
                "id_pago" => $idPago
            ]
        );
    }
}