<section>
    <h2>{{modeDsc}}</h2>
</section>

{{foreach error_global}}<div class="error">{{this}}</div>{{endfor error_global}}

<section class="grid depth-1 px-4 py-4 my-4">
    <form class="row col-12" action="index.php?page=Compras-ProveedorForm&mode={{mode}}&id_proveedor={{id_proveedor}}" method="post">
        <input type="hidden" name="mode" value="{{mode}}" />
        <input type="hidden" name="xssToken" value="{{xssToken}}" />

        {{with proveedor}}
        <div class="col-12 col-m-6">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{nombre}}" {{~readonly}} />
            {{foreach ~error_nombre}}<div class="error">{{this}}</div>{{endfor ~error_nombre}}
        </div>
        <div class="col-12 col-m-6">
            <label for="contacto">Contacto</label>
            <input type="text" name="contacto" id="contacto" value="{{contacto}}" {{~readonly}} />
        </div>
        <div class="col-12 col-m-6">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="{{telefono}}" {{~readonly}} />
        </div>
        <div class="col-12 col-m-6">
            <label for="correo">Correo</label>
            <input type="email" name="correo" id="correo" value="{{correo}}" {{~readonly}} />
            {{foreach ~error_correo}}<div class="error">{{this}}</div>{{endfor ~error_correo}}
        </div>
        <div class="col-12">
            <label for="direccion">Dirección</label>
            <textarea name="direccion" id="direccion" {{~readonly}}>{{direccion}}</textarea>
        </div>
        {{endwith proveedor}}

        <div class="col-12 col-m-6">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" {{estadoDisabled}}>
                <option value="Activo" {{estado_Activo}}>Activo</option>
                <option value="Inactivo" {{estado_Inactivo}}>Inactivo</option>
            </select>
            {{foreach error_estado}}<div class="error">{{this}}</div>{{endfor error_estado}}
        </div>

        <div class="col-12 flex align-center">
            {{if editable}}<button type="submit">Guardar</button>{{endif editable}}
            {{ifnot editable}}
                {{if modoEliminar}}<button type="submit">Confirmar eliminación</button>{{endif modoEliminar}}
            {{endifnot editable}}
            <a class="btn px-4" href="index.php?page=Compras-ProveedoresList">Volver</a>
        </div>
    </form>
</section>
