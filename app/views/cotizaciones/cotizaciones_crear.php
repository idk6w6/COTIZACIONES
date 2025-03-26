<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Cotizaciones/layout/user_header.php');
require_once(__DIR__ . '/../../controllers/CotizacionesController.php');

$controller = new CotizacionesController();
$producto = null;
$producto_id = isset($_GET['producto_id']) ? $_GET['producto_id'] : null;

if ($producto_id) {
    $producto = $controller->obtenerProducto($producto_id);
}

// Get success/error messages
$success = isset($_GET['success']) ? true : false;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<div class="container mt-4 cotizacion-crear">
    <?php if ($success): ?>
        <div class="alert alert-success">
            Cotización creada exitosamente.
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!$producto_id): ?>
    <!-- Vista de listado de cotizaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Cotizaciones</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Para crear una nueva cotización, seleccione un producto desde la sección de 
                        <a href="/Cotizaciones/app/views/productos/productos_crear.php" class="alert-link">Productos</a>
                    </div>
                    
                    <?php 
                    $cotizaciones = $controller->index();
                    if (!empty($cotizaciones)): 
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
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
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotizaciones as $cotizacion): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></td>
                                    <td><?php echo htmlspecialchars($cotizacion['nombre_producto']); ?></td>
                                    <td><?php echo $cotizacion['cantidad']; ?></td>
                                    <td>$<?php echo number_format($cotizacion['precio'], 2); ?></td>
                                    <td>$<?php echo number_format($cotizacion['subtotal'], 2); ?></td>
                                    <td>$<?php echo number_format($cotizacion['iva'], 2); ?></td>
                                    <td>$<?php echo number_format($cotizacion['descuento'], 2); ?></td>
                                    <td>$<?php echo number_format($cotizacion['total'], 2); ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
    <!-- Formulario de creación de cotización -->
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
                        
                        <!-- Información del Cliente -->
                        <div class="mb-4">
                            <h4 class="border-bottom pb-2">Información del Cliente</h4>
                            <div class="mb-3">
                                <label for="cliente" class="form-label">Cliente*</label>
                                <input type="text" class="form-control" id="cliente" name="cliente" 
                                       value="<?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>" readonly>
                            </div>
                        </div>

                        <!-- Información del Producto -->
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
                                           value="1" min="1" max="<?php echo $producto['stock']; ?>" required>
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

                        <!-- Totales -->
                        <div class="mb-4">
                            <h4 class="border-bottom pb-2">Totales</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="subtotal" class="form-label">Subtotal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" id="subtotal" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="montoIva" class="form-label">IVA</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" id="montoIva" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
                            <a href="/Cotizaciones/app/views/productos/productos_crear.php" class="btn btn-secondary">Cancelar</a>
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
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Update hidden fields before submit
            document.getElementById('subtotal_hidden').value = document.getElementById('subtotal').value;
            document.getElementById('montoIva_hidden').value = document.getElementById('montoIva').value;
            
            // Set the form action and submit
            form.action = '/Cotizaciones/app/controllers/CotizacionesController.php';
            form.submit();
        });
    }
});
</script>

<script src="/Cotizaciones/public/js/Cotizacion.js"></script>

