/**
 * @file plugins/blocks/supportBlock/js/supportMenu.js
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * @brief Script para insertar el menú de soporte en el panel de administración
 */

(function() {
    // Función para crear el elemento de soporte
    function createSupportItem() {
        console.log('SupportBlock: Intentando insertar el menú de soporte...');
        
        // Intentar encontrar el menú de navegación principal
        const navMenu = document.querySelector('.app__nav');
        if (!navMenu) {
            console.log('SupportBlock: No se encontró el menú de navegación principal');
            return false;
        }
        
        // URL de soporte (debería ser inyectada por PHP, pero la hardcodeamos para prueba)
        const supportUrl = 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3';
        
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
    
    // Función para intentar varias veces
    function tryInsertSupport() {
        let attempts = 0;
        const maxAttempts = 5;
        const interval = setInterval(function() {
            if (createSupportItem() || attempts >= maxAttempts) {
                clearInterval(interval);
                console.log('SupportBlock: Intento finalizado después de ' + attempts + ' intentos');
            }
            attempts++;
        }, 1000);
    }
    
    // Intentar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tryInsertSupport);
    } else {
        tryInsertSupport();
    }
})();