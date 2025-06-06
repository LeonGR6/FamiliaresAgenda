<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../models/conexion.php';

// Verificar si la conexión existe
if (!isset($db)) {
    die("Error: No se pudo establecer conexión a la base de datos");
}

// Obtener el cargo del usuario actual
$query = $db->prepare("SELECT cargo FROM usuarios WHERE id = ?");
$query->execute([$_SESSION['usuario_id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Guardar si es administrador en una variable
$isAdmin = ($user['cargo'] === 'Administrador');

// Solo cargar los usuarios si es administrador
$usuarios = [];
if ($isAdmin) {
    $query = $db->prepare("SELECT id, nombre, username, email, cargo, created_at FROM usuarios ORDER BY created_at DESC");
    $query->execute();
    $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
}

include 'inc/header.php';
include 'inc/navbar_app.php';
?>

<script>
    <?php if (!$isAdmin): ?>
        alert("⚠️ Acceso restringido\n\nEste módulo es solo para administradores.");
        window.location.href = "inicio.php"; // Redirige a otra página
    <?php endif; ?>
</script>

<?php if ($isAdmin): ?>
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
                                    <a href="modals/registrar_usuarios" class="btn btn-outline-success  btn-md">
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

<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script src="/SIADO-PJAGS/public/js/usuarios.js"></script>



