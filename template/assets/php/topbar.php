<?php

$items = $db->select( '*', $config['Sections'], false, 'id');
if( is_array($items) ){
  $children = array();
  foreach( $items as $k => $v ){
    preg_match('/(.*)\((.*)\)/', $v['position'], $matches);

    if($matches[2] == 'x') {
      array_push($children[$matches[1]], $v['id']);
    } else {
      $children[$matches[1]][$matches[2]] = $v['id'];
    }
  }
  $output = createOrder( $items, $children );
}
echo '<div id="topbar">';
echo $output;
echo '</div>';

function createOrder( $items, $order, $parent=0, $path='' ){

  ksort($order[$parent]); 

  $listings .= '<ul>';

  $middle = '</span></a>';
  $close = '</li>';
  foreach( $order[$parent] as $k => $v ){
    foreach( $items as $l => $w ){
      // now we can create the listings
      if( $w['id'] == $v ){
        $open = '<li><a href="./'. $path . $w['slug'] .'.html"><span>';
        // check for children in this branch
        if (array_key_exists($w['id'], $order)) {
		  $new_path = $path . $w['slug'] . '/';
	      $children = createOrder( $items, $order, $w['id'], $new_path );
	    } else {
	      $children = '';
	    }
	  $listings .= $open . $w['title'] . $middle .  $children . $close;
      }
    }
  }
  $listings .= '</ul>';

  return $listings;  
}

?>