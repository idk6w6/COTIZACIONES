<?php
require_once __DIR__ . '/../../controllers/CotizacionesController.php';
require_once __DIR__ . '/../../../layout/user_header.php';

$controller = new CotizacionesController();
$cotizacion_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$cotizacion_id) {
    header('Location: cotizaciones_crear.php');
    exit;
}

$cotizacion = $controller->obtenerCotizacionPorId($cotizacion_id);
$producto = $controller->obtenerProducto($cotizacion['producto_id']);

if (!$cotizacion) {
    header('Location: cotizaciones_crear.php?error=cotizacion_no_encontrada');
    exit;
}

$fecha_cotizacion = strtotime($cotizacion['fecha_cotizacion']);
$fecha_limite = strtotime('+1 day', $fecha_cotizacion);
$puede_editar = time() < $fecha_limite;

if (!$puede_editar) {
    header('Location: cotizaciones_crear.php?error=tiempo_excedido');
    exit;
}
?>

<div class="container mt-4 cotizacion-crear">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Nueva Cotización</h3>
                </div>
                <div class="card-body">
                    <form id="editarCotizacionForm" method="POST" action="/Cotizaciones/app/controllers/CotizacionesController.php">
                        <input type="hidden" name="action" value="actualizar">
                        <input type="hidden" name="cotizacion_id" value="<?php echo htmlspecialchars($cotizacion_id); ?>">
                        <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($cotizacion['producto_id'] ?? ''); ?>">
                        <input type="hidden" name="subtotal" id="subtotal_hidden">
                        <input type="hidden" name="montoIva" id="montoIva_hidden">
                        <input type="hidden" name="total" id="total_hidden">
                        
                        <div class="mb-4">
                            <h4 class="border-bottom pb-2" style="color: #673ab7;">Información del Cliente</h4>
                            <div class="mb-3">
                                <label for="cliente" class="form-label fw-bold">Cliente*</label>
                                <input type="text" class="form-control bg-light" id="cliente" value="<?php echo htmlspecialchars($cotizacion['nombre_cliente'] ?? ''); ?>" readonly>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="border-bottom pb-2" style="color: #673ab7;">Detalles del Producto</h4>
                            <div class="mb-3">
                                <label class="form-label">Producto*</label>
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($cotizacion['nombre_producto'] ?? ''); ?>" readonly>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Precio Unitario*</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control bg-light" id="precio" name="precio" 
                                               value="<?php echo htmlspecialchars($cotizacion['precio'] ?? '0'); ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cantidad*</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                           value="<?php echo htmlspecialchars($cotizacion['cantidad'] ?? '1'); ?>" 
                                           min="1" max="<?php echo $cotizacion['stock'] ?? 1; ?>" required
                                           onchange="calcularTotales()">
                                    <small class="text-muted">Stock disponible: <?php echo $cotizacion['stock'] ?? 0; ?></small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">IVA (%)*</label>
                                    <input type="number" class="form-control bg-light" id="iva" name="iva" 
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
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Total*</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control form-control-lg bg-light" id="total" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg shadow">Generar Cotización</button>
                            <a href="cotizaciones_crear.php" class="btn btn-secondary shadow">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="/Cotizaciones/public/js/cotizaciones.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const producto = <?php echo json_encode($producto); ?>;
    
    function calcularTotales() {
        const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
        const precio = parseFloat(producto.precio) || 0;
        const iva = parseFloat(producto.iva) || 0;
        const descuento = parseFloat(producto.descuento) || 0;

        const subtotal = cantidad * precio;
        const montoDescuento = (subtotal * descuento) / 100;
        const baseIva = subtotal - montoDescuento;
        const montoIva = (baseIva * iva) / 100;
        const total = baseIva + montoIva;

        document.getElementById('subtotal').value = formatCurrency(subtotal);
        document.getElementById('montoDescuento').value = formatCurrency(montoDescuento);
        document.getElementById('montoIva').value = formatCurrency(montoIva);
        document.getElementById('total').value = formatCurrency(total);

        document.getElementById('subtotal_hidden').value = subtotal.toFixed(2);
        document.getElementById('montoIva_hidden').value = montoIva.toFixed(2);
        document.getElementById('total_hidden').value = total.toFixed(2);
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('es-MX', { 
            style: 'currency', 
            currency: 'MXN'
        }).format(value);
    }

    document.getElementById('cantidad').addEventListener('input', calcularTotales);
    calcularTotales();
});
</script>

