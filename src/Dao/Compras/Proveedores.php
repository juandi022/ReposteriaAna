<?php

namespace Dao\Compras;

use Dao\Table;

class Proveedores extends Table
{
    public static function getProveedoresFiltrados(
        string $partialName = "",
        string $estado = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ): array {
        $sqlstr = "SELECT id_proveedor, nombre, contacto, telefono, correo, direccion, estado
            FROM proveedores";
        $sqlstrCount = "SELECT COUNT(*) AS count FROM proveedores";
        $conditions = [];
        $params = [];

        if ($partialName !== "") {
            $conditions[] = "nombre LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }
        if ($estado !== "") {
            $conditions[] = "estado = :estado";
            $params["estado"] = $estado;
        }
        if (count($conditions) > 0) {
            $where = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $where;
            $sqlstrCount .= $where;
        }

        if (!in_array($orderBy, ["id_proveedor", "nombre", "estado", ""])) {
            throw new \Exception("El criterio de ordenamiento no es válido");
        }
        if ($orderBy !== "") {
            $sqlstr .= " ORDER BY " . $orderBy;
            if ($orderDescending) {
                $sqlstr .= " DESC";
            }
        } else {
            $sqlstr .= " ORDER BY id_proveedor DESC";
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
            "proveedores" => self::obtenerRegistros($sqlstr, $params),
            "total" => $total,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getProveedores(): array
    {
        $sqlstr = "SELECT id_proveedor, nombre, contacto, telefono, correo, direccion, estado
            FROM proveedores
            ORDER BY nombre ASC;";

        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getProveedoresActivos(): array
    {
        $sqlstr = "SELECT id_proveedor, nombre
            FROM proveedores
            WHERE estado = 'Activo'
            ORDER BY nombre ASC;";

        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getById(int $id): array
    {
        $sqlstr = "SELECT id_proveedor, nombre, contacto, telefono, correo, direccion, estado
            FROM proveedores
            WHERE id_proveedor = :id_proveedor;";

        $registro = self::obtenerUnRegistro(
            $sqlstr,
            ["id_proveedor" => $id]
        );

        return is_array($registro) ? $registro : [];
    }

    public static function create(
        string $nombre,
        string $contacto,
        string $telefono,
        string $correo,
        string $direccion,
        string $estado
    ): bool {
        $sqlIns = "INSERT INTO proveedores
            (nombre, contacto, telefono, correo, direccion, estado)
            VALUES
            (:nombre, :contacto, :telefono, :correo, :direccion, :estado);";

        return self::executeNonQuery($sqlIns, [
            "nombre" => $nombre,
            "contacto" => $contacto,
            "telefono" => $telefono,
            "correo" => $correo,
            "direccion" => $direccion,
            "estado" => $estado
        ]);
    }

    public static function update(
        int $id,
        string $nombre,
        string $contacto,
        string $telefono,
        string $correo,
        string $direccion,
        string $estado
    ): bool {
        $sqlUpd = "UPDATE proveedores SET
            nombre = :nombre,
            contacto = :contacto,
            telefono = :telefono,
            correo = :correo,
            direccion = :direccion,
            estado = :estado
            WHERE id_proveedor = :id_proveedor;";

        return self::executeNonQuery($sqlUpd, [
            "nombre" => $nombre,
            "contacto" => $contacto,
            "telefono" => $telefono,
            "correo" => $correo,
            "direccion" => $direccion,
            "estado" => $estado,
            "id_proveedor" => $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $countRow = self::obtenerUnRegistro(
            "SELECT COUNT(*) AS count FROM compras WHERE id_proveedor = :id_proveedor;",
            ["id_proveedor" => $id]
        );
        if (intval($countRow["count"] ?? 0) > 0) {
            throw new \Exception("No se puede eliminar el proveedor porque tiene compras registradas");
        }

        return self::executeNonQuery(
            "DELETE FROM proveedores WHERE id_proveedor = :id_proveedor;",
            ["id_proveedor" => $id]
        );
    }
}

?>
