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
 * Class: Admin functions
 */


class Admin {

  # Call constructor
  function __construct() {
    global $db, $common, $config;

	// connect to db
	$db = new Database();
 	// get an instance of the other classes
	$common = new Common();
    // run under certain conditions
	$common->checkPaths();
 	// see if all this is an authorized use of the script
	$this->checkSession();
   // load some html parts we are going to use
    require( 'admin/php/content.php' );

  }

  ## The initial function, loading up the HTML resource files (if necessary) and redirecting accordingly 
  function showPage() {
    global $config, $common, $login;
    
	if( !$login ){
	  // we don't have a verified user
	  if( isset($_REQUEST['page']) || ( isset($_REQUEST['action']) && $_REQUEST['action'] != 'logout' ) ){
	    //this is a timed out AJAX call
	    echo 'timeout';
		exit();
	  } else {
	    // this is a first time run
	  	$output = $common->readHTML( 'admin/html/login.html' );
	  }
    } elseif( isset($_REQUEST['action']) && $_REQUEST['action'] != '' && $_REQUEST['action'] != 'login' ) {
      if($_REQUEST['action'] == 'edit') {
   	    $output = $common->readHTML( 'admin/html/' . $_REQUEST['page'] .'_'. $_REQUEST['action'] . '.html' );
	  } elseif( $_REQUEST['action'] == 'add' || $_REQUEST['action'] == 'save' || $_REQUEST['action'] == 'delete' || $_REQUEST['action'] == 'save-tree' ) {
	    $this->updateData();
		exit();
	  }
    } elseif( isset($_REQUEST['page']) && $_REQUEST['page'] != '' ) {
   	  $output = $common->readHTML( 'admin/html/' . $_REQUEST['page'] . '.html' );
	} else {
      $output = $common->readHTML( 'admin/html/index.html' );
	}

	$output = $this->updatePage( $output );

    echo $output;

  }

  ## As a second step, figure out for each occassion  what needs updating in the html files previously loaded
  function updatePage( $output ){
 
    if($_REQUEST['action'] == 'edit') {
	  $output = $this->updateEdit( $output );
	} else {
	  $output = $this->updateOther( $output );
	}
    return $output;
  }

  ## Deals with the presenting all other pages except the edit pages
  function updateOther( $output ){
    global $db, $config, $common;

	// convert all variables in a form we can process them inside the HTML files
    foreach( $config as $k => $v ){
	  $$k = $v;
    }

	// then add other variables used in the html files
	$info = $this->getInfo('index');
	$site_uri = $GLOBALS['SITE_URI'];
	
    if( $_REQUEST['page'] == 'home' ) {
	  $block_items = $db->select( '*', $config['Blocks'] );
	  $template_items = $db->select( '*', $config['Templates'] );
	  $section_items = $db->select( '*', $config['Sections'] );
	  
      $Num_blocks = count( $block_items );
      $Num_templates = count( $template_items );
      $Num_sections = count( $section_items );
	  
 	}
	
	// convert all variables in a form we can process them inside the HTML files
    foreach( $config as $k => $v ){
	  $$k = $v;
    }

    if( preg_match('/\$listings/', $output) ) {
  	  $listings = $this->getListings();
    }
    if( preg_match('/\$checked/', $output) ) {
      $checked = ( $Admin_tips == 'on') ? 'checked="checked"' : '';
    }

	// and do the necessary replacements
	$output = addslashes($output);
	eval("\$output = \"$output\";");
	$output = stripslashes($output);
	
	return $output;
  }

