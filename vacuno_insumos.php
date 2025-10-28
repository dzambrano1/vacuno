<?php
require_once './pdo_conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Insumos - La Granja de Tito</title>
    
    <!-- Link to the Favicon -->
    <link rel="icon" href="images/default_image.png" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./vacuno.css">
    
    <style>
        /* Arrow Steps Styling */
        .arrow-step {
            position: relative;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 20px 30px;
            margin: 0 10px;
            clip-path: polygon(0% 0%, calc(100% - 30px) 0%, 100% 50%, calc(100% - 30px) 100%, 0% 100%, 30px 50%);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            min-height: 108px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0.7;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .arrow-step:hover:not(.arrow-step-active) {
            opacity: 0.9;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        .arrow-step-active {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%) !important;
            opacity: 1 !important;
            box-shadow: 0 8px 25px rgba(32, 201, 151, 0.5) !important;
            transform: scale(1.05);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(32, 201, 151, 0.5);
            }
            50% {
                box-shadow: 0 8px 35px rgba(32, 201, 151, 0.8);
            }
        }

        .arrow-step-first {
            clip-path: polygon(0% 0%, calc(100% - 30px) 0%, 100% 50%, calc(100% - 30px) 100%, 0% 100%);
            border-radius: 10px 0 0 10px;
        }

        .arrow-step-last {
            clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%, 30px 50%);
            border-radius: 0 10px 10px 0;
        }

        .badge-active {
            position: absolute;
            top: 10px;
            right: 20px;
            background: #ffc107;
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        /* Content Sections */
        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
        }

        /* Insumo Cards */
        .insumo-card {
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .insumo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .insumo-icon {
            font-size: 3rem;
            color: #28a745;
            margin-bottom: 15px;
        }

        /* WhatsApp Button */
        .whatsapp-btn {
            background: #25D366;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
        }

        .whatsapp-btn:hover {
            background: #128C7E;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(37, 211, 102, 0.6);
        }

        /* Status Badges */
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .status-pendiente {
            background: #ffc107;
            color: #000;
        }

        .status-transito {
            background: #17a2b8;
            color: white;
        }

        .status-entregado {
            background: #28a745;
            color: white;
        }

        @media (max-width: 768px) {
            .arrow-step, .arrow-step-first, .arrow-step-last {
                clip-path: none !important;
                border-radius: 10px !important;
                margin: 10px 0;
            }
            .badge-active {
                right: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Title -->
<nav class="navbar text-center" style="border: none !important; box-shadow: none !important;">
    <div class="container-fluid">
        <div class="row w-100">
            <div class="col-12 d-flex justify-content-between align-items-center position-relative">
                <!-- Botón de Configuración -->
                <button type="button" onclick="window.location.href='./vacuno_configuracion.php'" class="btn" style="color:white; border: none; border-radius: 8px; padding: 8px 15px; z-index: 1050; position: relative;" title="Configuración">
                    <i class="fas fa-cog"></i> 
                </button>
                
                <!-- Título centrado -->
                <h1 class="navbar-title text-center position-absolute" style="left: 50%; transform: translateX(-50%); z-index: 1;">
                    LA GRANJA DE TITO
                </h1>
                
                <!-- Botón de Salir -->
                <button type="button" onclick="window.location.href='../inicio.php'" class="btn" style="color: white; border: none; border-radius: 8px; padding: 8px 15px; z-index: 1050; position: relative;" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i> 
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- 3-Step Navigation -->
<div class="container-fluid mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-11">
            <div class="row justify-content-center align-items-stretch">
                <div class="col-md-4 d-flex px-1 mb-3 mb-md-0">
                    <div class="arrow-step arrow-step-first arrow-step-active w-100" id="step1-btn" onclick="showStep(1)">
                        <span class="badge-active">🎯 Estás aquí</span>
                        <div style="background: white; color: #17a2b8; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 1.8rem; font-weight: bold; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
                            1
                        </div>
                        <h5 class="text-white text-center mb-0" style="font-weight: bold; font-size: 1rem;">PASO 1:<br>Orden de Compra</h5>
                    </div>
                </div>
                <div class="col-md-4 d-flex px-1 mb-3 mb-md-0">
                    <div class="arrow-step w-100" id="step2-btn" onclick="showStep(2)">
                        <div style="background: white; color: #28a745; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 1.8rem; font-weight: bold; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
                            2
                        </div>
                        <h5 class="text-white text-center mb-0" style="font-weight: bold; font-size: 1rem;">PASO 2:<br>Seguimiento Tránsito</h5>
                    </div>
                </div>
                <div class="col-md-4 d-flex px-1 mb-3 mb-md-0">
                    <div class="arrow-step arrow-step-last w-100" id="step3-btn" onclick="showStep(3)">
                        <div style="background: white; color: #28a745; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 1.8rem; font-weight: bold; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
                            3
                        </div>
                        <h5 class="text-white text-center mb-0" style="font-weight: bold; font-size: 1rem;">PASO 3:<br>Entrega</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid mt-4">
    
    <!-- STEP 1: Orden de Compra -->
    <div id="step1-content" class="step-content active">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <h2 class="text-center mb-4" style="color: #28a745; font-weight: bold;">
                    <i class="fas fa-shopping-cart"></i> Nueva Orden de Compra
                </h2>
                
                <!-- Insumos Grid -->
                <div class="row">
                    <!-- Concentrado -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="insumo-card text-center">
                            <div class="insumo-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <h5 style="color: #28a745; font-weight: bold;">Concentrado</h5>
                            <div class="form-group mt-3">
                                <label>Cantidad (kg)</label>
                                <input type="number" id="concentrado-qty" class="form-control" min="0" step="1" value="0">
                            </div>
                            <div class="form-group mt-2">
                                <label>Precio/kg ($)</label>
                                <input type="number" id="concentrado-price" class="form-control" min="0" step="0.01" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Melaza -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="insumo-card text-center">
                            <div class="insumo-icon">
                                <i class="fas fa-flask"></i>
                            </div>
                            <h5 style="color: #28a745; font-weight: bold;">Melaza</h5>
                            <div class="form-group mt-3">
                                <label>Cantidad (L)</label>
                                <input type="number" id="melaza-qty" class="form-control" min="0" step="1" value="0">
                            </div>
                            <div class="form-group mt-2">
                                <label>Precio/L ($)</label>
                                <input type="number" id="melaza-price" class="form-control" min="0" step="0.01" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sal -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="insumo-card text-center">
                            <div class="insumo-icon">
                                <i class="fas fa-cube"></i>
                            </div>
                            <h5 style="color: #28a745; font-weight: bold;">Sal</h5>
                            <div class="form-group mt-3">
                                <label>Cantidad (kg)</label>
                                <input type="number" id="sal-qty" class="form-control" min="0" step="1" value="0">
                            </div>
                            <div class="form-group mt-2">
                                <label>Precio/kg ($)</label>
                                <input type="number" id="sal-price" class="form-control" min="0" step="0.01" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vitaminas -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="insumo-card text-center">
                            <div class="insumo-icon">
                                <i class="fas fa-pills"></i>
                            </div>
                            <h5 style="color: #28a745; font-weight: bold;">Vitaminas</h5>
                            <div class="form-group mt-3">
                                <label>Cantidad (unidades)</label>
                                <input type="number" id="vitaminas-qty" class="form-control" min="0" step="1" value="0">
                            </div>
                            <div class="form-group mt-2">
                                <label>Precio/unidad ($)</label>
                                <input type="number" id="vitaminas-price" class="form-control" min="0" step="0.01" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Información Adicional -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="font-weight: bold;">Proveedor:</label>
                        <select id="proveedor" class="form-control">
                            <option value="">Seleccione un proveedor</option>
                            <option value="Proveedor A">Marval - Concentrado Tito</option>
                            <option value="Proveedor B">Marval - Sal Tito</option>
                            <option value="Proveedor C">Marval - Melaza Tito</option>
                            <option value="Proveedor D">Marval - Vitaminas Tito</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="font-weight: bold;">Fecha de Entrega Deseada:</label>
                        <input type="date" id="fecha-entrega" class="form-control">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label" style="font-weight: bold;">Notas Adicionales:</label>
                        <textarea id="notas" class="form-control" rows="3" placeholder="Agregue cualquier información adicional..."></textarea>
                    </div>
                </div>
                
                <!-- Resumen y Total -->
                <div class="card mb-4" style="background: #f8f9fa; border: 2px solid #28a745;">
                    <div class="card-body">
                        <h4 class="text-center mb-3" style="color: #28a745; font-weight: bold;">Resumen de Orden</h4>
                        <div id="orden-resumen" class="mb-3">
                            <p class="text-center text-muted">Agregue cantidades para ver el resumen</p>
                        </div>
                        <hr>
                        <h3 class="text-center" style="color: #28a745; font-weight: bold;">
                            Total: $<span id="total-orden">0.00</span>
                        </h3>
                    </div>
                </div>
                
                <!-- Botón de Enviar por WhatsApp -->
                <div class="text-center mb-4">
                    <button class="whatsapp-btn" onclick="enviarPorWhatsApp()">
                        <i class="fab fa-whatsapp"></i> Enviar Orden por WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- STEP 2: Seguimiento Tránsito -->
    <div id="step2-content" class="step-content">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <h2 class="text-center mb-4" style="color: #28a745; font-weight: bold;">
                    <i class="fas fa-truck"></i> Seguimiento de Órdenes en Tránsito
                </h2>
                
                <div class="table-responsive">
                    <table id="transitoTable" class="table table-striped table-bordered" style="width:100%">
                        <thead style="background: #28a745; color: white;">
                            <tr>
                                <th>ID Orden</th>
                                <th>Fecha Solicitud</th>
                                <th>Proveedor</th>
                                <th>Insumos</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha Entrega Est.</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ejemplo de datos -->
                            <tr>
                                <td>#ORD-001</td>
                                <td>19/10/2025</td>
                                <td>Proveedor A</td>
                                <td>Concentrado (500kg), Sal (100kg)</td>
                                <td>$1,250.00</td>
                                <td><span class="status-badge status-transito">En Tránsito</span></td>
                                <td>22/10/2025</td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="verDetalleOrden('ORD-001')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- STEP 3: Entrega -->
    <div id="step3-content" class="step-content">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <h2 class="text-center mb-4" style="color: #28a745; font-weight: bold;">
                    <i class="fas fa-check-circle"></i> Registro de Entregas
                </h2>
                
                <div class="table-responsive">
                    <table id="entregasTable" class="table table-striped table-bordered" style="width:100%">
                        <thead style="background: #28a745; color: white;">
                            <tr>
                                <th>ID Orden</th>
                                <th>Fecha Solicitud</th>
                                <th>Fecha Entrega</th>
                                <th>Proveedor</th>
                                <th>Insumos</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Recibido Por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ejemplo de datos -->
                            <tr>
                                <td>#ORD-000</td>
                                <td>15/10/2025</td>
                                <td>18/10/2025</td>
                                <td>Proveedor B</td>
                                <td>Vitaminas (50u), Melaza (200L)</td>
                                <td>$850.00</td>
                                <td><span class="status-badge status-entregado">Entregado</span></td>
                                <td>Juan Pérez</td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="verRecibo('ORD-000')">
                                        <i class="fas fa-receipt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script>
// Step Navigation
function showStep(stepNumber) {
    // Hide all contents
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all steps
    document.querySelectorAll('.arrow-step').forEach(step => {
        step.classList.remove('arrow-step-active');
        // Remove badge
        const badge = step.querySelector('.badge-active');
        if (badge) badge.remove();
    });
    
    // Show selected content
    document.getElementById('step' + stepNumber + '-content').classList.add('active');
    
    // Add active class to selected step
    const activeStep = document.getElementById('step' + stepNumber + '-btn');
    activeStep.classList.add('arrow-step-active');
    
    // Add badge
    const badge = document.createElement('span');
    badge.className = 'badge-active';
    badge.textContent = '🎯 Estás aquí';
    activeStep.insertBefore(badge, activeStep.firstChild);
    
    // Update number color
    const numberDiv = activeStep.querySelector('div[style*="border-radius: 50%"]');
    if (numberDiv) {
        numberDiv.style.color = '#17a2b8';
    }
}

// Calcular Total
function calcularTotal() {
    const concentradoQty = parseFloat(document.getElementById('concentrado-qty').value) || 0;
    const concentradoPrice = parseFloat(document.getElementById('concentrado-price').value) || 0;
    
    const melazaQty = parseFloat(document.getElementById('melaza-qty').value) || 0;
    const melazaPrice = parseFloat(document.getElementById('melaza-price').value) || 0;
    
    const salQty = parseFloat(document.getElementById('sal-qty').value) || 0;
    const salPrice = parseFloat(document.getElementById('sal-price').value) || 0;
    
    const vitaminasQty = parseFloat(document.getElementById('vitaminas-qty').value) || 0;
    const vitaminasPrice = parseFloat(document.getElementById('vitaminas-price').value) || 0;
    
    const totalConcentrado = concentradoQty * concentradoPrice;
    const totalMelaza = melazaQty * melazaPrice;
    const totalSal = salQty * salPrice;
    const totalVitaminas = vitaminasQty * vitaminasPrice;
    
    const total = totalConcentrado + totalMelaza + totalSal + totalVitaminas;
    
    // Update resumen
    let resumenHTML = '';
    if (concentradoQty > 0) {
        resumenHTML += `<p><strong>Concentrado:</strong> ${concentradoQty} kg × $${concentradoPrice.toFixed(2)} = $${totalConcentrado.toFixed(2)}</p>`;
    }
    if (melazaQty > 0) {
        resumenHTML += `<p><strong>Melaza:</strong> ${melazaQty} L × $${melazaPrice.toFixed(2)} = $${totalMelaza.toFixed(2)}</p>`;
    }
    if (salQty > 0) {
        resumenHTML += `<p><strong>Sal:</strong> ${salQty} kg × $${salPrice.toFixed(2)} = $${totalSal.toFixed(2)}</p>`;
    }
    if (vitaminasQty > 0) {
        resumenHTML += `<p><strong>Vitaminas:</strong> ${vitaminasQty} u × $${vitaminasPrice.toFixed(2)} = $${totalVitaminas.toFixed(2)}</p>`;
    }
    
    if (resumenHTML === '') {
        resumenHTML = '<p class="text-center text-muted">Agregue cantidades para ver el resumen</p>';
    }
    
    document.getElementById('orden-resumen').innerHTML = resumenHTML;
    document.getElementById('total-orden').textContent = total.toFixed(2);
}

// Add event listeners to all quantity and price inputs
document.addEventListener('DOMContentLoaded', function() {
    const inputs = [
        'concentrado-qty', 'concentrado-price',
        'melaza-qty', 'melaza-price',
        'sal-qty', 'sal-price',
        'vitaminas-qty', 'vitaminas-price'
    ];
    
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('input', calcularTotal);
    });
    
    // Initialize DataTables
    $('#transitoTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[0, 'desc']]
    });
    
    $('#entregasTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[2, 'desc']]
    });
});

