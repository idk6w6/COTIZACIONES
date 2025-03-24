<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once __DIR__ . '/ClientesController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID de cliente no proporcionado']);
        exit();
    }

    try {
        $clientesController = new ClientesController();
        if ($clientesController->eliminar($id)) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Error al eliminar el cliente');
        }
    } catch (Exception $e) {
        error_log('Error en eliminar_cliente.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
