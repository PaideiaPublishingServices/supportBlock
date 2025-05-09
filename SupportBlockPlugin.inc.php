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
            // Registrar hook para el menú lateral de administración
            Hook::add('Templates::Management::Settings::sidebar', array($this, 'insertSupportBlock'));
            
            // Registrar hooks adicionales para el menú lateral en diferentes áreas
            Hook::add('Templates::Admin::Index::AdminFunctions', array($this, 'insertSupportBlock'));
            Hook::add('Templates::Common::Sidebar', array($this, 'insertSupportBlock'));
            
            // Este es un hook adicional para el menú lateral izquierdo de administración
            Hook::add('Template::Management::leftSidebar', array($this, 'insertSupportBlock'));
        }
        return $success;
    }
    
    /**
     * Insert the support block in admin area
     * @param $hookName string
     * @param $args array
     * @return boolean
     */
    function insertSupportBlock($hookName, $args) {
        $templateMgr = $args[0] ?? $args[1] ?? null;
        $output = &$args[1] ?? $args[2] ?? null;
        
        if (!$templateMgr) return false;
        
        $request = Application::get()->getRequest();
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
        
        $output .= $templateMgr->fetch($this->getTemplateResource('supportBlockAdmin.tpl'));
        
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