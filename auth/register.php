<?php
require_once '../app/models/authUsers.php';

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new AuthUsers();
    try {
        if ($auth->register($_POST['nombre_usuario'], $_POST['correo'], $_POST['contrasena'])) {
            header('Location: login.php?success=1');
            exit();
        } else {
            $mensaje = 'Error al registrar el usuario. Por favor, intente nuevamente.';
        }
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/auth.css">
</head>

<body class="auth-page register-page">
    <div class="auth-wrapper">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-md-6 col-lg-4">
                    <div class="auth-box register-box animate__animated animate__fadeInUp">
                        <div class="auth-header">
                            <i class="bi bi-person-plus-fill auth-icon"></i>
                            <h2>Registro de Usuario</h2>
                        </div>
                        <div class="auth-body glass-effect">
                            <?php if ($mensaje): ?>
                                <div class="alert alert-danger"><?php echo $mensaje; ?></div>
                            <?php endif; ?>
                            <form method="POST" class="auth-form register-form">
                                <div class="form-group">
                                    <label>Nombre de Usuario</label>
                                    <div class="input-group">
                                        <input type="text" name="nombre_usuario" class="form-control" required>
                                        <i class="bi bi-person position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Correo Electrónico</label>
                                    <div class="input-group">
                                        <input type="email" name="correo" class="form-control" required>
                                        <i class="bi bi-envelope position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="contrasena" class="form-control" required>
                                        <i class="bi bi-lock position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-auth btn-register">Registrarse</button>
                            </form>
                            <div class="auth-footer">
                                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                                <p><a href="../index.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Volver</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>