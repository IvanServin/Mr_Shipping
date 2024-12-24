<?php
include_once __DIR__.'/../conexion/conexion.php';
$con = conectar();
// Recuperar todos los productos
$query = "SELECT * FROM productos";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Si hay productos, mostrarlos en tarjetas dentro del contenedor
    echo '<div class="cont_productos">'; // Contenedor para los productos
    while ($producto = mysqli_fetch_assoc($result)) {
        // Recuperar las rutas de las imágenes (esto está en la columna 'dir_img')
        $imagenes = explode(',', $producto['dir_img']); // Divide las rutas por comas
        // Tomar la primera imagen para mostrar en la tarjeta
        $imagen_destino = !empty($imagenes[0]) ? $imagenes[0] : 'default.jpg'; // Si no hay imágenes, muestra una por defecto

        // Mostrar cada producto en una tarjeta
        echo "
        <div class='tarjeta'>
            <img src='img/{$imagen_destino}' alt='{$producto['nombre_producto']}'>
            <div class='contenido'>
                <h3>{$producto['nombre_producto']}</h3>
                <p class='descripcion'>{$producto['descripcion']}</p>
                <span class='precio'>$ {$producto['precio']}</span>
                <a href='#' class='btn'>Comprar ahora</a>
            </div>
        </div>
        ";
    }
    echo '</div>'; // Cerrar contenedor
} else {
    echo "<p>No hay productos disponibles en este momento.</p>";
}
?>
