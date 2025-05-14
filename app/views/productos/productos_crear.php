<?php
require_once(__DIR__ . '/../../controllers/ProductosController.php');
require_once(__DIR__ . '/../../../layout/user_header.php');

$controller = new ProductosController();
$productos = $controller->index();
$unidades_medida = $controller->getUnidadesMedida();
$metodos_costeo = $controller->getMetodosCosteo(); 
?>

    <link rel="stylesheet" href="/Cotizaciones/public/css/styles.css">


<div class="container productos_crear mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>Catálogo de Productos</h2>
            <div class="card">
                <div class="card-body">
                    <?php 
                    if (empty($productos)): ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p class="mb-0">No hay productos registrados</p>
                        </div>
                    <?php else: ?>
                        <div class="row mb-4">
                            <div class="col-md-8 mx-auto">
                                <form method="GET" action="" class="search-form">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control form-control-lg shadow-sm" 
                                               name="search" 
                                               placeholder="Buscar productos por nombre o descripción..." 
                                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        <button class="btn btn-primary btn-lg" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <?php if(isset($_GET['search'])): ?>
                                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" 
                                               class="btn btn-secondary btn-lg">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                        <th>Cotizar Ahora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($productos)): ?>
                                        <?php foreach ($productos as $producto): ?>
                                            <tr>
                                                <td><?php echo $producto['nombre_producto']; ?></td>
                                                <td>
                                                    <span data-bs-toggle="tooltip" 
                                                          data-bs-placement="top" 
                                                          title="<?php echo htmlspecialchars($producto['descripcion']); ?>">
                                                        <?php echo substr($producto['descripcion'], 0, 50) . (strlen($producto['descripcion']) > 50 ? '...' : ''); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo number_format($producto['precio'], 2); ?></td>
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
                                                    <div class="btn-group" role="group">
                                                        <button type="button" 
                                                                class="btn btn-primary" 
                                                                onclick="window.location.href='/Cotizaciones/app/views/cotizaciones/cotizaciones_crear.php?producto_id=<?php echo htmlspecialchars($producto['id']); ?>'"
                                                                title="Cotizar Producto">
                                                            <i class="fas fa-calculator"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    No se encontraron productos
                                                    <?php echo isset($_GET['search']) ? ' para la búsqueda "' . htmlspecialchars($_GET['search']) . '"' : ''; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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
<script>
//tooltips
document.addEventListener('DOMContentLoaded', function() {
    //tooltips en la página
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            html: true,
            container: 'body'
        });
    });
});
</script>

