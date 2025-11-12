<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['css_ia'])) {
    header('Location: index.php?modulo=asistente_perfil');
    exit;
}

// Obtener datos del usuario para mostrar imagen real
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();
$foto_perfil = 'default.jpg';
$nombre_usuario = $_SESSION['nombre_us'] ?? 'Usuario';
$descripcion = $_SESSION['descripcion'] ?? 'Descripción del usuario';

if (isset($_SESSION['id_usuario'])) {
    $query = "SELECT foto_perfil FROM perfil WHERE id_usuario = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $_SESSION['id_usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc() && !empty($row['foto_perfil'])) {
        $foto_perfil = $row['foto_perfil'];
    }
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
        // Cargar estilos base
        $estilos_base = file_get_contents(__DIR__ . '/../estilos/estilos_perfil.css');
        echo $estilos_base;
        ?>
        
        /* CSS personalizado por IA */
        <?php 
        $css_ia = $_SESSION['css_ia'];
        // Limpiar markdown si existe
        $css_ia = preg_replace('/```css|```/', '', $css_ia);
        $css_ia = trim($css_ia);
        
        // Si la IA devolvió CSS completo, usar solo eso
        if (strpos($css_ia, '.cabecera') !== false && strpos($css_ia, '.cont_nav_perfil') !== false) {
            // La IA devolvió CSS completo, usar solo este
            echo $css_ia;
        } else {
            // La IA devolvió solo modificaciones, combinar con base
            echo $css_ia;
        }
        ?>
    </style>
</head>
<body>
    <div style="background:#ffeb3b; padding:10px; text-align:center; border-bottom:2px solid #ffc107;">
        <strong>👁️ VISTA PREVIA</strong> - Estos son cambios temporales. Aplica para guardar permanentemente.
    </div>

    <!-- Copia del contenido de perfil.php -->
    <div class="cabecera">
        <div class="cont_img">
            <img src='img/<?php echo $foto_perfil; ?>' alt='foto perfil'>
        </div>
        <div class="datos_us">
            <div class="cont_name">
                <h3><?php echo $nombre_usuario; ?></h3>
            </div>
            <div class="cont_descripcion">
                <p><?php echo $descripcion; ?></p>
            </div>
        </div>
    </div>

    <nav class="cont_nav_perfil">
        <ul>
            <li><a href="index.php?modulo=perfil">Mi Perfil</a></li>
            <li><a href="index.php?modulo=cargar_productos">Publicar producto</a></li>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="index.php?modulo=asistente_perfil">Asistente IA</a></li>
        </ul>
    </nav>

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