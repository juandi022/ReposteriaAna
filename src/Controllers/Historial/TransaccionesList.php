<?php

namespace Controllers\Historial;

use Controllers\PublicController;
use Dao\Historial\Transacciones as TransaccionesDao;
use Utilities\Context;
use Views\Renderer;

class TransaccionesList extends PublicController
{
    private $tipo = "";
    private $estado = "";
    private $fechaDesde = "";
    private $fechaHasta = "";
    private $referencia = "";
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $viewData = [];
    private $transacciones = [];
    private $transaccionesCount = 0;
    private $pages = 0;

    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $tmpTransacciones = TransaccionesDao::getTransacciones(
            $this->tipo,
            $this->estado,
            $this->fechaDesde,
            $this->fechaHasta,
            $this->referencia,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->transacciones = $tmpTransacciones["transacciones"];
        $this->transaccionesCount = $tmpTransacciones["total"];
        $this->pages = $this->transaccionesCount > 0
            ? ceil($this->transaccionesCount / $this->itemsPerPage) : 1;
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }
        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("historial/transacciones", $this->viewData);
    }

    private function getParams(): void
    {
        $this->tipo = isset($_GET["tipo"]) ? $_GET["tipo"] : $this->tipo;
        $this->estado = isset($_GET["estado"]) ? trim($_GET["estado"]) : $this->estado;
        $this->fechaDesde = isset($_GET["fechaDesde"]) ? $_GET["fechaDesde"] : $this->fechaDesde;
        $this->fechaHasta = isset($_GET["fechaHasta"]) ? $_GET["fechaHasta"] : $this->fechaHasta;
        $this->referencia = isset($_GET["referencia"]) ? trim($_GET["referencia"]) : $this->referencia;
        if ($this->tipo === "EMP") {
            $this->tipo = "";
        }
        if (!in_array($this->tipo, ["", "COMPRA", "PEDIDO", "PAGO"])) {
            $this->tipo = "";
        }
        $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
        if ($this->pageNumber < 1) {
            $this->pageNumber = 1;
        }
        if ($this->itemsPerPage < 1) {
            $this->itemsPerPage = 10;
        }
        if ($this->fechaDesde !== "" && strtotime($this->fechaDesde) === false) {
            $this->fechaDesde = "";
        }
        if ($this->fechaHasta !== "" && strtotime($this->fechaHasta) === false) {
            $this->fechaHasta = "";
        }
    }

    private function getParamsFromContext(): void
    {
        $this->tipo = Context::getContextByKey("historial_tipo");
        $this->estado = Context::getContextByKey("historial_estado");
        $this->fechaDesde = Context::getContextByKey("historial_fechaDesde");
        $this->fechaHasta = Context::getContextByKey("historial_fechaHasta");
        $this->referencia = Context::getContextByKey("historial_referencia");
        $this->pageNumber = intval(Context::getContextByKey("historial_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("historial_itemsPerPage"));
        if ($this->pageNumber < 1) {
            $this->pageNumber = 1;
        }
        if ($this->itemsPerPage < 1) {
            $this->itemsPerPage = 10;
        }
    }

    private function setParamsToContext(): void
    {
        Context::setContext("historial_tipo", $this->tipo, true);
        Context::setContext("historial_estado", $this->estado, true);
        Context::setContext("historial_fechaDesde", $this->fechaDesde, true);
        Context::setContext("historial_fechaHasta", $this->fechaHasta, true);
        Context::setContext("historial_referencia", $this->referencia, true);
        Context::setContext("historial_page", $this->pageNumber, true);
        Context::setContext("historial_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["tipo"] = $this->tipo;
        $this->viewData["estado"] = htmlspecialchars($this->estado, ENT_QUOTES, "UTF-8");
        $this->viewData["fechaDesde"] = $this->fechaDesde;
        $this->viewData["fechaHasta"] = $this->fechaHasta;
        $this->viewData["referencia"] = htmlspecialchars($this->referencia, ENT_QUOTES, "UTF-8");
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["transaccionesCount"] = $this->transaccionesCount;
        $this->viewData["transacciones"] = $this->transacciones;
        $tipoKey = "tipo_" . ($this->tipo === "" ? "EMP" : $this->tipo);
        $this->viewData[$tipoKey] = "selected";
        $this->viewData["hasPrevious"] = $this->pageNumber > 1;
        $this->viewData["hasNext"] = $this->pageNumber < $this->pages;
        $this->viewData["previousPage"] = max(1, $this->pageNumber - 1);
        $this->viewData["nextPage"] = min($this->pages, $this->pageNumber + 1);
    }
}

?>
