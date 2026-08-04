<?php

namespace Controllers\Compras;

use Controllers\PrivateController;
use Dao\Compras\Compras as ComprasDao;
use Dao\Compras\Proveedores as ProveedoresDao;
use Exception;
use Utilities\Site;
use Views\Renderer;

const COMPRAS_LIST_VIEW_URI = "index.php?page=Compras-ComprasList";
const COMPRA_FORM_VIEW_URI = "index.php?page=Compras-CompraForm";
const COMPRA_FORM_TEMPLATE = "compras/compra_form";
const COMPRA_FORM_XSS_TOKEN = "compra_form";

class CompraForm extends PrivateController
{
    private string $mode = "NAS";
    private array $modes = [
        "INS" => "Creando Nueva Compra",
        "UPD" => "Editar Compra",
        "DEL" => "Eliminar Compra",
        "DSP" => "Detalle de Compra"
    ];
    private array $compra = [
        "id_compra" => null,
        "id_proveedor" => 0,
        "proveedor" => "",
        "fecha" => "",
        "numero_factura" => "",
        "subtotal" => 0.00,
        "impuesto" => 0.00,
        "total" => 0.00,
        "estado" => "Borrador"
    ];
    private array $detalle = [
        "id_producto" => 0,
        "cantidad" => 1,
        "costo_unitario" => 0.00
    ];
    private array $errors = [];

    public function run(): void
    {
        try {
            $this->getQueryParams();
            if ($this->isPostBack()) {
                $this->validarToken();
                $action = $_POST["action"] ?? "SAVE";
                $this->procesarPost($action);
            }
            $this->mostrarVista();
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            Site::redirectToWithMsg(COMPRAS_LIST_VIEW_URI, $ex->getMessage());
        }
    }

    private function getQueryParams(): void
    {
        $this->mode = $_GET["mode"] ?? "NAS";
        if (!isset($this->modes[$this->mode])) {
            throw new Exception("Modo no adecuado");
        }
        if ($this->mode !== "INS") {
            $this->compra["id_compra"] = intval($_GET["id_compra"] ?? 0);
            if ($this->compra["id_compra"] <= 0) {
                throw new Exception("ID de compra inválido");
            }
            $compraFromDb = ComprasDao::getById($this->compra["id_compra"]);
            if (count($compraFromDb) === 0) {
                throw new Exception("La compra solicitada no existe");
            }
            $this->compra = array_merge($this->compra, $compraFromDb);
        } else {
            $this->compra["fecha"] = date("Y-m-d\TH:i");
        }
    }

    private function validarToken(): void
    {
        $tmpToken = strval($_POST["xssToken"] ?? "");
        $localToken = strval($_SESSION[COMPRA_FORM_XSS_TOKEN] ?? "");
        if ($tmpToken === "" || $localToken === "" || !hash_equals($localToken, $tmpToken)) {
            throw new Exception("No pasó la prueba de XSS Script Forgery");
        }
    }

    private function procesarPost(string $action): void
    {
        if ($action === "ADD_DETAIL") {
            $this->agregarDetalle();
            return;
        }
        if ($action === "DEL_DETAIL") {
            $idDetalle = intval($_POST["id_detalle_compra"] ?? 0);
            if ($idDetalle <= 0) {
                throw new Exception("Detalle de compra inválido");
            }
            ComprasDao::deleteDetalle(intval($this->compra["id_compra"]), $idDetalle);
            Site::redirectTo(COMPRA_FORM_VIEW_URI . "&mode=UPD&id_compra=" . $this->compra["id_compra"]);
        }
        if ($action === "CONFIRM") {
            ComprasDao::confirmar(intval($this->compra["id_compra"]));
            Site::redirectToWithMsg(COMPRAS_LIST_VIEW_URI, "Compra confirmada e inventario actualizado");
        }
        if ($action === "DELETE") {
            ComprasDao::delete(intval($this->compra["id_compra"]));
            Site::redirectToWithMsg(COMPRAS_LIST_VIEW_URI, "Compra eliminada satisfactoriamente");
        }

        $this->cargarCompraPost();
        if (count($this->errors) > 0) {
            return;
        }
        if ($this->mode === "INS") {
            $idCompra = ComprasDao::create(
                intval($this->compra["id_proveedor"]),
                $this->compra["numero_factura"],
                $this->normalizarFechaSql($this->compra["fecha"])
            );
            Site::redirectTo(COMPRA_FORM_VIEW_URI . "&mode=UPD&id_compra=" . $idCompra);
        }
        if ($this->mode === "UPD") {
            ComprasDao::update(
                intval($this->compra["id_compra"]),
                intval($this->compra["id_proveedor"]),
                $this->compra["numero_factura"],
                $this->normalizarFechaSql($this->compra["fecha"])
            );
            Site::redirectToWithMsg(COMPRAS_LIST_VIEW_URI, "Compra actualizada satisfactoriamente");
        }
    }

