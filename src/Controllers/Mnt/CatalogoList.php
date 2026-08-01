<?php

namespace Controllers\Mnt;

use Controllers\PublicController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Mantenimiento\Catalogo as CatalogoDao;
use Views\Renderer;

class CatalogoList extends PublicController
{
  private $partialName = "";
  private $categoria = "";
  private $orderBy = "";
  private $orderDescending = false;
  private $pageNumber = 1;
  private $itemsPerPage = 10;
  private $viewData = [];
  private $productos = [];
  private $productosCount = 0;
  private $pages = 0;

  public function run(): void
  {
    $this->getParamsFromContext();
    $this->getParams();
    $tmpProductos = CatalogoDao::getProductos(
      $this->partialName,
      $this->categoria,
      $this->orderBy,
      $this->orderDescending,
      $this->pageNumber - 1,
      $this->itemsPerPage
    );
    $this->productos = $tmpProductos["products"];
    $this->productosCount = $tmpProductos["total"];
    $this->pages = $this->productosCount > 0 ? ceil($this->productosCount / $this->itemsPerPage) : 1;
    if ($this->pageNumber > $this->pages) {
      $this->pageNumber = $this->pages;
    }
    $this->setParamsToContext();
    $this->setParamsToDataView();
    Renderer::render("mnt/catalogo", $this->viewData);
  }

  private function getParams(): void
  {
    $this->partialName = isset($_GET["partialName"]) ? $_GET["partialName"] : $this->partialName;
    $this->categoria = isset($_GET["categoria"]) ? $_GET["categoria"] : $this->categoria;
    if ($this->categoria === "EMP") {
      $this->categoria = "";
    }
    $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], ["id_producto", "nombre_producto", "precio", "clear"]) ? $_GET["orderBy"] : $this->orderBy;
    if ($this->orderBy === "clear") {
      $this->orderBy = "";
    }
    $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
    $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
    $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
  }

  private function getParamsFromContext(): void
  {
    $this->partialName = Context::getContextByKey("catalogo_partialName");
    $this->categoria = Context::getContextByKey("catalogo_categoria");
    $this->orderBy = Context::getContextByKey("catalogo_orderBy");
    $this->orderDescending = boolval(Context::getContextByKey("catalogo_orderDescending"));
    $this->pageNumber = intval(Context::getContextByKey("catalogo_page"));
    $this->itemsPerPage = intval(Context::getContextByKey("catalogo_itemsPerPage"));
    if ($this->pageNumber < 1) $this->pageNumber = 1;
    if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
  }

  private function setParamsToContext(): void
  {
    Context::setContext("catalogo_partialName", $this->partialName, true);
    Context::setContext("catalogo_categoria", $this->categoria, true);
    Context::setContext("catalogo_orderBy", $this->orderBy, true);
    Context::setContext("catalogo_orderDescending", $this->orderDescending, true);
    Context::setContext("catalogo_page", $this->pageNumber, true);
    Context::setContext("catalogo_itemsPerPage", $this->itemsPerPage, true);
  }

  private function setParamsToDataView(): void
  {
    $this->viewData["partialName"] = $this->partialName;
    $this->viewData["categoria"] = $this->categoria;
    $this->viewData["categorias"] = CatalogoDao::getCategorias();
    $this->viewData["orderBy"] = $this->orderBy;
    $this->viewData["orderDescending"] = $this->orderDescending;
    $this->viewData["pageNum"] = $this->pageNumber;
    $this->viewData["itemsPerPage"] = $this->itemsPerPage;
    $this->viewData["productosCount"] = $this->productosCount;
    $this->viewData["pages"] = $this->pages;
    $this->viewData["productos"] = $this->productos;
    if ($this->orderBy !== "") {
      $orderByKey = "Order" . ucfirst($this->orderBy);
      $orderByKeyNoOrder = "OrderBy" . ucfirst($this->orderBy);
      $this->viewData[$orderByKeyNoOrder] = true;
      if ($this->orderDescending) {
        $orderByKey .= "Desc";
      }
      $this->viewData[$orderByKey] = true;
    }
    $categoriaKey = "categoria_" . ($this->categoria === "" ? "EMP" : $this->categoria);
    $this->viewData[$categoriaKey] = "selected";
    $pagination = Paging::getPagination(
      $this->productosCount,
      $this->itemsPerPage,
      $this->pageNumber,
      "index.php?page=Mnt-CatalogoList",
      "Mnt-CatalogoList"
    );
    $this->viewData["pagination"] = $pagination;
  }
}
?>