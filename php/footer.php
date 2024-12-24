<footer>
    <?php
    if (isset($_SESSION['correo'])) {
        echo "<h3>correo: $_SESSION[correo]</h3>";
    } else {
        echo "<a>correo: inicie session </a>";
    }
    ?>
</footer>