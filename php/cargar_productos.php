<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$id_usuario = $_SESSION['id_usuario'];
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_producto'];
    $precio = $_POST['precio_producto'];
    $estado =  $_POST['estado'];
    $estado_venta = 'disponible';
    $stock = $_POST['stock'];
    $descripcion = $_POST['descripcion_producto'];
    $usuarioid = $id_usuario;
    $categoria =  $_POST['categoria'];

    // Procesar las imágenes
    $imagenes = $_FILES['img_producto'];

    // Definir los tipos permitidos y el tamaño máximo
    $allowed_types = ['image/jpeg', 'image/png','image/webp'];
    $max_size = 2 * 1024 * 1024; // 2 MB
    $imagenes_guardadas = [];  // Array para guardar las rutas de las imágenes

    // Validar y mover las imágenes
    for ($i = 0; $i < count($imagenes['name']); $i++) {
        // Verificar si se subió un archivo en esta posición
        if (empty($imagenes['name'][$i]) || $imagenes['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

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
        $carpeta_destino = './img/';
        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0755, true);
        }
        
        $nombre_archivo = time() . "_{$i}_" . uniqid() . '_' . basename($imagenes['name'][$i]);
        $ruta_final = $carpeta_destino . $nombre_archivo;

        if (move_uploaded_file($imagenes['tmp_name'][$i], $ruta_final)) {
            echo "Imagen {$imagenes['name'][$i]} guardada con éxito en: $ruta_final<br>";
            $imagenes_guardadas[] = './img/' . $nombre_archivo;
        } else {
            echo "Error al guardar {$imagenes['name'][$i]}.<br>";
        }
    }

    // Verificar que se hayan subido imágenes
    if (empty($imagenes_guardadas)) {
        echo "Error: No se pudieron subir las imágenes. Verifica los archivos.<br>";
        exit;
    }

    // Convertir las rutas de las imágenes en una cadena separada por comas
    $imagenes_rutas = implode(',', $imagenes_guardadas);

    // DEBUG: Mostrar los valores que vamos a insertar
    echo "<br>DEBUG - Valores a insertar:<br>";
    echo "Nombre: $nombre<br>";
    echo "Precio: $precio<br>";
    echo "Estado: $estado<br>";
    echo "Estado venta: $estado_venta<br>";
    echo "Stock: $stock<br>";
    echo "Descripción: $descripcion<br>";
    echo "Imágenes: $imagenes_rutas<br>";
    echo "Usuario ID: $usuarioid<br>";
    echo "Categoría: $categoria<br>";

    // Insertar el producto en la base de datos
    $sql = "INSERT INTO productos (nombre_producto, precio, estado_condicion, estado_venta, stock, descripcion, fecha_publicacion, dir_img, id_usuario, id_categoria) 
            VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?)";
    
    echo "SQL: $sql<br>";
    echo "Número de parámetros: 10<br>";
    
    $stmt = $con->prepare($sql);
    
    if ($stmt) {
        // CORREGIDO: 9 parámetros exactos
        // s = string, d = double, i = integer
        $stmt->bind_param("sdssissii", 
            $nombre,           // s - string
            $precio,           // d - double
            $estado,           // s - string
            $estado_venta,     // s - string   
            $stock,            // s - string (aunque sea número, lo tratamos como string)
            $descripcion,      // s - string
            $imagenes_rutas,   // s - string
            $usuarioid,        // i - integer
            $categoria         // i - integer
        );

        if ($stmt->execute()) {
            echo "<br><strong>¡Producto guardado exitosamente!</strong><br>";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php?modulo=perfil';
                }, 3000);
            </script>";
        } else {
            echo "Error al ejecutar: " . $stmt->error . "<br>";
        }
        $stmt->close();
    } else {
        echo "Error en la preparación: " . $con->error . "<br>";
    }
}
?>

<link rel="stylesheet" href="../estilos/estilos_pd.css">
<form class="form-1" action="index.php?modulo=cargar_productos" method="POST" enctype="multipart/form-data">
    <label for="categoria">Seleccione la categoría de su producto</label>
    <select name='categoria' id='categoria' required>
        <option value="">Seleccione una categoría</option>
        <?php
        $sql = "SELECT id_categoria, nombre_categoria FROM categorias";
        $categorias = $con->query($sql);
        if ($categorias->num_rows > 0) {
            while ($fila = $categorias->fetch_assoc()) {
                echo "<option value='" . $fila['id_categoria'] . "'>" . htmlspecialchars($fila['nombre_categoria']) . "</option>";
            }
        } else {
            echo "<option value=''>No hay categorías disponibles</option>";
        }
        ?>
    </select>

    <label for="nombre_producto">Nombre del producto</label>
    <input type="text" name="nombre_producto" id="nombre_producto" required>

    <label for="precio">Precio de su producto</label>
    <input type="number" name="precio_producto" id="precio" step="0.01" min="0" required>

    <label for="stock">¿Cuántas unidades posee? (stock)</label>
    <input type="number" name="stock" id="stock" min="1" required>

    <label for="estado">Estado del producto</label>
    <select name="estado" id="estado" required>
        <option value="">Seleccione el estado del producto</option>
        <option value="nuevo">Nuevo</option>
        <option value="usado">Usado</option>
        <option value="reacondicionado">Reacondicionado</option>
    </select>

    <label for="descripcion_producto">Agregue detalles del producto</label>
    <textarea name="descripcion_producto" id="descripcion_producto" required></textarea>

    <label for="img_producto">Imágenes del producto (múltiples)</label>
    <input type="file" name="img_producto[]" id="img_producto" accept="image/*" multiple required>

    <button class="continuar-btn" type="submit">Continuar</button>
</form>