<?php

require_once './pdo_conexion.php';  // Go up one directory since inventario_vacuno.php is in the vacuno folder
// Now you can use $conn for database queries

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Indices Ganaderos</title>
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

<!-- ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<!-- Custom Modal Styles -->
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

    /* Chart container responsive styling */
    .chart-container {
        position: relative;
        height: min(400px, 50vh);
        width: 100%;
        margin: auto;
    }

    /* Export button styling */
    #exportMilkRevenuePDF {
        transition: all 0.3s ease;
    }

    #exportMilkRevenuePDF:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    #exportMilkRevenuePDF:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

.button-label {
    display: block;
    text-align: center;
    font-size: 0.7rem;
    width: 100%;
}

    .error-message {
        background-color: #ffebee;
        color: #c62828;
        padding: 10px;
        margin: 10px 0;
        border-radius: 20px;
        border-bottom-left-radius: 5px;
    }

    .full-width-button {
        width: 100% !important;
        display: block !important;
        box-sizing: border-box !important;
    }

    /* Initial system message style */
</style>

</head>
<body>
<!-- Navigation Title -->

<!-- Icon Navigation Buttons -->

<div class="container nav-icons-container">
    <div class="icon-button-container">
        <button onclick="window.location.href='../inicio.php'" class="icon-button">
            <img src="./images/Ganagram_New_Logo-png.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">INICIO</span>
    </div>
    
    <div class="icon-button-container">
        <button onclick="window.location.href='./inventario_vacuno.php'" class="icon-button">
            <img src="./images/robot-de-chat.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">VETERINARIO IA</span>
    </div>

    <div class="icon-button-container">
        <button onclick="window.location.href='./vacuno_registros.php'" class="icon-button">
            <img src="./images/registros.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">REGISTROS</span>
    </div>
    
    <div class="icon-button-container">
            <button onclick="window.location.href='./vacuno_configuracion.php'" class="icon-button">
                <img src="./images/configuracion.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">CONFIG</span>
        </div>

</div>

<!-- Librerias -->
<!-- Bootstrap  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<!-- Librerias -->
<!-- Bootstrap  -->
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- Popper Js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
<!-- para usar botones en datatables JS -->  
    <script src="https://ganagram.com/ganagram/crud/datatables/Buttons-1.5.6/js/dataTables.buttons.min.js"></script>  
    <script src="https://ganagram.com/ganagram/crud/datatables/JSZip-2.5.0/jszip.min.js"></script>    
    <script src="https://ganagram.com/ganagram/crud/datatables/pdfmake-0.1.36/pdfmake.min.js"></script>    
    <script src="https://ganagram.com/ganagram/crud/datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
    <script src="https://ganagram.com/ganagram/crud/datatables/Buttons-1.5.6/js/buttons.html5.min.js"></script>
<!-- Ion Icon Js -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>    
<!-- Custom Menu Js -->
<script src="https://ganagram.com/ganagram/html/js/menu.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- Back to top button -->
<button id="backToTop" class="back-to-top" onclick="scrollToTop()" title="Volver arriba">
    <div class="arrow-up"><i class="fa-solid fa-arrow-up"></i></div>
</button>

<script>
window.onscroll = function() {
    const backToTopButton = document.getElementById("backToTop");
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        backToTopButton.style.display = "flex";
    } else {
        backToTopButton.style.display = "none";
    }
};

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>

<!-- Milk Revenue Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #28a745, #20c997); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-chart-line me-2"></i>
                Flujo de Ingresos por Leche
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportMilkRevenuePDF" class="btn btn-success">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="milkRevenueChart"></canvas>
            </div>
            <div id="chartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Milk Revenue Chart Implementation
let milkRevenueChart = null;