    private function cargarCompraPost(): void
    {
        $this->compra["id_proveedor"] = intval($_POST["id_proveedor"] ?? 0);
        $this->compra["numero_factura"] = htmlspecialchars(
            trim(strval($_POST["numero_factura"] ?? "")),
            ENT_QUOTES,
            "UTF-8"
        );
        $this->compra["fecha"] = strval($_POST["fecha"] ?? "");
        if ($this->compra["id_proveedor"] <= 0) {
            $this->addViewError("Debe seleccionar un proveedor", "id_proveedor");
        }
        if ($this->compra["numero_factura"] === "") {
            $this->addViewError("Debe ingresar el número de factura", "numero_factura");
        }
        if ($this->compra["fecha"] === "" || strtotime($this->compra["fecha"]) === false) {
            $this->addViewError("Debe ingresar una fecha válida", "fecha");
        }
    }

    private function agregarDetalle(): void
    {
        $this->detalle["id_producto"] = intval($_POST["id_producto"] ?? 0);
        $this->detalle["cantidad"] = intval($_POST["cantidad"] ?? 0);
        $this->detalle["costo_unitario"] = floatval($_POST["costo_unitario"] ?? 0);
        if ($this->detalle["id_producto"] <= 0) {
            $this->addViewError("Debe seleccionar un producto", "id_producto");
        }
        if ($this->detalle["cantidad"] <= 0) {
            $this->addViewError("La cantidad debe ser mayor a cero", "cantidad");
        }
        if ($this->detalle["costo_unitario"] <= 0) {
            $this->addViewError("El costo unitario debe ser mayor a cero", "costo_unitario");
        }
        if (count($this->errors) > 0) {
            return;
        }
        ComprasDao::addDetalle(
            intval($this->compra["id_compra"]),
            intval($this->detalle["id_producto"]),
            intval($this->detalle["cantidad"]),
            floatval($this->detalle["costo_unitario"])
        );
        Site::redirectTo(COMPRA_FORM_VIEW_URI . "&mode=UPD&id_compra=" . $this->compra["id_compra"]);
    }

    private function mostrarVista(): void
    {
        $idCompra = intval($this->compra["id_compra"] ?? 0);
        if ($idCompra > 0) {
            $actualizada = ComprasDao::getById($idCompra);
            if (count($actualizada) > 0) {
                $this->compra = array_merge($this->compra, $actualizada);
            }
        }
        $esBorrador = ($this->compra["estado"] ?? "Borrador") === "Borrador";
        $viewData = [];
        $viewData["mode"] = $this->mode;
        $viewData["modeDsc"] = $this->modes[$this->mode];
        $viewData["compra"] = $this->compra;
        $viewData["fechaFormulario"] = $this->compra["fecha"] !== ""
            ? date("Y-m-d\TH:i", strtotime($this->compra["fecha"])) : "";
        $viewData["detalle"] = $this->detalle;
        $viewData["id_compra"] = $idCompra;
        $viewData["esNueva"] = $this->mode === "INS";
        $viewData["esBorrador"] = $esBorrador;
        $viewData["editable"] = in_array($this->mode, ["INS", "UPD"]) && $esBorrador;
        $viewData["puedeAgregarDetalle"] = $this->mode === "UPD" && $esBorrador;
        $viewData["puedeConfirmar"] = $this->mode === "UPD" && $esBorrador;
        $viewData["modoEliminar"] = $this->mode === "DEL";
        $viewData["detalles"] = $idCompra > 0 ? ComprasDao::getDetalles($idCompra) : [];
        $viewData["productos"] = ComprasDao::getProductos();
        $viewData["proveedores"] = array_map(function ($proveedor) {
            $proveedor["isSelected"] = intval($proveedor["id_proveedor"] ?? 0)
                === intval($this->compra["id_proveedor"] ?? 0);
            return $proveedor;
        }, ProveedoresDao::getProveedoresActivos());
        foreach ($this->errors as $scope => $errors) {
            $viewData["error_" . $scope] = $errors;
        }
        $viewData["xssToken"] = $this->generarXssToken();
        Renderer::render(COMPRA_FORM_TEMPLATE, $viewData);
    }

    private function normalizarFechaSql(string $fecha): string
    {
        return date("Y-m-d H:i:s", strtotime($fecha));
    }

    private function generarXssToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[COMPRA_FORM_XSS_TOKEN] = $token;
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
