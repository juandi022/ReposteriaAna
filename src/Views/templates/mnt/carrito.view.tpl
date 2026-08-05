<section>
    <h2>Mi Carrito de Compras</h2>
</section>

<section class="WWList">
    {{if detalle}}
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {{foreach detalle}}
            <tr>
                <td>{{nombre_producto}}</td>
                <td>{{cantidad}}</td>
                <td>L {{precio}}</td>
                <td>L {{subtotal}}</td>
                <td>
                    <a href="index.php?page=Mnt-CarritoForm&mode=DEL&id_producto={{id_producto}}">Eliminar</a>
                </td>
            </tr>
            {{endfor detalle}}
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
                <td><strong>L {{total}}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    {{endif detalle}}
    {{ifnot detalle}}
    <p>Tu carrito está vacío.</p>
    {{endifnot detalle}}
    <p>
        <a class="btn" href="index.php?page=Checkout-Checkout"><i class="fa-solid fa-credit-card"></i>&nbsp;Ir a pagar</a>
        <a class="btn" href="index.php?page=Mnt-CatalogoList"><i class="fa-solid fa-arrow-left"></i>&nbsp;Seguir comprando</a>
    </p>
</section>
