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
 *
 * Template Blocks: Administration
 */


// get the configuration file
require( 'config.php' );

// redirect if there is a setup folder 
if (file_exists('setup') && !$_REQUEST['del-setup'] ) {
	header('Refresh: 0;url=setup/index.php');
	exit;
}

// load all the classes we'll use
require( 'classes/Database.php' );
require( 'classes/Common.php' );
require( 'classes/Admin.php' );

$template_blocks = new Admin();
$template_blocks->showPage();

unset($template_blocks);


?>