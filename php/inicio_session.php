<?php
if(session_status() == PHP_SESSION_NONE){
  session_start();
}
include_once __DIR__.'/../conexion/conexion.php';
$con = conectar();

if (isset($_SESSION['id_usuario'])) {
  // Redirige a la ruta correcta
  header('Location: ../index.php?modulo=perfil'); // Usamos la ruta relativa aquí
  exit;
} else {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_us = mysqli_real_escape_string($con, $_POST['nombre']);
    $pass = $_POST['pass'];

    $sql = "SELECT id_usuario, contrasena, nombre_usuario, descripcion, correo FROM usuarios WHERE nombre_usuario = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $nombre_us);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
      $contrasena_almacenada = $row['contrasena'];
      if (password_verify($pass, $contrasena_almacenada)) {
        // Inicio de sesión exitoso
        $_SESSION['id_usuario'] = $row['id_usuario']; // Asigna el ID del usuario a la sesión
        $_SESSION['nombre_us'] = $row['nombre_usuario'];
        $_SESSION['descripcion'] = $row['descripcion'];
        $_SESSION['correo'] = $row['correo'];
        $_SESSION['id_perfil'] = $row['id_perfil'];
        // Redirige a la ruta correcta
        header('Location: ../index.php?modulo=perfil'); // Usamos la ruta relativa aquí
        exit;
      } else {
        echo "Contraseña incorrecta"; // Muestra mensaje de error
      }
    } else {
      echo "Usuario no encontrado";
    }
  }
}
?>
<link rel="stylesheet" href="../estilos/estilos_pd.css">

<form class="form-1" action="php/inicio_session.php" method="POST">
  <label for="nombre">Nombre de usuario</label>
  <input type="text" id="nombre" name="nombre">
  <label for="pass">Contraseña</label>
  <input type="password" name="pass" id="pass">
  <button class="continuar-btn" type="submit">Continuar</button>
  <a href="../index.php?modulo=registrar_us">Registrarme</a>
</form>
