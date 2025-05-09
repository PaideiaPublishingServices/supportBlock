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
use APP\core\Application;
use APP\facades\Repo;
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
		
		$contextId = $request->getContext() ? $request->getContext()->getId() : CONTEXT_SITE;
		$hasRole = false;
		
		// Comprobar si el usuario tiene rol de administrador del sitio
		$hasRole = Repo::user()->userHasRole($user->getId(), Role::ROLE_ID_SITE_ADMIN, CONTEXT_SITE);
		
		// Si no es administrador del sitio, comprobar si es gestor
		if (!$hasRole && $contextId != CONTEXT_SITE) {
			$hasRole = Repo::user()->userHasRole($user->getId(), Role::ROLE_ID_MANAGER, $contextId);
		}
		
		// Solo mostrar el bloque si el usuario tiene uno de los roles necesarios
		if (!$hasRole) return '';
		
		// Cargar la plantilla del bloque
		$templateMgr->assign([
			'supportUrl' => 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3'
		]);
		
		return parent::getContents($templateMgr, $request);
	}
}
