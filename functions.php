<?php 
define('TWP_THEME_PATH', get_template_directory() );
define('TWP_THEME_URL', get_template_directory_uri() );
define('TPW_THEME_VERSION', '0.7.0' );


function tpw_setup(){

	add_theme_support( "title-tag" ) ;
	add_theme_support( "post-thumbnails" );

  // Add HTML5 Support
  add_theme_support( 'html5',
           array(
            'comment-list',
            'comment-form',
            'search-form',
           )
  );

	add_editor_style();

  themename_check_update();

}
add_action( 'after_setup_theme', 'tpw_setup');



/**
 * Enqueue Theme Styles
 */
function tpw_setup_enqueue_styles() {
		
	if (!is_admin()) {    
    // Template Main CSS File
    wp_enqueue_style( 'tpw-style', TWP_THEME_URL . '/css/style.css', array(), TPW_THEME_VERSION);        
		wp_enqueue_style( 'tpw', get_stylesheet_uri(), array(), TPW_THEME_VERSION);

	}

}
add_action( 'wp_enqueue_scripts', 'tpw_setup_enqueue_styles' );


/**
 * Enqueue Theme Scripts
*/
function tpw_enqueue_scripts() {
  	
	wp_enqueue_script( 'tpw', TWP_THEME_URL . '/js/main.js',  array('jquery'), TPW_THEME_VERSION, true );
			
}
add_action( 'wp_enqueue_scripts', 'tpw_enqueue_scripts');


/*
*
* CÓDIGO PARA COMPROBAR ACTUALIZACIONES
*
*/
require_once 'includes/class-themename-updater.php';

// Comprobamos si hay versiones nuevas del tema
function themename_check_update() {
	if ( is_admin() ) {

		$theme_name = basename( dirname(__FILE__) ) ;
		$config  = array(
			'github_uri' => 'https://api.github.com/repos/PisanoWP/theme-wpupdater/releases',			                 
			'token'      => false,
		);
		$updater = new WPTHEMENAME_Updater( $theme_name, $config);
		$updater->check_update();		
    
	}
} // check_update
