<section>
  <h2>{{modeDsc}}</h2>
</section>

<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php?page=Mnt-CarritoForm&mode={{mode}}&id_producto={{id_producto}}&id_detalle_carrito={{id_detalle_carrito}}" method="post">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Mnt-CarritoForm" />
          <input type="hidden" name="mode" value="{{mode}}" />
          <input type="hidden" name="xssToken" value="{{xssToken}}" />
          <input type="hidden" name="id_producto" value="{{id_producto}}" />
          <input type="hidden" name="id_detalle_carrito" value="{{id_detalle_carrito}}" />

          <label class="col-3" for="nombre_producto">Producto</label>
          <input class="col-9" type="text" id="nombre_producto" value="{{nombre_producto}}" readonly />

          <label class="col-3" for="precio">Precio</label>
          <input class="col-9" type="text" id="precio" value="L {{precio}}" readonly />

          {{if modoAgregar}}
          <label class="col-3" for="cantidad">Cantidad</label>
          <input class="col-9" type="number" name="cantidad" id="cantidad" value="{{cantidad}}" min="1" max="{{disponible}}" />
          <div class="col-9" style="margin-left:25%">
            <small>Stock: {{stock}} · Ya en carrito: {{cantidadEnCarrito}} · Disponibles: {{disponible}}</small>
          </div>
          {{endif modoAgregar}}
          {{ifnot modoAgregar}}
          <label class="col-3" for="cantidad">Cantidad en carrito</label>
          <input class="col-9" type="text" id="cantidad" value="{{cantidad}}" readonly />
          {{endifnot modoAgregar}}

          {{if error_cantidad}}
          <div class="col-9" style="margin-left:25%; color:#c00;">
            {{error_cantidad}}
          </div>
          {{endif error_cantidad}}
        </div>

        <div class="col-4 align-end">
          {{if modoAgregar}}
          {{if disponible}}
          <button type="submit"><i class="fa-solid fa-cart-plus"></i>&nbsp;Agregar al carrito</button>
          {{endif disponible}}
          {{ifnot disponible}}
          <p style="color:#c00;">Producto sin stock disponible</p>
          {{endifnot disponible}}
          {{endif modoAgregar}}
          {{ifnot modoAgregar}}
          <button type="submit"><i class="fa-solid fa-trash"></i>&nbsp;Eliminar del carrito</button>
          {{endifnot modoAgregar}}
          <a href="index.php?page=Mnt-CarritoList">Volver al carrito</a>
        </div>
      </div>
    </form>
  </div>
</section>
