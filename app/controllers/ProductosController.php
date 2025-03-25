<?php
require_once(__DIR__ . '/../models/Productos.php');

class ProductosController {
    private $model;

    public function __construct() {
        $this->model = new Productos();
    }

    public function index() {
        return $this->model->getAll();
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'store') {
            $data = [
                'clave' => $_POST['clave'],
                'nombre_producto' => $_POST['nombre_producto'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'iva' => $_POST['iva'],
                'unidad_medida_id' => $_POST['unidad_medida_id'],
                'unidad_peso' => $_POST['unidad_peso'],
                'metodo_costeo_id' => $_POST['metodo_costeo_id']
            ];

            if ($this->model->create($data)) {
                header('Location: /Cotizaciones/app/views/productos/productos_editar.php?success=1');
                exit;
            }
        }
        header('Location: /Cotizaciones/app/views/productos/productos_editar.php?error=1');
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
            $data = [
                'id' => $_POST['id'],
                'clave' => $_POST['clave'],
                'nombre_producto' => $_POST['nombre_producto'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'iva' => $_POST['iva'],
                'unidad_medida_id' => $_POST['unidad_medida_id'],
                'unidad_peso' => $_POST['unidad_peso'],
                'metodo_costeo_id' => $_POST['metodo_costeo_id']
            ];

            if ($this->model->update($data)) {
                header('Location: /Cotizaciones/app/views/productos/productos_editar.php?success=2');
                exit;
            }
        }
        header('Location: /Cotizaciones/app/views/productos/productos_editar.php?error=2');
        exit;
    }

    public function destroy() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
            if ($this->model->delete($_POST['id'])) {
                header('Location: /Cotizaciones/app/views/productos/productos_editar.php?success=3');
                exit;
            }
        }
        header('Location: /Cotizaciones/app/views/productos/productos_editar.php?error=3');
        exit;
    }

    public function getUnidadesMedida() {
        return $this->model->getUnidadesMedida();
    }

    public function getMetodosCosteo() {
        return $this->model->getMetodosCosteo();
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ProductosController();
    switch ($_POST['action']) {
        case 'store':
            $controller->store();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->destroy();
            break;
    }
}
?>

