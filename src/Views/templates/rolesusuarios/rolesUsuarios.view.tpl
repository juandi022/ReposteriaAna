<h1>Roles de Usuarios</h1>
<section class="grid">
  <div class="row">
    <form class="col-12 col-m-8" action="index.php" method="get">
      <div class="flex align-center">
        <div class="col-8 row">
          <input type="hidden" name="page" value="RolesUsuarios_RolesUsuarios">
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
        <th>Código de Usuario</th>
        <th>Email</th>
        <th>Código de Rol</th>
        <th>Rol</th>
        <th>Estado</th>
        <th>Fecha de Asignación</th>
        <th>Fecha de Expiración</th>
        <th><a href="index.php?page=RolesUsuarios_RolUsuario&mode=INS">Nuevo</a></th>
      </tr>
    </thead>
    <tbody>
      {{foreach rolesUsuarios}}
      <tr>
        <td>{{usercod}}</td>
        <td> <a class="link" href="index.php?page=RolesUsuarios_RolUsuario&mode=DSP&usercod={{usercod}}&rolescod={{rolescod}}">{{useremail}}</a>
        </td>
        <td>{{rolescod}}</td>
        <td>{{rolesdsc}}</td>
        <td class="center">
          {{roleuserestDesc}}
        </td>
        <td>{{roleuserfch}}</td>
        <td>{{roleuserexp}}</td>
        <td class="center">
          <a href="index.php?page=RolesUsuarios_RolUsuario&mode=UPD&usercod={{usercod}}&rolescod={{rolescod}}">Editar</a>
          &nbsp;
          <a href="index.php?page=RolesUsuarios_RolUsuario&mode=DEL&usercod={{usercod}}&rolescod={{rolescod}}">Eliminar</a>
        </td>
      </tr>
      {{endfor rolesUsuarios}}
    </tbody>
  </table>
  {{pagination}}
</section>
