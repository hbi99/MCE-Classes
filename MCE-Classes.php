<?php
/**
 * Plugin Name: MCE Classes
 * Plugin URI: http://
 * Description: 
 * Version: 0.0.1
 * Author: Hakan Bilgin
 * Author URI: http://
 * License: GPLv2 or later
 */

class MCE_Classes {

	function __construct() {
		//******** settings
		$this->settings = array(
			'path'    => plugin_dir_url( __FILE__ ),
			'dir'     => plugin_dir_path( __FILE__ ),
			'version' => '0.0.1'
		);
		//******** ajax
		add_action( 'wp_ajax_MCE_Classes/save_classes', array( $this, 'save_classes' ) );
		
		add_action( 'init', array( $this, 'init' ), 1 );
	}

	function __destruct() {
		
	}

	function init() {
		//******** scripts
		$this->scripts = array(
			array(  'handle' => 'MCE_Classes-admin_script',
					'src'    => $this->settings['path'] . 'js/MCE_Classes.js',
					'deps'   => array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' )
			),
		);
		foreach( $this->scripts as $script ) {
			wp_register_script( $script['handle'], $script['src'], $script['deps'] );
		}

		//******** styles
		$this->styles = array(
			'MCE_Classes-editor_style' => $this->settings['path'] . 'css/MCE-public.css.php',
			'MCE_Classes-admin_style' => $this->settings['path'] . 'css/MCE_Classes.css'
		);
		foreach( $this->styles as $handle => $src ) {
			wp_register_style( $handle, $src, false, $this->settings['version'] );
		}

		//******** ajax nonce
		$nonce = array(
			'ajax_path'  => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => esc_js( wp_create_nonce( 'MCE_Classes_nonce') ),
		);
		wp_localize_script( 'MCE_Classes-admin_script',  'mcef_cfg', $nonce );
		
		if ( is_admin() ) {
			//******** filters
			add_filter( 'mce_buttons', array( $this, 'add_font_family_to_mce' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'populate_fonts' ) );
			
			//******** actions
			add_action( 'admin_menu', array( $this, 'MCE_Classes_options_menu' ) );
			add_filter( 'mce_css', array( $this, 'add_css_mce_editor' ) );
		} else {
			wp_enqueue_style( array( "MCE_Classes-editor_style" ) );
		}
	}

	function add_css_mce_editor($wp) {
		$wp .= ','. $this->styles['MCE_Classes-editor_style'];
		return $wp;
	}

	function get_font_list() {
		$ret = array();
		$fonts_len = get_option( 'MCE_Classes_len' );
		if ( isset( $fonts_len ) ) {
			for ($i=1; $i<=$fonts_len; $i++) {
				$ret[ 'mce_font-'. $i ] = get_option( 'mce_font-'. $i );
				$ret[ 'mce_font-'. $i ][ 'css' ] = urldecode( $ret[ 'mce_font-'. $i ][ 'css' ] );
			}
		}
		return $ret;
	}

	function save_classes() {
		// check nonce
		if ( !wp_verify_nonce( $_POST['nonce'], 'MCE_Classes_nonce' ) ) {
			die( -1 );
		}
		// send update package
		$data = $_POST['data'];
		// remove previous list
		$fonts_len = get_option( 'MCE_Classes_len' );
		if ( isset( $fonts_len ) ) {
			for ($i=1; $i<$fonts_len; $i++) {
				delete_option( 'mce_font-'+ $i );
			}
		}
		$len = count( $data );
		for ($i=0; $i<$len; $i++) {
			update_option( 'mce_font-' . ($i+1), array(
				'name'      => $data[$i]['name'],
				'classname' => $data[$i]['class'],
				'css'       => $data[$i]['css']
			) );
		}
		update_option( 'MCE_Classes_len', $len );
		
		die( 1 );
	}

	/*
	function add_font_family_to_mce( $mce_buttons ) {
		$strikethrough = array_search( 'strikethrough', $mce_buttons );
		$fullscreen    = array_search( 'fullscreen', $mce_buttons );
		$wp_more       = array_search( 'wp_more', $mce_buttons );
		unset( $mce_buttons[ $strikethrough ] );
		unset( $mce_buttons[ $fullscreen ] );
		unset( $mce_buttons[ $wp_more ] );
		array_splice( $mce_buttons, $strikethrough, 0, 'styleselect' );
		return $mce_buttons;
	}

	function populate_fonts($init ) {
		$style_formats = array(
			// Each array child is a format with it's own settings
			array(
				'title' => 'Big text',
				'inline' => 'span',
				'classes' => 'transl',
				'wrapper' => false,
			),
		);  
		// Insert the array, JSON ENCODED, into 'style_formats'
		$init['style_formats'] = json_encode( $style_formats );  
		return $init;
	}
	*/
	function add_font_family_to_mce( $mce_buttons ) {
		$strikethrough = array_search( 'strikethrough', $mce_buttons );
		$fullscreen    = array_search( 'fullscreen', $mce_buttons );
		$wp_more       = array_search( 'wp_more', $mce_buttons );
		unset( $mce_buttons[ $strikethrough ] );
		unset( $mce_buttons[ $fullscreen ] );
		unset( $mce_buttons[ $wp_more ] );
		array_splice( $mce_buttons, $strikethrough, 0, 'fontselect' );
		return $mce_buttons;
	}

	function populate_fonts($init ) {
		$font_list = $this->get_font_list();
		$str = '';
		foreach( $font_list as $font ) {
			$str .= $font['name'] .'='. $font['classname'] .';';
		};
		$init['theme_advanced_fonts'] = $str;
		return $init;
	}

	function MCE_Classes_options_menu() {
		add_options_page( 'MCE Classes Options', 'MCE Classes', 'manage_options', 'MCE_Classes_options', array( $this, 'MCE_Classes_options_page' ) );
	}

	function MCE_Classes_options_page() {
		include_once( $this->settings['dir'] . 'options.php' );
	}

}

function MCE_Classes() {
	global $MCE_Classes;
	if( !isset( $MCE_Classes ) ) {
		$MCE_Classes = new MCE_Classes();
	}
	return $MCE_Classes;
}

// initialize
MCE_Classes();

?>