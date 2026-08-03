<?php

namespace Controllers\Mnt;

use Controllers\PrivateController;
use Dao\Mantenimiento\Carrito as CarritoDao;
use Utilities\Security;
use Utilities\Site;
use Views\Renderer;

const CARRITO_LIST_URI = "index.php?page=Mnt-CarritoList";
const CARRITO_FORM_TEMPLATE = "mnt/form_carrito";
const CARRITO_XSS_TOKEN = "carrito_form";

class CarritoForm extends PrivateController
{
    private string $mode = "NAS"; // Not Assigned as default
    private array $modes = [
        "INS" => "Agregar al Carrito",
        "DEL" => "Eliminar del Carrito"
    ];

    private array $item = [
        "id_detalle_carrito" => 0,
        "id_carrito" => 0,
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

        if ($this->mode === "DEL") {
            $this->item["id_detalle_carrito"] = intval($_GET["id_detalle_carrito"] ?? 0);
            if ($this->item["id_detalle_carrito"] === 0) {
                throw new \Exception("ID de detalle es inválido.");
            }
            $detalle = CarritoDao::getDetalleById($this->item["id_detalle_carrito"]);
            if (empty($detalle)) {
                throw new \Exception("Detalle de carrito no encontrado.");
            }
            $this->item["id_carrito"] = intval($detalle["id_carrito"]);
            $this->item["id_producto"] = intval($detalle["id_producto"]);
            $this->item["nombre_producto"] = $detalle["nombre_producto"];
            $this->item["cantidad"] = intval($detalle["cantidad"]);
            $this->item["precio"] = floatval($detalle["precio"]);
            return;
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
        $this->item["stock"] = intval($producto["stock"]);

        $carrito = CarritoDao::getOrCreateCarrito(Security::getUserId());
        $this->item["id_carrito"] = intval($carrito["id_carrito"] ?? 0);
        $detalle = CarritoDao::getDetalleProducto(
            $this->item["id_carrito"],
            $this->item["id_producto"]
        );
        $this->item["cantidadEnCarrito"] = intval($detalle["cantidad"] ?? 0);
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

        if ($this->mode === "DEL") {
            $this->item["id_detalle_carrito"] = intval($_POST["id_detalle_carrito"] ?? 0);
            if ($this->item["id_detalle_carrito"] === 0) {
                throw new \Exception("ID de detalle es inválido.");
            }
            return true;
        }

        $this->item["id_producto"] = intval($_POST["id_producto"] ?? 0);
        if ($this->item["id_producto"] === 0) {
            throw new \Exception("ID de producto es inválido.");
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
                $carrito = CarritoDao::getOrCreateCarrito(Security::getUserId());
                $this->item["id_carrito"] = intval($carrito["id_carrito"] ?? 0);
                $detalle = CarritoDao::getDetalleProducto(
                    $this->item["id_carrito"],
                    $this->item["id_producto"]
                );
                if (empty($detalle)) {
                    if (CarritoDao::addProducto(
                        $this->item["id_carrito"],
                        $this->item["id_producto"],
                        $this->item["cantidad"],
                        $this->item["precio"]
                    )) {
                        Site::redirectToWithMsg(CARRITO_LIST_URI, "Producto agregado al carrito satisfactoriamente!");
                    } else {
                        $this->addViewError("No se pudo agregar el producto al carrito");
                    }
                } else {
                    $nuevaCantidad = intval($detalle["cantidad"]) + $this->item["cantidad"];
                    if (CarritoDao::updateCantidad(
                        intval($detalle["id_detalle_carrito"]),
                        $nuevaCantidad
                    )) {
                        Site::redirectToWithMsg(CARRITO_LIST_URI, "Cantidad actualizada en el carrito satisfactoriamente!");
                    } else {
                        $this->addViewError("No se pudo actualizar la cantidad en el carrito");
                    }
                }
                break;
            case "DEL":
                if (CarritoDao::removeDetalle($this->item["id_detalle_carrito"])) {
                    Site::redirectToWithMsg(CARRITO_LIST_URI, "Producto eliminado del carrito satisfactoriamente!");
                } else {
                    $this->addViewError("No se pudo eliminar el producto del carrito");
                }
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
        $dataView["id_detalle_carrito"] = $this->item["id_detalle_carrito"];
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
