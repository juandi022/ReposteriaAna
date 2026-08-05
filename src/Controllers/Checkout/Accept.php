<?php

namespace Controllers\Checkout;

use Controllers\PublicController;
use Dao\Mantenimiento\Pagos;
use Dao\Mantenimiento\DetallePago;
use Dao\Mantenimiento\Carrito;
use Utilities\Security;
use Utilities\Site;

class Accept extends PublicController
{
    public function run(): void
    {
        if (!Security::isLogged()) {
            Site::redirectTo("index.php?page=Sec_Login");
            return;
        }

        $token = $_GET["token"] ?? "";
        $sessionToken = $_SESSION["paypal_order_id"] ?? "";

        if ($token == "" || $token != $sessionToken) {
            Site::redirectToWithMsg(
                "index.php?page=Mnt-CarritoList",
                "La orden de PayPal no es válida."
            );
            return;
        }

        try {

            $response = \Utilities\Paypal\PayPalCapture::captureOrder($token);

            if ($response->result->status != "COMPLETED") {

                Pagos::updateEstado(
                    intval($_SESSION["id_pago"]),
                    "Cancelado",
                    $response->result->id
                );

                Site::redirectToWithMsg(
                    "index.php?page=Mnt-CarritoList",
                    "El pago no pudo completarse."
                );

                return;
            }

            $idPago = intval($_SESSION["id_pago"]);

            Pagos::updateEstado(
                $idPago,
                "Pagado",
                $response->result->id
            );

            $detalle = DetallePago::getDetallePago($idPago);

            foreach ($detalle as $item) {

                $producto = Carrito::getProductoById(
                    intval($item["id_producto"])
                );

                $nuevoStock =
                    intval($producto["stock"]) -
                    intval($item["cantidad"]);
                                    if ($nuevoStock < 0) {
                    $nuevoStock = 0;
                }

                // Estos métodos los agregaremos después en Carrito.php
                Carrito::updateStock(
                    intval($item["id_producto"]),
                    $nuevoStock
                );
            }

            $userId = Security::getUserId();

            $carrito = Carrito::getOrCreateCarrito($userId);

            Carrito::clearCarrito(
                intval($carrito["id_carrito"])
            );

            unset($_SESSION["paypal_order_id"]);
            unset($_SESSION["id_pago"]);
            unset($_SESSION["carrito"]);

            Site::redirectToWithMsg(
                "index.php?page=Mnt-CatalogoList",
                "¡Pago realizado correctamente!"
            );

        } catch (\Exception $ex) {

            error_log($ex->getMessage());

            Site::redirectToWithMsg(
                "index.php?page=Mnt-CarritoList",
                "Ocurrió un error al procesar el pago."
            );
        }
    }
}