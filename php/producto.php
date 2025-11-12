<?php
// php/producto.php
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();

// Incluir el CSS de Mercado Libre
echo '<link rel="stylesheet" href="estilos/estilos_ml.css">';
echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">';

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    
    // JOINs para obtener nombres de marca y categoría
    $sql = "SELECT p.*, m.nombre_marca, c.nombre_categoria 
            FROM productos p 
            LEFT JOIN marcas m ON p.id_marca = m.id_marca 
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
            WHERE p.id_producto = ? AND p.estado_venta = 'disponible'";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($producto = $result->fetch_assoc()) {
        $imagenes_string = $producto['dir_img'] ?? ''; 
        $imagenes = !empty($imagenes_string) ? explode(',', $imagenes_string) : [];
        $imagen_principal = !empty($imagenes[0]) ? $imagenes[0] : './img/default.jpg';
        
        $marca = $producto['nombre_marca'] ?? 'Sin marca';
        $categoria = $producto['nombre_categoria'] ?? 'General';
        $descripcion = $producto['descripcion'] ?? 'Descripción no disponible.';
        $condicion = $producto['estado_condicion'] ?? 'nuevo';
        $stock = $producto['stock'] ?? 1;
        
        $texto_condicion = '';
        switch($condicion) {
            case 'nuevo':
                $texto_condicion = 'Nuevo';
                break;
            case 'usado':
                $texto_condicion = 'Usado';
                break;
            case 'reacondicionado':
                $texto_condicion = 'Reacondicionado';
                break;
            default:
                $texto_condicion = 'Nuevo';
        }
        
        echo "
        <div class='pagina-producto'>
            <!-- Ruta de navegación -->
            <div class='ruta-navegacion'>
                <a href='index.php'>Inicio</a> > <a href='index.php'>Productos</a> > <span>{$producto['nombre_producto']}</span>
            </div>
            
            <div class='producto-detalle'>
                <!-- Sección de imágenes -->
                <div class='galeria-producto'>
                    <img src='{$imagen_principal}' alt='{$producto['nombre_producto']}' class='imagen-principal' id='imagenPrincipal'>
                    
                    <div class='miniaturas'>";
        
        // Mostrar miniaturas si hay más de una imagen
        foreach($imagenes as $index => $imagen) {
            $clase_activa = $index === 0 ? 'activa' : '';
            echo "<img src='{$imagen}' alt='Miniatura {$index}' class='miniatura {$clase_activa}' onclick=\"cambiarImagen('{$imagen}', this)\">";
        }
        
        echo "
                    </div>
                </div>
                
                <!-- Sección de información -->
                <div class='info-producto'>
                    <div class='estado-producto'>
                        <i class='fas fa-check-circle'></i> {$texto_condicion} | Stock: {$stock}
                    </div>
                    
                    <h1 class='titulo-producto'>{$producto['nombre_producto']}</h1>
                    
                    <div class='precio-producto'>
                        <div class='precio-actual'>$ {$producto['precio']}</div>
                        <div class='precio-descuento'>en 12x $".number_format($producto['precio'] / 12, 2)."</div>
                    </div>
                    
                    <div class='envio-gratis'>
                        <i class='fas fa-truck'></i> Envío gratis
                    </div>
                    
                    <div class='stock-disponible'>
                        <i class='fas fa-box'></i> {$stock} unidades disponibles
                    </div>
                    
                    <!-- Selector de cantidad -->
                    <div class='selector-cantidad'>
                        <label for='cantidad'>Cantidad:</label>
                        <select id='cantidad'>";
        
        // Generar opciones de cantidad basado en el stock disponible
        $max_cantidad = min($stock, 10); // Máximo 10 o el stock disponible
        for ($i = 1; $i <= $max_cantidad; $i++) {
            echo "<option value='{$i}'>{$i} " . ($i == 1 ? 'unidad' : 'unidades') . "</option>";
        }
        
        echo "
                        </select>
                    </div>
                    
                    <!-- Botones de compra -->
                    <div class='botones-compra'>
                        <button class='btn-comprar-ahora' onclick='comprarAhora()'>
                            Comprar ahora
                        </button>
                        <button class='btn-agregar-carrito' onclick='agregarAlCarrito()'>
                            <i class='fas fa-shopping-cart'></i> Agregar al carrito
                        </button>
                    </div>
                    
                    <!-- Información de envío -->
                    <div class='info-envio'>
                        <div class='direccion-envio'>
                            <i class='fas fa-map-marker-alt'></i>
                            <div>
                                <div>Enviar a Capital Federal</div>
                                <button class='cambiar-direccion'>Cambiar dirección</button>
                            </div>
                        </div>
                        <div class='costo-envio'>Envío gratis a todo el país</div>
                        <div class='fecha-entrega'>Llega entre el <strong>lunes y martes</strong></div>
                    </div>
                    
                    <!-- Garantías -->
                    <div class='garantias'>
                        <div class='item-garantia'>
                            <i class='fas fa-shield-alt'></i>
                            <span class='texto-garantia'>Compra Protegida, recibí el producto que esperabas o te devolvemos tu dinero.</span>
                        </div>
                        <div class='item-garantia'>
                            <i class='fas fa-credit-card'></i>
                            <span class='texto-garantia'>Pago seguro</span>
                        </div>
                        <div class='item-garantia'>
                            <i class='fas fa-store'></i>
                            <span class='texto-garantia'>Garantía del vendedor</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Descripción y características -->
            <div class='seccion-descripcion'>
                <h2 class='titulo-seccion'>Descripción</h2>
                <div class='descripcion-texto'>{$descripcion}</div>
                
                <div class='caracteristicas'>
                    <div class='caracteristica'>
                        <span class='caracteristica-titulo'>Marca:</span>
                        <span class='caracteristica-valor'>{$marca}</span>
                    </div>
                    <div class='caracteristica'>
                        <span class='caracteristica-titulo'>Categoría:</span>
                        <span class='caracteristica-valor'>{$categoria}</span>
                    </div>
                    <div class='caracteristica'>
                        <span class='caracteristica-titulo'>Condición:</span>
                        <span class='caracteristica-valor'>{$texto_condicion}</span>
                    </div>
                    <div class='caracteristica'>
                        <span class='caracteristica-titulo'>Stock:</span>
                        <span class='caracteristica-valor'>{$stock} unidades</span>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        function cambiarImagen(src, elemento) {
            document.getElementById('imagenPrincipal').src = src;
            
            // Remover clase activa de todas las miniaturas
            document.querySelectorAll('.miniatura').forEach(miniatura => {
                miniatura.classList.remove('activa');
            });
            
            // Agregar clase activa a la miniatura clickeada
            elemento.classList.add('activa');
        }
        
        function comprarAhora() {
            const cantidad = document.getElementById('cantidad').value;
            // CORREGIDO: Usar concatenación en lugar de template literal
            window.location.href = 'index.php?modulo=comprar&producto=" . $producto['id_producto'] . "&cantidad=' + cantidad;
        }
        
        function agregarAlCarrito() {
            const cantidad = document.getElementById('cantidad').value;
            
            // Aquí tu lógica para agregar al carrito (puede ser AJAX)
            fetch('acciones/agregar_carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    producto_id: " . $producto['id_producto'] . ",
                    cantidad: cantidad
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // CORREGIDO: Usar concatenación
                    alert('Producto agregado al carrito: " . addslashes($producto['nombre_producto']) . "');
                } else {
                    alert('Error al agregar al carrito: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al agregar al carrito');
            });
        }
        </script>
        ";
    } else {
        echo "<div class='pagina-producto'><p>Producto no encontrado o no disponible.</p></div>";
    }
} else {
    echo "<div class='pagina-producto'><p>No se especificó un producto.</p></div>";
}

$con->close();
?>