<?php
function conectar() {
    // Crear conexión
    $con = mysqli_connect('localhost', 'root', '', 'bd_tshopp');

    // Verificar la conexión
    if (!$con) {
        die("Conexión fallida: " . mysqli_connect_error());
    } else {
        // Establecer el conjunto de caracteres a UTF-8
        mysqli_set_charset($con, "utf8");
        return $con;
    }
}
?>