// Enviar por WhatsApp
function enviarPorWhatsApp() {
    const concentradoQty = parseFloat(document.getElementById('concentrado-qty').value) || 0;
    const melazaQty = parseFloat(document.getElementById('melaza-qty').value) || 0;
    const salQty = parseFloat(document.getElementById('sal-qty').value) || 0;
    const vitaminasQty = parseFloat(document.getElementById('vitaminas-qty').value) || 0;
    
    if (concentradoQty === 0 && melazaQty === 0 && salQty === 0 && vitaminasQty === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Orden Vacía',
            text: 'Por favor agregue al menos un insumo a la orden.',
            confirmButtonColor: '#28a745'
        });
        return;
    }
    
    const proveedor = document.getElementById('proveedor').value;
    if (!proveedor) {
        Swal.fire({
            icon: 'warning',
            title: 'Proveedor Requerido',
            text: 'Por favor seleccione un proveedor.',
            confirmButtonColor: '#28a745'
        });
        return;
    }
    
    const fechaEntrega = document.getElementById('fecha-entrega').value;
    const notas = document.getElementById('notas').value;
    const total = document.getElementById('total-orden').textContent;
    
    // Construir mensaje de WhatsApp
    let mensaje = `*ORDEN DE COMPRA - LA GRANJA DE TITO*%0A%0A`;
    mensaje += `*Fecha:* ${new Date().toLocaleDateString('es-ES')}%0A`;
    mensaje += `*Proveedor:* ${proveedor}%0A%0A`;
    mensaje += `*INSUMOS SOLICITADOS:*%0A`;
    
    if (concentradoQty > 0) {
        mensaje += `• Concentrado: ${concentradoQty} kg%0A`;
    }
    if (melazaQty > 0) {
        mensaje += `• Melaza: ${melazaQty} L%0A`;
    }
    if (salQty > 0) {
        mensaje += `• Sal: ${salQty} kg%0A`;
    }
    if (vitaminasQty > 0) {
        mensaje += `• Vitaminas: ${vitaminasQty} unidades%0A`;
    }
    
    mensaje += `%0A*TOTAL: $${total}*%0A`;
    
    if (fechaEntrega) {
        mensaje += `%0A*Fecha de entrega deseada:* ${new Date(fechaEntrega).toLocaleDateString('es-ES')}%0A`;
    }
    
    if (notas) {
        mensaje += `%0A*Notas:* ${encodeURIComponent(notas)}%0A`;
    }
    
    // Número de WhatsApp del proveedor (puedes cambiarlo o hacer que sea dinámico)
    const numeroWhatsApp = '+584143332662'; // Reemplazar con número real
    
    // Abrir WhatsApp
    window.open(`https://wa.me/${numeroWhatsApp}?text=${mensaje}`, '_blank');
    
    // Mostrar confirmación
    Swal.fire({
        icon: 'success',
        title: '¡Orden Enviada!',
        text: 'La orden ha sido enviada por WhatsApp al proveedor.',
        confirmButtonColor: '#28a745'
    });
}

