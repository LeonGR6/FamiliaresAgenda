<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once 'inc/header.php';
require_once 'inc/navbar_app.php';
?>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="#">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#reservas">
                                <i class="fas fa-calendar-check me-2"></i>Reservas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#estadisticas">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
                <p class="lead">Panel de control del sistema de reservas</p>
                
                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Reservas Hoy</h5>
                                <h2 class="card-text">15</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Reservas Semana</h5>
                                <h2 class="card-text">87</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Salas Disponibles</h5>
                                <h2 class="card-text">5/8</h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mb-4" id="reservas">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Acciones Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                    <button class="btn btn-primary me-md-2" type="button">
                                        <i class="fas fa-plus me-2"></i>Nueva Reserva
                                    </button>
                                    <button class="btn btn-outline-secondary me-md-2" type="button">
                                        <i class="fas fa-search me-2"></i>Buscar Reserva
                                    </button>
                                    <button class="btn btn-outline-success" type="button">
                                        <i class="fas fa-print me-2"></i>Reporte Diario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Calendar and Recent Reservations -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Calendario de Reservas</h5>
                            </div>
                            <div class="card-body">
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Reservas Recientes</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Sala</th>
                                                <th>Hora</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2023-06-15</td>
                                                <td>Sala Principal</td>
                                                <td>09:00 - 11:00</td>
                                                <td><span class="badge bg-success">Confirmada</span></td>
                                            </tr>
                                            <tr>
                                                <td>2023-06-15</td>
                                                <td>Sala 2</td>
                                                <td>14:00 - 16:00</td>
                                                <td><span class="badge bg-warning">Pendiente</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Section -->
                <div class="row mt-4" id="estadisticas">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Estadísticas de Reservas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Reservas por Día de la Semana</h6>
                                        <canvas id="weekdayChart" height="200"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Reservas por Tipo de Audiencia</h6>
                                        <canvas id="audienceTypeChart" height="200"></canvas>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6>Reservas Mensuales</h6>
                                        <canvas id="monthlyChart" height="150"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    
    <script>
        // Initialize Calendar
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    {
                        title: 'Audiencia Penal',
                        start: '2023-06-15T09:00:00',
                        end: '2023-06-15T11:00:00',
                        backgroundColor: '#dc3545'
                    },
                    {
                        title: 'Audiencia Civil',
                        start: '2023-06-15T14:00:00',
                        end: '2023-06-15T16:00:00',
                        backgroundColor: '#0d6efd'
                    }
                ]
            });
            calendar.render();
        });

        // Initialize Charts
        // Weekday Chart
        const weekdayCtx = document.getElementById('weekdayChart').getContext('2d');
        const weekdayChart = new Chart(weekdayCtx, {
            type: 'bar',
            data: {
                labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                datasets: [{
                    label: 'Reservas',
                    data: [12, 19, 15, 20, 18, 5],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Audience Type Chart
        const audienceCtx = document.getElementById('audienceTypeChart').getContext('2d');
        const audienceChart = new Chart(audienceCtx, {
            type: 'pie',
            data: {
                labels: ['Penal', 'Civil', 'Familia', 'Laboral', 'Otros'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: [
                        '#dc3545',
                        '#0d6efd',
                        '#ffc107',
                        '#198754',
                        '#6c757d'
                    ]
                }]
            }
        });

        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Reservas 2023',
                    data: [120, 135, 150, 145, 160, 90],
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });
    </script>
</body>
</html>