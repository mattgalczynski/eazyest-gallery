<?php
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 

/**
 * Eazyest_Ajax
 * 
 * @package Eazyest Gallery
 * @subpackage Admin/Ajax
 * @author Marcel Brinkkemper
 * @copyright 2012 Brimosoft
 * @since 0.1.0 (r2)
 * @version 0.1.0 (r2)
 * @access public
 */
class Eazyest_Ajax {
	
	/**
	 * @staticvar Eazyest_Ajax $instance The single object in memory
	 */
	private static $instance;
	
	/**
	 * Eazyest_Ajax::__construct()
	 * 
	 * @return void
	 */
	function __construct() {
	}
	
	/**
	 * Eazyest_Ajax::init()
	 * 
	 * @return void
	 */
	private function init() {		
		$this->actions();
	}
	
	/**
	 * Eazyest_Ajax::instance()
	 * create Eazyest_Akax instance
	 * 
	 * @since 0.1.0 (r2)
	 * @return Eazyest_Ajax object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Eazyest_Ajax;
			self::$instance->init();
		}
		return self::$instance;		
	}
	
	/**
	 * Eazyest_Ajax::actions()
	 * Add ajax actions used by Eazyest Gallery
	 * 
	 * @since 0.1.0 (r2)
	 * @uses add_action() 
	 * @return void
	 */
	function actions() {
		$actions = array( 'upload', 'filetree', 'select_dir', 'gallery_folder_change' );
		foreach( $actions as $action ) {
			add_action( "wp_ajax_eazyest_gallery_$action", array( $this, $action ) );
		}
	}
	
	/**
	 * Eazyest_Ajax::upload()
	 * Refresh the attachment list table after imaage uploads
	 * 
	 * @since 0.1.0 (r2)
	 * @uses check_ajax_referer()
	 * @uses get_post()
	 * @return void
	 */
	function upload() {		
		$post_id = isset( $_POST['post'] ) ? $_POST['post'] : 0;
		if ( check_ajax_referer( 'update-post_' . $post_id ) ) {
			global $post;
			$post = get_post( $post_id );
	  	require_once( eazyest_gallery()->plugin_dir . 'admin/class-eazyest-media-list-table.php' );  	
	  	$list_table = new Eazyest_Media_List_Table( array( 'plural' => 'media'  ) );  	
			$list_table->prepare_items();
			if ( $list_table->has_items() ) {			
				$list_table->views();
				$list_table->display();
			}
		}
		wp_die();
	}
	
	/**
	 * Eazyest_Ajax::filetree()
	 * Return sub-directory listing on request of jquery.filetree
	 * 
	 * @since 0.1.0 (r2)
	 * @uses check_ajax_referer() 
	 * @return void
	 */
	function filetree() {		
		$content_dir = str_replace( '\\', '/', WP_CONTENT_DIR );
		if ( check_ajax_referer( 'filetree' ) ) {
			$dir = urldecode( $_POST['dir'] );
			if( file_exists( $dir ) ) {
				$files = scandir( $dir );
				natcasesort( $files );
				 // the 2 accounts for . and .. 
				if( count( $files ) > 2 ) {
					echo "<ul class='jquery-filetree' style='display: none;'>";
					// All dirs
					foreach( $files as $file ) {
						$file = str_replace( '\\', '/', $file );
						if( file_exists( $dir . $file ) ) {
							if ( eazyest_folderbase()->valid_dir( $dir . $file ) ){
								if ( ( $dir . $file == $content_dir ) || ( ! eazyest_folderbase()->is_dangerous( $dir . $file ) ) ) {									
									echo "<li class='directory collapsed'><a href='#' rel='" . htmlentities( $dir . $file ) . "/'>" . htmlentities( $file ) . "</a></li>";
								}
								
							}
						}
					}				
					echo "</ul>";	
				}
			}
		}
		wp_die();
	}
	
	/**
	 * Eazyest_Ajax::select_dir()
	 * Reurn relative directory when user clicks directory in filetree dropdown
	 * 
	 * @since 0.1.0 (r2)
	 * @uses check_ajax_referer()
	 * @return void
	 */
	function select_dir() {
		if ( check_ajax_referer( 'filetree' ) ) {
			$dir = urldecode( $_POST['dir'] );
			if ( file_exists( $dir ) ) {
				$abspath = str_replace( '\\', '/', ABSPATH );
				echo eazyest_gallery()->get_relative_path( $abspath, $dir );
			}
		}
		wp_die();
	}
	
	/**
	 * Eazyest_Ajax::gallery_folder_change()
	 * Echo 1 if gallery folder path exists
	 * @return void
	 */
	function gallery_folder_change() {
		check_ajax_referer( 'gallery-folder' );
		eazyest_gallery()->change_option( 'gallery_folder', $_POST['gallery_folder'] );
		$result = 0;
		if ( eazyest_folderbase()->is_dangerous( eazyest_gallery()->root() ) )
			$result = 1;
		if ( eazyest_gallery()->right_path() )
			$result = $result + 10;
		echo str_pad( strval( $result ), 2, '0', STR_PAD_LEFT );	
		wp_die();	
	}
	
} // Eazyest_Ajax