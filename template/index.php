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
require( 'config.php' );

// don't accept direct requests
if( eregi($config['Template_dir'].'/index.php', $_SERVER['PHP_SELF'])) {
	header('Refresh: 0;url=/index.php');
	exit;
}

// redirect if there is a setup folder 
if (file_exists($config['Template_dir'].'/setup')) {
	header('Refresh: 0;url='.$config['Template_dir'].'/setup/index.php');
	exit;
}

// load all used classes
require( $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/admin/classes/Database.php' );
require( $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/admin/classes/Common.php' );
require( $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/admin/classes/Website.php' );

$template_blocks = new Website();
$template_blocks->section = $GLOBALS['section'];
$template_blocks->parents = $GLOBALS['parents'];

$template_blocks->showPage();

unset($template_blocks);


?>