  ## Deals with the edit forms
  function updateEdit( $output ){
    global $db, $common, $html, $config;

	$page_type = ucwords(substr($_REQUEST['page'], 0 , -1));
    // grab the values of the module if any
	if( isset($_REQUEST['id']) && $_REQUEST['id'] != '' ){
  	  $type = $_REQUEST['page'];
	  $table = $config[ucwords($type)];
	  $where = array( 'id' => $_REQUEST['id']);
      $fields = $db->select( '*', $table, $where );
	  // convert all variables ina form we can process them inside the HTML files
      foreach( $fields[0] as $k => $v ){
	    $$k = $v;
      }
      $action = 'save';
      $button_label = 'Save';
	  $heading = 'Edit ' . $page_type . ': &quot; ' . $title . ' &quot;';
      // get the content from the external file on existing blocks
	  if( $_REQUEST['page'] == 'blocks' ){
	    $file = $common->makeBlockFile($title, $html['Block_type'][$type]);
	    $content = htmlspecialchars( addslashes( $common->readFile( $file ) ) );
	  }
	} else {
	  // this is a new element so set the action to 'add' and the rest of the variables accordingly
      $action = 'add';
      $button_label = 'Add';
	  $heading = 'New ' . $page_type;
	  $id = '(to be assigned)';
	  $blocks = '#X';
	}

	// then add other variables used in the html files
	$info = $this->getInfo('edit');

	// and do the necessary replacements
 	$output = addslashes($output);
	eval("\$output = \"$output\";");
	$output = stripslashes($output);

    if( preg_match('{{LIST_TEMPLATES}}', $output) ) {
  	  $listings = $this->getSelections('templates', $template);
	  $output = str_replace('{{LIST_TEMPLATES}}', $listings, $output);
    }
    if( preg_match('{{LIST_DOCTYPES}}', $output) ) {
  	  $listings = $this->getSelections('doctype', $type);
	  $output = str_replace('{{LIST_DOCTYPES}}', $listings, $output);
    }
    if( preg_match('{{LIST_BLOCK_TYPES}}', $output) ) {
  	  $listings = $this->getSelections('block_type', $type);
	  $output = str_replace('{{LIST_BLOCK_TYPES}}', $listings, $output);
    }
	if( $_REQUEST['page'] == 'templates' ){ 
	  $block_listings = ( $_REQUEST['id'] ) ? $this->getListings('blocks', $blocks, 'head') : '';
	  $output = str_replace('{{BLOCKS_HEAD}}', $block_listings, $output);
	  $block_listings = ( $_REQUEST['id'] ) ? $this->getListings('blocks', $blocks, 'body') : $html['Templates']['Content']['Item'];
	  $output = str_replace('{{BLOCKS_BODY}}', $block_listings, $output);
	  $output = str_replace('{{BLOCKS_OTHER}}', $this->getListings('blocks', $blocks, false), $output);
	  $output = str_replace('{{BLOCKS_DRAGGABLE}}', $this->makeDraggable('blocks', $blocks, false), $output);
	}
	return $output;
  }

  
  ## Updating the data in the database and/or filesystem
  function updateData(){
    global $db, $common, $html, $config;
	
	$type = $_REQUEST['action'];
	$table = $config[ucwords($_REQUEST['page'])];
    if( $_REQUEST['page'] == 'blocks' && $_REQUEST['action'] == 'save' ){
	  $fields = array(
	    'id'=> $_REQUEST['block_id'],
	    'title'=> $_REQUEST['block_title'],
	    'type'=> $_REQUEST['block_type'],
	  );
	  // update the content in an external file
	  $file = $common->makeBlockFile($_REQUEST['block_title'], $html['Block_type'][$_REQUEST['block_type']]);
	  $content = stripslashes($_REQUEST['block_content']);
	  file_put_contents($file, $content, LOCK_EX);
	  // delete old file if necessary
	  $original_file = $common->getBlockFile($_REQUEST['block_id']);
	  if( $file != $original_file && is_file($original_file) ) {
	    unlink($original_file);
      }
	} elseif( $_REQUEST['page'] == 'blocks' && $_REQUEST['action'] == 'add' ){
	  $fields = "(title, type) VALUES ('" . $_REQUEST['block_title'] . "', '" . $_REQUEST['block_type'] . "')";
	  // update the content in an external file
	  $file = $common->makeBlockFile($_REQUEST['block_title'], $html['Block_type'][$_REQUEST['block_type']]);
	  $content = stripslashes($_REQUEST['block_content']);
	  file_put_contents($file, $content, LOCK_EX);
	} elseif( $_REQUEST['page'] == 'blocks' && $_REQUEST['action'] == 'delete' ){
	  $fields = array(
	    'id'=> $_REQUEST['block_id'],
	  );
	} elseif( $_REQUEST['page'] == 'templates' && $_REQUEST['action'] == 'save' ){
	  $fields = array(
	    'id'=> $_REQUEST['template_id'],
	    'title'=> $_REQUEST['template_title'],
	    'type'=> $_REQUEST['template_doctype'],
	    'blocks'=> $_REQUEST['template_blocks'],
	  );
	} elseif( $_REQUEST['page'] == 'templates' && $_REQUEST['action'] == 'add' ){
	  $fields = "(title, type, blocks) VALUES ('" . $_REQUEST['template_title'] . "', '" . $_REQUEST['template_doctype'] . "', '" . $_REQUEST['template_blocks'] . "')";
	} elseif( $_REQUEST['page'] == 'templates' && $_REQUEST['action'] == 'delete' ){
	  $fields = array(
	    'id'=> $_REQUEST['template_id'],
	  );
	} elseif( $_REQUEST['page'] == 'sections' && $_REQUEST['action'] == 'save' && !$_REQUEST['tree']){
	  // a special condition if the content is external
	  if( $_REQUEST['is_external'] ){ 
		$this->sectionScript();
	    $_REQUEST['section_content'] = 'external|' . $_REQUEST['section_content'];
		if( $_REQUEST['custom_class'] ){
		  $_REQUEST['section_content'] .= '|' . $_REQUEST['custom_class'];
		}
	  }
	  $fields = array(
	    'id'=> $_REQUEST['section_id'],
	    'title'=> $_REQUEST['section_title'],
	    'slug'=> $_REQUEST['section_slug'],
	    'template'=> $_REQUEST['section_template'],
	    'content'=> htmlspecialchars( $_REQUEST['section_content'] ),
	  );
	} elseif( $_REQUEST['page'] == 'sections' && $_REQUEST['action'] == 'add' ){
		  // a special condition if the content is external
	  if( $_REQUEST['is_external'] ){ 
		$this->sectionScript();
	    $_REQUEST['section_content'] = 'external|' . $_REQUEST['section_content'];
		if( $_REQUEST['custom_class'] ){
		  $_REQUEST['section_content'] .= '|' . $_REQUEST['custom_class'];
		}
	  } else {
	    $_REQUEST['section_content'] = htmlspecialchars( $_REQUEST['section_content'] );
	  }
	  $fields = "(title, slug, template, content, position) VALUES ('" . $_REQUEST['section_title'] . "', '" . $_REQUEST['section_slug'] . "', '" . $_REQUEST['section_template'] . "', '" . $_REQUEST['section_content'] . "', '0(x)')";
	} elseif( $_REQUEST['page'] == 'sections' && $_REQUEST['action'] == 'delete' ){
	  if( $_REQUEST['is_external'] ){ 
		$this->sectionScript(false);
	  }
	  $fields = array(
	    'id'=> $_REQUEST['section_id'],
	  );
	} elseif( $_REQUEST['page'] == 'sections' && $_REQUEST['action'] == 'save' && $_REQUEST['tree'] ){
	  $fields = $this->saveOrder($_REQUEST['sections']);
	  exit();
	} elseif( $_REQUEST['page'] == 'settings' && $_REQUEST['action'] == 'save' ){
      $this->writeConfig();
	  exit();
    }
	
	// let's send an output
	$valid = $this->checkInput();

	if( $valid != 1 ){
	  echo $valid;
	} else {
      $result = $db->execute( $type, $table, $fields );
	  echo $result;
	}
  }

  
  function getSelections( $type, $value='' ){
    global $db, $config, $html;
   
    $type = ucwords($type);
    $page = ucwords($_REQUEST['page']);

	if( $config[$type] ){
      $items = $db->select( '*', $config[$type] );
	  $database = 1;
    }elseif( $html[$type] ){
      $items = $html[$type];
	  $database = 0;
	}
	if( is_array($items) ){
      foreach( $items as $k => $v ){
	    $open = $html[$page][$type.'_Select']['Item_Open'];
	    $close = $html[$page][$type.'_Select']['Item_Close'];
		$key = ( is_array($v) ) ? $v['id'] : $k;
		$title = ( is_array($v) ) ? $v['title'] : $k;
	    $open = str_replace('{{VALUE}}', $key, $open);
	    $selected= ( $key == $value ) ? 'selected' : '';
	    $open = str_replace('{{SELECTED}}', $selected, $open);
	    $selections .= $open . $title . $close;
	  }
	} else {
	  $selections .= $html['No_Listings_Found'];
	}
	return $selections;
  }


