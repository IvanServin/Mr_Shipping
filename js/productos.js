// Hacer las tarjetas de productos clickeables
function inicializarProductos() {
    // Seleccionar todas las tarjetas de productos
    const tarjetasProductos = document.querySelectorAll('.tarjeta');
    
    console.log('Tarjetas encontradas:', tarjetasProductos.length); // Debug
    
    // Agregar evento click a cada tarjeta
    tarjetasProductos.forEach(tarjeta => {
        // Agregar estilo de cursor pointer
        tarjeta.style.cursor = 'pointer';
        
        tarjeta.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn') || e.target.closest('.btn')) {
                console.log('Click en botón, ignorando click de tarjeta');
                return;
            }
            
            // Obtener el ID del producto
            const productId = this.getAttribute('data-product-id');
            console.log('Click en tarjeta, producto ID:', productId);
            
            // Redirigir a la página del producto
            if (productId) {
                window.location.href = `index.php?modulo=producto&id=${productId}`;
            }
        });
    });

    const botonesComprar = document.querySelectorAll('.tarjeta .btn');
    botonesComprar.forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Evitar que se active el click de la tarjeta
            
            const tarjeta = this.closest('.tarjeta');
            const productId = tarjeta.getAttribute('data-product-id');
            
            console.log('Click en botón comprar, producto ID:', productId);
            
            // redirigir directamente a compra o hacer otra acción
            if (productId) {
                window.location.href = `index.php?modulo=comprar&producto=${productId}`;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    inicializarProductos();
});

if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                // Verificar si se agregaron tarjetas de productos
                const nuevasTarjetas = document.querySelectorAll('.tarjeta');
                if (nuevasTarjetas.length > 0) {
                    inicializarProductos();
                }
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}