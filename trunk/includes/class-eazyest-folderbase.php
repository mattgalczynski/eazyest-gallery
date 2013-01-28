<?php
/**
 * Eazyest_FolderBase
 * Handels all functions related to post_type galleryfolder and their attachments
 * 
 * @package Eazyest Gallery
 * @subpackage Folders
 * @author Marcel Brinkkemper
 * @copyright 2012 Brimosoft
 * @since @since 0.1.0 (r2)
 * @version 0.1.0 (r40)
 * @access public
 */

 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;  
 
class Eazyest_FolderBase {
	/**
	 * @var private array of posted folder paths
	 */
	private $posted_paths;
	
	/**
	 * @var private array of folder paths
	 */
	private $folder_paths;
	
	/**
	 * @var private array of posted images
	 */
	private $posted_images;
	
	/**
	* @var private array of images in folder
	*/
  private $folder_images;
  
  /**
   * @var number of new (+) or deleted (-) images found the last time collect_images() run
	 */ 
  private $images_collected;
  
  /**
   * @var number of new (+) or deleted (-) folders found the last time collect_folders() run
	 */
  private $folders_collected;
  
  /**
   * @staticvar Eazyest_FolderBase $instance single object in memory
   */ 
  private static $instance;
  
  /**
   * Eazyest_FolderBase::__construct()
   * 
   * @return void
   */
  function __construct() {}
  
  /**
   * Eazyest_FolderBase::init()
   * 
   * @since 0.1.0 (r2)
   * @return void
   */
  private function init() {
  	$this->actions();
		$this->filters();
		$this->register_post_types();
  }
  
