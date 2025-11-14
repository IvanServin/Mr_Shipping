<?php
// php/editar_perfil.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../conexion/conexion.php';
include_once __DIR__ . '/../includes/funciones_estilos.php';
$con = conectar();

// Inicializar variables
$mensaje_exito = "";
$mensaje_error = "";
$foto_perfil_actual = './img/default.jpg';
$foto_portada_actual = null;
$biografia_actual = 'Agrega info para que la gente sepa más de ti';
$plantillas = [];

if (!isset($_SESSION['id_usuario'])) {
    $mensaje_sesion = "Inicia sesión para ver o cargar productos.";
} else {
    $id_usuario = $_SESSION['id_usuario'];
    
    // CARGAR PLANTILLAS DEL USUARIO
    $plantillas = obtenerPlantillasUsuario($id_usuario);
    $plantilla_activa = obtenerPlantillaActiva($id_usuario);
    
    // Obtener datos actuales del perfil
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
    }
    $stmt_perfil->close();
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    
    // Procesar cambio de plantilla
    if (isset($_POST['cambiar_plantilla'])) {
        $id_estilo = intval($_POST['cambiar_plantilla']);
        error_log("🔴 INTENTANDO CAMBIAR PLANTILLA: " . $id_estilo . " para usuario: " . $id_usuario);
        
        if (activarPlantilla($id_usuario, $id_estilo)) {
            $mensaje_exito = "Plantilla cambiada correctamente.";
            // Recargar plantillas
            $plantillas = obtenerPlantillasUsuario($id_usuario);
            error_log("✅ PLANTILLA CAMBIADA EXITOSAMENTE");
        } else {
            $mensaje_error = "Error al cambiar la plantilla.";
            error_log("❌ ERROR AL CAMBIAR PLANTILLA");
        }
    }
    
    // Procesar eliminación de plantilla
    if (isset($_POST['eliminar_plantilla']) && isset($_POST['plantilla_a_eliminar'])) {
        $id_estilo = intval($_POST['plantilla_a_eliminar']);
        error_log("🔴 INTENTANDO ELIMINAR PLANTILLA: " . $id_estilo . " para usuario: " . $id_usuario);
        
        if (eliminarPlantilla($id_usuario, $id_estilo)) {
            $mensaje_exito = "Plantilla eliminada correctamente.";
            // Recargar plantillas
            $plantillas = obtenerPlantillasUsuario($id_usuario);
            error_log("✅ PLANTILLA ELIMINADA EXITOSAMENTE");
        } else {
            $mensaje_error = "Error al eliminar la plantilla. Verifica que no sea la única plantilla activa.";
            error_log("❌ ERROR AL ELIMINAR PLANTILLA");
        }
    }
    
    if (empty($mensaje_error) && !isset($_POST['cambiar_plantilla']) && !isset($_POST['eliminar_plantilla'])) {
        // Procesar foto de perfil
        $foto_perfil = $foto_perfil_actual;
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
            $nueva_foto = subirImagen($_FILES['foto_perfil'], 'perfil');
            if ($nueva_foto) {
                $foto_perfil = $nueva_foto;
            } else {
                $mensaje_error = "Error al subir la foto de perfil.";
            }
        }
        
        // Procesar foto de portada
        $foto_portada = $foto_portada_actual;
        if (isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] === 0) {
            $nueva_portada = subirImagen($_FILES['foto_portada'], 'portada');
            if ($nueva_portada) {
                $foto_portada = $nueva_portada;
            } else {
                $mensaje_error = "Error al subir la foto de portada.";
            }
        }
        
        // Procesar biografía
        $biografia = isset($_POST['biografia']) ? trim($_POST['biografia']) : $biografia_actual;
        
        // Si no hay errores, guardar en la base de datos
        if (empty($mensaje_error)) {
            $query = "UPDATE perfil SET foto_perfil = ?, foto_portada = ?, biografia = ? WHERE id_usuario = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("sssi", $foto_perfil, $foto_portada, $biografia, $id_usuario);
            
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
}

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
    
    // Validar tamaño (máximo 5MB)
    if ($archivo['size'] > 5 * 1024 * 1024) {
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
    
    <?php if (!empty($mensaje_exito)): ?>
        <div class="success-message"><?php echo $mensaje_exito; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje_error)): ?>
        <div class="error-message"><?php echo $mensaje_error; ?></div>
    <?php endif; ?>
    
    <div class="menu-lateral">
        <div class="tabs-header">
            <button class="tab-header active" type="button" onclick="cambiarTab('foto-perfil')">
                <span>👤</span>
                Foto Perfil
            </button>
            <button class="tab-header" type="button" onclick="cambiarTab('foto-portada')">
                <span>🏞️</span>
                Foto Portada
            </button>
            <button class="tab-header" type="button" onclick="cambiarTab('biografia')">
                <span>📝</span>
                Biografía
            </button>
            <button class="tab-header" type="button" onclick="cambiarTab('plantillas')">
                <span>🎨</span>
                Mis Plantillas
                <?php if (!empty($plantillas)): ?>
                    <span class="badge"><?php echo count($plantillas); ?></span>
                <?php endif; ?>
            </button>
        </div>
    </div>
    
    <div class="contenido-principal">
        <!-- Formulario para fotos y biografía -->
        <form class="form-2" action="./index.php?modulo=editar_perfil" method="POST" enctype="multipart/form-data" id="formEditarPerfil">
            
            <!-- Foto de Perfil -->
            <div class="tab-panel active" id="foto-perfil">
                <div class="panel-content">
                    <h3>Foto de Perfil</h3>
                    <p class="descripcion">Recomendado: 400x400px, formato JPG o PNG (Máx. 5MB)</p>
                    
                    <div class="imagen-section">
                        <div class="imagen-row">
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
                            
                            <div class="imagen-side">
                                <div class="preview-image">
                                    <img src="" alt="Vista previa" id="preview_perfil" style="display:none;">
                                    <p>Vista previa</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="upload-section">
                            <div class="upload-area" onclick="document.getElementById('foto_perfil').click()">
                                <div class="upload-icon">📁</div>
                                <div class="upload-text">Seleccionar archivo</div>
                                <div class="upload-hint">o arrastrar aquí</div>
                            </div>
                            <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" style="display: none;" onchange="mostrarVistaPrevia(this, 'preview_perfil')">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Foto de Portada -->
            <div class="tab-panel" id="foto-portada">
                <div class="panel-content">
                    <h3>Foto de Portada</h3>
                    <p class="descripcion">Recomendado: 1200x400px, formato JPG o PNG (Máx. 5MB)</p>
                    
                    <div class="imagen-section">
                        <div class="imagen-row">
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
                            
                            <div class="imagen-side">
                                <div class="preview-image">
                                    <img src="" alt="Vista previa" id="preview_portada" style="display:none;">
                                    <p>Vista previa</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="upload-section">
                            <div class="upload-area" onclick="document.getElementById('foto_portada').click()">
                                <div class="upload-icon">📁</div>
                                <div class="upload-text">Seleccionar archivo</div>
                                <div class="upload-hint">o arrastrar aquí</div>
                            </div>
                            <input type="file" name="foto_portada" id="foto_portada" accept="image/*" style="display: none;" onchange="mostrarVistaPrevia(this, 'preview_portada')">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Biografía -->
            <div class="tab-panel" id="biografia">
                <div class="panel-content">
                    <h3>Biografía</h3>
                    <p class="descripcion">Comparte información sobre ti</p>
                    <textarea name="biografia" id="textarea_biografia" 
                              placeholder="Escribe tu biografía..."><?php echo htmlspecialchars($biografia_actual); ?></textarea>
                    <div class="contador-caracteres" id="contador_biografia">
                        <?php echo strlen($biografia_actual); ?> caracteres
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button class="continuar-btn" type="submit" name="guardar_cambios">Guardar Cambios</button>
            </div>
        </form>

        <div class="tab-panel active" id="plantillas">
            <div class="panel-content">
                <h3>Mis Plantillas de Estilo</h3>
                <p class="descripcion">Gestiona todas las versiones de tu perfil</p>
                
                <div class="plantillas-actions">
                    <a href="index.php?modulo=asistente_perfil" class="btn-crear">
                        🎨 Crear Nueva Plantilla con IA
                    </a>
                </div>
                
                <?php if (empty($plantillas)): ?>
                    <div class="no-plantillas">
                        <div class="empty-state">
                            <span>🎨</span>
                            <h4>No tienes plantillas guardadas</h4>
                            <p>Crea tu primera plantilla personalizada con el asistente IA</p>
                            <a href="index.php?modulo=asistente_perfil" class="btn-primary">
                                Crear Primera Plantilla
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="plantillas-grid">
                        <?php foreach ($plantillas as $plantilla): ?>
                            <div class="plantilla-card <?php echo $plantilla['esta_activo'] ? 'activa' : ''; ?>">
                                <div class="plantilla-header">
                                    <h4><?php echo htmlspecialchars($plantilla['nombre_estilo']); ?></h4>
                                    <?php if ($plantilla['esta_activo']): ?>
                                        <span class="badge-activa">ACTIVA</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="plantilla-info">
                                    <div class="info-item">
                                        <strong>Versión:</strong> v<?php echo $plantilla['version']; ?>
                                    </div>
                                    <div class="info-item">
                                        <strong>Creado:</strong> <?php echo date('d/m/Y H:i', strtotime($plantilla['fecha_creacion'])); ?>
                                    </div>
                                    <?php if (!empty($plantilla['descripcion'])): ?>
                                        <div class="info-item">
                                            <strong>Descripción:</strong> 
                                            <p><?php echo htmlspecialchars($plantilla['descripcion']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="plantilla-actions">
                                    <!-- Formulario para CAMBIAR plantilla -->
                                    <?php if (!$plantilla['esta_activo']): ?>
                                        <form method="POST" action="./index.php?modulo=editar_perfil" style="display: inline;">
                                            <button type="submit" name="cambiar_plantilla" value="<?php echo $plantilla['id_estilo']; ?>" class="btn-usar">
                                                🎯 Usar Esta
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="btn-actual">✅ En Uso</span>
                                    <?php endif; ?>
                                    
                                    <!-- Formulario para ELIMINAR plantilla -->
                                    <?php if (count($plantillas) > 1 && !$plantilla['esta_activo']): ?>
                                        <form method="POST" action="./index.php?modulo=editar_perfil" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta plantilla?');">
                                            <input type="hidden" name="plantilla_a_eliminar" value="<?php echo $plantilla['id_estilo']; ?>">
                                            <button type="submit" name="eliminar_plantilla" class="btn-eliminar">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="plantillas-stats">
                        <div class="stat">
                            <strong>Total:</strong> <?php echo count($plantillas); ?> plantillas
                        </div>
                        <div class="stat">
                            <strong>Activa:</strong> 
                            <?php 
                            $activa = array_filter($plantillas, function($p) { return $p['esta_activo']; });
                            echo count($activa) > 0 ? current($activa)['nombre_estilo'] : 'Ninguna';
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema de tabs
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
    event.currentTarget.classList.add('active');
}

// Vista previa para fotos
function mostrarVistaPrevia(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        // Validar tamaño (5MB máximo)
        if (file.size > 5 * 1024 * 1024) {
            alert('El archivo es demasiado grande. Máximo 5MB.');
            input.value = '';
            preview.style.display = 'none';
            return;
        }
        
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

// Contador de caracteres para biografía
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('textarea_biografia');
    const contador = document.getElementById('contador_biografia');
    
    if (textarea && contador) {
        textarea.addEventListener('input', function(e) {
            contador.textContent = e.target.value.length + ' caracteres';
        });
    }
    
    // Drag and drop para áreas de upload
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
});

document.getElementById('formEditarPerfil')?.addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Guardar Cambios';
        }, 3000);
    }
});
</script>