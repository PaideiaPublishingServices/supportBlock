/**
 * @file plugins/blocks/supportBlock/js/supportMenu.js
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * @brief Script para insertar el enlace de soporte técnico
 */

(function() {
    // URL de soporte
    const supportUrl = 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3';
    
    // ID único para evitar múltiples inicializaciones
    const SUPPORT_INITIALIZED_KEY = 'paideia_support_initialized';
    
    // Si ya se inicializó, salir
    if (window[SUPPORT_INITIALIZED_KEY]) return;
    window[SUPPORT_INITIALIZED_KEY] = true;
    
    // Intentar ambos métodos: menú lateral y cabecera
    function createSupportElements() {
        // Intentar insertar en la cabecera (nueva ubicación)
        tryInsertHeaderSupport();
        
        // También intentar el método original (menú lateral)
        tryInsertMenuSupport();
    }
    
    // Función para insertar soporte en la cabecera
    function tryInsertHeaderSupport() {
        // Buscar el área de header donde están los iconos (campana y usuario)
        const headerActions = document.querySelector('.app__header .app__headerActions');
        
        if (!headerActions) {
            return false;
        }
        
        // Verificar si ya existe
        if (document.querySelector('.support-header-item')) {
            return true;
        }
        
        // Crear el elemento de soporte para la cabecera
        const supportItem = document.createElement('div');
        supportItem.className = 'support-header-item';
        supportItem.innerHTML = `
            <a href="${supportUrl}" target="_blank" class="support-header-link" title="Soporte Técnico">
                <span class="fa fa-life-ring support-header-icon"></span>
            </a>
        `;
        
        // Insertar al inicio de las acciones de cabecera
        headerActions.insertBefore(supportItem, headerActions.firstChild);
        console.log('SupportBlock: Elemento de soporte añadido a la cabecera');
        
        return true;
    }
    
    // Función original para insertar en el menú lateral
    function tryInsertMenuSupport() {
        // Intentar encontrar el menú de navegación principal
        const navMenu = document.querySelector('.app__nav');
        if (!navMenu) {
            return false;
        }
        
        // Verificar si ya existe
        if (document.querySelector('.support-menu-item')) {
            return true;
        }
        
        // Crear el elemento para el menú
        const supportItem = document.createElement('li');
        supportItem.className = 'support-menu-item';
        supportItem.innerHTML = `
            <a href="${supportUrl}" target="_blank" class="support-menu-link">
                <span class="fa fa-life-ring support-icon"></span>
                <span class="label">Soporte</span>
            </a>
        `;
        
        // Agregar al menú
        navMenu.appendChild(supportItem);
        console.log('SupportBlock: Elemento de soporte añadido al menú lateral');
        
        return true;
    }
    
    // Configurar monitoreo continuo para detectar cambios en el DOM
    function setupMonitoring() {
        // Intentar insertar inmediatamente
        createSupportElements();
        
        // Configurar intervalo para verificar periódicamente (cada 2 segundos)
        setInterval(createSupportElements, 2000);
        
        // Observar cambios en el DOM para insertar tan pronto como sea posible
        const observer = new MutationObserver(function() {
            createSupportElements();
        });
        
        // Observar todo el documento
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Detectar cambios de URL (navegación SPA)
        let lastUrl = location.href;
        setInterval(function() {
            if (lastUrl !== location.href) {
                lastUrl = location.href;
                setTimeout(createSupportElements, 500);
            }
        }, 500);
        
        console.log('SupportBlock: Sistema de monitoreo inicializado');
    }
    
    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupMonitoring);
    } else {
        setupMonitoring();
    }
    
    // También intentar cuando la ventana termine de cargar
    window.addEventListener('load', function() {
        setTimeout(createSupportElements, 500);
    });
})();