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
                    <a href="index.php?page=Mnt-CarritoForm&mode=DEL&id_detalle_carrito={{id_detalle_carrito}}">
                        Eliminar
                    </a>
                </td>
            </tr>
            {{endfor detalle}}

            <tr>
                <td colspan="3" style="text-align:right;">
                    <strong>Total</strong>
                </td>
                <td>
                    <strong>L {{total}}</strong>
                </td>
                <td></td>
            </tr>

        </tbody>
    </table>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:25px;">

        <a class="btn" href="index.php?page=Mnt-CatalogoList">
            <i class="fa-solid fa-arrow-left"></i>
            Seguir comprando
        </a>

        <form action="index.php?page=Checkout_Checkout" method="post" style="margin:0;">
            <button type="submit">
                Place Order
            </button>
        </form>

    </div>

    {{endif detalle}}

    {{ifnot detalle}}

    <p>Tu carrito está vacío.</p>

    <p>
        <a class="btn" href="index.php?page=Mnt-CatalogoList">
            <i class="fa-solid fa-arrow-left"></i>
            Seguir comprando
        </a>
    </p>

    {{endifnot detalle}}

</section>