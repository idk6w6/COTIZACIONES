<?php
require_once __DIR__ . '/../../controllers/ClientesController.php';
$controller = new ClientesController();
$resultado = $controller->manejarVistaEdicion();
$clientes = $resultado['clientes'];
$cliente = $resultado['cliente'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="/Cotizaciones/public/css/style.css"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> 
</head>
<body class="clientes_editar">
    <?php include 'c:/xampp/htdocs/Cotizaciones/layout/admin_header.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Editar Cliente</h2>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        switch($_GET['error']) {
                            case 1:
                                echo "Cliente no encontrado.";
                                break;
                            default:
                                echo "Error al procesar la solicitud.";
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        switch($_GET['success']) {
                            case 1:
                                echo "Cliente creado exitosamente.";
                                break;
                            case 2:
                                echo "Cliente actualizado exitosamente.";
                                break;
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="mb-4">
                    <div class="form-group">
                        <label for="search" class="form-label">Buscar Cliente</label>
                        <input type="text" name="search" class="form-control" 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                               placeholder="Buscar por nombre, celular, correo, etc.">
                    </div>
                    <button type="submit" class="btn btn-secondary">Buscar</button>
                    <?php if (isset($_GET['search'])): ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-outline-secondary ml-2">Limpiar búsqueda</a>
                    <?php endif; ?>
                </form>

                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-info">
                        <?php 
                            echo $_SESSION['mensaje'];
                            unset($_SESSION['mensaje']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php 
                if (!isset($cliente)) {
                    $cliente = null;
                }
                if ($cliente): ?>
                    <form action="" method="POST">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($cliente['id']); ?>">
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre del cliente</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="celular1" class="form-label">Celular</label>
                            <input type="text" name="celular1" class="form-control" value="<?php echo htmlspecialchars($cliente['celular1']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tel_oficina" class="form-label">Telefono de Oficina</label>
                            <input type="text" name="tel_oficina" class="form-control" value="<?php echo htmlspecialchars($cliente['tel_oficina']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="correo" class="form-label">Correo electronico</label>
                            <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea name="direccion" class="form-control"><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </form>
                <?php else: ?>
                <?php endif; ?>
                <div class="search-results mt-4">
                    <?php if (isset($_GET['search']) && empty($clientes)): ?>
                        <div class="alert alert-info">Cliente no encontrado.</div>
                    <?php endif; ?>
                    
                    <?php if (!empty($clientes)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Celular</th>
                                        <th>Tel. Oficina</th>
                                        <th>Correo</th>
                                        <th>Dirección</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($_GET['search']) && !empty($clientes)): ?>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($cliente['celular1']); ?></td>
                                                <td><?php echo htmlspecialchars($cliente['tel_oficina']); ?></td>
                                                <td><?php echo htmlspecialchars($cliente['correo']); ?></td>
                                                <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm me-2">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        <a href="#" onclick="return confirmarEliminacionCliente(<?= $cliente['id'] ?>)" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash-alt"></i> Eliminar
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php elseif (isset($cliente) && $cliente): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['celular1']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['tel_oficina']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['correo']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm me-2">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <a href="#" onclick="return confirmarEliminacionCliente(<?= $cliente['id'] ?>)" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash-alt"></i> Eliminar
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">No hay datos del cliente.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    function confirmarEliminacionCliente(id) {
        if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
            fetch(`/Cotizaciones/app/controllers/eliminar_cliente.php?id=${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cliente eliminado correctamente');
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al eliminar el cliente');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el cliente');
            });
        }
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deleteLinks = document.querySelectorAll('a[href*="action=delete"]');
        deleteLinks.forEach(link => {
            link.onclick = function(e) {
                e.preventDefault();
                const id = this.href.split('id=')[1].split('&')[0];
                confirmarEliminacionCliente(id);
            };
        });
    });
    </script>
</body>
</html>
