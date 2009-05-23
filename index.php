<?php

require_once("config.php");

# Make the sql connection.
dbConnect();

if (!$page) { $page = 1; }

# Get the relative contents
$querychar = "SELECT * FROM $webpages WHERE ID='$page'";
$result = mysql_query($querychar) or die( mysql_error() );
$properties = mysql_fetch_array( $result );
$pagename = $properties['Name'];
$template = $properties['Template'];
$allcontent = $properties['Content'];

$contentnum = explode(",", $allcontent);

# Compile the page
if (file_exists("$template_path/$template")) {
	$output = file_get_contents("$template_path/$template");
} else {
	die("Template file $template not found.");
}

$mycontent = $output;
if (count($contentnum) > 0) {
	getModules($contentnum);
}

# Replace the title if it is in the page
$mycontent = str_replace("%pagename%", $pagename, $mycontent);


# Export the final page
echo $mycontent;


# Functions used above

function dbConnect()
{
	global $db_host, $db_name, $db_user, $db_pass;
	mysql_connect( $db_host, $db_user, $db_pass ) or die( mysql_error() );
	mysql_select_db( $db_name );
}

function getModules($str)
{
	global $contents, $mycontent;
	for($i = 0; $i < count($str); $i++) {
		$querychar = "SELECT * FROM $contents WHERE ID='$str[$i]'";
		// insert here the code to see is $str[$i] exists - if not replace with 0
		$result = mysql_query($querychar) or die( mysql_error() );
		$moduleindex = mysql_fetch_array( $result );
		$modulename = $moduleindex['Module'];
		$modulecode = $moduleindex['Code'];

		$mycontent = str_replace("%". $modulename ."%", $modulecode, $mycontent);
  	}
}

?>