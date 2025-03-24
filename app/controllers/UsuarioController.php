<?php
require_once(__DIR__ . '/../../config/db.php');
require_once(__DIR__ . '/../models/Usuario.php');

class UsuarioController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function login($correo, $contrasena) {
        global $pdo;  // Use the global $pdo variable

        $query = $pdo->prepare("SELECT u.id, u.nombre_usuario, u.correo, r.tipo AS rol, u.contrasena
                               FROM usuarios u
                               LEFT JOIN usuarios_roles ur ON u.id = ur.usuario_id
                               LEFT JOIN roles r ON ur.rol_id = r.id
                               WHERE u.correo = :correo
                               LIMIT 1");
        $query->bindParam(':correo', $correo);
        $query->execute();

        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['rol'] = $usuario['rol'];

            if ($usuario['rol'] === 'Administrador') {  // Cambiar aquí
                header('Location: /Cotizaciones/app/views/admin/dashboard.php');
                exit;
            } else {
                header('Location: /Cotizaciones/app/views/usuario/dashboard.php');
                exit;
            }
        } else {
            header('Location: /Cotizaciones/app/views/login.php?error=1');  // Redirigir al login si no se verifica
            exit;
        }
    }

    public function register($nombre_usuario, $correo, $contrasena) {
        try {
            $this->conn->beginTransaction();

            // Verificar si el correo existe
            $query = $this->conn->prepare("SELECT id FROM usuarios WHERE correo = :correo");
            $query->execute(['correo' => $correo]);

            if ($query->fetch()) {
                throw new Exception('El correo electrónico ya está registrado.');
            }

            // Crear usuario
            $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
            $query = $this->conn->prepare("
                INSERT INTO usuarios (nombre_usuario, correo, contrasena) 
                VALUES (:nombre_usuario, :correo, :contrasena)
                RETURNING id
            ");
            $query->execute([
                'nombre_usuario' => $nombre_usuario,
                'correo' => $correo,
                'contrasena' => $hashed_password
            ]);
            
            $usuario_id = $query->fetchColumn();

            // Obtener el ID del rol Cliente
            $query = $this->conn->prepare("
                SELECT id FROM roles WHERE tipo = 'Cliente'
            ");
            $query->execute();
            $rol_id = $query->fetchColumn();

            if (!$rol_id) {
                throw new Exception('Rol Cliente no encontrado');
            }

            // Asignar rol de cliente
            $query = $this->conn->prepare("
                INSERT INTO usuarios_roles (usuario_id, rol_id) 
                VALUES (:usuario_id, :rol_id)
            ");
            $query->execute([
                'usuario_id' => $usuario_id,
                'rol_id' => $rol_id
            ]);

            // Crear registro de cliente
            $query = $this->conn->prepare("
                INSERT INTO clientes (nombre, correo, usuario_id) 
                VALUES (:nombre, :correo, :usuario_id)
            ");
            $query->execute([
                'nombre' => $nombre_usuario,
                'correo' => $correo,
                'usuario_id' => $usuario_id
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en registro: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerTodosUsuarios() {
        try {
            $query = $this->conn->query("
                SELECT u.*, r.tipo as rol 
                FROM usuarios u 
                LEFT JOIN usuarios_roles ur ON u.id = ur.usuario_id 
                LEFT JOIN roles r ON ur.rol_id = r.id 
                ORDER BY u.id
            ");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerUsuarioPorId($id) {
        try {
            $query = $this->conn->prepare("
                SELECT u.*, r.tipo as rol 
                FROM usuarios u 
                LEFT JOIN usuarios_roles ur ON u.id = ur.usuario_id 
                LEFT JOIN roles r ON ur.rol_id = r.id 
                WHERE u.id = :id
            ");
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return null;
        }
    }

    public function actualizarUsuario($id, $datos) {
        try {
            // Verificar si el correo ya existe para otro usuario
            $stmt = $this->conn->prepare("
                SELECT id FROM usuarios 
                WHERE correo = :correo AND id != :id
            ");
            $stmt->execute([
                ':correo' => $datos['correo'],
                ':id' => $id
            ]);
            
            if ($stmt->fetch()) {
                throw new Exception('El correo ya está en uso');
            }

            $this->conn->beginTransaction();

            // Actualizar datos del usuario
            $stmt = $this->conn->prepare("
                UPDATE usuarios 
                SET nombre_usuario = :nombre_usuario,
                    correo = :correo
                WHERE id = :id
            ");
            
            $success = $stmt->execute([
                ':nombre_usuario' => $datos['nombre_usuario'],
                ':correo' => $datos['correo'],
                ':id' => $id
            ]);

            if (!$success) {
                throw new Exception("Error actualizando usuario");
            }

            // Obtener el rol_id correcto
            $stmt = $this->conn->prepare("
                SELECT id FROM roles WHERE LOWER(tipo) = LOWER(:rol)
            ");
            $stmt->execute([':rol' => $datos['rol']]);
            $rol = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$rol) {
                throw new Exception("Rol '{$datos['rol']}' no encontrado en la base de datos");
            }

            // Eliminar rol existente y agregar el nuevo
            $stmt = $this->conn->prepare("
                DELETE FROM usuarios_roles WHERE usuario_id = :usuario_id
            ");
            $stmt->execute([':usuario_id' => $id]);

            $stmt = $this->conn->prepare("
                INSERT INTO usuarios_roles (usuario_id, rol_id)
                VALUES (:usuario_id, :rol_id)
            ");
            
            $success = $stmt->execute([
                ':rol_id' => $rol['id'],
                ':usuario_id' => $id
            ]);

            if (!$success) {
                throw new Exception("Error actualizando rol");
            }

            // Si el rol es 'Cliente', manejar registro en tabla clientes
            if (strtolower($datos['rol']) === 'cliente') {
                // Verificar si ya existe el cliente
                $stmt = $this->conn->prepare("
                    SELECT id FROM clientes WHERE usuario_id = :usuario_id
                ");
                $stmt->execute([':usuario_id' => $id]);
                $clienteExiste = $stmt->fetch();

                if (!$clienteExiste) {
                    // Si no existe, crear nuevo cliente
                    $stmt = $this->conn->prepare("
                        INSERT INTO clientes (nombre, correo, usuario_id)
                        VALUES (:nombre, :correo, :usuario_id)
                    ");
                    $stmt->execute([
                        ':nombre' => $datos['nombre_usuario'],
                        ':correo' => $datos['correo'],
                        ':usuario_id' => $id
                    ]);
                } else {
                    // Si existe, actualizar datos del cliente
                    $stmt = $this->conn->prepare("
                        UPDATE clientes 
                        SET nombre = :nombre,
                            correo = :correo
                        WHERE usuario_id = :usuario_id
                    ");
                    $stmt->execute([
                        ':nombre' => $datos['nombre_usuario'],
                        ':correo' => $datos['correo'],
                        ':usuario_id' => $id
                    ]);
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error en UsuarioController->actualizarUsuario: " . $e->getMessage());
            throw $e;
        }
    }

    public function eliminarUsuario($id) {
        try {
            $this->conn->beginTransaction();
            
            // 1. Eliminar registros en detalles_cotizacion relacionados con las cotizaciones del cliente
            $stmt = $this->conn->prepare("
                DELETE FROM detalles_cotizacion 
                WHERE cotizacion_id IN (
                    SELECT c.id 
                    FROM cotizaciones c 
                    INNER JOIN clientes cl ON c.cliente_id = cl.id 
                    WHERE cl.usuario_id = :id
                )
            ");
            $stmt->execute(['id' => $id]);
            
            // 2. Eliminar cotizaciones relacionadas con el cliente
            $stmt = $this->conn->prepare("
                DELETE FROM cotizaciones 
                WHERE cliente_id IN (
                    SELECT id FROM clientes WHERE usuario_id = :id
                )
            ");
            $stmt->execute(['id' => $id]);
            
            // 3. Eliminar el registro del cliente
            $stmt = $this->conn->prepare("DELETE FROM clientes WHERE usuario_id = :id");
            $stmt->execute(['id' => $id]);
            
            // 4. Eliminar roles del usuario
            $stmt = $this->conn->prepare("DELETE FROM usuarios_roles WHERE usuario_id = :id");
            $stmt->execute(['id' => $id]);
            
            // 5. Finalmente eliminar el usuario
            $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function contarAdministradores() {
        try {
            $query = $this->conn->prepare("
                SELECT COUNT(*) FROM usuarios u
                JOIN usuarios_roles ur ON u.id = ur.usuario_id
                JOIN roles r ON ur.rol_id = r.id
                WHERE r.tipo = 'admin'
            ");
            $query->execute();
            return $query->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al contar administradores: " . $e->getMessage());
            return 0;
        }
    }

    public function obtenerDatosDashboardCliente($usuario_id) {
        require_once __DIR__ . '/../models/Cliente.php';
        
        $cliente = new Cliente();
        $datos_cliente = $cliente->obtenerClientePorUsuarioId($usuario_id);

        if (!$datos_cliente) {
            $datos_cliente = [
                'nombre' => $_SESSION['nombre_usuario'],
                'direccion' => 'No especificada',
                'fecha_registro' => date('d/m/Y')
            ];
        }

        return $datos_cliente;
    }
}
?>
