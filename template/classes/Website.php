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
 * Class: Frontend functions
 */


class Website {
  # Define class varriables
  public $section;
  public $parents;
  public $end_tag;

  function __construct() {
    global $db, $common, $config;

	// Connect to db
	$db = new Database();
 	// get an instance of the other classes
	$common = new Common();
    // load some html parts we are going to use
    require( $GLOBALS['TEMPLATE_PATH'] . '/admin/php/content.php' );
	// Define public variables
    $this->head_blocks = array();
    $this->body_blocks = '';
  }


  function showPage() {
    global $db, $config, $page;

	// First check the URI to see if it is in the exception list
    $exception = $this->checkExceptions();

	$page_attributes = $this->findSection();
	
	// If we can't find the page from the section list, redirect to the domain's root
	if( !$page_attributes ){
      header('Refresh: 0;url=/');
	  exit();
	}

	$output = $this->getTemplate($page_attributes['template']);

	// Make the necessary replacements
	$title = ( $page['title'] ) ? $page['title'] : $page_attributes['title'];

	// Get the content
	$output = str_replace("{{CONTENT}}", $this->createContent( $page_attributes['content'] ), $output);

	// convert all variables in a form we can process them inside the HTML files
    foreach( $config as $k => $v ){
	  $$k = $v;
    }
 	$output = addslashes($output);
	eval("\$output = \"$output\";");
	$output = stripslashes($output);
	
	if( $exception ){
	  $GLOBALS['template-pre'] = '';
	  $GLOBALS['template-post'] = '';
	} elseif( preg_match('{{EXTERNAL}}', $output) ) {
	  // If you are calling the template from an external source then split it in header-footer vars
	  $output = explode("{{EXTERNAL}}", $output);
	  $GLOBALS['template-pre'] = $output[0];
	  $GLOBALS['template-post'] = $output[1];
	} else {
      echo $output;
	}

  }


  function getTemplate($id=1) {
    global $config, $db, $common, $html;
 
	$where = array( 'id' => $id);

	$template_attributes = $db->select( '*', $config['Templates'], $where );

	$this->end_tag = ( strpos($template_attributes[0]['type'], 'XHTML' ) === false ) ? '' : ' /';

	// find the blocks of this template
	$blocks =  explode( '#', $template_attributes[0]['blocks'] );
	$head_blocks =  explode( '|', $blocks[0] );
	$body_blocks =  explode( '|', $blocks[1] );

	// load the template
	$template = $common->readHTML( $GLOBALS['TEMPLATE_PATH'] . '/assets/default.html' );

	// Make the necessary replacements
	$template = str_replace("{{DOCTYPE}}", $html['Doctype'][$template_attributes[0]['type']], $template);
	$template = str_replace("{{HEAD}}", $this->createHEAD($head_blocks), $template);
	$template = str_replace("{{BODY}}", $this->createBODY($body_blocks), $template);
    	
	return $template;

  }


  function getBlock($id=1) {
    global $db, $config, $common, $html;
	
	$file = $common->getBlockFile( $id );

	$where = array( 'id' => $id);
    $attributes = $db->select( '*', $config['Blocks'], $where );

	switch($attributes[0]['type']) {
	  case "CSS":
	    $file = $common->getURI( $file );
	    $output = $html['Indent'] . '<link rel="stylesheet" type="text/css" href="' . $file . '"' . $this->end_tag . '>' . "\n";
        break;
      case "JavaScript":
	    $file = $common->getURI( $file );
        $output = $html['Indent'] . '<script type="text/JavaScript" src="' . $file . '"></script>' . "\n";
        break;
      case "HTML":
        $content .= $common->readHTML( $file ) . "\n";
        $output .= $html['Indent'] . '<!-- ' . ucwords( $attributes[0]['title'] ) . ' Start -->' . "\n";
        $output .= $common->putIndent( $content ) . "\n";
		$output .= $html['Indent'] . '<!-- ' . ucwords( $attributes[0]['title'] ) . ' End -->' . "\n";
        break;
      case "PHP":
        $content .= $common->readHTML( $file );
        $output .= $html['Indent'] . '<!-- ' . ucwords( $attributes[0]['title'] ) . ' Start -->' . "\n";
        $output .= $common->phpExecute( $content ) . "\n";
		$output .= $html['Indent'] . '<!-- ' . ucwords( $attributes[0]['title'] ) . ' End -->' . "\n";
        break;
    }
	
	return $output;
  }


