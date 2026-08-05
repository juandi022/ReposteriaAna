<section>
    <h2>{{modeDsc}}</h2>
</section>

{{foreach error_global}}
<div class="error">{{this}}</div>
{{endfor error_global}}

<section class="grid depth-1 px-4 py-4 my-4">
    <form class="row col-12" action="index.php?page=Compras-CompraForm&mode={{mode}}&id_compra={{id_compra}}" method="post">
        <input type="hidden" name="mode" value="{{mode}}" />
        <input type="hidden" name="xssToken" value="{{xssToken}}" />

        <div class="col-12 col-m-6">
            <label for="id_proveedor">Proveedor</label>
            <select name="id_proveedor" id="id_proveedor" {{ifnot editable}}disabled{{endifnot editable}}>
                <option value="">Seleccione un proveedor</option>
                {{foreach proveedores}}
                <option value="{{id_proveedor}}" {{if isSelected}}selected{{endif isSelected}}>{{nombre}}</option>
                {{endfor proveedores}}
            </select>
            {{foreach error_id_proveedor}}<div class="error">{{this}}</div>{{endfor error_id_proveedor}}
        </div>

        <div class="col-12 col-m-6">
            <label for="numero_factura">Número de factura</label>
            {{with compra}}
            <input type="text" name="numero_factura" id="numero_factura" value="{{numero_factura}}" {{ifnot ~editable}}readonly{{endifnot ~editable}} />
            {{endwith compra}}
            {{foreach error_numero_factura}}<div class="error">{{this}}</div>{{endfor error_numero_factura}}
        </div>

        <div class="col-12 col-m-6">
            <label for="fecha">Fecha</label>
            <input type="datetime-local" name="fecha" id="fecha" value="{{fechaFormulario}}" {{ifnot editable}}readonly{{endifnot editable}} />
            {{foreach error_fecha}}<div class="error">{{this}}</div>{{endfor error_fecha}}
        </div>

        {{with compra}}
        <div class="col-6 col-m-3"><label>Subtotal</label><input type="text" value="L {{subtotal}}" readonly /></div>
        <div class="col-6 col-m-3"><label>Impuesto</label><input type="text" value="L {{impuesto}}" readonly /></div>
        <div class="col-6 col-m-3"><label>Total</label><input type="text" value="L {{total}}" readonly /></div>
        <div class="col-6 col-m-3"><label>Estado</label><input type="text" value="{{estado}}" readonly /></div>
        {{endwith compra}}

        <div class="col-12 flex align-center">
            {{if editable}}<button type="submit" name="action" value="SAVE">Guardar</button>{{endif editable}}
            {{if modoEliminar}}<button type="submit" name="action" value="DELETE"><i class="fas fa-trash"></i>&nbsp;Confirmar eliminación</button>{{endif modoEliminar}}
            {{if puedeConfirmar}}<button type="submit" name="action" value="CONFIRM">Confirmar y Reabastecer</button>{{endif puedeConfirmar}}
            <a class="btn px-4" href="index.php?page=Compras-ComprasList">Volver</a>
        </div>
    </form>
</section>

{{if puedeAgregarDetalle}}
<section class="grid depth-1 px-4 py-4 my-4">
    <h3 class="col-12">Agregar Producto</h3>
    <form class="row col-12" action="index.php?page=Compras-CompraForm&mode=UPD&id_compra={{id_compra}}" method="post">
        <input type="hidden" name="xssToken" value="{{xssToken}}" />
        <div class="col-12 col-m-6">
            <label for="id_producto">Producto</label>
            <select name="id_producto" id="id_producto">
                <option value="">Seleccione un producto</option>
                {{foreach productos}}
                <option value="{{id_producto}}">{{nombre}} — Stock: {{stock}}</option>
                {{endfor productos}}
            </select>
            {{foreach error_id_producto}}<div class="error">{{this}}</div>{{endfor error_id_producto}}
        </div>
        {{with detalle}}
        <div class="col-6 col-m-3">
            <label for="cantidad">Cantidad</label>
            <input type="number" min="1" name="cantidad" id="cantidad" value="{{cantidad}}" />
        </div>
        <div class="col-6 col-m-3">
            <label for="costo_unitario">Costo unitario</label>
            <input type="number" min="0.01" step="0.01" name="costo_unitario" id="costo_unitario" value="{{costo_unitario}}" />
        </div>
        {{endwith detalle}}
        <div class="col-12"><button type="submit" name="action" value="ADD_DETAIL">Agregar Producto</button></div>
    </form>
</section>
{{endif puedeAgregarDetalle}}

{{ifnot esNueva}}
<section class="WWList my-4">
    <table>
        <thead>
            <tr><th>Producto</th><th>Cantidad</th><th>Costo unitario</th><th>Subtotal</th><th>Acción</th></tr>
        </thead>
        <tbody>
            {{foreach detalles}}
            <tr>
                <td>{{producto}}</td>
                <td>{{cantidad}}</td>
                <td>L {{costo_unitario}}</td>
                <td>L {{subtotal}}</td>
                <td>
                    {{if ~puedeAgregarDetalle}}
                    <form action="index.php?page=Compras-CompraForm&mode=UPD&id_compra={{~id_compra}}" method="post">
                        <input type="hidden" name="xssToken" value="{{~xssToken}}" />
                        <input type="hidden" name="id_detalle_compra" value="{{id_detalle_compra}}" />
                        <button type="submit" name="action" value="DEL_DETAIL"><i class="fas fa-trash"></i>&nbsp;Quitar</button>
                    </form>
                    {{endif ~puedeAgregarDetalle}}
                </td>
            </tr>
            {{endfor detalles}}
        </tbody>
    </table>
</section>
{{endifnot esNueva}}
