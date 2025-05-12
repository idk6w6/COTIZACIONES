<?php
require_once(__DIR__ . '/../../controllers/ProductosController.php');
require_once(__DIR__ . '/../../../layout/admin_header.php');

$controller = new ProductosController();
$unidades_medida = $controller->getUnidadesMedida();
$metodos_costeo = $controller->getMetodosCosteo();
$productos = $controller->index();
?>

<div class="container productos_editar mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Listado de Productos</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 mx-auto">
                            <form method="GET" action="" class="search-form">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="search" 
                                           placeholder="Buscar producto..." 
                                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if(isset($_GET['search'])): ?>
                                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" 
                                           class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                            <button type="button" 
                                    class="crear-producto"
                                    onclick="window.location.href='/Cotizaciones/app/views/productos/productos_crear_formulario.php'"
                                    <i class="fas fa-plus"></i> Crear un Producto
                            </button>
                        </div>
                    </div>        

                    <?php if (empty($productos)): ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p class="mb-0">No hay productos registrados</p>
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>IVA</th>
                                    <th>Descuento</th>
                                    <th>Unidad de Medida</th>
                                    <th>Peso</th>
                                    <th>Stock</th>
                                    <th>Método de Costeo</th>
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
                                        <td><?php echo isset($producto['descuento']) ? $producto['descuento'] : 0; ?>%</td>
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
                                            <?php foreach ($metodos_costeo as $metodo): 
                                                if ($metodo['id'] == $producto['metodo_costeo_id']) {
                                                    echo $metodo['descripcion'];
                                                    break;
                                                }
                                            endforeach; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="editarProducto(<?php echo $producto['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="eliminarProducto(<?php echo $producto['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/Cotizaciones/public/js/Product.js"></script>
</body>
</html>

