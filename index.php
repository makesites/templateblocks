<?php

//de-compile the URL into variables
$uri = $_SERVER["REQUEST_URI"];
$uri_parts = explode('?', $uri);
$uri_array = explode('/', $uri_parts[0]);
array_shift($uri_array);

$extensions = array(".html", ".htm", ".php");

// these are the two variables we actually need
$section = array_pop($uri_array);
if( $section == '' ){ $section = array_pop($uri_array); }
$section = str_replace($extensions, '', $section);
$parents = $uri_array;

// on to load our template
include('template/index.php');

?>