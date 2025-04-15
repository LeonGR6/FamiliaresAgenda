<?php require_once 'inc/header.php'; ?>
<?php require_once 'inc/defnavbar.php'; ?>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                
                        
                        <form id="registroForm" method="POST" action="/mvc-php/app/controllers/registrar_usuario.php">
                <div id="registroCard" class="card bg-dark text-light">
                    <div class="card-header text-center" style="font-size: 2rem;">Registrar Usuario</div>
                    <div class="card-body">

                        

                       

                            <div class="form-group">
                                <label for="nombre">Nombre Completo:</label>
                                <input type="text" id="nombre" class="form-control" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Usuario:</label>
                                <input type="text" id="username" class="form-control" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" class="form-control" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña:</label>
                                <input type="password" id="password" class="form-control" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="cargo">Cargo:</label>
                                <select id="cargo" name="cargo" required class="form-control">
                                    <option value="" disabled selected>Seleccione un cargo</option>
                                    <option value="Espectador">Espectador</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Administrador">Administrador</option>
                                </select>
                            </div>

                            <div class="row justify-content-center">
                                <button type="submit" class="text-center btn btn-outline-primary fw-bold">Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de éxito (oculto inicialmente) -->
    <div id="mensajeExito" class="container mt-5" style="display: none;">
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">¡Formulario de Registro Enviado con Éxito!</h4>
                    <p>Gracias por enviar tu formulario de registro. Hemos recibido tu solicitud.</p>
                    <p>¡Felicidades! 🎉 Tu registro se ha completado con éxito.
                        <br>
                    Ahora puedes explorar todas las funciones y beneficios que tenemos para ti.
                    </p>
                    <hr>
                    <p class="mb-0">Te vamos a redirigir al inicio de sesión para que accedas a tu cuenta
                     Si no ocurre automáticamente, puedes hacer clic en el siguiente enlace para continuar:
                     <a href="/mvc-php/app/views/login.php">Iniciar sesión</a>.
                    <p>Redirigiendo en <span id="countdown">11</span> segundos...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    
    <!-- Script de registro -->
    <script src="/mvc-php/public/js/registro.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>