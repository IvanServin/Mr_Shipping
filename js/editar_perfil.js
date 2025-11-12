// Sistema de tabs
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
        // Remover clase active de todos los botones y contenidos
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        // Agregar clase active al botón clickeado
        button.classList.add('active');
        
        // Mostrar el contenido correspondiente
        const tabId = button.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
    });
});

// Vista previa para foto de perfil
document.getElementById('foto_perfil').addEventListener('change', function(e) {
    const preview = document.getElementById('preview_perfil');
    const file = e.target.files[0];
    
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
});

// Vista previa para foto de portada
document.getElementById('foto_portada').addEventListener('change', function(e) {
    const preview = document.getElementById('preview_portada');
    const file = e.target.files[0];
    
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
});

// Contador de caracteres para biografía
document.getElementById('biografia').addEventListener('input', function(e) {
    const contador = document.getElementById('contador_biografia');
    contador.textContent = e.target.value.length + ' caracteres';
});

// Drag and drop functionality
function setupDragDrop(inputId, labelId) {
    const input = document.getElementById(inputId);
    const label = document.getElementById(labelId);
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        label.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        label.addEventListener(eventName, () => label.classList.add('dragover'), false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        label.addEventListener(eventName, () => label.classList.remove('dragover'), false);
    });
    
    label.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        input.files = files;
        
        // Trigger change event
        const event = new Event('change');
        input.dispatchEvent(event);
    });
}

setupDragDrop('foto_perfil', 'label_perfil');
setupDragDrop('foto_portada', 'label_portada');