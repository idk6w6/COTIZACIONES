<?php
require_once __DIR__ . '/../../config/db.php';

class AuthUsers {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function login($correo, $contrasena) {
        $stmt = $this->conn->prepare("SELECT u.*, r.tipo as rol FROM usuarios u 
                                   LEFT JOIN usuarios_roles ur ON u.id = ur.usuario_id 
                                   LEFT JOIN roles r ON ur.rol_id = r.id 
                                   WHERE u.correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            return $usuario;
        }
        return false;
    }

    public function register($nombre_usuario, $correo, $contrasena) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            if ($stmt->fetch()) {
                throw new Exception('El correo ya está registrado');
            }

            $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("
                INSERT INTO usuarios (nombre_usuario, correo, contrasena)
                VALUES (?, ?, ?) RETURNING id
            ");
            $stmt->execute([$nombre_usuario, $correo, $hashed_password]);
            $usuario_id = $stmt->fetchColumn();

            $stmt = $this->conn->prepare("SELECT id FROM roles WHERE LOWER(tipo) = LOWER('Cliente')");
            $stmt->execute();
            $rol = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$rol) {
                throw new Exception('Rol Cliente no encontrado');
            }

            $stmt = $this->conn->prepare("
                INSERT INTO usuarios_roles (usuario_id, rol_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$usuario_id, $rol['id']]);

            $stmt = $this->conn->prepare("
                INSERT INTO clientes (nombre, correo, usuario_id)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$nombre_usuario, $correo, $usuario_id]);

            $this->conn->commit();
            
            session_start();
            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['nombre_usuario'] = $nombre_usuario;
            $_SESSION['correo'] = $correo;
            $_SESSION['rol'] = 'Cliente';
            
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error en registro: " . $e->getMessage());
            return false;
        }
    }
}
?>
