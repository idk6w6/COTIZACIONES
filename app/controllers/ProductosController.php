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
            error_log("Controlador: Productos recuperados: " . count($productos));
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
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Producto creado exitosamente'
                    ]);
                    exit;
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear el producto: ' . $e->getMessage()
                ]);
                exit;
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
            try {
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
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Producto actualizado exitosamente'
                    ]);
                    exit;
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar el producto: ' . $e->getMessage()
                ]);
                exit;
            }
        }
    }

    public function destroy() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || 
           ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete')) {
            try {
                $id = $_SERVER['REQUEST_METHOD'] === 'DELETE' ? 
                      filter_var($_GET['id'], FILTER_VALIDATE_INT) : 
                      filter_var($_POST['id'], FILTER_VALIDATE_INT);

                if (!$id) {
                    throw new Exception('ID de producto inválido');
                }

                if ($this->model->delete($id)) {
                    echo json_encode(['success' => true]);
                    exit;
                }

                throw new Exception('No se pudo eliminar el producto');
            } catch (Exception $e) {
                error_log("Error al eliminar producto: " . $e->getMessage());
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
            $query = "SELECT * FROM productos WHERE id = :id";
            $stmt = $this->model->conn->prepare($query);
            $stmt->execute(['id' => $id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                if (isset($_GET['action']) && $_GET['action'] === 'get') {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Producto no encontrado']);
                    exit;
                }
                return null;
            }

            if (isset($_GET['action']) && $_GET['action'] === 'get') {
                header('Content-Type: application/json');
                echo json_encode($producto);
                exit;
            }
            
            return $producto;
        } catch (Exception $e) {
            error_log("Error en get producto: " . $e->getMessage());
            if (isset($_GET['action']) && $_GET['action'] === 'get') {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
            return null;
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


<?php
    function store($data) {
    try {
        if (empty($data['nombre_producto']) || empty($data['precio'])) {
            throw new Exception('Faltan campos requeridos');
        }


        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Producto guardado exitosamente',
            'data' => $data['id']
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
        exit;
    }
}

    function update($data) {
    try {
        if (empty($data['id']) || empty($data['nombre_producto'])) {
            throw new Exception('Faltan campos requeridos');
        }


        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'data' => $data['id']
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
        exit;
    }
}



