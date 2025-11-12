<?php
// api_deepseek.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<div style='padding:20px; background:#f8f9fa; border-radius:10px; margin:20px;'>";
echo "<h2>🔧 Debug - Proceso de IA</h2>";

// Mostrar todo lo que llega por POST
echo "<h3>📨 Datos POST recibidos:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Recibir instrucción del usuario
$instruccion_usuario = $_POST['instruccion'] ?? '';

echo "<h3>👤 Instrucción del usuario:</h3>";
echo "<p style='background:white; padding:10px; border-radius:5px;'><strong>" . htmlspecialchars($instruccion_usuario) . "</strong></p>";

if (empty($instruccion_usuario)) {
    echo "<p style='color: red;'>❌ Error: No se recibió instrucción.</p>";
    echo "<br><a href='index.php?modulo=asistente_perfil' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>← Volver al Asistente</a>";
    echo "</div>";
    exit;
}

// CONEXIÓN A LA BASE DE DATOS Y OBTENCIÓN DEL CSS DEL PERFIL
$estilos_actuales = '';
$id_usuario = $_SESSION['id_usuario'] ?? null;

if ($id_usuario) {
    try {
        // Usar tu conexión existente
        include_once __DIR__ . '/../conexion/conexion.php';
        $con = conectar();
        
        // Obtener la ruta del CSS personalizado del perfil
        $sql = "SELECT css_personalizado FROM perfil WHERE id_usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $perfil = $result->fetch_assoc();
        
        if ($perfil && !empty($perfil['css_personalizado'])) {
            $ruta_css = $perfil['css_personalizado'];
            
            // Verificar si el archivo existe
            if (file_exists($ruta_css)) {
                $estilos_actuales = file_get_contents($ruta_css);
                echo "<p style='color: green;'>✅ CSS cargado desde: " . htmlspecialchars($ruta_css) . "</p>";
            } else {
                // Si el archivo no existe, cargar el CSS por defecto
                $ruta_default = 'estilos/estilos_perfil.css';
                if (file_exists($ruta_default)) {
                    $estilos_actuales = file_get_contents($ruta_default);
                    echo "<p style='color: orange;'>⚠️ Archivo personalizado no encontrado. Cargando CSS por defecto: " . htmlspecialchars($ruta_default) . "</p>";
                } else {
                    echo "<p style='color: red;'>❌ Error: No se pudo cargar ningún archivo CSS.</p>";
                    $estilos_actuales = '/* Error: No se pudo cargar CSS */';
                }
            }
        } else {
            // Si no hay CSS personalizado, cargar el por defecto
            $ruta_default = 'estilos/estilos_perfil.css';
            if (file_exists($ruta_default)) {
                $estilos_actuales = file_get_contents($ruta_default);
                echo "<p style='color: blue;'>ℹ️ Cargando CSS por defecto: " . htmlspecialchars($ruta_default) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Error: No se pudo cargar el CSS por defecto.</p>";
                $estilos_actuales = '/* Error: No se pudo cargar CSS */';
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error al obtener CSS de la base de datos: " . htmlspecialchars($e->getMessage()) . "</p>";
        // Intentar cargar CSS por defecto como fallback
        $ruta_default = 'estilos/estilos_perfil.css';
        if (file_exists($ruta_default)) {
            $estilos_actuales = file_get_contents($ruta_default);
        } else {
            $estilos_actuales = '/* Error: No se pudo cargar CSS */';
        }
    }
} else {
    echo "<p style='color: red;'>❌ No se pudo identificar al usuario.</p>";
    // Cargar CSS por defecto
    $ruta_default = 'estilos/estilos_perfil.css';
    if (file_exists($ruta_default)) {
        $estilos_actuales = file_get_contents($ruta_default);
    } else {
        $estilos_actuales = '/* Error: No se pudo cargar CSS */';
    }
}

echo "<h3>🎨 CSS que se enviará a la IA:</h3>";
echo "<pre style='background:white; padding:15px; border-radius:5px; max-height:300px; overflow:auto;'>" . htmlspecialchars($estilos_actuales) . "</pre>";

// Tu API key de DeepSeek
$apiKey = "aca va la ApiKey";

// Data para DeepSeek
$data = [
    "model" => "deepseek-chat",
    "messages" => [
        [
            "role" => "user",
            "content" => "Eres un asistente especializado en CSS. Analiza el siguiente CSS y aplica esta instrucción: '" . $instruccion_usuario . "'\n\nCSS actual:\n" . $estilos_actuales . "\n\nIMPORTANTE: Responde ÚNICAMENTE con el código CSS completo y modificado, sin explicaciones, sin comentarios adicionales, sin texto introductorio, solo el código CSS tampoco cambies propiedades como hidden o cosas que afecten la visivilidad, tambien ten cuidado en que los botones de navegacion sean siempre visibles."
        ]
    ],
    "max_tokens" => 2000,
    "temperature" => 0.4,
];

echo "<h3>📤 Datos enviados a DeepSeek:</h3>";
echo "<pre style='background:white; padding:15px; border-radius:5px;'>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";

// CURL - CONFIGURACIÓN MEJORADA
echo "<h3>🔄 Conectando con DeepSeek API...</h3>";

$ch = curl_init("https://api.deepseek.com/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_TIMEOUT => 1000, // Aumentado
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
    echo "<p style='color: red;'>❌ CURL ERROR: " . htmlspecialchars($error_msg) . "</p>";
    echo "<p style='color: orange;'>💡 Consejo: La API está tardando demasiado. Intenta con una instrucción más simple o intenta más tarde.</p>";
    echo "<br><a href='index.php?modulo=asistente_perfil' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>← Volver al Asistente</a>";
    echo "</div>";
    exit;
}

curl_close($ch);

// Verificar código HTTP
if ($http_code !== 200) {
    echo "<p style='color: red;'>❌ HTTP ERROR: Código " . $http_code . "</p>";
}

echo "<h3>📥 Respuesta cruda de DeepSeek:</h3>";
echo "<pre style='background:white; padding:15px; border-radius:5px; max-height:300px; overflow:auto;'>" . htmlspecialchars($response) . "</pre>";

$result = json_decode($response, true);

echo "<h2>🤖 Respuesta de la IA:</h2>";

if (isset($result["choices"][0]["message"]["content"])) {
    $respuesta_ia = $result["choices"][0]["message"]["content"];
    
    // Limpiar la respuesta - asegurarse de que solo tenga CSS
    $respuesta_ia = preg_replace('/```css\s*/', '', $respuesta_ia);
    $respuesta_ia = preg_replace('/```\s*/', '', $respuesta_ia);
    $respuesta_ia = trim($respuesta_ia);
    
    echo "<pre style='background:#e8f5e8; padding:15px; border-radius:5px; border:2px solid #4CAF50; max-height:400px; overflow:auto;'>" . htmlspecialchars($respuesta_ia) . "</pre>";
    
    // Guardar en sesión
    $_SESSION['css_ia'] = $respuesta_ia;
    echo "<p style='color: green; font-weight:bold;'>✅ Respuesta guardada en sesión</p>";
    
    // Mostrar botón para guardar el CSS
    echo "<br><a href='index.php?modulo=aplicar_estilos' style='background:#28a745; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; margin-right:10px;'>💾 Guardar CSS en mi perfil</a>";
    
} else {
    echo "<p style='color: red;'>❌ La IA no devolvió código.</p>";
    echo "<h4>Debug respuesta completa:</h4>";
    echo "<pre style='background:#ffe6e6; padding:15px; border-radius:5px;'>" . print_r($result, true) . "</pre>";
}

echo "<br><br>";
echo "<a href='index.php?modulo=asistente_perfil' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; margin-right:10px;'>← Volver al Asistente</a>";
  ?><?php if(isset($_SESSION['css_ia'])): ?>
        <div style="margin-top:20px; padding:15px; background:#e8f5e8; border:2px solid #4CAF50; border-radius:5px;">
            <h4>✅ Vista Previa Generada</h4>
            <p>Se creó una vista previa con tus cambios. Revísala antes de aplicar.</p>
            <a href="index.php?modulo=perfil_preview" style="background:#4CAF50; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block; margin-top:10px;">
                👀 Ver Vista Previa
            </a>
        </div>
    ?>   
    <?php endif; ?><?php
echo "<a href='index.php?modulo=perfil' style='background:#6c757d; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Ver Perfil →</a>";

echo "</div>";