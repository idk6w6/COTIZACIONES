<?php
require_once(__DIR__ . '/../../config/db.php');

class Cotizacion {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function crear($datos) {
        try {
            $this->conn->beginTransaction();

            // Verify cliente_id exists
            $stmt = $this->conn->prepare("SELECT id FROM clientes WHERE id = :cliente_id");
            $stmt->execute([':cliente_id' => $datos['cliente_id']]);
            if (!$stmt->fetch()) {
                throw new Exception('Cliente no encontrado');
            }

            // Insertar la cotización principal
            $stmt = $this->conn->prepare("
                INSERT INTO cotizaciones (
                    cliente_id, 
                    usuario_id, 
                    fecha_cotizacion, 
                    subtotal, 
                    iva, 
                    descuento, 
                    total
                ) VALUES (
                    :cliente_id, 
                    :usuario_id, 
                    CURRENT_TIMESTAMP, 
                    :subtotal, 
                    :iva, 
                    :descuento, 
                    :total
                ) RETURNING id"
            );

            $stmt->execute([
                ':cliente_id' => $datos['cliente_id'],
                ':usuario_id' => $datos['usuario_id'],
                ':subtotal' => $datos['subtotal'],
                ':iva' => $datos['iva'],
                ':descuento' => $datos['descuento'],
                ':total' => $datos['total']
            ]);

            $cotizacion_id = $stmt->fetchColumn();

            // Insertar el detalle de la cotización
            $stmt = $this->conn->prepare("
                INSERT INTO detalles_cotizacion (
                    cotizacion_id, 
                    producto_id, 
                    cantidad, 
                    precio, 
                    iva, 
                    descuento, 
                    total
                ) VALUES (
                    :cotizacion_id,
                    :producto_id,
                    :cantidad,
                    :precio,
                    :iva,
                    :descuento,
                    :total
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
            return $cotizacion_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en Cotizacion->crear: " . $e->getMessage());
            throw $e;
        }
    }

    public function crearDetalle($datos) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO detalles_cotizacion (
                    cotizacion_id,
                    producto_id,
                    cantidad,
                    precio,
                    iva,
                    descuento,
                    total
                ) VALUES (
                    :cotizacion_id,
                    :producto_id,
                    :cantidad,
                    :precio,
                    :iva,
                    :descuento,
                    :total
                )
            ");

            return $stmt->execute($datos);
        } catch (Exception $e) {
            error_log("Error al crear detalle: " . $e->getMessage());
            throw $e;
        }
    }
}
