<?php

namespace Dao\FuncionesRoles;

use Dao\Table;

class FuncionesRoles extends Table
{

    public static function getFuncionesRoles(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {

        $sqlstr = "SELECT fr.rolescod, fr.fncod, fr.fnrolest, fr.fnexp,
        CASE WHEN fr.fnrolest = 'ACT' THEN 'Activo'
        WHEN fr.fnrolest = 'INA' THEN 'Inactivo'
        ELSE 'Sin asignar'
        END AS fnrolestDesc
        FROM funciones_roles fr";

        $sqlstrCount = "SELECT COUNT(*) AS count FROM funciones_roles fr";

        $conditions = [];

        $params = [];

        if ($partialName != "") {
            $conditions[] = "(fr.rolescod LIKE :partialName OR fr.fncod LIKE :partialName)";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Procesando la Petición, Estado Tiene un Valor Inválido");
        }

        if ($status != "") {
            $conditions[] = "fr.fnrolest = :status";
            $params["status"] = $status;
        }

        if (count($conditions) > 0) {
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
        }

        if (!in_array($orderBy, ["rolescod", "fncod", "fnrolest", "fnexp", ""])) {
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
            "funcionesRoles" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getFuncionesRolesById(string $rolescod, string $fncod)
    {
        $sqlstr = "SELECT fr.rolescod, fr.fncod, fr.fnrolest, fr.fnexp
        FROM funciones_roles fr
        WHERE fr.rolescod = :rolescod AND fr.fncod = :fncod";

        $params = [
            "rolescod" => $rolescod,
            "fncod" => $fncod];

        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function insertFuncionesRoles(
        string $rolescod,
        string $fncod,
        string $fnrolest,
        string $fnexp
    ) {
        $sqlstr = "INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp)
            VALUES (:rolescod, :fncod, :fnrolest, :fnexp)";
        $params = [
            "rolescod" => $rolescod,
            "fncod" => $fncod,
            "fnrolest" => $fnrolest,
            "fnexp" => $fnexp
        ];
        return self::executeNonQuery($sqlstr, $params);
    }


        

    public static function updateFuncionesRoles(
        string $rolescod,
        string $fncod,
        string $fnrolest,
        string $fnexp
    ) {
        $sqlstr = "UPDATE funciones_roles SET fnrolest = :fnrolest, fnexp = :fnexp
            WHERE rolescod = :rolescod AND fncod = :fncod";
        $params = [
            "rolescod" => $rolescod,
            "fncod" => $fncod,
            "fnrolest" => $fnrolest,
            "fnexp" => $fnexp
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function deleteRol(string $rolescod, string $fncod)
    {
        $sqlstr = "DELETE FROM funciones_roles WHERE rolescod = :rolescod AND fncod = :fncod";
        $params = [
            "rolescod" => $rolescod,
            "fncod" => $fncod];
        return self::executeNonQuery($sqlstr, $params);
    }
}
