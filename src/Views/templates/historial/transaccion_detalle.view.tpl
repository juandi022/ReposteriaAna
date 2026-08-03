<section>
    <h2>Detalle de Transacción</h2>
    <p>Consulta de solo lectura.</p>
</section>

<section class="grid depth-1 px-4 py-4 my-4">
    {{with transaccion}}
    <div class="col-12 col-m-3"><label>Tipo</label><input type="text" value="{{tipo}}" readonly /></div>
    <div class="col-12 col-m-3"><label>Referencia</label><input type="text" value="{{referencia}}" readonly /></div>
    <div class="col-12 col-m-3"><label>Fecha</label><input type="text" value="{{fecha}}" readonly /></div>
    <div class="col-12 col-m-3"><label>Estado</label><input type="text" value="{{estado}}" readonly /></div>
    <div class="col-12 col-m-6"><label>Proveedor o cliente</label><input type="text" value="{{tercero}}" readonly /></div>
    <div class="col-12 col-m-3"><label>Teléfono</label><input type="text" value="{{telefono}}" readonly /></div>
    <div class="col-12 col-m-3"><label>Correo</label><input type="text" value="{{correo}}" readonly /></div>
    <div class="col-12 col-m-4"><label>Subtotal</label><input type="text" value="L {{subtotal}}" readonly /></div>
    <div class="col-12 col-m-4"><label>Impuesto</label><input type="text" value="L {{impuesto}}" readonly /></div>
    <div class="col-12 col-m-4"><label>Total</label><input type="text" value="L {{monto}}" readonly /></div>
    {{endwith transaccion}}

    {{if esPago}}
    <div class="col-12 col-m-4"><label>Método de pago</label><input type="text" value="{{metodoPago}}" readonly /></div>
    {{endif esPago}}
</section>

{{if hasDetalles}}
<section class="WWList my-4">
    <table>
        <thead>
            <tr><th>Concepto</th><th>Cantidad</th><th>Precio o costo</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            {{foreach detalles}}
            <tr>
                <td>{{concepto}}</td>
                <td>{{cantidad}}</td>
                <td>L {{precio}}</td>
                <td>L {{subtotal}}</td>
            </tr>
            {{endfor detalles}}
        </tbody>
    </table>
</section>
{{endif hasDetalles}}

<section class="my-4">
    <a class="btn px-4" href="index.php?page=Historial-TransaccionesList">Volver al Historial</a>
</section>
