<?php
header('Content-Type: application/json');
session_start();

// Habilitar logging de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once __DIR__ . '/UsuarioController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'] ?? '';
        $nombre_usuario = $_POST['nombre_usuario'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $rol = $_POST['rol'] ?? '';

        if (!$id || !$nombre_usuario || !$correo || !$rol) {
            throw new Exception('Datos incompletos');
        }

        $usuarioController = new UsuarioController();
        $resultado = $usuarioController->actualizarUsuario($id, [
            'nombre_usuario' => $nombre_usuario,
            'correo' => $correo,
            'rol' => $rol
        ]);

        if ($resultado === false) {
            throw new Exception('Error en la actualización de la base de datos');
        }

        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        error_log('Error en actualizar_usuario.php: ' . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
