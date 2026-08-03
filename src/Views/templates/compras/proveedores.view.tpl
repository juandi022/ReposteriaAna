<section>
    <h2>Proveedores</h2>
</section>

<section class="grid depth-1 px-4 py-4 my-4">
    <form class="row col-12" method="get" action="index.php">
        <input type="hidden" name="page" value="Compras-ProveedoresList" />
        <div class="col-12 col-m-5">
            <label for="partialName">Nombre del proveedor</label>
            <input type="text" id="partialName" name="partialName" value="{{partialName}}" />
        </div>
        <div class="col-12 col-m-4">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="EMP" {{estado_EMP}}>Todos</option>
                <option value="Activo" {{estado_Activo}}>Activo</option>
                <option value="Inactivo" {{estado_Inactivo}}>Inactivo</option>
            </select>
        </div>
        <div class="col-12 col-m-3 align-end">
            <button type="submit">Filtrar</button>
            <a class="btn px-4" href="index.php?page=Compras-ProveedoresList&orderBy=clear&estado=EMP&partialName=">Limpiar</a>
        </div>
    </form>
</section>

<section class="WWList">
    <table>
        <thead>
            <tr>
                <th><a href="index.php?page=Compras-ProveedoresList&orderBy=id_proveedor">Código</a></th>
                <th><a href="index.php?page=Compras-ProveedoresList&orderBy=nombre">Nombre</a></th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th><a href="index.php?page=Compras-ProveedoresList&orderBy=estado">Estado</a></th>
                <th><a href="index.php?page=Compras-ProveedorForm&mode=INS">Crear</a></th>
            </tr>
        </thead>
        <tbody>
            {{foreach proveedores}}
            <tr>
                <td>{{id_proveedor}}</td>
                <td>{{nombre}}</td>
                <td>{{contacto}}</td>
                <td>{{telefono}}</td>
                <td>{{correo}}</td>
                <td>{{estado}}</td>
                <td>
                    <a href="index.php?page=Compras-ProveedorForm&mode=DSP&id_proveedor={{id_proveedor}}">Mostrar</a><br/>
                    <a href="index.php?page=Compras-ProveedorForm&mode=UPD&id_proveedor={{id_proveedor}}">Editar</a><br/>
                    <a href="index.php?page=Compras-ProveedorForm&mode=DEL&id_proveedor={{id_proveedor}}">Borrar</a>
                </td>
            </tr>
            {{endfor proveedores}}
        </tbody>
    </table>
</section>

<section class="flex flex-center align-center my-4">
    {{if hasPrevious}}<a class="btn px-4" href="index.php?page=Compras-ProveedoresList&pageNum={{previousPage}}">Anterior</a>{{endif hasPrevious}}
    <span class="px-4">Página {{pageNum}} de {{pages}} — {{proveedoresCount}} proveedores</span>
    {{if hasNext}}<a class="btn px-4" href="index.php?page=Compras-ProveedoresList&pageNum={{nextPage}}">Siguiente</a>{{endif hasNext}}
</section>

<section class="my-4">
    <a class="btn px-4" href="index.php?page=Compras-ComprasList">Ir a Compras</a>
</section>
