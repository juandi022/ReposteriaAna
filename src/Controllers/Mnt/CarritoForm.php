<?php

namespace Controllers\Mnt;

use Controllers\PublicController;
use Dao\Mantenimiento\Carrito as CarritoDao;
use Utilities\Carrito as CarritoSession;
use Utilities\Site;
use Views\Renderer;

const CARRITO_LIST_URI = "index.php?page=Mnt-CarritoList";
const CARRITO_FORM_TEMPLATE = "mnt/form_carrito";
const CARRITO_XSS_TOKEN = "carrito_form";

class CarritoForm extends PublicController
{
    private string $mode = "NAS"; // Not Assigned as default
    private array $modes = [
        "INS" => "Agregar al Carrito",
        "DEL" => "Eliminar del Carrito"
    ];

    private array $item = [
        "id_producto" => 0,
        "nombre_producto" => "",
        "cantidad" => 1,
        "cantidadEnCarrito" => 0,
        "precio" => 0.00,
        "stock" => 0,
        "disponible" => 0
    ];

    private array $errors = [];
    private string $xssToken = "";

    public function run(): void
    {
        try {
            $this->getQueryParams();
            if ($this->isPostBack()) {
                if ($this->validarPostData()) {
                    $this->procesarPost();
                }
            }
            $this->mostrarVista();
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            Site::redirectToWithMsg(CARRITO_LIST_URI, $ex->getMessage());
        }
    }

    private function getQueryParams(): void
    {
        $this->mode = $_GET["mode"] ?? "INS";
        if (!isset($this->modes[$this->mode])) {
            throw new \Exception("Modo no adecuado");
        }

        $this->item["id_producto"] = intval($_GET["id_producto"] ?? 0);
        if ($this->item["id_producto"] === 0) {
            throw new \Exception("ID de producto es inválido.");
        }
        $producto = CarritoDao::getProductoById($this->item["id_producto"]);
        if (empty($producto)) {
            throw new \Exception("Producto no encontrado.");
        }
        $this->item["nombre_producto"] = $producto["nombre"];
        $this->item["precio"] = floatval($producto["precio"]);
        $this->item["cantidadEnCarrito"] = CarritoSession::cantidadEnCarrito($this->item["id_producto"]);

        if ($this->mode === "DEL") {
            $this->item["cantidad"] = $this->item["cantidadEnCarrito"];
            return;
        }

        $this->item["stock"] = intval($producto["stock"]);
        $this->item["cantidad"] = 1;
        $this->item["disponible"] = $this->item["stock"] - $this->item["cantidadEnCarrito"];
        if ($this->item["disponible"] < 0) {
            $this->item["disponible"] = 0;
        }
    }

    private function validarPostData(): bool
    {
        $tmp_mode = $_POST["mode"] ?? 'NAP';
        if (!isset($this->modes[$tmp_mode])) {
            throw new \Exception("Error modo no es válido");
        }

        // Seguridad XSS: validación de token CSRF/XSS
        $tmp_xssToken = $_POST["xssToken"] ?? 'NAP';
        $local_xssToken = $_SESSION[CARRITO_XSS_TOKEN] ?? 'NAP';
        if ($tmp_xssToken === 'NAP' || $local_xssToken === 'NAP' || $local_xssToken !== $tmp_xssToken) {
            throw new \Exception("No paso la prueba de XSS Script Forgery");
        }

        $this->item["id_producto"] = intval($_POST["id_producto"] ?? 0);
        if ($this->item["id_producto"] === 0) {
            throw new \Exception("ID de producto es inválido.");
        }

        if ($this->mode === "DEL") {
            return true;
        }

        $cantidad = intval($_POST["cantidad"] ?? 0);
        if ($cantidad < 1) {
            $this->addViewError("La cantidad debe ser al menos 1", "cantidad");
        }
        if ($this->item["stock"] <= 0) {
            $this->addViewError("El producto no tiene stock disponible", "cantidad");
        } elseif ($cantidad > $this->item["disponible"]) {
            $this->addViewError(
                sprintf(
                    "Cantidad no permitida, solo hay %d unidades disponibles (ya tiene %d en el carrito).",
                    $this->item["disponible"],
                    $this->item["cantidadEnCarrito"]
                ),
                "cantidad"
            );
        }
        $this->item["cantidad"] = $cantidad;

        return count($this->errors) <= 0;
    }

    private function procesarPost(): void
    {
        switch ($this->mode) {
            case "INS":
                $producto = CarritoDao::getProductoById($this->item["id_producto"]);
                if (empty($producto)) {
                    throw new \Exception("Producto no encontrado.");
                }
                CarritoSession::agregar(
                    $this->item["id_producto"],
                    $producto["nombre"],
                    floatval($producto["precio"]),
                    intval($producto["stock"]),
                    $this->item["cantidad"]
                );
                Site::redirectToWithMsg(CARRITO_LIST_URI, "Producto agregado al carrito satisfactoriamente!");
                break;
            case "DEL":
                CarritoSession::eliminar($this->item["id_producto"]);
                Site::redirectToWithMsg(CARRITO_LIST_URI, "Producto eliminado del carrito satisfactoriamente!");
                break;
        }
    }

    private function mostrarVista(): void
    {
        $dataView = [];
        $dataView["mode"] = $this->mode;
        $dataView["modeDsc"] = $this->modes[$this->mode];
        $dataView["modoAgregar"] = ($this->mode === "INS");
        $dataView["id_producto"] = $this->item["id_producto"];
        $dataView["nombre_producto"] = $this->item["nombre_producto"];
        $dataView["precio"] = $this->item["precio"];
        $dataView["cantidad"] = $this->item["cantidad"];
        $dataView["cantidadEnCarrito"] = $this->item["cantidadEnCarrito"];
        $dataView["stock"] = $this->item["stock"];
        $dataView["disponible"] = $this->item["disponible"];

        if (count($this->errors)) {
            foreach ($this->errors as $scope => $errors) {
                $dataView['error_' . $scope] = implode("<br/>", $errors);
            }
        }

        $dataView["xssToken"] = $this->generarXssToken();

        Renderer::render(CARRITO_FORM_TEMPLATE, $dataView);
    }

    private function generarXssToken(): string
    {
        $seed = rand(100000, 999999);
        $dateTime = microtime(true);
        $toHash = md5("carrito_form_token_" . $seed . $dateTime);
        $_SESSION[CARRITO_XSS_TOKEN] = $toHash;
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
