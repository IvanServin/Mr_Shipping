<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['css_ia']) || !isset($_SESSION['html_ia'])) {
    header('Location: index.php?modulo=asistente_perfil');
    exit;
}

// Obtener datos del usuario para la vista previa
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();

// Obtener datos reales del usuario
$datos_reemplazo = [
    'foto_portada' => '',
    'foto_perfil' => './img/default.jpg',
    'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
    'biografia' => 'Esta es una vista previa de tu perfil personalizado',
    'productos' => '<!-- Los productos se cargarán cuando apliques los cambios -->'
];

if (isset($_SESSION['id_usuario'])) {
    $query = "SELECT foto_perfil, foto_portada, biografia FROM perfil WHERE id_usuario = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $_SESSION['id_usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['foto_perfil'])) {
            $datos_reemplazo['foto_perfil'] = $row['foto_perfil'];
        }
        if (!empty($row['foto_portada'])) {
            $datos_reemplazo['foto_portada'] = $row['foto_portada'];
        }
        if (!empty($row['biografia'])) {
            $datos_reemplazo['biografia'] = $row['biografia'];
        }
    }
}

// Procesar CSS y HTML directamente desde la sesión
$css_ia = $_SESSION['css_ia'];
$css_ia = preg_replace('/```css|```|```json/', '', $css_ia);
$css_ia = trim($css_ia);

$html_ia = $_SESSION['html_ia'];
$html_ia = preg_replace('/```html|```|```json/', '', $html_ia);
$html_ia = trim($html_ia);

// FORZAR placeholders si la IA los eliminó
$placeholders = ['{foto_portada}', '{foto_perfil}', '{nombre_usuario}', '{biografia}', '{productos}'];
foreach ($placeholders as $placeholder) {
    if (strpos($html_ia, $placeholder) === false) {
        // Si falta algún placeholder, usar HTML base con placeholders
        $html_ia = '
        <!-- CONTENIDO BASE DEL PERFIL CON PLACEHOLDERS -->
        <div class="foto-portada">
            <img src="{foto_portada}" alt="Foto de portada" onerror="this.style.display=\'none\'">
        </div>

        <div class="cabecera">
            <div class="cont_img">
                <img src="{foto_perfil}" alt="foto perfil" onerror="this.src=\'./img/default.jpg\'">
            </div>
            <div class="datos_us">
                <div class="cont_name">
                    <h3>{nombre_usuario}</h3>
                </div>
                <div class="cont_descripcion">
                    <p>{biografia}</p>
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
                        <button type="button" onclick="window.location.href=\'./index.php?modulo=editar_perfil\'">
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
            {productos}
        </div>

        <script>
        function toggleMenu(event) {
            event.preventDefault();
            const menu = document.getElementById("menuConfig");
            menu.classList.toggle("mostrar");
            
            const todosMenus = document.querySelectorAll(".menu-desplegable");
            todosMenus.forEach(m => {
                if (m !== menu) {
                    m.classList.remove("mostrar");
                }
            });
        }

        document.addEventListener("click", function(event) {
            const menu = document.getElementById("menuConfig");
            const trigger = document.querySelector(".menu-trigger");
            
            if (menu && !menu.contains(event.target) && !trigger.contains(event.target)) {
                menu.classList.remove("mostrar");
            }
        });

        window.addEventListener("scroll", function() {
            const menu = document.getElementById("menuConfig");
            if (menu) {
                menu.classList.remove("mostrar");
            }
        });
        </script>';
        break;
    }
}

// Reemplazar placeholders con datos reales
foreach ($datos_reemplazo as $key => $value) {
    $html_ia = str_replace("{{$key}}", $value, $html_ia);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Previa - Tu Perfil</title>
    <style>
        /* CSS base del perfil */
        <?php 
        $estilos_base = file_get_contents(__DIR__ . '/../estilos/estilos_perfil.css');
        echo $estilos_base;
        ?>
        
        /* CSS personalizado por IA */
        <?php echo $css_ia; ?>
    </style>
</head>
<body>
    <div style="background:#ffeb3b; padding:10px; text-align:center; border-bottom:2px solid #ffc107;">
        <strong>👁️ VISTA PREVIA</strong> - Estos son cambios temporales. Aplica para guardar permanentemente.
    </div>

    <?php echo $html_ia; ?>

    <div style="text-align:center; padding:20px; margin:20px; background:#f8f9fa; border-radius:10px;">
        <h3>¿Te gustan los cambios?</h3>
        <p>Aplica los estilos para hacerlos permanentes en tu perfil.</p>
        
        <div style="margin-top:20px;">
            <a href="index.php?modulo=aplicar_estilos" style="background:#4CAF50; color:white; padding:12px 25px; text-decoration:none; border-radius:5px; margin-right:10px;">
                ✅ Aplicar a Mi Perfil
            </a>
            <a href="index.php?modulo=descartar_estilos" style="background:#f44336; color:white; padding:12px 25px; text-decoration:none; border-radius:5px;">
                ❌ Descartar Cambios
            </a>
        </div>
    </div>
</body>
</html>