  /**
   * Eazyest_FolderBase::instance()
   * 
   * @since 0.1.0 (r2)
   * @return Eazyest_FolderBase object
   */
  public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Eazyest_FolderBase;
			self::$instance->init();
		}
		return self::$instance;  	
  }
	
	/**
	 * Eazyest_FolderBase::actions()
	 * Add WordPress actions
	 * @uses apply_filter() for 'eazyest_gallery_insert_folder_action' to set action before the images list is built
	 * filtered value can be either: 
	 *  'collect_images' : collect new (ftp) uploaded images when new uploaded folder has been found and inserted in the WP database
	 *                     this could potentially create many transactions when you open admin 
	 *  'no_action'      : take no action ( default )
	 * 
	 * @since 0.1.0 (r2)
	 * @uses add_action() for 'save_post', 'before_delete_post' and 'eazyest_gallery_insert_folder'
	 * @return void
	 */
	function actions() {
		$insert_folder_action = apply_filters( 'eazyest_gallery_insert_folder_action', 'no_action' );
		
		add_action( 'save_post',                     array( $this, 'save_post'           ),  1    );
		add_action( 'save_post',                     array( $this, 'save_attachment'     ),  2    );
		add_action( 'before_delete_post',            array( $this, 'before_delete_post'  ),  1    );
		add_action( 'eazyest_gallery_insert_folder', array( $this, $insert_folder_action ), 10, 1 );
	} 
	
	/**
	 * Eazyest_FolderBase::no_action()
	 * Do nothing
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function no_action(){}
	
	/**
	 * Eazyest_FolderBase::filters()
	 * Add WordPress filters
	 * 
	 * @since 0.1.0 (r2)
	 * @uses add_filter
	 * @return void
	 */
	function filters() {
		// filters related to folders
		add_filter( 'pre_get_posts',                   array( $this, 'pre_get_posts'                ),  10    );
		// filters related to attachments and images
		add_filter( 'image_downsize',                  array( $this, 'image_downsize'               ),   5, 3 );
		add_filter( 'get_attached_file',               array( $this, 'get_attached_file'            ),  20, 2 );
		add_filter( 'update_attached_file',            array( $this, 'get_attachment_url'           ),  20, 2 );
		add_filter( 'wp_get_attachment_url',           array( $this, 'get_attachment_url'           ),  20, 2 );
		add_filter( 'update_post_metadata',            array( $this, 'update_attachment_metadata'   ),  20, 5 );
		add_filter( 'wp_image_editors',                array( $this, 'image_editors'                ), 999    );
		add_filter( 'wp_save_image_editor_file',       array( $this, 'save_image_editor_file'       ),  20, 5 );
	}
	
	// Functions related to folders ----------------------------------------------
	
	/**
	 * Eazyest_FolderBase::register_post_types()
	 * Register post types used by Eazyest Gallery
	 * 
	 * @since 0.1.0 (r2)
	 * @uses register_post_type()
	 * @return void
	 */
	function register_post_types() {
		
		$post_type = array();
		
		$post_type['labels'] = array(
			'name'               => eazyest_gallery()->gallery_title(),
			'menu_name'          => eazyest_gallery()->gallery_name(),
			'singular_name'      => __( 'Folder',                    'eazyest-gallery' ),
			'all_items'          => __( 'All Folders',               'eazyest-gallery' ),
			'add_new'            => __( 'Add New' ,                  'eazyest-gallery' ),
			'add_new_item'       => __( 'Create New Folder',         'eazyest-gallery' ),
			'edit'               => __( 'Edit',                      'eazyest-gallery' ),
			'edit_item'          => __( 'Edit Folder',               'eazyest-gallery' ),
			'new_item'           => __( 'New Folder',                'eazyest-gallery' ),
			'view'               => __( 'View Folder',               'eazyest-gallery' ),
			'view_item'          => __( 'View Folder',               'eazyest-gallery' ),
			'search_items'       => __( 'Search Folders',            'eazyest-gallery' ),
			'not_found'          => __( 'No folders found',          'eazyest-gallery' ),
			'not_found_in_trash' => __( 'No folders found in Trash', 'eazyest-gallery' ),
			'parent_item_colon'  => __( 'Parent Folder:',            'eazyest-gallery' ),
		);		
		
		$post_type['rewrite'] = array(
			'slug'       => eazyest_gallery()->gallery_slug(),
			'with_front' => true,
			'feeds'      => true,
		);		
		
		$post_type['supports'] = array(
			'title',
			'editor',
			'author',
			'custom-fields',
			'categories',
			'thumbnail',
			'comments',
			'revisions',
			'page-attributes',		
		);
		
		register_post_type(
			eazyest_gallery()->post_type, array(		
				'labels'              => $post_type['labels'],
				'rewrite'             => $post_type['rewrite'],
				'supports'            => $post_type['supports'],
				'description'         => __( 'Eazyest Gallery Folder', 'eazyest-gallery' ),
				'menu_position'       => 10,
				'exclude_from_search' => false,
				'show_in_nav_menus'   => true,
				'public'              => true,
				'show_ui'             => true,
				'can_export'          => true,
				'hierarchical'        => true,
				'query_var'           => true,
				'menu_icon'           => eazyest_gallery()->plugin_url . '/admin/images/file-manager-menu.png',
				'taxonomies'          => array('post_tag'),
				'has_archive'         => true,
			)
		);
		
		// if a new gallery_slug has been assigned
		if ( $flush = get_transient( 'eazyest-gallery-flush-rewrite-rules' ) ) {
			flush_rewrite_rules();
			delete_transient( 'eazyest-gallery-flush-rewrite-rules' );
		}		
	}
	
	/**
	 * Eazyest_FolderBase::refered_by_folder()
	 * Check if the current action is refered by the edit folder screen
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wp_get_referer()
	 * @uses get_post_type()
	 * @return integer ID of Folder being edited
	 */
	function refered_by_folder() {
		$request = explode( '&', parse_url( wp_get_referer(), PHP_URL_QUERY ) );
		$post_id = 0;
		if ( ! empty( $request ) ) {
			foreach( $request as $part )
				if ( false !== strpos( $part, 'post' ) ) {
					$post_id = intval( substr( $part, 5 ) );
					$post_id = get_post_type( $post_id ) == eazyest_gallery()->post_type ? $post_id : 0;
				}
		}		
		return $post_id;	
	}
	
	/**
	 * Eazyest_FolderBase::pre_get_posts()
	 * Add gallery post_type to tag query
	 * 
	 * @since 0.1.0 (r2)
	 * @uses WP_Query::get()
	 * @uses WP_Query::set()
	 * @param WP_Query $query
	 * @return WP_Query object
	 */
	function pre_get_posts( $query ) {
		// order by from eazyest-galery options
		if ( eazyest_gallery()->post_type == $query->get( 'post_type' ) ) {
			if ( ! isset( $_REQUEST['orderby'] ) ) {
				// set sort order from options
				$option = explode( '-', eazyest_gallery()->sort_by() );
				$order_by = $option == 'menu_order-ASC' ? 'menu_order' :  substr( $option[0], 5 );
				$query->set( 'orderby', $order_by );
				$query->set( 'order',   $option[1] );
			}
			
			if ( 'menu_order-ASC' == eazyest_gallery()->sort_by() && empty( $query->query_vars['post_parent'] ) )
				$query->set( 'post_parent', 0 );
		}	
		
		// add galleryfolder to tag query
  	if ( is_tag() ) {
			$post_type = $query->get( 'post_type' );						
			if( $post_type )
	    	$post_type = $post_type;
			else
	    	$post_type = array( 'post', eazyest_gallery()->post_type );
    	$query->set( 'post_type', $post_type );
		}
		
		// show only images attached to folder if query-attachments
		if ( isset( $_REQUEST['action'] ) && 'query-attachments' == $_REQUEST['action'] ) {
			$post_id = $this->refered_by_folder();
			if ( $post_id )
				$query->set( 'post_parent', $post_id ); 
		}    				
		return $query;
	}
	
	/**
	 * Eazyest_FolderBase::save_post()
	 * Store meta data and attachment changes when a galleryfolder is saved
	 * 
	 * @since 0.1.0 (r2)
	 * @param int $post_id
	 * @return void
	 */
	function save_post( $post_id ) {
		// don't run on autosave
		if ( defined( 'DOING_AUTOSAVE' ) || defined( 'LAZYEST_GALLERY_UPGRADING' ) )
			return;
		// don't  run if not initiated from edit post	
		if ( ! isset( $_POST['action'] ) )	
			return;				
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == eazyest_gallery()->post_type ) {
			// only do this for post type galleryfolder 
			$this->save_gallery_path( $post_id );
			$this->save_attachments( $post_id );
		}
	}
	
	/**
	 * Eazyest_FolderBase::save_attachment()
	 * When a single attachment is saved, the post_excerpt (caption) is copied from post_title
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post()
	 * @uses get_post_type()
	 * @param mixed $post_id
	 * @return
	 */
	function save_attachment( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) || defined( 'LAZYEST_GALLERY_UPGRADING' ) )
			return;
		// don't run if not initiated from edit post	
		if ( ! isset( $_POST['action'] ) )	
			return;				
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'attachment' ) {
			$attachment = get_post( $post_id );
			if ( eazyest_gallery()->post_type == get_post_type( $attachment->post_parent ) ) {
				if ( empty( $attachment->post_excerpt ) ) {
					$attachment->post_excerpt = $attachment->post_title;
					wp_update_post( $attachment );
				}
			}
		}
	}
	
	/**
	 * Eazyest_FolderBase::save_gallery_path()
	 * Save and/or create the file system path for this folder
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wp_unique_post_slug()
	 * @uses sanitize_title()
	 * @uses wp_mkdir_p()
	 * @uses trailingslashit()
	 * @uses update_post_meta()
	 * @param int $post_id
	 * @return void
	 */
	function save_gallery_path( $post_id ) {
		$gallery_path = isset( $_POST['gallery_path'] ) ? $_POST['gallery_path'] : '';
		 
		// when gallery path is not set, construct one		
		if ( '' == $gallery_path ) {			
			// use post_name for folder name or sanitize title if not set
			$gallery_path = isset( $_POST['post_name'] ) ? $_POST['post_name'] : '';
			
			if ( ( '' == $gallery_path ) ) {
	  		// post name has not been set yet, but we need a slug to make a directory to store images
				$gallery_path = wp_unique_post_slug( 
					sanitize_title( $_POST['post_title'] ), 
					$post_id, 
					'fake', 
					eazyest_gallery()->post_type, 
					$_POST['post_type']
				); 
			}
			 
			// possibly append to post parent
			if ( isset( $_POST['post_parent'] ) ) {
				$parent_path = get_post_meta( $_POST['post_parent'], 'gallery_path', true );
				$gallery_path = '' == $parent_path ? $gallery_path : trailingslashit( $parent_path ) . $gallery_path; 
			}
			
			// check directory on file system
			$new_directory = eazyest_gallery()->root() . $gallery_path;
			if ( ! file_exists( $new_directory ) )
				wp_mkdir_p( $new_directory );
			if ( file_exists( $new_directory ) ) {
				// only save when gallery path exists
				update_post_meta( $post_id, 'gallery_path', $gallery_path ); 			
			}			
		}	
	}
	
	/**
	 * Eazyest_FolderBase::save_attachments()
	 * Save attachments fields edited in the attachment list table	 * 
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post()
	 * @uses wp_update_post()
	 * @param int $post_id ID for attachment parent folder
	 * @return void
	 */
	function save_attachments( $post_id ) {
		if ( isset( $_POST['attachment'] ) && is_array( $_POST['attachment'] ) ) {
			$reordered = isset( $_POST['gallery-changed-media'] ) && $_POST['gallery-changed-media'];
			foreach( $_POST['attachment'] as $item_id => $fields ) {
				$attachment = get_post( $item_id );
				foreach( $fields as $field => $value ) {
					$attachment->$field = sanitize_text_field( $value );
					if ( $reordered )
						$attachment->menu_order = array_search( $attachment->ID, explode( ' ', $_POST['gallery-order-media'] ) );
				}
				if ( empty( $attachment->post_title ) ) {
					$pathinfo = pathinfo( $attachment->guid );
					if ( $this->replace_dashes() )
						$attachment->post_title = str_replace( array( '-', '_' ), ' ', $pathinfo['filename'] );
					else					
						$attachment->post_title = $pathinfo['filename'];
				}
				if ( empty( $attachment->post_excerpt ) )
					$attachment->post_excerpt = $attachment->post_title;
				wp_update_post( $attachment );
			}
		}
	}
	
	/**
	 * Eazyest_FolderBase::save_subfolders()
	 * Save subfolders menu order for his folder
	 * 
	 * @param integer $post_id
	 * @return void
	 */
	function save_subfolders( $post_id ) {
		if ( isset( $_POST['gallery-changed-pages'] ) && $_POST['gallery-changed-pages'] ) {
			foreach( $_POST['gallery-order-pages'] as $menu_order => $item_id ) {
				$subfolder = get_post( $item_id );
				if ( $menu_order != $subfolder->menu_order ) {
					$subfolder->menu_order = $menu_order;
					wp_update_post( $subfolder );
				}
			}
		}
	}
	
	/**
	 * Eazyest_FolderBase::goto_parent()
	 * Move file system path if parent-directory gets deleted and post_parent has other directory
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post_meta()
	 * @uses update_post_meta()
	 * @uses get_children()
	 * @uses wp_update_post(
	 * @uses wp_die()
	 * @param mixed $sub_id
	 * @return void
	 */
	function goto_parent( $sub_id ) {
		$sub = get_post( $sub_id );
		$sub_path    = get_post_meta( $sub->ID,          'gallery_path', true );
		$parent_path = get_post_meta( $sub->post_parent, 'gallery_path', true );		
		$new_path = $parent_path . '/' . basename( $sub_path );
		
		// rename path in filesystem
		if ( rename( eazyest_gallery()->root() . $sub_path, eazyest_gallery()->root() . $new_path ) ) {
			// change metadata
			update_post_meta( $sub->ID, 'gallery_path', $new_path, $sub_path );
			// check attachments
			$attachments = get_children(  array( 'post_parent' => $sub_id, 'post_type' => 'attachment' )  );
			if ( ! empty( $attachments ) ) {
				foreach( $attachments as $attachment ) {
					// update each attachment
					$old_attached = get_post_meta( $attachment->ID, '_wp_attached_file', true ); 
					update_post_meta( $attachment->ID, '_wp_attached_file', $new_path . '/' . basename( $old_attached ), $old_attached );
					$attachment->guid = eazyest_gallery()->address() . $new_path . '/' . basename( $old_attached );
					wp_update_post( $attachment );
				}
			}	
		} else {
			wp_die( __( 'Could not rename folder in file system', 'eazyest-gallery' ) );
		} 
	}
	
	/**
	 * Eazyest_FolderBase::clear_dir()
	 * Remove directory and all files and sub-directories
	 * 
	 * @since 0.1.0 (r2)
	 * @uses trailingslashit
	 * @param mixed $directory
	 * @return void
	 */
	function clear_dir( $directory ) {
		if ( empty( $directory ) )
			return;
			
		$directory = trailingslashit( $directory );
		if ( file_exists( $directory ) ) {
			if ( $handle = opendir( $directory ) ) {
				while( $file = readdir( $handle ) ) {
					if (	!	in_array(	$file, array(	'.', '..'	)	)	) {
						if ( is_file( $directory . $file ) ) 
							@unlink( $directory . $file );
						else if ( is_dir( $directory . $file ) ) 
							$this->clear_dir ( $directory . $file );
					}
				}
				@rmdir( $directory );
			}
			if ( is_resource( $handle ) )
				closedir( $handle );
		}
	}
	
	/**
	 * Eazyest_FolderBase::before_delete_post()
	 * Die if user attempts to delete a parent folder
	 * Delete the gallery directory for the just deleted folder
	 * Sub-directories may exist if user has selected another parent in the WordPress admin
	 * Move sub-directories to parent folder and change attachment metadata
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post_type()
	 * @uses get_children()
	 * @uses wp_die()
	 * @param mixed $postid
	 * @return void
	 */
	public function before_delete_post( $post_id ) {
		// check delete post for post_type galleryfolder
		if ( eazyest_gallery()->post_type == get_post_type( $post_id ) ) {
			// do not delete folder if it has sibbling WP_Posts
			if ( $this->has_subfolders( $post_id ) ) {
				wp_die( __( 'You cannot delete a parent folder', 'eazyest-gallery' ) );
			}
			// if it has subdirectories, but folder has changed parent, relocate directory and attachments
			// links in posts will be broken, but images still exist
			$subdirectories = $this->get_subdirectories( $post_id );
			if ( ! empty( $subdirectories ) ) {
				foreach( $subdirectories as $subdirectory ) {
					$path = $gallery_path . '/' . $directory;
					$sub_id = $this->get_folder_by_path( $path );
					$this->goto_parent( $sub_id );
					// remove all attachments and files in this folder/directory
					$attachments = get_children(  array( 'post_parent' => $sub_id, 'post_type' => 'attachment' )  );
					if ( ! empty( $attachments ) ) {
						foreach( $attachments as $attachment )
							wp_delete_attachment( $attachment->ID, true );
					} 
				}
			}
			$gallery_path = get_post_meta( 'gallery_path', $post_id );
			$delete_dir = eazyest_gallery()->root() . $gallery_path;
			$this->clear_dir( $gallery_path ); 	
		}
	}
	
	/**
	 * Eazyest_FolderBase::get_subdirectories()
	 * Get an array of subdirectory paths
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post_meta()
	 * @param integer $post_id
	 * @return array
	 */
	public function get_subdirectories( $post_id ) {
		$directory = get_post_meta( $post_id, 'gallery_path', true );
		$paths = $this->get_folder_paths();
		foreach( $paths as $key => $path ) {
			if ( false === strpos( $path, $directory ) || strlen( $path ) == strlen( $directory ) )
				unset( $paths[ $key] );
		}
		return $paths;
	}
	
	/**
	 * Eazyest_FolderBase::get_subfolders()
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_children()
	 * @param int $post_id
	 * @return array of WP_Post subfolders
	 */
	public function get_subfolders( $post_id ) {
		return get_children( array( 'post_parent' => $post_id, 'post_type' => eazyest_gallery()->post_type ) );
	}
	
	/**
	 * Eazyest_FolderBase::has_subfolders()
	 * Check if folder has subfolders
	 * 
	 * @since 0.1.0 (r2)
	 * @param mixed $post_id
	 * @return
	 */
	public function has_subfolders( $post_id ) {
		$has_subfolders = $this->get_subfolders( $post_id );
		return ! empty( $has_subfolders );
	}
	
	/**
	 * Eazyest_FolderBase::get_posted_paths()
	 * Selects all posted gallery paths from postmeta
	 * If $post_id is set, search for folders child of $post_id
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wpdb::get_col()
	 * @param string $reset
	 * @return array
	 */
	private function get_posted_paths( $post_id = '', $cached = 'cached' ) {
		
		if ( 'cached' == $cached && ! empty( $this->posted_paths ) ) {
			if ( $this->posted_paths['post_id'] == $post_id )
				return ( $this->posted_paths['folders'] );
		}
		unset( $this->posted_paths );
		global $wpdb;
		
		$query = "SELECT {$wpdb->postmeta}.meta_value FROM {$wpdb->postmeta}, {$wpdb->posts} 
							WHERE $wpdb->posts.ID=$wpdb->postmeta.post_id";
		if ( '' != $post_id )
			$query .= "
							AND $wpdb->posts.post_parent={$post_id}";							 
		$query .= "				 
							AND {$wpdb->postmeta}.meta_key='gallery_path'";
		$this->posted_paths = array( 'post_id' => $post_id, 'folders' => $wpdb->get_col( $query ) );
		return $this->posted_paths['folders'];
	}
		
	/**
	 * Eazyest_FolderBase::excluded_folders()
	 * Folder names that have a special purpose in file system or in Eazyest Gallery
	 * These folders should not be indexed
	 * thumbnails and other sizes are stored in subfolders '_thumbnail', '_medium'', '_large'
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @uses get_intermediate_image_sizes()
	 * @return array
	 */
	function excluded_folders() {			
		$excluded_folders = apply_filters( 'eazyest_gallery_excluded_folders', 
			array(
				'cgi-bin', 
				'thumbs', 
				'slides',
				)
			);
		return apply_filters( 'eazyest_excluded_folders', $excluded_folders );	
	}

	/**
	 * Eazyest_FolderBase::valid_dir()
	 * Checks if the directory is valid to open as EazyestFolder
	 * 
	 * @since lazyest-gallery 1.0.0
	 * @param string $adir
	 * @return bool
	 */
	function valid_dir( $adir ) {
		if ( ! is_dir( $adir ) )
			return false;
		$valid = ! in_array( basename( $adir ), $this->excluded_folders() ) && 
			'_' != substr( basename( $adir ), 0, 1 )  &&
			'.' != substr( basename( $adir ), 0, 1 );
		return $valid;		
	}	

	/**
	 * Eazyest_FolderBase::_dangerous()
	 * 
	 * @since lazyest-gallery 1.1.0
	 * @uses apply_filters()
	 * @return array containg directories in which the gallery should not be.
	 */
	private function _dangerous() {
		// potentially dangerous subdirs in wp-content
		$content_dirs = array( 'themes', 'plugins', 'languages', 'upgrade', 'cache', 'wptouch-data' );
		// WordPress core dirs
		$dangerous = array(	'wp-admin',	'wp-includes'	);
		foreach( $content_dirs as $dir )
			$dangerous[] = 'wp-content/' . $dir;
		return apply_filters( 'eazyest_dangerous_paths', $dangerous );
	}

	/**
	 * Eazyest_FolderBase::is_dangerous()
	 * Check if the directory selected for the gallery could break wordpress
	 * 
	 * @since lazyest-gallery 1.1.0
	 * @uses trailingslashit()
	 * @uses untrailingslashit()
	 * @param string $directory
	 * @return bool
	 */
	function is_dangerous( $directory ) {
		$directory = trailingslashit( str_replace( '\\', '/', $directory ) );
		
		// check if gallery folder is not on server root
		if ( $directory == '/' )
	 		return true;
		
		// see if gallery is not in a WordPress core directory
		$dangerous = $this->_dangerous();				
		foreach ( $dangerous as $path ) {
			$notok = strpos( $directory, $path );
			if ( false !== $notok )
				return true;				
		}
		
		// check if gallery is not WordPress wp-content
		if ( strlen( $directory ) > 11 ) {
			if ( 'wp-content' == basename( untrailingslashit( $directory ) ) )
				return true;
		}
			
		// check if WordPress is not in a gallery subirectory
		$subdirs = $this->get_folder_paths( $directory );
		if ( ! empty( $subdirs ) ) {
			foreach( $subdirs as $dir ) {
				if ( false !== strpos( $dir, 'wp-includes' ) ) 
					return true;
			}
		}
			 				
		return false;
	}
	
	/**
	 * Eazyest_FolderBase::sort_paths()
	 * usort callback to sort paths smallest length first
	 * 
	 * @since 0.1.0 (r2)
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 */
	private function sort_paths( $a, $b ) {
		return strlen( $a ) - strlen( $b );
	} 
	
	/**
	 * Eazyest_FolderBase::_get_folder_paths()
	 * Recursively collect all folder paths from the gallery file system
	 * 
	 * @since 0.1.0 (r2)
	 * @param string $root
	 * @return array
	 */
	private function _get_folder_paths( $root = '' ) {
		$folder_paths = array();
		$root = ( $root == '' ) ? untrailingslashit( eazyest_gallery()->root() ) : $root;
		if ( $dir_handler = @opendir( $root ) ) {
			while ( false !== ( $folder_path = readdir( $dir_handler ) ) ) {
				$folder_path = trailingslashit( $root ) . $folder_path;
				if ( $this->valid_dir( $folder_path ) ) {										
					$folder_paths[] = utf8_encode( substr( str_replace( '\\', '/', $folder_path), strlen( eazyest_gallery()->root() ) ) );
					$folder_paths = array_merge( $folder_paths, $this->_get_folder_paths( $folder_path ) );
				} else {
					continue;
				}
			}
			
			@closedir( $dir_handler );
			usort( $folder_paths, array( $this, 'sort_paths' ) );
			return $folder_paths ;
		}
	}
		
	/**
	 * Eazyest_FolderBase::get_folder_paths()
	 * Get all folder paths relative to gallery root
	 * If $post_id != 0, it will return all folder_path children
	 * 
	 * @since 0.1.0 (r2)
	 * @param integer $post_id
	 * @param string $cache
	 * @return array
	 */
	function get_folder_paths( $post_id = 0, $cached = 'cached' ) {
		if ( 'cached' == $cached && ! empty( $this->folder_paths ) ) {
			if ( $this->folder_paths['post_id'] == $post_id )
				return ( $this->folder_paths['folders'] );
		}
		$gallery_path = get_metadata( 'post', $post_id, 'gallery_path', true );
		$root = ( empty( $gallery_path ) ) ? '' : eazyest_gallery()->root() . $gallery_path;
				
		unset( $this->folder_paths );
		$this->folder_paths = array( 'post_id' => $post_id, 'folders' => $this->_get_folder_paths( $root ) );
		return $this->folder_paths['folders'];	
	}
	
	/**
	 * Eazyest_FolderBase::get_folder_by_path()
	 *
	 * @since 0.1.0 (r2)
	 * @uses wpdb::get_col()
	 * @uses wpdb::prepare() 
	 * @param mixed $folder_path
	 * @return
	 */
	function get_folder_by_path( $folder_path ) {		
		$folder = array();
		global $wpdb;
		$folder = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key=%s AND meta_value=%s", 'gallery_path', $folder_path ) );
		return count( $folder ) ? $folder[0] : 0;
	}	
	
	/**
	 * Eazyest_FolderBase::get_folder_by_string()
	 * Returns ID for galleryfolder identified by directory name
	 * Also searches to find sanitized directory names
	 * 
	 * @example <code>$this->get_folder_by_string( 'Foldername/Old Name/' );</code>
	 * @since 0.1.0 (r2)
	 * @uses get_page_by_path()
	 * @param string $folder_string
	 * @return int post ID, 0 if not found
	 */
	function get_folder_by_string( $folder_string = '' ) {		
		if ( ! empty( $folder_string ) ) {			
			$post = get_page_by_path( $folder_string, OBJECT, eazyest_gallery()->post_type );
			if ( ! empty( $post ) )
				return $post->ID;
			else if ( $id = $this->get_folder_by_path( untrailingslashit( $folder_string ) ) )
				return $id;
			else {
				global $wpdb;
				$results = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s", basename( $folder_string ), eazyest_gallery()->post_type ), ARRAY_A );
				if ( count( $results ) )
					return $results[0]['ID'];				
			}	
		}	
		return 0;	
	}
	
	/**
	 * Eazyest_FolderBase::get_folder_children()
	 * Returns an array of post IDs for all generations children of a folder
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wpdb
	 * @param int $folder_id
	 * @return array
	 */
	function get_folder_children( $folder_id ) {
		global $wpdb;
		$folders = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_parent FROM $wpdb->posts WHERE post_type = %s AND post_status IN ('inherit', 'publish')", eazyest_gallery()->post_type ), ARRAY_A );
		$children = array();
		foreach( $folders as $folder ) {
			if ( $folder['post_parent'] == $folder_id ) {
				$children[] = $folder['ID'];
				if ( $grandchildren = $this->get_folder_children( $folder['ID'] ) )
					$children = array_merge( $children, $grandchildren );
			}
		}		
		return $children;	
	}
	
	/**
	 * Eazyest_FolderBase::get_parent()
	 * Get the post id based on a folder path 
	 * 
	 * @since 0.1.0 (r2)
	 * @param string $folder_path
	 * @return int post_id for parent folder
	 */
	function get_parent( $folder_path ) {
		$parent_path = substr( $folder_path, 0, -strlen( basename( $folder_path ) ) -1 );
		return $this->get_folder_by_path( $parent_path );
	}
	
	/**
	 * Eazyest_FolderBase::sanitize_dirname()
	 * Sanitize a folder directory name
	 * 
	 * @since 0.1.0 (r2)
	 * @param string $folder_path
	 * @return string santized folder path
	 */
	function sanitize_dirname( $folder_path ) {
		$folder_path = rtrim( $folder_path, '/' );
		$parts = explode( '/', $folder_path );
		// sanitize full path
		foreach( $parts as $key => $part )
			$parts[$key] = sanitize_title( $part );
		$new_path = implode( '/', $parts );
		return $new_path;
	}
	
	/**
	 * Eazyest_FolderBase::sanitize_folder()
	 * Sanitize a folder directory name in file system to prevent illegible image urls
	 * 
	 * @since 0.1.0 (r2)
	 * @uses sanitize_title
	 * @param string $folder_path raw path ( utf8_encoded )
	 * @return string sanitized path
	 */
	function sanitize_folder( $folder_path = '' ) {
		if ( '' != $folder_path ) {
			$root = str_replace( '/', DIRECTORY_SEPARATOR, eazyest_gallery()->root() );
			$folder_path = rtrim( $folder_path, '/' );
			$parts = explode( '/', $folder_path );
			// sanitize full path
			foreach( $parts as $key => $part )
				$parts[$key] = sanitize_title( $part );				
			$new_path = implode( DIRECTORY_SEPARATOR, $parts ) . DIRECTORY_SEPARATOR;
			// set only basename to raw path because parent folders have already been converted	
			$parts[count( $parts )-1] = utf8_decode( basename( $folder_path ) );
			$old_path = implode( DIRECTORY_SEPARATOR, $parts ) . DIRECTORY_SEPARATOR;
			$old_dir = $root . $old_path;						
			$new_dir = $root . $new_path;
			if ( rename( $old_dir, $new_dir ) )
				return str_replace( DIRECTORY_SEPARATOR, '/', $new_path );
			else {				
				return new WP_Error( 'error', sprintf( __( 'Could not rename folder from %1$s to %2$s', 'eazyest-gallery' ), $old_path, $new_path ) );
			}
		}		
		return $folder_path;
	} 
	
	/**
	 * Eazyest_FolderBase::name_is_posted()
	 * Check if post_name already exists in wpdb
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wpdb->get_col()
	 * @param string $post_name
	 * @return bool true if $post_name is found
	 */
	private function name_is_posted( $post_name ) {		
		global $wpdb;
		$result = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_name = '$post_name'" );
		return ( ! empty( $result ) );
	}
	
	/**
	 * Eazyest_FolderBase::replace_dashes()
	 * Should dashes and underscores be replaced by spaces
	 * 
	 * @since 0.1.0 (r2)
	 * @return bool
	 */
	function replace_dashes() {
		return apply_filters( 'eazyest_gallery_replace_dashes', true );
	}
	
	/**
	 * Eazyest_FolderBase::insert_folder()
	 * Used when a new folder is found in the gallery file system, but no galleryfolder is connected
	 * Insert a new galleryfolder post in the WordPress database
	 * Folders will be saved with status 'publish' because other new folders could be child folders
	 * 
	 * @since 0.1.0 (r2)
	 * @uses sanitize_title()
	 * @uses is_wp_error()
	 * @uses get_error_message()
	 * @uses apply_filters()
	 * @uses wp_insert_post()
	 * @uses update_post_meta()
	 * @param string $folder_path
	 * @param integer $post_id
	 * @return int
	 */
	function insert_folder( $folder_path, $post_id = 0 ) {
		$post_parent = $post_id;
		
		$title = basename( $folder_path );
		if ( sanitize_title( $title ) != $title ) {
			// rename folder in file system to prevent awkward image urls
			$folder_path = $this->sanitize_folder( $folder_path );
		}	
		if ( is_wp_error( $folder_path ) ) {	
			return 0;
		}
		
		if ( ! $post_parent ) {
			if ( strlen( basename( $folder_path ) ) < strlen( $folder_path )  ) {
				$post_parent = $this->get_parent( $folder_path );
			}
		}	
		$add = 2;		
		$post_name = sanitize_title( $title );
		while ( $this->name_is_posted( $post_name ) ) {
			$post_name .= '-' . $add++;
		}	
		if ( $this->replace_dashes() )
			$title = str_replace( array( '-', '_' ), ' ', $title );
		$folder = array(
			'post_type'   => eazyest_gallery()->post_type,
			'post_title'  => $title,
			'post_name'   => $post_name,
			'post_status' => apply_filters( 'eazyest_gallery_folder_status', 'publish' ),
			'post_parent' => $post_parent
		);
		
		$post_id = wp_insert_post( $folder );
		if ( $post_id ) {			
			update_post_meta( $post_id, 'gallery_path', untrailingslashit( $folder_path ) );
			do_action( 'eazyest_gallery_insert_folder', $post_id );
		}
		return $post_id;	
	}
	 
	/**
	 * Eazyest_FolderBase::add_folders()
	 * Add folders to wpdb when user has (ftp) uploaded new folders
	 * 
	 * @since 0.1.0 (r2)
	 * @param integer $post_id
	 * @return integer number of folders added
	 */
	function add_folders( $post_id ) {
		$this->get_posted_paths( $post_id );
		$this->get_folder_paths( $post_id );	
		$added = 0;
		if ( ! empty( $this->folder_paths['folders'] ) ) {
			foreach( $this->folder_paths['folders'] as $folder_name ) {
				$child_name = $this->sanitize_dirname( $folder_name );
				if ( ! in_array( $child_name, $this->posted_paths['folders'] ) ) {
					$this->insert_folder( $folder_name, $post_id );
					$this->posted_paths['folders'][] = $child_name;
					$added++;
				}
			}
		}
		return $added;
	}
	
	/**
	 * Eazyest_FolderBase::delete_folders()
	 * Delete folders from wpdb when user has (ftp)  delete folders
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wp_delete_post
	 * @param integer $post_id
	 * @return integer number of folders deleted
	 */
	function delete_folders( $post_id ) {
		$this->get_posted_paths( $post_id );
		$this->get_folder_paths( $post_id );
		$deleted = 0;
		if ( ! empty( $this->posted_paths['folders'] ) ) {
			foreach( $this->posted_paths['folders'] as $key => $path_name ) {
				if ( ! in_array( $path_name, $this->folder_paths['folders'] ) ) {
					$folder_id = $this->get_folder_by_path( $path_name );
					wp_delete_post( $post_id, true );
					$deleted--;
				}
			}
		}
		return $deleted;
	}
	
	/**
	 * Eazyest_FolderBase::get_new_folders()
	 * Check if folders have been added or deleted
	 * 
	 * @since 0.1.0 (r2)
	 * @param integer $post_id
	 * @return integer number of folders added + or deleted -
	 */
	function get_new_folders( $post_id ) {
		$posted_paths = $this->get_posted_paths( $post_id, 'new' );
		$folder_paths = $this->get_folder_paths( $post_id, 'new' );
		return ( count( $folder_paths ) - count( $posted_paths ) );		
	}
	
	/**
	 * Eazyest_FolderBase::folders_collected()
	 * 
	 * @since 0.1.0 (r2)
	 * @return integer
	 */
	public function folders_collected() {
		return $this->folders_collected;	
	}
	
	/**
	 * Eazyest_FolderBase::collect_folders()
	 * Check all folders in file system if they exist as posted galleryfolder
	 * 
	 * @since 0.1.0 (r2)
	 * @return integer
	 */
	public function collect_folders( $post_id = 0 ) {
		// this loads the image name caches
		$new_folders = $this->get_new_folders( $post_id ); 
		// check if something has changed in the folder
		if ( 0 == $new_folders )
			return;
		$function = $new_folders > 0 ? 'add_folders' : 'delete_folders';
		
		$this->folders_collected = $this->$function( $post_id );
		return $this->folders_collected ;
	}
	
	/**
	 * Eazyest_FolderBase::save_gallery_order()
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wp_update_post()
	 * @param mixed $gallery_order
	 * @return integer number of posts changed
	 */
	public function save_gallery_order( $gallery_order = array() ) {
		$updated    = 0;
		if ( ! empty( $gallery_order ) ) {
			foreach( $gallery_order as $menu_order => $post_id ) {
				$post = get_post( $post_id );
					if ( $post->menu_order != $menu_order ) {						
						$post->menu_order = $menu_order;						
						wp_update_post( $post );
						$updated++;	
					} 	
			}
		}
		return $updated;
	}
	
	/**
	 * Eazyest_FolderBase::_sort_menu_order()
	 * usort array of posts by menu_order
	 * 
	 * @since 0.1.0 (r2)
	 * @param array $a
	 * @param array $b
	 * @return integer
	 */
	private function _sort_menu_order( $a, $b ) {
		return intval( $a['menu_order'] ) - intval( $b['menu_order'] );
	}
	
	/**
	 * Eazyest_FolderBase::move_folder()
	 * Move a folder to top or to bottom of the list
	 * This function should hardly ever be called.
	 * Manually sorting should be handeled by AJAX
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wp_update_post()
	 * @param integer $post_id
	 * @param string $direction
	 * @return void
	 */
	public function move_folder( $post_id, $direction = 'to_top' ) {
		$mover       = get_post( $post_id );
		$post_parent = $mover->post_parent; 
		$post_type   = eazyest_gallery()->post_type;
		
		global $wpdb;
		$updated = 0;
		$folder_ids = $wpdb->get_results( "SELECT ID,menu_order FROM $wpdb->posts WHERE post_type='$post_type' AND post_parent=$post_parent ORDER BY menu_order ASC", ARRAY_A );
		$key = array_search( array( 'ID' => $mover->ID, 'menu_order' => $mover->menu_order ), $folder_ids );
		if ( false !== $key ) {
			$folder_ids[$key]['menu_order'] = 'to_top' == $direction ? -1 : count( $folder_ids );
			usort( $folder_ids, array( $this, '_sort_menu_order' ) );
			$menu_order = 0;	
			foreach( $folder_ids as $item ) {
				if ( $item['menu_order'] != $menu_order ) {
					$folder = array(
						'ID' => $item['ID'],
						'menu_order' => $menu_order
					);
					wp_update_post( $folder );
					$updated++;
				}
				$menu_order++;
			}
		}
		return $updated;
	}
	
	// Functions related to images/attachments -----------------------------------
	
	/**
	 * Eazyest_FolderBase::get_attached_file()
	 * Filter for get_attached_file()
	 * Returns filename in gallery
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post()
	 * @uses get_post_meta()
	 * @uses get_post_type()
	 * @param string $file
	 * @param int $post_id
	 * @return string file
	 */
	public function get_attached_file( $file, $post_id ) {
		$attachment = get_post( $post_id );
		$parent_id = $attachment->post_parent;
		
		if ( $parent_id ) {			
			if ( $this->is_gallery_image( $post_id ) ) {				
				$gallery_path = trailingslashit( get_metadata( 'post', $parent_id, 'gallery_path', true ) );
				if ( basename( $file ) != basename( $attachment->guid ) )
					$gallery_path .= '_temp/';
				$path = $gallery_path . basename( $file );								
				if ( false === strpos( $file, $path )  ) {
					update_post_meta( $post_id, '_wp_attached_file', $path );
				}
				$file = eazyest_gallery()->root() . $path; 
			}				
		}	
		return $file;
	}
		
	/**
	 * Eazyest_FolderBase::get_attachment_url()
	 * Filter for get_attachment_url()
	 * Returns url for image in gallery
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post_type()
	 * @param string $url
	 * @param int $post_id
	 * @return string
	 */
	public function get_attachment_url( $url, $post_id ) {
		if ( $this->is_gallery_image( $post_id ) ) {
			$filename = basename( $url );
			$guid = get_post( $post_id )->guid;
			$original = basename( $guid );	
 			if ( $filename !=  $original )
 				$url = substr( $guid, 0, -strlen( $original ) ) . '_temp/' . $filename;
			else 
				$url = $guid;	  
 		}
		return $url;	
	}
	
	/**
	 * Eazyest_FolderBase::attachment_view_url()
	 * Return the url to view the attachment
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_permalink()
	 * @param int $post_id
	 * @return string
	 */
	function attachment_view_url( $post_id ) {
		return get_permalink( $post_id );
	}
	
	/**
	 * Eazyest_FolderBase::get_folder_images()
	 * Get all filenames of images currently in the folder
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_metadata
	 * @uses trailingsalshit
	 * @param int $post_id
	 * @return array
	 */
	private function get_folder_images( $post_id, $cached = 'cached' ) {
		
		if ( 'cached' == $cached && ! empty( $this->folder_images ) ) {
			if ( $this->folder_images['post_id'] == $post_id )
				return ( $this->folder_images['images'] );
		}
		$gallery_path = get_metadata( 'post', $post_id, 'gallery_path', true );
		if ( empty( $gallery_path ) )
			return array();
		
		unset( $this->folder_images );
		$this->folder_images = array( 'post_id' => $post_id, 'images' => array() );		
			
		$folder_path = eazyest_gallery()->root() . $gallery_path;
		if ( $dir_content = @opendir( $folder_path ) ) {  
			while ( false !== ( $dir_file = readdir( $dir_content ) ) ) {
        if ( ! is_dir( $dir_file ) && ( 0 < preg_match( "/^.*\.(jpg|gif|png|jpeg)$/i", $dir_file ) ) ) {
          $this->folder_images['images'][] = utf8_encode( basename( $dir_file ) );
        }        			 
			}
      @closedir( $dir_content );
		} else {	  
	    return false;
		}  
    return $this->folder_images['images'];    
	}
	
	/**
	 * Eazyest_FolderBase::sanitize_filename()
	 * sanitize a filename for a new found image, but check if an image with sanitized name already exists
	 * 
	 * @since 0.1.0 (r2)
	 * @uses sanitize_file_name()
	 * @uses get_metadata()
	 * @uses trailingslashit 
	 * @param string $filename
	 * @param integer $post_id
	 * @return string the sanitized filename
	 */
	function sanitize_filename( $filename, $post_id = 0 ) {
		$sanitized = sanitize_file_name( $filename ) ;
		if ( $post_id ) {
			$gallery_path = get_metadata( 'post', $post_id, 'gallery_path', true );
			$folder_path = eazyest_gallery()->root() . $gallery_path;
			if ( $sanitized != $filename ) {
				// filename changed after sanitizing
				$pathinfo = pathinfo( $sanitized );				
				$i = 0;
				while ( file_exists( $folder_path . '/' . $sanitized ) ) {
					// renamed file exists
					$sanitized = $pathinfo['filename'] . '-' . ++$i . $pathinfo['extension'];
				}
				@rename( $folder_path . '/' . $filename, $folder_path . '/' . $sanitized );
			}
		}
		return $sanitized;
	}
	
	/**
	 * Eazyest_FolderBase::get_posted_images()
	 * Get all image attachments for a particular galleryfolder
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_children()
	 * @param int $post_id
	 * @return array
	 */
	private function get_posted_images( $post_id, $cached = 'cached' ) {
		
		if ( 'cached' == $cached && ! empty( $this->posted_images ) ) {
			if ( $this->posted_images['post_id'] == $post_id )
				return ( $this->posted_images['images'] );
		}
		
		unset( $this->posted_images );
		$this->posted_images = array( 'post_id' => $post_id, 'images' => array() );
		$attachments = get_children( array(
			'post_parent'    => $post_id,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image'
		) );
		if ( ! empty( $attachments ) ) {
			foreach( $attachments as $attachment ) {
				$this->posted_images['images'][] = basename( $attachment->guid );
			}
		}
		return $this->posted_images['images'];
	}
	
	/**
	 * Eazyest_FolderBase::insert_image()
	 * Insert a new image found in a folder into the WP database
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_metadata()
	 * @uses wp_check_filetype()
	 * @uses wp_upload_dir()
	 * @uses trailingslashit()
	 * @uses wp_insert_attachment() to store attachemnt in database
	 * @uses wp_read_image_metadata() to get exif and iptc data
	 * @uses wp_update_attachment_metadata() to store exif and iptc data
	 * @param int $post_id
	 * @param string $filename
	 * @param string $title
	 * @return void
	 */
	function insert_image( $post_id, $filename, $title ) {
		$gallery_path = get_metadata( 'post', $post_id, 'gallery_path', true );
		$wp_filetype = wp_check_filetype( basename( $filename ), null );
  	$wp_upload_dir = wp_upload_dir(); 
		$title = preg_replace( '/\.[^.]+$/', '', basename( $title ) );
		if ( $this->replace_dashes() )
			$title = str_replace( array( '-', '_' ), ' ', $title );
  	$attachment = array(
     'guid' => eazyest_gallery()->address()  . $gallery_path . '/' . basename( $filename ),
     'post_mime_type' => $wp_filetype['type'],
     'post_title' => $title,
     'post_excerpt' => $title,
     'post_content' => '',
     'post_status' => 'inherit'
  	); 	
  	$attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
  	if ( !is_wp_error( $attach_id ) ) {
			wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $filename ) );
			return $attach_id;
		} else { 
			return false;
		}
	}
	
	/**
	 * Eazyest_FolderBase::new_images()
	 * Count the number of (ftp) uploaded images in this folder
	 *  
	 * @since 0.1.0 (r2)
	 * @param mixed $post_id
	 * @return integer positive: new images have been added, negative: images have been deleted outside WordPress
	 */
	function get_new_images( $post_id ) {		
		$posted_images = $this->get_posted_images( $post_id, 'new' );
		$folder_images = $this->get_folder_images( $post_id, 'new' );
		return ( count( $folder_images ) - count( $posted_images) );
	}
	
	/**
	 * Eazyest_FolderBase::add_images()
	 * Add new images as attachments
	 * 
	 * Add new names to post_images names because sanitized filenames could be equal
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_metadata()
	 * @uses trailingslashit()
	 * @param integer $post_id
	 * @return void
	 */
	function add_images( $post_id ) {		
		$gallery_path = get_metadata( 'post', $post_id, 'gallery_path', true );
		$this->get_posted_images( $post_id );
		$this->get_folder_images( $post_id );
		$added = 0;
		if ( ! empty( $this->folder_images['images'] ) ) {
			foreach( $this->folder_images['images'] as $image_name ) {				
				$attach_name = $this->sanitize_filename( $image_name, $post_id );		
				$attach_file = eazyest_gallery()->root() . $gallery_path . '/' . $attach_name;
				if ( ! in_array( $attach_name, $this->posted_images['images'] ) ) {
					$this->insert_image( $post_id, $attach_file, $image_name  );
					$this->posted_images['images'][] = $attach_name;
					$added++;
				}
			}				
		}
		return $added;
	}
	
	/**
	 * Eazyest_FolderBase::delete_attachment_by_filename()
	 * Delete an attachment by filename path/image.ext
	 * 
	 * @since 0.1.0 (r2)
	 * @uses $wpdb
	 * @uses wp_delete_attachment()
	 * @param string $image_name
	 * @return void
	 */
	function delete_attachment_by_filename( $image_name ) {
		global $wpdb;
		$guid = eazyest_gallery()->address . $image_name;
		$ids = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid ), ARRAY_A );
		if ( empty( $ids ) )
			return false;
		$attachment_id = $ids[0]['ID'];
		return wp_delete_attachment( $attachment_id, true );	
	}
	
	/**
	 * Eazyest_FolderBase::delete_attachments()
	 * Delete attachments from WordPress database when original image has been (ftp) erased 
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post_meta()
	 * @param integer $post_id
	 * @return void
	 */
	function delete_attachments( $post_id ) {		
		$this->get_posted_images( $post_id );
		$this->get_folder_images( $post_id );
		$deleted = 0;		
		if ( ! empty( $this->posted_images['images'] ) ) {			
			$gallery_path = get_post_meta( $post_id, 'gallery_path', true );
			foreach( $this->posted_images['images'] as $key => $image_name  ) {
				if ( ! in_array( $image_name, $this->folder_images['images'] ) ) {
					if ( $this->delete_attachment_by_filename( $gallery_path . '/' . $image_name ) ) {
						unset( $this->posted_images['images'][$key] );
						$deleted--;
					}
				}
			}
		}
		return $deleted;
	}
	
	/**
	 * Eazyest_FolderBase::images_collected()
	 * Number of new (+) or deleted (-) images found the last time collect_images() run
	 * 
	 * @since 0.1.0 (r2) 
	 * @return integer
	 */
	public function images_collected() {
		return $this->images_collected;	
	}
	
	/**
	 * Eazyest_FolderBase::collect_images()
	 * Get all images for a particular galleryfolder
	 * Check for new or deleted (ftp) uploaded images
	 * 
	 * @since 0.1.0 (r2)
	 * @param int $post_id
	 * @return array
	 */
	public function collect_images( $post_id ) {
		// this loads the image name caches
		$new_images = $this->get_new_images( $post_id ); 
		// check if something has changed in the folder
		if ( 0 == $new_images )
			return;
		$function = $new_images > 0 ? 'add_images' : 'delete_attachments';
		
		$this->images_collected = $this->$function( $post_id );
		return $this->images_collected ;
	}
	
	// Image resizing functions --------------------------------------------------
	
	/**
	 * Eazyest_FolderBase::image_downsize()
	 * This function filters the image_downsize($id, $size = 'medium')
	 * 
	 * @since 0.1.0 (r2)
	 * @see wordpress/wp-includes/media.php
	 * @uses get_post()
	 * @uses get_post_meta()
	 * @uses get_post_type()
	 * @param array $resize
	 * @param int $post_id
	 * @param string $size
	 * @return array
	 */
	public function image_downsize( $resize, $post_id, $size ) {
		if ( $this->is_gallery_image( $post_id ) ) {
			$resize = $this->resize_image( $post_id, $size );
		}		
		return $resize;
	}
	
	public function image_editors( $editor_classes ) {
		require_once( eazyest_gallery()->plugin_dir . 'includes/class-eazyest-image-editor.php' );
		array_unshift( $editor_classes, 'Eazyest_Image_Editor' );
		return $editor_classes;
	}
	
	/**
	 * Eazyest_FolderBase::size_dir()
	 * Generate the name for the subdirectory to cache resized images 
	 * 
	 * @since 0.1.0 (r2)
	 * @param string $size
	 * @return string
	 */
	public function size_dir( $size ) {
		return  'full' == $size ? '' : '_' . $size;
	}
	
	/**
	 * Eazyest_FolderBase::resize_filename()
	 * Generate the full path filename for a resized image
	 * 
	 * @since 0.1.0 (r2)
	 * @uses trailingslashit
	 * @param string $filename
	 * @param string $size
	 * @return string
	 */
	private function resize_filename( $filename, $size ) {
		$size_dir = $this->size_dir( $size );
		$size_dir = '' == $size_dir ? $size_dir : $size_dir . '/';	
		return trailingslashit( dirname( $filename ) ) . $size_dir . basename( $filename ); 
	}
	
	/**
	 * Eazyest_FolderBase::create_size()
	 * Create a new thumbnail, medium or large image in the gallery
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wp_mkdir_p()
	 * @uses get_option()
	 * @uses wp_get_image_editor()
	 * @uses is_wp_error()
	 * @param string $gallery_path
	 * @param string $filename
	 * @param int $size
	 * @param string $mime_type
	 * @return array
	 */
	private function create_size( $filename, $size, $mime_type ) {
		
		if ( $size == 'thumb' ) $size = 'thumbnail';
		// default sizes
		$width = 128;
		$height = 96;
		$crop = $size == 'thumbnail' && get_option( 'thumbnail_crop' );
		$gallery_path = dirname( $filename );
		$basename = 		basename( $filename );
		$size_dir = $this->size_dir( $size );		
		$original_dir = eazyest_gallery()->root() . $gallery_path;
		$original 		= trailingslashit( eazyest_gallery()->root() ) . $filename;
		$resize_dir   = trailingslashit( $original_dir ) . $size_dir;
		$resized      = trailingslashit( $resize_dir   ) . $basename;
		global $_wp_additional_image_sizes;		
		if ( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
			$width  = intval( get_option( "{$size}_size_w" ) );
			$height = intval( get_option( "{$size}_size_h" ) ); 
			 
		} elseif ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
			$width  = intval( $_wp_additional_image_sizes[$size]['width'] );
			$height = intval( $_wp_additional_image_sizes[$size]['height'] );			
		}
		$original = str_replace( '\\', '/', $original );			
		$editor = wp_get_image_editor( $original );
		if ( ! is_wp_error( $editor ) ) {			
			$resize = $editor->resize( $width, $height, $crop );
			if ( is_wp_error( $resize ) )
				return $resize;
			return $editor->save( $resized, $mime_type );		
		}	else {
			return $editor;
		}				
	}
	
	/**
	 * Eazyest_FolderBase::resize_image()
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post()
	 * @uses get_post_meta()
	 * @uses is_wp_error() for result of create_size
	 * @uses trailingslashit()
	 * @uses get_option() to get resized dimensions
	 * @uses wp_get_attachment_metadata()
	 * @param int $post_id attachment->ID
	 * @param string $size
	 * @return mixed array on success, bool false on failure
	 */
	public function resize_image( $post_id, $size ) {
		$attachment   = get_post( $post_id );
		$mime_type    = $attachment->post_mime_type;
		$filename    = get_post_meta( $post_id, '_wp_attached_file', true );
		if ( false !== strpos( $filename, eazyest_gallery()->root() ) )
			$filename = substr( $filename, strlen( eazyest_gallery()->root() ) );
		$gallery_path = dirname( $filename );
		if ( basename( $filename ) != basename( get_post( $post_id )->guid ) ) {
			$gallery_path .=  '/_temp';
			$filename = trailingslashit( $gallery_path ) . basename( $filename );
		}		
		$restrict_sizes = array( 'thumbnail', 'medium', 'large' );
		if ( ! is_array( $size ) && ! in_array( $size, $restrict_sizes ) ) {
			global $_wp_additional_image_sizes;
			if ( isset( $_wp_additional_image_sizes[$size] ) ) {
				$size = array( $_wp_additional_image_sizes[$size]['width'],  $_wp_additional_image_sizes[$size]['height'] );
			} else {
				$size = 'full';
			}
		} 		
		if ( is_array( $size ) ) {
			$width  = $size[0];
			$height = $size[1];
			$size   = 'full';	
			foreach( $restrict_sizes as $dim ) {
				if ( $width <= intval( get_option( "{$dim}_size_w" ) ) && $height <= intval( get_option( "{$dim}_size_h" ) ) ){
					$size = $dim;
					break;
				}
			}		
		}			
		// check and create cache directory for this size
		$resize_dir = eazyest_gallery()->root() . trailingslashit( $gallery_path ) . $this->size_dir( $size );
		
		if ( !file_exists( $resize_dir ) )
			wp_mkdir_p( $resize_dir );			
		
		$resize_name = $this->resize_filename( $filename, $size );
		$img_file = eazyest_gallery()->root() . $resize_name;		
		$img_url  = eazyest_gallery()->address() . $resize_name;	
		
		if ( ! file_exists( $img_file ) ) {
			$result = $this->create_size( $filename, $size, $mime_type );
			if ( ! is_wp_error( $result ) ) {
				return( array( $img_url, $result['width'], $result['height'], false ) );
			} else {
				$img_file = eazyest_gallery()->root()  . trailingslashit( $gallery_path ) . $filename;
				$img_url  = eazyest_gallery()->address() . trailingslashit( $gallery_path ) . $filename;
				return false;	
			}
		}
		if ( file_exists( $img_file ) ) {
			$metadata = wp_get_attachment_metadata( $post_id );
			if ( isset( $metadata['sizes'][$size] ) ) {
				$width  = $metadata['sizes'][$size]['width'];
				$height = $metadata['sizes'][$size]['height'];
			} else {
				list( $width, $height ) = getimagesize( $img_file );
			}				
			return array( $img_url, $width, $height, true );
		}				
		return false;
	}
	
	/**
	 * Eazyest_FolderBase::sizes_metadata()
	 * Update metadata for image sizes.
	 * Delete any resized files made by wordpress (filename150x150.jpg) because Eazyest Gallery stores resized files in subdirectories
	 * 
	 * @since 0.1.0 (r36)
	 * @uses get_post() to retrieve attachment
	 * @uses get_post_meta() to get gallery_path
	 * @uses trailingslashit to build resized filename
	 * @param array $metadata
	 * @param int $attachment_id
	 * @return array update metadata
	 */
	function sizes_metadata( $metadata, $attachment_id ) {
    $file = $metadata['file'];
		
		// Make the file path relative to the eazyest gallery dir
		if ( false !== strpos( $file, eazyest_gallery()->root() ) )
			$file = substr( $file, strlen( eazyest_gallery()->root() ) );
		$gallery_path = trailingslashit( dirname( $file ) );		    
		$pathinfo = pathinfo( $file );
		
		// Check if we are dealing with an edited image saved in the _temp directory
		$temp_file = basename( $file ) != basename( get_post( $attachment_id )->guid );
		if ( $temp_file && ( false === strpos( $file, '_temp' ) ) ) {
    	$gallery_path .= '_temp/';
 		}
  	$metadata['file'] = $gallery_path . basename( $file );
  	
		if ( isset( $metadata['sizes'] ) ) {
			foreach( $metadata['sizes'] as $key => $size ) {
				
				$size_dir      = $this->size_dir( $key );				 
				$good_dir      = eazyest_gallery()->root() . trailingslashit( $gallery_path . $size_dir ) ;
				$good_resized  = $good_dir . $pathinfo['basename'];
				
				if ( isset( $metadata['sizes'][$key]['width'] ) && isset( $metadata['sizes'][$key]['height'] ) ) {
					$wrong_resized = eazyest_gallery()->root() . $gallery_path . $pathinfo['filename'] . '-' . 	$metadata['sizes'][$key]['width'] . 'x' . $metadata['sizes'][$key]['height'] . $pathinfo['extension'];
					
					if ( file_exists( $wrong_resized ) ) {
						if ( file_exists( $good_resized ) ) {
							unset( $wrong_resized );							
						} else {
							if ( ! file_exists( $good_dir ) )
								wp_mk_dir_p( $good_dir );
							rename( $wrong_resized, $good_resized );	
						}	
					}
				}
				if ( file_exists( $good_resized ) )							
					$metadata['sizes'][$key]['file'] = $size_dir . '/' . $pathinfo['basename'];
				else 
					unset( $metadata['sizes'][$key] );	
			}
			if ( isset( $metadata['sizes']['thumbnail'] ) )
				$metadata['sizes']['post-thumbnail'] = $metadata['sizes']['thumbnail'];
		}		 
		return $metadata;			
	}
	
	/**
	 * Eazyest_FolderBase::backup_metadata()
	 * Update backup metadata for resized images in _temp folder.
	 * 
	 * @since 0.1.0 (r36)
	 * @uses get_post() to get attachment
	 * @param array $metadata
	 * @param int $attachment_id
	 * @return array updated metadata
	 */
	function backup_metadata( $metadata, $attachment_id ) {
		if ( ! $this->is_gallery_image( $attachment_id ) )
			return $metadata;				
		$file = basename( get_post( $attachment_id )->guid );	
		foreach( $metadata as $key => $backup ) {
			if ( $file != basename( $backup['file'] ) && ( false === strpos( '_temp', $backup['file'] ) ) ) {
				$metadata[$key]['file'] =  '_temp/' . $backup['file'];
			}
		}			
		return( $metadata );
	}
	
	/**
	 * Eazyest_FolderBase::file_metadata()
	 * Update attachment file metadata for resized images in _temp folder.
	 * 
	 * @since 0.1.0 (r36)
	 * @uses get_post() to get attachment
	 * @param array $metadata
	 * @param int $attachment_id
	 * @return array updated metadata
	 */
	function file_metadata( $metadata, $attachment_id ) {
		if ( ! $this->is_gallery_image( $attachment_id ) )
			return $metadata;			
		$file = basename( get_post( $attachment_id )->guid );
		if ( false !== strpos( $metadata, eazyest_gallery()->address() ) )
			$metadata = substr( $metadata, strlen( eazyest_gallery()->address() ) );
		if ( $file != basename( $metadata ) && ( false === strpos( $metadata, '_temp' ) ) ) {
			$metadata = dirname( $metadata) . '/_temp/' . basename( $metadata );
		}			
		return $metadata;		
	}
	
	/**
	 * Eazyest_FolderBase::update_attachment_metadata()
	 * Update metadata for gallery images.
	 * Filters WordPress 'update_attachment_metadata'
	 * 
	 * @since 0.1.0 (r36)
	 * @uses wpdb;
	 * @uses  get_metadata() to check if value has changed
	 * @global $wpdb
	 * @param mixed $result
	 * @param int $object_id
	 * @param string $meta_key meta key value for wpdb->postmeta 
	 * @param mixed $meta_value
	 * @param mixed $prev_value
	 * @return mixed null|mixed if nothing changed return null
	 */
	function  update_attachment_metadata( $result, $object_id, $meta_key, $meta_value, $prev_value ) {
		// only filter attachment metadata we need
		if ( ! in_array( $meta_key, array( '_wp_attachment_metadata', '_wp_attachment_backup_sizes', '_wp_attached_file' ) ) )
			return $result;
		
		// if nothing has changed, return
		if ( empty($prev_value) ) {
			$old_value = get_metadata( 'post', $object_id, $meta_key );
			if ( count( $old_value) == 1 ) {	
				if ( $old_value[0] === $meta_value )
					return false;
			}
		}	
		// only filter metadata for gallery images	
		if ( ! $this->is_gallery_image( $object_id ) )
			return $result;
		if ( $meta_key == '_wp_attachment_metadata' )	
			$meta_value = $this->sizes_metadata( $meta_value, $object_id );
		if ( $meta_key == '_wp_attachment_backup_sizes' )
			$meta_value = $this->backup_metadata( $meta_value, $object_id );
		if ( $meta_key == '_wp_attached_file' )
			$meta_value = $this->file_metadata( $meta_value, $object_id );
		
		// add or change metavalue;
		global $wpdb;	
		if ( ! $meta_id = $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d", $meta_key, $object_id ) ) )
			return add_metadata('post', $object_id, $meta_key, $meta_value);
		
		// process the metadata
		$_meta_value = $meta_value;
		$meta_value = maybe_serialize( $meta_value );
	
		$data  = compact( 'meta_value' );
		$where = array( 'post_id' => $object_id, 'meta_key' => $meta_key );
	
		if ( !empty( $prev_value ) ) {
			$prev_value = maybe_serialize($prev_value);
			$where['meta_value'] = $prev_value;
		}
		// now update the metadata
		$wpdb->update( $wpdb->postmeta, $data, $where );
		return true;		
	} 
	
	/**
	 * Eazyest_FolderBase::save_image_editor_file()
	 * Save edited files in a seprate folder to prevent them from being indexed as new.
	 * 
	 * @since 0.1.0 (r36)
	 * @uses get_post() to get folder->ID
	 * @uses get_post_meta() to get gallery_path
	 * @uses wp_mkdir_p() to create temporaray folder
	 * @uses trailingslashit to build filename
	 * @param mixed $result
	 * @param string $filename
	 * @param object $image WP_Image_Editor
	 * @param string $mime_type
	 * @param int $post_id
	 * @return mixed WP_Image_Editor::save()
	 */
	function save_image_editor_file( $result, $filename, $image, $mime_type, $post_id ) {
		if ( ! $this->is_gallery_image( $post_id ) )
			return $result;	
		if ( basename( $filename ) != basename( get_post( $post_id )->guid ) ) {
			// probably a temporary name
			$gallery_path = get_post_meta( get_post( $post_id )->post_parent, 'gallery_path', 'true' );
			$dirname = eazyest_gallery()->root() . $gallery_path . '/_temp';
			if ( ! file_exists( $dirname ) )
				wp_mkdir_p( $dirname );
			$filename = trailingslashit( $dirname ) . basename( $filename );
			$result = $image->save( $filename, $mime_type );
		}			
		return $result;	
	}
	
	// image select functions ----------------------------------------------------
	/**
	 * Eazyest_FolderBase::is_gallery_image()
	 * Test if attachment resides in eazyest gallery.
	 * 
	 * @since 0.1.0 (r12)
	 * @uses absint() to check attachment_id
	 * @uses get_post()
	 * @param int $attachment_id
	 * @return bool
	 */
	function is_gallery_image( $attachment_id ) {
		if ( ! $attachment_id  = absint( $attachment_id ) )
			return false;
			
		$attachment = get_post( $attachment_id );
		
		if ( empty( $attachment ) )
			return false;
		
		$gallery_filename = substr( $attachment->guid, strlen( eazyest_gallery()->address ) );
		return file_exists( eazyest_gallery()->root() . $gallery_filename );	
	}
	
	/**
	 * Eazyest_FolderBase::featured_image()
	 * Returns the ID for the featured image
	 * If no featured image is selected in edit > galleryfolder, the first image in the folder is selected.
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wpdb
	 * @param int $post_id
	 * @return int
	 */
	function featured_image( $post_id ) {
		global $wpdb;
		$results = $wpdb->get_results( "
			SELECT meta_value 
			FROM $wpdb->postmeta 
			WHERE meta_key = 'thumbnail_id' 
			AND 'post_id' = $post_id;", 
			ARRAY_A 
		);			
		if ( ! empty( $results ) )
		 return $results[0]['ID'];	
		else
			return $this->first_image( $post_id );
	}
	
	/**
	 * Eazyest_FolderBase::first_image()
	 * Returns the ID for the first image in the folder after sorting according to Eazyest Gallery folder Settings
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wpdb
	 * @param int $post_id
	 * @return int
	 */
	function first_image( $post_id ) {
		global $wpdb;
		list( $order_by, $ascdesc ) = explode( '-', eazyest_gallery()->sort_by( 'thumbnails' ) );
		$results = $wpdb->get_results( "
			SELECT ID FROM $wpdb->posts 
			WHERE post_parent = $post_id 
			AND post_type = 'attachment' 
			AND post_status IN ('inherit', 'publish')
			ORDER BY $order_by $ascdesc 
			LIMIT 1;", 
			ARRAY_A
		);		
		$id = ! empty( $results ) ? $results[0]['ID'] : 0;
		return $id;
	}
	
	/**
	 * Eazyest_FolderBase::random_images()
	 * Returns an array with a number of randomly selected image ID from a folder.
	 * 
	 * @example To select one random image from all folders do: 
	 * @example <?php $post_id = Eazyest_FolderBase->random_images( 0, 1, true ); ?> 
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wpdb
	 * @param integer $post_id the gallery folder id. if 0 and subfolders, all root folders will be included
	 * @param integer $number 1 or higher
	 * @param bool $subfolders include subfolders in selection 
	 * @return array of integers
	 */
	function random_images( $post_id = 0, $number = 1, $subfolders = false ) {
		$number = max( 1, $number );
		$post_ids = "= $post_id";
		if ( $subfolders ) {
			$children = $this->get_folder_children( $post_id );
			if ( ! empty( $children ) ) {
				if ( $post_id )
					$children[] = $post_id;
				$childlist = implode( ',', $children );
				$post_ids = "IN ($childlist)";
			} 				
		}	
		global $wpdb;
		$results = $wpdb->get_results( "
			SELECT ID FROM $wpdb->posts 
			WHERE post_parent $post_ids 
			AND post_type = 'attachment' 
			AND post_status IN ('inherit', 'publish') 
			ORDER BY RAND() 
			LIMIT $number;", 
			ARRAY_A 
		);
		if ( empty( $results ) )
			return array( 0 );
		
		$random = array();
		foreach( $results as $result ) {
			$random[] = $result['ID'];
		}
		return $random;						
	}
	
	/**
	 * Eazyest_FolderBase::recent_images()
	 * Returns an array with a number of latest included images
	 * 
	 * @example To select the latest image from all folders do: 
	 * @example <?php $post_id = Eazyest_FolderBase->recent_images( 0, 1, true ); ?> 
	 * 
	 * @since 0.1.0 (r2)
	 * @use wpdb
	 * @param integer $post_id the gallery folder id. if 0 and subfolders, all root folders will be included
	 * @param integer $number
	 * @param bool $subfolders
	 * @return array of integers
	 */
	function recent_images( $post_id = 0, $number = 1, $subfolders = false ) {
		$number = max( 1, $number );	
		$post_ids = "= $post_id";
		if ( $subfolders ) {
			$children = $this->get_folder_children( $post_id );
			if ( ! empty( $children ) ) {
				if ( $post_id )
					$children[] = $post_id;
				$childlist = implode( ',', $children );
				$post_ids = "IN ($childlist)";
			}
		}
		global $wpdb;
		$results = $wpdb->get_results( "
			SELECT ID FROM $wpdb->posts 
			WHERE post_parent $post_ids 
			AND post_type = 'attachment' 
			AND post_status IN ('inherit', 'publish') 
			ORDER BY post_date DESC 
			LIMIT $number;", 
			ARRAY_A 
		);		
		if ( empty( $results ) )
			return array( 0 );
		
		$recent = array();
		foreach( $results as $result ) {
			$recent[] = $result['ID'];
		}
		return $recent;		
	}
	
	/**
	 * Eazyest_FolderBase::children_images()
	 * Get all attachemnt ID for galleryfolder and its subfolders
	 * 
	 * @since 0.1.0 (r2) 
	 * @param integer $post_id
	 * @param integer $number of images to retrieve
	 * @param bool $subfolders
	 * @return array of int attachment ID
	 */
	function children_images( $post_id = 0, $number = 0 ) {
		global $wpdb;
		list( $order_by, $ascdesc ) = explode( '-', eazyest_gallery()->sort_by( 'thumbnails' ) );
		$limit = 0 < $number ? "LIMIT $number" : '';
		$post_ids = "= $post_id";
		$children = $this->get_folder_children( $post_id );
		if ( ! empty( $children ) ) {
			if ( $post_id )
				$children[] = $post_id;
			$childlist = implode( ',', $children );
			$post_ids = "IN ($childlist)";
		}
		$results = $wpdb->get_results( "
			SELECT ID FROM $wpdb->posts 
			WHERE post_parent $post_ids 
			AND post_type = 'attachment' 
			AND post_status IN ('inherit', 'publish')
			ORDER BY $order_by $ascdesc 
			$limit", 
			ARRAY_A
		);				
		if ( empty( $results ) )
			return array( 0 );
		
		$images = array();
		foreach( $results as $result ) {
			$images[] = $result['ID'];
		}
		return $images;			
	}
	
} // Eazyest_FolderBase

/**
 * eazyest_folderbase()
 * 
 * @since 0.1.0 (r2)
 * @return object Eazyest_FolderBase
 */
function eazyest_folderbase() {
	return Eazyest_Folderbase::instance();
}
