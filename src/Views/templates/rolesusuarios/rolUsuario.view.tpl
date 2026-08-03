<section class="container-m row px-4 py-4">
  <h1>{{FormTitle}}</h1>
</section>
<section class="container-m row px-4 py-4">
  {{with rolUsuario}}
  <form action="index.php?page=RolesUsuarios_RolUsuario&mode={{~mode}}&usercod={{usercod}}&rolescod={{rolescod}}" method="POST" class="col-12 col-m-8 offset-m-2">
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="usercod">Usuario</label>
      <select name="usercod" id="usercod" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        {{foreach ~usuariosOptions}}
        <option value="{{usercod}}" {{selected}}>{{usercod}} - {{useremail}}</option>
        {{endfor ~usuariosOptions}}
      </select>
      {{if ~readonly}}
      <input type="hidden" name="usercod" value="{{usercod}}" />
      {{endif ~readonly}}
      <input type="hidden" name="mode" value="{{~mode}}" />
      <input type="hidden" name="token" value="{{~rolUsuario_xss_token}}" />
      {{if usercod_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{usercod_error}}
      </div>
      {{endif usercod_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="rolescod">Rol</label>
      <select name="rolescod" id="rolescod" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        {{foreach ~rolesOptions}}
        <option value="{{rolescod}}" {{selected}}>{{rolescod}} - {{rolesdsc}}</option>
        {{endfor ~rolesOptions}}
      </select>
      {{if ~readonly}}
      <input type="hidden" name="rolescod" value="{{rolescod}}" />
      {{endif ~readonly}}
      {{if rolescod_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{rolescod_error}}
      </div>
      {{endif rolescod_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="roleuserest">Estado del Rol</label>
      <select name="roleuserest" id="roleuserest" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        <option value="ACT" {{rolUsuarioStatus_act}}>Activo</option>
        <option value="INA" {{rolUsuarioStatus_ina}}>Inactivo</option>
      </select>
      {{if roleuserest_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{roleuserest_error}}
      </div>
      {{endif roleuserest_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="roleuserfch">Fecha de asignación</label>
      <input class="col-12 col-m-9" {{~readonly}} type="datetime-local" name="roleuserfch" id="roleuserfch" value="{{roleuserfch}}" />
      {{if roleuserfch_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{roleuserfch_error}}
      </div>
      {{endif roleuserfch_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="roleuserexp">Fecha de expiración</label>
      <input class="col-12 col-m-9" {{~readonly}} type="datetime-local" name="roleuserexp" id="roleuserexp" value="{{roleuserexp}}" />
      {{if roleuserexp_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{roleuserexp_error}}
      </div>
      {{endif roleuserexp_error}}
    </div>
    {{endwith rolUsuario}}
    <div class="row my-4 align-center flex-end">
      {{if showCommitBtn}}
      <button class="primary col-12 col-m-2" type="submit" name="btnConfirmar">Confirmar</button>
      &nbsp;
      {{endif showCommitBtn}}
      <button class="col-12 col-m-2"type="button" id="btnCancelar">
        {{if showCommitBtn}}
        Cancelar
        {{endif showCommitBtn}}
        {{ifnot showCommitBtn}}
        Regresar
        {{endifnot showCommitBtn}}
      </button>
    </div>
    </div>
  </form>
</section>

<script>
  document.addEventListener("DOMContentLoaded", ()=>{
    const btnCancelar = document.getElementById("btnCancelar");
    btnCancelar.addEventListener("click", (e)=>{
      e.preventDefault();
      e.stopPropagation();
      window.location.assign("index.php?page=RolesUsuarios_RolesUsuarios");
    });
  });
</script>
