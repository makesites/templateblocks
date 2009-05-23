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
  function getXML( $url ) {
    global $db, $common, $config;

    $cache_dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/admin/cache/';
	$filename = $this->makeFilename( $url );
	$filename = str_replace( array('http','www','xml','rss2') , '', $filename);
	$cache_file = $cache_dir . $filename . '.xml';

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
  function makeBlockFile( $title, $type ) {
    global $config;
	
	$name = $this->makeFilename( $title );
	$file = strtolower( $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/assets/' . $type . '/' . $name . '.' . $type );
	
	return $file;
  }

  # Get the path of a block
  function getBlockFile( $id ) {
    global $db, $config, $html;
	
	$where = array( 'id' => $id);
	$fields = $db->select( '*', $config['Blocks'], $where );
	$file = $this->makeBlockFile( $fields[0]['title'], $html['Block_type'][$fields[0]['type']] );

	return $file;
  }

  # Execute a piece of php code
  function phpExecute( $content ) {
    global $db, $common, $config, $html;

    ob_start();
    $content = str_replace('<'.'?php','<'.'?',$content);
    eval('?'.'>'.trim($content).'<'.'?');
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  # Indent the content
  function putIndent( $content ) {
    global $html;
    // a simple replacement for now...
    $content = str_replace("\n", "\n".$html['Indent'], $content);
	$content = $html['Indent'].$content;
	return $content;
  }

  # Remove special characters from string to create filename
  function makeFilename( $string ) {

	// first let's make the title a valid filename
    $characters_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
    $characters_replace = array('','-','','','','','','','','','','','','','','','','','','','','','','','');
    $string = str_replace($characters_match, $characters_replace, $string);
	
    return $string;

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
        if (is_dir($sf) && !is_link($sf)) {
          $this->delTree($sf);
        } else {
          unlink($sf);
        } 
      } 
    }
    rmdir($f);
  }

  # Delete setup files
  function delSetup(){
    global $config;
    
	//compile the setup directory
    $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/setup';
	
	// start the deletion process
	if(is_dir($dir)) {
	  $this->delTree( $dir );
	}

	// then load the admin page again
    header('Refresh: 0;url=admin.php');
	
  }

}

?>