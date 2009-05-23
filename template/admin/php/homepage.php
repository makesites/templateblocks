<?php

require( '../../config.php' );

require( '../../classes/Common.php' );
$common = new Common();

$task = ( $_REQUEST['task'] ) ? $_REQUEST['task'] : '';

updateHomepage( $task );

  ## loads the dynamic content of the homepage
  function updateHomepage( $task='' ){
    global $common;

	// the number of items to display from each feed
    $max_items = 5;
	
    switch( $task ){

      case 'version':
	    $xml = $common->getXML( 'http://www.templateblocks.com/support/wp-rss2.php?cat=3' );
        if( $xml ){
          $xml_content = $xml->channel;
          $latest_version = $xml_content->item[0];
	      echo ( empty($latest_version) ) ? 'N/A' : str_replace( 'Version ', '', $latest_version->title );
		}
      break;

      case 'repository':
	    $xml = $common->getXML( 'http://www.templateblocks.com/support/wp-rss2.php?cat=6' );
        if( $xml ){
          $xml_content = $xml->channel;
		  $num_items = 0;
          foreach( $xml_content->item as $item ){
		    if( $num_items < $max_items ){
		      echo '<h4><a href="' . $item->link . '">' . $item->title . '</a></h4>' . "\n";
              echo '<small>' . substr( $item->pubDate, 0, -6 ) . '</small>' . "\n";
		      echo '<p>' . substr( $item->description, 0, strpos($item->description, "\n") ) . '</p>' . "\n";
			}
			$num_items++;
          }
        }
      break;

      case 'comments':
	    $xml = $common->getXML( 'http://www.templateblocks.com/support/wp-commentsrss2.php' );
        if( $xml ){
          $xml_content = $xml->channel;
		  $num_items = 0;
          foreach( $xml_content->item as $item ){
		    if( $num_items < $max_items ){
		      echo '<h4><a href="' . $item->link . '">' . $item->title . '</a></h4>' . "\n";
              echo '<small>' . substr( $item->pubDate, 0, -6 ) . '</small>' . "\n";
		      echo '<p>' . substr( $item->description, 0, strpos($item->description, "\n") ) . '</p>' . "\n";
			}
		    $num_items++;
          }
        }
      break;

	}

  }

?>