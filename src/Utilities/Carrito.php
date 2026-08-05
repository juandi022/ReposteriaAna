<?php

namespace Utilities;

class Carrito
{
    const SESSION_KEY = "carrito";

    private function __construct()
    {
    }
    private function __clone()
    {
    }

    public static function obtener(): array
    {
        $items = $_SESSION[self::SESSION_KEY] ?? [];
        if (!is_array($items)) {
            $items = [];
        }
        foreach ($items as &$item) {
            $item["subtotal"] = round(floatval($item["precio"]) * intval($item["cantidad"]), 2);
        }
        return $items;
    }

    public static function agregar(int $idProducto, string $nombre, float $precio, int $stock, int $cantidad): void
    {
        $items = $_SESSION[self::SESSION_KEY] ?? [];
        if (!is_array($items)) {
            $items = [];
        }
        if (isset($items[$idProducto])) {
            $items[$idProducto]["cantidad"] = intval($items[$idProducto]["cantidad"]) + $cantidad;
        } else {
            $items[$idProducto] = [
                "id_producto" => $idProducto,
                "nombre_producto" => $nombre,
                "precio" => $precio,
                "stock" => $stock,
                "cantidad" => $cantidad
            ];
        }
        $_SESSION[self::SESSION_KEY] = $items;
    }

    public static function eliminar(int $idProducto): void
    {
        $items = $_SESSION[self::SESSION_KEY] ?? [];
        if (!is_array($items)) {
            $items = [];
        }
        if (isset($items[$idProducto])) {
            unset($items[$idProducto]);
        }
        $_SESSION[self::SESSION_KEY] = $items;
    }

    public static function cantidadEnCarrito(int $idProducto): int
    {
        $items = $_SESSION[self::SESSION_KEY] ?? [];
        if (!is_array($items)) {
            return 0;
        }
        return isset($items[$idProducto]) ? intval($items[$idProducto]["cantidad"]) : 0;
    }

    public static function cantidadItems(): int
    {
        $items = $_SESSION[self::SESSION_KEY] ?? [];
        if (!is_array($items)) {
            return 0;
        }
        return count($items);
    }

    public static function total(): float
    {
        $total = 0.0;
        foreach (self::obtener() as $item) {
            $total += floatval($item["subtotal"]);
        }
        return round($total, 2);
    }

    public static function vaciar(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}
