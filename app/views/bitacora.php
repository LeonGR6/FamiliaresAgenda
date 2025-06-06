<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once 'inc/header.php'; 
require_once 'inc/navbar_app.php'; 

// Verificación de permisos (opcional)

?>

<body>
    <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
        <!-- Título -->
        <div class="card-header text-center text-dark mb-5">
            <h1 class="h3 mb-4" style="font-size: calc(1.2rem + 0.6vw)">Bitácora del sistema</h1>
        </div>

        <!-- Filtros -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-dark">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-dark">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-dark">Usuario</label>
                        <input type="text" name="usuario" class="form-control" placeholder="Ej: admin">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Botones de exportación -->
        <div class="text-end mb-3">
            <a href="exportar_bitacora.php?formato=excel" class="btn btn-success me-2">
                <i class="fas fa-file-excel me-1"></i> Excel
            </a>
            <a href="exportar_bitacora.php?formato=pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
        </div>

        <!-- Tabla de registros -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Fecha/Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ejemplo de datos (reemplazar con consulta a BD)
                            $logs = [
                                ["1", "2024-06-06 10:30", "admin", "Inicio de sesión", "IP: 192.168.1.1"],
                                ["2", "2024-06-06 11:15", "user1", "Actualización", "Cliente ID: 100"],
                                ["3", "2024-06-06 12:00", "admin", "Eliminación", "Producto ID: 205"],
                                ["4", "2024-06-06 14:30", "user2", "Creación", "Nuevo pedido #3012"],
                            ];
                            
                            foreach ($logs as $log) {
                                echo "<tr>
                                    <td>{$log[0]}</td>
                                    <td>{$log[1]}</td>
                                    <td>{$log[2]}</td>
                                    <td>{$log[3]}</td>
                                    <td>{$log[4]}</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <nav aria-label="Paginación" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Anterior</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Siguiente</a>
                </li>
            </ul>
        </nav>

      
    

    
</body>
</html>