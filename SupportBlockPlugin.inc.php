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
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled($mainContextId)) {
            // Registrar el hook para agregar JavaScript y CSS
            Hook::add('TemplateManager::display', array($this, 'handleTemplateDisplay'));
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
		
		$request = Application::get()->getRequest();
		$context = $request->getContext();
		$user = $request->getUser();
		
		// Verificar que el usuario tiene rol de administrador o gestor
		if ($user) {
			$userRoleDao = \PKP\db\DAORegistry::getDAO('RoleDAO');
			$isAdmin = $userRoleDao->userHasRole(CONTEXT_SITE, $user->getId(), \PKP\security\Role::ROLE_ID_SITE_ADMIN);
			$isManager = ($context && $userRoleDao->userHasRole($context->getId(), $user->getId(), \PKP\security\Role::ROLE_ID_MANAGER));
			
			// Solo agregar recursos para administradores y gestores
			if ($isAdmin || $isManager) {
				$baseUrl = $request->getBaseUrl();
				$pluginPath = $this->getPluginPath();
				
				// Agregar JavaScript para insertar el menú
				$templateMgr->addJavaScript(
					'supportBlockScript',
					$baseUrl . '/' . $pluginPath . '/js/supportMenu.js',
					array('contexts' => 'backend')
				);
				
				// Agregar estilos CSS
				$templateMgr->addStyleSheet(
					'supportBlockStyles',
					$baseUrl . '/' . $pluginPath . '/css/supportMenu.css',
					array('contexts' => 'backend')
				);
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