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
    // Open file to write
    $file = fopen($url, 'r');
	$content = fread($file, strlen(file_get_contents($url)));
    fclose($file);
    return $content;
  }

  # Write a file to your webspace
  function writeFile( $url, $content ){
    // Open file to write
    $file = fopen($url, 'w+');
    fwrite($file, $content);
    fclose($file);
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
		if( $time_difference < 10000 ) {
			$content = $this->readFile($cache_file);
		} else {
			$content = $this->readFile($url);
			$this->writeFile($cache_file, $content);
		}
	} else {
		$content = $this->readFile($url);
		$this->writeFile($cache_file, $content);
	}

	$content = $this->parseRSS($content);

	return $content;

  }
  
  # Move XML content to an array
  function parseRSS( $content ) {

	$content = stristr($content, '<item');

	$parser = xml_parser_create();
	xml_parser_set_option($parser,XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parse_into_struct($parser, $content, $values);
	xml_parser_free($parser);

    $params = array();
    $level = array();
    foreach( $values as $xml_elem ){

      if( $xml_elem['type'] == 'open' ){
        if( array_key_exists('attributes',$xml_elem) ){
          list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
        } else {
          $level[$xml_elem['level']] = $xml_elem['tag'];
        }
      }
      if( $xml_elem['type'] == 'complete' ){
	    $start_level = 1;                      
        $php_stmt = '$params';
        while($start_level < $xml_elem['level']) {
          $php_stmt .= '[$level['.$start_level.']]';
          $start_level++; 
        }
        $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
        eval($php_stmt);
        //$php_stmt .= '[$xml_elem[\'tag\']]';
        //eval('$tmp = '.$php_stmt.';');
        //if ( isset($tmp) ) {
        //  if( !is_array($tmp) ){ 
        //    eval($php_stmt . ' = array( 0 => $tmp);');
        //    eval($php_stmt . '[] = $xml_elem[\'value\'];');
        //  } else {
        //    eval($php_stmt . ' = $xml_elem[\'value\'];');
        //  }
		//}
	  }
	}

	return $params;
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
    global $db, $config;
	
	$where = array( 'id' => $id);
	$fields = $db->select( '*', $config['Blocks'], $where );
	$file = $this->makeBlockFile( $fields[0]['title'], $fields[0]['type'] );

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