  function createHEAD( $blocks ) {
    global $db, $config, $common, $html;

    $head = '';
    if( is_array($blocks) ){
     foreach( $blocks as $k => $v ){
	    $head .= $this->getBlock( $v );
      }
	}
    if( $config['Rss_feed'] ){ 
	  $head .= $html['Indent'] . '<link rel="alternate" type="application/rss+xml" title="' . $config['Website'] . ' RSS Feed" href="' . $config['Rss_feed'] . '"' . $this->end_tag . '>' . "\n";
	}
    if( $config['Favicon'] ){ 
	  $head .= $html['Indent'] . '<link rel="shortcut icon" href="' . $config['Favicon'] . '"' . $this->end_tag . '>' . "\n";
	}

    return $head;
  }


  function createBODY( $blocks ) {
    global $common; 
	
    $body = '';
    if( is_array($blocks) ){
      foreach( $blocks as $k => $v ){
	    if( $v == 'X' ) { 
		  // this is the content block - let's deal with it later...
          $body .= '{{CONTENT}}' . "\n";
		} else {		
	      $body .= $this->getBlock( $v );
		}
	  }
    }

	return $body;
  }


  function createContent( $input ) {
    global $common, $html;

    // do here conditions for executing the php...
    $output .= $html['Indent'] . '<!-- Content Start -->' . "\n";
	$output .= $html['Indent'] . '<div id="content">' . "\n";
	if( substr($input, 0, 8) == 'external' ){
	$output .= '{{EXTERNAL}}' . "\n";
	} else {
	$output .= htmlspecialchars_decode( $common->putIndent( $input ) ) . "\n";
	}
	$output .= $html['Indent'] . '</div>' . "\n";
    $output .= $html['Indent'] . '<!-- Content End -->' . "\n";
   return $output;
  }

  
  function findSection(){
    global $db, $config, $common; 
    
	// a special condition for the index page
	if( isset($this->section) &&  $this->section != '' ){
	  $slug = $this->section;
	} else {
	  $slug = 'index';
	}

	// make one database call for all the sections to deal with the parents as well
	$sections = $db->select( '*', $config['Sections'] );
    
	$parent_list = $this->parents;
	// for this version must only have one parent as the sections are saved just in two levels
	if( count( $parent_list ) > 1 ){
	  return false;
	} else {
	  // determine the parent
	  $parent = ( is_array($parent_list) ) ? end($parent_list) : '';
	  if( $parent != '' ){
	    foreach($sections as $item){
	      if( $item['slug'] == $parent ){ $parent_id = $item['id']; }
        }
	  } else {
	  	  $parent_id = '0';
	  }
	  // determine the section
	  foreach($sections as $item){
	    // define the attributes for the section we are viewing
	    if( $item['slug'] == $slug && substr($item['position'], 0 ,1) == $parent_id){
	      $section_attributes = $item;
	    }
	  }
      return $section_attributes;
    }
  }

  
  function checkExceptions(){
    global $config;
	
	$exception_list = explode('|', $config['Exception_list']);
    foreach( $exception_list as $exception_uri ){
	  if( $exception_uri == $_SERVER["REQUEST_URI"] ){
	    return true;
	  }
	}
	return false;
  }

  
  
  function __destruct() {
  global $db;
  // close database connection...
  //$db->db_close;
  }

}

?>