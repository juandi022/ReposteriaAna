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
                <th>Codigo de Rol</th>
                <th>Codigo de Funcion</th>
                <th>Estado de la Funcion</th>
                <th>Expiración de la Función</th>
                <th><a href="index.php?page=FuncionesRoles_FuncionRol&mode=INS">Nuevo</a></th>
            </tr>
        </thead>
        <tbody>
            {{foreach funcionesRoles}}
            <tr>
                <td>{{rolescod}}</td>
                <td>{{fncod}}</td>
                <td>{{fnrolestDesc}}</td>
                <td>{{fnexp}}</td>

                <td>
                    <a href="index.php?page=FuncionesRoles_FuncionRol&mode=DSP&rolescod={{rolescod}}&fncod={{fncod}}">Mostrar</a><br />
                    <a href="index.php?page=FuncionesRoles_FuncionRol&mode=UPD&rolescod={{rolescod}}&fncod={{fncod}}">Editar</a><br />
                    <a href="index.php?page=FuncionesRoles_FuncionRol&mode=DEL&rolescod={{rolescod}}&fncod={{fncod}}">Borrar</a>
                </td>
            </tr>
            {{endfor funcionesRoles}}
        </tbody>
    </table>
    {{pagination}}
</section>