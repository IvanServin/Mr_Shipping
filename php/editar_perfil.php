<?php
include_once __DIR__. '/../conexion/conexion.php';
$con = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = $_POST['descripcion'];

    // Limpia el texto para evitar inyección de código
    $descripcion = htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8');

    // Actualiza la base de datos
    $sql = "UPDATE usuarios SET descripcion = ? WHERE id_usuario = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $descripcion, $_SESSION['id_usuario']);
    $stmt->execute();
}
?>

<link rel="stylesheet" href="../estilos/estilos_pd.css">
<form class="form-1" action="./index.php?modulo=editar_perfil" method="POST">
    <label for="descripcion">Editar descripción</label>
    <textarea name="descripcion" id="descripcion"><?php echo htmlspecialchars($_SESSION['descripcion'], ENT_QUOTES, 'UTF-8'); ?></textarea>
    <button class="continuar-btn" type="submit">Guardar</button>
</form>
