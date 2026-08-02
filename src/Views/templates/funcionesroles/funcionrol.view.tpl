<section class="container-m row px-4 py-4">
  <h1>{{FormTitle}}</h1>
</section>
<section class="container-m row px-4 py-4">
  {{with funcionRol}}
  <form action="index.php?page=FuncionesRoles_FuncionRol&mode={{~mode}}&rolescod={{rolescod}}&fncod={{fncod}}" method="POST" class="col-12 col-m-8 offset-m-2">
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
      <input type="hidden" name="mode" value="{{~mode}}" />
      <input type="hidden" name="token" value="{{~funcionRol_xss_token}}" />
      {{if rolescod_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{rolescod_error}}
      </div>
      {{endif rolescod_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="fncod">Función</label>
      <select name="fncod" id="fncod" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        {{foreach ~funcionesOptions}}
        <option value="{{fncod}}" {{selected}}>{{fncod}} - {{fndsc}}</option>
        {{endfor ~funcionesOptions}}
      </select>
      {{if ~readonly}}
      <input type="hidden" name="fncod" value="{{fncod}}" />
      {{endif ~readonly}}
      {{if fncod_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{fncod_error}}
      </div>
      {{endif fncod_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="fnrolest">Estado de la Función</label>
      <select name="fnrolest" id="fnrolest" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        <option value="ACT" {{funcionRolStatus_act}}>Activo</option>
        <option value="INA" {{funcionRolStatus_ina}}>Inactivo</option>
      </select>
      {{if fnrolest_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{fnrolest_error}}
      </div>
      {{endif fnrolest_error}}
    </div>
    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="fnexp">Fecha de expiración</label>
      <input class="col-12 col-m-9" {{~readonly}} type="datetime-local" name="fnexp" id="fnexp" value="{{fnexp}}" />
      {{if fnexp_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{fnexp_error}}
      </div>
      {{endif fnexp_error}}
    </div>
    {{endwith funcionRol}}
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
      window.location.assign("index.php?page=FuncionesRoles_FuncionesRoles");
    });
  });
</script>
