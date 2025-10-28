<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vacuno Registros</title>
<!-- Link to the Favicon -->
<link rel="icon" href="images/Ganagram_icono.ico" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!--Bootstrap 5 Css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


<!-- Include Chart.js and Chart.js DataLabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<!-- Add these in the <head> section, after your existing CSS/JS links -->

<!-- Place these in the <head> section in this exact order -->

<!-- jQuery Core (main library) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

<!-- DataTables JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables Buttons JS -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Add these in the <head> section, after your existing DataTables CSS/JS -->
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables Buttons JS -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="./vacuno.css">
<style>
    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(to right, #28a745, #20c997);
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .modal-header .btn-close {
        color: white;
        opacity: 0.8;
        transition: opacity 0.3s;
        filter: brightness(0) invert(1);
    }
    
    .modal-header .btn-close:hover {
        opacity: 1;
    }
    
    .modal-body {
        padding: 1.75rem;
        background-color: #f8f9fa;
    }
    
    .modal-footer {
        border-top: none;
        padding: 1rem 1.75rem 1.5rem;
        background-color: #f8f9fa;
    }
    
    /* Form Elements */
    .modal .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .modal .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        padding: 0.75rem 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .modal .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    .modal .form-control:hover:not(:focus) {
        border-color: #adb5bd;
    }
    
    /* Buttons */
    .modal .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        border-radius: 0.375rem;
        transition: all 0.3s;
    }
    
    .modal .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .modal .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .modal .btn-success:active {
        transform: translateY(0);
        box-shadow: none;
    }
    
    .modal .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .modal .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }
    
    .modal .btn-secondary:active {
        transform: translateY(0);
        box-shadow: none;
    }
    
    /* Animation */
    .modal.fade .modal-dialog {
        transform: scale(0.9);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
        opacity: 1;
    }
    
    /* Modal Backdrop */
    .modal-backdrop.show {
        opacity: 0.7;
        backdrop-filter: blur(3px);
    }
    
    /* Input Group */
    .input-group {
        margin-bottom: 1rem;
    }
    
    /* Input Group Text */
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ced4da;
        color: #28a745;
    }
    
    /* Focused Form Group Effect */
    .modal .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    /* Modal Highlight Animation on Open */
    @keyframes modalHighlight {
        0% {
            box-shadow: 0 0 0 rgba(40, 167, 69, 0);
        }
        50% {
            box-shadow: 0 0 30px rgba(40, 167, 69, 0.3);
        }
        100% {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
    }
    
    .modal.show .modal-content {
        animation: modalHighlight 0.5s ease forwards;
    }
    
    /* Hover effect for input groups */
    .modal .input-group:hover .input-group-text {
        background-color: #e9ecef;
        transition: background-color 0.3s;
    }
    
    /* Readonly fields styling */
    .modal input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
    
    /* Form validation styles */
    .modal .form-control:invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
    
    /* Modal title icon */
    .modal-title i {
        margin-right: 8px;
    }

    /* Back to Top Button Styling */
    .back-to-top {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
    }

    .back-to-top:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .back-to-top:active {
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .back-to-top {
            bottom: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }

    .button-label {
        display: block;
        text-align: center;
        font-size: 0.7rem;
        font-weight: bold;
        width: 100%;
        margin-top: 2px;
    }

    /* Updated styles to fit all buttons in 100vh */
    .scroll-icons-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-height: 100vh;
        overflow-y: auto;
        padding: 10px 0;
    }

    .scroll-icons-container .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
        margin-bottom: 5px;
        width: 100%;
        padding: 0 10px;
    }

    .btn.btn-outline-secondary {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 85px;
        height: 85px;
        margin: 2px;
        padding: 5px;
        border-radius: 8px;
    }

    .nav-icon {
        width: 30px;
        height: 30px;
        margin-bottom: 4px;
        object-fit: contain;
    }

    .button-label {
        display: block;
        text-align: center;
        font-size: 0.7rem;
        font-weight: bold;
        width: 100%;
        margin-top: 2px;
    }

    /* Make nav-buttons container more compact */
    #nav-buttons {
        margin-bottom: 10px;
        padding: 5px;
    }

    .icon-button-container {
        margin: 0 2px;
    }

    .icon-button {
        padding: 5px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn.btn-outline-secondary {
            width: 75px;
            height: 75px;
            margin: 2px;
        }
        
        .nav-icon {
            width: 25px;
            height: 25px;
        }
        
        .button-label {
            font-size: 0.65rem;
            font-weight: bold;
        }
    }

    @media (max-width: 480px) {
        .btn.btn-outline-secondary {
            width: 65px;
            height: 65px;
            margin: 1px;
        }
        
        .scroll-icons-container .container {
            gap: 3px;
        }
        
        .button-label {
            font-size: 0.6rem;
            font-weight: bold;
        }
    }

    /* Optimize for height */
    @media (max-height: 700px) {
        .btn.btn-outline-secondary {
            width: 70px;
            height: 70px;
        }
        
        .scroll-icons-container .container {
            margin-bottom: 3px;
        }
    }

    @media (max-height: 600px) {
        .btn.btn-outline-secondary {
            width: 60px;
            height: 60px;
        }
        
        .nav-icon {
            width: 22px;
            height: 22px;
            margin-bottom: 2px;
        }
        
        .button-label {
            font-size: 0.6rem;
            font-weight: bold;
        }
    }

    /* Target only the INSEMINACION button label */
    .btn[data-tooltip="Inseminar"] .button-label {
        font-size: 0.6rem !important;
    }

    /* Professional button styling */
    .btn.btn-outline-secondary {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 85px;
        height: 85px;
        margin: 3px;
        padding: 5px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(to bottom, #ffffff, #f8f9fa);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease-in-out;
    }

    .btn.btn-outline-secondary:hover {
        transform: translateY(-2px);
        border-color: #28a745;
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.15), 0 3px 6px rgba(0, 0, 0, 0.08);
        background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
    }

    .btn.btn-outline-secondary:active {
        transform: translateY(1px);
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1) inset;
        background: #e9ecef;
    }

    .btn.btn-outline-secondary:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
    }

    .nav-icon {
        width: 32px;
        height: 32px;
        margin-bottom: 6px;
        object-fit: contain;
        filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.1));
        transition: transform 0.2s ease;
    }

    .btn.btn-outline-secondary:hover .nav-icon {
        transform: scale(1.1);
    }

    .button-label {
        display: block;
        text-align: center;
        font-size: 0.7rem;
        font-weight: 600;
        width: 100%;
        margin-top: 3px;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        transition: color 0.2s ease;
    }

    .btn.btn-outline-secondary:hover .button-label {
        color: #28a745;
    }

    /* Container refinements */
    .scroll-icons-container .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        margin-bottom: 10px;
        width: 100%;
        padding: 0 15px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn.btn-outline-secondary {
            width: 75px;
            height: 75px;
        }
        
        .nav-icon {
            width: 28px;
            height: 28px;
        }
    }

    @media (max-width: 480px) {
        .btn.btn-outline-secondary {
            width: 65px;
            height: 65px;
            border-radius: 10px;
        }
        
        .nav-icon {
            width: 24px;
            height: 24px;
        }
        
        .button-label {
            font-size: 0.6rem;
        }
    }

    /* Keep specific font-size for INSEMINAR */
    .btn[data-tooltip="Inseminacion"] .button-label {
        font-size: 0.5rem !important;
    }
