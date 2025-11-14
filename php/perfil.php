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
include_once __DIR__ . '/../includes/funciones_estilos.php';
$con = conectar();
// FUNCIÓN TEMPORAL PARA DEBUG
function debugPlantillas($id_usuario) {
    global $con;
    
    echo "<!-- DEBUG FUNCIÓN: Buscando plantillas para usuario $id_usuario -->";
    
    $query = "SELECT COUNT(*) as total FROM estilos_perfil WHERE id_usuario = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['total'];
    
    echo "<!-- DEBUG FUNCIÓN: Total de plantillas en BD: $count -->";
    
    return $count;
}

// En el selector, prueba con:
$total_plantillas = debugPlantillas($id_usuario);
echo "<!-- DEBUG: Total plantillas: $total_plantillas -->";

// DEBUG VISIBLE
echo "<!-- DEBUG: Sesión iniciada -->";
echo "<!-- DEBUG: ID Usuario en sesión: " . ($_SESSION['id_usuario'] ?? 'NO HAY') . " -->";

// Verificar si la sesión está activa y obtener el id_usuario desde la sesión
if (!isset($_SESSION['id_usuario'])) {
  $mensaje_sesion = "Inicia sesión para ver o cargar productos.";
  echo "<!-- DEBUG: Usuario NO logueado -->";
} else {
  $id_usuario = $_SESSION['id_usuario'];
  echo "<!-- DEBUG: Usuario logueado - ID: $id_usuario -->";
  
  // CARGAR PLANTILLA ACTIVA DEL USUARIO
  $plantilla_activa = obtenerPlantillaActiva($id_usuario);
  echo "<!-- DEBUG: Plantilla activa: " . print_r($plantilla_activa, true) . " -->";
  
  if ($plantilla_activa) {
      $ruta_css = $plantilla_activa['ruta_css'];
      $ruta_html = $plantilla_activa['ruta_html'];
      echo "<!-- DEBUG: CSS: $ruta_css -->";
      echo "<!-- DEBUG: HTML: $ruta_html -->";
  } else {
      // Valores por defecto si no hay plantilla activa
      $ruta_css = 'estilos/estilos_perfil.css';
      $ruta_html = 'html_personalizado/perfil_base.php';
      echo "<!-- DEBUG: Usando valores por defecto -->";
  }
  
  // Cargar CSS y HTML de la plantilla activa
  $css_personalizado = file_exists($ruta_css) ? file_get_contents($ruta_css) : '';
  $html_personalizado = file_exists($ruta_html) ? file_get_contents($ruta_html) : '';
  echo "<!-- DEBUG: CSS cargado: " . (empty($css_personalizado) ? 'NO' : 'SÍ') . " -->";
  echo "<!-- DEBUG: HTML cargado: " . (empty($html_personalizado) ? 'NO' : 'SÍ') . " -->";
  
  // OBTENER PLANTILLAS PARA EL SELECTOR
  $plantillas = obtenerPlantillasUsuario($id_usuario);
  echo "<!-- DEBUG: Número de plantillas: " . count($plantillas) . " -->";
  echo "<!-- DEBUG: Plantillas: " . print_r($plantillas, true) . " -->";
  
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

// Aplicar CSS personalizado si existe
if (!empty($css_personalizado)) {
    echo "<style>" . $css_personalizado . "</style>";
    echo "<!-- DEBUG: CSS aplicado -->";
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

<?php if (isset($id_usuario)): ?>
<div class="selector-plantillas" style="background: rgba(255,255,255,0.95); padding: 1rem 2rem; border-bottom: 2px solid #e8f5e8; border: 3px solid #ff6b6b;">
    <h4 style="margin: 0 0 1rem 0; color: #2e7d32;">🎨 MIS PLANTILLAS DE ESTILO - SELECTOR VISIBLE</h4>
    
    <!-- INFO DEBUG VISIBLE -->
    <div style="background: #e3f2fd; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #2196f3;">
        <strong style="color: #1976d2;">INFORMACIÓN DE DEBUG:</strong><br>
        Usuario ID: <strong><?php echo $id_usuario; ?></strong><br>
        Plantillas encontradas: <strong><?php echo count($plantillas); ?></strong><br>
        <?php if (!empty($plantillas)): ?>
            Plantillas: <?php echo implode(', ', array_column($plantillas, 'nombre_estilo')); ?>
        <?php endif; ?>
    </div>
    
    <div class="lista-plantillas">
        <?php
        if (empty($plantillas)) {
            echo '<div style="text-align: center; padding: 2rem; color: #666; background: #fff3cd; border-radius: 10px;">';
            echo '<p style="margin: 0 0 1rem 0;"><strong>No tienes plantillas guardadas</strong></p>';
            echo '<a href="index.php?modulo=asistente_perfil" style="background: #388e3c; color: white; padding: 10px 20px; border-radius: 20px; text-decoration: none; display: inline-block;">Crea tu primera plantilla con el Asistente IA</a>';
            echo '</div>';
        } else {
            foreach ($plantillas as $plantilla) {
                $activa = $plantilla['esta_activo'] ? 'style="background: #c8e6c9; border-color: #4caf50; border: 2px solid #4caf50;"' : 'style="background: rgba(232,245,232,0.5); border: 2px solid #c8e6c9;"';
                echo "
                <div class='plantilla-item' {$activa}>
                    <div class='plantilla-info'>
                        <strong style='color: #2e7d32; display: block; font-size: 1.1rem;'>{$plantilla['nombre_estilo']}</strong>
                        <span style='background: #4caf50; color: white; padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.8rem;'>v{$plantilla['version']}</span>
                        <small style='display: block; color: #666; margin-top: 0.5rem;'>{$plantilla['descripcion']}</small>
                        <small style='color: #888;'>Creado: " . date('d/m/Y H:i', strtotime($plantilla['fecha_creacion'])) . "</small>
                    </div>
                    <div class='plantilla-acciones'>";
                
                if (!$plantilla['esta_activo']) {
                    echo "<button onclick='activarPlantilla({$plantilla['id_estilo']})' style='background: #388e3c; color: white; border: none; padding: 0.5rem 1rem; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; font-weight: bold;'>Usar esta plantilla</button>";
                } else {
                    echo "<span style='background: #4caf50; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: bold; font-size: 1.1rem;'>✓ ACTIVA ACTUALMENTE</span>";
                }
                
                echo "</div>
                </div>";
            }
        }
        ?>
    </div>
</div>
<?php else: ?>
<!-- DEBUG: Si no hay usuario -->
<div style="background: #ffebee; padding: 1rem 2rem; border-bottom: 2px solid #e8f5e8; border: 3px solid #f44336;">
    <h4 style="margin: 0; color: #c62828;">⚠️ SELECTOR NO VISIBLE - USUARIO NO LOGUEADO</h4>
    <p style="color: #c62828; margin: 0.5rem 0 0 0;">Inicia sesión para ver el selector de plantillas.</p>
</div>
<?php endif; ?>

<!-- El resto de tu código permanece igual -->
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

<!-- ... resto del código ... -->

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

// FUNCIÓN PARA ACTIVAR PLANTILLAS
function activarPlantilla(id_estilo) {
    if (!confirm('¿Estás seguro de que quieres cambiar a esta plantilla?')) {
        return;
    }
    
    fetch('acciones/cambiar_plantilla.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id_estilo=${id_estilo}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Plantilla cambiada correctamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar la plantilla');
    });
}
</script>

