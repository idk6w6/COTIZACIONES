<?php
require_once __DIR__ . '/../../config/db.php';

class Usuario {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function validarLogin($correo, $contrasena) {
        try {
            // Caso especial para admin
            if ($correo === 'admin@example.com' && $contrasena === 'admin') {
                return [
                    'id' => 1, // O el ID que corresponda a tu admin
                    'nombre_usuario' => 'Administrador',
                    'correo' => 'admin@example.com',
                    'rol' => 'Administrador'
                ];
            }

            $sql = "SELECT u.*, r.tipo as rol 
                    FROM usuarios u 
                    LEFT JOIN usuarios_roles ur ON u.id = ur.usuario_id 
                    LEFT JOIN roles r ON ur.rol_id = r.id 
                    WHERE u.correo = :correo";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['correo' => $correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
                unset($usuario['contrasena']);
                return $usuario;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en validarLogin: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerClienteAsociado($usuario_id) {
        try {
            $sql = "SELECT * FROM clientes WHERE usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['usuario_id' => $usuario_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener cliente asociado: " . $e->getMessage());
            return false;
        }
    }
}
