/**
 * @file plugins/blocks/supportBlock/js/supportMenu.js
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * @brief Script para insertar el menú de soporte en el panel de administración
 */

(function() {
    // URL de soporte
    const supportUrl = 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3';
    
    // Función para crear el elemento de soporte
    function createSupportItem() {
        console.log('SupportBlock: Intentando insertar el menú de soporte...');
        
        // Intentar encontrar el menú de navegación principal
        const navMenu = document.querySelector('.app__nav');
        if (!navMenu) {
            console.log('SupportBlock: No se encontró el menú de navegación principal');
            return false;
        }
        
        // Verificar si ya existe el elemento de soporte para evitar duplicados
        if (document.querySelector('.support-menu-item')) {
            console.log('SupportBlock: El menú de soporte ya existe');
            return true;
        }
        
        // Crear el elemento de menú
        const supportItem = document.createElement('li');
        supportItem.className = 'support-menu-item';
        supportItem.innerHTML = `
            <a href="${supportUrl}" target="_blank" class="support-menu-link">
                <span class="fa fa-life-ring support-icon"></span>
                <span class="label">Soporte</span>
            </a>
        `;
        
        // Agregar al menú de navegación
        navMenu.appendChild(supportItem);
        console.log('SupportBlock: Menú de soporte insertado correctamente');
        
        return true;
    }
    
    // Función principal para monitorear cambios y insertar el menú
    function initSupportMenu() {
        // Intentar insertar inmediatamente
        createSupportItem();
        
        // También configurar un intervalo para intentar periódicamente
        // Esto es útil para aplicaciones SPA que pueden recargar el DOM
        setInterval(createSupportItem, 2000);
        
        // Monitorear cambios en la URL para SPA
        let lastUrl = location.href;
        const urlObserver = new MutationObserver(function() {
            if (lastUrl !== location.href) {
                lastUrl = location.href;
                setTimeout(createSupportItem, 500);
            }
        });
        
        urlObserver.observe(document, { subtree: true, childList: true });
        
        // Monitorear cambios en el DOM específicamente para el menú de navegación
        const navObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    for (let i = 0; i < mutation.addedNodes.length; i++) {
                        const node = mutation.addedNodes[i];
                        if (node.classList && (node.classList.contains('app__nav') || 
                            (node.querySelector && node.querySelector('.app__nav')))) {
                            setTimeout(createSupportItem, 100);
                            return;
                        }
                    }
                }
            });
        });
        
        navObserver.observe(document.body, { childList: true, subtree: true });
        
        console.log('SupportBlock: Sistema de monitoreo inicializado');
    }
    
    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSupportMenu);
    } else {
        initSupportMenu();
    }
})();