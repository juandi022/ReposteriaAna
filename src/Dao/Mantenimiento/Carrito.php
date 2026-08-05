<?php

namespace Dao\Mantenimiento;

use Dao\Table;

class Carrito extends Table
{
    public static function getCarritoActivo(int $usercod): array
    {
        $sqlstr = "SELECT * FROM carrito WHERE usercod = :usercod AND estado = 'Activo' ORDER BY id_carrito DESC LIMIT 1;";
        $registro = self::obtenerUnRegistro($sqlstr, ["usercod" => $usercod]);
        return $registro ?: [];
    }

    public static function getOrCreateCarrito(int $usercod): array
    {
        $carrito = self::getCarritoActivo($usercod);
        if (empty($carrito)) {
            self::executeNonQuery(
                "INSERT INTO carrito (usercod, estado) VALUES (:usercod, 'Activo');",
                ["usercod" => $usercod]
            );
            $carrito = self::getCarritoActivo($usercod);
        }
        return $carrito;
    }

    public static function getDetalleCarrito(int $idCarrito): array
    {
        $sqlstr = "SELECT dc.id_detalle_carrito, dc.id_carrito, dc.id_producto,
            dc.cantidad, dc.precio, p.nombre AS nombre_producto, p.stock
            FROM detalle_carrito dc
            INNER JOIN productos p ON dc.id_producto = p.id_producto
            WHERE dc.id_carrito = :id_carrito;";
        return self::obtenerRegistros($sqlstr, ["id_carrito" => $idCarrito]);
    }

    public static function getDetalleProducto(int $idCarrito, int $idProducto): array
    {
        $sqlstr = "SELECT * FROM detalle_carrito WHERE id_carrito = :id_carrito AND id_producto = :id_producto LIMIT 1;";
        $registro = self::obtenerUnRegistro($sqlstr, ["id_carrito" => $idCarrito, "id_producto" => $idProducto]);
        return $registro ?: [];
    }

    public static function getDetalleById(int $idDetalleCarrito): array
    {
        $sqlstr = "SELECT dc.id_detalle_carrito, dc.id_carrito, dc.id_producto,
            dc.cantidad, dc.precio, p.nombre AS nombre_producto, p.stock
            FROM detalle_carrito dc
            INNER JOIN productos p ON dc.id_producto = p.id_producto
            WHERE dc.id_detalle_carrito = :id_detalle_carrito LIMIT 1;";
        $registro = self::obtenerUnRegistro($sqlstr, ["id_detalle_carrito" => $idDetalleCarrito]);
        return $registro ?: [];
    }

    public static function getProductoById(int $idProducto): array
    {
        $registro = self::obtenerUnRegistro(
            "SELECT * FROM productos WHERE id_producto = :id_producto LIMIT 1;",
            ["id_producto" => $idProducto]
        );
        return $registro ?: [];
    }

    public static function addProducto(int $idCarrito, int $idProducto, int $cantidad, float $precio)
    {
        $sqlIns = "INSERT INTO detalle_carrito (id_carrito, id_producto, cantidad, precio)
            VALUES (:id_carrito, :id_producto, :cantidad, :precio);";
        return self::executeNonQuery($sqlIns, [
            "id_carrito" => $idCarrito,
            "id_producto" => $idProducto,
            "cantidad" => $cantidad,
            "precio" => $precio
        ]);
    }

    public static function updateCantidad(int $idDetalleCarrito, int $cantidad)
    {
        $sqlUpd = "UPDATE detalle_carrito SET cantidad = :cantidad WHERE id_detalle_carrito = :id_detalle_carrito;";
        return self::executeNonQuery($sqlUpd, [
            "cantidad" => $cantidad,
            "id_detalle_carrito" => $idDetalleCarrito
        ]);
    }

    public static function removeDetalle(int $idDetalleCarrito)
    {
        $sqlDel = "DELETE FROM detalle_carrito WHERE id_detalle_carrito = :id_detalle_carrito;";
        return self::executeNonQuery($sqlDel, ["id_detalle_carrito" => $idDetalleCarrito]);
    }
}
