<?php
require_once '../config/db.php';

function createAdminUser() {
    $db = new Database();
    $conn = $db->connect();

    try {

        $conn->query("INSERT INTO roles (tipo) VALUES ('admin') ON CONFLICT (tipo) DO NOTHING");
        $stmt = $conn->query("SELECT id FROM roles WHERE tipo = 'admin'");
        $roleId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

        $sql = "INSERT INTO usuarios (nombre_usuario, correo, contrasena) 
                VALUES ('Admin', 'admin@example.com', 'admin') 
                ON CONFLICT (correo) DO UPDATE 
                SET contrasena = 'admin', 
                    nombre_usuario = 'Admin'
                RETURNING id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $userId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

        $sql = "INSERT INTO usuarios_roles (usuario_id, rol_id) 
                VALUES (:usuario_id, :rol_id) 
                ON CONFLICT (usuario_id, rol_id) DO NOTHING";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'usuario_id' => $userId,
            'rol_id' => $roleId
        ]);

        echo "Admin user created successfully!\n";
        echo "Email: admin@example.com\n";
        echo "Password: admin\n";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

createAdminUser();
