<?php
// Verificar si la sesión está activa y obtener el id_usuario desde la sesión
if (!isset($_SESSION['id_usuario'])) {
    $mensaje_sesion = "Inicia sesión para ver o cargar productos.";
} else {
    $id_usuario = $_SESSION['id_usuario'];
    
    $query_perfil = "SELECT * FROM perfil WHERE id_usuario = ?";
    $stmt_perfil = $con->prepare($query_perfil);
    $stmt_perfil->bind_param("i", $id_usuario);
    $stmt_perfil->execute();
    $result_perfil = $stmt_perfil->get_result();
    
    if ($result_perfil && $result_perfil->num_rows > 0) {
        $perfil = $result_perfil->fetch_assoc();
        $foto_perfil_actual = !empty($perfil['foto_perfil']) ? $perfil['foto_perfil'] : './img/default.jpg';
        $foto_portada_actual = !empty($perfil['foto_portada']) ? $perfil['foto_portada'] : null;
        $biografia_actual = !empty($perfil['biografia']) ? $perfil['biografia'] : 'Agrega info para que la gente sepa más de ti';
    } else {
        $foto_perfil_actual = './img/default.jpg';
        $foto_portada_actual = null;
        $biografia_actual = 'Agrega info para que la gente sepa más de ti';
    }
    $stmt_perfil->close();
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $mensaje_exito = "";
    $mensaje_error = "";
    
    // Verificar si el perfil ya existe
    $query_check = "SELECT id_perfil FROM perfil WHERE id_usuario = ?";
    $stmt_check = $con->prepare($query_check);
    $stmt_check->bind_param("i", $id_usuario);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $perfil_existe = $result_check->num_rows > 0;
    $stmt_check->close();
    
    // Procesar foto de perfil
    $foto_perfil = $foto_perfil_actual;
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
        $foto_perfil = subirImagen($_FILES['foto_perfil'], 'perfil');
        if (!$foto_perfil) {
            $mensaje_error = "Error al subir la foto de perfil.";
        }
    }
    
    // Procesar foto de portada
    $foto_portada = $foto_portada_actual;
    if (isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] === 0) {
        $foto_portada = subirImagen($_FILES['foto_portada'], 'portada');
        if (!$foto_portada) {
            $mensaje_error = "Error al subir la foto de portada.";
        }
    }
    
    // Procesar biografía
    $biografia = isset($_POST['biografia']) ? trim($_POST['biografia']) : $biografia_actual;
    
    // Si no hay errores, guardar en la base de datos
    if (empty($mensaje_error)) {
        if ($perfil_existe) {
            // UPDATE si el perfil ya existe
            $query = "UPDATE perfil SET foto_perfil = ?, foto_portada = ?, biografia = ? WHERE id_usuario = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("sssi", $foto_perfil, $foto_portada, $biografia, $id_usuario);
        } else {
            // INSERT si el perfil no existe
            $query = "INSERT INTO perfil (id_usuario, foto_perfil, foto_portada, biografia, css_personalizado) VALUES (?, ?, ?, ?, 'estilos/estilos_perfil.css')";
            $stmt = $con->prepare($query);
            $stmt->bind_param("isss", $id_usuario, $foto_perfil, $foto_portada, $biografia);
        }
        
        if ($stmt->execute()) {
            $mensaje_exito = "Perfil actualizado correctamente.";
            // Actualizar variables para mostrar los nuevos datos
            $foto_perfil_actual = $foto_perfil;
            $foto_portada_actual = $foto_portada;
            $biografia_actual = $biografia;
        } else {
            $mensaje_error = "Error al guardar los cambios en la base de datos: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Función para subir imágenes
function subirImagen($archivo, $tipo) {
    $directorio = './img/perfiles/';
    
    // Crear directorio si no existe
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }
    
    // Validar tipo de archivo
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $extensiones_permitidas)) {
        return false;
    }
    
    // Generar nombre único
    $nombre_archivo = $tipo . '_' . uniqid() . '.' . $extension;
    $ruta_completa = $directorio . $nombre_archivo;
    
    // Mover archivo
    if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
        return $ruta_completa;
    }
    
    return false;
}
?>

