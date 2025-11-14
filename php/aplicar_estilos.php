<?php
// aplicar_estilos.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['css_ia']) || !isset($_SESSION['html_ia'])) {
    $_SESSION['error'] = "No hay estilos o estructura para aplicar.";
    header('Location: index.php?modulo=asistente_perfil');
    exit;
}

include_once __DIR__ . '/../conexion/conexion.php';
include_once __DIR__ . '/../includes/funciones_estilos.php';
$con = conectar();

// DEBUG CRÍTICO
error_log("🎯 aplicar_estilos.php INICIADO");
error_log("📏 CSS en sesión: " . strlen($_SESSION['css_ia']));
error_log("📏 HTML en sesión: " . strlen($_SESSION['html_ia']));

// Limpiar el CSS y HTML de la IA
$css_ia = $_SESSION['css_ia'];
$html_ia = $_SESSION['html_ia'];

$css_ia = preg_replace('/```css|```|```json/', '', $css_ia);
$css_ia = trim($css_ia);

$html_ia = preg_replace('/```html|```|```json/', '', $html_ia);
$html_ia = trim($html_ia);

$id_usuario = $_SESSION['id_usuario'];

// ✅ PASO 1: PROCESAR CON generarHTMLDinamico PRIMERO
error_log("🚨 LLAMANDO A generarHTMLDinamico");
$html_dinamico = generarHTMLDinamico($html_ia, $id_usuario);
error_log("✅ generarHTMLDinamico COMPLETADO");

// ✅ PASO 2: Generar nombres únicos para los archivos
$timestamp = time();
$nombre_archivo_css = 'estilos_perfil_' . $id_usuario . '_' . $timestamp . '.css';
$ruta_relativa_css = 'estilos/' . $nombre_archivo_css;
$ruta_absoluta_css = __DIR__ . '/../' . $ruta_relativa_css;

$nombre_archivo_html = 'perfil_html_' . $id_usuario . '_' . $timestamp . '.php';
$ruta_relativa_html = 'html_personalizado/' . $nombre_archivo_html;
$ruta_absoluta_html = __DIR__ . '/../' . $ruta_relativa_html;

// ✅ PASO 3: Crear directorios si no existen
$directorio_estilos = __DIR__ . '/../estilos/';
$directorio_html = __DIR__ . '/../html_personalizado/';

if (!is_dir($directorio_estilos)) mkdir($directorio_estilos, 0755, true);
if (!is_dir($directorio_html)) mkdir($directorio_html, 0755, true);

// ✅ PASO 4: Guardar archivos
if (file_put_contents($ruta_absoluta_css, $css_ia)) {
    error_log("✅ Archivo CSS guardado: " . $ruta_relativa_css);
    
    if (file_put_contents($ruta_absoluta_html, $html_dinamico)) {
        error_log("✅ Archivo HTML dinámico guardado: " . $ruta_relativa_html);
        
        // ✅ PASO 5: Guardar en estilos_perfil (esto activará automáticamente)
        $nombre_estilo = "Estilo IA v" . date('Y-m-d H:i');
        $descripcion = "Generado por IA: " . ($_SESSION['instruccion_ia'] ?? 'Personalización automática');
        
        $nueva_version = guardarNuevaVersionEstilo(
            $id_usuario,
            $css_ia, // CSS original
            $html_dinamico, // HTML procesado dinámicamente
            $nombre_estilo,
            $descripcion
        );
        
        if ($nueva_version) {
            $_SESSION['mensaje_exito'] = "¡Nueva plantilla creada y activada (v{$nueva_version})!";
            error_log("🎉 PLANTILLA CREADA EXITOSAMENTE - Versión: {$nueva_version}");
            
            // Limpiar sesión
            unset($_SESSION['css_ia']);
            unset($_SESSION['html_ia']); 
            unset($_SESSION['instruccion_ia']);
        } else {
            $_SESSION['error'] = "Error al guardar la plantilla en la base de datos.";
            error_log("❌ Error al guardar en estilos_perfil");
        }
        
    } else {
        $_SESSION['error'] = "Error al crear el archivo HTML.";
        error_log("❌ Error al crear archivo HTML");
        if (file_exists($ruta_absoluta_css)) unlink($ruta_absoluta_css);
    }
} else {
    $_SESSION['error'] = "Error al crear el archivo CSS.";
    error_log("❌ Error al crear archivo CSS");
}

header('Location: index.php?modulo=perfil');
exit;

