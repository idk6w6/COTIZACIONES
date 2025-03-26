<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClientesController {
    private $clienteModel;
    private $conn;

    public function __construct() {
        $this->clienteModel = new Cliente();
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campos = ['nombre', 'celular1', 'tel_oficina', 'correo', 'direccion', 'usuario_id'];
            $errores = [];
            
            foreach ($campos as $campo) {
                if (empty($_POST[$campo])) {
                    $errores[] = "El campo " . ucfirst($campo) . " es obligatorio";
                }
            }
            
            if (!empty($errores)) {
                return ['errores' => $errores];
            }

            $datos = [
                'nombre' => trim($_POST['nombre']),
                'celular1' => trim($_POST['celular1']),
                'tel_oficina' => trim($_POST['tel_oficina']),
                'correo' => trim($_POST['correo']),
                'direccion' => trim($_POST['direccion']),
                'usuario_id' => $_POST['usuario_id']
            ];

            try {
                if ($this->clienteModel->actualizar($datos)) {
                    $_SESSION['nombre_usuario'] = $datos['nombre'];
                    $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente';
                    header('Location: /Cotizaciones/app/views/usuario/dashboard.php');
                    exit;
                }
                throw new Exception('Error al guardar los datos del cliente');
            } catch (Exception $e) {
                return ['error' => $e->getMessage()];
            }
        }
        return null;
    }

    public function index() {
        if (isset($_GET['search'])) {
            $searchTerm = trim($_GET['search']);
            if (!empty($searchTerm)) {
                $resultados = $this->clienteModel->buscarClientes($searchTerm);
                if (empty($resultados)) {
                    $_SESSION['mensaje'] = "No se encontraron resultados para: " . htmlspecialchars($searchTerm);
                }
                return $resultados;
            }
        }
        return $this->clienteModel->obtenerTodos();
    }

    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nombre']) || empty($_POST['correo'])) {
                return ['error' => 'Nombre y correo son obligatorios'];
            }

            $datos = [
                'id' => $id,
                'nombre' => trim($_POST['nombre']),
                'celular1' => trim($_POST['celular1']),
                'tel_oficina' => trim($_POST['tel_oficina']),
                'correo' => trim($_POST['correo']),
                'direccion' => trim($_POST['direccion'])
            ];

            if ($this->clienteModel->actualizar($datos)) {
                header('Location: /Cotizaciones/app/views/clientes/index.php?success=2');
                exit();
            }
            return ['error' => 'Error al actualizar el cliente'];
        }

        $cliente = $this->clienteModel->obtenerPorId($id);
        if (!$cliente) {
            header('Location: /Cotizaciones/app/views/clientes/index.php?error=1');
            exit();
        }
        return $cliente;
    }

    public function eliminar($id) {
        try {
            return $this->clienteModel->eliminar($id);
        } catch (Exception $e) {
            error_log("Error en ClientesController->eliminar: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorId($id) {
        return $this->clienteModel->obtenerPorId($id);
    }

    public function gestionarCliente() {
        if (isset($_GET['search'])) {
            return $this->index();
        } elseif (isset($_GET['id'])) {
            if (isset($_GET['action']) && $_GET['action'] === 'delete') {
                if ($this->eliminar($_GET['id'])) {
                    $_SESSION['mensaje'] = "Cliente eliminado exitosamente.";
                    header("Location: /Cotizaciones/app/views/clientes/clientes_editar.php");
                    exit;
                } else {
                    $_SESSION['mensaje'] = "Error al eliminar el cliente.";
                }
            } else {
                return $this->obtenerPorId($_GET['id']);
            }
        }
        return null;
    }

    public function manejarVistaEdicion() {
        $resultado = [
            'clientes' => [],
            'cliente' => null,
            'mensaje' => $_SESSION['mensaje'] ?? null
        ];

        if (isset($_SESSION['mensaje'])) {
            unset($_SESSION['mensaje']);
        }

        if (isset($_GET['search'])) {
            $resultado['clientes'] = $this->index();
        } elseif (isset($_GET['id'])) {
            if (isset($_GET['action']) && $_GET['action'] === 'delete') {
                if ($this->eliminar($_GET['id'])) {
                    $_SESSION['mensaje'] = "Cliente eliminado exitosamente.";
                    header("Location: /Cotizaciones/app/views/clientes/clientes_editar.php");
                    exit;
                } else {
                    $_SESSION['mensaje'] = "Error al eliminar el cliente.";
                }
            } else {
                $resultado['cliente'] = $this->obtenerPorId($_GET['id']);
            }
        }

        return $resultado;
    }

    public function manejarFormularioCreacion() {
        $resultado = [
            'resultado' => null,
            'formData' => [
                'nombre' => '',
                'correo' => '',
                'celular1' => '',
                'tel_oficina' => '',
                'direccion' => ''
            ]
        ];

        if (isset($_SESSION['nombre_usuario'])) {
            $resultado['formData']['nombre'] = $_SESSION['nombre_usuario'];
        }
        if (isset($_SESSION['correo'])) {
            $resultado['formData']['correo'] = $_SESSION['correo'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST['nombre'] = $resultado['formData']['nombre'];
            $_POST['correo'] = $resultado['formData']['correo'];
            $_POST['usuario_id'] = $_SESSION['usuario_id'];
            
            $resultado['resultado'] = $this->crear();
            
            if (!isset($resultado['resultado']['success'])) {
                $resultado['formData'] = array_merge($resultado['formData'], $_POST);
            }
        }

        return $resultado;
    }
}
