<?php
require_once(__DIR__ . '/../../config/db.php');
require_once(__DIR__ . '/../models/Productos.php');
require_once(__DIR__ . '/../models/Cotizacion.php');

class CotizacionesController {
    private $cotizacionModel;
    private $productosModel;
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
        $this->cotizacionModel = new Cotizacion();
        $this->productosModel = new Productos();
    }

    public function index() {
        try {
            // Get cliente_id first
            $cliente_id = $this->getClienteIdByUsuario($_SESSION['usuario_id']);
            if (!$cliente_id) {
                error_log("No cliente_id found for usuario_id: " . $_SESSION['usuario_id']);
                return [];
            }

            $sql = "SELECT 
                        c.id, 
                        c.fecha_cotizacion, 
                        p.nombre_producto,
                        dc.cantidad,
                        dc.precio,
                        c.subtotal,
                        c.iva,
                        c.descuento,
                        c.total
                    FROM cotizaciones c 
                    INNER JOIN detalles_cotizacion dc ON c.id = dc.cotizacion_id 
                    INNER JOIN productos p ON dc.producto_id = p.id 
                    WHERE c.cliente_id = :cliente_id 
                    ORDER BY c.fecha_cotizacion DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['cliente_id' => $cliente_id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Cotizaciones encontradas: " . count($results));
            return $results;

        } catch (Exception $e) {
            error_log("Error en CotizacionesController->index: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerProducto($id) {
        return $this->productosModel->getProducto($id);
    }

    public function getClienteIdByUsuario($usuario_id) {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM clientes WHERE usuario_id = :usuario_id");
            $stmt->execute(['usuario_id' => $usuario_id]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Error getting cliente_id: " . $e->getMessage());
            return null;
        }
    }

    public function crear($datos) {
        try {
            if (!isset($_SESSION['usuario_id'])) {
                throw new Exception('Usuario no identificado');
            }

            $cliente_id = $this->getClienteIdByUsuario($_SESSION['usuario_id']);
            if (!$cliente_id) {
                throw new Exception('No se encontró el registro de cliente asociado. Por favor, complete su registro como cliente primero.');
            }

            $this->conn->beginTransaction();

            // Insertar cotización sin descuento
            $stmt = $this->conn->prepare("
                INSERT INTO cotizaciones (
                    cliente_id, usuario_id, fecha_cotizacion, 
                    subtotal, iva, total
                ) VALUES (
                    :cliente_id, :usuario_id, CURRENT_TIMESTAMP,
                    :subtotal, :iva, :total
                ) RETURNING id"
            );

            $stmt->execute([
                ':cliente_id' => $cliente_id,
                ':usuario_id' => $_SESSION['usuario_id'],
                ':subtotal' => $datos['subtotal'],
                ':iva' => $datos['iva'],
                ':total' => $datos['total']
            ]);

            $cotizacion_id = $stmt->fetchColumn();

            // Insertar detalle
            $stmt = $this->conn->prepare("
                INSERT INTO detalles_cotizacion (
                    cotizacion_id, producto_id, cantidad, precio,
                    iva, descuento, total
                ) VALUES (
                    :cotizacion_id, :producto_id, :cantidad, :precio,
                    :iva, :descuento, :total
                )"
            );

            $stmt->execute([
                ':cotizacion_id' => $cotizacion_id,
                ':producto_id' => $datos['producto_id'],
                ':cantidad' => $datos['cantidad'],
                ':precio' => $datos['precio'],
                ':iva' => $datos['iva'],
                ':descuento' => $datos['descuento'],
                ':total' => $datos['total']
            ]);

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Cotización creada exitosamente',
                'cotizacion_id' => $cotizacion_id
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en CotizacionesController->crear: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

// Request handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    session_start();
    $controller = new CotizacionesController();

    if ($_POST['action'] === 'crear') {
        $datos = [
            'producto_id' => $_POST['producto_id'],
            'cantidad' => intval($_POST['cantidad']),
            'precio' => floatval($_POST['precio']),
            'subtotal' => floatval(str_replace(['$', ','], '', $_POST['subtotal'])),
            'iva' => floatval(str_replace(['$', ','], '', $_POST['montoIva'])),
            'descuento' => floatval(str_replace(['$', ','], '', $_POST['montoDescuento'])),
            'total' => floatval(str_replace(['$', ','], '', $_POST['total']))
        ];

        // Validate all numeric values
        foreach (['cantidad', 'precio', 'subtotal', 'iva', 'descuento', 'total'] as $field) {
            if ($datos[$field] === 0 && $field !== 'descuento') {
                header('Location: /Cotizaciones/app/views/cotizaciones/cotizaciones_crear.php?error=' . urlencode('Los valores numéricos no pueden estar vacíos'));
                exit;
            }
        }

        $result = $controller->crear($datos);
        
        if ($result['success']) {
            header('Location: /Cotizaciones/app/views/cotizaciones/cotizaciones_crear.php?success=1');
        } else {
            header('Location: /Cotizaciones/app/views/cotizaciones/cotizaciones_crear.php?error=' . urlencode($result['message']));
        }
        exit;
    }
}
?>

