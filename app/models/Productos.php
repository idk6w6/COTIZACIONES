<?php
require_once(__DIR__ . '/../../config/db.php');

class Productos {
    public $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll() {
        try {
            $stmt = $this->conn->prepare("
                SELECT p.*, mc.descripcion as metodo_costeo_desc, um.descripcion as unidad_medida_desc
                FROM productos p
                LEFT JOIN metodos_costeo mc ON p.metodo_costeo_id = mc.id
                LEFT JOIN unidades_medida um ON p.unidad_medida_id = um.id
                ORDER BY p.id DESC
            ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Productos encontrados: " . count($result));
            return $result;
        } catch (PDOException $e) {
            error_log("Error en Productos->getAll: " . $e->getMessage());
            return [];
        }
    }

    public function create($data) {
        if ($data['precio'] < 0 || $data['precio'] > 99999.99) {
            throw new Exception('El precio debe estar entre 0 y 99,999.99');
        }
        if ($data['unidad_peso'] < 0 || $data['unidad_peso'] > 999.99) {
            throw new Exception('El peso debe estar entre 0 y 999.99');
        }
        if ($data['stock'] < 0 || $data['stock'] > 9999) {
            throw new Exception('El stock debe estar entre 0 y 9,999 unidades');
        }

        $tasasValidas = [0, 8, 16];
        if (!in_array((float)$data['iva'], $tasasValidas)) {
            throw new Exception('Tasa de IVA no válida para México');
        }

        $query = "INSERT INTO productos (
            nombre_producto, descripcion, precio, iva, 
            unidad_medida_id, unidad_peso, metodo_costeo_id, 
            stock, descuento
        ) VALUES (
            :nombre_producto, :descripcion, :precio, :iva, 
            :unidad_medida_id, :unidad_peso, :metodo_costeo_id, 
            :stock, :descuento
        )";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function update($data) {
        if ($data['precio'] < 0 || $data['precio'] > 99999.99) {
            throw new Exception('El precio debe estar entre 0 y 99,999.99');
        }
        if ($data['unidad_peso'] < 0 || $data['unidad_peso'] > 999.99) {
            throw new Exception('El peso debe estar entre 0 y 999.99');
        }
        if ($data['stock'] < 0 || $data['stock'] > 9999) {
            throw new Exception('El stock debe estar entre 0 y 9,999 unidades');
        }

        $tasasValidas = [0, 8, 16];
        if (!in_array((float)$data['iva'], $tasasValidas)) {
            throw new Exception('Tasa de IVA no válida para México');
        }

        $query = "UPDATE productos SET 
            nombre_producto = :nombre_producto,
            descripcion = :descripcion,
            precio = :precio,
            iva = :iva,
            unidad_medida_id = :unidad_medida_id,
            unidad_peso = :unidad_peso,
            metodo_costeo_id = :metodo_costeo_id,
            stock = :stock,
            descuento = :descuento
            WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function delete($id) {
        try {
            $this->conn->beginTransaction();

            // First delete related records in detalles_cotizacion
            $stmt = $this->conn->prepare("DELETE FROM detalles_cotizacion WHERE producto_id = :id");
            $stmt->execute(['id' => $id]);

            // Then delete the product
            $stmt = $this->conn->prepare("DELETE FROM productos WHERE id = :id");
            $stmt->execute(['id' => $id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception("Error al eliminar el producto: " . $e->getMessage());
        }
    }

    public function getUnidadesMedida() {
        $query = "SELECT * FROM unidades_medida";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMetodosCosteo() {
        $query = "SELECT * FROM metodos_costeo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProducto($id) {
        $query = "SELECT * FROM productos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function search($searchTerm) {
        try {
            $query = "SELECT id, nombre_producto, descripcion, precio, iva, 
                             unidad_medida_id, unidad_peso, metodo_costeo_id, 
                             stock, descuento 
                      FROM productos 
                      WHERE LOWER(nombre_producto) LIKE LOWER(:searchTerm) 
                         OR LOWER(descripcion) LIKE LOWER(:searchTerm)
                      ORDER BY nombre_producto ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en search: " . $e->getMessage());
            return [];
        }
    }
}
?>

