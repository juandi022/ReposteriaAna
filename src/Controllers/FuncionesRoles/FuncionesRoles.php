<?php

    namespace Controllers\FuncionesRoles;

    use Controllers\PrivateController;
    use Utilities\Context;
    use Utilities\Paging;
    use Dao\FuncionesRoles\FuncionesRoles as DaoFuncionesRoles;
    use Views\Renderer;

    class FuncionesRoles extends PrivateController
    {
        private $partialName = "";
        private $status = "";
        private $orderBy = "";
        private $orderDescending = false;
        private $pageNumber = 1;
        private $itemsPerPage = 10;
        private $viewData = [];
        private $funcionesRoles = [];
        private $funcionesRolesCount = 0;
        private $pages = 0;

        public function run(): void
        {
            $this->getParamsFromContext();
            $this->getParams();
            $tmpFuncionesRoles = DaoFuncionesRoles::getFuncionesRoles(
                $this->partialName,
                $this->status,
                $this->orderBy,
                $this->orderDescending,
                $this->pageNumber - 1,
                $this->itemsPerPage
            );
            $this->funcionesRoles = $tmpFuncionesRoles["funcionesRoles"];
            $this->funcionesRolesCount = $tmpFuncionesRoles["total"];
            $this->pages = $this->funcionesRolesCount > 0 ? ceil($this->funcionesRolesCount / $this->itemsPerPage) : 1;
            
            if ($this->pageNumber > $this->pages) {
                $this->pageNumber = $this->pages;
            }

            $this->setParamsToContext();
            $this->setParamsToDataView();
            Renderer::render("funcionesRoles/funcionesRoles", $this->viewData);
        }

        private function getParams(): void
        {
            $this->partialName = isset($_GET["partialName"]) ? $_GET["partialName"] : $this->partialName;
            $this->status = isset($_GET["status"]) && in_array($_GET["status"], ['ACT', 'INA', 'EMP']) ? $_GET["status"] : $this->status;
            
            if ($this->status === "EMP") {
                $this->status = "";
            }
            
            $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], ["rolescod", "fncod", "fnrolest", "clear"]) ? $_GET["orderBy"] : $this->orderBy;
            
            if ($this->orderBy === "clear") {
                $this->orderBy = "";
            }

            $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
            $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
            $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
        }

        private function getParamsFromContext(): void
        {
            $this->partialName = Context::getContextByKey("funcionesRoles_partialName");
            $this->status = Context::getContextByKey("funcionesRoles_status");
            $this->orderBy = Context::getContextByKey("funcionesRoles_orderBy");
            $this->orderDescending = boolval(Context::getContextByKey("funcionesRoles_orderDescending"));
            $this->pageNumber = intval(Context::getContextByKey("funcionesRoles_page"));
            $this->itemsPerPage = intval(Context::getContextByKey("funcionesRoles_itemsPerPage"));
            
            if ($this->pageNumber < 1) $this->pageNumber = 1;
            
            if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
        }

        private function setParamsToContext(): void
        {
            Context::setContext("funcionesRoles_partialName", $this->partialName, true);
            Context::setContext("funcionesRoles_status", $this->status, true);
            Context::setContext("funcionesRoles_orderBy", $this->orderBy, true);
            Context::setContext("funcionesRoles_orderDescending", $this->orderDescending, true);
            Context::setContext("funcionesRoles_page", $this->pageNumber, true);
            Context::setContext("funcionesRoles_itemsPerPage", $this->itemsPerPage, true);
        }

        private function setParamsToDataView(): void
        {
            $this->viewData["partialName"] = $this->partialName;
            $this->viewData["status"] = $this->status;
            $this->viewData["orderBy"] = $this->orderBy;
            $this->viewData["orderDescending"] = $this->orderDescending;
            $this->viewData["pageNum"] = $this->pageNumber;
            $this->viewData["itemsPerPage"] = $this->itemsPerPage;
            $this->viewData["funcionesRolesCount"] = $this->funcionesRolesCount;
            $this->viewData["pages"] = $this->pages;
            $this->viewData["funcionesRoles"] = $this->funcionesRoles;

            if ($this->orderBy !== "") {
                $orderByKey = "Order" . ucfirst($this->orderBy);
                $orderByKeyNoOrder = "OrderBy" . ucfirst($this->orderBy);
                $this->viewData[$orderByKeyNoOrder] = true;

                if ($this->orderDescending) {
                    $orderByKey .= "Desc";
                }

                $this->viewData[$orderByKey] = true;
            }
            
            $statusKey = "status_" . ($this->status === "" ? "EMP" : $this->status);
            $this->viewData[$statusKey] = "selected";
            $pagination = Paging::getPagination(
                $this->funcionesRolesCount,
                $this->itemsPerPage,
                $this->pageNumber,
                "index.php?page=FuncionesRoles_FuncionesRoles",
                "FuncionesRoles_FuncionesRoles"
            );
            $this->viewData["pagination"] = $pagination;
        }
    }
?>