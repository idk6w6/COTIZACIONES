<?php
require_once(__DIR__ . '/../../config/db.php');

class Productos {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAll() {
        try {
            $query = "SELECT id, nombre_producto, descripcion, precio, iva, 
                             unidad_medida_id, unidad_peso, metodo_costeo_id 
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
        // Validar valores no negativos
        if ($data['precio'] < 0 || $data['unidad_peso'] < 0) {
            throw new Exception('El precio y peso no pueden ser menores a 0');
        }

        // Validar tasa de IVA mexicana
        $tasasValidas = [0, 8, 16];
        if (!in_array((float)$data['iva'], $tasasValidas)) {
            throw new Exception('Tasa de IVA no válida para México');
        }

        $query = "INSERT INTO productos (nombre_producto, descripcion, precio, iva, 
                                       unidad_medida_id, unidad_peso, metodo_costeo_id) 
                 VALUES (:nombre_producto, :descripcion, :precio, :iva, 
                        :unidad_medida_id, :unidad_peso, :metodo_costeo_id)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function update($data) {
        // Validar valores no negativos
        if ($data['precio'] < 0 || $data['unidad_peso'] < 0) {
            throw new Exception('El precio y peso no pueden ser menores a 0');
        }

        // Validar tasa de IVA mexicana
        $tasasValidas = [0, 8, 16];
        if (!in_array((float)$data['iva'], $tasasValidas)) {
            throw new Exception('Tasa de IVA no válida para México');
        }

        $query = "UPDATE productos SET 
                 clave = :clave,
                 nombre_producto = :nombre_producto,
                 descripcion = :descripcion,
                 precio = :precio,
                 iva = :iva,
                 unidad_medida_id = :unidad_medida_id,
                 unidad_peso = :unidad_peso,
                 metodo_costeo_id = :metodo_costeo_id
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $query = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
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
}
?>

