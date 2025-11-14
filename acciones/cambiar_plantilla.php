<?php
session_start();
include_once '../conexion/conexion.php';
include_once '../includes/funciones_estilos.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_estilo'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $id_estilo = intval($_POST['id_estilo']);
    
    $con = conectar();
    
    // ✅ Esta función ahora actualiza la tabla 'perfil'
    if (activarPlantilla($id_usuario, $id_estilo)) {
        echo json_encode(['success' => true, 'message' => 'Plantilla activada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al activar la plantilla']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
}
?>