<?php
require_once '../app/models/usuario.php';
session_start();

if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol'] === 'Administrador') { 
        header('Location: ../app/views/admin/dashboard.php');
        exit();
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena'];

    if ($correo && $contrasena) {
        $usuario = new Usuario();
        $result = $usuario->validarLogin($correo, $contrasena);

        if ($result) {
            $_SESSION['usuario_id'] = $result['id'];
            $_SESSION['nombre_usuario'] = $result['nombre_usuario'];
            $_SESSION['correo'] = $result['correo'];
            $_SESSION['rol'] = $result['rol'];

            if ($result['rol'] === 'Administrador') {
                header('Location: ../app/views/admin/dashboard.php');
                exit();
            } else {
                header('Location: ../app/views/usuario/dashboard.php');
                exit();
            }
        } else {
            $error = 'Credenciales inválidas';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="auth-page login-page">
    <div class="auth-wrapper">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-md-6 col-lg-4">
                    <div class="auth-box login-box animate__animated animate__fadeInUp">
                        <div class="auth-header">
                            <i class="bi bi-lock-fill auth-icon"></i>
                            <h2>Iniciar Sesión</h2>
                        </div>
                        <div class="auth-body glass-effect">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <form method="POST" class="auth-form login-form">
                                <div class="form-group">
                                    <label>Correo Electrónico</label>
                                    <input type="email" name="correo" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" name="contrasena" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-auth btn-login">Iniciar Sesión</button>
                            </form>
                            <div class="auth-footer">
                                <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
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