</style>


</head>
<body>
<!-- Icon Navigation Buttons -->
<div class="container" id="nav-buttons">
    <div class="container nav-icons-container" id="nav-buttons">
        <div class="icon-button-container">
            <button onclick="window.location.href='#'" class="icon-button">
                <img src="./images/Ganagram_New_Logo-png.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">INICIO</span>
        </div>
        
        <div class="icon-button-container">
            <button onclick="window.location.href='./inventario_vacuno.php'" class="icon-button">
                <img src="./images/vaca.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">INVENTARIO</span>
        </div>

        <div class="icon-button-container">
            <button onclick="window.location.href='./vacuno_registros.php'" class="icon-button">
                <img src="./images/registros.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">REGISTROS</span>
        </div>
        
        <div class="icon-button-container">
            <button onclick="window.location.href='./vacuno_indices.php'" class="icon-button">
                <img src="./images/fondo-indexado.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">INDICES</span>
        </div>
        
        <div class="icon-button-container">
            <button onclick="window.location.href='./vacuno_configuracion.php'" class="icon-button">
                <img src="./images/configuracion.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">CONFIG</span>
        </div>
    </div>
</div>

<!-- Scroll Icons Container -->
<div class="container scroll-icons-container">
    <div class="container">    
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-tooltip="Aftosa"
        aria-expanded="false"
        aria-controls="aftosa"
        onclick="window.location.href='./vacuno_register_aftosa.php'">
        <img src="./images/aftosa.png" alt="Aftosa" class="nav-icon">
        <span class="button-label">AFTOSA</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-tooltip="Aftosa"
        aria-expanded="false"
        aria-controls="brucelosis"
        onclick="window.location.href='./vacuno_register_brucelosis.php'">
        <img src="./images/brucelosis.png" alt="Brucelosis" class="nav-icon">
        <span class="button-label">BRUCELOSIS</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-tooltip="Carbunco"
        aria-expanded="false"
        aria-controls="carbunco"
        onclick="window.location.href='./vacuno_register_carbunco.php'">
        <img src="./images/carbunco.png" alt="Carbunco" class="nav-icon">
        <span class="button-label">CARBUNCO</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-tooltip="CBR"
        aria-expanded="false"
        aria-controls="cbr"
        onclick="window.location.href='./vacuno_register_cbr.php'">
        <img src="./images/cbr.png" alt="CBR" class="nav-icon">
        <span class="button-label">CBR</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#ibr" 
        data-tooltip="IBR"
        aria-expanded="false"
        aria-controls="ibr"
        onclick="window.location.href='./vacuno_register_ibr.php'">
        <img src="./images/ibr.png" alt="IBR" class="nav-icon">
        <span class="button-label">IBR</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#garrapatas" 
        data-tooltip="Garrapatas"
        aria-expanded="false"
        aria-controls="garrapatas"
        onclick="window.location.href='./vacuno_register_garrapatas.php'">
        <img src="./images/garrapatas.png" alt="Garrapatas" class="nav-icon">
        <span class="button-label">GARROTAS</span>
    </button>
    
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#alimentacion" 
        data-tooltip="Parasitos"
        aria-expanded="false"
        aria-controls="alimentacion"
        onclick="window.location.href='./vacuno_register_parasitos.php'">
        <img src="./images/parasitos.png" alt="Parasitos" class="nav-icon">
        <span class="button-label">PARASITOS</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#salud" 
        data-tooltip="Mastitis"
        aria-expanded="false"
        aria-controls="salud"
        onclick="window.location.href='./vacuno_register_mastitis.php'">
        <img src="./images/mastitis.png" alt="Mastitis" class="nav-icon">
        <span class="button-label">MASTITIS</span>
    </button>
    </div>

    <div class="container">
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#reproduccion" 
        data-tooltip="Inseminacion"
        aria-expanded="false"
        aria-controls="reproduccion"
        onclick="window.location.href='./vacuno_register_inseminacion.php'">
        <img src="./images/inseminacion.png" alt="Inseminacion" class="nav-icon">
        <span class="button-label">INSEMINAR</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#gestacion" 
        data-tooltip="Gestacion"
        aria-expanded="false"
        aria-controls="gestacion"   
        onclick="window.location.href='./vacuno_register_gestacion.php'">
        <img src="./images/gestacion.png" alt="Gestacion" class="nav-icon">
        <span class="button-label">GESTACION</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#parto" 
        data-tooltip="Parto"
        aria-expanded="false"
        aria-controls="parto"   
        onclick="window.location.href='./vacuno_register_parto.php'">
        <img src="./images/parto.png" alt="Parto" class="nav-icon"> 
        <span class="button-label">PARTO</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#destete" 
        data-tooltip="Destete"
        aria-expanded="false"
        aria-controls="destete"
        onclick="window.location.href='./vacuno_register_destete.php'">
        <img src="./images/destete.png" alt="Destete" class="nav-icon">
        <span class="button-label">DESTETE</span>
    </button>
    </div>

    <div class="container">
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#compra" 
        data-tooltip="Compras"
        aria-expanded="false"
        aria-controls="compra"
        onclick="window.location.href='./vacuno_register_compras.php'">
        <img src="./images/compras.png" alt="Compra" class="nav-icon">
        <span class="button-label">COMPRAS</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#venta" 
        data-tooltip="Venta"
        aria-expanded="false"   
        aria-controls="venta"
        onclick="window.location.href='./vacuno_register_ventas.php'">
        <img src="./images/venta.png" alt="Venta" class="nav-icon">
        <span class="button-label">VENTAS</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#deceso" 
        data-tooltip="Deceso"
        aria-expanded="false"   
        aria-controls="deceso"
        onclick="window.location.href='./vacuno_register_decesos.php'">
        <img src="./images/deceso.png" alt="Deceso" class="nav-icon">
        <span class="button-label">DECESOS</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#descarte" 
        data-tooltip="Descarte"
        aria-expanded="false"   
        aria-controls="descarte"  
        onclick="window.location.href='./vacuno_register_descarte.php'">
        <img src="./images/descarte.png" alt="Descarte" class="nav-icon">
        <span class="button-label">DESCARTE</span>
    </button>
    </div>

    <div class="container">
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#feed" 
        data-tooltip="Feed"
        aria-expanded="false"   
        aria-controls="feed"  
        onclick="window.location.href='./vacuno_register_feed.php'">    
        <img src="./images/feed.png" alt="Feed" class="nav-icon">
        <span class="button-label">ABA</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#melaza" 
        data-tooltip="Melaza"
        aria-expanded="false"   
        aria-controls="melaza"  
        onclick="window.location.href='./vacuno_register_molasses.php'"> 
        <img src="./images/melaza.png" alt="Melaza" class="nav-icon">
        <span class="button-label">MELAZA</span>
    </button>

    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#sal" 
        data-tooltip="Sal"
        aria-expanded="false"   
        aria-controls="Sal"  
        onclick="window.location.href='./vacuno_register_salt.php'">
        <img src="./images/sal.png" alt="Sal" class="nav-icon">
        <span class="button-label">SAL</span>
    </button>
    </div>

    <div class="container">
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#carne" 
        data-tooltip="Carne"
        aria-expanded="false"   
        aria-controls="carne"  
        onclick="window.location.href='./vacuno_register_meat.php'">
        <img src="./images/carne.png" alt="Carne" class="nav-icon">
        <span class="button-label">CARNE</span>
    </button>
    
    <button class="btn btn-outline-secondary mb-3" type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#leche" 
        data-tooltip="Leche"
        aria-expanded="false"   
        aria-controls="leche"  
        onclick="window.location.href='./vacuno_register_leche.php'">
        <img src="./images/leche.png" alt="Leche" class="nav-icon">
        <span class="button-label">LECHE</span>
    </button>
    </div>
</div>


<!-- Add back button before the header container -->

    <a href="./inventario_vacuno.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
    </a>   

