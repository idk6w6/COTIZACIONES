<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: /Cotizaciones/auth/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/UsuarioController.php';
$usuarioController = new UsuarioController();
$usuarios = $usuarioController->obtenerTodosUsuarios();

include '../../../layout/admin_header.php';
?>

<div class="container mt-4">
    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_GET['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></h2>
                    <p class="card-text">Panel de administración del sistema de cotizaciones.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Usuarios Registrados</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Usuario</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                                    <td><?= htmlspecialchars($usuario['fecha_creacion']) ?></td>
                                    <td>
                                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger"
                                                onclick="confirmarEliminacion(<?= $usuario['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editarUsuarioForm">
                    <input type="hidden" id="usuario_id" name="id">
                    <div class="mb-3">
                        <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-control" id="rol" name="rol" required>
                            <option value="Administrador">Administrador</option>
                            <option value="Cliente">Cliente</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarUsuario()">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../../layout/footer.php'; ?>

<script>
function confirmarEliminacion(id) {
    if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
        fetch(`/Cotizaciones/app/controllers/eliminar_usuario.php?id=${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error al eliminar el usuario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el usuario');
        });
    }
}

function abrirModalEditar(id, nombre, correo, rol) {
    document.getElementById('usuario_id').value = id;
    document.getElementById('nombre_usuario').value = nombre;
    document.getElementById('correo').value = correo;
    document.getElementById('rol').value = rol;
    
    let modal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'));
    modal.show();
}

function actualizarUsuario() {
    const formData = new FormData(document.getElementById('editarUsuarioForm'));
    
    fetch('/Cotizaciones/app/controllers/actualizar_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Usuario actualizado correctamente');
            location.reload();
        } else {
            alert('Error al actualizar el usuario: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el usuario');
    });
}

// Modificar el botón de editar en la tabla
document.querySelectorAll('a[href^="editar_usuario.php"]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const row = this.closest('tr');
        const id = row.querySelector('td:first-child').textContent;
        const nombre = row.querySelector('td:nth-child(2)').textContent;
        const correo = row.querySelector('td:nth-child(3)').textContent;
        const rol = row.querySelector('td:nth-child(4)').textContent;
        
        abrirModalEditar(id, nombre, correo, rol);
    });
});
</script>

