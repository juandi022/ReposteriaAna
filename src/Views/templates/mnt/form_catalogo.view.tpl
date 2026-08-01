<section>
  <h2>{{modeDsc}}</h2>
</section>

<section class="grid">
  <div class="row"><form class="col-12 col-m-8" action="index.php?page=Mnt-CatalogoForm&mode={{mode}}&id_producto={{id_producto}}" method="post" enctype="multipart/form-data">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Mnt-CatalogoForm" />
          <input type="hidden" name="mode" value="{{mode}}" />
          <input type="hidden" name="xssToken" value="{{xssToken}}" />
          <input type="hidden" name="estado" value="Disponible" />

          {{with producto}}
          <label class="col-3" for="nombre">Nombre</label>
          <input class="col-9" type="text" name="nombre" id="nombre" value="{{nombre}}" {{&readonly}} />
          {{endwith producto}}

          <label class="col-3" for="categoria">Categoría</label>
          <select class="col-9" name="categoria" id="categoria" {{readonly}}>
            {{foreach categorias}}
            <option value="{{categoria}}" {{if isSelected}}selected{{endif isSelected}}>{{categoria}}</option>
            {{endfor categorias}}
          </select>

          {{with producto}}
          <label class="col-3" for="descripcion">Descripción</label>
          <textarea class="col-9" name="descripcion" id="descripcion" {{&readonly}}>{{descripcion}}</textarea>

          <label class="col-3" for="precio">Precio</label>
          <input class="col-9" type="number" step="0.01" name="precio" id="precio" value="{{precio}}" {{&readonly}} />

          <label class="col-3" for="stock">Stock</label>
          <input class="col-9" type="number" name="stock" id="stock" value="{{stock}}" {{&readonly}} />
          {{endwith producto}}

          <label class="col-3" for="imagen">Imagen</label>
          {{if editable}}
          <input class="col-9" type="file" name="imagen" id="imagen" accept="image/*" />
          <input type="hidden" name="imagen_actual" value="{{imagen}}" />
          {{endif editable}}
          {{ifnot editable}}
          <input class="col-9" type="text" value="{{imagen}}" readonly />
          {{endifnot editable}}

          {{if imagen}}
          <div class="col-9" style="margin-left:25%">
            <img src="uploads/catalogo/{{imagen}}" width="120" />
          </div>
          {{endif imagen}}
        </div>

        <div class="col-4 align-end">
          {{if editable}}
          <button type="submit">Guardar</button>
          {{endif editable}}
          {{ifnot editable}}
          {{with producto}}
          <a class="btn" href="index.php?page=Checkout-Checkout&id_producto={{id_producto}}"><i class="fa-solid fa-cart-shopping"></i>&nbsp;Comprar</a>
          {{endwith producto}}
          {{endifnot editable}}
          <a href="index.php?page=Mnt-CatalogoList">Volver</a>
        </div>
      </div>
    </form>
  </div>
</section>