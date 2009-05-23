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
 * Class: Common functions
 */


class Common {

  # Call constructor
  function __construct() {
 	// Get the root and template paths
	$this->getPaths();
  }

  # Read a template file
  function readHTML( $url ){

    $_file_status = is_readable($url);
    
	if( $_file_status ) {
	  $_parse_file = file($url);
	  # Loop array and load
	  for ($i = 0; $i < count($_parse_file); $i++) {
	    $_parse_file_str .= $_parse_file[$i];
	  }
	}
	return $_parse_file_str;
  }

  # Read a file from your webspace
  function readFile( $url ){
	$file = fopen($url, "rb");
    $content = stream_get_contents($file);
    fclose($file);
    return $content;
  }
  
  # Read an XML file
  function getXML( $url ){

    $cache_dir = realpath( $GLOBALS['TEMPLATE_PATH'] . '/admin/cache/');
	$filename = str_replace( array('http','www','xml','rss2') , '', $this->makeFilename( $url ));
	$cache_file = $cache_dir . '/' . $filename . '.xml';

  	if (file_exists( $cache_file ) && strlen(file_get_contents($cache_file)) > 0 ) {
		$time_difference = @(time() - filemtime( $cache_file ));
		if( $time_difference > 10000 ) {
			$xml = $this->readFile( $url );
			file_put_contents($cache_file, $xml, LOCK_EX);
		}
	} else {
		$xml = $this->readFile( $url );
		file_put_contents($cache_file, $xml, LOCK_EX);
	}
    $content = simplexml_load_file($cache_file);

    return $content;
  }

  # Compile the path of a block file
  function makeBlockFile( $title, $type ){

	$name = $this->makeFilename( $title );
	$file =  strtolower( realpath( $GLOBALS['TEMPLATE_PATH'] . '/assets/' . $type . '/' ) . '/' . $name . '.' . $type ) ;
	
	return $file;
  }

  # Get the path of a block
  function getBlockFile( $id ){
    global $db, $config, $html;
	
	$where = array( 'id' => $id);
	$fields = $db->select( '*', $config['Blocks'], $where );
	$file = $this->makeBlockFile( $fields[0]['title'], $html['Block_type'][$fields[0]['type']] );

	return $file;
  }

  # Execute a piece of php code
  function phpExecute( $content ){
    global $db, $config, $common, $html;

    ob_start();
    $content = str_replace('<'.'?php','<'.'?',$content);
    eval('?'.'>'.trim($content).'<'.'?');
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  # Indent the content
  function putIndent( $content ){
    global $html;
    // a simple replacement for now...
    $content = str_replace("\n", "\n".$html['Indent'], $content);
	$content = $html['Indent'].$content;
	return $content;
  }

  # Remove special characters from string to create filename
  function makeFilename( $string ){

	// first let's make the title a valid filename
    $characters_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
    $characters_replace = array('','-','','','','','','','','','','','','','','','','','','','','','','','');
    $string = str_replace($characters_match, $characters_replace, $string);
	
    return $string;

  }


  function getPaths(){
    global $config;

    // register the paths a global variables
	//
	// fix the registered server path in case there is a trailing slash
	$GLOBALS['DOCUMENT_ROOT'] = ( substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' || substr($_SERVER['DOCUMENT_ROOT'], -1) == '\\' ) ? realpath( substr($_SERVER['DOCUMENT_ROOT'], 0, -1) ) : realpath( $_SERVER['DOCUMENT_ROOT'] );
	// the template path is this path removing the "/classes" dir
	$GLOBALS['TEMPLATE_PATH'] = substr( realpath( dirname(__FILE__) ) , 0, -8 );
	// the template directory is the template path minus the document root path
	$GLOBALS['TEMPLATE_DIR'] = substr( str_replace($GLOBALS['DOCUMENT_ROOT'], '', $GLOBALS['TEMPLATE_PATH']), 1);
	// the template URI is the same as it's directory, replacing the \ paths in windows machines
	$GLOBALS['TEMPLATE_URI'] = str_replace('\\', '/', $GLOBALS['TEMPLATE_DIR']);
	// the site uri is calculated from the template location relative to where the index.php lives (this is to cover more extreme uses of the script)
	$GLOBALS['SITE_URI'] = '/' . str_replace( $config['Template_dir'], '', $GLOBALS['TEMPLATE_DIR'] );
	// now that we've got all the paths, make comparisons and run under certain conditions
    $this->checkPaths();

  }


  function checkPaths(){
    global $config;

    $setup_folder = realpath($GLOBALS['TEMPLATE_PATH'] . '/setup');
    $caller_path = dirname( realpath( basename( $_SERVER['PHP_SELF'] ) ) );

	// stop application if not the correct version of PHP
	if( phpversion() < '5' ){
		echo "You will need PHP 5 and above to run Template Blocks";
	  exit;
	}

    // redirect if there is a setup folder 
    if ( file_exists( $setup_folder ) && $setup_folder != $caller_path && !$_REQUEST['del-setup'] ) {
	  header('Refresh: 0;url=/' . $GLOBALS['TEMPLATE_URI'] .'/setup/index.php');
	  exit;
    }

    // don't accept direct requests of the template index file
    if( realpath( basename( $_SERVER['PHP_SELF'] ) ) ==  realpath( $GLOBALS['TEMPLATE_PATH'] . '/index.php' ) ) {
	  header('Refresh: 0;url=/');
	  exit;
    }

  }

  # Convert a file path to a URI
  function getURI( $file_path ){
   $file_uri = str_replace('\\', '/', substr( $file_path, strlen($GLOBALS['DOCUMENT_ROOT']) ) );

   return $file_uri;
  }

  # Count dimensions of a multi-array
  function countDim($array){
    if (is_array(reset($array))) 
      $return = $this->countDim(reset($array)) + 1;
    else
      $return = 1;
 
   return $return;
  }

  # Delete a directory in your webspace along with its containing files
  function delTree($f) {
    if (is_dir($f)) {
      foreach(glob($f.'/*') as $sf) {
        if (is_dir( realpath($sf) ) && !is_link($sf)) {
          $this->delTree( realpath($sf) );
        } else {
          unlink($sf);
        } 
      } 
    }
    rmdir($f);
  }

  # Delete setup files
  function delSetup(){

	//compile the setup directory
    $dir = realpath( $GLOBALS['TEMPLATE_PATH'] . '/setup');
	
	// start the deletion process
	if(is_dir($dir)) {
	  $this->delTree( $dir );
	}

	// then load the admin page again
    header('Refresh: 0;url=admin.php');
	
  }

}

?>