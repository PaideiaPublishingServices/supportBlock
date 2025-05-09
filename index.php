<?php

/**
 * @defgroup plugins_blocks_supportBlock Support Block Plugin
 */

/**
 * @file plugins/blocks/supportBlock/index.php
 *
 * Copyright (c) 2023-2025 Paideia Studio
 * Distributed under the GNU GPL v3.
 *
 * @ingroup plugins_blocks_supportBlock
 * @brief Wrapper para el plugin de bloque de soporte.
 *
 */

require_once('SupportBlockPlugin.inc.php');

return new \APP\plugins\blocks\supportBlock\SupportBlockPlugin();

?>
