<?php
session_start();
require_once __DIR__ . '/../../controllers/ClientesController.php';

$controller = new ClientesController();
$data = $controller->manejarFormularioCreacion();
$resultado = $data['resultado'];
$formData = $data['formData'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cliente</title>
    <link rel="stylesheet" href="/Cotizaciones/public/css/style.css"> <!-- Updated path -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="clientes_crear">
    <?php include __DIR__ . '/../../../layout/user_header.php'; ?>
    <div class="container mt-5">
        <div class="form-container">
            <h2>Crear Cliente</h2>
            
            <?php if ($resultado && isset($resultado['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($resultado['success']); ?>
                </div>
            <?php endif; ?>

            <?php if ($resultado && isset($resultado['errores']) && is_array($resultado['errores'])): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($resultado['errores'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($resultado && isset($resultado['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($resultado['error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group cliente-form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" class="form-control cliente-input" 
                           value="<?php echo htmlspecialchars($formData['nombre']); ?>">
                </div>
                <div class="form-group cliente-form-group">
                    <label for="celular1">Celular *</label>
                    <input type="text" name="celular1" class="form-control cliente-input" required
                           value="<?php echo isset($formData['celular1']) ? htmlspecialchars($formData['celular1']) : ''; ?>">
                </div>
                <div class="form-group cliente-form-group">
                    <label for="tel_oficina">Teléfono de Oficina *</label>
                    <input type="text" name="tel_oficina" class="form-control cliente-input" required
                           value="<?php echo isset($formData['tel_oficina']) ? htmlspecialchars($formData['tel_oficina']) : ''; ?>">
                </div>
                <div class="form-group cliente-form-group">
                    <label for="correo">Correo electrónico *</label>
                    <input type="email" name="correo" class="form-control cliente-input" readonly
                           value="<?php echo htmlspecialchars($formData['correo']); ?>">
                </div>
                <div class="form-group cliente-form-group">
                    <label for="direccion">Dirección *</label>
                    <textarea name="direccion" class="form-control cliente-input" required><?php echo isset($formData['direccion']) ? htmlspecialchars($formData['direccion']) : ''; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Guardar información</button>
            </form>
        </div>
    </div>
</body>
</html>
