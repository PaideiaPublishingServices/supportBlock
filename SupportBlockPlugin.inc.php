<?php
/**
 * @file plugins/blocks/supportBlock/SupportBlockPlugin.inc.php
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * @class SupportBlockPlugin
 * @ingroup plugins_blocks_supportBlock
 *
 * @brief Plugin para añadir un enlace de soporte técnico en el menú lateral
 */

namespace APP\plugins\blocks\supportBlock;

use PKP\plugins\BlockPlugin;
use APP\core\Application;
use PKP\plugins\Hook;
use PKP\db\DAORegistry;
use PKP\security\Role;

class SupportBlockPlugin extends BlockPlugin {
    /**
     * Get the display name of this plugin.
     * @return String
     */
    function getDisplayName() {
        return __('plugins.blocks.supportBlock.displayName');
    }

    /**
     * Get a description of the plugin.
     * @return String
     */
    function getDescription() {
        return __('plugins.blocks.supportBlock.description');
    }

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null) {
        error_log("SupportBlock: Registrando hooks");
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled($mainContextId)) {
            // Verificar si HookRegistry está disponible
            if (class_exists('\PKP\plugins\Hook')) {
                error_log("SupportBlock: HookRegistry existe, intentando registrar hooks antiguos");
                // Registrar el hook para agregar JavaScript y CSS
                Hook::add('TemplateManager::display', array($this, 'handleTemplateDisplay'));
                // También registramos un hook específico para el layouto backend
                Hook::add('Templates::Common::Header::Wrapper', array($this, 'injectSupportResources'));
                error_log("SupportBlock: Hooks registrados");
            } else {
                error_log("SupportBlock: No se pudo encontrar HookRegistry");
            }
        }
        return $success;
    }

    /**
     * Hook callback: add JavaScript and CSS to insert the support menu
     * @param $hookName string
     * @param $args array
     * @return boolean
     */
    function handleTemplateDisplay($hookName, $args) {
        $templateMgr = $args[0];
        $template = $args[1] ?? '';
        
        error_log("SupportBlock: Plantilla solicitada: $template");
        
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        $user = $request->getUser();
        
        // Verificar que el usuario tiene rol de administrador o gestor
        if ($user) {
            $userRoleDao = \PKP\db\DAORegistry::getDAO('RoleDAO');
            $isAdmin = $userRoleDao->userHasRole(CONTEXT_SITE, $user->getId(), \PKP\security\Role::ROLE_ID_SITE_ADMIN);
            $isManager = ($context && $userRoleDao->userHasRole($context->getId(), $user->getId(), \PKP\security\Role::ROLE_ID_MANAGER));
            
            // Solo agregar recursos para administradores y gestores
            if (($isAdmin || $isManager)) {
                // Esto asegura que aunque la plantilla sea diferente, vamos a añadir los recursos
                // siempre que estemos en un contexto de backend
                $this->addSupportResourcesIfBackend($templateMgr, $request, $template);
            }
        }
        
        return false;
    }
    
    /**
     * Hook callback para inyectar recursos en cualquier plantilla del backend
     * @param $hookName string
     * @param $params array
     * @return boolean
     */
    function injectSupportResources($hookName, $params) {
        $templateMgr = $params[0] ?? null;
        $output = &$params[1];
        
        $request = Application::get()->getRequest();
        $user = $request->getUser();
        
        if ($user && $templateMgr) {
            $context = $request->getContext();
            $userRoleDao = \PKP\db\DAORegistry::getDAO('RoleDAO');
            $isAdmin = $userRoleDao->userHasRole(CONTEXT_SITE, $user->getId(), \PKP\security\Role::ROLE_ID_SITE_ADMIN);
            $isManager = ($context && $userRoleDao->userHasRole($context->getId(), $user->getId(), \PKP\security\Role::ROLE_ID_MANAGER));
            
            if ($isAdmin || $isManager) {
                $baseUrl = $request->getBaseUrl();
                $pluginPath = $this->getPluginPath();
                
                // Injección directa de los scripts en el HTML
                $scriptUrl = $baseUrl . '/' . $pluginPath . '/js/supportMenu.js';
                $cssUrl = $baseUrl . '/' . $pluginPath . '/css/supportMenu.css';
                
                $output .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$cssUrl}\" />\n";
                $output .= "<script type=\"text/javascript\" src=\"{$scriptUrl}\"></script>\n";
                
                error_log("SupportBlock: Recursos inyectados directamente en el HTML");
            }
        }
        
        return false;
    }

    /**
     * Helper method to add support resources if we're in a backend context
     * @param $templateMgr TemplateManager
     * @param $request Request
     * @param $template string Template name
     */
    private function addSupportResourcesIfBackend($templateMgr, $request, $template) {
        // Siempre añadimos los recursos, independientemente de la plantilla
        // Esta es la solución más segura para asegurar que nuestro script esté disponible
        $baseUrl = $request->getBaseUrl();
        $pluginPath = $this->getPluginPath();
        
        // Agregar JavaScript para insertar el menú
        $templateMgr->addJavaScript(
            'supportBlockScript',
            $baseUrl . '/' . $pluginPath . '/js/supportMenu.js',
            ['contexts' => ['backend'], 'priority' => STYLE_SEQUENCE_LAST]
        );
        
        // Agregar estilos CSS
        $templateMgr->addStyleSheet(
            'supportBlockStyles',
            $baseUrl . '/' . $pluginPath . '/css/supportMenu.css',
            ['contexts' => ['backend'], 'priority' => STYLE_SEQUENCE_LAST]
        );
        
        error_log("SupportBlock: Script y estilos añadidos para la plantilla: $template");
    }

    /**
     * @copydoc BlockPlugin::getContents
     */
    function getContents($templateMgr, $request = null) {
        // No mostrar en el frontend
        return '';
    }
}