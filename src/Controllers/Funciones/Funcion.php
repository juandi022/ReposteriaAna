<?php

namespace Controllers\Funciones;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\Funciones\Funciones as FuncionesDao;
use Utilities\Site;
use Utilities\Validators;

class Funcion extends PrivateController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de %s %s",
        "INS" => "Nueva Función",
        "UPD" => "Editar %s %s",
        "DEL" => "Eliminar %s %s"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $funciones = [
        "fncod" => "",
        "fndsc" => "",
        "fnest" => "ACT",
        "fntyp" => ""
    ];
    private $funciones_xss_token = "";

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
            Renderer::render("funciones/funcion", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Funciones_Funciones",
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
                $this->funciones = FuncionesDao::getFuncionById($_GET["fncod"]);
                if (!$this->funciones) {
                    throw new \Exception("No se encontró la Función", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad invalida", 1);
        }
    }

    private function validateData()
    {
        $errors = [];
        $this->funciones_xss_token = $_POST["funciones_xss_token"] ?? "";
        $this->funciones["fncod"] = strval($_POST["fncod"] ?? "");
        $this->funciones["fndsc"] = strval($_POST["fndsc"] ?? "");
        $this->funciones["fnest"] = strval($_POST["fnest"] ?? "");
        $this->funciones["fntyp"] = strval($_POST["fntyp"] ?? "");

        if ($this->mode === "DEL") {
            return !Validators::IsEmpty($this->funciones["fncod"]);
        }

        if (Validators::IsEmpty($this->funciones["fncod"])) {
            $errors["fncod_error"] = "El código de la función es requerido";
        }

        if (Validators::IsEmpty($this->funciones["fndsc"])) {
            $errors["fndsc_error"] = "La descripción de la función es requerida";
        }

        if (!in_array($this->funciones["fnest"], ["ACT", "INA"])) {
            $errors["fnest_error"] = "El estado de la función es invalido";
        }

        if (Validators::IsEmpty($this->funciones["fntyp"])) {
            $errors["fntyp_error"] = "El tipo de la función es inválido";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->funciones[$key] = $value;
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
        $result = FuncionesDao::insertFuncion(
            $this->funciones["fncod"],
            $this->funciones["fndsc"],
            $this->funciones["fnest"],
            $this->funciones["fntyp"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Funciones_Funciones",
                "Función creada exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        $result = FuncionesDao::updateFuncion(
            $this->funciones["fncod"],
            $this->funciones["fndsc"],
            $this->funciones["fnest"],
            $this->funciones["fntyp"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Funciones_Funciones",
                "Función actualizada exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        $result = FuncionesDao::deleteFuncion($this->funciones["fncod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Funciones_Funciones",
                "Función Eliminada exitosamente"
            );
        }
    }
    private function setViewData(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["funciones_xss_token"] = $this->funciones_xss_token;
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->funciones["fncod"],
            $this->funciones["fndsc"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $funcionesStatusKey = "funcionesStatus_" . strtolower($this->funciones["fnest"]);
        $this->funciones[$funcionesStatusKey] = "selected";

        $this->viewData["funciones"] = $this->funciones;
    }
}
