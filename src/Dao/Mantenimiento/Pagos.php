<?php

namespace Dao\Mantenimiento;

use Dao\Table;

class Pagos extends Table
{
    public static function createPago(
        int $usercod,
        float $total,
        string $metodoPago,
        string $estado = "Pendiente",
        ?string $codigoTransaccion = null
    ): int {

        $sqlstr = "INSERT INTO pagos
            (usercod, total, metodo_pago, estado, codigo_transaccion)
            VALUES
            (:usercod, :total, :metodo_pago, :estado, :codigo_transaccion);";

        self::executeNonQuery(
            $sqlstr,
            [
                "usercod" => $usercod,
                "total" => $total,
                "metodo_pago" => $metodoPago,
                "estado" => $estado,
                "codigo_transaccion" => $codigoTransaccion
            ]
        );

        $row = self::obtenerUnRegistro(
            "SELECT LAST_INSERT_ID() AS id_pago;",
            []
        );

        return intval($row["id_pago"]);
    }

    public static function getPagoById(int $idPago): array
    {
        $sqlstr = "SELECT *
                   FROM pagos
                   WHERE id_pago = :id_pago
                   LIMIT 1;";

        return self::obtenerUnRegistro(
            $sqlstr,
            [
                "id_pago" => $idPago
            ]
        ) ?? [];
    }

    public static function getPagosByUsuario(int $usercod): array
    {
        $sqlstr = "SELECT *
                   FROM pagos
                   WHERE usercod = :usercod
                   ORDER BY fecha DESC;";

        return self::obtenerRegistros(
            $sqlstr,
            [
                "usercod" => $usercod
            ]
        );
    }

    public static function updateEstado(
        int $idPago,
        string $estado,
        ?string $codigoTransaccion = null
    ): bool {

        $sqlstr = "UPDATE pagos
                   SET estado = :estado,
                       codigo_transaccion = :codigo_transaccion
                   WHERE id_pago = :id_pago;";

        return self::executeNonQuery(
            $sqlstr,
            [
                "id_pago" => $idPago,
                "estado" => $estado,
                "codigo_transaccion" => $codigoTransaccion
            ]
        );
    }

    public static function deletePago(int $idPago): bool
    {
        $sqlstr = "DELETE
                   FROM pagos
                   WHERE id_pago = :id_pago;";

        return self::executeNonQuery(
            $sqlstr,
            [
                "id_pago" => $idPago
            ]
        );
    }
}