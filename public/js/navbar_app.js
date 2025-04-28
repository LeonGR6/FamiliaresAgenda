



//Script para efectos hover y detección de página activa//



    document.addEventListener('DOMContentLoaded', function() {
        // Obtener la ruta actual sin parámetros
        const currentPath = window.location.pathname.split('?')[0];
        
        // Seleccionar todos los elementos del menú
        const menuItems = document.querySelectorAll('.nav-link, .dropdown-item');
        
        // Recorrer todos los elementos del menú
        menuItems.forEach(item => {
            if (item.href) {
                const itemPath = new URL(item.href).pathname;
                
                // Comparar las rutas
                if (currentPath === itemPath || 
                    (itemPath !== '/' && currentPath.startsWith(itemPath))) {
                    
                    // Marcar el elemento como activo
                    item.classList.add('active');
                    
                    // Si es un dropdown-item, marcar también el dropdown padre
                    if (item.classList.contains('dropdown-item')) {
                        const dropdownId = item.closest('.dropdown-menu').getAttribute('aria-labelledby');
                        if (dropdownId) {
                            document.getElementById(dropdownId).classList.add('active');
                        }
                    }
                }
            }
        });
        
        // Caso especial para la página de inicio
       
    });
