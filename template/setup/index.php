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
 * Template Blocks: Setup
 */


// get the configuration file
require( '../config.php' );

// load all used classes
require( '../admin/classes/Database.php' );
require( '../admin/classes/Common.php' );

// get an instance of the classes
$db = new Database();
$common = new Common();

// load some html parts we are going to use
require( 'assets/content.php' );

// convert all variables in a form we can process them inside the HTML files
foreach( $_REQUEST as $k => $v ){
  $$k = $v;
}

// do the following conditional statements only if we are requesting the index page, aka there is no $page defined
if( !$page ) {
  // an extra variable in case we want to run the setup for a second time
  if( $overide_check ){
      $homepage = 'welcome';
  } else {
    $already_setup = $db->testConnection();
    if( !$already_setup ){
      $homepage = 'welcome';
    } else {
      $homepage = 'already_setup';
    }
  }
}

if( $mode == 'write' ) {
  writeConfig();
} elseif ( $mode == 'run_sql') {
  createTables();
} else {
  showPage();
}

## The initial function, loading up the HTML resource files (if necessary) and redirecting accordingly 
function showPage(){
  global $common, $config, $html, $homepage;

  // convert all variables in a form we can process them inside the HTML files
  foreach( $_REQUEST as $k => $v ){
    $$k = $v;
  }

  foreach( $config as $k => $v ){
    $$k = $v;
  }

  if( isset($page) && $page != '') {
    $output = $common->readHTML( 'assets/' . $page . '.html' );
  } else {
    $output = $common->readHTML( 'assets/index.html' );
  }

  $output = addslashes($output);
  eval("\$output = \"$output\";");
  $output = stripslashes($output);

  if( preg_match('{{CHECKED}}', $output) ) {
    $checked = ( $Admin_tips == 'on') ? 'checked="checked"' : '';
	$output = str_replace('{{CHECKED}}', $checked, $output);
  }

  echo $output;

}

## As a second step, figure out for each occassion  what needs updating in the html files previously loaded
function writeConfig(){
  global $common, $config;
  
  $step = array();
  $step['1']  = array('Website', 'Template_dir', 'Favicon', 'Rss_feed');
  $step['2']  = array('Database_server', 'Database_name', 'Database_user', 'Database_password', 'Database_table_prefix');
  $step['3']  = array('Admin_user', 'Admin_password', 'Admin_tips');

  $output = $common->readHTML( '../config.php' );
  
  foreach( $step[$_REQUEST['step']] as $k => $v ){
    $variable = "\$config['" . $v . "']";
    $new_line = $variable . " = '" . $_REQUEST[strtolower($v)] . "';";
    $start = strpos($output, $variable);
    $length = strpos($output, "\n", $start) - $start;
    $old_line = substr($output, $start, $length);
    $output = str_replace($old_line, $new_line, $output);
  }
  $common->writeFile( '../config.php', $output );
}

function createTables(){
  global $db, $common, $config;
  
  $sql = $common->readHTML( 'mysql.sql' );
  $sql = str_replace('{{TABLE_PREFIX}}', $config['Database_table_prefix'], $sql);

  $sql_lines = explode("\n", $sql);
  foreach($sql_lines as $query){
    if(trim($query) != "" && strpos($query, "--") === false){
      $db->runSQL($query);
    }
  }
  
}


?>