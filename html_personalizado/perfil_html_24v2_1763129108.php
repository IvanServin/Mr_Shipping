<?php
// Cargar datos del perfil dinámicamente
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mostrar mensajes
if (isset($_SESSION['mensaje_exito'])) {
    echo "<div style='background:#d4edda; color:#155724; padding:10px; margin:10px; border-radius:5px; text-align:center;'>".$_SESSION['mensaje_exito']."</div>";
    unset($_SESSION['mensaje_exito']);
}

if (isset($_SESSION['error'])) {
    echo "<div style='background:#f8d7da; color:#721c24; padding:10px; margin:10px; border-radius:5px; text-align:center;'>".$_SESSION['error']."</div>";
    unset($_SESSION['error']);
}

// ✅ CORRECCIÓN: Incluir archivos necesarios con rutas absolutas
$base_dir = dirname(__DIR__);
include_once $base_dir . '/conexion/conexion.php';
include_once $base_dir . '/includes/funciones_estilos.php';

$con = conectar();

// Verificar si la sesión está activa
if (!isset($_SESSION['id_usuario'])) {
    $mensaje_sesion = "Inicia sesión para ver o cargar productos.";
} else {
    $id_usuario = $_SESSION['id_usuario'];
    
    $query_perfil = "SELECT * FROM perfil WHERE id_usuario = ?";
    $stmt_perfil = $con->prepare($query_perfil);
    $stmt_perfil->bind_param("i", $id_usuario);
    $stmt_perfil->execute();
    $result_perfil = $stmt_perfil->get_result();
    
    if ($result_perfil && $result_perfil->num_rows > 0) {
        $perfil = $result_perfil->fetch_assoc();
        $rt_foto_perfil = !empty($perfil['foto_perfil']) ? $perfil['foto_perfil'] : './img/default.jpg';
        $foto_portada = !empty($perfil['foto_portada']) ? $perfil['foto_portada'] : null;
        $biografia = !empty($perfil['biografia']) ? $perfil['biografia'] : 'Agrega info para que la gente sepa más de ti';
    } else {
        $rt_foto_perfil = './img/default.jpg';
        $foto_portada = null;
        $biografia = 'Agrega info para que la gente sepa más de ti';
    }
    $stmt_perfil->close();
}

// Procesar cierre de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// Generar HTML de productos
$productos_html = '';
if (isset($mensaje_sesion)) {
    $productos_html = "<p style='text-align:center; padding:2rem; color:#666;'>".$mensaje_sesion."</p>";
} else {
    $query = "SELECT * FROM productos WHERE id_usuario = ? AND estado_venta = 'disponible'";
    $stmt = $con->prepare($query); 
    if ($stmt) {
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) > 0) {
            while ($producto = $result->fetch_assoc()) {
                $imagenes_string = $producto['dir_img'] ?? '';
                $imagenes = !empty($imagenes_string) ? explode(',', $imagenes_string) : [];
                
                $imagen_destino = !empty($imagenes[0]) ? 
                    (strpos($imagenes[0], './') === 0 ? $imagenes[0] : './' . $imagenes[0]) : 
                    './img/default.jpg';

                $productos_html .= "
                    <div class='perfil_tarjeta' data-product-id='{$producto['id_producto']}'>
                        <div class='perfil_img-container'>
                            <img src='{$imagen_destino}' alt='{$producto['nombre_producto']}' onerror=\"this.src='./img/default.jpg'\">
                        </div>
                        <div class='perfil_contenido'>
                            <h3>{$producto['nombre_producto']}</h3>
                            <span class='perfil_precio'>$ {$producto['precio']}</span>
                        </div>
                    </div>
                ";
            }
        } else {
            $productos_html = "<h3 style='text-align:center; grid-column:1/-1; color:#666; padding:2rem;'>No tienes productos disponibles.</h3>";
        }
        $stmt->close();
    } else {
        $productos_html = "<h3 style='text-align:center; grid-column:1/-1; color:#666; padding:2rem;'>Error al obtener los productos.</h3>";
    }
}

// Preparar datos para reemplazar
$datos_reemplazo = [
    'foto_portada' => $foto_portada ?? '',
    'foto_perfil' => $rt_foto_perfil,
    'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
    'biografia' => nl2br(htmlspecialchars($biografia, ENT_QUOTES, 'UTF-8')),
    'productos' => $productos_html
];
?>
<!-- PERFIL TIENDA CROCHET -->

<div class="foto-portada">
    <img src="<?php echo $datos_reemplazo['foto_portada']; ?>" alt="Foto de portada tienda crochet" onerror="this.style.display='none'">
</div>

<div class="cabecera">
    <div class="cont_img">
        <img src="<?php echo $datos_reemplazo['foto_perfil']; ?>" alt="Foto de perfil tienda crochet" onerror="this.style.display='none'">
    </div>
    <div class="datos_us">
        <div class="cont_name">
            <h3><?php echo htmlspecialchars($datos_reemplazo['nombre_usuario']); ?></h3>
        </div>
        <div class="cont_descripcion">
            <p><?php echo $datos_reemplazo['biografia']; ?></p>
        </div>
    </div>
</div>

<nav class="cont_nav_perfil">
    <ul class="nav-lista">
        <li><a href="index.php?modulo=perfil">🧶 Mi Perfil</a></li>
        <li><a href="index.php?modulo=cargar_productos">📦 Publicar producto</a></li>
        <li><a href="./index.php">🏠 Inicio</a></li>
        <li><a href="index.php?modulo=asistente_perfil">✨ Asistente IA</a></li>
        <li class="menu-config">
            <a href="#" class="menu-trigger" onclick="toggleMenu(event)">
                <i class="fa-solid fa-gear"></i> Configuración
            </a>
            <div class="menu-desplegable" id="menuConfig">
                <button type="button" onclick="window.location.href='./index.php?modulo=editar_perfil'">
                    ✏️ Editar perfil
                </button>
                <form action="" method="POST">
                    <button type="submit" class="btn-cerrar-sesion">🚪 Cerrar Sesión</button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<div class="cont_productos">
    <?php echo $datos_reemplazo['productos']; ?>
</div>

<script>
// FUNCIÓN PARA EL MENÚ DESPLEGABLE 
function toggleMenu(event) {
    event.preventDefault();
    const menu = document.getElementById('menuConfig');
    menu.classList.toggle('mostrar');
}

// CERRAR MENÚ AL HACER CLIC FUERA
document.addEventListener('DOMContentLoaded', function() {
    // Configurar el menú desplegable
    const menu = document.getElementById('menuConfig');
    const trigger = document.querySelector('.menu-trigger');
    
    document.addEventListener('click', function(event) {
        
    });

    // Cerrar menú al hacer scroll
    window.addEventListener('scroll', function() {
        menu.classList.remove('mostrar');
    });
    
    // Configurar botones de productos si existen
    const tarjetasProductos = document.querySelectorAll('.perfil_tarjeta');
    tarjetasProductos.forEach(tarjeta => {
        tarjeta.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            
        });
    });
});

// Manejar el formulario de cerrar sesión
document.addEventListener('DOMContentLoaded', function() {
    const formsCerrarSesion = document.querySelectorAll('form[method="POST"]');
    formsCerrarSesion.forEach(form => {
        form.addEventListener('submit', function(e) {
            const botonCerrar = this.querySelector('.btn-cerrar-sesion');
            
        });
    });
});
</script>