<?php


//de-compile the URL into variables
$original_uri = $_SERVER["REQUEST_URI"];
$main_uri_parts = explode('?', $original_uri);
$sections = explode('/', $main_uri_parts[0]);
$extensions = array(".html", ".html", ".php");
$section = str_replace($extensions, '', end($sections));
$parents = prev($sections);

// on to load our template
include('template/index.php');

?>