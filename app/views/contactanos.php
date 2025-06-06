<?php require_once 'inc/header.php'; ?>
<?php require_once 'inc/navbar.php'; ?>

<body class="bodylogin" style="background-color: transparent; color: white;">

  <div class="container mt-5">
    <h2 class="fw-bold mb-4 text-white text-center">Bienvenido al Sistema de Reservas del Poder Judicial</h2>
    
    <div class="row justify-content-center mt-5">
      <div class="col-md-6">
        <div class="card  bg-opacity-75" style="background-color: rgba(33, 37, 41, 0.80);">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Contactar a Soporte</h3>
            
            <div class="text-center mb-4">
              <p>¿Necesitas ayuda? Contáctanos directamente por WhatsApp</p>
              
              <a href="https://wa.me/524493909651?text=Hola,%20necesito%20ayuda%20con%20el%20Sistema%20de%20Reservas" 
                 class="btn btn-success btn-lg mt-3 px-4" 
                 target="_blank"
                 style="border-radius: 40px;">
                <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
              </a>
              
              <div class="mt-3">
                <small class="text-muted">Horario de atención: Lunes a Viernes de 8:00 am a 4:00 pm</small>
              </div>
            </div>
            
            <div class="text-center mt-5">
              <p>O si prefieres, escríbenos a:</p>
              <i class="fas fa-envelope text-info me-2"></i>
              <a href="mailto:soporte@poderjudicial.com" 
              class="text-info fw-bold">soporte@poderjudicial.com</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>

<?php require_once 'inc/footer.php'; ?>