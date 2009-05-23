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

// first of all, quit if the server is running on PHP <5
if( phpversion() < '5.0' ){
  die('Your server is running the setup with PHP v' . phpversion() . ' - you will need at least PHP 5 to use Template Blocks.');
}

// get the configuration file
require( '../config.php' );

// load all used classes
require( '../classes/Database.php' );
require( '../classes/Common.php' );

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
  } elseif ( $update ) {
    doUpdate();
  } else {
    $already_setup = $db->testConnection();
    if( $already_setup ){
      $homepage = 'already_setup';
    } else {
      $homepage = 'welcome';
    }
  }
}

if( $mode == 'write' ) {
  validateInput();
} elseif ( $mode == 'run_sql') {
  createTables();
} else {
  showPage();
}

## The initial function, loading up the HTML resource files (if necessary) and redirecting accordingly 
function showPage(){
  global $common, $config, $homepage;

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

## Check the data entered
function validateInput(){
global $db, $html;

  // do a check by step
  if( $_REQUEST['step'] == '2' ){
    $db_details = $db->testConnection( $_REQUEST['database_server'], $_REQUEST['database_name'], $_REQUEST['database_user'], $_REQUEST['database_password'] );
    if( !$db_details ){
      echo $html['Error']['Database_Details'];
	  exit();
    }
  }

  // update the config file if all is well
  writeConfig();

}

## Do the necessary updating in the config file
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
  file_put_contents('../config.php', $output, LOCK_EX);
}

## Create the database strucutre
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

function  doUpdate( $version = false){
  global $db, $common, $config, $html, $homepage;

  if( !$version ){
    $version = $config['Version'];
  }
  
  if( $version == '1.0' ){
    // 1 . change the config file: a version number and a new variable
    $output = $common->readHTML( '../config.php' );

    $variable = "\$config['Version']";
    $new_part  = "\$config['Version'] = '1.1';\n\n";
    $new_part .= "\$config['Exception_list'] = '';\n";

	$start = strpos($output, $variable);
    $length = strpos($output, "\n", $start) - $start;
    $old_part = substr($output, $start, $length);
    $output = str_replace($old_part, $new_part, $output);
    file_put_contents('../config.php', $output, LOCK_EX);

	// 2. move the sceleton html file outside the html folder
	
	// rename original script file 
	$old_file = $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/assets/html/template.html';
	$new_file = $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/assets/default.html';

	if( file_exists( $old_file ) ){ 
	  rename( $old_file, $new_file ); 
	}
	  // write our new file in it's place...
	  //file_put_contents($original_file, $script, LOCK_EX);
	//} else {
	  // delete old file if necessary
	  //unlink($original_file);
	  //rename( $renamed_file, $original_file );
	//}

    // and redirect to the final page of the setup process
    $homepage = 'finish';
  } else {
    // couldn't determine the exact version - you should start the whole process again
    $homepage = 'welcome';
  }

}

?>