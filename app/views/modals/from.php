<form id="formReserva" method="post">  <!-- Cambio 1: Añadí id al formulario -->
    <p class="text-danger"><b>Los datos son obligatorios.</b></p>

            <div class="form-group">
            <label for="numero_carpeta">Número y Año de Carpeta </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">DEMANDA-</span>
                </div>
                <input type="text" class="form-control" id="numero_carpeta" name="numero_carpeta" 
                    placeholder="1245-2024" pattern="\d+-\d{4}" required>
            </div>
            <small class="form-text text-muted">Formato correcto: 1245-2024</small>
        </div>

        <div class="form-group">
    <label for="fecha_hora">Fecha y Hora </label>
    <input type="datetime-local" class="form-control" id="fecha_hora" name="fecha_hora" required>
    <small class="form-text text-muted">Selecciona la fecha y hora correspondiente.</small>
</div>

<div class="form-group">
    <label for="duracion">Duración (minutos) </label>
    <input type="number" class="form-control" id="duracion" name="duracion" 
           placeholder="EJEMPLO: 30" min="30" step="1" required>
    <small class="form-text text-muted">Ingresa la duración en minutos.</small>
</div>

<div class="form-group">
    <label for="tipo">Tipo </label>
    <input type="text" class="form-control text-uppercase" id="tipo" name="tipo" 
           placeholder="EJEMPLO: CONCILIACIÓN/AVENIMIENTO" 
           pattern="[A-ZÁÉÍÓÚÑ \/]+" 
           oninput="this.value = this.value.toUpperCase(); this.value = this.value.replace(/[^A-ZÁÉÍÓÚÑ \/]/g, '');"
           required>
    <small class="form-text text-muted">Ingrese solo letras </small>
</div>

<div class="form-group">
    <label for="puesto">Puesto </label>
    <input type="text" class="form-control text-uppercase" id="puesto" name="puesto" 
           placeholder="EJEMPLO: ANALISTA JR 1, GERENTE DE VENTAS 2" 
           pattern="[A-Z0-9 ]+" 
           oninput="this.value = this.value.toUpperCase(); this.value = this.value.replace(/[^A-Z0-9 ]/g, '');"
           required>
    <small class="form-text text-muted">Ingrese su puesto </small>
</div>

<div class="form-group">
    <label for="observaciones">Observaciones</label>
    <textarea class="form-control text-uppercase" id="observaciones" name="observaciones" 
              rows="3" maxlength="500"
              oninput="this.value = this.value.toUpperCase();"
              placeholder="INGRESE SUS OBSERVACIONES (MÁXIMO 500 CARACTERES)"></textarea>
    <small class="form-text text-muted">Opcional. Describa cualquier información adicional relevante.</small>
</div>

<div class="form-group">
    <label for="motivo">Motivo</label>
    <input type="text" class="form-control text-uppercase" id="motivo" name="motivo"
           placeholder="EJEMPLO: SOLICITUD DE REVISIÓN"
           oninput="this.value = this.value.toUpperCase()">
</div>

    <input type="hidden" name="estado" value="Pendiente">
    <input type="hidden" name="oculto" value="1">
    
    <button type="reset" class="btn btn-warning">Limpiar</button>
    <button type="submit" id="btnSubmit" class="btn btn-primary">Enviar</button> <!-- Cambio 2: Añadí id al botón -->
</form>

<script src="/mvc-php/public/js/fecha.js"></script>