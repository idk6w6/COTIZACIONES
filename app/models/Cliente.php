<?php
require_once __DIR__ . '/../../config/db.php';

class Cliente {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function crear($datos) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO clientes (nombre, celular1, tel_oficina, correo, direccion, usuario_id) 
                VALUES (:nombre, :celular1, :tel_oficina, :correo, :direccion, :usuario_id)
                RETURNING id"
            );
            $stmt->execute($datos);
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            throw new Exception('Error al crear el cliente: ' . $e->getMessage());
        }
    }

    public function obtenerTodos() {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, u.nombre_usuario 
                FROM clientes c
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                ORDER BY c.nombre
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Cliente->obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarClientes($termino) {
        $sql = "SELECT * FROM clientes 
                WHERE LOWER(nombre) LIKE LOWER(:termino) 
                   OR LOWER(celular1) LIKE LOWER(:termino) 
                   OR LOWER(correo) LIKE LOWER(:termino) 
                   OR LOWER(tel_oficina) LIKE LOWER(:termino) 
                ORDER BY nombre";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['termino' => '%' . $termino . '%']);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Búsqueda realizada. Término: " . $termino . ". Resultados encontrados: " . count($resultados));
            return $resultados;
        } catch(PDOException $e) {
            error_log("Error en búsqueda de clientes: " . $e->getMessage());
            return [];
        }
    }

    public function actualizar($datos) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id FROM clientes WHERE usuario_id = :usuario_id
            ");
            $stmt->execute([':usuario_id' => $datos['usuario_id']]);
            $existe = $stmt->fetch();

            if (!$existe) {
                $stmt = $this->conn->prepare("
                    INSERT INTO clientes (nombre, celular1, tel_oficina, correo, direccion, usuario_id)
                    VALUES (:nombre, :celular1, :tel_oficina, :correo, :direccion, :usuario_id)
                ");
            } else {
                $stmt = $this->conn->prepare("
                    UPDATE clientes 
                    SET nombre = :nombre,
                        celular1 = :celular1,
                        tel_oficina = :tel_oficina,
                        correo = :correo,
                        direccion = :direccion
                    WHERE usuario_id = :usuario_id
                ");
            }
            
            $stmt2 = $this->conn->prepare("
                UPDATE usuarios 
                SET nombre_usuario = :nombre
                WHERE id = :usuario_id
            ");
            
            $this->conn->beginTransaction();
            
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':celular1' => $datos['celular1'],
                ':tel_oficina' => $datos['tel_oficina'],
                ':correo' => $datos['correo'],
                ':direccion' => $datos['direccion'],
                ':usuario_id' => $datos['usuario_id']
            ]);
            
            $stmt2->execute([
                ':nombre' => $datos['nombre'],
                ':usuario_id' => $datos['usuario_id']
            ]);
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error en actualizar: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $this->conn->beginTransaction();
            
            $stmt = $this->conn->prepare("
                SELECT id FROM cotizaciones WHERE cliente_id = ?
            ");
            $stmt->execute([$id]);
            $cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($cotizaciones)) {
                foreach ($cotizaciones as $cotizacion) {
                    $stmt = $this->conn->prepare("
                        DELETE FROM detalles_cotizacion 
                        WHERE cotizacion_id = ?
                    ");
                    $stmt->execute([$cotizacion['id']]);
                }

                $stmt = $this->conn->prepare("
                    DELETE FROM cotizaciones 
                    WHERE cliente_id = ?
                ");
                $stmt->execute([$id]);
            }
            
            $stmt = $this->conn->prepare("
                DELETE FROM clientes 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error al eliminar cliente: " . $e->getMessage());
            throw new Exception("Error al eliminar el cliente: " . $e->getMessage());
        }
    }

    public function existeCorreo($correo, $id = null) {
        $sql = "SELECT id FROM clientes WHERE LOWER(correo) = LOWER(:correo)";
        if ($id) {
            $sql .= " AND id != :id";
        }
        
        try {
            $stmt = $this->conn->prepare($sql);
            $params = ['correo' => $correo];
            if ($id) {
                $params['id'] = $id;
            }
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function obtenerClientePorUsuarioId($usuario_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    c.id,
                    COALESCE(c.nombre, u.nombre_usuario) as nombre_cliente,
                    u.nombre_usuario,
                    c.direccion,
                    TO_CHAR(u.fecha_creacion, 'DD/MM/YYYY') as fecha_registro
                FROM usuarios u
                LEFT JOIN clientes c ON u.id = c.usuario_id
                WHERE u.id = :usuario_id
            ");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultado) {
                return [
                    'nombre_usuario' => $_SESSION['nombre_usuario'],
                    'nombre_cliente' => $_SESSION['nombre_usuario'],
                    'direccion' => 'No especificada',
                    'fecha_registro' => date('d/m/Y')
                ];
            }
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en obtenerClientePorUsuarioId: " . $e->getMessage());
            return null;
        }
    }
}
