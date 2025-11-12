<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Mostrar mensajes
if (isset($_SESSION['mensaje_exito'])) {
    echo "<div style='background:#d4edda; color:#155724; padding:10px; margin:10px; border-radius:5px; text-align:center;'>{$_SESSION['mensaje_exito']}</div>";
    unset($_SESSION['mensaje_exito']);
}

if (isset($_SESSION['error'])) {
    echo "<div style='background:#f8d7da; color:#721c24; padding:10px; margin:10px; border-radius:5px; text-align:center;'>{$_SESSION['error']}</div>";
    unset($_SESSION['error']);
}

if (isset($_SESSION['mensaje_info'])) {
    echo "<div style='background:#d1ecf1; color:#0c5460; padding:10px; margin:10px; border-radius:5px; text-align:center;'>{$_SESSION['mensaje_info']}</div>";
    unset($_SESSION['mensaje_info']);
}

include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();

// Verificar si la sesión está activa y obtener el id_usuario desde la sesión
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
?>

<!-- CONTENIDO DEL PERFIL -->
<?php if (!empty($foto_portada)): ?>
<div class="foto-portada">
    <img src="<?php echo $foto_portada; ?>" alt="Foto de portada" onerror="this.style.display='none'">
</div>
<?php endif; ?>

<div class="cabecera">
    <div class="cont_img">
        <?php echo "<img src='{$rt_foto_perfil}' alt='foto perfil' onerror=\"this.src='./img/default.jpg'\">"; ?>
    </div>
    <div class="datos_us">
        <div class="cont_name">
            <?php
            if (isset($_SESSION['nombre_us'])) {
                echo "<h3>{$_SESSION['nombre_us']}</h3>";
            } else {
                echo "<h3>User</h3>";
            }
            ?>
        </div>
        <div class="cont_descripcion">
            <?php
            if (isset($biografia)) {
                echo "<p>" . nl2br(htmlspecialchars($biografia, ENT_QUOTES, 'UTF-8')) . "</p>";
            } else {
                echo "<p>Agrega info para que la gente sepa más de ti</p>";
            }
            ?>
        </div>
    </div>
</div>

<nav class="cont_nav_perfil">
    <ul class="nav-lista">
        <li><a href="index.php?modulo=perfil">Mi Perfil</a></li>
        <li><a href="index.php?modulo=cargar_productos">Publicar producto</a></li>
        <li><a href="./index.php">Inicio</a></li>
        <li><a href="index.php?modulo=asistente_perfil">Asistente IA ✨🖌️</a></li>
        <li class="menu-config">
            <a href="#" class="menu-trigger" onclick="toggleMenu(event)">
                <i class="fa-solid fa-gear"></i>
            </a>
            <div class="menu-desplegable" id="menuConfig">
                <button type="button" onclick="window.location.href='./index.php?modulo=editar_perfil'">
                    Editar perfil
                </button>
                <form action="" method="POST">
                    <button type="submit" class="btn-cerrar-sesion">Cerrar Sesión</button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<div class="cont_productos">
    <?php
    if (isset($mensaje_sesion)) {
        echo "<p style='text-align:center; padding:2rem; color:#666;'>{$mensaje_sesion}</p>";
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

                    echo "
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
                echo "<h3 style='text-align:center; grid-column:1/-1; color:#666; padding:2rem;'>No tienes productos disponibles.</h3>";
            }
            $stmt->close();
        } else {
            echo "<h3 style='text-align:center; grid-column:1/-1; color:#666; padding:2rem;'>Error al obtener los productos.</h3>";
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        session_unset();
        session_destroy();
        header('Location:index.php');
        exit;
    }
    ?>
</div>

<script>
// FUNCIÓN PARA EL MENÚ DESPLEGABLE
function toggleMenu(event) {
    event.preventDefault();
    const menu = document.getElementById('menuConfig');
    menu.classList.toggle('mostrar');
    
    const todosMenus = document.querySelectorAll('.menu-desplegable');
    todosMenus.forEach(m => {
        if (m !== menu) {
            m.classList.remove('mostrar');
        }
    });
}

// CERRAR MENÚ AL HACER CLIC FUERA
document.addEventListener('click', function(event) {
    const menu = document.getElementById('menuConfig');
    const trigger = document.querySelector('.menu-trigger');
    
    if (menu && !menu.contains(event.target) && !trigger.contains(event.target)) {
        menu.classList.remove('mostrar');
    }
});

// CERRAR MENÚ AL HACER SCROLL
window.addEventListener('scroll', function() {
    const menu = document.getElementById('menuConfig');
    if (menu) {
        menu.classList.remove('mostrar');
    }
});
</script>