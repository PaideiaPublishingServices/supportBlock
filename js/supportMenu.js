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
        
        // Verificar si ya existe el elemento de soporte para evitar duplicados
        if (document.querySelector('.support-menu-item')) {
            console.log('SupportBlock: El menú de soporte ya existe');
            return true;
        }
        
        // URL de soporte
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
    
    // Función para intentar varias veces con un intervalo
    function tryInsertSupport() {
        let attempts = 0;
        const maxAttempts = 10;
        const interval = setInterval(function() {
            if (createSupportItem() || attempts >= maxAttempts) {
                clearInterval(interval);
                console.log('SupportBlock: Intento finalizado después de ' + attempts + ' intentos');
            }
            attempts++;
        }, 500); // Intentar cada 500ms, hasta 10 veces (5 segundos total)
    }
    
    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tryInsertSupport);
    } else {
        tryInsertSupport();
    }
    
    // También intentar cuando la página cambie mediante AJAX
    if (typeof(document.pjax) !== 'undefined') {
        document.pjax.on('pjax:success', tryInsertSupport);
    }
})();