<section>
    <h2>Compras para Reabastecimiento</h2>
    <a class="btn px-4" href="index.php?page=Compras-ProveedoresList">Administrar Proveedores</a>
</section>

<section class="grid depth-1 px-4 py-4 my-4">
    <form class="row col-12" method="get" action="index.php">
        <input type="hidden" name="page" value="Compras-ComprasList" />
        <div class="col-12 col-m-5">
            <label for="partialFactura">Número de factura</label>
            <input type="text" id="partialFactura" name="partialFactura" value="{{partialFactura}}" />
        </div>
        <div class="col-12 col-m-4">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="EMP" {{estado_EMP}}>Todos</option>
                <option value="Borrador" {{estado_Borrador}}>Borrador</option>
                <option value="Confirmada" {{estado_Confirmada}}>Confirmada</option>
                <option value="Anulada" {{estado_Anulada}}>Anulada</option>
            </select>
        </div>
        <div class="col-12 col-m-3 align-end">
            <button type="submit">Filtrar</button>
            <a class="btn px-4" href="index.php?page=Compras-ComprasList&orderBy=clear&estado=EMP&partialFactura=">Limpiar</a>
        </div>
    </form>
</section>

<section class="WWList">
    <table>
        <thead>
            <tr>
                <th><a href="index.php?page=Compras-ComprasList&orderBy=id_compra">Código</a></th>
                <th>Proveedor</th>
                <th><a href="index.php?page=Compras-ComprasList&orderBy=numero_factura">Factura</a></th>
                <th><a href="index.php?page=Compras-ComprasList&orderBy=fecha">Fecha</a></th>
                <th><a href="index.php?page=Compras-ComprasList&orderBy=total">Total</a></th>
                <th><a href="index.php?page=Compras-ComprasList&orderBy=estado">Estado</a></th>
                <th><a href="index.php?page=Compras-CompraForm&mode=INS">Crear</a></th>
            </tr>
        </thead>
        <tbody>
            {{foreach compras}}
            <tr>
                <td>{{id_compra}}</td>
                <td>{{proveedor}}</td>
                <td>{{numero_factura}}</td>
                <td>{{fecha}}</td>
                <td>L {{total}}</td>
                <td>{{estado}}</td>
                <td>
                    <a href="index.php?page=Compras-CompraForm&mode=DSP&id_compra={{id_compra}}">Mostrar</a><br/>
                    {{if esBorrador}}
                    <a href="index.php?page=Compras-CompraForm&mode=UPD&id_compra={{id_compra}}">Editar</a><br/>
                    <a href="index.php?page=Compras-CompraForm&mode=DEL&id_compra={{id_compra}}">Borrar</a>
                    {{endif esBorrador}}
                </td>
            </tr>
            {{endfor compras}}
        </tbody>
    </table>
</section>

<section class="flex flex-center align-center my-4">
    {{if hasPrevious}}
    <a class="btn px-4" href="index.php?page=Compras-ComprasList&pageNum={{previousPage}}">Anterior</a>
    {{endif hasPrevious}}
    <span class="px-4">Página {{pageNum}} de {{pages}} — {{comprasCount}} compras</span>
    {{if hasNext}}
    <a class="btn px-4" href="index.php?page=Compras-ComprasList&pageNum={{nextPage}}">Siguiente</a>
    {{endif hasNext}}
</section>
