<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $edad = $_POST['edad'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');

    $errores = [];
    if (empty($nombre) || strlen($nombre) < 3) $errores[] = 'Nombre inválido.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Email inválido.';
    if (empty($password) || strlen($password) < 6) $errores[] = 'Contraseña muy corta.';
    if (empty($edad) || $edad < 18 || $edad > 120) $errores[] = 'Edad inválida.';
    if (empty($telefono)) $errores[] = 'Teléfono inválido.';

    if (empty($errores)) {
        $_SESSION['usuario_registrado'] = [
            'nombre' => htmlspecialchars($nombre),
            'email' => htmlspecialchars($email),
            'edad' => (int)$edad,
            'telefono' => htmlspecialchars($telefono)
        ];
        $mensaje = '¡Registro exitoso! Redirigiendo al login...';
        $tipo = 'success';
        header('Refresh: 2; URL=login.php');
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
    <title>Registro de Usuario | Sistema Web</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle;">
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
                    <li class="nav-item"><a class="nav-link active" href="index.php">Registro</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="producto.php">Productos</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">

                <!-- Hero text compacto -->
                <div class="text-center mb-2" style="color: #fff;">
                    <h1 class="h4 fw-bold mb-1" style="text-shadow: 0 2px 10px rgba(0,0,0,0.3);">Crear Cuenta</h1>
                    <p class="mb-0 opacity-75" style="font-size:0.85rem;text-shadow: 0 1px 4px rgba(0,0,0,0.3);">Completa el formulario para comenzar</p>
                </div>

                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="mb-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle;">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </svg>
                            Registro de Usuario
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo; ?> alert-dismissible fade show" role="alert">
                                <?php echo $mensaje; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form id="registroForm" action="index.php" method="POST" novalidate>
                            <div class="mb-2">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       placeholder="Ej: Juan Pérez" required>
                                <div class="error-message" id="nombreError"></div>
                            </div>
                            <div class="mb-2">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="juan@empresa.com" required>
                                <div class="error-message" id="emailError"></div>
                            </div>
                            <div class="mb-2">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Mín. 6 caracteres" required>
                                <div class="error-message" id="passwordError"></div>
                            </div>
                            <div class="mb-2">
                                <label for="edad" class="form-label">Edad</label>
                                <input type="number" class="form-control" id="edad" name="edad" 
                                       placeholder="Debe ser mayor de edad (18+)" min="1" required>
                                <div class="error-message" id="edadError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       placeholder="+58 412-1234567" required>
                                <div class="error-message" id="telefonoError"></div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Crear cuenta
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0" style="color: var(--gray-500); font-size: 0.85rem;">
                                ¿Ya tienes cuenta? 
                                <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Inicia sesión aquí</a>
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