  function getListings( $type=false, $value=false, $flag=false ){
    global $db, $common, $config, $html;

    $type = (!$type) ? ucwords($_REQUEST['page']) : ucwords($type);
    $items = $db->select( '*', $config[$type], false, 'id');

	if( is_array($items) ){
	  if( $_REQUEST['page'] == 'sections' ){
	    $children = array();
        foreach( $items as $k => $v ){
		  // first pass is to determine the children
		  preg_match('/(.*)\((.*)\)/', $v['position'], $matches);
		  // if this is the first time a section is accessed enter it's order automatically
		  if($matches[2] == 'x') {
		    array_push($children[$matches[1]], $v['id']);
		  } else {
		    $children[$matches[1]][$matches[2]] = $v['id'];
		  }
		}
		$listings = $this->createOrder( $items, $children );
	  } elseif( $_REQUEST['page'] == 'templates' && $type == 'Blocks' ){
 		if( $flag == false ){
		  // blocks not used by the template
	      $value = str_replace('#', '|', $value);
          $group = explode('|', $value);
          foreach( $items as $k => $v ){
		    if(in_array($v['id'], $group) === $flag){
	          $open = str_replace('{{ID}}', $v['id'], $html['Templates']['Blocks_Other']['Item_Open']);
	          $open = str_replace('{{TYPE}}', $v['type'], $open);
	          $close = str_replace('{{ID}}', $v['id'], $html['Templates']['Blocks_Other']['Item_Close']);
	          $close = str_replace('{{TYPE}}', $v['type'], $close);
	          $listings .= $open . $v['title'] . $close;
		    }
		  }
		} else {
		  // blocks used by the template - time to break them apart to head-body
          $template_parts = explode('#', $value);
		  if( $flag == 'head' ){
		    $value_part = $template_parts[0];
		  } else {
		    $value_part = $template_parts[1];
		  }
          $group = explode('|', $value_part);
		  // we need to preserve the order for the used blocks so we need a different loop
		  foreach( $group as $k => $v ){
		    if( $v == 'X' ){
	          $listings .= $html['Templates']['Content']['Item'];
		    } else {
              foreach( $items as $l => $w ){
		        if( $w['id'] === $v ){
	              $open = str_replace('{{ID}}', $w['id'], $html['Templates']['Blocks_Used']['Item_Open']);
	              $open = str_replace('{{TYPE}}', $w['type'], $open);
	              $close = str_replace('{{ID}}', $w['id'], $html['Templates']['Blocks_Used']['Item_Close']);
	              $close = str_replace('{{TYPE}}', $w['type'], $close);
	              $listings .= $open . $w['title'] . $close;
		        }
			  }
	        }
          }
		}
	  } else {
        foreach( $items as $k => $v ){
	      $open = str_replace('{{ID}}', $v['id'], $html[$type]['List_Item_Open']);
	      $open = str_replace('{{TYPE}}', $v['type'], $open);
	      $close = $html[$type]['List_Item_Close'];
	      $listings .= $open . $v['title'] . $close;
	    }
	  }
	} else {
	      $listings .= '<li>' . $html['No_Listings_Found'] . '</li>';
	}
	return $listings;
  }


