<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio</title>
  <link rel="icon" type="image/x-icon" href="http://ganagram.com/wp-content/uploads/2023/11/favicon-32x32-1.png">
<!--Bootstrap 5 Css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<!-- Nuevos cambios -->
 
<style>
    /* Ensure the card body is a flex container */
    .card-body {
        display: flex;
        flex-direction: column; /* Stack children vertically */
        justify-content: center; /* Center items vertically */
        align-items: center; /* Center items horizontally */
        height: 50vh; /* Full height of the viewport */
    }

    .img-center img, .logo-img-center img {
        max-width: 100%; /* Ensure images are responsive */
        height: auto; /* Maintain aspect ratio */
    }

    .img-center img{
        width:17.7rem;
        height:18rem;
        border-radius: 10px;
        box-shadow: 3px 3px grey, -1em 0 .4em olive;
    }
    .logo-img-center img{
        width:10rem;
        height:10rem;
        border-radius: 50%;
        box-shadow: 3px 3px grey, -1em 0 .4em olive;
    }
    h6 {
        margin-top: 20px;
        font-size: 0.8rem;
        color:red;
        text-align: center
    }

    .vacuno {
    color: white;
    background: linear-gradient(135deg, #9dc590 0%, #9dc590 25%, #6b8e76 50%, #8baa8b 75%, #a8c5a8 100%);
    text-align: center;
    font-size: 1rem;
    line-height: 1;
    margin: 0;
    padding: 8px 12px;
    border-radius: 8px;
    box-shadow: 
        0 4px 8px rgba(0,0,0,0.4),
        0 8px 16px rgba(0,0,0,0.3),
        inset 0 1px 0px rgba(255,255,255,0.1),
        inset 0 -1px 0px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.1);
    transform: perspective(1000px) rotateX(-5deg);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.vacuno::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.5s ease;
}

.vacuno:hover {
    transform: perspective(1000px) rotateX(0deg) scale(1.05);
    box-shadow: 
        0 6px 12px rgba(0,0,0,0.5),
        0 12px 24px rgba(0,0,0,0.4),
        inset 0 2px 0px rgba(255,255,255,0.15),
        inset 0 -2px 0px rgba(0,0,0,0.6);
}

.vacuno:hover::before {
    left: 100%;
}

</style>
</head>
<body>
<div class="container">
  <div class="row mt-5">
    <!-- Card 1 - GANAGRAM -->
    <div class="col-6 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-center" style="display: flex; align-items: baseline; justify-content: center; gap: 0;">
            <h1 class="gana">GANA</h1><h5 class="vacuno">VACUNO</h5>
          </div>
          <div class="img-center pt-3">
          </div>
          <div class="logo-img-center pt-3 d-flex justify-content-center">
            <a href="./vacuno/inventario_vacuno.php">
              <img src="./images/Ganagram_New_Logo-png.png" width="50%" height="50px" alt="Ganagram Logo">
            </a>
          </div>
          <h6>Unidad Produccion Vacuna #: 01012025-3526</h6>
        </div>
      </div>
    </div>

    <!-- Card 2 - BUFAGRAM -->
    <div class="col-6 col-md-3">
      <div class="card">
        <div class="card-body">
        <div class="text-center" style="display: flex; align-items: baseline; justify-content: center; gap: 0;">
            <h1 class="gana">GANA</h1><h5 class="vacuno" style="font-family: 'Arial', sans-serif; color: white;background-color: #9dc590;border-radius: 8px;">BUFALO</h5>
          </div>
          <div class="img-center pt-3">
          </div>
          <div class="logo-img-center pt-3 d-flex justify-content-center">
            <a href="../bufalino/inventario_bufalino.php">
              <img src="../bufalino/images/default_image.png" width="50%" height="50px" alt="Bufagram Logo">
            </a>
          </div>
          <h6>Unidad Produccion Bufalina #: 01012025-3527</h6>
        </div>
      </div>
    </div>

    <!-- Card 3 - PORCIGRAM -->
    <div class="col-6 col-md-3">
      <div class="card">
        <div class="card-body">
        <div class="text-center" style="display: flex; align-items: baseline; justify-content: center; gap: 0;">
            <h1 class="gana">GANA</h1><h5 class="vacuno" style="font-family: 'Arial', sans-serif; color: white;background-color: #9dc590;border-radius: 8px;">PORCINO</h5>
          </div>
          <div class="img-center pt-3">
          </div>
          <div class="logo-img-center pt-3 d-flex justify-content-center">
            <a href="../porcino/inventario_porcino.php">
              <img src="../porcino/images/default_image.png" width="50%" height="50px" alt="Porcigram Logo">
            </a>
          </div>
          <h6>Unidad Produccion Porcina #: 01012025-3528</h6>
        </div>
      </div>
    </div>

    <!-- Card 4 - AVEGRAM -->
    <div class="col-6 col-md-3">
      <div class="card">
        <div class="card-body">
        <div class="text-center" style="display: flex; align-items: baseline; justify-content: center; gap: 0;">
            <h1 class="gana">GANA</h1><h5 class="vacuno" style="font-family: 'Arial', sans-serif; color: white;background-color: #9dc590;border-radius: 8px;">AVIAR</h5>
          </div>
          <div class="img-center pt-3">
          </div>
          <div class="logo-img-center pt-3 d-flex justify-content-center">
            <a href="../aviar/inventario_aviar.php">
              <img src="../aviar/images/Avegram_logo.png" width="50%" height="50px" alt="Avegram Logo">
            </a>
          </div>
          <h6>Unidad Produccion Aviar #: 01012025-3529</h6>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>