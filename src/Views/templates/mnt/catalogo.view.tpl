<section>
    <h2>Catálogo de productos</h2>
</section>
<section class="WWList">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Imagen</th>
                <th>
                    {{ifnot isCliente}}
                    <a href="index.php?page=Mnt-CatalogoForm&mode=INS">Crear</a>
                    {{endifnot isCliente}}
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach productos}}
            <tr>
                <td>{{nombre}}</td>
                <td>{{categoria}}</td>
                <td>{{descripcion}}</td>
                <td>{{precio}}</td>
                <td>{{stock}}</td>
                <td>
                    {{if imagen}}
                    <img src="uploads/catalogo/{{imagen}}" alt="{{nombre}}" width="60" height="60" style="object-fit: cover; border-radius: 4px;" />
                    {{endif imagen}}
                    {{ifnot imagen}}
                    <span style="color: #999;">Sin imagen</span>
                    {{endifnot imagen}}
                </td>
                <td>
                    {{ifnot ~isCliente}}
                    <a href="index.php?page=Mnt-CatalogoForm&mode=DSP&id_producto={{id_producto}}">Mostrar</a><br/>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=UPD&id_producto={{id_producto}}">Editar</a><br/>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=DEL&id_producto={{id_producto}}">Borrar</a>
                    {{endifnot ~isCliente}}
                </td>
            </tr>
            {{endfor productos}}
        </tbody>
    </table>
</section>