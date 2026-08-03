<section>
    <h2>Historial de Transacciones</h2>
    <p>Consulta cronológica de compras a proveedores, pedidos y pagos.</p>
</section>

<section class="grid depth-1 px-4 py-4 my-4">
    <form class="row col-12" method="get" action="index.php">
        <input type="hidden" name="page" value="Historial-TransaccionesList" />
        <div class="col-12 col-m-3">
            <label for="tipo">Tipo</label>
            <select id="tipo" name="tipo">
                <option value="EMP" {{tipo_EMP}}>Todos</option>
                <option value="COMPRA" {{tipo_COMPRA}}>Compra</option>
                <option value="PEDIDO" {{tipo_PEDIDO}}>Pedido</option>
                <option value="PAGO" {{tipo_PAGO}}>Pago</option>
            </select>
        </div>
        <div class="col-12 col-m-3">
            <label for="estado">Estado</label>
            <input type="text" id="estado" name="estado" value="{{estado}}" placeholder="Ej. Confirmada" />
        </div>
        <div class="col-12 col-m-3">
            <label for="fechaDesde">Desde</label>
            <input type="date" id="fechaDesde" name="fechaDesde" value="{{fechaDesde}}" />
        </div>
        <div class="col-12 col-m-3">
            <label for="fechaHasta">Hasta</label>
            <input type="date" id="fechaHasta" name="fechaHasta" value="{{fechaHasta}}" />
        </div>
        <div class="col-12 col-m-6">
            <label for="referencia">Referencia</label>
            <input type="text" id="referencia" name="referencia" value="{{referencia}}" placeholder="Factura o código" />
        </div>
        <div class="col-12 col-m-6 align-end">
            <button type="submit">Filtrar</button>
            <a class="btn px-4" href="index.php?page=Historial-TransaccionesList&tipo=EMP&estado=&fechaDesde=&fechaHasta=&referencia=">Limpiar</a>
        </div>
    </form>
</section>

<section class="WWList">
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Referencia</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            {{foreach transacciones}}
            <tr>
                <td>{{fecha}}</td>
                <td>{{tipo}}</td>
                <td>{{referencia}}</td>
                <td>{{descripcion}}</td>
                <td>L {{monto}}</td>
                <td>{{estado}}</td>
                <td><a href="index.php?page=Historial-TransaccionDetalle&tipo={{tipo}}&id={{id_transaccion}}">Ver</a></td>
            </tr>
            {{endfor transacciones}}
        </tbody>
    </table>
</section>

<section class="flex flex-center align-center my-4">
    {{if hasPrevious}}<a class="btn px-4" href="index.php?page=Historial-TransaccionesList&pageNum={{previousPage}}">Anterior</a>{{endif hasPrevious}}
    <span class="px-4">Página {{pageNum}} de {{pages}} — {{transaccionesCount}} transacciones</span>
    {{if hasNext}}<a class="btn px-4" href="index.php?page=Historial-TransaccionesList&pageNum={{nextPage}}">Siguiente</a>{{endif hasNext}}
</section>
