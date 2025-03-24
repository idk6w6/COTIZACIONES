<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Cliente') {
    header('Location: /Cotizaciones/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../controllers/UsuarioController.php';
require_once __DIR__ . '/../../../layout/user_header.php';

$usuarioController = new UsuarioController();
$datos_cliente = $usuarioController->obtenerDatosDashboardCliente($_SESSION['usuario_id']);
?>

<div class="container mt-4">
    <div class="user-dashboard-container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="welcome-card">
                    <div class="welcome-content text-center">
                        <div class="welcome-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2 class="welcome-title">¡Hola, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>!</h2>
                        <p class="welcome-text">Bienvenido a tu panel de control</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="client-info-card">
                    <div class="card-header">
                        <h3><i class="fas fa-info-circle me-2"></i>Mis Datos</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user me-2"></i>Nombre</th>
                                        <th><i class="fas fa-map-marker-alt me-2"></i>Dirección</th>
                                        <th><i class="fas fa-calendar-alt me-2"></i>Fecha de Registro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo htmlspecialchars($datos_cliente['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($datos_cliente['direccion']); ?></td>
                                        <td><?php echo htmlspecialchars($datos_cliente['fecha_registro']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
