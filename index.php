<?php
// first of all, quit if the server is running on PHP <5
if( phpversion() < '5.0' ){
  die('Your server is running the setup with PHP v' . phpversion() . ' - you will need at least PHP 5 to use Template Blocks.');
}

//de-compile the URL into variables
$uri = substr( $_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['PHP_SELF']) ) ); 
$uri_parts = parse_url($uri);
$uri_path = explode('/', $uri_parts['path']);

$extensions = array(".html", ".htm", ".php");

// get the section, if requesting a directory (empty filename) jump one level up
$section = array_pop($uri_path);
if( $section == '' ){ $section = array_pop($uri_path); }
// these are the two variables we actually need
$GLOBALS['section'] = str_replace($extensions, '', $section);
$GLOBALS['parents'] = $uri_path;

// on to load our template...
//  CHANGE THIS IF YOU WANT THE TEMPLATE SOMEWHERE ELSE
include( 'template/index.php');

?>