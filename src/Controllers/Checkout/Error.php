<?php

namespace Controllers\Checkout;

use Controllers\PublicController;
use Dao\Mantenimiento\Pagos;
use Utilities\Site;

class Error extends PublicController
{
    public function run(): void
    {
        if (isset($_SESSION["id_pago"])) {

            Pagos::updateEstado(
                intval($_SESSION["id_pago"]),
                "Cancelado",
                $_SESSION["paypal_order_id"] ?? null
            );
        }

        unset($_SESSION["paypal_order_id"]);
        unset($_SESSION["id_pago"]);

        Site::redirectToWithMsg(
            "index.php?page=Mnt-CarritoList",
            "El pago fue cancelado."
        );
    }
}