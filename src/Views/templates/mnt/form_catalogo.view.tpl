<h1>Repostería Ana</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Mnt-CatalogoList">
          <label class="col-3" for="partialName">Nombre</label>
          <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />
          <label class="col-3" for="categoria">Categoría</label>
          <select class="col-9" name="categoria" id="categoria">
              <option value="EMP" {{categoria_EMP}}>Todas</option>
              {{foreach categorias}}
              <option value="{{this.categoria}}">{{this.categoria}}</option>
              {{endfor categorias}}
          </select>
        </div>
        <div class="col-4 align-end">
          <button type="submit">Filtrar</button>
        </div>
      </div>
    </form>
  </div>
</section>

<section class="row px-4 py-4 catalogo-header">
  <div class="col-6">
    {{ifnot OrderByNombre_producto}}
    <a href="index.php?page=Mnt-CatalogoList&orderBy=nombre_producto&orderDescending=0">Ordenar por Nombre <i class="fas fa-sort"></i></a>
    {{endifnot OrderByNombre_producto}}
    {{if OrderNombre_productoDesc}}
    <a href="index.php?page=Mnt-CatalogoList&orderBy=clear&orderDescending=0">Ordenar por Nombre <i class="fas fa-sort-down"></i></a>
    {{endif OrderNombre_productoDesc}}
    {{if OrderNombre_producto}}
    <a href="index.php?page=Mnt-CatalogoList&orderBy=nombre_producto&orderDescending=1">Ordenar por Nombre <i class="fas fa-sort-up"></i></a>
    {{endif OrderNombre_producto}}
    &nbsp;|&nbsp;
    {{ifnot OrderByPrecio}}
    <a href="index.php?page=Mnt-CatalogoList&orderBy=precio&orderDescending=0">Ordenar por Precio <i class="fas fa-sort"></i></a>
    {{endifnot OrderByPrecio}}
    {{if OrderPrecioDesc}}
    <a href="index.php?page=Mnt-CatalogoList&orderBy=clear&orderDescending=0">Ordenar por Precio <i class="fas fa-sort-down"></i></a>
    {{endif OrderPrecioDesc}}
    {{if OrderPrecio}}
    <a href="index.php?page=Mnt-CatalogoList&orderBy=precio&orderDescending=1">Ordenar por Precio <i class="fas fa-sort-up"></i></a>
    {{endif OrderPrecio}}
  </div>
  <div class="col-6 align-end">
    <a href="index.php?page=Mnt-CatalogoForm&mode=INS" class="btn-nuevo">+ Nuevo Producto</a>
  </div>
</section>

<section class="row px-4 catalogo-grid">
  {{foreach productos}}
  <div class="col-12 col-m-4 col-l-3 producto-card depth-1 my-2">
    <div class="producto-imagen">
      <img src="{{this.imagen_url}}" alt="{{this.nombre_producto}}">
    </div>
    <div class="producto-info px-2 py-2">
      <h3 class="producto-nombre">
        <a href="index.php?page=Mnt-CatalogoForm&mode=DSP&id_producto={{this.id_producto}}">{{this.nombre_producto}}</a>
      </h3>
      <span class="producto-categoria">{{this.categoria}}</span>
      <p class="producto-descripcion">{{this.descripcion}}</p>
      <div class="producto-detalle row align-center">
        <span class="producto-precio">L. {{this.precio}}</span>
        <span class="producto-stock">Stock: {{this.cantidad_stock}}</span>
      </div>
      <div class="producto-acciones row my-2">
        <a href="index.php?page=Mnt-CatalogoForm&mode=UPD&id_producto={{this.id_producto}}">Editar</a>
        &nbsp;
        <a href="index.php?page=Mnt-CatalogoForm&mode=DEL&id_producto={{this.id_producto}}">Eliminar</a>
      </div>
    </div>
  </div>
  {{endfor productos}}
</section>

{{pagination}}