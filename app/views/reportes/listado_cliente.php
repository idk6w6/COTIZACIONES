<?php
require_once '../../../layout/user_header.php';
require_once '../../controllers/CotizacionesController.php';

$controller = new CotizacionesController();
$cotizaciones = array_filter($controller->index(), function($cotizacion) {
    return $cotizacion['estado'] === 'realizada';
});
?>

<div class="container mt-4">
    <div class="cotizaciones-historial">
        <div class="card shadow">
            <div class="card-header">
                <h2 class="mb-0">Historial de Cotizaciones Realizadas</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($cotizaciones)): ?>
                    <div class="table-responsive">
                        <table id="cotizacionesTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>IVA</th>
                                    <th>Descuento</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotizaciones as $cotizacion): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['nombre_producto']); ?></td>
                                        <td class="text-center"><?php echo $cotizacion['cantidad']; ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['precio'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['subtotal'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['iva'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['descuento'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($cotizacion['total'], 2); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success">Realizada</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay cotizaciones realizadas.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">;
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#cotizacionesTable').DataTable({
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },
            pageLength: 10,
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip><"clear">'
        });
    }
});
</script>
