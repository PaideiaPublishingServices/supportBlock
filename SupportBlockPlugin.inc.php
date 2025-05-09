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
 * @brief Plugin para añadir un bloque de soporte técnico en el sidebar
 */

namespace APP\plugins\blocks\supportBlock;

use PKP\plugins\BlockPlugin;
use PKP\db\DAORegistry;
use PKP\security\Role;
use APP\core\Application;
use PKP\core\Registry;
use PKP\config\Config;
use PKP\facade\HookRegistry;

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
			// Registrar hook para cargar el CSS
			HookRegistry::register('TemplateManager::display', array($this, 'loadStylesheet'));
            
			// Registrar hook para el panel de administración
			HookRegistry::register('Templates::Management::Settings::sidebar', array($this, 'insertSupportBlock'));
			HookRegistry::register('Templates::Common::Header::Navbar::MainMenu', array($this, 'insertSupportBlock'));
		}
		return $success;
	}
    
	/**
	 * Load the plugin's CSS file
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function loadStylesheet($hookName, $args) {
		$templateMgr = $args[0];
		$request = Application::get()->getRequest();
		
		$templateMgr->addStyleSheet(
			'supportBlockStyles',
			$request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/supportBlock.css',
			array('contexts' => array('backend'))
		);
		
		return false;
	}
    
	/**
	 * Insert the support block in admin area
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function insertSupportBlock($hookName, $args) {
		$templateMgr = $args[1];
		$output = &$args[2];
		
		$request = Application::get()->getRequest();
		$dispatcher = $request->getDispatcher();
		$context = $request->getContext();
		$user = $request->getUser();
		
		if (!$user) return false;
		
		// Verificar que el usuario tiene rol de administrador o gestor
		$userRoleDao = DAORegistry::getDAO('RoleDAO');
		
		$isAdmin = $userRoleDao->userHasRole(CONTEXT_SITE, $user->getId(), Role::ROLE_ID_SITE_ADMIN);
		$isManager = ($context && $userRoleDao->userHasRole($context->getId(), $user->getId(), Role::ROLE_ID_MANAGER));
		
		if (!$isAdmin && !$isManager) return false;
		
		$supportUrl = 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3';
		
		$templateMgr->assign(array(
			'supportUrl' => $supportUrl
		));
		
		$output .= $templateMgr->fetch($this->getTemplateResource('supportBlockBackend.tpl'));
		
		return false;
	}

	/**
	 * @copydoc BlockPlugin::getContents
	 */
	function getContents($templateMgr, $request = null) {
		// No mostrar en el frontend
		return '';
	}
    
	/**
 * Get the template resource for this plugin.
 * @param $template string|null
 * @param $inCore boolean
 * @return string
 */
	function getTemplateResource($template = null, $inCore = false) {
		if ($inCore) {
			return parent::getTemplateResource($template, $inCore);
		}
		return $this->getPluginPath() . '/templates/' . $template;
	}
}