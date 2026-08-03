<?php

namespace Controllers\Compras;

use Controllers\PublicController;
use Utilities\Context;
use Dao\Compras\Proveedores as ProveedoresDao;
use Views\Renderer;

class ProveedoresList extends PublicController
{
    private $partialName = "";
    private $estado = "";
    private $orderBy = "";
    private $orderDescending = false;
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $viewData = [];
    private $proveedores = [];
    private $proveedoresCount = 0;
    private $pages = 0;

    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $tmpProveedores = ProveedoresDao::getProveedoresFiltrados(
            $this->partialName,
            $this->estado,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->proveedores = $tmpProveedores["proveedores"];
        $this->proveedoresCount = $tmpProveedores["total"];
        $this->pages = $this->proveedoresCount > 0 ? ceil($this->proveedoresCount / $this->itemsPerPage) : 1;
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }
        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("compras/proveedores", $this->viewData);
    }

    private function getParams(): void
    {
        $this->partialName = isset($_GET["partialName"]) ? trim($_GET["partialName"]) : $this->partialName;
        $this->estado = isset($_GET["estado"]) ? $_GET["estado"] : $this->estado;
        if ($this->estado === "EMP") {
            $this->estado = "";
        }
        if (!in_array($this->estado, ["", "Activo", "Inactivo"])) {
            $this->estado = "";
        }
        $validOrder = ["id_proveedor", "nombre", "estado", "clear"];
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
        $this->partialName = Context::getContextByKey("proveedores_partialName");
        $this->estado = Context::getContextByKey("proveedores_estado");
        $this->orderBy = Context::getContextByKey("proveedores_orderBy");
        $this->orderDescending = boolval(Context::getContextByKey("proveedores_orderDescending"));
        $this->pageNumber = intval(Context::getContextByKey("proveedores_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("proveedores_itemsPerPage"));
        if ($this->pageNumber < 1) {
            $this->pageNumber = 1;
        }
        if ($this->itemsPerPage < 1) {
            $this->itemsPerPage = 10;
        }
    }

    private function setParamsToContext(): void
    {
        Context::setContext("proveedores_partialName", $this->partialName, true);
        Context::setContext("proveedores_estado", $this->estado, true);
        Context::setContext("proveedores_orderBy", $this->orderBy, true);
        Context::setContext("proveedores_orderDescending", $this->orderDescending, true);
        Context::setContext("proveedores_page", $this->pageNumber, true);
        Context::setContext("proveedores_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialName"] = htmlspecialchars($this->partialName, ENT_QUOTES, "UTF-8");
        $this->viewData["estado"] = $this->estado;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["proveedoresCount"] = $this->proveedoresCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["proveedores"] = $this->proveedores;
        $estadoKey = "estado_" . ($this->estado === "" ? "EMP" : $this->estado);
        $this->viewData[$estadoKey] = "selected";
        $this->viewData["hasPrevious"] = $this->pageNumber > 1;
        $this->viewData["hasNext"] = $this->pageNumber < $this->pages;
        $this->viewData["previousPage"] = max(1, $this->pageNumber - 1);
        $this->viewData["nextPage"] = min($this->pages, $this->pageNumber + 1);
    }
}

?>
