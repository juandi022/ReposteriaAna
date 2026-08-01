<?php

namespace Controllers\Mnt;

use Exception;
use Controllers\PublicController;
use Views\Renderer;
use Dao\Mantenimiento\Catalogo as CatalogoDao;
use Utilities\Site;
use Utilities\Validators;

const LIST_VIEW_URI = "index.php?page=Mnt-CatalogoList";
const FORM_VIEW_URI = "index.php?page=Mnt-CatalogoForm";
const FORM_VIEW_TEMPLATE = "mnt/form_catalogo";
const FORM_XSS_TOKEN = "catalogo_form";

class CatalogoForm extends PublicController
{
    private string $mode = "NAS"; // Not Assigned as default
    private array  $modes = [
        "INS" => "Creando Nuevo Producto",
        "UPD" => "Editar Producto ",
        "DEL" => "Eliminar Producto",
        "DSP" => "Producto "
    ];
    private array $producto = [
        "id_producto" => null,
        "nombre" => "",
        "categoria" => "",
        "descripcion" => "",
        "precio" => 0.00,
        "stock" => 0,
        "imagen" => "",
        "estado" => "Disponible"
    ];
    private $errors = [];

    private $xssToken = "";

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
            Site::redirectToWithMsg(
                LIST_VIEW_URI,
                "Algo inesperado ocurrió, vuelva a intentar. Si el error persiste contacte con el administrador."
            );
        }
    }

    private function getQueryParams()
    {
        $this->mode = $_GET["mode"] ?? "NAS";
        if (!isset($this->modes[$this->mode])) {
            throw new Exception("Modo no adecuado");
        }
        if ($this->mode !== "INS") {
            $this->producto["id_producto"] = intval($_GET["id_producto"] ?? 0);
            if ($this->producto["id_producto"] == 0) {
                throw new Exception("ID es inválido.");
            }
            $productoFromDb = CatalogoDao::getById($this->producto["id_producto"]);

            $this->producto["nombre"]      = $productoFromDb["nombre"];
            $this->producto["categoria"]   = $productoFromDb["categoria"];
            $this->producto["descripcion"] = $productoFromDb["descripcion"];
            $this->producto["precio"]      = $productoFromDb["precio"];
            $this->producto["stock"]       = $productoFromDb["stock"];
            $this->producto["imagen"]      = $productoFromDb["imagen"];
            $this->producto["estado"]      = $productoFromDb["estado"];
        }
    }

    private function validarPostData(): bool
    {
        $tmp_mode = $_POST["mode"] ?? 'NAP';
        if (!isset($this->modes[$tmp_mode])) {
            throw new Exception("Error modo no es válido");
        }

        // Seguridad XSS: validación de token CSRF/XSS
        $tmp_xssToken = $_POST["xssToken"] ?? 'NAP';
        if ($tmp_xssToken === 'NAP') {
            throw new Exception("No paso la prueba de XSS Script Forgery");
        }
        $local_xssToken = $_SESSION[FORM_XSS_TOKEN] ?? 'NAP';
        if ($local_xssToken === 'NAP') {
            throw new Exception("No paso la prueba de XSS Script Forgery");
        }
        if ($local_xssToken !== $tmp_xssToken) {
            throw new Exception("No paso la prueba de XSS Script Forgery");
        }

        // Seguridad XSS: sanitizar todos los campos de entrada
        $nombre     = htmlspecialchars(strval($_POST["nombre"] ?? ''), ENT_QUOTES, "UTF-8");
        $categoria  = htmlspecialchars(strval($_POST["categoria"] ?? ''), ENT_QUOTES, "UTF-8");
        $descripcion = htmlspecialchars(strval($_POST["descripcion"] ?? ''), ENT_QUOTES, "UTF-8");
        $precio     = floatval($_POST["precio"] ?? 0);
        $stock      = intval($_POST["stock"] ?? 0);
        $imagen     = htmlspecialchars(strval($_POST["imagen"] ?? ''), ENT_QUOTES, "UTF-8");
        $estado     = htmlspecialchars(strval($_POST["estado"] ?? 'Disponible'), ENT_QUOTES, "UTF-8");

        if (Validators::IsEmpty($nombre)) {
            $this->addViewError("Campo requiere de un valor", "nombre");
        }
        if (Validators::IsEmpty($categoria)) {
            $this->addViewError("Campo requiere de un valor", "categoria");
        }
        if (Validators::IsEmpty($descripcion)) {
            $this->addViewError("Campo requiere de un valor", "descripcion");
        }
        if ($precio <= 0) {
            $this->addViewError("El precio debe ser mayor a cero", "precio");
        }
        if ($stock < 0) {
            $this->addViewError("La cantidad en stock no puede ser negativa", "stock");
        }
        if (!in_array($estado, ["Disponible", "Agotado"])) {
            $this->addViewError("El estado seleccionado no es válido", "estado");
        }

        $this->producto["nombre"]      = $nombre;
        $this->producto["categoria"]   = $categoria;
        $this->producto["descripcion"] = $descripcion;
        $this->producto["precio"]      = $precio;
        $this->producto["stock"]       = $stock;
        $this->producto["imagen"]      = $imagen;
        $this->producto["estado"]      = $estado;

        return count($this->errors) <= 0;
    }

    private function procesarPost(): void
    {
        switch ($this->mode) {
            case "INS":
                if (CatalogoDao::create(
                    $this->producto["nombre"],
                    $this->producto["categoria"],
                    $this->producto["descripcion"],
                    $this->producto["precio"],
                    $this->producto["stock"],
                    $this->producto["imagen"],
                    $this->producto["estado"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_VIEW_URI, "producto creado satisfactoriamente!!!!");
                } else {
                    $this->addViewError("No se pudo insertar nuevo registro");
                }
                break;
            case "UPD":
                if (CatalogoDao::update(
                    $this->producto["id_producto"],
                    $this->producto["nombre"],
                    $this->producto["categoria"],
                    $this->producto["descripcion"],
                    $this->producto["precio"],
                    $this->producto["stock"],
                    $this->producto["imagen"],
                    $this->producto["estado"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_VIEW_URI, "producto actualizado satisfactoriamente!!!!");
                } else {
                    $this->addViewError("No se actualizó registro");
                }
                break;
            case "DEL":
                if (CatalogoDao::delete(
                    $this->producto["id_producto"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_VIEW_URI, "producto eliminado satisfactoriamente!!!!");
                } else {
                    $this->addViewError("No se eliminó registro");
                }
                break;
        }
    }

    private function mostrarVista()
    {
        $dataView = [];
        $dataView["mode"] = $this->mode;
        $dataView["modeDsc"] = ($this->mode === "INS") ?
            $this->modes[$this->mode]
            : sprintf(
                $this->modes[$this->mode],
                $this->producto["id_producto"],
                $this->producto["nombre"]
            );
        $dataView["producto"] = $this->producto;

        if (count($this->errors)) {
            foreach ($this->errors as $scope => $errors) {
                $dataView['error_' . $scope] = $errors;
            }
        }

        if (in_array($this->mode, ["DSP", "DEL"])) {
            $dataView["readonly"] = "readonly";
        }

        $dataView["editable"] = ($this->mode !== "DSP");

        $dataView["xssToken"] = $this->generarXssToken();

        Renderer::render(FORM_VIEW_TEMPLATE, $dataView);
    }

    private function generarXssToken()
    {
        $seed = rand(100000, 999999);
        $dateTime = microtime(true);
        $toHash = md5("producto_form_token_" . $seed . $dateTime);
        $_SESSION[FORM_XSS_TOKEN] = $toHash;
        return $toHash;
    }

    private function addViewError($errormsg, $context = "global")
    {
        if (isset($this->errors[$context])) {
            $this->errors[$context][] = $errormsg;
        } else {
            $this->errors[$context] = [$errormsg];
        }
    }
}