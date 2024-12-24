
<?php 
include_once __DIR__. '/../conexion/conexion.php';
$con = conectar();
if ($_SERVER["REQUEST_METHOD"] =="POST"){

    $correo = mysqli_real_escape_string($con, $_POST['correo']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($con, $_POST['apellido']);
    $edad = mysqli_real_escape_string($con,$_POST['edad']);
    $contrasena = password_hash($_POST['contrasena'],PASSWORD_DEFAULT);
    $nombre_usuario = mysqli_real_escape_string($con,$_POST['nombre_usuario']);

    $sql = "INSERT INTO usuarios(correo,nombre,apellido,edad,contrasena,nombre_usuario)
    VALUES(?,?,?,?,?,?)";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssss",$correo,$nombre,$apellido,$edad,$contrasena,$nombre_usuario);

    if($stmt->execute()){
        echo"Registrado correctamente";
    }else{
        echo"Error no se pudo registrar: ".$stmt->error;
    }

    $stmt->close();

}
?>

<form class="form-1"action="php/registrar_us.php" method="POST">
    <label for="correo">Correo electrónico:</label>
    <input type="email" id="correo" name="correo" required>

    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required>

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" required>

    <label for="edad">Edad:</label>
    <input type="number" id="edad" name="edad" required>

    <label for="contrasena">Contraseña:</label>
    <input type="password" id="contrasena" name="contrasena" required>

    <label for="nombre_usuario">Nombre de usuario:</label>
    <input type="text" id="nombre_usuario" name="nombre_usuario" required>

    <button class="continuar-btn" type="submit">Registrarse</button>
</form>

