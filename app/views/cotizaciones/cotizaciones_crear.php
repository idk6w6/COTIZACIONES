    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Cotizaciones/layout/user_header.php');
    require_once(__DIR__ . '/../../controllers/CotizacionesController.php');

    $controller = new CotizacionesController();
    $producto = null;
    $producto_id = isset($_GET['producto_id']) ? $_GET['producto_id'] : null;

    if ($producto_id) {
        $producto = $controller->obtenerProducto($producto_id);
    }
    ?>

    <div class="container mt-4 cotizacion-crear">
        <?php if (!$producto_id): ?>
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Cotizaciones</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Sus cotizaciones se mostrarán aquí. Por parte del administrador,
                            podrá ver el estado de cada cotización.
                        </div>
                        
                        <?php 
                        $cotizaciones = $controller->index();
                        if (!empty($cotizaciones)): 
                        ?>
                        <div class="table-responsive">
                            <table id="cotizacionesTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Cliente</th>
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
                                    <?php foreach ($cotizaciones as $cotizacion): 
                                        $fecha_cotizacion = strtotime($cotizacion['fecha_cotizacion']);
                                        $fecha_limite = strtotime('+1 day', $fecha_cotizacion);
                                        $puede_editar = time() < $fecha_limite;
                                    ?>
                                        <tr>
                                            <td><?php echo $cotizacion['id']; ?></td>
                                            <td><?php echo htmlspecialchars($cotizacion['nombre_usuario']); ?></td>
                                            <td><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></td>
                                            <td><?php echo htmlspecialchars($cotizacion['nombre_producto']); ?></td>
                                            <td><?php echo $cotizacion['cantidad']; ?></td>
                                            <td>$<?php echo number_format($cotizacion['precio'], 2); ?></td>
                                            <td>$<?php echo number_format($cotizacion['subtotal'], 2); ?></td>
                                            <td>$<?php echo number_format($cotizacion['iva'], 2); ?></td>
                                            <td>$<?php echo number_format($cotizacion['descuento'], 2); ?></td>
                                            <td>$<?php echo number_format($cotizacion['total'], 2); ?></td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    echo $cotizacion['estado'] == 'pendiente' ? 'warning text-dark' : 
                                                        ($cotizacion['estado'] == 'realizada' ? 'success text-dark' : 'danger text-dark');
                                                ?>">
                                                    <?php echo ucfirst(htmlspecialchars($cotizacion['estado'] ?? 'pendiente')); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            No hay cotizaciones registradas.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Nueva Cotización</h3>
                    </div>
                    <div class="card-body">
                        <form id="cotizacionForm" method="POST">
                            <input type="hidden" name="action" value="crear">
                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto_id); ?>">
                            <input type="hidden" name="subtotal" id="subtotal_hidden">
                            <input type="hidden" name="montoIva" id="montoIva_hidden">
                            <input type="hidden" name="total_hidden" id="total_hidden">
                            
                            <div class="mb-4">
                                <h4 class="border-bottom pb-2">Información del Cliente</h4>
                                <div class="mb-3">
                                    <label for="cliente" class="form-label">Cliente*</label>
                                    <input type="text" class="form-control" id="cliente" name="cliente" 
                                        value="<?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h4 class="border-bottom pb-2">Detalles del Producto</h4>
                                <div class="mb-3">
                                    <label for="producto" class="form-label">Producto*</label>
                                    <input type="text" class="form-control" id="producto" name="producto" 
                                        value="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" readonly>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="precio" class="form-label">Precio Unitario*</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="precio" name="precio" 
                                                value="<?php echo htmlspecialchars($producto['precio']); ?>" 
                                                step="0.01" min="0" required readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="cantidad" class="form-label">Cantidad*</label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                            value="0" min="1" max="<?php echo $producto['stock']; ?>" 
                                            required onchange="actualizarCantidadMax(<?php echo $producto_id; ?>)">
                                        <small class="text-muted">Stock disponible: <?php echo $producto['stock']; ?></small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="iva" class="form-label">IVA (%)*</label>
                                        <input type="number" class="form-control" id="iva" name="iva" 
                                            value="<?php echo htmlspecialchars($producto['iva']); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h4 class="border-bottom pb-2" style="color: #673ab7;">Totales</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Subtotal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control bg-light" id="subtotal" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Descuento (<?php echo htmlspecialchars($producto['descuento'] ?? '0'); ?>%)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control bg-light" id="montoDescuento" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">IVA</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control bg-light" id="montoIva" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="total" class="form-label">Total*</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control form-control-lg" id="total" name="total" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Generar Cotización</button>
                                <button type="button" 
                                    class="btn btn-primary"
                                    onclick="window.location.href='/Cotizaciones/app/views/productos/productos_editar.php'">
                                    <i class="fas fa-list"></i> Volver a Listado de Productos
                                </button>                            
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('cotizacionForm');
        if (form) {
            inicializarCalculos();

            document.getElementById('cantidad').addEventListener('input', function() {
                actualizarCantidadMax(<?php echo $producto_id; ?>);
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                form.action = '/Cotizaciones/app/controllers/CotizacionesController.php';
                form.submit();
            });
        }
    });

    function cancelarCotizacion(id) {
        if (confirm('¿Está seguro de que desea cancelar esta cotización?')) {
            fetch('/Cotizaciones/app/controllers/CotizacionesController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'cancelar',
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cotización cancelada exitosamente');
                    location.reload();
                } else {
                    alert(data.message || 'Error al cancelar la cotización');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }
    }
    </script>

    <script src="/Cotizaciones/public/js/Cotizacion.js"></script>
    <script src="/Cotizaciones/public/js/cotizaciones.js"></script>

