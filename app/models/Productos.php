<?php
require_once(__DIR__ . '/../../config/db.php');

class Productos {
    public $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAll() {
        try {
            $query = "SELECT id, nombre_producto, descripcion, precio, iva, 
                             unidad_medida_id, unidad_peso, metodo_costeo_id, 
                             stock, descuento 
                      FROM productos 
                      ORDER BY id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getAll: " . $e->getMessage());
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
            
            $checkCotizacionQuery = "SELECT COUNT(*) FROM detalles_cotizacion WHERE producto_id = :id";
            $checkStmt = $this->conn->prepare($checkCotizacionQuery);
            $checkStmt->execute(['id' => $id]);
            
            if ($checkStmt->fetchColumn() > 0) {
                throw new Exception("No se puede eliminar el producto porque está siendo usado en cotizaciones existentes");
            }

            $getIdsQuery = "SELECT metodo_costeo_id, unidad_medida_id FROM productos WHERE id = :id";
            $getIdsStmt = $this->conn->prepare($getIdsQuery);
            $getIdsStmt->execute(['id' => $id]);
            $ids = $getIdsStmt->fetch(PDO::FETCH_ASSOC);

            $updateQuery = "UPDATE productos SET 
                          metodo_costeo_id = NULL,
                          unidad_medida_id = NULL 
                          WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->execute(['id' => $id]);

            $deleteQuery = "DELETE FROM productos WHERE id = :id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $result = $deleteStmt->execute(['id' => $id]);

            if ($ids['metodo_costeo_id']) {
                $this->conn->exec("UPDATE metodos_costeo SET descripcion = descripcion WHERE id = " . $ids['metodo_costeo_id']);
            }
            if ($ids['unidad_medida_id']) {
                $this->conn->exec("UPDATE unidades_medida SET descripcion = descripcion WHERE id = " . $ids['unidad_medida_id']);
            }

            $this->conn->commit();
            return $result;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar producto: " . $e->getMessage());
            throw new Exception("Error en la base de datos al eliminar el producto: " . $e->getMessage());
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
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