// ✅ FUNCIÓN generarHTMLDinamico 
function generarHTMLDinamico($html_ia, $id_usuario) {
    error_log("🚨 INICIANDO generarHTMLDinamico");
    error_log("📏 HTML recibido: " . strlen($html_ia) . " caracteres");
    
    // VERIFICACIÓN DE PLACEHOLDERS
    $placeholders = ['{foto_portada}', '{foto_perfil}', '{nombre_usuario}', '{biografia}', '{productos}'];
    foreach ($placeholders as $placeholder) {
        $count = substr_count($html_ia, $placeholder);
        error_log("🔍 $placeholder: encontrado $count veces");
    }
    
    // Crear un archivo PHP que cargue los datos dinámicamente
    $php_code = '<?php
// Cargar datos del perfil dinámicamente
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mostrar mensajes
if (isset($_SESSION[\'mensaje_exito\'])) {
    echo "<div style=\'background:#d4edda; color:#155724; padding:10px; margin:10px; border-radius:5px; text-align:center;\'>".$_SESSION[\'mensaje_exito\']."</div>";
    unset($_SESSION[\'mensaje_exito\']);
}

if (isset($_SESSION[\'error\'])) {
    echo "<div style=\'background:#f8d7da; color:#721c24; padding:10px; margin:10px; border-radius:5px; text-align:center;\'>".$_SESSION[\'error\']."</div>";
    unset($_SESSION[\'error\']);
}

// ✅ CORRECCIÓN: Incluir archivos necesarios con rutas absolutas
$base_dir = dirname(__DIR__);
include_once $base_dir . \'/conexion/conexion.php\';
include_once $base_dir . \'/includes/funciones_estilos.php\';

$con = conectar();

// Verificar si la sesión está activa
if (!isset($_SESSION[\'id_usuario\'])) {
    $mensaje_sesion = "Inicia sesión para ver o cargar productos.";
} else {
    $id_usuario = $_SESSION[\'id_usuario\'];
    
    $query_perfil = "SELECT * FROM perfil WHERE id_usuario = ?";
    $stmt_perfil = $con->prepare($query_perfil);
    $stmt_perfil->bind_param("i", $id_usuario);
    $stmt_perfil->execute();
    $result_perfil = $stmt_perfil->get_result();
    
    if ($result_perfil && $result_perfil->num_rows > 0) {
        $perfil = $result_perfil->fetch_assoc();
        $rt_foto_perfil = !empty($perfil[\'foto_perfil\']) ? $perfil[\'foto_perfil\'] : \'./img/default.jpg\';
        $foto_portada = !empty($perfil[\'foto_portada\']) ? $perfil[\'foto_portada\'] : null;
        $biografia = !empty($perfil[\'biografia\']) ? $perfil[\'biografia\'] : \'Agrega info para que la gente sepa más de ti\';
    } else {
        $rt_foto_perfil = \'./img/default.jpg\';
        $foto_portada = null;
        $biografia = \'Agrega info para que la gente sepa más de ti\';
    }
    $stmt_perfil->close();
}

// Procesar cierre de sesión
if ($_SERVER[\'REQUEST_METHOD\'] === \'POST\') {
    session_start();
    session_unset();
    session_destroy();
    header(\'Location: index.php\');
    exit;
}

// Generar HTML de productos
$productos_html = \'\';
if (isset($mensaje_sesion)) {
    $productos_html = "<p style=\'text-align:center; padding:2rem; color:#666;\'>".$mensaje_sesion."</p>";
} else {
    $query = "SELECT * FROM productos WHERE id_usuario = ? AND estado_venta = \'disponible\'";
    $stmt = $con->prepare($query); 
    if ($stmt) {
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) > 0) {
            while ($producto = $result->fetch_assoc()) {
                $imagenes_string = $producto[\'dir_img\'] ?? \'\';
                $imagenes = !empty($imagenes_string) ? explode(\',\', $imagenes_string) : [];
                
                $imagen_destino = !empty($imagenes[0]) ? 
                    (strpos($imagenes[0], \'./\') === 0 ? $imagenes[0] : \'./\' . $imagenes[0]) : 
                    \'./img/default.jpg\';

                $productos_html .= "
                    <div class=\'perfil_tarjeta\' data-product-id=\'{$producto[\'id_producto\']}\'>
                        <div class=\'perfil_img-container\'>
                            <img src=\'{$imagen_destino}\' alt=\'{$producto[\'nombre_producto\']}\' onerror=\"this.src=\'./img/default.jpg\'\">
                        </div>
                        <div class=\'perfil_contenido\'>
                            <h3>{$producto[\'nombre_producto\']}</h3>
                            <span class=\'perfil_precio\'>$ {$producto[\'precio\']}</span>
                        </div>
                    </div>
                ";
            }
        } else {
            $productos_html = "<h3 style=\'text-align:center; grid-column:1/-1; color:#666; padding:2rem;\'>No tienes productos disponibles.</h3>";
        }
        $stmt->close();
    } else {
        $productos_html = "<h3 style=\'text-align:center; grid-column:1/-1; color:#666; padding:2rem;\'>Error al obtener los productos.</h3>";
    }
}

// Preparar datos para reemplazar
$datos_reemplazo = [
    \'foto_portada\' => $foto_portada ?? \'\',
    \'foto_perfil\' => $rt_foto_perfil,
    \'nombre_usuario\' => $_SESSION[\'nombre_us\'] ?? \'Usuario\',
    \'biografia\' => nl2br(htmlspecialchars($biografia, ENT_QUOTES, \'UTF-8\')),
    \'productos\' => $productos_html
];
?>
';

    // Agregar el HTML de la IA y reemplazar placeholders con variables PHP
    $html_modificado = $html_ia;
    
    // Reemplazar placeholders con código PHP
    $html_modificado = str_replace('{foto_portada}', '<?php echo $datos_reemplazo[\'foto_portada\']; ?>', $html_modificado);
    $html_modificado = str_replace('{foto_perfil}', '<?php echo $datos_reemplazo[\'foto_perfil\']; ?>', $html_modificado);
    $html_modificado = str_replace('{nombre_usuario}', '<?php echo htmlspecialchars($datos_reemplazo[\'nombre_usuario\']); ?>', $html_modificado);
    $html_modificado = str_replace('{biografia}', '<?php echo $datos_reemplazo[\'biografia\']; ?>', $html_modificado);
    $html_modificado = str_replace('{productos}', '<?php echo $datos_reemplazo[\'productos\']; ?>', $html_modificado);

    // Eliminar cualquier contenedor de lista-plantillas que la IA pueda haber incluido
    $html_modificado = preg_replace('/<div class="lista-plantillas">\s*.*?\s*<\/div>/s', '', $html_modificado);
    $html_modificado = preg_replace('/<div class="selector-plantillas">\s*.*?\s*<\/div>/s', '', $html_modificado);

    // Combinar el código PHP con el HTML modificado
    return $php_code . $html_modificado;
}
?>