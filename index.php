<?php
session_start();
// ERRORES
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/conexion/conexion.php';
$con = conectar(); // Asignar a variable

// Cargar CSS personalizado desde BD si el usuario está logueado
$css_personalizado = '';
if (isset($_SESSION['id_usuario']) && $con) {
    $query = "SELECT css_personalizado FROM perfil WHERE id_usuario = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $_SESSION['id_usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $css_personalizado = $row['css_personalizado'];
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
    <!-- SIEMPRE cargar el CSS base -->
    <!--<link rel="stylesheet" href="estilos/estilos_perfil.css">-->
    
    <!-- Cargar personalizado si existe -->
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
                include($ruta_archivo);
                echo "<!-- DEBUG: Archivo incluido -->";
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