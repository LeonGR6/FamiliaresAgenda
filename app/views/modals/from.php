<form id="formReserva" method="post">  <!-- Cambio 1: Añadí id al formulario -->
    <p class="text-danger"><b>Los datos con (*) son obligatorios.</b></p>

    <div class="form-group">
        <label for="nombre">Nombre *</label>
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Escribe tus nombres" required>
        <small class="form-text text-muted">Si tienes dos nombres, colócalos aquí.</small>
    </div>

    <div class="form-group">
        <label for="apellidos">Apellidos *</label>
        <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Escribe tu apellido paterno y materno" required>
        <small class="form-text text-muted">Coloca tus apellidos.</small>
    </div>

    <div class="form-group">
        <label for="correo">Correo *</label>
        <input type="email" class="form-control" id="correo" name="correo" placeholder="correo@gmail.com" required>
    </div>

    <div class="form-group">
        <label for="servicio">Selecciona un servicio *</label>
        <select class="custom-select" id="servicio" name="servicio" required>
            <option value="" selected>Elige...</option>
            <option value="Entrega de documentos">Entrega de documentos</option>
            <option value="Audiencia">Audiencia</option>
            <option value="Sellado de oficios">Sellado de oficios</option>
        </select>
    </div>

    <div class="form-group">
        <label for="fecha">Fecha:</label>
        <input type="date" class="form-control" id="fecha" name="fecha" required>
        <div id="mensaje-error" style="color: red;"></div>
    </div>

    <div class="form-group">
        <label for="hora">Hora:</label>
        <input type="time" class="form-control" id="hora" name="hora" required>
    </div>

    <div class="form-group">
        <label for="mensaje">Mensaje adicional:</label>
        <textarea class="form-control" id="mensaje" name="mensaje" rows="3"></textarea>
    </div>
    <input type="hidden" name="estado" value="Pendiente">
    <input type="hidden" name="oculto" value="1">
    
    <button type="reset" class="btn btn-warning">Limpiar</button>
    <button type="submit" id="btnSubmit" class="btn btn-primary">Enviar</button> <!-- Cambio 2: Añadí id al botón -->
</form>

<script src="/mvc-php/public/js/fecha.js"></script>