<?php

namespace Controllers\Checkout;

use Controllers\PublicController;
use Dao\Mantenimiento\Carrito as CarritoDao;
use Dao\Mantenimiento\Pagos;
use Dao\Mantenimiento\DetallePago;
use Utilities\Security;
use Utilities\Site;

class Checkout extends PublicController
{
    public function run(): void
    {
        if (!Security::isLogged()) {
            Site::redirectTo("index.php?page=Sec_Login");
            return;
        }

        $userId = Security::getUserId();

        $carrito = CarritoDao::getOrCreateCarrito($userId);

        if (empty($carrito)) {
            Site::redirectToWithMsg(
                "index.php?page=Mnt-CarritoList",
                "No existe un carrito activo."
            );
            return;
        }

        $detalle = CarritoDao::getDetalleCarrito(
            intval($carrito["id_carrito"])
        );

        if (count($detalle) == 0) {
            Site::redirectToWithMsg(
                "index.php?page=Mnt-CarritoList",
                "El carrito está vacío."
            );
            return;
        }

        $total = 0;

        foreach ($detalle as $item) {
            $total += floatval($item["precio"]) * intval($item["cantidad"]);
        }

        if ($this->isPostBack()) {

            $idPago = Pagos::createPago(
                $userId,
                $total,
                "PayPal",
                "Pendiente",
                null
            );
                    foreach ($detalle as $item) {

                DetallePago::addDetalle(
                    $idPago,
                    intval($item["id_producto"]),
                    intval($item["cantidad"]),
                    floatval($item["precio"])
                );
            }

            $PayPalOrder = new \Utilities\Paypal\PayPalOrder(
                "PAGO_" . $idPago,
                "http://localhost:8080//ReposteriaAna-main/index.php?page=Checkout-Error&idPago=" . $idPago,
                "http://localhost:8080//ReposteriaAna-main/index.php?page=Checkout-Accept&idPago=" . $idPago
            );

            foreach ($detalle as $item) {

                $PayPalOrder->addItem(
                    $item["nombre_producto"],
                    $item["nombre_producto"],
                    "PRD" . $item["id_producto"],
                    floatval($item["precio"]),
                    0,
                    intval($item["cantidad"]),
                    "PHYSICAL_GOODS"
                );
            }

            $response = $PayPalOrder->createOrder();

            $_SESSION["paypal_order_id"] = $response[1]->result->id;
            $_SESSION["id_pago"] = $idPago;

            Pagos::updateEstado(
                $idPago,
                "Pendiente",
                $response[1]->result->id
            );

            Site::redirectTo($response[0]->href);
            return;
        }

        $viewData = [
            "carrito" => $detalle,
            "total" => round($total, 2)
        ];

        \Views\Renderer::render(
            "paypal/checkout",
            $viewData
        );
    }
}