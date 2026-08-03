<?php

namespace Controllers\Usuarios;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\Usuarios\Usuarios as UsuariosDao;
use DateTime;
use Utilities\Site;
use Utilities\Validators;

class Usuario extends PrivateController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de %s %s",
        "INS" => "Nuevo Usuario",
        "UPD" => "Editar %s %s",
        "DEL" => "Eliminar %s %s"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $usuario = [
        "usercod" => 0,
        "useremail" => "",
        "username" => "",
        "userpswd" => "",
        "userfching" => "",
        "userpswdest" => "ACT",
        "userpswdexp" => "",
        "userest" => "ACT",
        "useractcod" => "",
        "userpswdchg" => "",
        "usertipo" => ""
    ];
    private $usuario_xss_token = "";

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
            Renderer::render("usuarios/usuario", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_Usuarios",
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
                $this->usuario = UsuariosDao::getUsuarioById(intval($_GET["usercod"]));
                if (!$this->usuario) {
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
        $this->usuario_xss_token = $_POST["usuario_xss_token"] ?? "";
        $this->usuario["usercod"] = intval($_POST["usercod"] ?? "");
        $this->usuario["useremail"] = strval($_POST["useremail"] ?? "");
        $this->usuario["username"] = strval($_POST["username"] ?? "");
        $this->usuario["userpswd"] = strval($_POST["userpswd"] ?? "");
        
        $this->usuario["userpswdest"] = strval($_POST["userpswdest"] ?? "");

        $this->usuario["userest"] = strval($_POST["userest"] ?? "");
        $this->usuario["useractcod"] = strval($_POST["useractcod"] ?? "");
        $this->usuario["userpswdchg"] = strval($_POST["userpswdchg"] ?? "");
        $this->usuario["usertipo"] = strval($_POST["usertipo"] ?? "");

        if ($this->mode === "DEL") {
            return $this->usuario["usercod"] > 0;
        }

        if (Validators::IsEmpty($this->usuario["useremail"])) {
            $errors["useremail_error"] = "El email del usuario es requerido";
        }

        if (Validators::IsEmpty($this->usuario["username"])) {
            $errors["username_error"] = "El nombre del usuario es requerido";
        }

        if (Validators::IsEmpty($this->usuario["userpswd"])) {
            $errors["userpswd_error"] = "La contraseña del usuario es requerida";
        }

        if (Validators::IsEmpty($this->usuario["userpswdest"])) {
            $errors["userpswdest_error"] = "El estado de la contraseña del usuario es requerido";
        }

        if (!in_array($this->usuario["userest"], ["ACT", "INA"])) {
            $errors["userest_error"] = "El estado del usuario es invalido";
        }

        if (Validators::IsEmpty($this->usuario["useractcod"])) {
            $errors["useractcod_error"] = "El estado de la contraseña del usuario es requerido";
        }

        if (!in_array($this->usuario["usertipo"], ["NOR", "CON", "CLI"])) {
            $errors["usertipo_error"] = "El tipo de usuario es invalido";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->usuario[$key] = $value;
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
        $result = UsuariosDao::insertUsuario(
            $this->usuario["useremail"],
            $this->usuario["username"],
            $this->usuario["userpswd"],
            new DateTime(),
            $this->usuario["userpswdest"],
            new DateTime(),
            $this->usuario["userest"],
            $this->usuario["useractcod"],
            $this->usuario["userpswdchg"],
            $this->usuario["usertipo"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_Usuarios",
                "Usuario creado exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        $result = UsuariosDao::updateUsuario(
            $this->usuario["usercod"],
            $this->usuario["useremail"],
            $this->usuario["username"],
            $this->usuario["userpswd"],
            new DateTime(),
            $this->usuario["userpswdest"],
            new DateTime(),
            $this->usuario["userest"],
            $this->usuario["useractcod"],
            $this->usuario["userpswdchg"],
            $this->usuario["usertipo"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_Usuarios",
                "Usuario actualizado exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        $result = UsuariosDao::deleteUsuario($this->usuario["usercod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_Usuarios",
                "Usuario Eliminado exitosamente"
            );
        }
    }
    private function setViewData(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["usuario_xss_token"] = $this->usuario_xss_token;
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->usuario["usercod"],
            $this->usuario["username"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $this->usuario["userfching"] = $this->formatDateTimeForInput($this->usuario["userfching"]);
        $this->usuario["userpswdexp"] = $this->formatDateTimeForInput($this->usuario["userpswdexp"]);

        $usuarioStatusKey = "usuarioStatus_" . strtolower($this->usuario["userest"]);
        $this->usuario[$usuarioStatusKey] = "selected";

        $this->viewData["usuario"] = $this->usuario;
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
