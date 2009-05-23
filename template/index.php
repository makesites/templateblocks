<?php
/**
 * TEMPLATE BLOCKS
 * Copyright (C) 2008 Makis Tracend
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * http://www.gnu.org/licenses/gpl-2.0.txt
 */

// get the configuration file
require( dirname(__FILE__) . '/config.php' );

// load all the classes we'll use
require( dirname(__FILE__) . '/classes/Common.php' );
require( dirname(__FILE__) . '/classes/Database.php' );
require( dirname(__FILE__) . '/classes/Website.php' );

$template_blocks = new Website();
$template_blocks->section = $GLOBALS['section'];
$template_blocks->parents = $GLOBALS['parents'];

$template_blocks->showPage();

unset($template_blocks);


?>
