<?php
// includes/funciones_estilos.php

function obtenerPlantillaActiva($id_usuario) {
    global $con;
    
    $query = "SELECT * FROM estilos_perfil WHERE id_usuario = ? AND esta_activo = 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

function obtenerPlantillasUsuario($id_usuario) {
    global $con;
    
    $query = "SELECT * FROM estilos_perfil WHERE id_usuario = ? ORDER BY version DESC";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $plantillas = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $plantillas[] = $row;
        }
    }
    
    return $plantillas;
}

function activarPlantilla($id_usuario, $id_estilo) {
    global $con;
    
    // Iniciar transacción
    $con->begin_transaction();
    
    try {
        // 1. Desactivar todas las plantillas del usuario
        $query_desactivar = "UPDATE estilos_perfil SET esta_activo = 0 WHERE id_usuario = ?";
        $stmt_desactivar = $con->prepare($query_desactivar);
        $stmt_desactivar->bind_param("i", $id_usuario);
        $stmt_desactivar->execute();
        
        // 2. Activar la plantilla seleccionada
        $query_activar = "UPDATE estilos_perfil SET esta_activo = 1 WHERE id_estilo = ? AND id_usuario = ?";
        $stmt_activar = $con->prepare($query_activar);
        $stmt_activar->bind_param("ii", $id_estilo, $id_usuario);
        $stmt_activar->execute();
        
        // 3. Actualizar la tabla perfil con la plantilla activa
        $query_estilo = "SELECT ruta_css, ruta_html FROM estilos_perfil WHERE id_estilo = ?";
        $stmt_estilo = $con->prepare($query_estilo);
        $stmt_estilo->bind_param("i", $id_estilo);
        $stmt_estilo->execute();
        $result_estilo = $stmt_estilo->get_result();
        
        if ($result_estilo && $result_estilo->num_rows > 0) {
            $estilo = $result_estilo->fetch_assoc();
            
            $query_actualizar_perfil = "UPDATE perfil SET css_personalizado = ?, html_personalizado = ? WHERE id_usuario = ?";
            $stmt_actualizar_perfil = $con->prepare($query_actualizar_perfil);
            $stmt_actualizar_perfil->bind_param("ssi", $estilo['ruta_css'], $estilo['ruta_html'], $id_usuario);
            $stmt_actualizar_perfil->execute();
        }
        
        $con->commit();
        return true;
    } catch (Exception $e) {
        $con->rollback();
        error_log("Error al activar plantilla: " . $e->getMessage());
        return false;
    }
}

function eliminarPlantilla($id_usuario, $id_estilo) {
    global $con;
    
    // 1. Obtener información de la plantilla
    $query_info = "SELECT ruta_css, ruta_html, esta_activo FROM estilos_perfil WHERE id_estilo = ? AND id_usuario = ?";
    $stmt_info = $con->prepare($query_info);
    $stmt_info->bind_param("ii", $id_estilo, $id_usuario);
    $stmt_info->execute();
    $result_info = $stmt_info->get_result();
    
    if ($result_info->num_rows === 0) {
        return false;
    }
    
    $plantilla = $result_info->fetch_assoc();
    $es_activa = $plantilla['esta_activo'];
    
    // 2. Eliminar archivos físicos
    if (file_exists($plantilla['ruta_css'])) {
        unlink($plantilla['ruta_css']);
    }
    if (file_exists($plantilla['ruta_html'])) {
        unlink($plantilla['ruta_html']);
    }
    
    // 3. Eliminar de la base de datos
    $query_eliminar = "DELETE FROM estilos_perfil WHERE id_estilo = ? AND id_usuario = ?";
    $stmt_eliminar = $con->prepare($query_eliminar);
    $stmt_eliminar->bind_param("ii", $id_estilo, $id_usuario);
    
    if ($stmt_eliminar->execute()) {
        // 4. Si era la plantilla activa, activar la más reciente
        if ($es_activa) {
            $query_reciente = "SELECT id_estilo FROM estilos_perfil WHERE id_usuario = ? ORDER BY version DESC LIMIT 1";
            $stmt_reciente = $con->prepare($query_reciente);
            $stmt_reciente->bind_param("i", $id_usuario);
            $stmt_reciente->execute();
            $result_reciente = $stmt_reciente->get_result();
            
            if ($result_reciente->num_rows > 0) {
                $nueva_activa = $result_reciente->fetch_assoc();
                activarPlantilla($id_usuario, $nueva_activa['id_estilo']);
            } else {
                // Si no hay más plantillas, actualizar perfil a valores por defecto
                $query_actualizar_perfil = "UPDATE perfil SET css_personalizado = 'estilos/estilos_perfil.css', html_personalizado = 'html_personalizado/perfil_base.php' WHERE id_usuario = ?";
                $stmt_actualizar_perfil = $con->prepare($query_actualizar_perfil);
                $stmt_actualizar_perfil->bind_param("i", $id_usuario);
                $stmt_actualizar_perfil->execute();
            }
        }
        return true;
    }
    
    return false;
}

