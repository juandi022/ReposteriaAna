<?php

namespace Dao\Funciones;

use Dao\Table;

class Funciones extends Table
{

    public static function getFunciones(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {

        $sqlstr = "SELECT f.fncod, f.fndsc, f.fnest, f.fntyp,
        CASE WHEN f.fnest = 'ACT' then 'Activo'
        WHEN f.fnest = 'INA' then 'Inactivo'
        ELSE 'Sin asignar'
        END AS fnestDesc
        FROM funciones f";

        $sqlstrCount = "SELECT COUNT(*) AS count FROM funciones f";

        $conditions = [];

        $params = [];

        if ($partialName != "") {
            $conditions[] = "fndsc LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Procesando la Petición, Estado Tiene un Valor Inválido");
        }

        if ($status != "") {
            $conditions[] = "fnest = :status";
            $params["status"] = $status;
        }

        if (count($conditions) > 0) {
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
        }

        if (!in_array($orderBy, ["fncod", "fndsc", "fnest", "fntyp", ""])) {
            throw new \Exception("Error Procesando la Petición, OrderBy Tiene un Valor Inválido");
        }

        if ($orderBy != "") {
            $sqlstr .= " ORDER BY " . $orderBy;

            if ($orderDescending) {
                $sqlstr .= " DESC";
            }
        }

        $numeroDeRegistros = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
        $pagesCount = ceil($numeroDeRegistros / $itemsPerPage);

        if ($page > $pagesCount - 1) {
            $page = $pagesCount - 1;
        }

        $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

        $registros = self::obtenerRegistros($sqlstr, $params);
        return [
            "funciones" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getFuncionById(string $fncond)
    {
        $sqlstr = "SELECT f.fncod, f.fndsc, f.fnest, f.fntyp
        FROM funciones f
        WHERE f.fncod = :fncod";

        $params = ["fncod" => $fncond];

        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function getFuncionesForOptions()
    {
        $sqlstr = "SELECT f.fncod, f.fndsc
        FROM funciones f
        WHERE f.fnest = 'ACT'
        ORDER BY f.fncod";

        return self::obtenerRegistros($sqlstr, array());
    }

    public static function insertFuncion(
        string $fncod,
        string $fndsc,
        string $fnest,
        string $fntyp
    ) {
        $sqlstr = "INSERT INTO funciones (fncod, fndsc, fnest, fntyp)
            VALUES (:fncod, :fndsc, :fnest, :fntyp)";
        $params = [
            "fncod" => $fncod,
            "fndsc" => $fndsc,
            "fnest" => $fnest,
            "fntyp" => $fntyp
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateFuncion(
        string $fncod,
        string $fndsc,
        string $fnest,
        string $fntyp
    ) {
        $sqlstr = "UPDATE funciones SET fncod = :fncod,
            fndsc = :fndsc, fnest = :fnest, fntyp = :fntyp
            WHERE fncod = :fncod";
        $params = [
            "fncod" => $fncod,
            "fndsc" => $fndsc,
            "fnest" => $fnest,
            "fntyp" => $fntyp
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function deleteFuncion(string $fncod)
    {
        $sqlstr = "DELETE FROM funciones WHERE fncod = :fncod";
        $params = ["fncod" => $fncod];
        return self::executeNonQuery($sqlstr, $params);
    }
}
