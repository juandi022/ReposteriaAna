<?php

namespace Controllers\Mnt;

use Controllers\PrivateController;
use Dao\Mantenimiento\Carrito as CarritoDao;
use Utilities\Security;
use Views\Renderer;

class CarritoList extends PrivateController
{
    public function run(): void
    {
        $userId = Security::getUserId();
        $carrito = CarritoDao::getOrCreateCarrito($userId);
        $detalle = CarritoDao::getDetalleCarrito(intval($carrito["id_carrito"] ?? 0));

        $total = 0;
        $detalle = array_map(function ($item) use (&$total) {
            $item["subtotal"] = round(floatval($item["precio"]) * intval($item["cantidad"]), 2);
            $total += $item["subtotal"];
            return $item;
        }, $detalle);

        $viewData = [
            "carrito" => $carrito,
            "detalle" => $detalle,
            "total" => round($total, 2),
            "itemsCount" => count($detalle)
        ];

        Renderer::render("mnt/carrito", $viewData);
    }
}
