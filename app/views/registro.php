<?php require_once 'inc/header.php'; ?>
<?php require_once 'inc/defnavbar.php'; ?>

                                <body>
                                <div class="container mt-5">
                                    <div class="row justify-content-center">
                                        <div class="col-md-6">

                                            
                                                    
                                        <form id="registroForm" method="POST" action="/mvc-php/app/controllers/registrar_usuario.php">
                                        <div id="registroCard" class="card bg-light shadow-lg text-dark mb-5">
                                <div class="card-header text-center bg-dark text-white" style="font-size: 2rem; ">
                                    Registrar Usuario
                                </div>
                                <div class="card-body p-4">
                                    <div class="text-center mb-2">
                                        <img src="/mvc-php/public/images/flamacom.png" alt="Logo" class="img-fluid" style="width: 100px; height: 120px;">
                                    </div>
                                <div class="card-body p-4">
                                    <div class="form-group mb-2">
                                        <label for="nombre" class="form-label">Nombre Completo:</label>
                                        <input type="text" id="nombre" class="form-control border-primary rounded-pill" style="width: 420px;" name="nombre" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="username" class="form-label">Usuario:</label>
                                        <input type="text" id="username" class="form-control rounded border-primary rounded-pill" style="width: 420px;" name="username" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" id="email" class="form-control border-primary rounded-pill" style="width: 420px;" name="email" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="password" class="form-label">Contraseña:</label>
                                        <input type="password" id="password" class="form-control border-primary rounded-pill" style="width: 420px;" name="password" required>
                                    </div>
                                    <div class=" form-group mb-5">
                                        <label for="cargo" class="form-label ">Cargo:</label>
                                        <select id="cargo" name="cargo" required class="form-control border-primary rounded-pill" style="width: 420px;">
                                            <option value="" disabled selected>Seleccione un cargo</option>
                                            <option value="Espectador">Espectador</option>
                                            <option value="Personal">Personal</option>
                                            <option value="Administrador">Administrador</option>
                                        </select>
                                    </div>
                                    <div class="row justify-content-center">
                                        <button type="submit" class="btn sanclear btn-block fw-bold rounded-pill " style="width: 300px; height: 50px;">Registrar</button>
                                    </div>
                                </div>
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
                    <p>Redirigiendo en <span id="countdown">10</span> segundos...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-2.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    
    <!-- Script de registro -->
    <script src="/mvc-php/public/js/registro.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>