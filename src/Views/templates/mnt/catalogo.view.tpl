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
                    {{if isAdmin}}
                    <a href="index.php?page=Mnt-CatalogoForm&mode=INS">Crear</a>
                    {{endif isAdmin}}
                </th>
            </tr>
        </thead>

        <tbody>
            {{foreach productos}}
            <tr>

                <td>{{nombre}}</td>

                <td>{{categoria}}</td>

                <td>{{descripcion}}</td>

                <td>L {{precio}}</td>

                <td>{{stock}}</td>

                <td>
                    {{if imagen}}
                    <img src="uploads/catalogo/{{imagen}}" alt="{{nombre}}" width="60" height="60" style="object-fit:cover;border-radius:6px;">
                    {{endif imagen}}

                    {{ifnot imagen}}
                    <span style="color:#999;">Sin imagen</span>
                    {{endifnot imagen}}
                </td>

                <td>
                    <a href="index.php?page=Mnt-CarritoForm&mode=INS&id_producto={{id_producto}}">
                        <i class="fas fa-cart-plus"></i>&nbsp;Comprar
                    </a><br/>
                    {{if ~isAdmin}}
                    <a href="index.php?page=Mnt-CatalogoForm&mode=UPD&id_producto={{id_producto}}">
                        <i class="fas fa-pen-to-square"></i>&nbsp;Editar
                    </a><br/>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=DEL&id_producto={{id_producto}}">
                        <i class="fas fa-trash"></i>&nbsp;Borrar
                    </a>
                    {{endif ~isAdmin}}
                </td>

            </tr>
            {{endfor productos}}
        </tbody>
    </table>
</section>
