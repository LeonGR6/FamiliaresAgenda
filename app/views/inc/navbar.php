<nav class="navbar navbar-expand-md bg-dark navbar-dark py-4">
    <div class="container-fluid">

        <!-- Logo -->
        
    
      <!-- Botón hamburguesa CORRECTO -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
              data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" 
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div>
            <a >
                <img src="/mvc-php/public/images/loginlog.png"
                 alt="Logo" 
                 style="max-height: 30px;">
            </a>
        </div>

      <!-- Contenido colapsable -->
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav me-auto mb-2 mb-md-0">
            <a>

            <li class="nav-item">
                    <a class="nav-link mx-2" href="./" id="nav-inicio">
                        <i class="fas fa-home me-1"></i> Inicio
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mx-2" href="https://www.poderjudicialags.gob.mx/Inicio" id="nav-web">
                        <i class="fas fa-globe me-1"></i> Sitio Web
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mx-2" href="contactanos.php" id="nav-contacto">
                        <i class="fas fa-envelope me-1"></i> Contacto
                    </a>
                </li>
        </ul>

 
        

        <!-- Botón de login -->
        <div class="d-flex align-items-center" style="margin-left: auto;">
        <form class="d-flex" action="login.php" method="get">
        <button class="btn btn-outline-light fw-bold" type="submit">
            <i class="fas fa-user-circle me-1"></i> Iniciar Sesión
        </button>
        </form>

        
      </div>
    </div>
  </nav>

  <!-- Bootstrap 5 JS Bundle con Popper (IMPRESCINDIBLE) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script src="/mvc-php/public/js/navbar.js"></script>