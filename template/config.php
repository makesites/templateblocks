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


global $config;

// Database properties (currently works only with MySQL)
$config['Database_server'] = 'localhost';
$config['Database_name'] = 'template_blocks';
$config['Database_user'] = 'root';
$config['Database_password'] = '<Enter Password>';

// Table names
$config['Database_table_prefix'] = 'template_';

$config['Sections'] = $config['Database_table_prefix'] . 'sections';
$config['Templates'] = $config['Database_table_prefix'] . 'templates';
$config['Blocks'] = $config['Database_table_prefix'] . 'blocks';

// The folder where the Template Blocks files are located - no trailing slash please...
$config['Template_dir'] = 'template';

// Title of the website...
$config['Website'] = 'Website Title';

// Full URL path to the favicon - delete if none...
$config['Favicon'] = '/favicon.ico';

// Full URL pathto RSS feed - delete if none...
$config['Rss_feed'] = '/rss.xml';

// Configure the operation of Template Blocks
$config['Admin_user'] = '';
$config['Admin_password'] = '';
$config['Admin_tips'] = 'on';

// Used for internal use...
$config['Version'] = '1.1';

$config['Exception_list'] = '';


?>