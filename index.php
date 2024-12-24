<?php
session_start();
include_once __DIR__. '/conexion/conexion.php';
conectar();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos/estilos_index.css">
    <link rel="stylesheet" href="estilos/estilos_pd.css">
    <link rel="stylesheet" href="estilos/estilos_perfil.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>TuShop</title>
</head>

<body>
    <?php
    include('php/header.php');
    ?>
    <main>
        <?php
        if (!empty($_GET['modulo'])) {
            $pagina = addslashes($_GET['modulo']);
            include ('php/'.$pagina.'.php');
        } else {
            include('php/mostrar_productos.php');
        }
        ?>
    </main>

    <?php
    include('php/footer.php');
    ?>

</body>

</html>
