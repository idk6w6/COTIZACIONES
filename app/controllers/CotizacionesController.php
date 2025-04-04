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
            $cliente_id = $this->getClienteIdByUsuario($_SESSION['usuario_id']);
            if (!$cliente_id) {
                error_log("No cliente_id found for usuario_id: " . $_SESSION['usuario_id']);
                return [];
            }

            $sql = "SELECT 
                        c.id, 
                        u.nombre_usuario,
                        cl.nombre as nombre_cliente,
                        c.fecha_cotizacion, 
                        p.nombre_producto,
                        dc.cantidad,
                        dc.precio,
                        c.subtotal,
                        c.iva,
                        c.descuento,
                        c.total,
                        LOWER(COALESCE(c.estado, 'pendiente')) as estado
                    FROM cotizaciones c 
                    INNER JOIN detalles_cotizacion dc ON c.id = dc.cotizacion_id 
                    INNER JOIN productos p ON dc.producto_id = p.id 
                    INNER JOIN usuarios u ON c.usuario_id = u.id
                    INNER JOIN clientes cl ON c.cliente_id = cl.id
                    WHERE c.cliente_id = :cliente_id 
                    ORDER BY c.fecha_cotizacion DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['cliente_id' => $cliente_id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as &$row) {
                $row['estado'] = strtolower($row['estado']);
            }
            
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

    public function obtenerCotizacionPorId($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    c.*,
                    cl.nombre as nombre_cliente,
                    p.id as producto_id,
                    p.nombre_producto,
                    p.precio,
                    p.iva,
                    p.stock,
                    dc.cantidad,
                    TO_CHAR(c.fecha_cotizacion, 'YYYY-MM-DD HH24:MI:SS') as fecha_cotizacion
                FROM cotizaciones c
                INNER JOIN clientes cl ON c.cliente_id = cl.id
                INNER JOIN detalles_cotizacion dc ON c.id = dc.cotizacion_id
                INNER JOIN productos p ON dc.producto_id = p.id
                WHERE c.id = :id 
                AND cl.usuario_id = :usuario_id"
            );
            
            $stmt->execute([
                'id' => $id,
                'usuario_id' => $_SESSION['usuario_id']
            ]);
            
            $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$cotizacion) {
                return false;
            }
            
            $producto = $this->obtenerProducto($cotizacion['producto_id']);
            $cotizacion['producto'] = $producto;
            
            return $cotizacion;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerCotizacionPorId: " . $e->getMessage());
            return false;
        }
    }

    public function crear($datos) {
        try {
            //stock disponible
            $stmt = $this->conn->prepare("SELECT stock FROM productos WHERE id = :producto_id");
            $stmt->execute(['producto_id' => $datos['producto_id']]);
            $stock = $stmt->fetchColumn();
            
            if ($stock < $datos['cantidad']) {
                throw new Exception('La cantidad solicitada excede el stock disponible');
            }

            if (!isset($_SESSION['usuario_id'])) {
                throw new Exception('Usuario no identificado');
            }

            $cliente_id = $this->getClienteIdByUsuario($_SESSION['usuario_id']);
            if (!$cliente_id) {
                throw new Exception('No se encontró el registro de cliente asociado. Por favor, complete su registro como cliente primero.');
            }

            $this->conn->beginTransaction();

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

    public function actualizarCotizacion($datos) {
        try {
            //Obtener el producto y sus detalles
            $producto = $this->obtenerProducto($datos['producto_id']);
            $precio = $producto['precio'];
            $cantidad = $datos['cantidad'];
            
            //Calcular todos los montos
            $subtotal = $cantidad * $precio;
            $montoDescuento = ($subtotal * $producto['descuento']) / 100;
            $baseIva = $subtotal - $montoDescuento;
            $montoIva = ($baseIva * $producto['iva']) / 100;
            $total = $baseIva + $montoIva;

            $this->conn->beginTransaction();

            //Actualizar cotización principal
            $stmt = $this->conn->prepare("
                UPDATE cotizaciones 
                SET subtotal = :subtotal,
                    iva = :iva,
                    descuento = :descuento,
                    total = :total,
                    fecha_cotizacion = CURRENT_TIMESTAMP
                WHERE id = :cotizacion_id
            ");
            
            $stmt->execute([
                ':subtotal' => $subtotal,
                ':iva' => $montoIva,
                ':descuento' => $montoDescuento,
                ':total' => $total,
                ':cotizacion_id' => $datos['cotizacion_id']
            ]);

            //Actualizar detalles de la cotización
            $stmt = $this->conn->prepare("
                UPDATE detalles_cotizacion 
                SET cantidad = :cantidad,
                    precio = :precio,
                    iva = :iva,
                    descuento = :descuento,
                    neto = :neto,
                    total = :total
                WHERE cotizacion_id = :cotizacion_id
            ");

            $stmt->execute([
                ':cantidad' => $cantidad,
                ':precio' => $precio,
                ':iva' => $montoIva,
                ':descuento' => $montoDescuento,
                ':neto' => $baseIva,
                ':total' => $total,
                ':cotizacion_id' => $datos['cotizacion_id']
            ]);

            $this->conn->commit();
            return ['success' => true];

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function cancelarCotizacion($id) {
        try {
            if (!isset($_SESSION['usuario_id'])) {
                throw new Exception('Sesión no iniciada');
            }

            //Actualizar el estado a 'cancelada'
            $stmt = $this->conn->prepare("
                UPDATE cotizaciones 
                SET estado = 'cancelada' 
                WHERE id = :id
            ");
            
            $stmt->execute(['id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Cotización cancelada exitosamente'];
            } else {
                throw new Exception('No se pudo encontrar la cotización');
            }
            
        } catch (Exception $e) {
            error_log("Error en cancelarCotizacion: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function obtenerTodasLasCotizaciones() {
        $sql = "SELECT 
                    c.id, 
                    TO_CHAR(c.fecha_cotizacion, 'YYYY-MM-DD HH24:MI:SS') as fecha_cotizacion, 
                    c.estado, c.subtotal, c.iva, c.descuento, c.total,
                    u.nombre_usuario,
                    cl.nombre as nombre_cliente, cl.direccion,
                    p.nombre_producto,
                    dc.cantidad, dc.precio
                FROM cotizaciones c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                INNER JOIN clientes cl ON c.cliente_id = cl.id
                INNER JOIN detalles_cotizacion dc ON c.id = dc.cotizacion_id
                INNER JOIN productos p ON dc.producto_id = p.id
                ORDER BY c.fecha_cotizacion DESC";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener cotizaciones: " . $e->getMessage());
            return [];
        }
    }

    public function cambiarEstado($id, $estado) {
        try {
            $estadosPermitidos = ['pendiente', 'realizada', 'cancelada'];
            if (!in_array($estado, $estadosPermitidos)) {
                throw new Exception('Estado no válido');
            }

            $stmt = $this->conn->prepare("
                UPDATE cotizaciones 
                SET estado = :estado 
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':id' => $id,
                ':estado' => $estado
            ]);
            
            return ['success' => true, 'message' => 'Estado actualizado correctamente'];
        } catch (Exception $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

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

    if ($_POST['action'] === 'actualizar') {
        $datos = [
            'cotizacion_id' => $_POST['cotizacion_id'],
            'producto_id' => $_POST['producto_id'],
            'cantidad' => intval($_POST['cantidad']),
            'subtotal' => floatval(str_replace(['$', ','], '', $_POST['subtotal_hidden'])),
            'montoIva' => floatval(str_replace(['$', ','], '', $_POST['montoIva_hidden'])),
            'total' => floatval(str_replace(['$', ','], '', $_POST['total_hidden']))
        ];

        $result = $controller->actualizarCotizacion($datos);
        
        if ($result['success']) {
            header('Location: /Cotizaciones/app/views/cotizaciones/cotizaciones_crear.php?success=update');
        } else {
            header('Location: /Cotizaciones/app/views/cotizaciones/cotizaciones_crear.php?error=' . urlencode($result['message']));
        }
        exit;
    }

    if ($_POST['action'] === 'cambiar_estado') {
        header('Content-Type: application/json');
        echo json_encode($controller->cambiarEstado($_POST['id'], $_POST['estado']));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $controller = new CotizacionesController();
    
    if (isset($_POST['action'])) {
        header('Content-Type: application/json');
        
        if ($_POST['action'] === 'cancelar') {
            echo json_encode($controller->cancelarCotizacion($_POST['id']));
            exit;
        }
    }
}
?>