  function createOrder( $items, $order, $parent=0 ){
    global $html;
	
	ksort($order[$parent]); 

	foreach( $order[$parent] as $k => $v ){
      foreach( $items as $l => $w ){
        // now we can create the listings
		if( $w['id'] == $v ){
	      $open = str_replace('{{ID}}', $w['id'], $html['Sections']['List_Item_Open']);
		  // check for children in this branch
		  if (array_key_exists($w['id'], $order)) {
		    $children = $this->createOrder( $items, $order, $w['id'] );
		  } else {
		    $children = '';
		  }
	      $close = str_replace('{{CHILDREN}}', $children, $html['Sections']['List_Item_Close']);
	      $listings .= $open . $w['title'] . $close;
	    }
      }
	}
 	return $listings;  
  }


  function saveOrder(&$array, $parent=0, $level=0, $result=array() ) {
    global $db, $config;

    if( !$parent ){ $parent = 0; }
    foreach ( $array as $k => $v ) {
      if (!is_array($v)) { 
	    // If it's not an array,  save it
	    $type = $_REQUEST['action'];
	    $table = $config[ucwords($_REQUEST['page'])];
	    $fields = array(
	      'id'=> $v,
	      'position'=> $parent . '(' . $level . ')',
        );
        $db->execute( $type, $table, $fields );
		$parent = $v;
      } else {
	    $level = $k;
	    // If it is an array, call the function on it
        $this->saveOrder($v, $parent, $level, $result);
      }
    }
	return $result;
  }


