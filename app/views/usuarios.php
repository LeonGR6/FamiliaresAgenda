<?php
require_once '../models/conexion.php';

if (!isset($db)) {
    die("Error: No se pudo establecer conexión a la base de datos");
}

$query = $db->prepare("SELECT id, nombre, username, email, cargo, created_at FROM usuarios ORDER BY created_at DESC");
$query->execute();
$usuarios = $query->fetchAll(PDO::FETCH_ASSOC);

include 'inc/header.php';
include 'inc/navbar_app.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
            <div class="card-header text-center text-dark container-fluid bg-light rounded mb-4">
                <h1 class="h3 mb-0">Usuarios Registrados</h1>
            </div>
            
            <div class="table-responsive mx-auto">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">

                        <div class="d-flex justify-content-between mb-3">
                                    <a href="modals/registrar_usuarios.php" class="btn btn-outline-success border btn-md">
                                        <i class="fas fa-user"></i> Registrar Usuario
                                    </a>
                                </div>
                        
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Cargo</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['username']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td>
                                <span class="badge <?= 
                                    $usuario['cargo'] == 'Administrador' ? 'badge-dark' :
                                    ($usuario['cargo'] == 'Personal' ? 'badge-success' :
                                    ($usuario['cargo'] == 'Espectador' ? 'badge-danger' : 'badge-secondary'))
                                ?>">
                                    <?= htmlspecialchars($usuario['cargo']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($usuario['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm editar-usuario" 
                                        data-id="<?= $usuario['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($usuario['nombre']) ?>"
                                        data-username="<?= htmlspecialchars($usuario['username']) ?>"
                                        data-email="<?= htmlspecialchars($usuario['email']) ?>"
                                        data-cargo="<?= htmlspecialchars($usuario['cargo']) ?>">
                                    <i class=""></i> Editar
                                </button>
                                
                                <button class="btn btn-danger btn-sm eliminar-usuario" 
                                        data-id="<?= $usuario['id'] ?>">
                                    <i class=""></i> Eliminar
                                </button>
                                
                              
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>

<script>
   $(document).on('click', '.editar-usuario', function() {
    const boton = $(this);
    const id = boton.data('id');
    let nombre = boton.data('nombre');
    let username = boton.data('username');
    let email = boton.data('email');
    let cargo = boton.data('cargo');
    
    // Función para validar email
    function validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Editar Nombre
    nombre = prompt("Editar Nombre:", nombre);
    if (nombre === null) return;
    
    // Editar Username
    username = prompt("Editar Nombre de Usuario:", username);
    if (username === null) return;
    if (username.length < 3) {
        alert("El nombre de usuario debe tener al menos 3 caracteres");
        return;
    }
    
    // Editar Email
    email = prompt("Editar Email:", email);
    if (email === null) return;
    if (!validarEmail(email)) {
        alert("Por favor ingrese un email válido");
        return;
    }
    
    // Editar Cargo
    cargo = prompt("Editar Cargo (Administrador/Personal/Espectador):", cargo);
    if (cargo === null) return;
    if (!['Administrador', 'Personal', 'Espectador'].includes(cargo)) {
        alert("Cargo no válido. Debe ser: Administrador, Personal o Espectador");
        return;
    }
    
    // Confirmar cambios
    if (confirm(`¿Confirmas actualizar el usuario con estos datos?\n\nNombre: ${nombre}\nUsuario: ${username}\nEmail: ${email}\nCargo: ${cargo}`)) {
        // Feedback visual
        boton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: '../controllers/update_usuario.php',
            type: 'POST',
            data: {
                id: id,
                nombre: nombre,
                username: username,
                email: email,
                cargo: cargo
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert("✅ " + response.success);
                    location.reload(); // Recargar para ver cambios
                } else {
                    alert("⚠️ " + (response.error || "Error al actualizar"));
                }
            },
            error: function(xhr) {
                alert("❌ Error de conexión");
                console.error("Error detallado:", xhr.responseText);
            },
            complete: function() {
                boton.prop('disabled', false).html('<i class="fas fa-edit"></i> Editar');
            }
        });
    }
});



    





    $(document).on('click', '.eliminar-usuario', function() {
    const boton = $(this);
    const id = boton.data('id');
    const nombreUsuario = boton.closest('tr').find('td:nth-child(2)').text();
    
    if (!confirm(`¿Estás seguro de eliminar al usuario ${nombreUsuario}?`)) {
        return;
    }

    // Feedback visual
    const textoOriginal = boton.html();
    boton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');

    $.ajax({
        url: '../controllers/delete_usuario.php',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Eliminar la fila con animación
                boton.closest('tr').fadeOut(400, function() {
                    $(this).remove();
                });
                // Mostrar notificación de éxito
                alert('Usuario eliminado correctamente');
            } else {
                alert('Error: ' + response.message);
                boton.prop('disabled', false).html(textoOriginal);
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error al conectar con el servidor';
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch (e) {
                console.error('Error en la respuesta:', xhr.responseText);
            }
            alert(errorMsg);
            boton.prop('disabled', false).html(textoOriginal);
        }
    });
});

</script>

