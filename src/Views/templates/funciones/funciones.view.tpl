<h1>Funciones</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Funciones_Funciones">
          <label class="col-3" for="partialName">Nombre</label>
          <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />
          <label class="col-3" for="status">Estado</label>
          <select class="col-9" name="status" id="status">
              <option value="EMP" {{status_EMP}}>Todos</option>
              <option value="ACT" {{status_ACT}}>Activo</option>
              <option value="INA" {{status_INA}}>Inactivo</option>
          </select>
        </div>
        <div class="col-4 align-end">
          <button type="submit">Filtrar</button>
        </div>
      </div>
    </form>
  </div>
</section>
<section class="WWList">
  <table>
    <thead>
      <tr>
        <th>Codigo</th>
        <th>Descripcion</th>
        <th>Estado</th>
        <th>Tipo</th>
        <th><a href="index.php?page=Funciones_Funcion&mode=INS">Nuevo</a></th>
      </tr>
    </thead>
    <tbody>
      {{foreach funciones}}
      <tr>
        <td>{{fncod}}</td>
        <td> <a class="link" href="index.php?page=Funciones_Funcion&mode=DSP&fncod={{fncod}}">{{fndsc}}</a>
        </td>
        <td class="right">
          {{fnestDesc}}
        </td>
        <td class="right">
          {{fntyp}}
        </td>
        <td class="center">
          <a href="index.php?page=Funciones_Funcion&mode=UPD&fncod={{fncod}}">Editar</a>
          &nbsp;
          <a href="index.php?page=Funciones_Funcion&mode=DEL&fncod={{fncod}}">Eliminar</a>
        </td>
      </tr>
      {{endfor funciones}}
    </tbody>
  </table>
  {{pagination}}
</section>