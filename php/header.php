<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();
?>
<header>
    <div class="logo">
        <img src="./img/file (1).png" alt="logo">
    </div>
    <div class="buscador">
        <input type="text" name="busqueda" placeholder="Buscar producto">
    </div>
    
    <nav>
        <ul>
            <?php if (isset($_SESSION['id_usuario'])) {
                echo "<li><a href='index.php'>Inicio</a></li>";
                echo "<li><a href='index.php?modulo=perfil'>perfil</a></li>";
                echo "<li><a href='index.php?modulo=cargar_productos'>Vender</a></li>";
            } else {
                echo "<li><a href='index.php'>Inicio</a></li>";
                echo "<li><a href='index.php?modulo=inicio_session'>Iniciar session</a></li>";
                echo "<li><a href='index.php?modulo=registrar_us'>Registrarme</a></li>";
            }
            ?>
        </ul>
    </nav>
</header>