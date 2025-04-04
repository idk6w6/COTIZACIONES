<?php
require_once(__DIR__ . '/../models/Productos.php');

class ProductosController {
    private $model;

    public function __construct() {
        $this->model = new Productos();
    }

    public function index() {
        try {
            if (isset($_GET['search'])) {
                $searchTerm = trim($_GET['search']);
                if (!empty($searchTerm)) {
                    return $this->model->search($searchTerm);
                }
            }
            $productos = $this->model->getAll();
            error_log("Controlador: Productos recuperados: " . count($productos)); // Debug
            return $productos;
        } catch (Exception $e) {
            error_log("Error en ProductosController->index: " . $e->getMessage());
            return [];
        }
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'store') {
            try {
                $data = [
                    'nombre_producto' => $_POST['nombre_producto'],
                    'descripcion' => $_POST['descripcion'],
                    'precio' => $_POST['precio'],
                    'iva' => $_POST['iva'],
                    'unidad_medida_id' => $_POST['unidad_medida_id'],
                    'unidad_peso' => $_POST['unidad_peso'],
                    'metodo_costeo_id' => $_POST['metodo_costeo_id'],
                    'stock' => $_POST['stock'],
                    'descuento' => isset($_POST['descuento']) ? $_POST['descuento'] : 0
                ];

                if ($this->model->create($data)) {
                    header('Location: /Cotizaciones/app/views/productos/productos_editar.php?success=1');
                    exit;
                }
            } catch (Exception $e) {
                error_log("Error al crear producto: " . $e->getMessage());
            }
        }
        header('Location: /Cotizaciones/app/views/productos/productos_editar.php?error=1');
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
            $data = [
                'id' => $_POST['id'],
                'nombre_producto' => $_POST['nombre_producto'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'iva' => $_POST['iva'],
                'unidad_medida_id' => $_POST['unidad_medida_id'],
                'unidad_peso' => $_POST['unidad_peso'],
                'metodo_costeo_id' => $_POST['metodo_costeo_id'],
                'stock' => $_POST['stock'],
                'descuento' => isset($_POST['descuento']) ? $_POST['descuento'] : 0
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
            try {
                $id = $_POST['id'];
                if ($this->model->delete($id)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }
                throw new Exception('No se pudo eliminar el producto');
            } catch (Exception $e) {
                error_log("Error al eliminar producto: " . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        }
    }

    public function getUnidadesMedida() {
        return $this->model->getUnidadesMedida();
    }

    public function getMetodosCosteo() {
        return $this->model->getMetodosCosteo();
    }

    public function get($id) {
        try {
            $query = "SELECT p.*, 
                     mc.descripcion as metodo_costeo,
                     (SELECT AVG(precio) FROM productos 
                      WHERE metodo_costeo_id = p.metodo_costeo_id) as precio_promedio
                     FROM productos p 
                     JOIN metodos_costeo mc ON p.metodo_costeo_id = mc.id
                     WHERE p.id = :id";
            $stmt = $this->model->conn->prepare($query);
            $stmt->execute(['id' => $id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto) {
                // Ajustar precio según método de costeo
                $producto['precio'] = $this->ajustarPrecioPorMetodoCosteo($producto);
                echo json_encode($producto);
            } else {
                echo json_encode(['error' => 'Producto no encontrado']);
            }
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    private function ajustarPrecioPorMetodoCosteo($producto) {
        // Ya no aplicamos ajustes adicionales al precio base
        switch($producto['metodo_costeo']) {
            case 'PEPS - Primeras Entradas, Primeras Salidas':
            case 'Promedio Ponderado':
            case 'Costo Identificado':
            case 'Costo Estándar':
                return $producto['precio']; // Devolvemos el precio sin modificar
            default:
                return $producto['precio'];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $controller = new ProductosController();
    $controller->get($_GET['id']);
}

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

