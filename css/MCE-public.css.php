<?php

global $mce_fonts;

// include "WP core"
$root_path = realpath( $_SERVER['DOCUMENT_ROOT'] );
include_once( $root_path . '/wp-blog-header.php' );

// include plugin
$this_dir = pathinfo( $_SERVER['PHP_SELF'], PATHINFO_DIRNAME );
include_once( $root_path .'/'. $this_dir .'/../MCE-classes.php' );

// get list and output
$font_list = $mce_fonts->get_font_list();
foreach( $font_list as $font ) {
	echo $font['css'] ."\n\n";
}

?>