function obtenerEstructuraBasePerfil() {
    // Intentar cargar desde el archivo perfil_base.php si existe
    $ruta_base = __DIR__ . '/../php/perfil_base.php';
    if (file_exists($ruta_base)) {
        $contenido = file_get_contents($ruta_base);
        // Remover el código PHP para enviar solo HTML a la IA
        $contenido = preg_replace('/<\?php.*?\?>/s', '', $contenido);
        $contenido = preg_replace('/session_start\(\);/', '', $contenido);
        $contenido = preg_replace('/include_once.*?;/', '', $contenido);
        $contenido = preg_replace('/\$[a-zA-Z_].*?=.*?;/', '', $contenido);
        $contenido = preg_replace('/if\s*\(.*?\)\s*\{.*?\}/s', '', $contenido);
        $contenido = trim($contenido);
        return $contenido;
    }
    
    // Fallback: estructura base hardcodeada
    return '
    <!-- CONTENIDO BASE DEL PERFIL -->
    <div class="foto-portada">
        <img src="{foto_portada}" alt="Foto de portada" onerror="this.style.display=\'none\'">
    </div>

    <div class="cabecera">
        <div class="cont_img">
            <img src="{foto_perfil}" alt="foto perfil" onerror="this.src=\'./img/default.jpg\'">
        </div>
        <div class="datos_us">
            <div class="cont_name">
                <h3>{nombre_usuario}</h3>
            </div>
            <div class="cont_descripcion">
                <p>{biografia}</p>
            </div>
        </div>
    </div>

    <nav class="cont_nav_perfil">
        <ul class="nav-lista">
            <li><a href="index.php?modulo=perfil">Mi Perfil</a></li>
            <li><a href="index.php?modulo=cargar_productos">Publicar producto</a></li>
            <li><a href="./index.php">Inicio</a></li>
            <li><a href="index.php?modulo=asistente_perfil">Asistente IA ✨🖌️</a></li>
            <li class="menu-config">
                <a href="#" class="menu-trigger" onclick="toggleMenu(event)">
                    <i class="fa-solid fa-gear"></i>
                </a>
                <div class="menu-desplegable" id="menuConfig">
                    <button type="button" onclick="window.location.href=\'./index.php?modulo=editar_perfil\'">
                        Editar perfil
                    </button>
                    <form action="" method="POST">
                        <button type="submit" class="btn-cerrar-sesion">Cerrar Sesión</button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <div class="cont_productos">
        {productos}
    </div>

    <script>
    function toggleMenu(event) {
        event.preventDefault();
        const menu = document.getElementById("menuConfig");
        menu.classList.toggle("mostrar");
        
        const todosMenus = document.querySelectorAll(".menu-desplegable");
        todosMenus.forEach(m => {
            if (m !== menu) {
                m.classList.remove("mostrar");
            }
        });
    }

    document.addEventListener("click", function(event) {
        const menu = document.getElementById("menuConfig");
        const trigger = document.querySelector(".menu-trigger");
        
        if (menu && !menu.contains(event.target) && !trigger.contains(event.target)) {
            menu.classList.remove("mostrar");
        }
    });

    window.addEventListener("scroll", function() {
        const menu = document.getElementById("menuConfig");
        if (menu) {
            menu.classList.remove("mostrar");
        }
    });
    </script>';
}