// Ver detalle de orden
function verDetalleOrden(orderId) {
    Swal.fire({
        title: `Detalle de Orden ${orderId}`,
        html: `
            <div class="text-left">
                <p><strong>Estado:</strong> En Tránsito</p>
                <p><strong>Ubicación Actual:</strong> En ruta hacia la granja</p>
                <p><strong>Tiempo Estimado:</strong> 2-3 días</p>
                <p><strong>Transportista:</strong> Transporte XYZ</p>
                <p><strong>Contacto:</strong> +58 424-123-4567</p>
            </div>
        `,
        confirmButtonColor: '#28a745'
    });
}

// Ver recibo
function verRecibo(orderId) {
    Swal.fire({
        title: `Recibo de Entrega ${orderId}`,
        html: `
            <div class="text-left">
                <p><strong>Fecha de Recepción:</strong> 18/10/2025</p>
                <p><strong>Recibido por:</strong> Juan Pérez</p>
                <p><strong>Estado de Mercancía:</strong> Conforme</p>
                <p><strong>Observaciones:</strong> Todo en orden</p>
                <hr>
                <p class="text-center"><em>Documento firmado digitalmente</em></p>
            </div>
        `,
        confirmButtonColor: '#28a745',
        showCancelButton: true,
        confirmButtonText: 'Descargar PDF',
        cancelButtonText: 'Cerrar'
    }).then((result) => {
        if (result.isConfirmed) {
            alert('Función de descarga de PDF en desarrollo');
        }
    });
}
</script>

</body>
</html>

