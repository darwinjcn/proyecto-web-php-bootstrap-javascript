<?php
session_start();

if (!isset($_SESSION['usuario_logueado'])) {
    header('Location: login.php');
    exit;
}

// ==========================================
// FUNCIONES AUXILIARES JSON
// ==========================================
$jsonFile = __DIR__ . '/data/productos.json';

function leerProductos($file) {
    if (!file_exists($file)) return [];
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function guardarProductos($file, $productos) {
    file_put_contents($file, json_encode($productos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function siguienteId($productos) {
    if (empty($productos)) return 1;
    $ids = array_column($productos, 'id');
    return max($ids) + 1;
}

// ==========================================
// PROCESAR ACCIONES
// ==========================================
$mensaje = '';
$tipo = '';
$productoEditar = null;
$editId = null;

// --- ELIMINAR ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $idEliminar = (int)$_GET['id'];
    $productos = leerProductos($jsonFile);
    $productos = array_filter($productos, fn($p) => $p['id'] !== $idEliminar);
    $productos = array_values($productos); // Reindexar
    guardarProductos($jsonFile, $productos);
    $mensaje = 'Producto eliminado correctamente.';
    $tipo = 'success';
}

// --- CARGAR PARA EDITAR ---
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $productos = leerProductos($jsonFile);
    foreach ($productos as $p) {
        if ($p['id'] === $editId) {
            $productoEditar = $p;
            break;
        }
    }
}

// --- CREAR O ACTUALIZAR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreProducto = trim($_POST['nombreProducto'] ?? '');
    $precio = $_POST['precio'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $editIdPost = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;

    $errores = [];
    if (empty($nombreProducto) || strlen($nombreProducto) < 2) $errores[] = 'Nombre de producto inválido.';
    if (empty($precio) || !is_numeric($precio) || $precio <= 0) $errores[] = 'Precio inválido.';
    if (empty($descripcion) || strlen($descripcion) < 10) $errores[] = 'Descripción muy corta (mín. 10 caracteres).';

    if (empty($errores)) {
        $productos = leerProductos($jsonFile);

        if ($editIdPost) {
            // ACTUALIZAR
            $encontrado = false;
            foreach ($productos as &$p) {
                if ($p['id'] === $editIdPost) {
                    $p['nombre'] = htmlspecialchars($nombreProducto);
                    $p['precio'] = number_format((float)$precio, 2);
                    $p['descripcion'] = htmlspecialchars($descripcion);
                    $p['fecha'] = date('d/m/Y H:i') . ' (editado)';
                    $encontrado = true;
                    break;
                }
            }
            unset($p);
            if ($encontrado) {
                guardarProductos($jsonFile, $productos);
                $mensaje = '¡Producto actualizado correctamente!';
                $tipo = 'success';
            }
        } else {
            // CREAR NUEVO
            $nuevoProducto = [
                'id' => siguienteId($productos),
                'nombre' => htmlspecialchars($nombreProducto),
                'precio' => number_format((float)$precio, 2),
                'descripcion' => htmlspecialchars($descripcion),
                'fecha' => date('d/m/Y H:i')
            ];
            $productos[] = $nuevoProducto;
            guardarProductos($jsonFile, $productos);
            $mensaje = '¡Producto registrado exitosamente!';
            $tipo = 'success';
        }
    } else {
        $mensaje = implode('<br>', $errores);
        $tipo = 'danger';
        // Si falla la validación en edición, mantener datos en el formulario
        if ($editIdPost) {
            $productoEditar = [
                'id' => $editIdPost,
                'nombre' => htmlspecialchars($nombreProducto),
                'precio' => $precio,
                'descripcion' => htmlspecialchars($descripcion)
            ];
            $editId = $editIdPost;
        }
    }
}

// Leer productos para mostrar en tabla
$productos = leerProductos($jsonFile);
$usuario = $_SESSION['usuario_logueado']['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Sistema Web</title>
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Registro</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link active" href="producto.php">Productos</a></li>
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link" href="index.php?logout=1" style="color: #f87171 !important; font-weight: 600;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;vertical-align:middle;">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Welcome banner -->
        <div class="text-center mb-4" style="color: #fff;">
            <h1 class="h4 fw-bold mb-1" style="text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                Panel de Productos
            </h1>
            <p class="mb-0 opacity-75" style="font-size:0.85rem;text-shadow: 0 1px 4px rgba(0,0,0,0.3);">
                Bienvenido, <strong><?php echo htmlspecialchars($usuario); ?></strong>. Gestiona tu inventario.
            </p>
        </div>

        <div class="row g-4">
            <!-- Formulario de Producto (Crear / Editar) -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <?php if ($productoEditar): ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle;">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Editar Producto
                            <?php else: ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle;">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Nuevo Producto
                            <?php endif; ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo; ?> alert-dismissible fade show" role="alert">
                                <?php echo $mensaje; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form id="productoForm" action="producto.php" method="POST" novalidate>
                            <?php if ($editId): ?>
                                <input type="hidden" name="edit_id" value="<?php echo $editId; ?>">
                            <?php endif; ?>

                            <div class="mb-2">
                                <label for="nombreProducto" class="form-label">Nombre del producto</label>
                                <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" 
                                       value="<?php echo $productoEditar ? htmlspecialchars($productoEditar['nombre']) : ''; ?>"
                                       placeholder="Ej: Laptop Dell XPS 13" required>
                                <div class="error-message" id="nombreProductoError"></div>
                            </div>
                            <div class="mb-2">
                                <label for="precio" class="form-label">Precio (USD)</label>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio" 
                                       value="<?php echo $productoEditar ? $productoEditar['precio'] : ''; ?>"
                                       placeholder="999.99" required>
                                <div class="error-message" id="precioError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                          placeholder="Describe las características del producto..." required><?php echo $productoEditar ? htmlspecialchars($productoEditar['descripcion']) : ''; ?></textarea>
                                <div class="error-message" id="descripcionError"></div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <?php if ($productoEditar): ?>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;vertical-align:middle;">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    Guardar Cambios
                                    <?php else: ?>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;vertical-align:middle;">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Registrar Producto
                                    <?php endif; ?>
                                </button>
                                <?php if ($productoEditar): ?>
                                    <a href="producto.php" class="btn btn-outline-secondary">
                                        Cancelar edición
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Productos -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle;">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            Inventario
                        </h4>
                        <span class="badge bg-light text-dark"><?php echo count($productos); ?> productos</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($productos)): ?>
                            <div class="p-5 text-center">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                                <p class="mb-0 text-muted">No hay productos registrados aún.<br><small>Agrega tu primer producto usando el formulario.</small></p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Descripción</th>
                                            <th>Fecha</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_reverse($productos) as $prod): ?>
                                            <tr>
                                                <td><span class="badge" style="background: var(--primary-light); color: var(--primary-dark);">#<?php echo $prod['id']; ?></span></td>
                                                <td><strong><?php echo $prod['nombre']; ?></strong></td>
                                                <td><span style="color: var(--success); font-weight: 600;">$<?php echo $prod['precio']; ?></span></td>
                                                <td><?php echo substr($prod['descripcion'], 0, 40) . (strlen($prod['descripcion']) > 40 ? '...' : ''); ?></td>
                                                <td><small style="color: var(--gray-500);"><?php echo $prod['fecha']; ?></small></td>
                                                <td class="text-center">
                                                    <a href="producto.php?edit_id=<?php echo $prod['id']; ?>" class="btn btn-sm" style="background: rgba(79,70,229,0.1); color: var(--primary); border: none; font-weight: 600; font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:3px;vertical-align:middle;">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                        </svg>
                                                        Editar
                                                    </a>
                                                    <a href="producto.php?action=delete&id=<?php echo $prod['id']; ?>" 
                                                       class="btn btn-sm" 
                                                       style="background: rgba(239,68,68,0.1); color: var(--danger); border: none; font-weight: 600; font-size: 0.75rem; padding: 0.35rem 0.75rem;"
                                                       onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:3px;vertical-align:middle;">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                        Eliminar
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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
