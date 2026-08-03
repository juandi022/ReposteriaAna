<?php

namespace Controllers\FuncionesRoles;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\FuncionesRoles\FuncionesRoles as FuncionesRolesDao;
use Dao\Roles\Roles as RolesDao;
use Dao\Funciones\Funciones as FuncionesDao;
use Utilities\Site;
use Utilities\Validators;

class FuncionRol extends PrivateController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de %s %s",
        "INS" => "Nueva Funcion de Rol",
        "UPD" => "Editar %s %s",
        "DEL" => "Eliminar %s %s"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $funcionRol = [
        "rolescod" => "",
        "fncod" => "",
        "fnrolest" => "ACT",
        "fnexp" => ""
    ];
    private $funcionRol_xss_token = "";

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
            Renderer::render("funcionesroles/funcionrol", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=FuncionesRoles_FuncionesRoles",
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
                $this->funcionRol = FuncionesRolesDao::getFuncionesRolesById(
                    $_GET["rolescod"],
                    $_GET["fncod"]
                );
                if (!$this->funcionRol) {
                    throw new \Exception("No se encontró la Función de Rol", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad invalida", 1);
        }
    }

    private function validateData()
    {
        $errors = [];
        $this->funcionRol_xss_token = $_POST["funcionRol_xss_token"] ?? "";
        $this->funcionRol["rolescod"] = strval($_POST["rolescod"] ?? "");
        $this->funcionRol["fncod"] = strval($_POST["fncod"] ?? "");
        $this->funcionRol["fnrolest"] = strval($_POST["fnrolest"] ?? "");
        $this->funcionRol["fnexp"] = strval($_POST["fnexp"] ?? "");

        if ($this->mode === "DEL") {
            return !Validators::IsEmpty($this->funcionRol["rolescod"])
                && !Validators::IsEmpty($this->funcionRol["fncod"]);
        }

        if (Validators::IsEmpty($this->funcionRol["rolescod"])) {
            $errors["rolescod_error"] = "El código del rol es requerido";
        }

        if (Validators::IsEmpty($this->funcionRol["fncod"])) {
            $errors["fncod_error"] = "El código de la función es requerido";
        }

        if (!in_array($this->funcionRol["fnrolest"], ["ACT", "INA"])) {
            $errors["fnrolest_error"] = "El estado de la función de rol es invalido";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->funcionRol[$key] = $value;
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
        $result = FuncionesRolesDao::insertFuncionesRoles(
            $this->funcionRol["rolescod"],
            $this->funcionRol["fncod"],
            $this->funcionRol["fnrolest"],
            $this->funcionRol["fnexp"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=FuncionesRoles_FuncionesRoles",
                "Función de Rol creada exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        $result = FuncionesRolesDao::updateFuncionesRoles(
            $this->funcionRol["rolescod"],
            $this->funcionRol["fncod"],
            $this->funcionRol["fnrolest"],
            $this->funcionRol["fnexp"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=FuncionesRoles_FuncionesRoles",
                "Función de Rol actualizada exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        $result = FuncionesRolesDao::deleteRol(
            $this->funcionRol["rolescod"],
            $this->funcionRol["fncod"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=FuncionesRoles_FuncionesRoles",
                "Función de Rol eliminada exitosamente"
            );
        }
    }

    private function setViewData(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["funcionRol_xss_token"] = $this->funcionRol_xss_token;
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->funcionRol["rolescod"],
            $this->funcionRol["fncod"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $this->funcionRol["fnexp"] = $this->formatDateTimeForInput($this->funcionRol["fnexp"]);

        $funcionRolStatusKey = "funcionRolStatus_" . strtolower($this->funcionRol["fnrolest"]);
        $this->funcionRol[$funcionRolStatusKey] = "selected";

        $this->viewData["funcionRol"] = $this->funcionRol;

        $this->setOptionsToDataView();
    }

    private function setOptionsToDataView(): void
    {
        $rolesOptions = RolesDao::getRolesForOptions();
        foreach ($rolesOptions as &$rol) {
            $rol["selected"] = ($rol["rolescod"] === $this->funcionRol["rolescod"]) ? "selected" : "";
        }
        unset($rol);
        $this->viewData["rolesOptions"] = $rolesOptions;

        $funcionesOptions = FuncionesDao::getFuncionesForOptions();
        foreach ($funcionesOptions as &$fn) {
            $fn["selected"] = ($fn["fncod"] === $this->funcionRol["fncod"]) ? "selected" : "";
        }
        unset($fn);
        $this->viewData["funcionesOptions"] = $funcionesOptions;
    }

    private function formatDateTimeForInput($value): string
    {
        if (empty($value)) {
            return "";
        }

        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d\TH:i');
        }

        if (is_string($value)) {
            $date = new \DateTime($value);
            return $date->format('Y-m-d\TH:i');
        }

        return "";
    }
}
