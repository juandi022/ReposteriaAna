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
                    {{if ~puedeAdministrar}}
                    <a href="index.php?page=Mnt-CatalogoForm&mode=INS">Crear</a>
                    {{endif ~puedeAdministrar}}
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
                    <a href="index.php?page=Mnt-CarritoForm&mode=INS&id_producto={{id_producto}}"><i class="fa-solid fa-cart-plus"></i>&nbsp;Agregar al carrito</a>
                    {{if ~puedeAdministrar}}
                    <br/>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=DSP&id_producto={{id_producto}}">Mostrar</a><br/>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=UPD&id_producto={{id_producto}}">Editar</a><br/>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=DEL&id_producto={{id_producto}}">Borrar</a>
                    {{endif ~puedeAdministrar}}
                </td>
            </tr>
            {{endfor productos}}
        </tbody>
    </table>
</section>