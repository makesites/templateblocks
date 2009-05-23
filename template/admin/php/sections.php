<?php

require( '../../config.php' );

require( 'content.php' );

switch( $_REQUEST['task'] ) {
  case 'get-classes':
    getClasses();
  break;
}

function getClasses(){
  global $config, $html;
  
  $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $config['Template_dir'] . '/classes/';
  $files = scandir($dir);
  $exclude = array( '.', '..', 'Admin.php', 'Common.php', 'Database.php', 'Website.php' );
  
  echo '<h3>Custom Class</h3>' . "\n";
  echo '<p>' . $html['Sections']['Custom_Class'] . '</p>';
  echo '<div>' . "\n";
  echo '  <p><input type="radio" name="custom_class" value="0" /> No custom class</p>' . "\n";
  foreach($files as $file){
    if( !in_array($file, $exclude) ){
      echo '  <p><input type="radio" name="custom_class" value="' . $file . '" /> ' . $file . '</p>' . "\n";
	}
  } 
  echo '</div>' . "\n";

}

?>