<?php

namespace Controllers\Roles;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\Roles\Roles as RolesDao;
use Utilities\Site;
use Utilities\Validators;

class Rol extends PrivateController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de %s %s",
        "INS" => "Nuevo Rol",
        "UPD" => "Editar %s %s",
        "DEL" => "Eliminar %s %s"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $roles = [
        "rolescod" => "",
        "rolesdsc" => "",
        "rolesest" => "ACT"
    ];
    private $roles_xss_token = "";

    public function run(): void
    {
        try {
            $this->getData();
            if ($this->isPostBack()) {
                if ($this->validateData()) {
                    $this->handlePostAction();
                }
            }
            $this->setViewData();
            Renderer::render("roles/rol", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_Roles",
                $ex->getMessage()
            );
        }
    }

    private function getData()
    {
        $this->mode = $_GET["mode"] ?? "NOF";
        if (isset($this->modeDescriptions[$this->mode])) {
            $this->readonly = $this->mode === "DEL" ? "readonly" : "";
            $this->showCommitBtn = $this->mode !== "DSP";
            if ($this->mode !== "INS") {
                $this->roles = RolesDao::getRolesById($_GET["rolescod"]);
                if (!$this->roles) {
                    throw new \Exception("No se encontró el Producto", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad invalida", 1);
        }
    }

    private function validateData()
    {
        $errors = [];
        $this->roles_xss_token = $_POST["roles_xss_token"] ?? "";
        $this->roles["rolescod"] = strval($_POST["rolescod"] ?? "");
        $this->roles["rolesdsc"] = strval($_POST["rolesdsc"] ?? "");
        $this->roles["rolesest"] = strval($_POST["rolesest"] ?? "");

        if ($this->mode === "DEL") {
            return !Validators::IsEmpty($this->roles["rolescod"]);
        }

        if (Validators::IsEmpty($this->roles["rolescod"])) {
            $errors["rolescod_error"] = "El código del rol es requerido";
        }

        if (Validators::IsEmpty($this->roles["rolesdsc"])) {
            $errors["rolesdsc_error"] = "La descripción del rol es requerida";
        }

        if (!in_array($this->roles["rolesest"], ["ACT", "INA"])) {
            $errors["rolesest_error"] = "El estado del rol es invalido";
        }


        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->roles[$key] = $value;
            }
            return false;
        }
        return true;
    }

    private function handlePostAction()
    {
        switch ($this->mode) {
            case "INS":
                $this->handleInsert();
                break;
            case "UPD":
                $this->handleUpdate();
                break;
            case "DEL":
                $this->handleDelete();
                break;
            default:
                throw new \Exception("Modo invalido", 1);
                break;
        }
    }

    private function handleInsert()
    {
        $result = RolesDao::insertRol(
            $this->roles["rolescod"],
            $this->roles["rolesdsc"],
            $this->roles["rolesest"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_Roles",
                "Rol creado exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        $result = RolesDao::updateRol(
            $this->roles["rolescod"],
            $this->roles["rolesdsc"],
            $this->roles["rolesest"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_Roles",
                "Rol actualizado exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        $result = RolesDao::deleteRol($this->roles["rolescod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_Roles",
                "Rol Eliminado exitosamente"
            );
        }
    }
    private function setViewData(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["roles_xss_token"] = $this->roles_xss_token;
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->roles["rolescod"],
            $this->roles["rolesdsc"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $rolesStatusKey = "rolesStatus_" . strtolower($this->roles["rolesest"]);
        $this->roles[$rolesStatusKey] = "selected";

        $this->viewData["roles"] = $this->roles;
    }
}
