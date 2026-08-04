<?php

namespace Controllers\Compras;

use Controllers\PrivateController;
use Dao\Compras\Proveedores as ProveedoresDao;
use Exception;
use Utilities\Site;
use Views\Renderer;

const PROVEEDORES_LIST_VIEW_URI = "index.php?page=Compras-ProveedoresList";
const PROVEEDOR_FORM_VIEW_URI = "index.php?page=Compras-ProveedorForm";
const PROVEEDOR_FORM_TEMPLATE = "compras/proveedor_form";
const PROVEEDOR_FORM_XSS_TOKEN = "proveedor_form";

class ProveedorForm extends PrivateController
{
    private string $mode = "NAS";
    private array $modes = [
        "INS" => "Creando Nuevo Proveedor",
        "UPD" => "Editar Proveedor",
        "DEL" => "Eliminar Proveedor",
        "DSP" => "Proveedor"
    ];
    private array $proveedor = [
        "id_proveedor" => null,
        "nombre" => "",
        "contacto" => "",
        "telefono" => "",
        "correo" => "",
        "direccion" => "",
        "estado" => "Activo"
    ];
    private array $errors = [];

    public function run(): void
    {
        try {
            $this->getQueryParams();
            if ($this->isPostBack()) {
                $validado = $this->validarPostData();
                if ($validado) {
                    $this->procesarPost();
                }
            }
            $this->mostrarVista();
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            Site::redirectToWithMsg(PROVEEDORES_LIST_VIEW_URI, $ex->getMessage());
        }
    }

    private function getQueryParams(): void
    {
        $this->mode = $_GET["mode"] ?? "NAS";
        if (!isset($this->modes[$this->mode])) {
            throw new Exception("Modo no adecuado");
        }
        if ($this->mode !== "INS") {
            $this->proveedor["id_proveedor"] = intval($_GET["id_proveedor"] ?? 0);
            if ($this->proveedor["id_proveedor"] <= 0) {
                throw new Exception("ID de proveedor inválido");
            }
            $proveedorFromDb = ProveedoresDao::getById($this->proveedor["id_proveedor"]);
            if (count($proveedorFromDb) === 0) {
                throw new Exception("El proveedor solicitado no existe");
            }
            $this->proveedor = array_merge($this->proveedor, $proveedorFromDb);
        }
    }

    private function validarPostData(): bool
    {
        $tmpMode = $_POST["mode"] ?? "NAP";
        if (!isset($this->modes[$tmpMode]) || $tmpMode !== $this->mode) {
            throw new Exception("Error modo no es válido");
        }

        $tmpToken = strval($_POST["xssToken"] ?? "");
        $localToken = strval($_SESSION[PROVEEDOR_FORM_XSS_TOKEN] ?? "");
        if ($tmpToken === "" || $localToken === "" || !hash_equals($localToken, $tmpToken)) {
            throw new Exception("No pasó la prueba de XSS Script Forgery");
        }

        $this->proveedor["nombre"] = $this->sanitizar($_POST["nombre"] ?? "");
        $this->proveedor["contacto"] = $this->sanitizar($_POST["contacto"] ?? "");
        $this->proveedor["telefono"] = $this->sanitizar($_POST["telefono"] ?? "");
        $this->proveedor["correo"] = trim(strval($_POST["correo"] ?? ""));
        $this->proveedor["direccion"] = $this->sanitizar($_POST["direccion"] ?? "");
        $this->proveedor["estado"] = $this->sanitizar($_POST["estado"] ?? "Activo");

        if ($this->proveedor["nombre"] === "") {
            $this->addViewError("Campo requiere de un valor", "nombre");
        }
        if ($this->proveedor["correo"] !== "" && !filter_var($this->proveedor["correo"], FILTER_VALIDATE_EMAIL)) {
            $this->addViewError("El correo no tiene un formato válido", "correo");
        }
        $this->proveedor["correo"] = htmlspecialchars($this->proveedor["correo"], ENT_QUOTES, "UTF-8");
        if (!in_array($this->proveedor["estado"], ["Activo", "Inactivo"])) {
            $this->addViewError("El estado seleccionado no es válido", "estado");
        }

        return count($this->errors) === 0;
    }

    private function procesarPost(): void
    {
        switch ($this->mode) {
            case "INS":
                if (ProveedoresDao::create(
                    $this->proveedor["nombre"],
                    $this->proveedor["contacto"],
                    $this->proveedor["telefono"],
                    $this->proveedor["correo"],
                    $this->proveedor["direccion"],
                    $this->proveedor["estado"]
                )) {
                    Site::redirectToWithMsg(PROVEEDORES_LIST_VIEW_URI, "Proveedor creado satisfactoriamente");
                }
                $this->addViewError("No se pudo insertar el proveedor");
                break;
            case "UPD":
                if (ProveedoresDao::update(
                    intval($this->proveedor["id_proveedor"]),
                    $this->proveedor["nombre"],
                    $this->proveedor["contacto"],
                    $this->proveedor["telefono"],
                    $this->proveedor["correo"],
                    $this->proveedor["direccion"],
                    $this->proveedor["estado"]
                )) {
                    Site::redirectToWithMsg(PROVEEDORES_LIST_VIEW_URI, "Proveedor actualizado satisfactoriamente");
                }
                $this->addViewError("No se actualizó el proveedor");
                break;
            case "DEL":
                if (ProveedoresDao::delete(intval($this->proveedor["id_proveedor"]))) {
                    Site::redirectToWithMsg(PROVEEDORES_LIST_VIEW_URI, "Proveedor eliminado satisfactoriamente");
                }
                $this->addViewError("No se eliminó el proveedor");
                break;
        }
    }

    private function mostrarVista(): void
    {
        $viewData = [];
        $viewData["mode"] = $this->mode;
        $viewData["modeDsc"] = $this->modes[$this->mode];
        $viewData["proveedor"] = $this->proveedor;
        $viewData["id_proveedor"] = $this->proveedor["id_proveedor"];
        $viewData["editable"] = in_array($this->mode, ["INS", "UPD"]);
        $viewData["modoEliminar"] = $this->mode === "DEL";
        $viewData["readonly"] = in_array($this->mode, ["DSP", "DEL"]) ? "readonly" : "";
        $viewData["estadoDisabled"] = in_array($this->mode, ["DSP", "DEL"]) ? "disabled" : "";
        $viewData["estado_Activo"] = $this->proveedor["estado"] === "Activo" ? "selected" : "";
        $viewData["estado_Inactivo"] = $this->proveedor["estado"] === "Inactivo" ? "selected" : "";
        foreach ($this->errors as $scope => $errors) {
            $viewData["error_" . $scope] = $errors;
        }
        foreach (["global", "nombre", "correo", "estado"] as $scope) {
            if (!isset($viewData["error_" . $scope])) {
                $viewData["error_" . $scope] = [];
            }
        }
        $viewData["xssToken"] = $this->generarXssToken();
        Renderer::render(PROVEEDOR_FORM_TEMPLATE, $viewData);
    }

    private function sanitizar($value): string
    {
        return htmlspecialchars(trim(strval($value)), ENT_QUOTES, "UTF-8");
    }

    private function generarXssToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[PROVEEDOR_FORM_XSS_TOKEN] = $token;
        return $token;
    }

    private function addViewError(string $message, string $context = "global"): void
    {
        if (!isset($this->errors[$context])) {
            $this->errors[$context] = [];
        }
        $this->errors[$context][] = $message;
    }
}

?>
