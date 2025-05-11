<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['nombre_usuario'])) {
    header('Location: /Cotizaciones/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Cotizaciones - Panel de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/style.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/header.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/cotizaciones.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/dataTables.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/clientes.css">
    <link rel="stylesheet" href="/Cotizaciones/public/css/dashboard.css">





</head>

<body>
    <div class="header-import">
        <header class="bg-purple text-white py-3">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><a class="title_gestor" href="/Cotizaciones/app/views/usuario/dashboard.php">Gestor de Cotizaciones</a></h1>
                    <div class="user-info">
                        <span>Cliente: <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></span>
                        <a href="/Cotizaciones/auth/logout.php" class="btn btn-outline-light btn-sm ms-2">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="/Cotizaciones/app/views/usuario/dashboard.php">
                                        <i class="fas fa-home"></i> Inicio
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Cotizaciones/app/views/clientes/clientes_crear.php">
                                        <i class="fas fa-user-plus"></i> Registrarse como Cliente
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Cotizaciones/app/views/productos/productos_crear.php">
                                        <i class="fas fa-box"></i> Productos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Cotizaciones/app/views/cotizaciones/cotizaciones_crear.php">
                                        <i class="fas fa-file-invoice-dollar"></i> Cotizaciones
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Cotizaciones/app/views/reportes/listado_cliente.php">
                                        <i class="fas fa-chart-bar"></i> Listado de Cotizaciones
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>