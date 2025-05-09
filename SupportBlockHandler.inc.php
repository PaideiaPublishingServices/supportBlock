<?php
/**
 * @file plugins/blocks/supportBlock/SupportBlockHandler.inc.php
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * @class SupportBlockHandler
 * @ingroup plugins_blocks_supportBlock
 *
 * @brief Handler para el plugin de bloque de soporte
 */

namespace APP\plugins\blocks\supportBlock;

use PKP\handler\PKPHandler;
use APP\core\Application;

class SupportBlockHandler extends PKPHandler {
    /** @var SupportBlockPlugin El plugin */
    var $plugin;

    /**
     * Constructor
     * @param $plugin SupportBlockPlugin
     */
    function __construct($plugin) {
        $this->plugin = $plugin;
    }
}