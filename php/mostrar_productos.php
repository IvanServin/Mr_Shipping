<?php
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {
        $busqueda = $_GET['busqueda'];
        $sql = "SELECT * FROM productos WHERE nombre_producto LIKE ?";
        $stmt = $con->prepare($sql);
        $busqueda = "%" . $busqueda . "%"; 
        $stmt->bind_param("s", $busqueda);
    } else {
        $sql = "SELECT * FROM productos";
        $stmt = $con->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<div class="cont_productos">'; 
        while ($producto = mysqli_fetch_assoc($result)) {
            $imagenes = explode(',', $producto['dir_img']); 
            $imagen_destino = !empty($imagenes[0]) ? $imagenes[0] : 'default.jpg';
            echo "
            <div class='tarjeta'>
                <img src='img/{$imagen_destino}' alt='{$producto['nombre_producto']}'>
                <div class='contenido'>
                    <h3>{$producto['nombre_producto']}</h3>
                    <span class='precio'>$ {$producto['precio']}</span><br>
                    <a href='#' class='btn'>Comprar ahora</a>
                </div>
            </div>
            ";
        }
        echo '</div>'; 
    } else {
        echo "<p>No se encontraron resultados disponibles en este momento.</p>";
    }
}
?>