<?php

// this is the name of the section you have assigned for this script
$GLOBALS['section'] = '{{SECTION}}';

// define here your custom class
{{CUCTOM_CLASS}}

// on to load our template
include( $_SERVER['DOCUMENT_ROOT'] . '/{{TEMPLATE_DIR}}/index.php' );

// output the template section before the script content
echo $GLOBALS['template-pre'];

include( '{{FILENAME}}-original.php' );

// output the template section after the script content
echo $GLOBALS['template-post'];

?>