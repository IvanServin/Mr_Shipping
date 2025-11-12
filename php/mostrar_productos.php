<?php
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();


$busqueda = '';

if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {
    $busqueda = trim($_GET['busqueda']);
}

if (!empty($busqueda)) {
    // Si hay búsqueda, filtrar productos
    $sql = "SELECT * FROM productos WHERE nombre_producto LIKE ? AND estado_venta = 'disponible'";
    $stmt = $con->prepare($sql);
    $param_busqueda = "%" . $busqueda . "%"; 
    $stmt->bind_param("s", $param_busqueda);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Si NO hay búsqueda, mostrar todos los productos activos
    $sql = "SELECT * FROM productos WHERE estado_venta = 'disponible'";
    $result = $con->query($sql);
}

if ($result && $result->num_rows > 0) {
    echo '<div class="cont_productos">'; 
    while ($producto = $result->fetch_assoc()) {
        $imagenes_string = $producto['dir_img']?? ''; 
        $imagenes = !empty($imagenes_string) ? explode(',', $imagenes_string) : [];
        
        $imagen_destino = !empty($imagenes[0]) ? 
            (strpos($imagenes[0], './') === 0 ? $imagenes[0] : './' . $imagenes[0]) : 
            './img/default.jpg';
        
        // Agregar data-id para identificar el producto
        echo "
        <div class='tarjeta' data-product-id='{$producto['id_producto']}'>
            <div class='img-container'>
                <img src='{$imagen_destino}' alt='{$producto['nombre_producto']}' onerra
                r=\"this.src='./img/default.jpg'\">
            </div>
            <div class='contenido'>
                <h3>{$producto['nombre_producto']}</h3>
                <span class='precio'>$ {$producto['precio']}</span>
            </div>
        </div>
        ";
    }
    echo '</div>'; 
} else {
    echo "<p>No se encontraron productos disponibles en este momento.</p>";
}

// Cerrar conexión si es necesario
$con->close();
?>