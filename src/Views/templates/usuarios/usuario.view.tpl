<section class="container-m row px-4 py-4">
  <h1>{{FormTitle}}</h1>
</section>
<section class="container-m row px-4 py-4">
  {{with usuario}}
  <form action="index.php?page=Usuarios_Usuario&mode={{~mode}}&usercod={{usercod}}" method="POST" class="col-12 col-m-8 offset-m-2">
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="userCodD">Código</label>
      <input class="col-12 col-m-9" readonly disabled type="text" name="userCodD" id="userCodD" placehoder="Código" value="{{usercod}}" />
      <input type="hidden" name="usercod" value="{{usercod}}" />
      <input type="hidden" name="mode" value="{{~mode}}" />
      <input type="hidden" name="userCod" value="{{usercod}}" />
      <input type="hidden" name="token" value="{{~usuario_xss_token}}" />
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="useremail">Email</label>
      <input class="col-12 col-m-9" {{~readonly}} type="text" name="useremail" id="useremail" placehoder="Email del usuario" value="{{useremail}}" />
      {{if useremail_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{useremail_error}}
      </div>
      {{endif useremail_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="username">Usuario</label>
      <input type="text" class="col-12 col-m-9"  {{~readonly}} name="username" id="username" placehoder="Descripción del Producto" value="{{username}}"/>
      {{if username_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{username_error}}
      </div>
      {{endif username_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="userpswd">Contraseña</label>
      <input class="col-12 col-m-9" {{~readonly}} type="text" name="userpswd" id="userpswd" placehoder="" value="{{userpswd}}" />
      {{if userpswd_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{userpswd_error}}
      </div>
      {{endif userpswd_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="userfching">Fecha de creación</label>
      <input class="col-12 col-m-9" {{~readonly}} type="datetime-local" name="userfching" id="userfching" value="{{userfching}}" />
      {{if userfching_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{userfching_error}}
      </div>
      {{endif userfching_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="userpswdest">Estado de la Contraseña</label>
      <select name="userpswdest" id="userpswdest" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        <option value="ACT" {{userpswdest_act}}>Activo</option>
        <option value="INA" {{userpswdest_ina}}>Inactivo</option>
      </select>
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="userpswdexp">Fecha de expiración</label>
      <input class="col-12 col-m-9" {{~readonly}} type="datetime-local" name="userpswdexp" id="userpswdexp" value="{{userpswdexp}}" />
      {{if userpswdexp_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{userpswdexp_error}}
      </div>
      {{endif userpswdexp_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="userest">Estado del Usuario</label>
      <select name="userest" id="userest" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        <option value="ACT" {{userest_act}}>Activo</option>
        <option value="INA" {{userest_ina}}>Inactivo</option>
      </select>
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="useractcod">ActCod</label>
      <input class="col-12 col-m-9" {{~readonly}} type="text" name="useractcod" id="useractcod" placehoder="Nombre del Producto" value="{{useractcod}}" />
      {{if useractcod_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{useractcod_error}}
      </div>
      {{endif useractcod_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="userpswdchg">Contraseña Nueva</label>
      <input class="col-12 col-m-9" {{~readonly}} type="text" name="userpswdchg" id="userpswdchg" placehoder="Nombre del Producto" value="{{userpswdchg}}" />
      {{if userpswdchg_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{userpswdchg_error}}
      </div>
      {{endif userpswdchg_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="usertipo">Tipo del Usuario</label>
      <select name="usertipo" id="usertipo" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        <option value="NOR" {{usertipo_nor}}>Normal</option>
        <option value="CON" {{usertipo_con}}>Consultor</option>
        <option value="CLI" {{usertipo_cli}}>Cliente</option>
      </select>
    </div>
    {{endwith usuario}}
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
      window.location.assign("index.php?page=Usuarios_Usuarios");
    });
  });
</script>