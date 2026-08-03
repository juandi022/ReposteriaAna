<?php

namespace Controllers\Compras;

use Controllers\PublicController;
use Utilities\Context;
use Dao\Compras\Compras as ComprasDao;
use Views\Renderer;

class ComprasList extends PublicController
{
    private $partialFactura = "";
    private $estado = "";
    private $orderBy = "";
    private $orderDescending = false;
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $viewData = [];
    private $compras = [];
    private $comprasCount = 0;
    private $pages = 0;

    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $tmpCompras = ComprasDao::getCompras(
            $this->partialFactura,
            $this->estado,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->compras = $tmpCompras["compras"];
        $this->comprasCount = $tmpCompras["total"];
        $this->pages = $this->comprasCount > 0 ? ceil($this->comprasCount / $this->itemsPerPage) : 1;
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }
        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("compras/compras", $this->viewData);
    }

    private function getParams(): void
    {
        $this->partialFactura = isset($_GET["partialFactura"]) ? trim($_GET["partialFactura"]) : $this->partialFactura;
        $this->estado = isset($_GET["estado"]) ? $_GET["estado"] : $this->estado;
        if ($this->estado === "EMP") {
            $this->estado = "";
        }
        if (!in_array($this->estado, ["", "Borrador", "Confirmada", "Anulada"])) {
            $this->estado = "";
        }
        $validOrder = ["id_compra", "fecha", "numero_factura", "total", "estado", "clear"];
        $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], $validOrder)
            ? $_GET["orderBy"] : $this->orderBy;
        if ($this->orderBy === "clear") {
            $this->orderBy = "";
        }
        $this->orderDescending = isset($_GET["orderDescending"])
            ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
        if ($this->pageNumber < 1) {
            $this->pageNumber = 1;
        }
        if ($this->itemsPerPage < 1) {
            $this->itemsPerPage = 10;
        }
    }

    private function getParamsFromContext(): void
    {
        $this->partialFactura = Context::getContextByKey("compras_partialFactura");
        $this->estado = Context::getContextByKey("compras_estado");
        $this->orderBy = Context::getContextByKey("compras_orderBy");
        $this->orderDescending = boolval(Context::getContextByKey("compras_orderDescending"));
        $this->pageNumber = intval(Context::getContextByKey("compras_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("compras_itemsPerPage"));
        if ($this->pageNumber < 1) {
            $this->pageNumber = 1;
        }
        if ($this->itemsPerPage < 1) {
            $this->itemsPerPage = 10;
        }
    }

    private function setParamsToContext(): void
    {
        Context::setContext("compras_partialFactura", $this->partialFactura, true);
        Context::setContext("compras_estado", $this->estado, true);
        Context::setContext("compras_orderBy", $this->orderBy, true);
        Context::setContext("compras_orderDescending", $this->orderDescending, true);
        Context::setContext("compras_page", $this->pageNumber, true);
        Context::setContext("compras_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialFactura"] = htmlspecialchars($this->partialFactura, ENT_QUOTES, "UTF-8");
        $this->viewData["estado"] = $this->estado;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["comprasCount"] = $this->comprasCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["compras"] = array_map(function ($compra) {
            $compra["esBorrador"] = ($compra["estado"] ?? "") === "Borrador";
            return $compra;
        }, $this->compras);

        $estadoKey = "estado_" . ($this->estado === "" ? "EMP" : $this->estado);
        $this->viewData[$estadoKey] = "selected";
        $this->viewData["hasPrevious"] = $this->pageNumber > 1;
        $this->viewData["hasNext"] = $this->pageNumber < $this->pages;
        $this->viewData["previousPage"] = max(1, $this->pageNumber - 1);
        $this->viewData["nextPage"] = min($this->pages, $this->pageNumber + 1);
    }
}

?>
