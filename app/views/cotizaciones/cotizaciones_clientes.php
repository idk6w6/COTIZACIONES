<?php
session_start();
require_once __DIR__ . '/../../controllers/CotizacionesController.php';
require_once __DIR__ . '/../../controllers/ClientesController.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /Cotizaciones/auth/login.php');
    exit;
}

$cotizacionesController = new CotizacionesController();
$clientesController = new ClientesController();

$cotizaciones = $cotizacionesController->obtenerTodasLasCotizaciones();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotizaciones de Clientes</title>
    <link rel="stylesheet" href="/Cotizaciones/public/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <?php 
    try {
        include __DIR__ . '/../../../layout/admin_header.php';
    } catch (Exception $e) {
        error_log("Error loading header: " . $e->getMessage());
    }
    ?>
    
    <div class="container mt-4">
        <div class="table-cotizaciones-container">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Cotizaciones de Clientes</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="cotizacionesTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Cliente</th>
                                    <th>Dirección</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>IVA</th>
                                    <th>Descuento</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotizaciones as $cotizacion): ?>
                                    <tr>
                                        <td class="text-center"><?php echo htmlspecialchars($cotizacion['id']); ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['nombre_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['direccion']); ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['nombre_producto']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($cotizacion['cantidad']); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['precio'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['subtotal'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['iva'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['descuento'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['total'], 2); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($cotizacion['fecha_cotizacion']); ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?php echo $cotizacion['estado'] == 'pendiente' ? 'warning' : ($cotizacion['estado'] == 'realizada' ? 'success' : 'danger'); ?>">
                                                <?php echo ucfirst(htmlspecialchars($cotizacion['estado'])); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-warning cambiar-estado" 
                                                        data-id="<?php echo $cotizacion['id']; ?>" 
                                                        data-estado="pendiente"
                                                        <?php echo $cotizacion['estado'] == 'pendiente' ? 'disabled' : ''; ?>>
                                                    Pendiente
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success cambiar-estado" 
                                                        data-id="<?php echo $cotizacion['id']; ?>" 
                                                        data-estado="realizada"
                                                        <?php echo $cotizacion['estado'] == 'realizada' ? 'disabled' : ''; ?>>
                                                    Realizada
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger cancelar-cotizacion" 
                                                        data-id="<?php echo $cotizacion['id']; ?>"
                                                        <?php echo $cotizacion['estado'] == 'cancelada' ? 'disabled' : ''; ?>>
                                                    Cancelar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cotizacionesTable').DataTable({
                "language": {
                    "sProcessing":     "Procesando...",
                    "sLengthMenu":     "Mostrar _MENU_ registros",
                    "sZeroRecords":    "No se encontraron resultados",
                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix":    "",
                    "sSearch":         "Buscar:",
                    "sUrl":            "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sLast":     "Último",
                        "sNext":     "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    },
                    "buttons": {
                        "copy": "Copiar",
                        "colvis": "Visibilidad"
                    }
                },
                "order": [[0, "desc"]]
            });

            $('.cambiar-estado').click(function() {
                const id = $(this).data('id');
                const estado = $(this).data('estado');
                
                $.ajax({
                    url: '/Cotizaciones/app/controllers/CotizacionesController.php',
                    type: 'POST',
                    data: {
                        action: 'cambiar_estado',
                        id: id,
                        estado: estado
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error al cambiar el estado: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error al procesar la solicitud');
                    }
                });
            });

            $('.cancelar-cotizacion').click(function() {
                if(confirm('¿Está seguro de que desea cancelar esta cotización?')) {
                    const id = $(this).data('id');
                    
                    $.ajax({
                        url: '/Cotizaciones/app/controllers/CotizacionesController.php',
                        type: 'POST',
                        data: {
                            action: 'cancelar',
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if(response.success) {
                                location.reload();
                            } else {
                                alert('Error al cancelar la cotización: ' + (response.message || 'Error desconocido'));
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error en la solicitud: ' + error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

