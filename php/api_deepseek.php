<?php
// api_deepseek.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Recibir instrucción del usuario
$instruccion_usuario = $_POST['instruccion'] ?? '';

if (empty($instruccion_usuario)) {
    $_SESSION['error'] = "No se recibió instrucción.";
    header('Location: index.php?modulo=asistente_perfil');
    exit;
}

// CONEXIÓN A LA BASE DE DATOS Y OBTENCIÓN DEL CSS Y HTML DEL PERFIL
$estilos_actuales = '';
$html_actual = '';
$id_usuario = $_SESSION['id_usuario'] ?? null;

if ($id_usuario) {
    try {
        include_once __DIR__ . '/../conexion/conexion.php';
        include_once __DIR__ . '/../includes/funciones_estilos.php';
        $con = conectar();
        
        //Cargar desde la tabla 'perfil' (plantilla activa actual)
        $query_perfil = "SELECT css_personalizado, html_personalizado FROM perfil WHERE id_usuario = ?";
        $stmt_perfil = $con->prepare($query_perfil);
        $stmt_perfil->bind_param("i", $id_usuario);
        $stmt_perfil->execute();
        $result_perfil = $stmt_perfil->get_result();
        
        if ($result_perfil && $result_perfil->num_rows > 0) {
            $perfil = $result_perfil->fetch_assoc();
            $css_personalizado = $perfil['css_personalizado'];
            $html_personalizado = $perfil['html_personalizado'];
            
            // OBTENER CSS
            if (!empty($css_personalizado) && file_exists($css_personalizado)) {
                $estilos_actuales = file_get_contents($css_personalizado);
            }
        }
        
        // Si no hay CSS personalizado, cargar por defecto
        if (empty($estilos_actuales)) {
            $ruta_default = 'estilos/estilos_perfil.css';
            if (file_exists($ruta_default)) {
                $estilos_actuales = file_get_contents($ruta_default);
            }
        }
        
        // Si no hay HTML personalizado, cargar estructura base
        if (empty($html_actual)) {
            $html_actual = obtenerEstructuraBasePerfil();
        }
        
    } catch (Exception $e) {
        // Fallback a valores por defecto
        $ruta_default = 'estilos/estilos_perfil.css';
        if (file_exists($ruta_default)) {
            $estilos_actuales = file_get_contents($ruta_default);
        }
        $html_actual = obtenerEstructuraBasePerfil();
    }
} else {
    // Cargar CSS por defecto
    $ruta_default = 'estilos/estilos_perfil.css';
    if (file_exists($ruta_default)) {
        $estilos_actuales = file_get_contents($ruta_default);
    }
    $html_actual = obtenerEstructuraBasePerfil();
}

// Tu API key de DeepSeek
$apiKey = "sk-67fc2035b3b144b39d8324a66f463b54";

// Data para DeepSeek
$data = [
    "model" => "deepseek-chat",
    "messages" => [
        [
            "role" => "user",
            "content" => "Eres un asistente especializado en CSS y HTML. Analiza el siguiente CSS y HTML de un perfil y aplica esta instrucción: '" . $instruccion_usuario . "'\n\nESTRUCTURA HTML ACTUAL:\n" . $html_actual . "\n\nCSS ACTUAL:\n" . $estilos_actuales . "\n\nIMPORTANTE: \n1. Responde ÚNICAMENTE con un JSON que tenga dos campos: 'css' y 'html'\n2. El campo 'css' debe contener el código CSS completo y modificado\n3. El campo 'html' debe contener la estructura HTML completa y modificada\n4. Mantén TODA la funcionalidad existente incluyendo:\n   - Formulario de cerrar sesión con method='POST'\n   - Botones de navegación funcionales\n   - Placeholders {foto_perfil}, {foto_portada}, {nombre_usuario}, {biografia}, {productos}\n5. Conserva las clases principales como 'cabecera', 'cont_img', 'datos_us', 'cont_nav_perfil', 'cont_productos'\n6. NO elimines elementos funcionales como el menú de configuración\n7. El HTML debe ser válido y mantener la estructura básica\n8. En el CSS, no cambies propiedades que afecten la funcionalidad\n9. Asegúrate de que los botones de navegación sean siempre visibles\n10. NO uses markdown ni bloques de código, responde SOLO con el JSON\n11. MANTÉN los placeholders: {foto_perfil}, {foto_portada}, {nombre_usuario}, {biografia}, {productos}\n12. MANTÉN el formulario de cerrar sesión EXACTAMENTE como está: <form action='' method='POST'><button type='submit' class='btn-cerrar-sesion'>Cerrar Sesión</button></form>",
        ]
    ],
    "max_tokens" => 8000,
    "temperature" => 0.4,
];

