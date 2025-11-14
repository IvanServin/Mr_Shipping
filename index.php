<?php
session_start();
// ERRORES
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/conexion/conexion.php';
$con = conectar(); // Asignar a variable

// Cargar CSS y HTML personalizado desde BD si el usuario está logueado
$css_personalizado = '';
$html_personalizado = '';
if (isset($_SESSION['id_usuario']) && $con) {
    $query = "SELECT css_personalizado, html_personalizado FROM perfil WHERE id_usuario = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $_SESSION['id_usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $css_personalizado = $row['css_personalizado'];
        $html_personalizado = $row['html_personalizado'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos/estilos_index.css">
    <link rel="stylesheet" href="estilos/estilos_vista_productos.css">
    <link rel="stylesheet" href="estilos/estilos_pd.css">
    <link rel="stylesheet" href="estilos/estilos_editar_perfil.css">
    <!-- SIEMPRE cargar el CSS base del perfil -->
    <link rel="stylesheet" href="estilos/estilos_perfil.css">
    
    <!-- Cargar CSS personalizado si existe -->
    <?php if (!empty($css_personalizado) && file_exists("{$css_personalizado}")): ?>
    <link rel="stylesheet" href="<?php echo $css_personalizado; ?>?version=<?php echo time(); ?>">
    <?php endif; ?>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>TuShopp</title>
</head>
<body>
    <?php
    $modulo = isset($_GET['modulo']) ? $_GET['modulo'] : '';
    echo "<!-- DEBUG: Módulo = '$modulo' -->";
    echo "<!-- DEBUG: CSS personalizado = '$css_personalizado' -->";
    echo "<!-- DEBUG: HTML personalizado = '$html_personalizado' -->";
    
    if ($modulo !== 'perfil') {
        include('php/header.php');
    }
    ?>

    <main>
        <?php
        echo "<!-- DEBUG: Entrando en main -->";
        
        if (!empty($modulo)) {
            $pagina = addslashes($modulo);
            $ruta_archivo = 'php/' . $pagina . '.php';
            echo "<!-- DEBUG: Intentando incluir: $ruta_archivo -->";
            
            if (file_exists($ruta_archivo)) {
                // Si es el módulo perfil, usar el sistema dinámico
                if ($pagina === 'perfil') {
                    // Si hay HTML personalizado y el archivo existe, incluirlo
                    if (!empty($html_personalizado) && file_exists($html_personalizado)) {
                        try {
                            include($html_personalizado);
                            echo "<!-- DEBUG: HTML personalizado incluido desde: $html_personalizado -->";
                        } catch (Exception $e) {
                            echo "<!-- DEBUG: ERROR al incluir HTML personalizado: " . $e->getMessage() . " -->";
                            echo "<div style='background:#f8d7da; color:#721c24; padding:10px; margin:10px; border-radius:5px; text-align:center;'>Error al cargar el perfil personalizado. Cargando perfil base.</div>";
                            include('php/perfil_base.php');
                            echo "<!-- DEBUG: Perfil base incluido por error -->";
                        }
                    } else {
                        // Si no hay HTML personalizado, usar el perfil base
                        if (empty($html_personalizado)) {
                            echo "<!-- DEBUG: No hay HTML personalizado en BD -->";
                        } else {
                            echo "<!-- DEBUG: Archivo HTML personalizado no encontrado: $html_personalizado -->";
                        }
                        include('php/perfil_base.php');
                        echo "<!-- DEBUG: Perfil base incluido -->";
                    }
                } else {
                    include($ruta_archivo);
                    echo "<!-- DEBUG: Archivo incluido -->";
                }
            } else {
                echo "<!-- DEBUG: Archivo NO existe: $ruta_archivo -->";
                include('php/mostrar_productos.php');
            }
        } else {
            echo "<!-- DEBUG: Cargando mostrar_productos por defecto -->";
            include('php/mostrar_productos.php');
        }
        ?>
    </main>

    <?php
    if($modulo !== 'perfil'){
        echo "<!-- DEBUG: Incluyendo footer -->";
        include('php/footer.php');
    }
    ?>

    <!-- SCRIPT DE PRODUCTOS -->
    <script src="js/productos.js"></script>

</body>
</html>