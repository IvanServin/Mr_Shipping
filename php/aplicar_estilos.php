<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['css_ia'])) {
    $_SESSION['error'] = "No hay estilos para aplicar.";
    header('Location: index.php?modulo=asistente_perfil');
    exit;
}

include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();

// Limpiar el CSS de la IA
$css_ia = $_SESSION['css_ia'];
$css_ia = preg_replace('/```css|```/', '', $css_ia);
$css_ia = trim($css_ia);

// Generar nombre único para el CSS usando el ID del usuario
$id_usuario = $_SESSION['id_usuario'];
$nombre_archivo = 'estilos_perfil_' . $id_usuario . '.css';
$ruta_relativa = 'estilos/' . $nombre_archivo; // Ruta relativa para la BD
$ruta_absoluta = __DIR__ . '/../' . $ruta_relativa; // Ruta absoluta para guardar el archivo

// DEBUG: Verificar permisos de carpeta
error_log("Ruta CSS absoluta: " . $ruta_absoluta);
error_log("Ruta CSS relativa: " . $ruta_relativa);
error_log("Carpeta escribible: " . (is_writable(dirname($ruta_absoluta)) ? 'SI' : 'NO'));

// Crear directorio estilos si no existe
$directorio_estilos = __DIR__ . '/../estilos/';
if (!is_dir($directorio_estilos)) {
    mkdir($directorio_estilos, 0755, true);
    error_log("Directorio estilos creado");
}

// Guardar archivo CSS
if (file_put_contents($ruta_absoluta, $css_ia)) {
    error_log("Archivo CSS creado exitosamente en: " . $ruta_absoluta);
    
    // Actualizar BD - Primero verificar si existe el perfil
    $query_check = "SELECT id_perfil FROM perfil WHERE id_usuario = ?";
    $stmt_check = $con->prepare($query_check);
    $stmt_check->bind_param("i", $id_usuario);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        // Actualizar perfil existente
        $query = "UPDATE perfil SET css_personalizado = ? WHERE id_usuario = ?";
        error_log("Actualizando perfil existente para usuario: " . $id_usuario);
    } else {
        // Crear nuevo perfil
        $query = "INSERT INTO perfil (css_personalizado, id_usuario) VALUES (?, ?)";
        error_log("Creando nuevo perfil para usuario: " . $id_usuario);
    }
    
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $ruta_relativa, $id_usuario);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "¡Estilos aplicados exitosamente a tu perfil!";
        unset($_SESSION['css_ia']);
        error_log("CSS guardado en BD correctamente: " . $ruta_relativa);
    } else {
        $_SESSION['error'] = "Error al guardar en la base de datos: " . $con->error;
        error_log("Error BD: " . $con->error);
        // Borrar archivo CSS si falla la BD
        if (file_exists($ruta_absoluta)) {
            unlink($ruta_absoluta);
        }
    }
} else {
    $_SESSION['error'] = "Error al crear el archivo CSS. Verifica permisos de la carpeta 'estilos'.";
    error_log("Error al crear archivo CSS en: " . $ruta_absoluta);
}

header('Location: index.php?modulo=perfil');
exit;
?>