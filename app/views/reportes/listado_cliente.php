    <?php
        require_once '../../../layout/user_header.php';
        require_once '../../controllers/CotizacionesController.php';
    ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="/Cotizaciones/public/js/listado-cliente.js"></script>


    <?php
        $controller = new CotizacionesController();
        $cotizaciones = array_filter($controller->index(), function($cotizacion) {
            return $cotizacion['estado'] === 'realizada';
        });
    ?>
    <div class="listado-cliente">
        <div class="container mt-4">
            <div class="cotizaciones-historial">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="mb-0">Historial de Cotizaciones Realizadas</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Aquí puedes ver el historial de todas las cotizaciones que estan realizadas.
                            Puedes filtrar por fecha, producto, estado, etc.
                        </div>
                        <?php if (!empty($cotizaciones)): ?>
                            <div class="table-container">
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
    </div>
