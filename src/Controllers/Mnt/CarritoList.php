<?php

namespace Controllers\Mnt;

use Controllers\PublicController;
use Utilities\Carrito as CarritoSession;
use Views\Renderer;

class CarritoList extends PublicController
{
    public function run(): void
    {
        $detalle = CarritoSession::obtener();
        $viewData = [
            "detalle" => $detalle,
            "total" => round(CarritoSession::total(), 2),
            "itemsCount" => CarritoSession::cantidadItems()
        ];

        Renderer::render("mnt/carrito", $viewData);
    }
}
