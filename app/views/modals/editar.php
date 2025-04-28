<!-- Modal de edición -->
<div class="modal fade" id="modalEditarReserva" tabindex="-1" aria-labelledby="modalEditarLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg- text-dark">
                <h5 class="modal-title" id="modalEditarLabel">
                    <i class="fas fa-edit"></i> Editar Reserva #<span id="reservaIdHeader"></span>
                </h5>

              
            </div>
            
            <!-- Formulario -->
            <form id="formEditarReserva">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="row g-3">
                        <!-- Nombre (asociado con for/id) -->
                        <div class="col-md-6">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                        </div>
                        
                        <!-- Apellidos (asociado con for/id) -->
                        <div class="col-md-6">
                            <label for="edit_apellidos" class="form-label">Apellidos</label>
                            <input type="text" id="edit_apellidos" name="apellidos" class="form-control" required>
                        </div>
                        
                        <!-- Correo (asociado con for/id) -->
                        <div class="col-md-6">
                            <label for="edit_correo" class="form-label">Correo</label>
                            <input type="email" id="edit_correo" name="correo" class="form-control" required>
                        </div>
                        
                        <!-- Servicio (asociado con for/id) -->
                        <div class="col-md-6">
                            <label for="edit_servicio" class="form-label">Servicio</label>
                            <input type="text" id="edit_servicio" name="servicio" class="form-control" required>
                        </div>
                        
                        <!-- Fecha (asociado con for/id) -->
                        <div class="col-md-6">
                            <label for="edit_fecha" class="form-label">Fecha</label>
                            <input type="date" id="edit_fecha" name="fecha" class="form-control" required>
                        </div>
                        
                        <!-- Hora (asociado con for/id) -->
                        <div class="col-md-6">
                            <label for="edit_hora" class="form-label">Hora</label>
                            <input type="time" id="edit_hora" name="hora" class="form-control" required>
                        </div>
                        
                        <!-- Mensaje (asociado con for/id) -->
                        <div class="col-12">
                            <label for="edit_mensaje" class="form-label">Mensaje Adicional</label>
                            <textarea id="edit_mensaje" name="mensaje" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <!-- Estado (asociado con for/id) -->
                        <div class="col-md-6">
                            <label for="edit_estado" class="form-label">Estado</label>
                            <select id="edit_estado" name="estado" class="form-select" required>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Confirmado">Confirmado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                </div>



                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class=""></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="btnEditarReserva">
                        <i class=""></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>