<?php

    namespace Controllers\RolesUsuarios;

    use Controllers\PublicController;
    use Utilities\Context;
    use Utilities\Paging;
    use Dao\RolesUsuarios\RolesUsuarios as DaoRolesUsuarios;
    use Views\Renderer;

    class RolesUsuarios extends PublicController
    {
        private $partialName = "";
        private $status = "";
        private $orderBy = "";
        private $orderDescending = false;
        private $pageNumber = 1;
        private $itemsPerPage = 10;
        private $viewData = [];
        private $rolesUsuarios = [];
        private $rolesUsuariosCount = 0;
        private $pages = 0;

        public function run(): void
        {
            $this->getParamsFromContext();
            $this->getParams();
            $tmpRolesUsuarios = DaoRolesUsuarios::getRolesUsuarios(
                $this->partialName,
                $this->status,
                $this->orderBy,
                $this->orderDescending,
                $this->pageNumber - 1,
                $this->itemsPerPage
            );
            $this->rolesUsuarios = $tmpRolesUsuarios["rolesUsuarios"];
            $this->rolesUsuariosCount = $tmpRolesUsuarios["total"];
            $this->pages = $this->rolesUsuariosCount > 0 ? ceil($this->rolesUsuariosCount / $this->itemsPerPage) : 1;
            
            if ($this->pageNumber > $this->pages) {
                $this->pageNumber = $this->pages;
            }

            $this->setParamsToContext();
            $this->setParamsToDataView();
            Renderer::render("rolesusuarios/rolesUsuarios", $this->viewData);
        }

        private function getParams(): void
        {
            $this->partialName = isset($_GET["partialName"]) ? $_GET["partialName"] : $this->partialName;
            $this->status = isset($_GET["status"]) && in_array($_GET["status"], ['ACT', 'INA', 'EMP']) ? $_GET["status"] : $this->status;
            
            if ($this->status === "EMP") {
                $this->status = "";
            }
            
            $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], ["usercod", "rolescod", "roleuserest", "roleuserfch", "clear"]) ? $_GET["orderBy"] : $this->orderBy;
            
            if ($this->orderBy === "clear") {
                $this->orderBy = "";
            }

            $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
            $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
            $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
        }

        private function getParamsFromContext(): void
        {
            $this->partialName = Context::getContextByKey("rolesUsuarios_partialName");
            $this->status = Context::getContextByKey("rolesUsuarios_status");
            $this->orderBy = Context::getContextByKey("rolesUsuarios_orderBy");
            $this->orderDescending = boolval(Context::getContextByKey("rolesUsuarios_orderDescending"));
            $this->pageNumber = intval(Context::getContextByKey("rolesUsuarios_page"));
            $this->itemsPerPage = intval(Context::getContextByKey("rolesUsuarios_itemsPerPage"));
            
            if ($this->pageNumber < 1) $this->pageNumber = 1;
            
            if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
        }

        private function setParamsToContext(): void
        {
            Context::setContext("rolesUsuarios_partialName", $this->partialName, true);
            Context::setContext("rolesUsuarios_status", $this->status, true);
            Context::setContext("rolesUsuarios_orderBy", $this->orderBy, true);
            Context::setContext("rolesUsuarios_orderDescending", $this->orderDescending, true);
            Context::setContext("rolesUsuarios_page", $this->pageNumber, true);
            Context::setContext("rolesUsuarios_itemsPerPage", $this->itemsPerPage, true);
        }

        private function setParamsToDataView(): void
        {
            $this->viewData["partialName"] = $this->partialName;
            $this->viewData["status"] = $this->status;
            $this->viewData["orderBy"] = $this->orderBy;
            $this->viewData["orderDescending"] = $this->orderDescending;
            $this->viewData["pageNum"] = $this->pageNumber;
            $this->viewData["itemsPerPage"] = $this->itemsPerPage;
            $this->viewData["rolesUsuariosCount"] = $this->rolesUsuariosCount;
            $this->viewData["pages"] = $this->pages;
            $this->viewData["rolesUsuarios"] = $this->rolesUsuarios;

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
                $this->rolesUsuariosCount,
                $this->itemsPerPage,
                $this->pageNumber,
                "index.php?page=RolesUsuarios_RolesUsuarios",
                "RolesUsuarios_RolesUsuarios"
            );
            $this->viewData["pagination"] = $pagination;
        }
    }
?>
