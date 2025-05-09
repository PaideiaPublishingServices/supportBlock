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
use PKP\plugins\Hook;

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
			error_log("SupportBlock: Registrando hooks");
			
			// Probar diferentes formas de llamar a los hooks en OJS 3.4
			// Usando Hook::add
			Hook::add('Templates::Management::Settings::sidebar', array($this, 'insertSupportBlock'));
			Hook::add('Templates::Admin::Index::AdminFunctions', array($this, 'insertSupportBlock'));
			Hook::add('Templates::Common::Sidebar', array($this, 'insertSupportBlock'));
			Hook::add('Template::Management::leftSidebar', array($this, 'insertSupportBlock'));
			
			// Intentar también con la versión anterior para compatibilidad
			if (class_exists('\HookRegistry')) {
				error_log("SupportBlock: HookRegistry existe, intentando registrar hooks antiguos");
				\HookRegistry::register('Templates::Management::Settings::sidebar', array($this, 'insertSupportBlock'));
				\HookRegistry::register('Templates::Common::Sidebar', array($this, 'insertSupportBlock'));
				\HookRegistry::register('Templates::Management::leftSidebar', array($this, 'insertSupportBlock'));
			}
			
			// Agregar más hooks potenciales
			Hook::add('Templates::Management::Settings::management', array($this, 'insertSupportBlock'));
			Hook::add('Templates::Common::Header::Navbar::MainMenu', array($this, 'insertSupportBlock'));
			Hook::add('Templates::User::Navigation::User', array($this, 'insertSupportBlock'));
			// Agregar un hook para detectar todos los hooks que se están llamando
			Hook::add('*', function($hookName, $args) {
				error_log("OJS Hook detectado: " . $hookName);
				return false;
			});
			
			// Probar hook directo a la plantilla principal
			Hook::add('TemplateManager::display', array($this, 'handleTemplateDisplay'));
			
			error_log("SupportBlock: Hooks registrados");
		}
		return $success;
	}

	/**
	 * Hook callback: register output filter to add sidebar link to
	 * management dashboard pages
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function handleTemplateDisplay($hookName, $args) {
		$templateMgr = $args[0];
		$template = $args[1];
		
		error_log("SupportBlock: Plantilla solicitada: " . $template);
		
		// Si estamos en una página de administración o gestión
		if (strpos($template, 'management') !== false || 
			strpos($template, 'admin') !== false || 
			strpos($template, 'settings') !== false) {
			
			// Añadir una referencia al estilo CSS
			$templateMgr->addStyleSheet(
				'supportBlockStyles',
				$this->getRequest()->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/supportBlock.css',
				array('contexts' => array('backend'))
			);
			
			// Si estamos en una página específica, intentar insertar directamente
			if (strpos($template, 'pkp/templates/management/context.tpl') !== false ||
				strpos($template, 'pkp/templates/management/settings/workflow.tpl') !== false) {
				
				$supportUrl = 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3';
				$templateMgr->assign('supportUrl', $supportUrl);
				
				// Agregar contenido directamente al sidebar
				$output = $templateMgr->fetch($this->getTemplateResource('supportBlockAdmin.tpl'));
				$templateMgr->assign('supportBlockOutput', $output);
				
				error_log("SupportBlock: Contenido agregado directamente a la plantilla: " . $template);
			}
		}
		
		return false;
	}
    
    /**
	 * Insert the support block in admin area
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function insertSupportBlock($hookName, $args) {
		// Agregar log para ver qué hook se está llamando
		error_log("SupportBlock: Hook llamado: " . $hookName);
		
		$templateMgr = $args[0] ?? $args[1] ?? null;
		$output = &$args[1] ?? $args[2] ?? null;
		
		if (!$templateMgr) {
			error_log("SupportBlock: TemplateMgr no encontrado en los argumentos");
			return false;
		}
		
		$request = Application::get()->getRequest();
		$context = $request->getContext();
		$user = $request->getUser();
		
		if (!$user) {
			error_log("SupportBlock: Usuario no encontrado");
			return false;
		}
		
		// Verificar que el usuario tiene rol de administrador o gestor
		$userRoleDao = DAORegistry::getDAO('RoleDAO');
		$isAdmin = $userRoleDao->userHasRole(CONTEXT_SITE, $user->getId(), Role::ROLE_ID_SITE_ADMIN);
		$isManager = ($context && $userRoleDao->userHasRole($context->getId(), $user->getId(), Role::ROLE_ID_MANAGER));
		
		if (!$isAdmin && !$isManager) {
			error_log("SupportBlock: Usuario no es admin o gestor");
			return false;
		}
		
		$supportUrl = 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3';
		
		// Agregar log para verificar si estamos llegando a este punto
		error_log("SupportBlock: Asignando supportUrl y renderizando plantilla");
		
		$templateMgr->assign(array(
			'supportUrl' => $supportUrl
		));
		
		// Registrar la plantilla que se está intentando cargar
		error_log("SupportBlock: Intentando cargar plantilla: " . $this->getTemplateResource('supportBlockAdmin.tpl'));
		
		$output .= $templateMgr->fetch($this->getTemplateResource('supportBlockAdmin.tpl'));
		
		// Agregar log para verificar si el contenido se agregó al output
		error_log("SupportBlock: Plantilla renderizada y agregada al output");
		
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