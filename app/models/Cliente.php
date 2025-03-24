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
        $sql = "SELECT * FROM clientes ORDER BY nombre";  // Changed to order by name
        try {
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
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
            // Primero verificar si existe el registro de cliente
            $stmt = $this->conn->prepare("
                SELECT id FROM clientes WHERE usuario_id = :usuario_id
            ");
            $stmt->execute([':usuario_id' => $datos['usuario_id']]);
            $existe = $stmt->fetch();

            if (!$existe) {
                // Si no existe, crear nuevo registro
                $stmt = $this->conn->prepare("
                    INSERT INTO clientes (nombre, celular1, tel_oficina, correo, direccion, usuario_id)
                    VALUES (:nombre, :celular1, :tel_oficina, :correo, :direccion, :usuario_id)
                ");
            } else {
                // Si existe, actualizar
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
            
            // También actualizar la tabla usuarios para mantener sincronizado
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
            
            // Primero verificar si existen cotizaciones relacionadas
            $stmt = $this->conn->prepare("
                SELECT id FROM cotizaciones WHERE cliente_id = ?
            ");
            $stmt->execute([$id]);
            $cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si hay cotizaciones, eliminar sus detalles primero
            if (!empty($cotizaciones)) {
                foreach ($cotizaciones as $cotizacion) {
                    $stmt = $this->conn->prepare("
                        DELETE FROM detalles_cotizacion 
                        WHERE cotizacion_id = ?
                    ");
                    $stmt->execute([$cotizacion['id']]);
                }

                // Luego eliminar las cotizaciones
                $stmt = $this->conn->prepare("
                    DELETE FROM cotizaciones 
                    WHERE cliente_id = ?
                ");
                $stmt->execute([$id]);
            }
            
            // Finalmente eliminar el cliente
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
                    c.nombre,
                    c.direccion,
                    c.celular1,
                    c.tel_oficina,
                    c.correo,
                    c.usuario_id,
                    TO_CHAR(u.fecha_creacion, 'DD/MM/YYYY') as fecha_registro
                FROM usuarios u
                INNER JOIN clientes c ON u.id = c.usuario_id
                WHERE u.id = :usuario_id
            ");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cliente) {
                // Log para debug
                error_log("Datos del cliente encontrados: " . print_r($cliente, true));
                return $cliente;
            }
            
            error_log("No se encontraron datos del cliente para usuario_id: " . $usuario_id);
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerClientePorUsuarioId: " . $e->getMessage());
            return null;
        }
    }
}
