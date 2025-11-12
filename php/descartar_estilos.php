<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['css_ia']);
$_SESSION['mensaje_info'] = "Cambios descartados. Puedes generar una nueva vista previa.";

header('Location: index.php?modulo=asistente_perfil');
exit;
?>