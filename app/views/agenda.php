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
<div class="container-fluid">
        <div class="row">
            <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
                <div class="card-header text-center text-dark">
                    <h1 class="h3 mb-0" style="font-size: calc(1.2rem + 0.6vw)">Reservaciones en agenda</h1>
                </div>


    <div class="container">
        <div class="card">
        <div class="card-header text-center text-dark">
            <p class="text-secondary"><b>Tus citas agendadas son las siguientes</b></p>
        </div>
        <div class="text-dark">
             <?php include "calendario.php";?>
        </div>
        </div>
    </div>


    
    
</body>


  


<?php include("inc/footerq.php"); ?>