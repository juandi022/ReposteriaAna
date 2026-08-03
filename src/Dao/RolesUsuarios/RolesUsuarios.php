<?php

namespace Dao\RolesUsuarios;

use Dao\Table;
use DateTime;

class RolesUsuarios extends Table
{

    public static function getRolesUsuarios(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {

        $sqlstr = "SELECT ru.usercod, ru.rolescod, ru.roleuserest, ru.roleuserfch, ru.roleuserexp,
        u.useremail, r.rolesdsc,
        CASE WHEN ru.roleuserest = 'ACT' THEN 'Activo'
        WHEN ru.roleuserest = 'INA' THEN 'Inactivo'
        ELSE 'Sin asignar'
        END AS roleuserestDesc
        FROM roles_usuarios ru
        INNER JOIN usuario u ON u.usercod = ru.usercod
        INNER JOIN roles r ON r.rolescod = ru.rolescod";

        $sqlstrCount = "SELECT COUNT(*) AS count FROM roles_usuarios ru";

        $conditions = [];

        $params = [];

        if ($partialName != "") {
            $conditions[] = "(ru.usercod LIKE :partialName OR ru.rolescod LIKE :partialName)";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if (!in_array($status, ["ACT", "INA", ""])) {
            throw new \Exception("Error Procesando la Petición, Estado Tiene un Valor Inválido");
        }

        if ($status != "") {
            $conditions[] = "ru.roleuserest = :status";
            $params["status"] = $status;
        }

        if (count($conditions) > 0) {
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
        }

        if (!in_array($orderBy, ["usercod", "rolescod", "roleuserest", "roleuserfch", ""])) {
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
            "rolesUsuarios" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getRolUsuarioById(int $usercod, string $rolescod)
    {
        $sqlstr = "SELECT ru.usercod, ru.rolescod, ru.roleuserest, ru.roleuserfch, ru.roleuserexp,
        u.useremail, r.rolesdsc
        FROM roles_usuarios ru
        INNER JOIN usuario u ON u.usercod = ru.usercod
        INNER JOIN roles r ON r.rolescod = ru.rolescod
        WHERE ru.usercod = :usercod AND ru.rolescod = :rolescod";

        $params = [
            "usercod" => $usercod,
            "rolescod" => $rolescod];

        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function insertRolUsuario(
        int $usercod,
        string $rolescod,
        string $roleuserest,
        DateTime $roleuserfch,
        DateTime $roleuserexp
    ) {
        $sqlstr = "INSERT INTO roles_usuarios (usercod, rolescod, roleuserest, roleuserfch, roleuserexp)
            VALUES (:usercod, :rolescod, :roleuserest, :roleuserfch, :roleuserexp)";
        $params = [
            "usercod" => $usercod,
            "rolescod" => $rolescod,
            "roleuserest" => $roleuserest,
            "roleuserfch" => $roleuserfch->format('Y-m-d H:i:s'),
            "roleuserexp" => $roleuserexp->format('Y-m-d H:i:s')
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateRolUsuario(
        int $usercod,
        string $rolescod,
        string $roleuserest,
        DateTime $roleuserfch,
        DateTime $roleuserexp
    ) {
        $sqlstr = "UPDATE roles_usuarios SET roleuserest = :roleuserest,
            roleuserfch = :roleuserfch, roleuserexp = :roleuserexp
            WHERE usercod = :usercod AND rolescod = :rolescod";
        $params = [
            "usercod" => $usercod,
            "rolescod" => $rolescod,
            "roleuserest" => $roleuserest,
            "roleuserfch" => $roleuserfch->format('Y-m-d H:i:s'),
            "roleuserexp" => $roleuserexp->format('Y-m-d H:i:s')
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function deleteRolUsuario(int $usercod, string $rolescod)
    {
        $sqlstr = "DELETE FROM roles_usuarios WHERE usercod = :usercod AND rolescod = :rolescod";
        $params = [
            "usercod" => $usercod,
            "rolescod" => $rolescod];
        return self::executeNonQuery($sqlstr, $params);
    }
}