async function loadMilkRevenueChart() {
    const messageDiv = document.getElementById('chartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_milk_revenue_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de ingresos por leche disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const revenues = data.map(item => parseFloat(item.total_milk_value));

        // Chart configuration
        const ctx = document.getElementById('milkRevenueChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (milkRevenueChart) {
            milkRevenueChart.destroy();
        }

        milkRevenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ingresos por Leche ($)',
                    data: revenues,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#20c997',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Ingresos Mensuales por Producción de Leche',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#28a745',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Ingresos: $${context.parsed.y.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Ingresos ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error loading milk revenue chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality
document.getElementById('exportMilkRevenuePDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('milkRevenueChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(40, 167, 69);
        pdf.text('Reporte de Ingresos por Leche', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `ingresos_leche_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load chart when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
});
</script>

<!-- Peso Revenue Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #dc3545, #fd7e14); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-weight me-2"></i>
                Flujo de Ingresos por Peso
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportPesoRevenuePDF" class="btn btn-danger">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="pesoRevenueChart"></canvas>
            </div>
            <div id="pesoChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Peso Revenue Chart Implementation
let pesoRevenueChart = null;

async function loadPesoRevenueChart() {
    const messageDiv = document.getElementById('pesoChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_peso_revenue_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de ingresos por peso disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const revenues = data.map(item => parseFloat(item.total_peso_value));

        // Chart configuration
        const ctx = document.getElementById('pesoRevenueChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (pesoRevenueChart) {
            pesoRevenueChart.destroy();
        }

        pesoRevenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ingresos por Peso ($)',
                    data: revenues,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#dc3545',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#fd7e14',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Ingresos Mensuales por Venta de Ganado (Peso)',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#dc3545',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Ingresos: $${context.parsed.y.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Ingresos ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error loading peso revenue chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Peso Revenue
document.getElementById('exportPesoRevenuePDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('pesoRevenueChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(220, 53, 69);
        pdf.text('Reporte de Ingresos por Peso', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `ingresos_peso_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load peso chart when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
});
</script>

<!-- Concentrado Expense Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #007bff, #6610f2); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-seedling me-2"></i>
                Gastos en Concentrado
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportConcentradoExpensePDF" class="btn btn-primary">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="concentradoExpenseChart"></canvas>
            </div>
            <div id="concentradoChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Concentrado Expense Chart Implementation
let concentradoExpenseChart = null;

async function loadConcentradoExpenseChart() {
    const messageDiv = document.getElementById('concentradoChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_concentrado_expense_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de gastos en concentrado disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const expenses = data.map(item => parseFloat(item.total_concentrado_expense));

        // Chart configuration
        const ctx = document.getElementById('concentradoExpenseChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (concentradoExpenseChart) {
            concentradoExpenseChart.destroy();
        }

        concentradoExpenseChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Gastos en Concentrado ($)',
                    data: expenses,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#6610f2',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Gastos Mensuales en Concentrado',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#007bff',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Gastos: $${context.parsed.y.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Gastos ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading concentrado expense chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Concentrado Expense
document.getElementById('exportConcentradoExpensePDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('concentradoExpenseChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(0, 123, 255);
        pdf.text('Reporte de Gastos en Concentrado', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `gastos_concentrado_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
});
</script>

<!-- Melaza Expense Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #fd7e14, #ffc107); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-tint me-2"></i>
                Gastos en Melaza
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportMelazaExpensePDF" class="btn btn-warning">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="melazaExpenseChart"></canvas>
            </div>
            <div id="melazaChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Melaza Expense Chart Implementation
let melazaExpenseChart = null;

async function loadMelazaExpenseChart() {
    const messageDiv = document.getElementById('melazaChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_melaza_expense_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de gastos en melaza disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const expenses = data.map(item => parseFloat(item.total_melaza_expense));

        // Chart configuration
        const ctx = document.getElementById('melazaExpenseChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (melazaExpenseChart) {
            melazaExpenseChart.destroy();
        }

        melazaExpenseChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Gastos en Melaza ($)',
                    data: expenses,
                    borderColor: '#fd7e14',
                    backgroundColor: 'rgba(253, 126, 20, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fd7e14',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#ffc107',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Gastos Mensuales en Melaza',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#fd7e14',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Gastos: $${context.parsed.y.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Gastos ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error loading melaza expense chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Melaza Expense
document.getElementById('exportMelazaExpensePDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('melazaExpenseChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(253, 126, 20);
        pdf.text('Reporte de Gastos en Melaza', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `gastos_melaza_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
});
</script>

<!-- Sal Expense Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #6f42c1, #e83e8c); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-fill-drip me-2"></i>
                Gastos en Sal
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportSalExpensePDF" class="btn btn-secondary" style="background-color: #6f42c1; border-color: #6f42c1;">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="salExpenseChart"></canvas>
            </div>
            <div id="salChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Sal Expense Chart Implementation
let salExpenseChart = null;

async function loadSalExpenseChart() {
    const messageDiv = document.getElementById('salChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_sal_expense_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de gastos en sal disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const expenses = data.map(item => parseFloat(item.total_sal_expense));

        // Chart configuration
        const ctx = document.getElementById('salExpenseChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (salExpenseChart) {
            salExpenseChart.destroy();
        }

        salExpenseChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Gastos en Sal ($)',
                    data: expenses,
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6f42c1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#e83e8c',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Gastos Mensuales en Sal',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#6f42c1',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Gastos: $${context.parsed.y.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Gastos ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error loading sal expense chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Sal Expense
document.getElementById('exportSalExpensePDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('salExpenseChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(111, 66, 193);
        pdf.text('Reporte de Gastos en Sal', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `gastos_sal_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
});
</script>

<!-- Vaccine Costs Bar Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #17a2b8, #28a745); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-syringe me-2"></i>
                Costos Totales de Vacunas
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportVaccineCostsPDF" class="btn btn-info">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="vaccineCostsChart"></canvas>
            </div>
            <div id="vaccineChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Vaccine Costs Bar Chart Implementation
let vaccineCostsChart = null;

async function loadVaccineCostsChart() {
    const messageDiv = document.getElementById('vaccineChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_vaccine_costs_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de costos de vacunas disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => item.vaccine_name);
        const costs = data.map(item => parseFloat(item.total_cost));

        // Generate different colors for each bar
        const backgroundColors = [
            'rgba(23, 162, 184, 0.8)',  // Teal
            'rgba(40, 167, 69, 0.8)',   // Green
            'rgba(0, 123, 255, 0.8)',   // Blue
            'rgba(255, 193, 7, 0.8)',   // Yellow
            'rgba(220, 53, 69, 0.8)',   // Red
            'rgba(111, 66, 193, 0.8)',  // Purple
            'rgba(253, 126, 20, 0.8)'   // Orange
        ];

        const borderColors = [
            '#17a2b8',  // Teal
            '#28a745',  // Green
            '#007bff',  // Blue
            '#ffc107',  // Yellow
            '#dc3545',  // Red
            '#6f42c1',  // Purple
            '#fd7e14'   // Orange
        ];

        // Chart configuration
        const ctx = document.getElementById('vaccineCostsChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (vaccineCostsChart) {
            vaccineCostsChart.destroy();
        }

        vaccineCostsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Costo Total de Vacunas ($)',
                    data: costs,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Costos Totales por Tipo de Vacuna',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: false // Hide legend for bar chart as colors are self-explanatory
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#17a2b8',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return `Costo Total: $${context.parsed.y.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tipo de Vacuna',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Costo Total ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading vaccine costs chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Vaccine Costs
document.getElementById('exportVaccineCostsPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('vaccineCostsChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(23, 162, 184);
        pdf.text('Reporte de Costos de Vacunas', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `costos_vacunas_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
});
</script>

<!-- Monthly Pregnancy Column Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #e83e8c, #fd7e14); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-baby me-2"></i>
                Gestaciones Mensuales
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportPregnancyPDF" class="btn" style="background-color: #e83e8c; border-color: #e83e8c; color: white;">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="pregnancyChart"></canvas>
            </div>
            <div id="pregnancyChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Pregnancy Column Chart Implementation
let pregnancyChart = null;

async function loadPregnancyChart() {
    const messageDiv = document.getElementById('pregnancyChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_pregnancy_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de gestaciones disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const pregnancyCounts = data.map(item => parseInt(item.pregnancy_count));
        const pregnantAnimals = data.map(item => item.pregnant_animals);

        // Chart configuration
        const ctx = document.getElementById('pregnancyChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (pregnancyChart) {
            pregnancyChart.destroy();
        }

        pregnancyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Gestaciones',
                    data: pregnancyCounts,
                    backgroundColor: 'rgba(232, 62, 140, 0.8)',
                    borderColor: '#e83e8c',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    pregnantAnimals: pregnantAnimals // Custom property to store animal IDs
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Número de Gestaciones por Mes',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#e83e8c',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label}`;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                const animalsData = context.dataset.pregnantAnimals[context.dataIndex];
                                
                                let tooltip = [`Gestaciones: ${count}`];
                                
                                if (animalsData && animalsData.trim() !== '') {
                                    tooltip.push(''); // Empty line for spacing
                                    tooltip.push('Animales gestantes:');
                                    
                                    // Split the animal IDs and format them nicely
                                    const animalIds = animalsData.split(', ');
                                    if (animalIds.length <= 5) {
                                        // If 5 or fewer animals, show all
                                        animalIds.forEach(id => {
                                            tooltip.push(`• ${id.trim()}`);
                                        });
                                    } else {
                                        // If more than 5, show first 4 and count
                                        animalIds.slice(0, 4).forEach(id => {
                                            tooltip.push(`• ${id.trim()}`);
                                        });
                                        tooltip.push(`• ... y ${animalIds.length - 4} más`);
                                    }
                                }
                                
                                return tooltip;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Gestaciones',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading pregnancy chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Pregnancy Chart
document.getElementById('exportPregnancyPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('pregnancyChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(232, 62, 140);
        pdf.text('Reporte de Gestaciones Mensuales', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `gestaciones_mensuales_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
    loadPregnancyChart();
});
</script>

<!-- Monthly Births Column Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #20c997, #28a745); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-heart me-2"></i>
                Partos Mensuales
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportBirthsPDF" class="btn btn-success">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="birthsChart"></canvas>
            </div>
            <div id="birthsChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Births Column Chart Implementation
let birthsChart = null;

async function loadBirthsChart() {
    const messageDiv = document.getElementById('birthsChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_births_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de partos disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const birthCounts = data.map(item => parseInt(item.birth_count));
        const birthAnimals = data.map(item => item.birth_animals);

        // Chart configuration
        const ctx = document.getElementById('birthsChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (birthsChart) {
            birthsChart.destroy();
        }

        birthsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Partos',
                    data: birthCounts,
                    backgroundColor: 'rgba(32, 201, 151, 0.8)',
                    borderColor: '#20c997',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    birthAnimals: birthAnimals // Custom property to store animal IDs
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Número de Partos por Mes',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#20c997',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label}`;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                const animalsData = context.dataset.birthAnimals[context.dataIndex];
                                
                                let tooltip = [`Partos: ${count}`];
                                
                                if (animalsData && animalsData.trim() !== '') {
                                    tooltip.push(''); // Empty line for spacing
                                    tooltip.push('Animales que parieron:');
                                    
                                    // Split the animal IDs and format them nicely
                                    const animalIds = animalsData.split(', ');
                                    if (animalIds.length <= 5) {
                                        // If 5 or fewer animals, show all
                                        animalIds.forEach(id => {
                                            tooltip.push(`• ${id.trim()}`);
                                        });
                                    } else {
                                        // If more than 5, show first 4 and count
                                        animalIds.slice(0, 4).forEach(id => {
                                            tooltip.push(`• ${id.trim()}`);
                                        });
                                        tooltip.push(`• ... y ${animalIds.length - 4} más`);
                                    }
                                }
                                
                                return tooltip;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Partos',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading births chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Births Chart
document.getElementById('exportBirthsPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('birthsChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(32, 201, 151);
        pdf.text('Reporte de Partos Mensuales', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `partos_mensuales_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
    loadPregnancyChart();
    loadBirthsChart();
});
</script>

<!-- Monthly Animal Purchases Column Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #6610f2, #007bff); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-shopping-cart me-2"></i>
                Compras Mensuales de Animales
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportPurchasesPDF" class="btn" style="background-color: #6610f2; border-color: #6610f2; color: white;">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="purchasesChart"></canvas>
            </div>
            <div id="purchasesChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Animal Purchases Column Chart Implementation
let purchasesChart = null;

async function loadPurchasesChart() {
    const messageDiv = document.getElementById('purchasesChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_purchases_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de compras de animales disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const purchaseCounts = data.map(item => parseInt(item.purchase_count));
        const purchasedAnimals = data.map(item => item.purchased_animals);

        // Chart configuration
        const ctx = document.getElementById('purchasesChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (purchasesChart) {
            purchasesChart.destroy();
        }

        purchasesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Compras de Animales',
                    data: purchaseCounts,
                    backgroundColor: 'rgba(102, 16, 242, 0.8)',
                    borderColor: '#6610f2',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    purchasedAnimals: purchasedAnimals // Custom property to store animal data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Número de Compras de Animales por Mes',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#6610f2',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label}`;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                const animalsData = context.dataset.purchasedAnimals[context.dataIndex];
                                
                                let tooltip = [`Compras: ${count}`];
                                
                                if (animalsData && animalsData.trim() !== '') {
                                    tooltip.push(''); // Empty line for spacing
                                    tooltip.push('Animales comprados:');
                                    
                                    // Split the animal data and format them nicely
                                    const animalData = animalsData.split(', ');
                                    if (animalData.length <= 5) {
                                        // If 5 or fewer animals, show all
                                        animalData.forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                    } else {
                                        // If more than 5, show first 4 and count
                                        animalData.slice(0, 4).forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                        tooltip.push(`• ... y ${animalData.length - 4} más`);
                                    }
                                }
                                
                                return tooltip;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Compras',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading purchases chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Purchases Chart
document.getElementById('exportPurchasesPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('purchasesChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(102, 16, 242);
        pdf.text('Reporte de Compras Mensuales de Animales', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `compras_animales_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
    loadPregnancyChart();
    loadBirthsChart();
    loadPurchasesChart();
});
</script>

<!-- Monthly Animal Sales Column Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #fd7e14, #ffc107); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-hand-holding-usd me-2"></i>
                Ventas Mensuales de Animales
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportSalesPDF" class="btn btn-warning">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
            <div id="salesChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Animal Sales Column Chart Implementation
let salesChart = null;

async function loadSalesChart() {
    const messageDiv = document.getElementById('salesChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_sales_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de ventas de animales disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const salesCounts = data.map(item => parseInt(item.sales_count));
        const soldAnimals = data.map(item => item.sold_animals);

        // Chart configuration
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (salesChart) {
            salesChart.destroy();
        }

        salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas de Animales',
                    data: salesCounts,
                    backgroundColor: 'rgba(253, 126, 20, 0.8)',
                    borderColor: '#fd7e14',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    soldAnimals: soldAnimals // Custom property to store animal data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Número de Ventas de Animales por Mes',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#fd7e14',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label}`;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                const animalsData = context.dataset.soldAnimals[context.dataIndex];
                                
                                let tooltip = [`Ventas: ${count}`];
                                
                                if (animalsData && animalsData.trim() !== '') {
                                    tooltip.push(''); // Empty line for spacing
                                    tooltip.push('Animales vendidos:');
                                    
                                    // Split the animal data and format them nicely
                                    const animalData = animalsData.split(', ');
                                    if (animalData.length <= 5) {
                                        // If 5 or fewer animals, show all
                                        animalData.forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                    } else {
                                        // If more than 5, show first 4 and count
                                        animalData.slice(0, 4).forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                        tooltip.push(`• ... y ${animalData.length - 4} más`);
                                    }
                                }
                                
                                return tooltip;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Ventas',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading sales chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Sales Chart
document.getElementById('exportSalesPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('salesChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(253, 126, 20);
        pdf.text('Reporte de Ventas Mensuales de Animales', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `ventas_animales_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
    loadPregnancyChart();
    loadBirthsChart();
    loadPurchasesChart();
    loadSalesChart();
});
</script>

<!-- Monthly Animal Deaths Column Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #6c757d, #495057); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-cross me-2"></i>
                Decesos Mensuales de Animales
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportDeathsPDF" class="btn btn-secondary">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="deathsChart"></canvas>
            </div>
            <div id="deathsChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Animal Deaths Column Chart Implementation
let deathsChart = null;

async function loadDeathsChart() {
    const messageDiv = document.getElementById('deathsChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_deaths_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de decesos de animales disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const deathsCounts = data.map(item => parseInt(item.deaths_count));
        const deceasedAnimals = data.map(item => item.deceased_animals);

        // Chart configuration
        const ctx = document.getElementById('deathsChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (deathsChart) {
            deathsChart.destroy();
        }

        deathsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Decesos de Animales',
                    data: deathsCounts,
                    backgroundColor: 'rgba(108, 117, 125, 0.8)',
                    borderColor: '#6c757d',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    deceasedAnimals: deceasedAnimals // Custom property to store animal data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Número de Decesos de Animales por Mes',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#6c757d',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label}`;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                const animalsData = context.dataset.deceasedAnimals[context.dataIndex];
                                
                                let tooltip = [`Decesos: ${count}`];
                                
                                if (animalsData && animalsData.trim() !== '') {
                                    tooltip.push(''); // Empty line for spacing
                                    tooltip.push('Animales fallecidos:');
                                    
                                    // Split the animal data and format them nicely
                                    const animalData = animalsData.split(', ');
                                    if (animalData.length <= 5) {
                                        // If 5 or fewer animals, show all
                                        animalData.forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                    } else {
                                        // If more than 5, show first 4 and count
                                        animalData.slice(0, 4).forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                        tooltip.push(`• ... y ${animalData.length - 4} más`);
                                    }
                                }
                                
                                return tooltip;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Decesos',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading deaths chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Deaths Chart
document.getElementById('exportDeathsPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('deathsChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(108, 117, 125);
        pdf.text('Reporte de Decesos Mensuales de Animales', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `decesos_animales_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
    loadPregnancyChart();
    loadBirthsChart();
    loadPurchasesChart();
    loadSalesChart();
    loadDeathsChart();
});
</script>

<!-- Monthly Animal Discards Column Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #721c24, #c1272d); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-trash-alt me-2"></i>
                Descartes Mensuales de Animales
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportDiscardsPDF" class="btn" style="background-color: #721c24; border-color: #721c24; color: white;">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="discardsChart"></canvas>
            </div>
            <div id="discardsChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Animal Discards Column Chart Implementation
let discardsChart = null;

async function loadDiscardsChart() {
    const messageDiv = document.getElementById('discardsChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_discards_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de descartes de animales disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const discardsCounts = data.map(item => parseInt(item.discards_count));
        const discardedAnimals = data.map(item => item.discarded_animals);

        // Chart configuration
        const ctx = document.getElementById('discardsChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (discardsChart) {
            discardsChart.destroy();
        }

        discardsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Descartes de Animales',
                    data: discardsCounts,
                    backgroundColor: 'rgba(114, 28, 36, 0.8)',
                    borderColor: '#721c24',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    discardedAnimals: discardedAnimals // Custom property to store animal data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Número de Descartes de Animales por Mes',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#721c24',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label}`;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                const animalsData = context.dataset.discardedAnimals[context.dataIndex];
                                
                                let tooltip = [`Descartes: ${count}`];
                                
                                if (animalsData && animalsData.trim() !== '') {
                                    tooltip.push(''); // Empty line for spacing
                                    tooltip.push('Animales descartados:');
                                    
                                    // Split the animal data and format them nicely
                                    const animalData = animalsData.split(', ');
                                    if (animalData.length <= 5) {
                                        // If 5 or fewer animals, show all
                                        animalData.forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                    } else {
                                        // If more than 5, show first 4 and count
                                        animalData.slice(0, 4).forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                        tooltip.push(`• ... y ${animalData.length - 4} más`);
                                    }
                                }
                                
                                return tooltip;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Descartes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading discards chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Discards Chart
document.getElementById('exportDiscardsPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('discardsChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(114, 28, 36);
        pdf.text('Reporte de Descartes Mensuales de Animales', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `descartes_animales_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
    loadPregnancyChart();
    loadBirthsChart();
    loadPurchasesChart();
    loadSalesChart();
    loadDeathsChart();
    loadDiscardsChart();
});
</script>

<!-- Monthly Animal Weaning Column Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #0dcaf0, #198754); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-cut me-2"></i>
                Destetes Mensuales de Animales
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportWeaningPDF" class="btn btn-info">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="weaningChart"></canvas>
            </div>
            <div id="weaningChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Animal Weaning Column Chart Implementation
let weaningChart = null;

async function loadWeaningChart() {
    const messageDiv = document.getElementById('weaningChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_weaning_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de destetes de animales disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const weaningCounts = data.map(item => parseInt(item.weaning_count));
        const weanedAnimals = data.map(item => item.weaned_animals);

        // Chart configuration
        const ctx = document.getElementById('weaningChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (weaningChart) {
            weaningChart.destroy();
        }

        weaningChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Destetes de Animales',
                    data: weaningCounts,
                    backgroundColor: 'rgba(13, 202, 240, 0.8)',
                    borderColor: '#0dcaf0',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    weanedAnimals: weanedAnimals // Custom property to store animal data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Número de Destetes de Animales por Mes',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#0dcaf0',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label}`;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                const animalsData = context.dataset.weanedAnimals[context.dataIndex];
                                
                                let tooltip = [`Destetes: ${count}`];
                                
                                if (animalsData && animalsData.trim() !== '') {
                                    tooltip.push(''); // Empty line for spacing
                                    tooltip.push('Animales destetados:');
                                    
                                    // Split the animal data and format them nicely
                                    const animalData = animalsData.split(', ');
                                    if (animalData.length <= 5) {
                                        // If 5 or fewer animals, show all
                                        animalData.forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                    } else {
                                        // If more than 5, show first 4 and count
                                        animalData.slice(0, 4).forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                        tooltip.push(`• ... y ${animalData.length - 4} más`);
                                    }
                                }
                                
                                return tooltip;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Destetes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading weaning chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Weaning Chart
document.getElementById('exportWeaningPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('weaningChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(13, 202, 240);
        pdf.text('Reporte de Destetes Mensuales de Animales', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `destetes_animales_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
    loadPregnancyChart();
    loadBirthsChart();
    loadPurchasesChart();
    loadSalesChart();
    loadDeathsChart();
    loadDiscardsChart();
    loadWeaningChart();
});
</script>

<!-- Monthly Insemination Column Chart Section -->
<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background: linear-gradient(to right, #32cd32, #228b22); color: white;">
            <h4 class="mb-0">
                <i class="fas fa-dna me-2"></i>
                Inseminaciones Mensuales
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button id="exportInseminationPDF" class="btn" style="background-color: #32cd32; border-color: #32cd32; color: white;">
                        <i class="fas fa-file-pdf me-2"></i>
                        Exportar a PDF
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="inseminationChart"></canvas>
            </div>
            <div id="inseminationChartMessage" class="text-center mt-3" style="display: none;">
                <p class="text-muted">Cargando datos del gráfico...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Insemination Column Chart Implementation
let inseminationChart = null;

async function loadInseminationChart() {
    const messageDiv = document.getElementById('inseminationChartMessage');
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = '<p class="text-muted">Cargando datos del gráfico...</p>';

    try {
        const response = await fetch('./get_insemination_data.php');
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (data.length === 0) {
            messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay datos de inseminaciones disponibles</div>';
            return;
        }

        messageDiv.style.display = 'none';

        // Prepare chart data
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            const monthNames = [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ];
            return `${monthNames[parseInt(month) - 1]} ${year}`;
        });

        const inseminationCounts = data.map(item => parseInt(item.insemination_count));
        const inseminatedAnimals = data.map(item => item.inseminated_animals);

        // Chart configuration
        const ctx = document.getElementById('inseminationChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (inseminationChart) {
            inseminationChart.destroy();
        }

        inseminationChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inseminaciones',
                    data: inseminationCounts,
                    backgroundColor: 'rgba(50, 205, 50, 0.8)',
                    borderColor: '#32cd32',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    inseminatedAnimals: inseminatedAnimals // Custom property to store animal data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Número de Inseminaciones por Mes',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#32cd32',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label}`;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                const animalsData = context.dataset.inseminatedAnimals[context.dataIndex];
                                
                                let tooltip = [`Inseminaciones: ${count}`];
                                
                                if (animalsData && animalsData.trim() !== '') {
                                    tooltip.push(''); // Empty line for spacing
                                    tooltip.push('Animales inseminados:');
                                    
                                    // Split the animal data and format them nicely
                                    const animalData = animalsData.split(', ');
                                    if (animalData.length <= 5) {
                                        // If 5 or fewer animals, show all
                                        animalData.forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                    } else {
                                        // If more than 5, show first 4 and count
                                        animalData.slice(0, 4).forEach(data => {
                                            tooltip.push(`• ${data.trim()}`);
                                        });
                                        tooltip.push(`• ... y ${animalData.length - 4} más`);
                                    }
                                }
                                
                                return tooltip;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Inseminaciones',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#e9ecef',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value) === value ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

    } catch (error) {
        console.error('Error loading insemination chart:', error);
        messageDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos: ${error.message}</div>`;
    }
}

// Export to PDF functionality for Insemination Chart
document.getElementById('exportInseminationPDF').addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';

    try {
        // Get the chart canvas
        const canvas = document.getElementById('inseminationChart');
        
        if (!canvas) {
            throw new Error('No se pudo encontrar el gráfico');
        }

        // Convert chart to image using html2canvas for better quality
        const chartContainer = canvas.parentElement;
        const chartImage = await html2canvas(chartContainer, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher resolution
            logging: false,
            useCORS: true
        });

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        // Add title
        pdf.setFontSize(18);
        pdf.setTextColor(50, 205, 50);
        pdf.text('Reporte de Inseminaciones Mensuales', 20, 25);
        
        // Add date
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        pdf.text(`Generado el: ${dateStr}`, 20, 35);

        // Add chart image
        const imgData = chartImage.toDataURL('image/png');
        const imgWidth = 250; // A4 landscape width minus margins
        const imgHeight = (chartImage.height * imgWidth) / chartImage.width;
        
        // Center the image
        const x = (297 - imgWidth) / 2; // A4 landscape width is 297mm
        const y = 45;
        
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Add footer
        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFontSize(10);
        pdf.setTextColor(128, 128, 128);
        pdf.text('Sistema de Gestión Ganadera - Ganagram', 20, pageHeight - 15);
        pdf.text(`Página 1 de 1`, 250, pageHeight - 15);

        // Save the PDF
        const filename = `inseminaciones_${now.getFullYear()}_${(now.getMonth()+1).toString().padStart(2,'0')}_${now.getDate().toString().padStart(2,'0')}.pdf`;
        pdf.save(filename);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'El reporte PDF se ha generado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar el PDF: ' + error.message
        });
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

// Load all charts when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadMilkRevenueChart();
    loadPesoRevenueChart();
    loadConcentradoExpenseChart();
    loadMelazaExpenseChart();
    loadSalExpenseChart();
    loadVaccineCostsChart();
    loadPregnancyChart();
    loadBirthsChart();
    loadPurchasesChart();
    loadSalesChart();
    loadDeathsChart();
    loadDiscardsChart();
    loadWeaningChart();
    loadInseminationChart();
});
</script>

</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all buttons in the scroll-icons-container
    const scrollButtons = document.querySelectorAll('.scroll-icons-container .btn');
    
    scrollButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Prevent default collapse behavior
            e.preventDefault();
            
            // Get the target section ID from data-bs-target
            const targetId = this.getAttribute('data-bs-target').replace('#', '');
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                // Add a small delay to allow for the collapse animation
                setTimeout(() => {
                    // Scroll to the section with smooth behavior
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                        inline: 'nearest'
                    });
                    
                    // Show the collapsed section
                    const bsCollapse = new bootstrap.Collapse(targetSection, {
                        show: true
                    });
                }, 100);
            }
        });
    });
});
</script>