// CURL
$ch = curl_init("https://api.deepseek.com/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_TIMEOUT => 1000,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; PHP Script)',
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);

if ($response === false) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    $_SESSION['error'] = "Error de conexión con la IA: " . $error_msg;
    header('Location: index.php?modulo=asistente_perfil');
    exit;
}

curl_close($ch);

$result = json_decode($response, true);

if (isset($result["choices"][0]["message"]["content"])) {
    $respuesta_ia = $result["choices"][0]["message"]["content"];
    
    // CORRECCIÓN: Extraer el JSON del bloque de código markdown si existe
    if (preg_match('/```json\s*(.*?)\s*```/s', $respuesta_ia, $matches)) {
        $json_content = $matches[1];
    } else {
        $json_content = $respuesta_ia;
    }
    
    // Intentar parsear como JSON
    $json_data = json_decode($json_content, true);
    
    if ($json_data && isset($json_data['css']) && isset($json_data['html'])) {
        $css_modificado = $json_data['css'];
        $html_modificado = $json_data['html'];
        
        // Guardar en SESIÓN y redirigir para procesamiento
        $_SESSION['css_ia'] = $css_modificado;
        $_SESSION['html_ia'] = $html_modificado;
        $_SESSION['instruccion_ia'] = $instruccion_usuario;
        
        // Redirigir a aplicar_estilos para procesamiento completo
        header('Location: index.php?modulo=aplicar_estilos');
        exit;
        
    } else {
        // Fallback: si no viene en JSON, tratar de separar CSS y HTML
        // Buscar código CSS (entre ```css o en el contenido)
        if (preg_match('/```css\s*(.*?)\s*```/s', $respuesta_ia, $matches)) {
            $css_modificado = $matches[1];
        } else if (preg_match('/```\s*(.*?)\s*```/s', $respuesta_ia, $matches)) {
            // Si no hay marcado específico de CSS, usar el primer bloque de código
            $css_modificado = $matches[1];
        } else {
            $css_modificado = $respuesta_ia;
        }
        
        // Buscar código HTML (entre ```html o con etiquetas)
        if (preg_match('/```html\s*(.*?)\s*```/s', $respuesta_ia, $matches)) {
            $html_modificado = $matches[1];
        } else if (preg_match('/<div class="cabecera".*?<\/div>.*?<nav.*?<\/nav>.*?<div class="cont_productos".*?<\/div>/s', $respuesta_ia, $matches)) {
            $html_modificado = $matches[0];
        } else {
            // Si no se encuentra HTML, mantener el original
            $html_modificado = $html_actual;
        }
        
        // Limpiar el CSS de marcadores
        $css_modificado = preg_replace('/```css|```|```json/', '', $css_modificado);
        $css_modificado = trim($css_modificado);
        
        // Limpiar el HTML de marcadores
        $html_modificado = preg_replace('/```html|```|```json/', '', $html_modificado);
        $html_modificado = trim($html_modificado);
        
        //Guardar en SESIÓN y redirigir
        $_SESSION['css_ia'] = $css_modificado;
        $_SESSION['html_ia'] = $html_modificado;
        $_SESSION['instruccion_ia'] = $instruccion_usuario;
        
        // Redirigir a aplicar_estilos para procesamiento completo
        header('Location: index.php?modulo=aplicar_estilos');
        exit;
    }
    
} else {
    $_SESSION['error'] = "La IA no devolvió una respuesta válida.";
    header('Location: index.php?modulo=asistente_perfil');
    exit;
}
?>