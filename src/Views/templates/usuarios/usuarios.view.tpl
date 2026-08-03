<h1>Usuarios</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="Usuarios-Usuarios">
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
        <th>Email</th>
        <th>Nombre de Usuario</th>
        <th>Contraseña</th>
        <th>Fecha</th>
        <th>Contraseña Estado</th>
        <th>Contraseña Expiración</th>
        <th>Usuario Estado</th>
        <th>Act</th>
        <th>Contraseña Nueva</th>
        <th>Usuario Tipo</th>
        <th><a href="index.php?page=Usuarios_Usuario&mode=INS">Nuevo</a></th>
      </tr>
    </thead>
    <tbody>
      {{foreach usuarios}}
      <tr>
        <td>{{usercod}}</td>
        <td> <a class="link" href="index.php?page=Usuarios_Usuario&mode=DSP&usercod={{usercod}}">{{useremail}}</a>
        </td>
        <td class="right">
          {{username}}
        </td>
        <td>{{userpswd}}</td>
        <td>{{userfching}}</td>
        <td>{{userpswdestDesc}}</td>
        <td>{{userpswdexp}}</td>
        <td class="center">{{userestDesc}}</td>
        <td>{{useractcod}}</td>
        <td>{{userpswdchg}}</td>
        <td>{{usertipoDesc}}</td>
        <td class="center">
          <a href="index.php?page=Usuarios_Usuario&mode=UPD&usercod={{usercod}}">Editar</a>
          &nbsp;
          <a href="index.php?page=Usuarios_Usuario&mode=DEL&usercod={{usercod}}">Eliminar</a>
        </td>
      </tr>
      {{endfor usuarios}}
    </tbody>
  </table>
  {{pagination}}
</section>