<div class="editor-perfil">
    <h2>Editar Perfil</h2>
    
    <?php if (isset($mensaje_exito)): ?>
        <div class="success-message"><?php echo $mensaje_exito; ?></div>
    <?php endif; ?>
    
    <?php if (isset($mensaje_error)): ?>
        <div class="error-message"><?php echo $mensaje_error; ?></div>
    <?php endif; ?>
    
    <div class="tabs-container">
        <div class="tabs-header">
            <button class="tab-header active" onclick="cambiarTab('foto-perfil')">
                <span>👤</span>
                Foto Perfil
            </button>
            <button class="tab-header" onclick="cambiarTab('foto-portada')">
                <span>🏞️</span>
                Foto Portada
            </button>
            <button class="tab-header" onclick="cambiarTab('biografia')">
                <span>📝</span>
                Biografía
            </button>
        </div>
        
        <form class="form-1" action="./index.php?modulo=editar_perfil" method="POST" enctype="multipart/form-data">
            
            <!-- Foto de Perfil -->
            <div class="tab-panel active" id="foto-perfil">
                <div class="panel-content">
                    <h3>Foto de Perfil</h3>
                    <p class="descripcion">Recomendado: 400x400px, formato JPG o PNG</p>
                    
                    <div class="imagen-section">
                        <div class="imagen-side">
                            <?php if (!empty($foto_perfil_actual)): ?>
                                <div class="current-image">
                                    <img src="<?php echo htmlspecialchars($foto_perfil_actual); ?>" 
                                         alt="Foto actual" 
                                         onerror="this.style.display='none'">
                                    <p>Actual</p>
                                </div>
                            <?php else: ?>
                                <div class="no-image">
                                    <span>👤</span>
                                    <p>Sin foto</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="upload-section">
                            <div class="upload-area" onclick="document.getElementById('foto_perfil').click()">
                                <div class="upload-icon">📁</div>
                                <div class="upload-text">Seleccionar archivo</div>
                                <div class="upload-hint">o arrastrar aquí</div>
                            </div>
                            <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" style="display: none;">
                        </div>
                        
                        <div class="imagen-side">
                            <div class="preview-image">
                                <img src="" alt="Vista previa" id="preview_perfil" style="display:none;">
                                <p>Vista previa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Foto de Portada -->
            <div class="tab-panel" id="foto-portada">
                <div class="panel-content">
                    <h3>Foto de Portada</h3>
                    <p class="descripcion">Recomendado: 1200x400px, formato JPG o PNG</p>
                    
                    <div class="imagen-section">
                        <div class="imagen-side">
                            <?php if (!empty($foto_portada_actual)): ?>
                                <div class="current-image">
                                    <img src="<?php echo htmlspecialchars($foto_portada_actual); ?>" 
                                         alt="Portada actual" 
                                         onerror="this.style.display='none'">
                                    <p>Actual</p>
                                </div>
                            <?php else: ?>
                                <div class="no-image">
                                    <span>🏞️</span>
                                    <p>Sin portada</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="upload-section">
                            <div class="upload-area" onclick="document.getElementById('foto_portada').click()">
                                <div class="upload-icon">📁</div>
                                <div class="upload-text">Seleccionar archivo</div>
                                <div class="upload-hint">o arrastrar aquí</div>
                            </div>
                            <input type="file" name="foto_portada" id="foto_portada" accept="image/*" style="display: none;">
                        </div>
                        
                        <div class="imagen-side">
                            <div class="preview-image">
                                <img src="" alt="Vista previa" id="preview_portada" style="display:none;">
                                <p>Vista previa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Biografía -->
            <div class="tab-panel" id="biografia">
                <div class="panel-content">
                    <h3>Biografía</h3>
                    <p class="descripcion">Comparte información sobre ti</p>
                    <textarea name="biografia" id="biografia" 
                              placeholder="Escribe tu biografía..."><?php echo htmlspecialchars($biografia_actual); ?></textarea>
                    <div class="contador-caracteres" id="contador_biografia">
                        <?php echo strlen($biografia_actual); ?> caracteres
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button class="continuar-btn" type="submit">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
// Sistema de tabs simple
function cambiarTab(tabName) {
    // Ocultar todos los panels
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.remove('active');
    });
    
    // Remover active de todos los headers
    document.querySelectorAll('.tab-header').forEach(header => {
        header.classList.remove('active');
    });
    
    // Mostrar el panel seleccionado
    document.getElementById(tabName).classList.add('active');
    
    // Activar el header correspondiente
    event.target.classList.add('active');
}

// Vista previa para fotos
document.getElementById('foto_perfil').addEventListener('change', function(e) {
    mostrarVistaPrevia(this, 'preview_perfil');
});

document.getElementById('foto_portada').addEventListener('change', function(e) {
    mostrarVistaPrevia(this, 'preview_portada');
});

function mostrarVistaPrevia(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

// Contador de caracteres
document.getElementById('biografia').addEventListener('input', function(e) {
    document.getElementById('contador_biografia').textContent = 
        e.target.value.length + ' caracteres';
});

// Drag and drop
document.querySelectorAll('.upload-area').forEach(area => {
    area.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.background = '#f0f8ff';
    });
    
    area.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.background = '#fafafa';
    });
    
    area.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.background = '#fafafa';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const input = this.closest('.upload-section').querySelector('input[type="file"]');
            input.files = files;
            
            // Disparar evento change
            const event = new Event('change');
            input.dispatchEvent(event);
        }
    });
});
</script>