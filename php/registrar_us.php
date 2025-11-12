<?php 
include_once __DIR__ . '/../conexion/conexion.php';
$con = conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = mysqli_real_escape_string($con, $_POST['correo']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($con, $_POST['apellido']);
    $edad = (int)$_POST['edad'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $nombre_usuario = mysqli_real_escape_string($con, $_POST['nombre_usuario']);

    $sql = "INSERT INTO usuarios(correo, nombre, apellido, edad, contrasena, nombre_usuario)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssiss", $correo, $nombre, $apellido, $edad, $contrasena, $nombre_usuario);

    if ($stmt->execute()) {
        echo "<script>alert('Registro completado correctamente');</script>";
    } else {
        echo "<script>alert('Error: ".$stmt->error."');</script>";
    }
    $stmt->close();
}
?>

<div class="registro-container">
    <form action="" method="POST" id="registroForm" class="registro-form">
        <h2>Registro de Usuario</h2>
        <div class="progress">
            <div class="progress-bar" id="progressBar"></div>
        </div>

        <div class="step active">
            <label for="correo">Correo electrónico:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required>
        </div>

        <div class="step">
            <label for="edad">Edad:</label>
            <input type="number" name="edad" id="edad" required>

            <label for="nombre_usuario">Nombre de usuario:</label>
            <input type="text" name="nombre_usuario" id="nombre_usuario" required>
        </div>

        <div class="step">
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" required>
            <p style="font-size:0.9em; opacity:0.8;">Tu perfil se podrá completar más adelante.</p>
        </div>

        <div class="step-buttons">
            <button type="button" id="prevBtn">Atrás</button>
            <button type="button" id="nextBtn">Siguiente</button>
            <button type="submit" id="submitBtn" style="display:none;">Registrarse</button>
        </div>
    </form>
</div>

<script>
const steps = document.querySelectorAll('.registro-form .step');
const progressBar = document.getElementById('progressBar');
const nextBtn = document.getElementById('nextBtn');
const prevBtn = document.getElementById('prevBtn');
const submitBtn = document.getElementById('submitBtn');
let currentStep = 0;

function updateSteps() {
    steps.forEach((step, index) => step.classList.toggle('active', index === currentStep));
    progressBar.style.width = ((currentStep + 1) / steps.length) * 100 + '%';
    prevBtn.style.display = currentStep === 0 ? 'none' : 'inline-block';
    nextBtn.style.display = currentStep === steps.length - 1 ? 'none' : 'inline-block';
    submitBtn.style.display = currentStep === steps.length - 1 ? 'inline-block' : 'none';
}
nextBtn.addEventListener('click', () => { if (currentStep < steps.length - 1) currentStep++; updateSteps(); });
prevBtn.addEventListener('click', () => { if (currentStep > 0) currentStep--; updateSteps(); });
updateSteps();
</script>

