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
 * @brief Plugin para añadir un enlace de soporte técnico en el header del backend
 */

namespace APP\plugins\blocks\supportBlock;

use PKP\plugins\BlockPlugin;
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
     * Override la ruta del template
     */
    function getTemplateResource($template) {
        if ($template === 'layouts/backend.tpl') {
            return dirname(__FILE__) . '/templates/layouts/backend.tpl';
        }
        return parent::getTemplateResource($template);
    }

    /**
     * @copydoc BlockPlugin::getContents
     */
    function getContents($templateMgr, $request = null) {
        // No mostrar en el frontend
        return '';
    }
}