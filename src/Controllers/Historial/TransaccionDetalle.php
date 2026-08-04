<?php

namespace Controllers\Historial;

use Controllers\PrivateController;
use Dao\Historial\Transacciones as TransaccionesDao;
use Exception;
use Utilities\Site;
use Views\Renderer;

const HISTORIAL_LIST_VIEW_URI = "index.php?page=Historial-TransaccionesList";

class TransaccionDetalle extends PrivateController
{
    private string $tipo = "";
    private int $idTransaccion = 0;
    private array $viewData = [];

    public function run(): void
    {
        try {
            $this->getQueryParams();
            $resultado = TransaccionesDao::getDetalle($this->tipo, $this->idTransaccion);
            if (count($resultado) === 0) {
                throw new Exception("La transacción solicitada no existe");
            }
            $this->setParamsToDataView($resultado);
            Renderer::render("historial/transaccion_detalle", $this->viewData);
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            Site::redirectToWithMsg(HISTORIAL_LIST_VIEW_URI, $ex->getMessage());
        }
    }

    private function getQueryParams(): void
    {
        $this->tipo = strtoupper(trim(strval($_GET["tipo"] ?? "")));
        $this->idTransaccion = intval($_GET["id"] ?? 0);
        if (!in_array($this->tipo, ["COMPRA", "PEDIDO", "PAGO"])) {
            throw new Exception("Tipo de transacción no válido");
        }
        if ($this->idTransaccion <= 0) {
            throw new Exception("ID de transacción inválido");
        }
    }

    private function setParamsToDataView(array $resultado): void
    {
        $encabezado = $resultado["encabezado"];
        $detalles = $resultado["detalles"];
        $this->viewData["tipo"] = $this->tipo;
        $this->viewData["idTransaccion"] = $this->idTransaccion;
        $this->viewData["transaccion"] = $encabezado;
        $this->viewData["detalles"] = $detalles;
        $this->viewData["hasDetalles"] = count($detalles) > 0;
        $this->viewData["esCompra"] = $this->tipo === "COMPRA";
        $this->viewData["esPedido"] = $this->tipo === "PEDIDO";
        $this->viewData["esPago"] = $this->tipo === "PAGO";
        $this->viewData["metodoPago"] = $encabezado["metodo_pago"] ?? "";
    }
}

?>
