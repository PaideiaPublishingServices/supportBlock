/**
 * @file plugins/blocks/supportBlock/SupportBlockPlugin.inc.php
 *
 * Copyright (c) 2023 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * @class SupportBlockPlugin
 * @ingroup plugins_blocks_supportBlock
 *
 * @brief Plugin para añadir un bloque de soporte técnico en el sidebar
 */

import('lib.pkp.classes.plugins.BlockPlugin');

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
	 * Get the plugin path.
	 * @return String
	 */
	function getPluginPath() {
		return $this->getPluginPath();
	}

	/**
	 * Determine whether the plugin can be enabled.
	 * @return boolean
	 */
	function getCanEnable() {
		return true;
	}

	/**
	 * Determine whether the plugin should be hidden.
	 * @return boolean
	 */
	function getHideManagement() {
		return false;
	}

	/**
	 * Get the HTML contents for this block.
	 * @param $templateMgr PKPTemplateManager
	 * @return $string
	 */
	function getContents($templateMgr, $request = null) {
		$user = $request->getUser();
		
		// Verificar si el usuario tiene rol de administrador o gestor
		if (!$user) return '';
		
		$userRoleDao = DAORegistry::getDAO('RoleDAO');
		$roleIds = array(ROLE_ID_MANAGER, ROLE_ID_SITE_ADMIN);
		$hasRole = false;
		
		foreach ($roleIds as $roleId) {
			$hasRole = $userRoleDao->userHasRole(
				$request->getContext()->getId(),
				$user->getId(),
				$roleId
			);
			if ($hasRole) break;
		}
		
		// Solo mostrar el bloque si el usuario tiene uno de los roles necesarios
		if (!$hasRole) return '';
		
		// Cargar la plantilla del bloque
		$templateMgr->assign(array(
			'supportUrl' => 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3'
		));
		
		return parent::getContents($templateMgr, $request);
	}
}
