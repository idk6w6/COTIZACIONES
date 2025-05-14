<?php
require_once(__DIR__ . '/../../controllers/ProductosController.php');
require_once(__DIR__ . '/../../../layout/admin_header.php');

$controller = new ProductosController();
$unidades_medida = $controller->getUnidadesMedida();
$metodos_costeo = $controller->getMetodosCosteo();

// Verificar si estamos en modo edición
$isEditing = isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'edit';
$producto = null;

if ($isEditing) {
    $producto = $controller->get($_GET['id']);
    if (!$producto) {
        echo "<script>alert('Producto no encontrado'); window.location.href='productos_editar.php';</script>";
        exit;
    }
}
?>

<div class="container productos_editar mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="form-container">
                <h2>Gestión de Productos</h2>
                <form id="productoForm" method="POST" action="/Cotizaciones/app/controllers/ProductosController.php">
                    <input type="hidden" name="action" value="<?php echo $isEditing ? 'update' : 'store'; ?>">
                    <?php if ($isEditing): ?>
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nombre_producto">Nombre del Producto*</label>
                                <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" 
                                       value="<?php echo $isEditing ? $producto['nombre_producto'] : ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción*</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo $isEditing ? $producto['descripcion'] : ''; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio">Precio*</label>
                                <input type="number" step="0.01" min="0" max="99999.99" 
                                       class="form-control" id="precio" name="precio" 
                                       value="<?php echo $isEditing ? $producto['precio'] : ''; ?>" required>
                                <small class="form-text text-muted">Máximo: $99,999.99</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="iva">IVA*</label>
                                <select class="form-control" id="iva" name="iva" required>
                                    <option value="0" <?php echo $isEditing && $producto['iva'] == 0 ? 'selected' : ''; ?>>0% - Exento</option>
                                    <option value="16" <?php echo $isEditing && $producto['iva'] == 16 ? 'selected' : ''; ?>>16% - General</option>
                                    <option value="8" <?php echo $isEditing && $producto['iva'] == 8 ? 'selected' : ''; ?>>8% - Fronterizo</option>
                                </select>
                                <small class="form-text text-muted">Tasa de IVA según la región y tipo de producto</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="descuento">Descuento (%)</label>
                                <input type="number" class="form-control" id="descuento" name="descuento" 
                                       value="<?php echo $isEditing ? $producto['descuento'] : '0'; ?>" min="0" max="100" step="0.01">
                                <small class="form-text text-muted">Porcentaje de descuento aplicable al producto (0-100%)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unidad_medida_id">Unidad de Medida*</label>
                                <select class="form-control" id="unidad_medida_id" name="unidad_medida_id" required>
                                    <option value="">Seleccione una unidad</option>
                                    <?php foreach ($unidades_medida as $unidad): ?>
                                        <option value="<?php echo $unidad['id']; ?>" <?php echo $isEditing && $producto['unidad_medida_id'] == $unidad['id'] ? 'selected' : ''; ?>><?php echo $unidad['descripcion']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unidad_peso">Peso*</label>
                                <input type="number" step="0.01" min="0" max="999.99" 
                                       class="form-control" id="unidad_peso" name="unidad_peso" 
                                       value="<?php echo $isEditing ? $producto['unidad_peso'] : ''; ?>" required>
                                <small class="form-text text-muted">Máximo: 999.99</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">Unidades Disponibles*</label>
                                <input type="number" min="0" max="9999" 
                                       class="form-control" id="stock" name="stock" 
                                       value="<?php echo $isEditing ? $producto['stock'] : ''; ?>" required>
                                <small class="form-text text-muted">Máximo: 9,999 unidades</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="metodo_costeo_id">Método de Costeo*</label>
                        <select class="form-control" id="metodo_costeo_id" name="metodo_costeo_id" required>
                            <option value="">Seleccione un método</option>
                            <?php foreach ($metodos_costeo as $metodo): ?>
                                <option value="<?php echo $metodo['id']; ?>" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="right"
                                        title="<?php echo obtenerDescripcionMetodoCosteo($metodo['descripcion']); ?>"
                                        <?php echo $isEditing && $producto['metodo_costeo_id'] == $metodo['id'] ? 'selected' : ''; ?>>
                                    <?php echo $metodo['descripcion']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            Pase el mouse sobre cada opción para ver más detalles
                        </small>
                    </div>

                    <?php
                    function obtenerDescripcionMetodoCosteo($metodo) {
                        $descripciones = [
                            'PEPS - Primeras Entradas, Primeras Salidas' => 
                                'Los primeros productos que entran al inventario son los primeros en salir.',
                            'Promedio Ponderado' => 
                                'Calcula un promedio del costo de todos los productos similares en inventario.',
                            'Costo Identificado' => 
                                'Para productos que pueden ser identificados individualmente por su costo.',
                            'Costo Estándar' => 
                                'Usa un costo predeterminado que se ajusta periódicamente.'
                        ];
                        return $descripciones[$metodo] ?? 'Método de costeo';
                    }
                    ?>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        //tooltips de Bootstrap
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl, {
                                html: true,
                                container: 'body'
                            });
                        });

                        //Actualizar tooltips cuando cambie el select
                        document.getElementById('metodo_costeo_id').addEventListener('change', function() {
                            tooltipList.forEach(tooltip => tooltip.update());
                        });
                    });
                    </script>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $isEditing ? 'Actualizar' : 'Guardar'; ?> Producto
                    </button>
                    <button type="button" 
                            class="btn btn-primary"
                            onclick="window.location.href='/Cotizaciones/app/views/productos/productos_editar.php'">
                            <i class="fas fa-list"></i> Listado de Productos
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
