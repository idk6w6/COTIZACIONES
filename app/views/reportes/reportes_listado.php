<?php
include_once('../../../layout/admin_header.php');
require_once(__DIR__ . '/../../controllers/UsuarioController.php');
require_once(__DIR__ . '/../../controllers/CotizacionesController.php');
require_once(__DIR__ . '/../../controllers/ProductosController.php');

$usuarioController = new UsuarioController();
$cotizacionesController = new CotizacionesController();
$productosController = new ProductosController();

// Obtener datos para las gráficas
$usuarios = $usuarioController->obtenerTodosUsuarios();
$cotizaciones = $cotizacionesController->obtenerTodasLasCotizaciones();
?>

<div class="container mt-4">
    <h2 class="mb-4">Reportes de Cotizaciones</h2>
    
    <div class="row justify-content-center">

        <!-- Gráfica de Usuarios y Clientes -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header" style="background-color: #673ab7; color: white;">
                    <h5 class="card-title mb-0">Administradores, Usuarios y Clientes Registrados</h5>
                </div>
                <div class="card-body">
                    <canvas id="usuariosChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Gráficas de la segunda fila -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header" style="background-color: #673ab7; color: white;">
                    <h5 class="card-title mb-0">Productos más Cotizados</h5>
                </div>
                <div class="card-body">
                    <canvas id="productosChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header" style="background-color: #673ab7; color: white;">
                    <h5 class="card-title mb-0">Estados de Cotizaciones</h5>
                </div>
                <div class="card-body">
                    <canvas id="estadosChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Datos para la gráfica de usuarios y clientes
    const usuariosData = {
        labels: ['Total Usuarios', 'Administradores', 'Clientes'],
        datasets: [{
            label: 'Cantidad de Usuarios',
            data: [
                <?php 
                $totalUsuarios = count($usuarios);
                $admins = array_filter($usuarios, function($u) { return strtolower($u['rol']) === 'administrador'; });
                $clientes = array_filter($usuarios, function($u) { return strtolower($u['rol']) === 'cliente'; });
                echo $totalUsuarios . ', ' . count($admins) . ', ' . count($clientes);
                ?>
            ],
            backgroundColor: ['rgba(153, 102, 255, 0.8)', 'rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)'],
            borderColor: ['rgb(153, 102, 255)', 'rgb(54, 162, 235)', 'rgb(75, 192, 192)'],
            borderWidth: 1
        }]
    };

    // Datos para la gráfica de productos
    const productosData = {
        labels: [],
        datasets: [{
            data: [],
            backgroundColor: [],
            borderColor: [],
            borderWidth: 1
        }]
    };

    <?php
    $productosCotizados = [];
    foreach ($cotizaciones as $cotizacion) {
        $nombreProducto = $cotizacion['nombre_producto'];
        if (!isset($productosCotizados[$nombreProducto])) {
            $productosCotizados[$nombreProducto] = 0;
        }
        $productosCotizados[$nombreProducto]++;
    }
    arsort($productosCotizados);
    $productosCotizados = array_slice($productosCotizados, 0, 5);
    
    foreach ($productosCotizados as $producto => $cantidad) {
        echo "productosData.labels.push('" . addslashes($producto) . "');\n";
        echo "productosData.datasets[0].data.push(" . $cantidad . ");\n";
    }
    ?>

    productosData.datasets[0].backgroundColor = [
        'rgba(103, 58, 183, 0.8)',
        'rgba(156, 39, 176, 0.8)',
        'rgba(33, 150, 243, 0.8)',
        'rgba(0, 188, 212, 0.8)',
        'rgba(233, 30, 99, 0.8)'
    ];
    productosData.datasets[0].borderColor = [
        'rgb(103, 58, 183)',
        'rgb(156, 39, 176)',
        'rgb(33, 150, 243)',
        'rgb(0, 188, 212)',
        'rgb(233, 30, 99)'
    ];

    //gráfica de estados
    const estadosData = {
        labels: ['Pendiente', 'Realizada', 'Cancelada'],
        datasets: [{
            data: [
                <?php
                $estados = array_count_values(array_column($cotizaciones, 'estado'));
                echo ($estados['pendiente'] ?? 0) . ', ';
                echo ($estados['realizada'] ?? 0) . ', ';
                echo ($estados['cancelada'] ?? 0);
                ?>
            ],
            backgroundColor: [
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ],
            borderColor: [
                'rgb(255, 206, 86)',
                'rgb(75, 192, 192)',
                'rgb(255, 99, 132)'
            ],
            borderWidth: 1
        }]
    };

    new Chart(document.getElementById('usuariosChart'), {
        type: 'bar',
        data: usuariosData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById('productosChart'), {
        type: 'pie',
        data: productosData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    new Chart(document.getElementById('estadosChart'), {
        type: 'doughnut',
        data: estadosData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>


