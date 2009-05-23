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

 	// get an instance of the other classes
	$common = new Common();
	// Connect to db
	$db = new Database();
	$common = new Common();
   // load some html parts we are going to use
    require( $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/admin/assets/php/content.php' );
	// Define public variables
    $this->head_blocks = array();
    $this->body_blocks = '';
  }


  function showPage() {
    global $db, $config;

	$page_attributes = $this->findSection();
	
	if( !$page_attributes ){
      header('Refresh: 0;url=/index.php');
	  exit;
	}

	$output = $this->getTemplate($page_attributes[0]['template']);

	// Make the necesasry replacements
	$title = $page_attributes[0]['title'];

	// Get the content
	$output = str_replace("{{CONTENT}}", $this->createContent( $page_attributes[0]['content'] ), $output);

	// convert all variables in a form we can process them inside the HTML files
    foreach( $config as $k => $v ){
	  $$k = $v;
    }
 	$output = addslashes($output);
	eval("\$output = \"$output\";");
	$output = stripslashes($output);
	
	// If you are calling the template from an external source then split it in header-footer vars
	if( preg_match('{{EXTERNAL}}', $output) ) {
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
	$template = $common->readHTML( $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/assets/html/template.html' );

	// Make the necessary replacements
	$template = str_replace("{{DOCTYPE}}", $html['Doctype'][$template_attributes[0]['type']], $template);
	$template = str_replace("{{HEAD}}", $this->createHEAD($head_blocks), $template);
	$template = str_replace("{{BODY}}", $this->createBODY($body_blocks), $template);
    	
	return $template;

  }

  function getBlock($id=1) {
    global $db, $config, $common;
	
	$file = $common->getBlockFile( $id );

	$where = array( 'id' => $id);
    $attributes = $db->select( '*', $config['Blocks'], $where );

	switch($attributes[0]['type']) {
	  case "CSS":
	    $file = substr( $file, strlen($_SERVER['DOCUMENT_ROOT']) );
	    $output = '  <link rel="stylesheet" type="text/css" href="' . $file . '"' . $this->end_tag . '>' . "\n";
        break;
      case "JavaScript":
	    $file = substr( $file, strlen($_SERVER['DOCUMENT_ROOT']) );
        $output = '  <script type="JavaScript" src="' . $file . '"' . $this->end_tag . '>' . "\n";
        break;
      case "HTML":
        $content .= $common->readHTML( $file ) . "\n";
        $output .= '  <!-- ' . ucwords( $attributes[0]['title'] ) . ' Start -->' . "\n";
        $output .= $common->putIndent( $content ) . "\n";
		$output .= '  <!-- ' . ucwords( $attributes[0]['title'] ) . ' End -->' . "\n";
        break;
      case "PHP":
        $content .= $common->readHTML( $file );
        $output .= '  <!-- ' . ucwords( $attributes[0]['title'] ) . ' Start -->' . "\n";
        $output .= $common->phpExecute( $content ) . "\n";
		$output .= '  <!-- ' . ucwords( $attributes[0]['title'] ) . ' End -->' . "\n";
        break;
    }
	
	return $output;
  }

  function createHEAD( $blocks ) {
    global $db, $config, $common;

    $head = '';
    if( is_array($blocks) ){
     foreach( $blocks as $k => $v ){
	    $head .= $this->getBlock( $v );
      }
	}
    if( $config['Rss_feed'] ){ 
	  $head .= '  <link rel="alternate" type="application/rss+xml" title="' . $config['Website'] . ' RSS Feed" href="' . $config['Rss_feed'] . '"' . $this->end_tag . '>' . "\n";
	}
    if( $config['Favicon'] ){ 
	  $head .= '  <link rel="shortcut icon" href="' . $config['Favicon'] . '"' . $this->end_tag . '>' . "\n";
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
    global $common;

    // do here conditions for executing the php...
    $output .= '  <!-- Content Start -->' . "\n";
	$output .= '  <div id="content">' . "\n";
	if( substr($input, 0, 8) == 'external' ){
	$output .= '{{EXTERNAL}}' . "\n";
	} else {
	$output .= $common->putIndent( $input ) . "\n";
	}
	$output .= '  </div>' . "\n";
    $output .= '  <!-- Content End -->' . "\n";
   return $output;
  }
  
  
  function findSection(){
    global $db, $config, $common; 
    
	// this is a condition if the user just enters the parent with a trailing slash...
	if( isset($this->section) &&  $this->section != '' ){
	  $slug = $this->section;
	} else {
	  $slug = 'index';
	}

	// make one database call for all the sections with that slug
	$where = array( 'slug' => $slug );
	$section_attributes = $db->select( '*', $config['Sections'], $where );

	// redirect the user to the index page if no section with that slug is found
	if( !$section_attributes ){
      return false;
	} else {
	  if( count( $section_attributes ) > 1) {
	    // we have more than one item with that slug, we need to verify the parents
	    if( $this->parents ){
	      $parent_list = explode( '/', $this->parents );
		  // this is quite an intensive loop - should be ok for a couple of nested levels
	      foreach( $parent_list as $k => $v ){
	        $where = array( 'slug' => $v );
	        $parent_attributes = $db->select( '*', $config['Sections'], $where );
	        $parent_string .= $parent_attributes['id'];
	        if ( $parent_list[$k+1] ) { $parent_string .= '|'; }
	      }
	    } else {
	      // no parents in path..
	      $parent_string = '0';
		}
		// now compare the parents string against our records
	    foreach( $section_attributes as $k => $v ){
		  if( substr($v['order'], -1) == $parent_string ) {
		    $parent_attributes = $v;
		    break;
		  }
		}
	    return $parent_attributes;
	  } else { 
	    // just output the one section we found
	    return $section_attributes;
	  }
	}

  }
  
  
  function __destruct() {
  global $db;
  // close database connection...
  //$db->db_close;
  }

}

?>