  ## Make any group of objects draggable 
  function makeDraggable( $type=false, $value=false, $flag=false ){
    global $db, $config, $html;

    if( $type ){ $type = ucwords($type); }else{ return ''; }
    $table = $config[$type];
    $items = $db->select( '*', $table );
	if( is_array($items) ){
	    if( $value ){ 
		  $group = explode('|', $value); 
          foreach( $items as $k => $v ){
		    if( in_array($v['id'], $group) === $flag ){
	          $draggable .= str_replace('{{ID}}', $v['id'], $html['JavaScript'][$type.'_Draggable']);
		    }
	      }
		}else{
          foreach( $items as $k => $v ){
	        $draggable .= str_replace('{{ID}}', $v['id'], $html['JavaScript'][$type.'_Draggable']);
	      }
		}
	} else {
	  $draggable = '';
	}
	return $draggable;
  }


  ## Show the admin tips and other popups
  function getInfo( $type='index'){
    global $config, $html;
	
	if( $config['Admin_tips'] == 'on' && $html['Info'][ucwords($_REQUEST['page'])][ucwords($type)] ){
	  $info = $html['Info'][ucwords($_REQUEST['page'])][ucwords($type)];
	}
	if( $_REQUEST['action'] == 'logout' ){
  	  $info = "You have logout successfully!";
	}
    
	return $info;
  }


  ## When a form is submitted this function checks if certain conditions apply
  function checkInput(){
    global $db, $config, $html;
	
	if ( $_REQUEST['page'] == 'sections' ){
      // check if we are working on the index page
	  if ($_REQUEST['section_id'] == '1'){
	    if ($_REQUEST['section_slug'] != 'index'){ $error = $html['Error']['Rename_Index_Page']; } 
		if ($_REQUEST['action'] == 'delete'){ $error = $html['Error']['Delete_Index_Page']; }
	  } else {
        // check if we have a duplicate page entry
        $items = $db->select( '*', $config['Sections'] );
        foreach( $items as $k => $v ){
	      if( ($_REQUEST['section_slug'] == $v['slug']) && ($_REQUEST['section_id'] != $v['id']) ){ 
		    $error = $html['Error']['No_Duplicate_Page']; 
		  }
	    }
      }
	}

	if ( $_REQUEST['page'] == 'templates' && $_REQUEST['action'] == 'delete' ){
	  // check if this template is used by the index page
	  $where = array( 'slug' => 'index');
      $items = $db->select( '*', $config['Sections'], $where );
      foreach( $items as $k => $v ){
	    if( $_REQUEST['template_id'] == $v['template'] ){ $error = $html['Error']['Index_Template']; 
		}
	  }
	}
	if ( $_REQUEST['page'] == 'blocks' && $_REQUEST['action'] != 'delete'){
        // check if we have a duplicate page entry
        $items = $db->select( '*', $config['Blocks'] );
        foreach( $items as $k => $v ){
	      if( ($_REQUEST['block_title'] == $v['title']) && ($_REQUEST['block_type'] == $v['type']) && ($_REQUEST['block_id'] != $v['id']) ){ 
		    $error = $html['Error']['No_Duplicate_Block']; 
		  }
	    }
    }
	return ( $error ) ? $error : true;
  }

  
  ## Updates the configuration file
  function writeConfig(){
    global $common, $config;
  
    $output = $common->readHTML( 'config.php' );
    $escape_options = array( 'Database_table_prefix', 'Sections', 'Templates', 'Blocks', 'Version' );
	
    foreach( $config as $k => $v ){
	  if( in_array($k, $escape_options) === false ) {
        $variable = "\$config['" . $k . "']";
        $new_line = $variable . " = '" . str_replace("\n", '|', $_REQUEST[strtolower($k)]) . "';";
        $start = strpos($output, $variable);
        $length = strpos($output, "\n", $start) - $start;
        $old_line = substr($output, $start, $length);
        $output = str_replace($old_line, $new_line, $output);
	  }
    }
    file_put_contents('config.php', $output, LOCK_EX);
  }


