<?php
require_once(__DIR__ . '/../../controllers/ProductosController.php');
require_once(__DIR__ . '/../../../layout/user_header.php');

$controller = new ProductosController();
$productos = $controller->index();
$unidades_medida = $controller->getUnidadesMedida();
?>

<div class="container productos_crear mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>Catálogo de Productos</h2>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>IVA</th>
                                    <th>Unidad de Medida</th>
                                    <th>Peso</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><?php echo $producto['nombre_producto']; ?></td>
                                        <td><?php echo substr($producto['descripcion'], 0, 50) . (strlen($producto['descripcion']) > 50 ? '...' : ''); ?></td>
                                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                        <td><?php echo $producto['iva']; ?>%</td>
                                        <td>
                                            <?php foreach ($unidades_medida as $unidad): 
                                                if ($unidad['id'] == $producto['unidad_medida_id']) {
                                                    echo $unidad['descripcion'];
                                                    break;
                                                }
                                            endforeach; ?>
                                        </td>
                                        <td><?php echo $producto['unidad_peso']; ?></td>
                                        <td><?php echo $producto['stock']; ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="cotizarProducto(<?php echo $producto['id']; ?>)">
                                                <i class="fas fa-calculator"></i> Cotizar Producto
                                            </button>
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
</div>
<script src="/Cotizaciones/public/js/Product.js"></script>

