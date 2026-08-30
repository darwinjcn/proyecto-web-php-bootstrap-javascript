<?php
session_start();

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errores = [];
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Email inválido.';
    if (empty($password)) $errores[] = 'Contraseña requerida.';

    if (empty($errores)) {
        $usuarioDemo = ['email' => 'demo@proyecto.com', 'password' => 'Demo1234'];
        $usuarioRegistrado = $_SESSION['usuario_registrado'] ?? null;

        $loginOk = false;
        if ($email === $usuarioDemo['email'] && $password === $usuarioDemo['password']) {
            $loginOk = true;
            $_SESSION['usuario_logueado'] = ['nombre' => 'Usuario Demo', 'email' => $email];
        } elseif ($usuarioRegistrado && $email === $usuarioRegistrado['email'] && !empty($password)) {
            $loginOk = true;
            $_SESSION['usuario_logueado'] = $usuarioRegistrado;
        }

        if ($loginOk) {
            $mensaje = '¡Bienvenido de vuelta! Redirigiendo...';
            $tipo = 'success';
            header('Refresh: 2; URL=producto.php');
        } else {
            $mensaje = 'Credenciales incorrectas. Intenta con las credenciales de demo.';
            $tipo = 'danger';
        }
    } else {
        $mensaje = implode('<br>', $errores);
        $tipo = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Sistema Web</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;vertical-align:middle;">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                SistemaWeb
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Registro</a></li>
                    <li class="nav-item"><a class="nav-link active" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="producto.php">Productos</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">

                <!-- Hero text -->
                <div class="text-center mb-4" style="color: #fff;">
                    <h1 class="display-6 fw-bold mb-2" style="text-shadow: 0 2px 10px rgba(0,0,0,0.3);">Bienvenido</h1>
                    <p class="mb-0 opacity-75" style="text-shadow: 0 1px 4px rgba(0,0,0,0.3);">Ingresa tus credenciales para continuar</p>
                </div>

                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="mb-0">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;vertical-align:middle;">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Iniciar Sesión
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo; ?> alert-dismissible fade show mb-4" role="alert">
                                <?php echo $mensaje; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form id="loginForm" action="login.php" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="demo@proyecto.com" required>
                                <div class="error-message" id="emailError"></div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="••••••" required>
                                <div class="error-message" id="passwordError"></div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Ingresar
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 p-3 rounded-3" style="background: var(--gray-50); border: 1px solid var(--gray-200);">
                            <p class="mb-1" style="font-size: 0.8rem; font-weight: 600; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em;">
                                Credenciales de demo
                            </p>
                            <p class="mb-0" style="font-size: 0.85rem; color: var(--gray-500);">
                                <strong>Email:</strong> demo@proyecto.com<br>
                                <strong>Contraseña:</strong> Demo1234
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0" style="color: var(--gray-500); font-size: 0.9rem;">
                                ¿No tienes cuenta? 
                                <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Regístrate aquí</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
