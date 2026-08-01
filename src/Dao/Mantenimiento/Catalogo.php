<?php

namespace Dao\Mantenimiento;

use Dao\Table;

class Catalogo extends Table
{
    public static function getProductos(
        string $partialName = "",
        string $categoria = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $sqlstr = "SELECT id_producto, nombre, categoria, descripcion, precio, stock, imagen, estado FROM productos";
        $sqlstrCount = "SELECT COUNT(*) as count FROM productos";
        $conditions = [];
        $params = [];

        if ($partialName != "") {
            $conditions[] = "nombre LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if ($categoria != "") {
            $conditions[] = "categoria = :categoria";
            $params["categoria"] = $categoria;
        }

        if (count($conditions) > 0) {
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
        }

        if (!in_array($orderBy, ["id_producto", "nombre", "precio", ""])) {
            throw new \Exception("Error Processing Request OrderBy has invalid value");
        }
        if ($orderBy != "") {
            $sqlstr .= " ORDER BY " . $orderBy;
            if ($orderDescending) {
                $sqlstr .= " DESC";
            }
        }

        $numeroDeRegistros = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
        $pagesCount = $itemsPerPage > 0 ? ceil($numeroDeRegistros / $itemsPerPage) : 1;

        if ($pagesCount > 0 && $page > $pagesCount - 1) {
            $page = $pagesCount - 1;
        }
        if ($page < 0) {
            $page = 0;
        }

        $offset = $page * $itemsPerPage;
        $sqlstr .= " LIMIT " . $offset . ", " . $itemsPerPage;

        $registros = self::obtenerRegistros($sqlstr, $params);

        return [
            "products" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getCategorias(): array
    {
        $sqlstr = "SELECT DISTINCT categoria FROM productos ORDER BY categoria ASC";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getById(int $id): array
    {
        $sqlstr = "SELECT * FROM productos WHERE id_producto = :id_producto;";
        return self::obtenerUnRegistro($sqlstr, ["id_producto" => $id]);
    }

    public static function create(
        string $nombre,
        string $categoria,
        string $descripcion,
        float $precio,
        int $stock,
        string $imagen,
        ?string $estado = null
    ) {
        if ($estado !== null && $estado !== "") {
            $sqlIns = "insert into productos (
                nombre, categoria, descripcion, precio, stock, imagen, estado)
            values
                ( :nombre, :categoria, :descripcion, :precio, :stock, :imagen, :estado);";

            $param = [
                "nombre" => $nombre,
                "categoria" => $categoria,
                "descripcion" => $descripcion,
                "precio" => $precio,
                "stock" => $stock,
                "imagen" => $imagen,
                "estado" => $estado
            ];
        } else {
            $sqlIns = "insert into productos (
                nombre, categoria, descripcion, precio, stock, imagen)
            values
                ( :nombre, :categoria, :descripcion, :precio, :stock, :imagen);";

            $param = [
                "nombre" => $nombre,
                "categoria" => $categoria,
                "descripcion" => $descripcion,
                "precio" => $precio,
                "stock" => $stock,
                "imagen" => $imagen
            ];
        }

        return self::executeNonQuery($sqlIns, $param);
    }

    public static function update(
        int $id,
        string $nombre,
        string $categoria,
        string $descripcion,
        float $precio,
        int $stock,
        string $imagen,
        string $estado
    ) {
        $sqlUpd = "update productos set
            nombre = :nombre, categoria = :categoria,
            descripcion = :descripcion, precio = :precio,
            stock = :stock, imagen = :imagen, estado = :estado
            where id_producto = :id_producto;";

        $param = [
            "nombre" => $nombre,
            "categoria" => $categoria,
            "descripcion" => $descripcion,
            "precio" => $precio,
            "stock" => $stock,
            "imagen" => $imagen,
            "estado" => $estado,
            "id_producto" => $id
        ];

        return self::executeNonQuery($sqlUpd, $param);
    }

    public static function delete(int $id)
    {
        $sqlstr = "DELETE FROM productos WHERE id_producto = :id_producto;";
        return self::executeNonQuery($sqlstr, ["id_producto" => $id]);
    }
}