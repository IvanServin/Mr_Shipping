<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['error'] = "No hay sesión activa";
    header('Location: index.php?modulo=asistente_perfil');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Aquí podrías agregar lógica para:
// - Eliminar la última plantilla creada
// - Revertir a la versión anterior
// Por ahora solo redirigimos

$_SESSION['mensaje_info'] = "Puedes generar nuevos estilos con el asistente IA";

header('Location: index.php?modulo=asistente_perfil');
exit;
?>