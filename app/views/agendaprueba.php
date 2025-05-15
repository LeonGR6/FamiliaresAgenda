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

    <div class="container"><!--Comienza Container-->
    <br><br>
        <div class="row"><!--Comienza Row-->
           <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
                <div class="card-header text-center text-dark">
                    <h1 class="h3 mb-0" style="font-size: calc(1.2rem + 0.6vw)">Reservaciones en agenda</h1>
                </div>

        
        </div><!--Finaliza Row-->

    </div><!--Finaliza Container-->

        <div class="container">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">Sala 1</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">Sala 2</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab" aria-controls="tab3" aria-selected="false">Sala 3</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4" type="button" role="tab" aria-controls="tab4" aria-selected="false">Camara Gesel 1</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab5-tab" data-bs-toggle="tab" data-bs-target="#tab5" type="button" role="tab" aria-controls="tab5" aria-selected="false">Camara Gesel 2</button>
            </li>
            </ul>
            <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                <div class="card">
                <div class="card-header text-center text-dark">
                    <p class="text-secondary"><b>Las audiencias agendadas para sala 1</b></p>
                </div>
                <div class="card-body text-dark">
                    <?php include "calendario.php"; ?>
                </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                <div class="card">
                <div class="card-header text-center text-dark">
                    <p class="text-secondary"><b>Las audiencias agendadas para sala 2</b></p>
                </div>
                <div class="card-body text-dark">
                    <!-- Add content for Tab 2 here -->
                      <?php include "calendario.php"; ?>
                </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                <div class="card">
                <div class="card-header text-center text-dark">
                    <p class="text-secondary"><b>Las audiencias agendadas para sala 3</b></p>
                </div>
                <div class="card-body text-dark">
                    <!-- Add content for Tab 3 here -->
                </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
                <div class="card">
                <div class="card-header text-center text-dark">
                    <p class="text-secondary"><b>Las audiencias agendadas para sala 4</b></p>
                </div>
                <div class="card-body text-dark">
                    <!-- Add content for Tab 4 here -->
                </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab5" role="tabpanel" aria-labelledby="tab5-tab">
                <div class="card">
                <div class="card-header text-center text-dark">
                    <p class="text-secondary"><b>Las audiencias agendadas para sala 5</b></p>
                </div>
                <div class="card-body text-dark">
                    <!-- Add content for Tab 5 here -->
                </div>
                </div>
            </div>
            </div>
        </div>


    
    
</body>


  


<?php include("inc/footerq.php"); ?>