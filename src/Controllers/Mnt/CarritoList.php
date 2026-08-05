<?php

namespace Controllers\Mnt;

use Controllers\PublicController;
use Dao\Mantenimiento\Carrito as CarritoDao;
use Utilities\Security;
use Views\Renderer;

class CarritoList extends PublicController
{
    public function run(): void
    {
        // Usuario NO logueado
        if (!Security::isLogged()) {

            $detalle = $_SESSION["carrito"] ?? [];

            $total = 0;

            foreach ($detalle as &$item) {

                $item["subtotal"] = round(
                    floatval($item["precio"]) * intval($item["cantidad"]),
                    2
                );

                $total += $item["subtotal"];
            }

            Renderer::render("mnt/carrito", [
                "carrito" => [],
                "detalle" => array_values($detalle),
                "total" => round($total, 2),
                "itemsCount" => count($detalle)
            ]);

            return;
        }

        // Usuario logueado
        $userId = Security::getUserId();

        $carrito = CarritoDao::getOrCreateCarrito($userId);

        $detalle = CarritoDao::getDetalleCarrito(
            intval($carrito["id_carrito"] ?? 0)
        );

        $total = 0;

        $detalle = array_map(function ($item) use (&$total) {

            $item["subtotal"] = round(
                floatval($item["precio"]) * intval($item["cantidad"]),
                2
            );

            $total += $item["subtotal"];

            return $item;

        }, $detalle);

        Renderer::render("mnt/carrito", [
            "carrito" => $carrito,
            "detalle" => $detalle,
            "total" => round($total, 2),
            "itemsCount" => count($detalle)
        ]);
    }
}