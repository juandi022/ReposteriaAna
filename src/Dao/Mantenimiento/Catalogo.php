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
        $sqlstr = "SELECT p.id_producto, p.nombre, c.nombre AS categoria, p.descripcion, p.precio, p.stock, p.imagen, p.estado FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria";
        $sqlstrCount = "SELECT COUNT(*) as count FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria";
        $conditions = [];
        $params = [];

        if ($partialName != "") {
            $conditions[] = "p.nombre LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if ($categoria != "") {
            $conditions[] = "c.nombre = :categoria";
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
        $sqlstr = "SELECT id_categoria, nombre AS categoria FROM categorias ORDER BY nombre ASC";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getById(int $id): array
    {
        $sqlstr = "SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria WHERE p.id_producto = :id_producto;";
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
        $categoriaId = self::getCategoriaIdByNombre($categoria);

        if ($estado !== null && $estado !== "") {
            $sqlIns = "insert into productos (
                nombre, id_categoria, descripcion, precio, stock, imagen, estado)
            values
                ( :nombre, :id_categoria, :descripcion, :precio, :stock, :imagen, :estado);";

            $param = [
                "nombre" => $nombre,
                "id_categoria" => $categoriaId,
                "descripcion" => $descripcion,
                "precio" => $precio,
                "stock" => $stock,
                "imagen" => $imagen,
                "estado" => $estado
            ];
        } else {
            $sqlIns = "insert into productos (
                nombre, id_categoria, descripcion, precio, stock, imagen)
            values
                ( :nombre, :id_categoria, :descripcion, :precio, :stock, :imagen);";

            $param = [
                "nombre" => $nombre,
                "id_categoria" => $categoriaId,
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
        $categoriaId = self::getCategoriaIdByNombre($categoria);

        $sqlUpd = "update productos set
            nombre = :nombre, id_categoria = :id_categoria,
            descripcion = :descripcion, precio = :precio,
            stock = :stock, imagen = :imagen, estado = :estado
            where id_producto = :id_producto;";

        $param = [
            "nombre" => $nombre,
            "id_categoria" => $categoriaId,
            "descripcion" => $descripcion,
            "precio" => $precio,
            "stock" => $stock,
            "imagen" => $imagen,
            "estado" => $estado,
            "id_producto" => $id
        ];

        return self::executeNonQuery($sqlUpd, $param);
    }

    private static function getCategoriaIdByNombre(string $categoria): int
    {
        $categoriaId = self::obtenerUnRegistro(
            "SELECT id_categoria FROM categorias WHERE nombre = :categoria LIMIT 1;",
            ["categoria" => $categoria]
        );

        return intval($categoriaId["id_categoria"] ?? 0);
    }

    public static function delete(int $id)
    {
        $sqlstr = "DELETE FROM productos WHERE id_producto = :id_producto;";
        return self::executeNonQuery($sqlstr, ["id_producto" => $id]);
    }
}