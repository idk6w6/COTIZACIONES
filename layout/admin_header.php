<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_path = $_SERVER['PHP_SELF'];
if (!str_contains($current_path, '/auth/') && !isset($_SESSION['usuario_id'])) {
    header('Location: /Cotizaciones/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Cotizaciones - Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/style.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/header.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/cotizaciones.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/dataTables.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/productos.css">

    
</head>

<body>
    <div class="header-import">
        <header class="bg-purple text-white py-3">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><a class="title_gestor" href="/Cotizaciones/app/views/admin/dashboard.php">Gestor de Cotizaciones</a></h1>

                    <div class="user-info">
                        <span>Admin: <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></span>
                        <a href="/Cotizaciones/auth/logout.php" class="btn btn-outline-light btn-sm ms-2">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="/Cotizaciones/app/views/clientes/clientes_editar.php">
                                    <i class="fas fa-users"></i> Clientes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Cotizaciones/app/views/productos/productos_editar.php">
                                    <i class="fas fa-box"></i> Productos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Cotizaciones/app/views/cotizaciones/cotizaciones_clientes.php">
                                    <i class="fas fa-file-invoice-dollar"></i> Cotización a Clientes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/Cotizaciones/app/views/reportes/reportes_listado.php">
                                    <i class="fas fa-chart-bar"></i> Reportes de Cotizaciones
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
    </div>
</body>

</html>