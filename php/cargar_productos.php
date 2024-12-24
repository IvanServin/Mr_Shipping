<?php
include_once __DIR__.'/../conxion/conexion.php';
conectar();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria =  $_POST['categoria'];
    $nombre = $_POST['nombre_producto'];
    $precio = $_POST['precio_producto'];
    $estado =  $_POST['estado'];
    $descripcion = $_POST['descripcion_producto'];

    // Procesar las imágenes
    $imagenes = $_FILES['img_producto'];
    
    // Definir los tipos permitidos y el tamaño máximo
    $allowed_types = ['image/jpeg', 'image/png'];
    $max_size = 2 * 1024 * 1024; // 2 MB
    $imagenes_guardadas = [];  // Array para guardar las rutas de las imágenes

    // Validar y mover las imágenes
    for ($i = 0; $i < count($imagenes['name']); $i++) {
        $tipo = $imagenes['type'][$i];
        $size = $imagenes['size'][$i];

        // Validar tipo de imagen
        if (!in_array($tipo, $allowed_types)) {
            echo "Error: {$imagenes['name'][$i]} no es un formato válido.<br>";
            continue;
        }

        // Validar tamaño de la imagen
        if ($size > $max_size) {
            echo "Error: {$imagenes['name'][$i]} excede el tamaño máximo permitido.<br>";
            continue;
        }

        // Mover la imagen al servidor
        $carpeta_destino = 'img/';
        $nombre_archivo = time() . "_{$i}_" . basename($imagenes['name'][$i]);
        $ruta_final = $carpeta_destino . $nombre_archivo;

        if (move_uploaded_file($imagenes['tmp_name'][$i], $ruta_final)) {
            echo "Imagen {$imagenes['name'][$i]} guardada con éxito.<br>";
            $imagenes_guardadas[] = $nombre_archivo;  // Guardar el nombre de la imagen
        } else {
            echo "Error al guardar {$imagenes['name'][$i]}.<br>";
        }
    }

    // Convertir las rutas de las imágenes en una cadena separada por comas
    $imagenes_rutas = implode(',', $imagenes_guardadas);

    // Insertar el producto en la base de datos
    $sql = "INSERT INTO productos (nombre, precio, estado, descripcion, dir_img, id_usuario) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $precio, $estado, $descripcion, $imagenes_rutas, $_SESSION['usuario_id']);
    
    if ($stmt->execute()) {
        echo "Producto y sus imágenes guardados con éxito.<br>";
    } else {
        echo "No se pudo guardar el producto.<br>";
    }
}
?>

<link rel="stylesheet" href="../estilos/estilos_pd.css">
<form class="form-1" action="index.php?modulo=cargar_productos" method="POST" enctype="multipart/form-data">
    <label for="categoria">Seleccione la categoría de su producto</label>
    <select name="categoria" id="categoria">
        <option value="">Seleccione una categoría</option>
        <option value="ropa">Ropa</option>
    </select>

    <label for="nombre_producto">Nombre del producto</label>
    <input type="text" name="nombre_producto" id="nombre_producto">

    <label for="precio">Precio de su producto</label>
    <input type="number" name="precio_producto" id="precio">

    <select name="estado" id="estado">
        <option value="">Seleccione el estado del producto</option>
        <option value="nuevo">Nuevo</option>
        <option value="usado">Usado</option>
    </select>

    <label for="descripcion_producto">Agregue detalles del producto</label>
    <input type="text" name="descripcion_producto" id="descripcion_producto">

    <label for="img_producto">Imágenes del producto</label>
    <input type="file" name="img_producto[]" id="img_producto" accept="image/*" multiple>

    <button class="continuar-btn" type="submit">Continuar</button>
</form>
