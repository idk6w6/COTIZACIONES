<?php
require_once(__DIR__ . '/../../controllers/ProductosController.php');
require_once(__DIR__ . '/../../../layout/admin_header.php');

$controller = new ProductosController();
$unidades_medida = $controller->getUnidadesMedida();
$metodos_costeo = $controller->getMetodosCosteo();
$productos = $controller->index();
?>

<link rel="stylesheet" href="sweetalert2.min.css">



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
                            <button class="productos_crear" onclick="location.href='productos_crear_formulario.php'">
                                <i >Crear Producto</i>
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
                                    <th>Cotizar Ahora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><?php echo $producto['nombre_producto']; ?></td>
                                        <td>
                                            <span span data-bs-toggle="tooltip" 
                                                  data-bs-placement="top" 
                                                  data-bs-title="<?php echo htmlspecialchars($producto['descripcion']); ?>">
                                                  <?php echo substr($producto['descripcion'], 0, 30) . '...'; ?>
                                            </span>
                                        </td>
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
                                            <a href="productos_crear_formulario.php?id=<?php echo $producto['id']; ?>&action=edit" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger" id="btn_eliminar" onlclick="btn_eliminar()" onclick= "eliminarProducto(<?php echo $producto['id']; ?>)"  >
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
<script src="/Cotizaciones/resources/btn.js"></script>
<script src="/Cotizaciones/resources/sweetalert2@11.js"></script>
<script src="/Cotizaciones/public/js/Product.js"></script>