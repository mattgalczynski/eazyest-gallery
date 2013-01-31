<?php
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 

/**
 * Eazyest_Admin
 * 
 * This class contains all functions and actions required for Eazyest Gallery to work in the WordPress admin
 * 
 * @package Eazyest Gallery
 * @subpackage Admin
 * @author Marcel Brinkkemper
 * @copyright 2010-2012 Brimosoft
 * @version 0.1.0 (r22)
 * @access public
 * @since lazyest-gallery 0.16.0
 * 
 */
class Eazyest_Admin {
	
	/**
	 * @var array $data overloaded vars
	 * @access private
	 */ 
	private $data;
	
	/**
	 * @staticvar Eazyest_Admin $instance single object in memory
	 */
	private static $instance;
  
  /**
   * Eazyest_Admin::__construct()
   * 
   * @return void
   */
  function __construct() {}

	/**
	 * Eazyest_Admin::__isset()
	 * 
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) { 
		return isset( $this->data[$key] ); 
	}
	
	/**
	 * Eazyest_Admin::__get()
	 * 
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) { 
		return isset( $this->data[$key] ) ? $this->data[$key] : null; 
	}
	
	/**
	 * Eazyest_Admin::__set()
	 * 
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	public function __set( $key, $value ) { 
		$this->data[$key] = $value; 
	}
	
	/**
	 * Eazyest_Admin::init()
	 * Initialize
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	private function init() {
		$this->setup_variables();
		$this->includes(); 
		$this->actions();
	}
	
	/**
	 * Eazyest_Admin::instance()
	 * Eazyest Admin should be loaded once
	 * 
	 * @since 0.1.0 (r2)
	 * @return Eazyest_Admin object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Eazyest_Admin;
			self::$instance->init();
		}
		return self::$instance;		
	}
  
  /**
   * Eazyest_Admin::setup_variables()
   * 
	 * @since 0.1.0 (r2) 
   * @return void
   */
  function setup_variables() {
  	if ( defined( 'DOING_AJAX' ) ) {
  		$this->ajax();
		}
		$this->folder_editor();
  }
	
	/**
	 * Eazyest_Admin::theme_compatible()
	 * 
	 * @since 0.1.0 (r61)
	 * @return string|bool name of theme when compatible theme is used | false if not
	 */
	function theme_compatible() {		
		$theme = basename( TEMPLATEPATH );
		if ( in_array( $theme, array( 'twentyten', 'twentyeleven', 'twentytwelve' ) ) )
			return $theme;
		else
			return false;	
	}
  
  /**
   * Eazyest_Admin::includes()
   * Include files.
   * 
   * @since 0.1.0 (r2)
   * @return void
   */
  function includes() {
  	// tools
  	include( eazyest_gallery()->plugin_dir . 'tools/class-eazyest-upgrader.php' );
  	if ( $theme = $this->theme_compatible() ) {
  		include( eazyest_gallery()->plugin_dir . 'themes/' . $theme . '/functions.php' );
  	}
  }
  
  /**
   * Eazyest_Admin::actions()
   * add WordPress actions
   * 
   * @since 0.1.0 (r2)
   * @uses add_action()
   * @return void
   */
  function actions() {
  	add_action( 'admin_init', array( $this, 'register_setting' ) );
  	add_action( 'admin_menu', array( $this, 'admin_menu'       ) );
  }
  
  /**
   * Eazyest_Admin::register_setting()
   * Register eazyest-gallery setting for options pagee
   * 
   * @since 0.1.0 (r2)
   * @uses register_setting()
   * @return void
   */
  function register_setting() {
  	register_setting( 'eazyest-gallery', 'eazyest-gallery', array( $this, 'sanitize_settings' ) );
  }
  
