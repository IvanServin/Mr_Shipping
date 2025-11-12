<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

$token = $_GET['token'] ?? '';
$mensaje = '';

if ($token) {
    $conn = conectarBD();
    
    $sql = "SELECT v.id_usuario, u.correo, u.nombre_usuario 
            FROM verificaciones_cuenta v 
            JOIN usuarios u ON v.id_usuario = u.id_usuario 
            WHERE v.token_verificacion = ? AND v.fecha_verificacion IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // Marcar como verificado
        $sql_update = "UPDATE verificaciones_cuenta SET fecha_verificacion = NOW() WHERE token_verificacion = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('s', $token);
        $stmt_update->execute();
        
        // Actualizar usuario
        $sql_user = "UPDATE usuarios SET verificado = TRUE WHERE id_usuario = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param('i', $data['id_usuario']);
        $stmt_user->execute();
        
        $mensaje = "✅ ¡Cuenta verificada exitosamente! Bienvenido/a " . htmlspecialchars($data['nombre_usuario']) . ".";
        
        // Auto-login
        $_SESSION['id_usuario'] = $data['id_usuario'];
        $_SESSION['nombre_usuario'] = $data['nombre_usuario'];
        $_SESSION['cuenta_verificada'] = true;
        
        $tipo = 'exito';
    } else {
        $mensaje = "❌ Token de verificación inválido o ya utilizado.";
        $tipo = 'error';
    }
    
    $conn->close();
} else {
    $mensaje = "❌ No se proporcionó token de verificación.";
    $tipo = 'error';
}
?>

<div class="main-content">
    <div class="verificacion-container" style="text-align: center; padding: 40px 20px;">
        <div style="max-width: 500px; margin: 0 auto;">
            <h2>Verificación de Cuenta</h2>
            <div style="font-size: 4em; margin: 20px 0; color: <?= $tipo === 'exito' ? '#28a745' : '#dc3545' ?>;">
                <?= $tipo === 'exito' ? '✅' : '❌' ?>
            </div>
            <p style="font-size: 1.2em; margin-bottom: 30px;"><?= $mensaje ?></p>
            
            <?php if ($tipo === 'exito'): ?>
                <a href="index.php?modulo=explorar" class="btn" style="padding: 12px 30px; font-size: 1.1em;">
                    Comenzar a Chatear
                </a>
            <?php else: ?>
                <a href="index.php?modulo=login" class="btn" style="padding: 12px 30px; font-size: 1.1em;">
                    Volver al Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
