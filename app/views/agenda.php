<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}


require_once 'inc/header.php'; 
require_once 'inc/navbar_app.php'; 

?>
    <body>
        <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
            <div class="card-header text-center text-dark mb-5">
                <h1 class="h3 mb-4" style="font-size: calc(1.2rem + 0.6vw)">Reservaciones en agenda</h1>
            </div>

            <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
            <div class="card mx-auto" >
                <div class="card-header text-center text-dark py-2">
                    <div class="row align-items-center">
                        
                         
                        <div class="col-lg-4 mx-auto">
                           <p class="text-secondary   mb-2"><b>Filtrar por sala:</b></p>
                            <select id="salaFilter" class="form-select w-100 mx-auto form-control">
                                <option class="" value="todas">Todas las salas</option>
                                <option value="SALA 1">Sala 1</option>
                                <option value="SALA 2">Sala 2</option>
                                <option value="SALA 3">Sala 3</option>
                                <option value="CAMARA 1">Cámara Gesell 1</option>
                                <option value="CAMARA 2">Cámara Gesell 2</option>
                            </select>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body text-dark">
                    <?php include "calendario2.php"; ?>
                </div>
                  <div class="row text-center">
                        <div class="col color-1 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #afda8c;"></div>1° Familiar</div> 
                        <div class="col color-2 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #8cd6da;"></div>2° Familiar</div>
                        <div class="col color-3 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #b78cda"></div>3° Familiar</div>
                        <div class="col color-4 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #da908c"></div>4° Familiar</div>
                        <div class="col color-5 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #FFEEAD;"></div>6° Familiar</div>
                    </div>
            </div>
            
        </div>
    </body>
    <style>
        #infooral-img {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            width: 190px; /* Aumenta el tamaño de la imagen */
            height: auto;
        }
    </style>
 <img id="infooral-img" src="/SIADO-PJAGS/public/images/infooral.png" alt="Info Oral">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<?php include("inc/footerq.php"); ?>