  /**
   * Eazyest_Admin::sanitize_settings()
   * Sanitize options after before saving to wpdb
   * 
   * @since 0.1.0 (r2)
   * @param array $options
   * @return array sanitized $options
   */
  function sanitize_settings( $options ) {
  	$defaults = eazyest_gallery()->defaults();
  	// eazyest gallery cannot work if gallery folder does not exist
  	$options['gallery_folder'] = str_replace( '\\', '/', $options['gallery_folder'] );
  	
		$gallery_folder = eazyest_gallery()->get_absolute_path(  ABSPATH . $options['gallery_folder'] );
  	if ( eazyest_folderbase()->is_dangerous( $gallery_folder ) ) {			
			$options['new_install']    = true;
  		$options['gallery_folder'] = $defaults['gallery_folder'];
			add_settings_error( __( 'eazyest-gallery', 'eazyest-gallery' ), 'gallery_folder', __( 'The folder you have selected cannot be used for a gallery', 'eazyest-gallery'), 'error' );
		}
  	
		// if gallery folder does not exist, user should visit settings page again
		if ( ! file_exists( eazyest_gallery()->get_absolute_path( ABSPATH . $options['gallery_folder'] ) ) ) {			
			$options['new_install']    = true;
  		$options['gallery_folder'] = $defaults['gallery_folder'];
			add_settings_error( __( 'eazyest-gallery', 'eazyest-gallery' ), 'gallery_folder', __( 'The folder you have selected does not exist', 'eazyest-gallery'), 'error' );
		}		
		
		// other fields to sanitize
		foreach ( $defaults as $setting => $value ) {
			switch( $setting ) {
				case 'folders_page' :
				case 'folders_columns' :
				case 'thumbs_page' :
				case 'thumbs_columns' :
					$options[$setting] = absint( $options[$setting] );
					break;
				case 'listed_as' :
					$options[$setting]	= esc_html( $options[$setting] );
					break;
				case 'gallery_slug' :
					if ( $options[$setting] != eazyest_gallery()->gallery_slug ) {
						set_transient( 'eazyest-gallery-flush-rewrite-rules', true, 0 );
					}
					$options[$setting]	= sanitize_title( $options[$setting] );
					break;
				case 'new_install' :
				case 'show_credits' :
				case 'random_subfolder' :
				case 'thumb_caption' :
				case 'thumb_description' :
				case 'enable_exif' :
					$options[$setting] = isset( $options[$setting] ) ? true : false;
					break;
				default :
					$options[$setting] = isset( $options[$setting] ) ? $options[$setting] : $value;		
			}
		}
		if ( ( ! $options['thumb_caption']) )
			$options['thumb_description'] = false;
  	return $options;
  }
  
  /**
   * Eazyest_Admin::admin_menu()
   * The Eazyest Gallery menu pages
   * 
   * @since 0.1.0 (r2)
   * @uses add_options_page()
   * @return void
   */
  function admin_menu() { 	
  	$settings = add_options_page(
  		__( 'Eazyest Gallery Settings', 'eazyest-gallery' ),
  		eazyest_gallery()->gallery_name(),
  		'manage_options',
  		'eazyest-gallery',
  		array( $this->settings_page(), 'display' )
		);	
		add_action( "load-$settings", array( $this->settings_page(), 'add_help_tabs' ) );
		
		$tools = add_management_page(
  		__( 'Eazyest Gallery Tools', 'eazyest-gallery' ),
  		eazyest_gallery()->gallery_name(),
  		'manage_options',
  		'eazyest-gallery-tools',
  		array( $this->tools_page(), 'display' )
		);
  }
  
  /**
   * Eazyest_Admin::settings_page()
   * Initiate the settings page
   * 
   * @since 0.1.0 (r2)
   * @return Eazyest_Settings_page object
   */
  function settings_page() {
		require_once( eazyest_gallery()->plugin_dir . 'admin/class-eazyest-settings-page.php' );
		return Eazyest_Settings_Page::instance();
  }
  
  /**
   * Eazyest_Admin::tools_page()
   * Initiate the tools page.
   * 
   * @since 0.1.0 (r2)
   * @return Eazyest_Tools_Page object
   */
  function tools_page() {
  	require_once( eazyest_gallery()->plugin_dir . 'tools/class-eazyest-tools-page.php' );
  	return Eazyest_Tools_Page:: instance();
  }
  
  /**
   * Eazyest_Admin::folder_editor()
   * Initiate the folder editor
   * 
   * @since 0.1.0 (r2)
   * @return Eazyest_Folder_Editor object
   */
  function folder_editor() {
  	if ( eazyest_gallery()->right_path() ) {
			require_once( eazyest_gallery()->plugin_dir . 'admin/class-eazyest-folder-editor.php' );			
	  	return Eazyest_Folder_Editor::instance();  	
		}
		return null;
  }
  
  /**
   * Eazyest_Admin::ajax()
   * Initiate AJAX functionality
   * 
   * @since 0.1.0 (r2)
   * @return Eazyest_Ajax object
   */
  function ajax() {		
		require_once( eazyest_gallery()->plugin_dir . 'admin/class-eazyest-admin-ajax.php' );
		return Eazyest_Admin_Ajax::instance();
  }
  
  
} // Eazyest_Admin

/**
 * eazyest_admin()
 * 
 * @since 0.1.0 (r2)
 * @return object Eazyest_Admin
 */
function eazyest_admin() {
	return Eazyest_Admin::instance();
}
?>