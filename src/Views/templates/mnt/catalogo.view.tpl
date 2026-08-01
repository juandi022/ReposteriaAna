
<section>
    <h2>Catalogo de productos</h2>
</section>
<section class="PRist">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoria</th>
                <th>Descripcion</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Imagen</th>
                <th>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=INS">Crear</a>
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach muebles}}
            <tr>
                <td>{{tipo_mueble}}</td>
                <td>{{material}}</td>
                <td>{{color}}</td>
                <td>{{precio}}</td>
                <td>{{cantidad_stock}}</td>
                <td>{{imagen_url}}</td>
                <td>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=DSP&id_mueble={{id_mueble}}">Mostrar</a><br/>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=UPD&id_mueble={{id_mueble}}">Editar</a><br/>
                    <a href="index.php?page=Mnt-CatalogoForm&mode=DEL&id_mueble={{id_mueble}}">Borrar</a>
                </td>
            </tr>
            {{endfor catalogos}}
        </tbody>
    </table>
</section>
