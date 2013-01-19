<?php
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 

/**
 * Eazyest_Settings_Page
 * Admin Settings Page for LAzyest Gallery
 * 
 * @package Eazyest Gallery
 * @subpackage Admin/Settings
 * @author Marcel Brinkkemper
 * @copyright 2013 Brimosoft
 * @version 0.1.0 (r21)
 * @since 0.1.0 (r2)
 * @access public
 */
class Eazyest_Settings_Page {
	
	/**
	 * single object in memory
	 */
	private static $instance;
	
	/**
	 * Eazyest_Settings_Page::__construct()
	 * 
	 * @return void
	 */
	function __construct(){}
	
	/**
	 * Eazyest_Settings_Page::init()
	 * Initialize
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	private function init() {
		$this->actions();
	}
	
	/**
	 * Eazyest_Settings_Page::instance()
	 * 
	 * @since 0.1.0 (r2)
	 * @return Eazyest_Settings_Page object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Eazyest_Settings_Page;
			self::$instance->init();
		}
		return self::$instance;
	}
	
	/**
	 * Eazyest_Settings_Page::actions()
	 * add WordPress actions
	 * 
	 * @since 0.1.0 (r2)
	 * @uses add_action()
	 * @return void
	 */
	function actions() {
		add_action( 'eazyest_gallery_main_settings', array( $this, 'main_settings' ), 1 );
		
		add_action( 'eazyest_gallery_settings_section', array( $this, 'folder_settings'   ), 10 );
		add_action( 'eazyest_gallery_settings_section', array( $this, 'image_settings'    ), 11 );
		add_action( 'eazyest_gallery_settings_section', array( $this, 'advanced_settings' ), 12 );
		
		add_action( 'admin_head',            array( $this, 'admin_style'           ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}
	
	/**
	 * Eazyest_Settings_Page::admin_style()
	 * Inline stylesheet for the settings page
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function admin_style() {
		if ( get_current_screen()->base != 'settings_page_eazyest-gallery' )
			return;
					
		$images_dir = eazyest_gallery()->plugin_url . 'admin/images/';
		$directory_png   = $images_dir . 'directory.png';
		$folder_open_png = $images_dir . 'folder_open.png';
		$file_png        = $images_dir . 'file.png';
		$spinner_gif     = $images_dir . 'spinner.gif';
		?>
		<style type="text/css" media="screen">
		#file-tree {
			background: white;
			border: 1px solid #DFDFDF;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			position:absolute;
			top: 25px;
			left: 0;
			width: 25em;
			z-index: 1000;
			display:none;
		}
		
		ul.jquery-filetree {
			font-family: Verdana, sans-serif;
			font-size: 11px;
			line-height: 18px;
			padding: 0px;
			margin: 0px;
		}

		ul.jquery-filetree li {
			list-style: none;
			padding: 0px;
			padding-left: 20px;
			margin: 0px;
			white-space: nowrap;
		}

		ul.jquery-filetree a {
			color: #333;
			text-decoration: none;
			display: block;
			padding: 0px 2px;
		}
		
		ul.jquery-filetree a:hover {
			background: #BDF;
		}
		.jquery-filetree li.directory { 
			background: url(<?php echo $directory_png; ?>) left top no-repeat; 
		}
		.jquery-filetree li.expanded { 
			background: url(<?php echo $folder_open_png; ?>) left top no-repeat; 
		}
		.jquery-filetree li.file {
			background: url(<?php echo $file_png ?>) left top no-repeat; 
		}
		.jquery-filetree li.wait { 
			background: url(<?php echo $spinner_gif ?>) left top no-repeat; 
		}
		#eazyest-ajax-response {
			color: #cc0000;
		}
		#eazyest-ajax-response code {
			color:  #ff0000;
			font-size:12px;
		}
		</style>
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::admin_enqueue_scripts()
	 * Register scripts for the settings page
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wp_register_script()
	 * @uses wp_localize_script()
	 * @return void
	 */
	function admin_enqueue_scripts() {
		$j = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js' : 'min.js';
		wp_register_script( 'jquery-filetree',          eazyest_gallery()->plugin_url . "admin/js/jquery.filetree.$j",          array( 'jquery' ),          '1.01-ezg',  true );
		wp_register_script( 'eazyest-gallery-settings', eazyest_gallery()->plugin_url . "admin/js/eazyest-gallery-settings.$j", array( 'jquery-filetree' ), '0.1.0-r21', true );
		
		wp_localize_script( 'eazyest-gallery-settings', 'fileTreeSettings', $this->filetree_args() );
	}
	
	/**
	 * Eazyest_Settings_Page::filetree_args()
	 * Array to set javascript argument to initialize jquery.filetree
	 * 
	 * @since 0.1.0 (r2)
	 * @uses admin_url()
	 * @return array
	 */
	function filetree_args() {	
		return array(
			'root'             => eazyest_gallery()->common_root(),
			'script'           => admin_url( 'admin-ajax.php' ),
			'loadMessage'      => __( 'Loading..',                               'eazyest-gallery' ),
			'errorMessage'     => __( 'You cannot use %s for a gallery folder.', 'eazyest-gallery' ),
			'notExistsMessage' => __( 'This folder does not exist yet.',         'eazyest-gallery' ),
			'notCreateMessage' => __( 'Could not create folder %s.',             'eazyest-gallery' ), 
		);	
	}
	
	// Sections definitions ------------------------------------------------------	
	
	/**
	 * Eazyest_Settings_Page::section()
	 * Returns array( 'title', 'description' ) per settings page section
	 * Add or change sections by adding filters: 
	 * apply_filters( 'eazyest_gallery_settings_sections', $sections );
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @param string $section
	 * @return array
	 */
	function section( $section = '' ) {
		$sections = array(
			'main-settings' => array(
				'title'       => __( 'Main Settings', 'eazyest-gallery' ),
				'description' => __( 'Main gallery settings to select your gallery', 'eazyest-gallery' ) 
			),
			'folder-settings' => array(
				'title'       => __( 'Folder Options', 'eazyest-gallery' ),
				'description' => __( 'How your folders will be displayed', 'eazyest-gallery' ) 
			),
			'image-settings' => array(
				'title'       => __( 'Image Options', 'eazyest-gallery' ),
				'description' => __( 'How your images will be displayed', 'eazyest-gallery' )
			),
			'advanced-settings' => array(
				'title'       => __( 'Advanced Options', 'eazyest-gallery' ),
				'description' => __( 'Options for advanced users', 'eazyest-gallery' )
			)
				
		);
		$sections = apply_filters( 'eazyest_gallery_settings_sections', $sections );
		return isset( $sections[$section] ) ? $sections[$section] : array();	
	}
	
	// Fields definitions --------------------------------------------------------
	
	/**
	 * Eazyest_Settings_Page::fields()
	 * Returns option fields array( 'title', 'callback'(optional) ) per settings page section
	 * Add more fields per section by adding filters:
	 * apply_filters( 'eazyest_gallery_folder_settings'   $fields );
	 * apply_filters( 'eazyest_gallery_image_settings',   $fields );
	 * apply_filters( 'eazyest_gallery_advanced_settings' $fields );
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @param string $section
	 * @return array
	 */
	function fields( $section ) {
		$sections = array();
		$sections['main-settings'] = array(
				'gallery_folder' => array(
					'title' => __( 'Your gallery folder', 'eazyest-gallery' )
				),
				'gallery_title' => array(
					'title' => __( 'Gallery title', 'eazyest-gallery' )
				),
				'donate' => array(
					'title' => __( 'Support Eazyest Gallery', 'eazyest-gallery' )
				)
			);
		$sections['folder-settings'] = apply_filters( 'eazyest_gallery_folder_settings', array(
			'folders_page' => array( 
				'title' => __( 'Icons per page', 'eazyest-gallery' ) 
			),
			'folders_columns' => array(
				'title' => __( 'Icon columns', 'eazyest-gallery' )
			),
			'sort_folders' => array(
				'title' => __( 'Sort folders by', 'eazyest-gallery' )
			),
			'count_subfolders' => array(
				'title' => __( 'Count images', 'eazyest-gallery' )
			),
			'folder_image' => array(
				'title' => __( 'Folder icons', 'eazyest-gallery' )
			)
		) );
		$sections['image-settings'] = apply_filters( 'eazyest_gallery_image_settings', array(
			'thumbs_page' => array(
				'title' => __( 'Thumbnails per page', 'eazyest-gallery' )					
			),
			'thumbs_columns' => array(
				'title' => ( 'Thumbnail columns' )
			),
			'thumb_caption' => array(
				'title' => __( 'Captions', 'eazyest-gallery' )
			),
			'sort_thumbnails' => array(
				'title' => __( 'Sort images by', 'eazyest-gallery' )
			),
			'on_thumb_click' => array(
				'title' => __( 'Thumbnail click', 'eazyest-gallery' )					
			),
			'on_slide_click' => array(
				'title' => __( 'Attachment click', 'eazyest-gallery' )
			),
			'listed_as' => array(
				'title' => __( 'List name', 'eazyest-gallery' )
			)
		) );
		$sections['advanced-settings'] = apply_filters( 'eazyest_gallery_advanced_settings', array(
			'gallery_slug' => array(
				'title' => __( 'Gallery slug', 'eazyest-gallery' )
			),
			'viewer_level' => array(
				'title' => __( 'Minimum viewer role', 'eazyest-gallery' )
			)
		) );
		$sections = apply_filters( 'eazyest-gallery-settings-section-fields', $sections );
		return isset( $sections[$section] ) ? $sections[$section] : array();
	}
	
	// Display section -----------------------------------------------------------
	
	/**
	 * Eazyest_Settings_Page::display_section()
	 * Output a section on the Settings screen
	 * 
	 * @since 0.1.0 (r2)
	 * @param string $section
	 * @return void
	 */
	function display_section( $section = '' ) {		
		if ( empty( $section ) )
			return;		
			
		$section_parts = $this->section( $section );
		$fields = $this->fields( $section );
		?>
		<h3><?php echo $section_parts['title']  ?></h3>
		<p><?php  echo $section_parts['description'] ?></p>		
		<?php if ( ! empty( $fields ) ) : ?>
		<table class="form-table">
			<tbody>
				<?php foreach( $fields as $field => $parts ) : ?>				
				<tr>
					<th scope="row"><?php echo $parts['title']; ?></th>
					<td>
						<?php if ( ! isset( $parts['callback'] ) ) : ?>
						<?php $this->$field(); ?>
						<?php else : ?>
						<?php call_user_func( $parts['callback'] ); ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php	
	}
	
	// Section Functions ---------------------------------------------------------	
	
	/**
	 * Eazyest_Settings_Page::main_settings()
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function main_settings() {
		$this->display_section( 'main-settings' );
	}
	
	/**
	 * Eazyest_Settings_Page::folder_settings()
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function folder_settings() {
		$this->display_section( 'folder-settings' );
	}
	
	/**
	 * Eazyest_Settings_Page::image_settings()
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function image_settings() {		
		$this->display_section( 'image-settings' );
	}
	
	/**
	 * Eazyest_Settings_Page::advanced_settings()
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function advanced_settings() {
		$this->display_section( 'advanced-settings' );
	}
	
	// Field Functions -----------------------------------------------------------
	
	// Main Settings ---------------------
	/**
	 * Eazyest_Settings_Page::gallery_folder()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @uses wp_nonce_field()
	 * @uses wp_enqueue_script()
	 * @return void
	 */
	function gallery_folder() {
		$gallery_folder = eazyest_gallery()->get_option( 'gallery_folder' );
		$gallery_secure = eazyest_gallery()->get_option( 'gallery_secure' );
		$new_install    = eazyest_gallery()->get_option( 'new_install'    );
		?><div style="position:relative">
			<?php wp_nonce_field( 'file-tree-nonce',      'file-tree-nonce',       false ) ?>
			<?php wp_nonce_field( 'gallery-folder-nonce', 'gallery-folder-nonce',  false ); ?>
			<input type="hidden" name="eazyest-gallery[gallery_secure]" value="<?php echo $gallery_secure; ?>" />			
			<input type="hidden" name="eazyest-gallery[new_install]" value="<?php echo $new_install; ?>" />
			<input type="text" name="eazyest-gallery[gallery_folder]" id="gallery_folder" size="60" class="regular-text code" value="<?php echo $gallery_folder ?>" />
			<a id="folder-select" class="button button-small open" href="#"><strong>&#8744;</strong></a><div id="file-tree"></div>
			<div id="eazyest-ajax-response" class="hidden"></div>
			<a id="create-folder" class="button hidden" href="#"><?php _e( 'Create folder', 'eazyest-gallery' ); ?></a>
		</div>	
		<?php
		wp_enqueue_script( 'eazyest-gallery-settings' );
	}
	
	function gallery_title() {
		$gallery_title = eazyest_gallery()->gallery_title();
		?>
		<input type="text" id="gallery_title" name="eazyest-gallery[gallery_title]" size="32" class="regular-text" value="<?php echo $gallery_title ?>" />
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::donate()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @uses checked()
	 * @return void
	 */
	function donate() {		
		$show_credits = eazyest_gallery()->get_option( 'show_credits' );
		$vife_stars   = eazyest_gallery()->plugin_url . 'admin/images/5-stars.png';		
		?> 
		<p>
			<input type="checkbox" id="show_credits" name="eazyest-gallery[show_credits]" <?php checked( $show_credits ) ?> />
			<label for="show_credits"><?php _e( 'Show the "Powered by Eazyest Gallery" banner with your gallery', 'eazyest-gallery' ) ?> </label>
		</p>
		<p>
			<a href="http://wordpress.org/support/view/plugin-reviews/eazyest-gallery" target="_blank"><?php _e( 'Review and rate Eazyest Gallery', 'eazyest-gallery' ) ?><img height="17" width="92" src="<?php echo $vife_stars; ?>" alt="five stars" /></a> 
		</p>
		<p>
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MSWK36DKJ7GW4" title="<?php _e( 'Support the development of Eazyest Galery', 'eazyest-gallery' ); ?>">
				<img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="PayPal - The safer, easier way to pay online!" />
			</a>
		</p>
		<?php
	}
	
	// Folder options ------------------------------------------------------------
	/**
	 * Eazyest_Settings_Page::folders_page()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function folders_page() {
		$folders_page = eazyest_gallery()->get_option( 'folders_page' );
		?>
		<input id="folders_page" name="eazyest-gallery[folders_page]" type="number" min="0" step="1" class="small-text" value="<?php echo $folders_page; ?>" />
		<label for="folders_page"><?php _e( 'per page', 'eazyest-gallery' ) ?></label>
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::folders_columns()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function folders_columns() {
		$folders_columns = eazyest_gallery()->get_option ( 'folders_columns' );
		?>
		<input id="folders_columns" name="eazyest-gallery[folders_columns]" type="number" min="0" step="1" class="small-text" value="<?php echo $folders_columns; ?>" />
		<label for="folders_columns"><?php _e( 'columns', 'eazyest-gallery' ) ?></label>
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::sort_options()
	 * Return array of options to sort folders and images
	 * 
	 * @since 0.1.0 (r2)
	 * @return array
	 */
	private function sort_options() {
		return array( 
			'post_name-ASC'   => __( 'Name, ascending (A-Z)',     'eazyest-gallery' ), 
			'post_name-DESC'  => __( 'Name, descending (Z-A)',    'eazyest-gallery' ),
			'post_title-ASC'  => __( 'Caption, ascending (A-Z)',  'eazyest-gallery' ), 
			'post_title-DESC' => __( 'Caption, descending (Z-A)', 'eazyest-gallery' ),
			'post_date-ASC'   => __( 'Date, oldest first',        'eazyest-gallery' ), 
			'post_date-DESC'  => __( 'Date, newest first',        'eazyest-gallery' ), 
			'menu_order-ASC'  => __( 'Manually',                  'eazyest-gallery' )
		);
	}
	
	/**
	 * Eazyest_Settings_Page::sort_folders()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function sort_folders() {
		$sort_folders = eazyest_gallery()->get_option( 'sort_folders' );
		$options = $this->sort_options(); 
		?>
		<select id="sort_folders" name="eazyest-gallery[sort_folders]">
			<?php foreach( $options as $value => $option) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, $sort_folders ); ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::count_subfolders()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function count_subfolders() {
		$count_subfolders = eazyest_gallery()->get_option( 'count_subfolders' );
		$options = array(
			'none'     => __( 'Show number of images in folder only',                      'eazyest-gallery' ),
			'include'  => __( 'Show number of images in folder including subfolders',      'eazyest-gallery' ),
			'separate' => __( 'Show number of images in folder and subfolders separately', 'eazyest-gallery' ),
			'nothing'  => __( 'Do not show number of images in folder',                    'eazyest-gallery' )
		);
		?>
		<select id="count_subfolders" name="eazyest-gallery[count_subfolders]">
			<?php foreach( $options as $value => $option ) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, $count_subfolders ); ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::folder_image()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function folder_image() {
		$folder_image     = eazyest_gallery()->get_option( 'folder_image'     );
		$random_subfolder = eazyest_gallery()->get_option( 'random_subfolder' );
		$options = array(
			'featured_image' => __( 'Featured image',        'eazyest-gallery' ),
			'first_image'    => __( 'First image in folder', 'eazyest-gallery' ),
			'random_image'   => __( 'Random Image',          'eazyest-gallery' ),
			'icon'           => __( 'Folder Icon',           'eazyest-gallery' ),
			'none'           => __( 'Title only',            'eazyest-gallery' )
		);
		?>
		<select id="folder_image" name="eazyest-gallery[folder_image]">
			<?php foreach( $options as $value => $option ) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, $folder_image ); ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
		</select>
		<p id="random-subfolder" style="visibility:hidden">
			<input id="random_subfolder" name="eazyest-gallery[random_subfolder]" type="checkbox" value="1" <?php checked( '1', $random_subfolder ) ?> />
			<label for="random_subfolder"><?php _e( 'Include images from subfolders', 'eazyest-gallery' ); ?></label>
   	</p>
		<?php
	}
	
	// Image options ---------------------
	/**
	 * Eazyest_Settings_Page::thumbs_page()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function thumbs_page() {
		$thumbs_page = eazyest_gallery()->get_option( 'thumbs_page' );
		?>
		<input id="thumbs_page" name="eazyest-gallery[thumbs_page]" type="number" min="0" step="1" class="small-text" value="<?php echo $thumbs_page; ?>" />
		<label for="thumbs_page"><?php _e( 'per page', 'eazyest-gallery' ) ?></label>
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::thumbs_columns()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function thumbs_columns() {
		$thumbs_columns = eazyest_gallery()->get_option ( 'thumbs_columns' );
		?>
		<input id="thumbs_columns" name="eazyest-gallery[thumbs_columns]" type="number" min="0" step="1" class="small-text" value="<?php echo $thumbs_columns; ?>" />
		<label for="thumbs_columns"><?php _e( 'columns', 'eazyest-gallery' ) ?></label>
		<?php
	} 
	
	/**
	 * Eazyest_Settings_Page::thumb_caption()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function thumb_caption() {
		$thumb_caption = eazyest_gallery()->get_option( 'thumb_caption' );
		?>
		<input type="checkbox" id="thumb_caption" name="eazyest-gallery[thumb_caption]" <?php checked( $thumb_caption ) ?> />
		<label for="thumb_caption"><?php _e( 'Show captions in thumbnail view', 'eazyest-gallery' ) ?> </label>
		<?php
	} 
	
	/**
	 * Eazyest_Settings_Page::sort_thumbnails()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function sort_thumbnails() {
		$sort_thumbnails = eazyest_gallery()->get_option( 'sort_thumbnails' );
		$options = $this->sort_options(); 
		?>
		<select id="sort_thumbnails" name="eazyest-gallery[sort_thumbnails]">
			<?php foreach( $options as $value => $option) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, $sort_thumbnails ); ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	
	private function popup_options() {
		return array(
			'none'      => __( 'Just display the image', 'eazyest-gallery' ),
			'lightbox'  => __( 'Add lightbox markup',    'eazyest-gallery' ),
			'thickbox'  => __( 'Add thickbox markup',    'eazyest-gallery' ),
			'fancybox'  => __( 'Add fancybox markup',    'eazyest-gallery' ),
			'shadowbox' => __( 'Add shadowbox markup',   'eazyest-gallery' ),
		);
	}
	
	/**
	 * Eazyest_Settings_Page::on_thumb_click()
	 * Output settings field markup
	 * Apply filters to add alternative popup options
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @return void
	 */
	function on_thumb_click() {
		$on_thumb_click = eazyest_gallery()->get_option( 'on_thumb_click' );
		$thumb_popup    = eazyest_gallery()->get_option( 'thumb_popup'    );
		$options = array(
			'nothing'    => __( 'Nothing',                'eazyest-gallery' ),
			'attachment' => __( 'Show attachment page',   'eazyest-gallery' ),
			'medium'     => __( 'Show medium size image', 'eazyest-gallery' ),
			'large'      => __( 'Show large size image',  'eazyest-gallery' ),
			'full'       => __( 'Show full size image',   'eazyest-gallery' ),			
		);
		$popups = $this->popup_options();
		$popups = apply_filters( 'eazyest_gallery_thumbnail_popup', $popups );
		?>
		<select id="on_thumb_click" name="eazyest-gallery[on_thumb_click]">
			<?php foreach( $options as $value => $option ) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, $on_thumb_click ); ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
		</select>
		<p id="thumb-popup" style="visibility:hidden">
			<select id="thump_popup" name="eazyest-gallery[thumb_popup]">
				<?php foreach( $popups as $value => $option ) : ?>
				<option value="<?php echo $value; ?>" <?php selected( $value, $thumb_popup ); ?>><?php echo $option; ?></option>
				<?php endforeach; ?>
			</select>
   	</p>
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::on_slide_click()
	 * Output settings field markup
	 * Apply filters to add alternative popup options
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @return void
	 */
	function on_slide_click() {
		$on_slide_click = eazyest_gallery()->get_option( 'on_slide_click' );
		$slide_popup    = eazyest_gallery()->get_option( 'slide_popup'    );
		$options = array(
			'nothing'    => __( 'Nothing',              'eazyest-gallery' ),
			'full'       => __( 'Show full size image', 'eazyest-gallery' ),			
		);
		$popups = $this->popup_options();
		$popups = apply_filters( 'eazyest_gallery_attachment_popup', $popups );
		?>
		<select id="on_slide_click" name="eazyest-gallery[on_slide_click]">
			<?php foreach( $options as $value => $option ) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, $on_slide_click ); ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
		</select>
		<p id="slide-popup" style="visibility:hidden">
			<select id="slide_popup" name="eazyest-gallery[slide_popup]">
				<?php foreach( $popups as $value => $option ) : ?>
				<option value="<?php echo $value; ?>" <?php selected( $value, $slide_popup ); ?>><?php echo $option; ?></option>
				<?php endforeach; ?>
			</select>
   	</p>
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::listed_as()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function listed_as() {
		$listed_as = eazyest_gallery()->get_option( 'listed_as' );
		?>
		<input type="text" id="listed_as" name="eazyest-gallery[listed_as]" value="<?php echo $listed_as; ?>" />
		<?php
	}
	
	// Advanced options ------------------
	/**
	 * Eazyest_Settings_Page::gallery_slug()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function gallery_slug() {
		$gallery_slug = eazyest_gallery()->get_option( 'gallery_slug' );?>
		<input type="text" id="gallery_slug" class="regular-text code" name="eazyest-gallery[gallery_slug]" value="<?php echo $gallery_slug; ?>" />
		<?php
	}
	
	/**
	 * Eazyest_Settings_Page::everyone()
	 * Returns dummy role name 'Everyone'
	 * 
	 * @since 0.1.0 (r2) 
	 * @return string
	 */
	function everyone() {
		return __( 'Everyone', 'eazyest-gallery' );
	}
	
	/**
	 * Eazyest_Settings_Page::viewer_level()
	 * Output settings field markup
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	function viewer_level() {
		$viewer_level = eazyest_gallery()->get_option( 'viewer_level' );
		$options = array(
			'everyone' => $this->everyone()
		);
		global $wp_roles;
		foreach( $wp_roles->role_names as $role => $name ) {
			$add = false;
			foreach( $wp_roles->roles[$role]['capabilities'] as $cap ) {
				if ( $cap )
					$add = true;
			}
			if ( $add )
				$options[$role] = $name;
		}
		?>
		<select id="viewer_level" name="eazyest-gallery[viewer_level]">
			<?php foreach( $options as $value => $option ) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, $viewer_level ); ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	
	// Help tabs -----------------------------------------------------------------
	
	/**
	 * Eazyest_Settings_Page::help_sections()
	 * Return array of help tabs for sections
	 * 
	 * @since 0.1.0 (r2)
	 * @see http://codex.wordpress.org/Function_Reference/add_help_tab
	 * @uses admin_url()
	 * @return array()
	 */
	function help_sections() {
		$options_media = admin_url( 'options-media.php' );
		return array(
			'overview' => array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'eazyest-gallery' ),
				'content' => "\n<p>" .          __( 'This screen provides access to all of the Eazyest Gallery settings.',                 'eazyest-gallery' ) . "</p>" .
				             "\n<p>" .          __( 'Please see the additional help tabs for more information on each indiviual section.', 'eazyest-gallery' ) . "</p>" .
										 /* translators %s <a href=> %s </a> */				    		             
    		             "\n<p>" . sprintf( __( 'For Image sizes, please refer to %sWordPress Media Settings%s.',                      'eazyest-gallery' ), "<a href='$options_media'>", "</a>" ) . "</p>"
			),
			'main' => array(
				'id'      => 'main-settings',
				'title'   => __( 'Main Settings', 'eazyest-gallery' ),
				'content' => "\n<p>" .         __( 'In the Main Settings you set the server directory for your gallery.', 'eazyest-gallery' ) . "<br />\n" .
				                               __( 'Eazyest Gallery will not work if this directory does not exist.',     'eazyest-gallery' ) . "</p>\n" .
				             "\n<p>" .         __( 'Set a title for your gallery archive page',                           'eazyest-gallery' ) . "</p>\n" .
				             "\n<p><strong>" . __( 'Please donate to support the development of Eazyest Gallery',         'eazyest-gallery' ) . "</strong></p>\n"       
			),
			'folder' => array(
				'id'      => 'folder-settings',
				'title'   => __( 'Folder Options', 'eazyest-gallery' ),
				'content' => "\n<p>" . __( 'In the Folder Options you have a number of options to control how your folders will be displayed.', 'eazyest-gallery' ) . "</p>" .
				             "\n\t<ul>" .
				             "\n\t\t<li>" .     __( 'Select the number of folder icons to initially display in the "Icons per page" option.',                           'eazyest-gallery' ) . "</li>"  .
				             "\n\t\t<li>" .     __( 'The option "Icon columns" sets the number of icons to display per row.',                                           'eazyest-gallery' ) . "<br />" .
				                                __( 'Set this option to 0 if you want to display as many icons that fit your page.',                                    'eazyest-gallery' ) . "<br />" .
				                                __( 'If you set any other value, Eazyest Gallery will shrink your icons when necessary.',                               'eazyest-gallery' ) . "</li>"  .
				             "\n\t\t<li>" .     __( 'Select how you want your want to sort your folders in the "Sort folders by" option.',                              'eazyest-gallery' ) . "</li>"  .
										 "\n\t\t<li>" .     __( 'If you want to display the number of images in your folders in the icons view, select the "Count images" option.', 'eazyest-gallery' ) . "</li>"  .
										 "\n\t\t<li>" .     __( 'In the "Folder icons" options, you set what to display in the folder icons view.',                                 'eazyest-gallery' ) . 
										 "\n\t\t\t<ul>" . 
										 "\n\t\t\t\t<li>" . __( '"Featured image" will display the folder featured image.',                                                         'eazyest-gallery' ) . "<br />" .
										                    __( 'If you haven\'t selected a featured image yet, Eazyest Gallery will show the first image in your folder.',         'eazyest-gallery' ) . "</li>"  .
  									 "\n\t\t\t\t<li>" . __( '"First image in folder" will display the first image, after sorting the images.',                                  'eazyest-gallery' ) . "</li>"  .
  									 "\n\t\t\t\t<li>" . __( '"Random image" will select a random image each time you open the folder icons page.',                              'eazyest-gallery' ) . "<br />" .
  									                    __( 'If you want to include random images from subfolders, please select ""Include images from subfolders".',         'eazyest-gallery' ) . "</li>"  .
  									 "\n\t\t\t\t<li>" . __( '"Folder icon" will display a standard folder icon.',                                                               'eazyest-gallery' ) . "</li>"  .
										 "\n\t\t\t\t<li>" . __( 'If you don\'t want to display an image, please select "Title only".',                                              'eazyest-gallery' ) . "</li>"  .                   
  									 "\n\t\t\t</ul>" .
										 "\n\t\t</li>" .									  
				             "\n\t</ul>" .				             				    		             
    		             "\n<p>" . sprintf( __( 'For Image sizes, please refer to %sWordPress Media Settings%s.',                                                   'eazyest-gallery' ), 
										 						"<a href='$options_media'>",
										 						"</a>" ) . 
										 "</p>"
			),
			'image' => array(
				'id'      => 'image-settings',
				'title'   => __( 'Image Options', 'eazyest-gallery' ),
				'content' => "\n<p>" .          __( 'In the Folder Options you have a number of options to control how your images will be displayed.', 'eazyest-gallery' ) . "</p>" .
				             "\n\t<ul>" .
				             
				             "\n\t\t<li>" .     __( 'Select the number of thumbnails to initially display in the "Thumbnails per page" option.',        'eazyest-gallery' ) . "</li>"  .
				             "\n\t\t<li>" .     __( 'The option "Thumbnail columns" sets the number of icons to display per row.',                      'eazyest-gallery' ) . "<br />" .
				                                __( 'Set this option to 0 if you want to display as many thumbnails that fit your page.',               'eazyest-gallery' ) . "<br />" .
				                                __( 'If you set any other value, Eazyest Gallery will shrink your thumbnails when necessary.',          'eazyest-gallery' ) . "</li>"  .
				             "\n\t\t<li>" .     __( 'Keep your descriptions as short as possible when you select the "Descriptions" option.',           'eazyest-gallery' ) . "</li>"  .
				             "\n\t\t<li>" .     __( 'Select how you want your want to sort your images in the "Sort images by" option.',                'eazyest-gallery' ) . "</li>"  .
                     "\n\t\t<li>" .     __( 'Select what will happen if you click on a thumbnail image in the "Thumbnail click" option.',       'eazyest-gallery' ) .  
										 "\n\t\t\t<ul>" . 
                     "\n\t\t\t\t<li>" . __( 'If you select "Nothing", you will stay on the thumnails page',                                     'eazyest-gallery' ) . "</li>"  .
                     "\n\t\t\t\t<li>" . __( 'If you select "Show attachment page", you will be redirected to the attachment page.',             'eazyest-gallery' ) . "</li>"  .
                     "\n\t\t\t\t<li>" . __( 'If you select "Show medium size image", you will see the medium sized image in your browser.(*)',  'eazyest-gallery' ) . "</li>"  .
                     "\n\t\t\t\t<li>" . __( 'If you select "Show large size image", you will see the large sized image in your browser.(*)',    'eazyest-gallery' ) . "</li>"  .
                     "\n\t\t\t\t<li>" . __( 'If you select "Show full size image", you will see the original image* in your browser.(*)',       'eazyest-gallery' ) . "</li>"  .
										 "\n\t\t\t</ul>" .  __( '(*) You may want to add markup for popular popup plugins like Lightbox or Thickbox.',              'eazyest-gallery' ) . "</li>"  .
										 "\n\t\t<li>" .     __( '"List name" displays what type of images you have in your gallery.',                               'eazyest-gallery' ) . "</li>"  .
										 "\n\t</ul>" . 				             				    		             
    		             "\n<p>" . sprintf( __( 'For Image sizes, please refer to %sWordPress Media Settings%s.',                                   'eazyest-gallery' ), 
										 						"<a href='$options_media'>",
										 						"</a>" ) . 
										 "</p>"
			),
			'advanced' => array(
				'id'      => 'advanced-settings',
				'title'   => __( 'Advanced Options', 'eazyest-gallery' ),
				'content' => "\n<p>" . __( 'In Advaced Options you have a number of options for advanced use of Eazyest Gallery',                  'eazyest-gallery' ) . "</p>"   .
				             "\n\t<ul>" .
				             "\n\t\t<li>" . __('"Gallery slug" is a custom root slug to prefix your gallery with.',                                'eazyest-gallery' ) . "</li>"  .
				             "\n\t\t<li>" . __('"Minimum viewer role" selects the minimum role/capability a user must have to view your gallery.', 'eazyest-gallery' ) . "<br />" . 
						               sprintf( __('If you select "%s", a viewer does not have to be logged in',                                       'eazyest-gallery' ), $this->everyone() ). "</li>" 
			)
		);
	}
	
	/**
	 * Eazyest_Settings_Page::add_help_tabs()
	 * Add all help tabs
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_current_screen()
	 * @uses WP_Screen::add_help_tab()
	 * @return void
	 */
	function add_help_tabs() {
		foreach( $this->help_sections() as $args )
			get_current_screen()->add_help_tab( $args );
	}
	
	// Display -------------------------------------------------------------------
	
	/**
	 * Eazyest_Settings_Page::display()
	 * Display the Setiings screen
	 * 
	 * @since 0.1.0 (r2)
	 * @uses settings_fields()
	 * @uses do_action()
	 * @uses submit_button()
	 * @return void
	 */
	function display() {
		?>
	
		<div class="wrap">
	
			<?php screen_icon(); ?>
	
			<h2><?php _e( 'Eazyest Gallery Settings', 'eazyest-gallery' ) ?></h2>
			<form action="options.php" method="post">
	
				<?php settings_fields( 'eazyest-gallery' ); ?>
				
				<?php do_action( 'eazyest_gallery_main_settings' ); ?>
				
				<?php if ( eazyest_gallery()->right_path() ) : ?>
				<?php do_action( 'eazyest_gallery_settings_section' ); ?>
				<?php endif; ?>
	
				<?php submit_button(); ?>
			</form>
		</div>
	
		<?php
	}
} // Eazyest_Settings_Page