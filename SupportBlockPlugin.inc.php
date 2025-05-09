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
            
            // Solo agregar recursos para administradores y gestores en el contexto "backend"
            if (($isAdmin || $isManager) && $this->_isBackendContext($request, $template)) {
                $baseUrl = $request->getBaseUrl();
                $pluginPath = $this->getPluginPath();
                
                // Agregar JavaScript para insertar el menú
                $templateMgr->addJavaScript(
                    'supportBlockScript',
                    $baseUrl . '/' . $pluginPath . '/js/supportMenu.js',
                    ['contexts' => ['backend']]
                );
                
                // Agregar estilos CSS
                $templateMgr->addStyleSheet(
                    'supportBlockStyles',
                    $baseUrl . '/' . $pluginPath . '/css/supportMenu.css',
                    ['contexts' => ['backend']]
                );
                
                error_log("SupportBlock: Script y estilos añadidos para la plantilla: $template");
            }
        }
        
        return false;
    }

    /**
     * Determine if we're in a backend context
     * @param $request Request
     * @param $template string Template name
     * @return boolean
     */
    private function _isBackendContext($request, $template) {
        // Comprobar si estamos en una ruta del panel de administración
        $requestPath = $request->getRequestPath();
        if (strpos($requestPath, '/management/') !== false || 
            strpos($requestPath, '/submissions') !== false || 
            strpos($requestPath, '/workflow') !== false || 
            strpos($requestPath, '/stats/') !== false) {
            return true;
        }
        
        // Alternativamente, comprobar si la plantilla es del backend
        if (strpos($template, 'management/') === 0 || 
            strpos($template, 'dashboard/') === 0 || 
            strpos($template, 'workflow/') === 0 || 
            strpos($template, 'stats/') === 0) {
            return true;
        }
        
        // Una verificación adicional basada en el contexto actual
        $router = $request->getRouter();
        if ($router && method_exists($router, 'getHandler')) {
            $handler = $router->getHandler();
            if ($handler && (is_a($handler, 'ManagerHandler') || 
                            is_a($handler, 'DashboardHandler') || 
                            is_a($handler, 'WorkflowHandler') || 
                            is_a($handler, 'StatisticsHandler'))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * @copydoc BlockPlugin::getContents
     */
    function getContents($templateMgr, $request = null) {
        // No mostrar en el frontend
        return '';
    }
}