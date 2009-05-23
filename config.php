<?php

# MySQL Configuration
$db_host = "localhost";    // 99% chance you will not need to change this value
$db_name = "kdiweb7_testing";    // The name of the database
$db_user = "kdiweb7_user";     // Your MySQL username
$db_pass = "mNj2I3a"; // ...and password

// You can have multiple installations in one database if you give each a unique prefix
$db_prefix = "makis_";   // Only numbers, letters, and underscores please!

$template_path = "/home/kdiweb7/public_html/upload_port/makis/scripts";

# Administration
$username = "localhost";
$password = "localhost";


########  YOU DO NOT HAVE TO EDIT BELOW THIS LINE ########

$webpages = $db_prefix . "webpages";
$contents = $db_prefix . "content";
$modules = $db_prefix . "modules";
$templates = $db_prefix . "templates";

?>