function guardarNuevaVersionEstilo($id_usuario, $css_content, $html_content, $nombre_estilo = 'Nueva Versión', $descripcion = '') {
    global $con;
    
    // Obtener la última versión del usuario
    $query_ultima_version = "SELECT COALESCE(MAX(version), 0) as ultima_version 
                            FROM estilos_perfil 
                            WHERE id_usuario = ?";
    $stmt_version = $con->prepare($query_ultima_version);
    $stmt_version->bind_param("i", $id_usuario);
    $stmt_version->execute();
    $result_version = $stmt_version->get_result();
    $ultima_version = $result_version->fetch_assoc()['ultima_version'];
    $nueva_version = $ultima_version + 1;
    
    // ✅ CORRECCIÓN: Usar timestamp para evitar conflictos
    $timestamp = time();
    $ruta_css = "estilos/estilos_perfil_{$id_usuario}v{$nueva_version}_{$timestamp}.css";
    $ruta_html = "html_personalizado/perfil_html_{$id_usuario}v{$nueva_version}_{$timestamp}.php";
    
    // Guardar archivos
    if (!file_put_contents($ruta_css, $css_content)) {
        error_log("❌ Error al guardar archivo CSS: $ruta_css");
        return false;
    }
    
    if (!file_put_contents($ruta_html, $html_content)) {
        error_log("❌ Error al guardar archivo HTML: $ruta_html");
        // Eliminar el archivo CSS si falla el HTML
        if (file_exists($ruta_css)) {
            unlink($ruta_css);
        }
        return false;
    }
    
    // Primero desactivar todas las plantillas existentes del usuario
    $query_desactivar = "UPDATE estilos_perfil SET esta_activo = 0 WHERE id_usuario = ?";
    $stmt_desactivar = $con->prepare($query_desactivar);
    $stmt_desactivar->bind_param("i", $id_usuario);
    $stmt_desactivar->execute();
    
    // Insertar nueva versión en estilos_perfil (por defecto estará activa)
    $query_insert = "INSERT INTO estilos_perfil 
                    (id_usuario, nombre_estilo, ruta_css, ruta_html, version, descripcion, esta_activo) 
                    VALUES (?, ?, ?, ?, ?, ?, 1)";
    $stmt_insert = $con->prepare($query_insert);
    $stmt_insert->bind_param("isssis", $id_usuario, $nombre_estilo, $ruta_css, $ruta_html, $nueva_version, $descripcion);
    
    if ($stmt_insert->execute()) {
        $id_nuevo_estilo = $con->insert_id;
        
        // Actualizar también la tabla perfil
        $query_actualizar_perfil = "UPDATE perfil SET css_personalizado = ?, html_personalizado = ? WHERE id_usuario = ?";
        $stmt_actualizar_perfil = $con->prepare($query_actualizar_perfil);
        $stmt_actualizar_perfil->bind_param("ssi", $ruta_css, $ruta_html, $id_usuario);
        
        if ($stmt_actualizar_perfil->execute()) {
            error_log("✅ Nueva versión creada: v{$nueva_version} - ID: {$id_nuevo_estilo}");
            return $nueva_version;
        } else {
            // Si falla actualizar perfil, eliminar el estilo recién creado
            $query_eliminar = "DELETE FROM estilos_perfil WHERE id_estilo = ?";
            $stmt_eliminar = $con->prepare($query_eliminar);
            $stmt_eliminar->bind_param("i", $id_nuevo_estilo);
            $stmt_eliminar->execute();
            error_log("❌ Error al actualizar perfil, estilo eliminado");
            return false;
        }
    }
    
    error_log("❌ Error al insertar en estilos_perfil");
    return false;
}
?>