<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();
// Verificar si la sesión está activa y obtener el id_usuario desde la sesión
if (!isset($_SESSION['id_usuario'])) {
  // Si no hay sesión activa, mostrar un mensaje en vez de mostrar productos
  $mensaje_sesion = "Inicia sesión para ver o cargar productos.";
} else {
  $id_usuario = $_SESSION['id_usuario']; // Obtener el id del usuario desde la sesion
}
?>
<?php
//carga de visitantes 
function getperfil($idperfil){
  global $con;
  $stmt = $con->prepare ("SELECT * FROM perfil WHERE id_perfil = ?");
  $stmt->excute([$idperfil]);
  return $stmt->fech(PDO::FETCH_ASSOC);
}

if(isset($_GET['id_perfil'])){
  $idperfil = $_GET['id_perfil'];
  $perfil = getperfil($idperfil);
}else{
  $idperfil = $_SESSION['id_perfil'];
  $perfil = getperfil($idperfil);
}
?>



<div class="cabecera"><!-- contenedor para la cabecera del perfil -->
  <div class="cont_img"><!-- contenedor de la imagen perfil -->
    <?php
     $sql = $con->prepare("SELECT foto_perfil FROM perfil WHERE id_perfil = ?" ); 
    ?>
  </div>
  <div class="cont_name">
    <?php
    if (isset($_SESSION['nombre_us'])) {
      echo "<h3>{$_SESSION['nombre_us']}</h3>";
    } else {
      echo "<h3>User</h3>";
    }
    ?>
  </div>
  <div class="cont_descripcion">
    <?php
    if (isset($_SESSION['descripcion'])) {
      echo "<p>" . nl2br(htmlspecialchars($_SESSION['descripcion'], ENT_QUOTES, 'UTF-8')) . "</p>";
    } else {
      echo "<p>Agrega info para que la gente sepa más de ti</p>";
    }
    ?>
  </div>
</div>

<nav class="cont_nav_perfil">
  <ul>
    <li><a href="index.php?modulo=perfil.php">Mi Perfil</a></li>
    <li><a href="index.php?modulo=cargar_productos">Publicar producto</a></li>
    <li><a href="./index.php">Inicio</a></li>
    <div class="box-confi">
      <li><a href="#"><i class="fa-solid fa-gear"></i></a></li>
      <div class="menu">
        <button type="button" onclick="window.location.href='./index.php?modulo=editar_perfil'">editar perfil</button>
        <!-- Botón de cerrar sesión -->
        <form action="php/perfil.php" method="POST" style="display: inline;">
          <button type="submit" class="btn-cerrar-sesion">Cerrar Sesión</button>
        </form>
      </div>


    </div>

  </ul>
</nav>

<div class="cont_productos">
  <?php
  if (isset($mensaje_sesion)) {
    // Si no está logueado, mostrar el mensaje
    echo "<p>{$mensaje_sesion}</p>";
  } else {
    // Recupera los productos del usuario logueado
    $query = "SELECT p.* FROM productos p
                INNER JOIN productos_usuarios pu ON p.id_producto = pu.id_producto
                WHERE pu.id_usuario = ?";

    $stmt = $con->prepare($query); // Prepara la consulta
    if ($stmt) {
      $stmt->bind_param("i", $id_usuario); // Bind el parámetro del usuario
      $stmt->execute();
      $result = $stmt->get_result(); // Ejecutar y obtener el resultado

      if ($result && mysqli_num_rows($result) > 0) {
        while ($producto = $result->fetch_assoc()) {
          // Recupera las rutas de las imágenes (esto está en la columna 'dir_img')
          $imagenes = explode(',', $producto['dir_img']); // Divide las rutas por comas

          // Toma la primera imagen para mostrar en la tarjeta
          $imagen_destino = !empty($imagenes[0]) ? $imagenes[0] : 'default.jpg'; // Si no hay imágenes, muestra una por defecto

          echo "
                    <div class='tarjeta'>
                        <img src='../img/{$imagen_destino}' alt='{$producto['nombre_producto']}'>
                        <div class='contenido'>
                            <h3>{$producto['nombre_producto']}</h3>
                            <p>{$producto['descripcion']}</p>
                            <span class='precio'>$ {$producto['precio']}</span>
                            <a href='#' class='btn'>Comprar ahora</a>
                        </div>
                    </div>
                    ";
        }
      } else {
        echo "<h3>No tienes productos disponibles.</h3>";
      }
      $stmt->close();
    } else {
      echo "<h3>Error al obtener los productos.</h3>";
    }
  }
  ?>
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    session_unset(); // Limpia todas las variables de sesión
    session_destroy(); // Destruye la sesión
    header('Location:/programas/TuShop/index.php'); // Redirige al login u otra página
    exit;
  } 
  ?>

</div>