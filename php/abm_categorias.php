<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include_once __DIR__.'/../conexion/conexion.php';
$con = conectar();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $nombre_cat = $_POST['nombre_cat'];
    $categoria = $_POST['categoria'];
    if(empty($categoria) || $categoria == 'null'){
        $sql = "INSERT INTO categorias(nombre_categoria)
        VALUES(?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s",$nombre_cat);
    }else{
        $sql = "INSERT INTO categorias(nombre_categoria, id_categoria_padre)
        VALUES(?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ss",$nombre_cat, $categoria);
    }
    if($stmt->execute()){
        ?><script>
            alert("se guardo la categoria");
        </script><?php
    }else{
        ?><script>
            alert("error no se pudo crear la categria");
        </script><?php
    }
}



?>
<form class="form-1"action="php/abm_categorias.php" method="post">
    <label for="nombre_cat">Nombre para la categoria</label>
    <input type="text"name="nombre_cat"id="nombre_cat">

    <label for="categoria">categoria padre</label>
    <select name="categoria" id="categoria">
        <option value="null"></option>
        <?php
        $sql = "SELECT id_categoria,nombre_categoria FROM categorias WHERE id_categoria_padre IS NULL";
        $categoria = $con->query($sql);
        while($fila = $categoria->fetch_assoc()){
            echo"<option value='".$fila['id_categoria']."'>".htmlspecialchars($fila['nombre_categoria'])."</option>"; 
        }
        ?>
    </select>
    <button class="continuar-btn" type="submit">aceptar</button>

</form>