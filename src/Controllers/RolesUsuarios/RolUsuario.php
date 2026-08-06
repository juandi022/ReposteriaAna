<?php

namespace Controllers\RolesUsuarios;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\RolesUsuarios\RolesUsuarios as RolesUsuariosDao;
use Dao\Roles\Roles as RolesDao;
use Dao\Usuarios\Usuarios as UsuariosDao;
use Utilities\Site;
use Utilities\Validators;

class RolUsuario extends PrivateController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de %s %s",
        "INS" => "Nuevo Rol de Usuario",
        "UPD" => "Editar %s %s",
        "DEL" => "Eliminar %s %s"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $rolUsuario = [
        "usercod" => 0,
        "rolescod" => "",
        "roleuserest" => "ACT",
        "roleuserfch" => "",
        "roleuserexp" => ""
    ];
    private $rolUsuario_xss_token = "";

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
            Renderer::render("rolesusuarios/rolUsuario", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=RolesUsuarios_RolesUsuarios",
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
                $this->rolUsuario = RolesUsuariosDao::getRolUsuarioById(
                    intval($_GET["usercod"]),
                    $_GET["rolescod"]
                );
                if (!$this->rolUsuario) {
                    throw new \Exception("No se encontró el Rol de Usuario", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad invalida", 1);
        }
    }

    private function validateData()
    {
        $errors = [];
        $this->rolUsuario_xss_token = $_POST["rolUsuario_xss_token"] ?? "";
        $this->rolUsuario["usercod"] = intval($_POST["usercod"] ?? "");
        $this->rolUsuario["rolescod"] = strval($_POST["rolescod"] ?? "");
        $this->rolUsuario["roleuserest"] = strval($_POST["roleuserest"] ?? "");
        $this->rolUsuario["roleuserfch"] = strval($_POST["roleuserfch"] ?? "");
        $this->rolUsuario["roleuserexp"] = strval($_POST["roleuserexp"] ?? "");

        if ($this->mode === "DEL") {
            return $this->rolUsuario["usercod"] > 0
                && !Validators::IsEmpty($this->rolUsuario["rolescod"]);
        }

        if ($this->rolUsuario["usercod"] <= 0) {
            $errors["usercod_error"] = "El código del usuario es requerido";
        }

        if (Validators::IsEmpty($this->rolUsuario["rolescod"])) {
            $errors["rolescod_error"] = "El código del rol es requerido";
        }

        if (!in_array($this->rolUsuario["roleuserest"], ["ACT", "INA"])) {
            $errors["roleuserest_error"] = "El estado del rol de usuario es invalido";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->rolUsuario[$key] = $value;
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
        $result = RolesUsuariosDao::insertRolUsuario(
            $this->rolUsuario["usercod"],
            $this->rolUsuario["rolescod"],
            $this->rolUsuario["roleuserest"],
            $this->parseDateTime($this->rolUsuario["roleuserfch"]),
            $this->parseDateTime($this->rolUsuario["roleuserexp"])
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=RolesUsuarios_RolesUsuarios",
                "Rol de Usuario creado exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        $result = RolesUsuariosDao::updateRolUsuario(
            $this->rolUsuario["usercod"],
            $this->rolUsuario["rolescod"],
            $this->rolUsuario["roleuserest"],
            $this->parseDateTime($this->rolUsuario["roleuserfch"]),
            $this->parseDateTime($this->rolUsuario["roleuserexp"])
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=RolesUsuarios_RolesUsuarios",
                "Rol de Usuario actualizado exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        $result = RolesUsuariosDao::deleteRolUsuario(
            $this->rolUsuario["usercod"],
            $this->rolUsuario["rolescod"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=RolesUsuarios_RolesUsuarios",
                "Rol de Usuario eliminado exitosamente"
            );
        }
    }

    private function setViewData(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["rolUsuario_xss_token"] = $this->rolUsuario_xss_token;
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->rolUsuario["usercod"],
            $this->rolUsuario["rolescod"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $this->rolUsuario["roleuserfch"] = $this->formatDateTimeForInput($this->rolUsuario["roleuserfch"]);
        $this->rolUsuario["roleuserexp"] = $this->formatDateTimeForInput($this->rolUsuario["roleuserexp"]);

        $rolUsuarioStatusKey = "rolUsuarioStatus_" . strtolower($this->rolUsuario["roleuserest"]);
        $this->rolUsuario[$rolUsuarioStatusKey] = "selected";

        $this->viewData["rolUsuario"] = $this->rolUsuario;

        $this->setOptionsToDataView();
    }

    private function setOptionsToDataView(): void
    {
        $usuariosOptions = UsuariosDao::getUsuariosForOptions();
        foreach ($usuariosOptions as &$usr) {
            $usr["selected"] = ($usr["usercod"] == $this->rolUsuario["usercod"]) ? "selected" : "";
        }
        unset($usr);
        $this->viewData["usuariosOptions"] = $usuariosOptions;

        $rolesOptions = RolesDao::getRolesForOptions();
        foreach ($rolesOptions as &$rol) {
            $rol["selected"] = ($rol["rolescod"] === $this->rolUsuario["rolescod"]) ? "selected" : "";
        }
        unset($rol);
        $this->viewData["rolesOptions"] = $rolesOptions;
    }

    private function parseDateTime($value): \DateTime
    {
        if (empty($value)) {
            return new \DateTime();
        }

        return new \DateTime($value);
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