  ## Deals with the external script sections
  function sectionScript( $write=true ){
    global $common, $config;
	  
	$uri_parts = explode('/', $_REQUEST['section_content']);
	$filename = str_replace('.php', '', end($uri_parts));
	
	$script = $common->readHTML(  'admin/php/external.php' );
	$script = str_replace('{{SECTION}}', $_REQUEST['section_slug'], $script);
	$script = str_replace('{{TEMPLATE_DIR}}', $GLOBALS['TEMPLATE_DIR'], $script);
	$script = str_replace('{{FILENAME}}', $filename, $script);
	
	// get the custom class, if any
	if( $_REQUEST['custom_class'] ){
	  $custom_class = "include( " . $GLOBALS['TEMPLATE_PATH'] . "/classes/" . $_REQUEST['custom_class'] . "' );";
    } else {
	  $custom_class = '';
	}
	$script = str_replace('{{CUCTOM_CLASS}}', $custom_class, $script);

	// rename original script file 
	$original_file = $GLOBALS['DOCUMENT_ROOT'] . $_REQUEST['section_content'];
	$renamed_file = $GLOBALS['DOCUMENT_ROOT'] . str_replace( end($uri_parts), $filename.'-original.php', $_REQUEST['section_content'] );

	if( $write ){ 
	  if( !file_exists( $renamed_file ) ){ rename( $original_file, $renamed_file ); }
	  // write our new file in it's place...
	  file_put_contents($original_file, $script, LOCK_EX);
	} else {
	  // delete old file if necessary
	  unlink($original_file);
	  rename( $renamed_file, $original_file );
	}
  
  }


  ## Checks if the user is logged in 
  function checkSession(){
    global $common, $config, $login;

    // first an instance that doesn't need login verification
    if( $_REQUEST['del-setup'] ) {
	  $common->delSetup();
	  exit();
	}
	// now let's test the session...
    session_start();
	if( isset( $_SESSION["TemplateBlocks-Admin"] ) && isset( $_SESSION["TemplateBlocks-Password"] ) )
	{
		if( ($_SESSION["TemplateBlocks-Admin"] == $config['Admin_user']) && ($_SESSION["TemplateBlocks-Password"] == $config['Admin_password']) ) {
		  $login = true;
		} else {
		  $login = false;
		}
	} else {
	  $login = false;
	}

    if( !$login || $_REQUEST['action'] == "logout") {
	  $this->processLogin();
	}

  }


  ## This function is run when the login info is sent
  function processLogin(){
    global $config, $common, $login;

	if( $_REQUEST['action'] == "login" ){
		if( $_REQUEST['username'] != $config['Admin_user'] ){ 
		  echo "Invalid username";
		  exit();
		}
		if( $_REQUEST['password'] != $config['Admin_password'] ){ 
		  echo "Invalid password";
		  exit();
		}
        session_start();
		/*session_register( "TemplateBlocks-Admin" );
		session_register( "TemplateBlocks-Password" );*/
		$_SESSION["TemplateBlocks-Admin"] = $_REQUEST['username'];
		$_SESSION["TemplateBlocks-Password"] = $_REQUEST['password'];
		$login = true;
		exit();
	} else {
		session_start();
		unset($_SESSION["TemplateBlocks-Admin"] );
		unset($_SESSION["TemplateBlocks-Password"] );
		$login = false;
	}
  }



}

?>