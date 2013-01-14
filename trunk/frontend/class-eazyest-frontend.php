<?php
  
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 

/**
 * Eazyest_Frontend class
 * This class contains all Frontend functions and actions for Eazyest Gallery
 *
 * @version 0.1.0 (r2)
 * @package Eazyest Gallery
 * @subpackage Frontend
 * @author Marcel Brinkkemper
 * @copyright 2010-2013 Brimosoft
 * @since lazyest-gallery 0.16.0
 */
class Eazyest_Frontend {
	
	/**
	 * @staticvar Eazyest_Frontend $instance single instance in memory
	 */ 
	private static $instance;
	
	/**
	 * @var array $data overloaded properties
	 * @access private
	 */
	private $data;
	
	/**
	 * Eazyest_Frontend::__construct()
	 * 
	 * @return void
	 */
	function __construct() {}

	/**
	 * Magic method for checking the existence of a certain custom field
	 *
	 * @since 0.1.0 (r2)
	 */
	public function __isset( $key ) { 
		return isset( $this->data[$key] ); 
	}

	/**
	 * Magic method for getting variables
	 *
	 * @since 0.1.0 (r2)
	 */
	public function __get( $key ) { 
		switch( $key ) {
			case 'folder_columns' :
				$columns = isset( $this->data['folder_columns'] ) ? $this->data['folder_columns'] : eazyest_gallery()->get_option( 'folders_columns' );
				return intval( $columns );
			case 'thumbnail_columns' :	
				$columns = isset( $this->data['thumbnail_columns'] ) ? $this->data['thumbnail_columns'] : eazyest_gallery()->get_option( 'thumbs_columns' );
				return intval( $columns ); 
			default :	
				return isset( $this->data[$key] ) ? $this->data[$key] : null;
		} 
	}

	/**
	 * Magic method for setting variables
	 *
	 * @since 0.1.0 (r2)
	 */
	public function __set( $key, $value ) { 
		if ( in_array( $key, 'folder_columns', 'thumbs_columns' ) )
			$value = absint( $value );
		$this->data[$key] = $value; 
	}
	
	/**
	 * Eazyest_Frontend::init()
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	private function init() {
		$this->includes();
		$this->actions();
		$this->filters();
		$this->shortcodes();
	}
	
	/**
	 * Eazyest_Frontend::instance()
	 * @since 0.1.0 (r2)
	 * @return
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Eazyest_Frontend;
			self::$instance->init();
		}
		return self::$instance;
	}
	
	/**
	 * Eazyest_Frontend::includes()
	 * Include files needed for frontend
	 * 
	 * @since 0.1.0 (r2)
	 * @return void
	 */
	private function includes() {
		/**
		 * include template tags for theme builders.
		 * @since 0.1.0 (r2)
		 */ 
		include( eazyest_gallery()->plugin_dir . '/frontend/template-tags.php' );	
	}
	
	/**
	 * Eazyest_Frontend::actions()
	 * Hook WordPress actions
	 * 
	 * @since 0.1.0 (r2)
	 * @uses add_action()
	 * @return void
	 */
	function actions() {
		add_action( 'wp_head', array( $this, 'setup_tags' ), 1 );
		
		// gallery output actions
		add_action( 'eazyest_gallery_after_folder_icon',         'lzg_folder_icon_caption',       5 );
		add_action( 'eazyest_gallery_after_folder_icon_caption', 'lzg_folder_attachments_count',  5 );
		add_action( 'eazyest_gallery_before_folder_content',     'lzg_breadcrumb',                5 );
		add_action( 'eazyest_gallery_before_folder_content',     'lzg_thumbnails',               10 );
		add_action( 'eazyest_gallery_after_folder_content',      'lzg_subfolders',                5 ); 
	}

	/**
	 * Eazyest_Frontend::filters()
	 * Hook WordPres filters
	 *
	 * @since 0.1.0 (r2)
	 * @uses add_filter()
	 * @return void
	 */
	function filters() {
		add_filter( 'pre_get_posts',          array( $this, 'pre_get_posts'       )        );		
		// post thumbnail filters
		add_filter( 'get_post_metadata',      array( $this, 'post_thumbnail_id'   ), 10, 3 );
		add_filter( 'post_thumbnail_size',    array( $this, 'post_thumbnail_size' )        );
		add_filter( 'post_thumbnail_html',    array( $this, 'post_thumbnail_html' ), 10, 2 );
		// template filters
		add_filter( 'template_include',       array( $this, 'template_include'    )        );
		// attachmnent filters
		add_filter( 'attachment_link',        array( $this, 'attachment_link'     ), 10, 2 );
		add_filter( 'wp_get_attachment_link', array( $this, 'add_attr_to_link'    ),  1, 2 );
		// content filters
		add_filter( 'the_content',            array( $this, 'folder_content'      ), 99    );
		add_filter( 'the_excerpt',            array( $this, 'folder_content'      ), 99    );
	}	
	
	/**
	 * Eazyest_Frontend::shortcodes()
	 * Load the EazyestShortcodes object
	 * 
	 * @since 0.1.0 (r2)
	 * @return object Eazyest_Shortcodes 
	 */
	function shortcodes()	{
		require_once( eazyest_gallery()->plugin_dir . '/frontend/class-eazyest-shortcodes.php' );
		return Eazyest_Shortcodes::instance();
	}
	
	// core functions ----------------------------------------
	/**
	 * Eazyest_Frontend::setup_tags()
	 * html tags for the Eazyest gallery gallery markup
	 * applies filters for plugins to change default tags:
	 * <code>'eazyest_gallery_itemtag'</code>
	 * <code>'eazyest_gallery_icontag'</code>
	 * <code>'eazyest_gallery_captiontag'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @return void
	 */
	function setup_tags() {		
		$this->itemtag    = apply_filters( 'eazyest_gallery_itemtag',    'dl' );
		$this->icontag    = apply_filters( 'eazyest_gallery_icontag',    'dt' );
		$this->captiontag = apply_filters( 'eazyest_gallery_captiontag', 'dd' );
	}
	
	/**
	 * Eazyest_Frontend::pre_get_posts()
	 * Set 'posts_per_page' for galleryfolder post_type according to setting 'folders_page'
	 * 
	 * @since 0.1.0 (r2)
	 * @uses WP_Query
	 * @uses is_admin()
	 * @uses is_post_type_archive()
	 * @param WP_Query $query
	 * @return WP_Query
	 */
	function pre_get_posts( $query ) {
		if ( is_admin() )
 			return $query;
 			$post_type = eazyest_gallery()->post_type;
 		if ( is_post_type_archive( $post_type ) && $post_type == $query->get( 'post_type' ) ) {
 			$query->set( 'post_parent', 0 );
 			$posts_per_page = eazyest_gallery()->get_option( 'folders_page' );
 			if ( 0 == $posts_per_page )
 				$posts_per_page = -1;
 			$query->set( 'posts_per_page', $posts_per_page );
 		}
 		return $query;
	}
	
	// template functions	--------------------------------------------------------
	/**
	 * Eazyest_Frontend::theme_compatible()
	 * Check if team is compatible 
	 * Eazyest Gallery includes templates and template parts for compatible themes
	 * 
	 * @since 0.1.0 (r2)
	 * @return bool
	 */
	function theme_compatible() {		
		$theme = basename( TEMPLATEPATH );
		if ( in_array( $theme, array( 'twentyten', 'twentyeleven', 'twentytwelve' ) ) )
			return $theme;
		else
			return false;	
	}
	
	/**
	 * Eazyest_Frontend::default_theme_base()
	 * Default compatible themes base directory
	 * Plugins can apply filter 
	 * <code>'eazyest_gallery_default_theme_base'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @return string path
	 */
	function default_theme_base() {
		$default_dir = eazyest_gallery()->plugin_dir . "themes/default";
		return apply_filters( 'eazyest_gallery_default_theme_base', $default_dir );
	}
	
	/**
	 * Eazyest_Frontend::theme_dir()
	 * Directory for specific compatible theme
	 * Plugins can apply filter
	 * <code>'eazyest_gallery_theme_dir'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @return mixed string when path exists, bool false if not found
	 */
	function theme_dir() {
		if ( $theme = $this->theme_compatible() ) {
			$theme_dir = eazyest_gallery()->plugin_dir . "themes/$theme";
			return apply_filters( 'eazyest_gallery_theme_dir', $theme_dir, $theme );
		}
		else 
			return false;	 	
	}
	
	/**
	 * Eazyest_Frontend::template_include()
	 * Returns template for galleryfolder post_type
	 * If theme does not support eazyest-gallery,
	 * Eazyest Gallery searches for template
	 * 
	 * @since 0.1.0 (r2)
	 * @uses is_post_type_archive()
	 * @uses is_single()
	 * @uses get_post_type()
	 * @param string $template
	 * @return string
	 */
	function template_include( $template ) {		
		if ( current_theme_supports( 'eazyest-gallery' ) )
			return;
		$post_type = eazyest_gallery()->post_type;
		$template_name = '';
		if ( is_post_type_archive( $post_type ) )
			$template_name =  "/archive-galleryfolder.php";
		if ( is_single() && $post_type == get_post_type() )
			$template_name = "/single-galleryfolder.php";
		if (  '' != $template_name ) {		
			if ( file_exists( STYLESHEETPATH . $template_name ) ) {
				$template = STYLESHEETPATH . $template_name;
			} else if ( file_exists( TEMPLATEPATH . $template_name ) ) {
				$template = TEMPLATEPATH . $template_name;
			} else if ( $theme_dir = $this->theme_dir()  ) {
				if ( file_exists( $theme_dir . $template_name ) ) {
					$template = $theme_dir . $template_name;
					if ( file_exists( $theme_dir . '/functions.php' ) )
						include( $theme_dir . '/functions.php' );
				}
			} 
		}
		return $template;		
	}
	
	/**
	 * Eazyest_Frontend::get_template_part()
	 * 
	 * @since 0.1.0 (r2)
	 * @see http://codex.wordpress.org/Function_Reference/get_template_part
	 * @uses do_action()
	 * @param string $slug
	 * @param string $name
	 * @return void
	 */
	function get_template_part( $slug, $name = null ) {
		do_action( "get_template_part_{$slug}", $slug, $name );
		$templates = array();
		if ( isset( $name ) )
			$templates[] = "{$slug}-{$name}.php";
	
		$templates[] = "{$slug}.php";
		$this->locate_template( $templates, true, false );		
	}
	
	/**
	 * Eazyest_Frontend::locate_template()
	 * Locate a template for Eazyest Gallery
	 * 
	 * @since 0.1.0 (r2)
	 * @see http://codex.wordpress.org/Function_Reference/locate_template
	 * @uses load_template()
	 * @param array $template_names
	 * @param bool $load
	 * @param bool $require_once
	 * @return string path of template
	 */
	function locate_template( $template_names, $load = false, $require_once = true ) {
		$located = '';
		foreach ( (array) $template_names as $template_name ) {
			if ( !$template_name )
				continue;
			if ( file_exists( STYLESHEETPATH . '/' . $template_name)) {
				$located = STYLESHEETPATH . '/' . $template_name;
				break;
			} else if ( file_exists( TEMPLATEPATH . '/' . $template_name) ) {
				$located = TEMPLATEPATH . '/' . $template_name;
				break;
			} else if ( ( $theme_dir = $this->theme_dir() ) && file_exists( $theme_dir . '/' . $template_name ) ) {
				$located = $theme_dir . '/' . $template_name;
				break;
			} else if ( file_exists( $this->default_theme_base() . '/' . $template_name ) ) {
				$located = $this->default_theme_base() . '/' . $template_name;
				break;
			}
		}
		if ( $load && '' != $located )
			load_template( $located, $require_once );
		return $located;
	}
	
	/**
	 * Eazyest_Frontend::content_galleryfolder()
	 * Return the content ( gallery icon ) for a galleryfolder for an archive query
	 * If you have template content-galleryfolder.php in your theme root, this will not be called
	 * Filters 'the_content' when no compatible theme is found
	 * Does actions for plugins to <strong>echo</strong> content
	 * <code>'eazyest_gallery_before_folder_icon'</code>
	 * <code>'eazyest_gallery_after_folder_icon'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses do_action()
	 * @uses the_permlink()
	 * @uses esc_attr()
	 * @uses the_title_attribute()
	 * @param string $content
	 * @return string
	 */
	function content_galleryfolder( $content ) {
		ob_start(); // buffer to use actions that echo content
		?>
		
		<div class="gallery eazyest-gallery">
			<dl class="gallery-item folder-item">
				<?php do_action( 'eazyest_gallery_before_folder_icon' ); ?>
				<dt class="gallery-icon folder-icon">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'View folder &#8220;%s&#8221;', 'eazyest-gallery' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
						<?php echo $this->folder_thumbnail_html() ?>
					</a>
				</dt>
				<?php do_action( 'eazyest_gallery_after_folder_icon' ); ?>
			</dl>
		</div>
		
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;		
	}
	
	/**
	 * Eazyest_Frontend::content_single_galleryfolder()
	 * Return the content ( thumbnails ) for a galleryfolder for a single query.
	 * If you have template content-single-galleryfolder.php in your theme root, this will not be called.
	 * Does actions for plugins to <strong>echo</strong> content:
	 * <code>'eazyest_gallery_before_folder_content'</code>
	 * <code>'eazyest_gallery_after_folder_content'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses do_action()
	 * @param string $content
	 * @return string
	 */
	function content_single_galleryfolder( $content ) {	
		ob_start(); // buffer to use actions that echo content
		?>
		
		<div class="eazyest-gallery">
			<?php do_action( 'eazyest_gallery_before_folder_content' ); // this action is used for breadcrumb trail and for thumbnail images ?>
			<?php echo $content;                                        // this is the text content like a regular WordPress post ?> 
			<?php do_action( 'eazyest_gallery_after_folder_content'  ); // this action is used for extra fields and for subfolders ?>
		</div>
		
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;		
	}
	
	/**
	 * Eazyest_Frontend::folder_content()
	 * Filter for 'the_content'
	 * Content is filtered when eazyest-gallery is not supported or theme is not compatible
	 * 
	 * @since 0.1.0 (r2)
	 * @uses current_theme_supports()
	 * @uses is_single()
	 * @uses is_archive()
	 * @uses is_search()
	 * @uses WP_Post
	 * @param string $content
	 * @return string
	 */
	function folder_content( $content ) {
		global $post;
				
		if ( eazyest_gallery()->post_type != $post->post_type )
			return $content;
				
		if ( current_theme_supports( 'eazyest-gallery' ) || $this->theme_compatible() )
			return $content; 
			
		if ( is_single() )
			return $this->content_single_galleryfolder( $content );		
		
		if ( is_archive() || is_search() )
			return $this->content_galleryfolder( $content );
	}
	
	// thumbnail functions -------------------------------------------------------
	
	/**
	 * Eazyest_Frontend::post_thumbnail_id()
	 * Filter for 'post_thumbnail_id'
	 * Returns thumbnail id according to setting
	 * 
	 * @since 0.1.0 (r2)
	 * @see http://codex.wordpress.org/Function_Reference/get_post_thumbnail_id
	 * @uses get_post_type()
	 * @uses is_single()
	 * @uses wpdb
	 * @param int $id
	 * @param int $post_id
	 * @param string $key
	 * @return int
	 */
	function post_thumbnail_id( $id, $post_id, $key ) {	
		if ( eazyest_gallery()->post_type != get_post_type( $post_id ) || '_thumbnail_id' != $key )		
			return $id;
		
		if ( is_single() && ! defined( 'DOING_GALLERYFOLDERS' ) )
			return $id;
	
		global $wpdb;
		$option = eazyest_gallery()->get_option( 'folder_image' );
		// featured image is selected
		if ( 'featured_image' == $option ) {
			$id = eazyest_folderbase()->featured_image( $post_id );
		}
		// first image is selected
		if ( 'first_image' == $option ) {
			$id = eazyest_folderbase()->first_image( $post_id );
		}
		// random image is selected
		if ( 'random_image' == $option ) {
			$id = eazyest_folderbase()->random_images( $post_id, 1, eazyest_gallery()->get_option( 'random_subfolder' ) );
		}	
		if ( ! $id )
			$id = null;
		return $id;	
	}
	
	/**
	 * Eazyest_Frontend::post_thumbnail_size()
	 * Post thumbnail size is always 'thumbnail'
	 * Filter for 'post_thumbnail_size'
	 * 
	 * @since 0.1.0 (r2)
	 * @uses WP_Post
	 * @param string $size
	 * @return string
	 */
	function post_thumbnail_size( $size ) {
		global $post;
		if ( isset( $post ) && $post->post_type == eazyest_gallery()->post_type )
			$size = 'thumbnail';
		return $size;	
	}
	
	/**
	 * Eazyest_Frontend::post_thumbnail_attr()
	 * Returns an filterd array for post_thumbnail attributes
	 * <code>
	 * array(
	 *  'width'  => intval( get_option( "thumbnail_size_w" ) ),
	 *  'height' => intval( get_option( "thumbnail_size_h" ) ),
	 *  'src'    => $src, 
	 *  'alt'    => 'Folder Icon'
	 * );
	 * </code>
	 * Filter:
	 * 'eazyest_gallery_folder_thumbnail_attr'
	 * 
	 * 
	 * Applies filter for default folder icon url
	 * <code>'eazyest_gallery_folder_icon'</code> 
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @uses wp_get_attachment_image_src()
	 * @param integer $post_id
	 * @return array
	 */
	function post_thumbnail_attr( $post_id = 0 ) {
		global $post;		
		$post_id = 0 != $post_id ? $post_id : $post->ID;
		$thumbnail_id = $this->post_thumbnail_id( null, $post_id, '_thumbnail_id' );
		$option = eazyest_gallery()->get_option( 'folder_image' );
		$src = '';
		$icon = apply_filters( 'eazyest_gallery_folder_icon', eazyest_gallery()->plugin_url . 'frontend/images/folder-icon.png' );
		if ( 'none' != $option ) {
			if ( 'icon' == $option )
				$src = $icon;
			else {		
				if ( ! empty( $thumbnail_id ) ) {
					$wp_src = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
					$src = $wp_src[0];
				} else {
					$src = $icon;
				}
			}
		}
		$attr = array(
			'width'  => intval( get_option( "thumbnail_size_w" ) ),
			'height' => intval( get_option( "thumbnail_size_h" ) ),
			'src'    => $src,
			'alt'    => __( 'Folder Icon', 'eazyest-gallery' )
		);
		return apply_filters( 'eazyest_gallery_folder_thumbnail_attr', $attr, $post_id );
	}
	
	/**
	 * Eazyest_Frontend::post_thumbnail_html()
	 * Filter for 'post_thumbnail_html'
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post_type()
	 * @uses is_single()
	 * @param string $html
	 * @param integer $post_id
	 * @return
	 */
	function post_thumbnail_html( $html, $post_id ) {
		if ( eazyest_gallery()->post_type == get_post_type( $post_id ) ) {
			if ( ! is_single() || defined( 'DOING_GALLERYFOLDERS' ) ) {
				$attr = $this->post_thumbnail_attr( $post_id );
				$html = empty( $attr['src'] ) ? '' : sprintf( '<img width="%d" height="%d" src="%s" class="attachment-thumbnail wp-post-image folder-icon" alt="%s" />',
					$attr['width'],
					$attr['height'],
					$attr['src'],
					$attr['alt']
				);
			} else {
				$html = '';
			}
		}			
		return $html;
	}
	
	/**
	 * Eazyest_Frontend::folder_thumbnail_html()
	 * Returns folder icon html markup
	 * 
	 * @since 0.1.0 (r2)
	 * @uses WP_Post
	 * @param integer $post_id
	 * @return string
	 */
	function folder_thumbnail_html( $post_id = 0 ) {
		global $post;
		$post_id = 0 != $post_id ? $post_id : $post->ID;		
		$attr = $this->post_thumbnail_attr( $post_id );
		$html = empty( $attr['src'] ) ? '' : sprintf( '<img src="%s" class="attachment-thumbnail folder-icon" alt="%s" />',
			$attr['src'],
			$attr['alt']
		);
		return $html;
	}
	
	// icon view output functions	------------------------------------------------
	/**
	 * Eazyest_Frontend::folders_break()
	 * Inserts a clearing break after set number of columns
	 * 
	 * @since 0.1.0 (r2)
	 * @param integer $count
	 * @return string line break element &lt;br style="clear: both" /&gt;
	 */
	function folders_break( $count ) {
		$output = '';
		$columns = $this->folder_columns;
		if ( $columns > 0 && $count % $columns == 0 )
			$output .= '<br style="clear: both" />';
		return $output;		 
	}
	
	/**
	 * Eazyest_Frontend::gallery_style()
	 * Return a style element for a particular selector
	 * Applies filter forr style element
	 * <code>'eazyest_gallery_style'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @param string $selector
	 * @return string &lt;style&gt; element
	 */
	function gallery_style( $selector, $columns = 3 ) {
		$width = $columns > 0 ? floor( 100 / $columns ) : intval( get_option( 'thumbnail_size_w' ) ) + 20;
		$px = $columns > 0 ? '%' : 'px';
		$itemwidth = $width . $px;
		$float = is_rtl() ? 'right' : 'left';
		$itemheight = $columns == 0 ? intval( get_option( 'thumbnail_size_h' ) ) + 22 : 0;
			
		$style = "
			<style type='text/css'>
				#{$selector} {
					margin: auto;
				}
				#{$selector} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth};
					min-height: {$itemheight}px;
				}
				#{$selector} .gallery-icon {
					max-width: 100%;
				}
				#{$selector} .gallery-icon img {
					max-width: 90%;
				}	
				#{$selector} .gallery-caption {
					margin-left: 0;
				}
				#{$selector} .gallery-caption p {
					margin-bottom: 0;
				}
			</style>";
			
		return apply_filters( 'eazyest_gallery_style', $style );			
	}
	
	/**
	 * Eazyest_Frontend::gallery_class()
	 * Returns a string to go in class="" atrtribute for gallery.
	 * Applies a filter for an array to add class names
	 * <code>'eazyest_gallery_classes'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @param string $type if 'archive' gallery id="gallery-0"
	 * @return string space separated class names
	 */
	function gallery_class( $type ) {
		if ( 'archive' == $type )  {
			$id = 0;
		} else {
			$post = get_post();
			$id = $post->ID;
		}	
		 
		$classes = array( 'eazyest-gallery', 'gallery' );
		$classes[] = "gallery-{$id}";
		
		if ( 'archive' == $type ) {
			if ( $columns = $this->folder_columns )
				$classes[] = "gallery-columns-{$columns}";
			if ( $id )
				$classes[] = "folder-{$id}";	
			$classes[] = 'folders';			
			$classes[] = 'gallery-size-thumbnail';		
		}
		$classes = apply_filters( 'eazyest_gallery_classes', $classes, $type );
		return implode( ' ', $classes ); 		
	}
	
	/**
	 * Eazyest_Frontend::itemtag()
	 * Returns escaped item tag for gallery item element
	 * 
	 * @since 0.1.0 (r2)
	 * @uses tag_escape()
	 * @return string
	 */
	function itemtag() {
		return tag_escape( $this->itemtag );
	}
	
	/**
	 * Eazyest_Frontend::icontag()
	 * Returns escaped icon tag for gallery item element
	 * 
	 * @since 0.1.0 (r2)
	 * @uses tag_escape()
	 * @return string
	 */
	function icontag() {
		return tag_escape( $this->icontag );
	}
	
	/**
	 * Eazyest_Frontend::captiontag()
	 * Returns escaped caption tag for gallery item element
	 * 
	 * @since 0.1.0 (r2)
	 * @uses tag_escape()
	 * @return string
	 */
	function captiontag() {
		return tag_escape( $this->captiontag );
	}
	
	/**
	 * Eazyest_Frontend::folder_icon_caption()
	 * Echo html markup for a folder icon caption
	 * Does action to echo content after folder icon.
	 * <code>'eazyest_gallery_after_folder_icon_caption'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_the_title()
	 * @uses do_action()
	 * @param integer $post_id
	 * @return void
	 */
	function folder_icon_caption( $post_id = 0 ) {
		global $post;	
		if ( $post_id == 0 ) {
			$folder = $post;
		} else {
			$folder = get_post( $post_id );
		}
		$title = get_the_title( $folder->ID );
		$tag = $this->captiontag();
		?>
	 	<<?php echo $tag; ?> class="wp-caption-text gallery-caption folder-caption">
	 		<span class="folder-title"><?php echo $title; ?></span><br/>
	 		<?php do_action( 'eazyest_gallery_after_folder_icon_caption', $post_id ); ?>
	  </<?php echo $tag; ?>>
	  <?php
	}
	
	/**
	 * Eazyest_Frontend::folder_attachments_count()
	 * Echo html markup for folder attchments count
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_post()
	 * @uses wpdb
	 * @uses WP_Post
	 * 
	 * @param integer $post_id
	 * @return void
	 */
	function folder_attachments_count( $post_id = 0 ) {
		$option = eazyest_gallery()->get_option( 'count_subfolders' );
		
		// bail if no attachment count
		if ( 'nothing' == $option )
			return;
			
		global $post;
		if ( $post_id == 0 ) {
			$folder = $post;
		} else {
			$folder = get_post( $post_id );
		}
		global $wpdb;
		if ( in_array( $option, array( 'separate', 'none' ) ) ) {
			$post_ids = "= $folder->ID";
		} else {
			$children = eazyest_folderbase()->get_folder_children( $folder->ID );
			if ( ! empty( $children ) ) {
				$children[] = $folder->ID;
				$childlist = implode( ',', $children );
				$post_ids = "IN ($childlist)";
			} else {
				$post_ids = "= $folder->ID";
			} 			
		}
		$foldercount = $wpdb->query( "
			SELECT ID 
			FROM $wpdb->posts 			
			WHERE post_parent $post_ids  
			AND post_type = 'attachment' 
			AND post_status IN ('inherit', 'publish');"
		); 
		if ( 'separate' == $option ) {		
			$children_count = 0;
			$children = eazyest_folderbase()->get_folder_children( $folder->ID );
			if ( ! empty( $children ) ) {
				$childlist = implode( ',', $children );
				$post_ids = "IN ($childlist)";
				$children_count = $wpdb->query( "
					SELECT ID 
					FROM $wpdb->posts 			
					WHERE post_parent $post_ids  
					AND post_type = 'attachment' 
					AND post_status IN ('inherit', 'publish');"
				);
			}  			
		}
		/* translators: %1s = number; %2s = images (text) */
		$images_string   = sprintf( __( '%1s %2s', 'eazyest-gallery' ), $foldercount, eazyest_gallery()->get_option( 'listed_as' ) );
		if ( 'separate' == $option )
			/* translators: number of images in subfolders */
			$children_string = sprintf( __( '%s in subfolders', 'eazyest-gallery' ), $children_count );
		?>
		<span class="folder-count"><?php echo $images_string ?></span><br />
		<?php	if ( 'separate' == $option ) : ?>
		<span class="subfolder-count"><?php echo $children_string; ?></span>
		<?php endif; 	
	}
	
	/**
	 * Eazyest_Frontend::breadcrumb()
	 * Echo breadcrumb trail html markup.
	 * Applies filters for the separator char (&rsaquo;) and for the breadcrumb items (array).
	 * $crumbs[0] = blog home page
	 * $crumbs[1] = gallerfolder archive page 
	 * <code>'eazyest_gallery_breadcrumb_separator'</code>
	 * <code>'eazyest_gallery_breadcrumb_items'</code>
	 * 
	 * @param integer $post_id
	 * @return void
	 */
	function breadcrumb( $post_id = 0 ) {
		
		if ( defined( 'LAZYEST_GALLERY_SHORTCODE' ) && ! apply_filters( 'eazyest_gallery_shortcode_breadcrumb', false ) )
			return '';
			
		if ( $post_id == 0 )
			$post_id = get_the_ID();
		$ancestors = array_reverse( (array) get_post_ancestors( $post_id ) );
		$crumbs = array();
		
		$crumbs[] = '<a href="' . trailingslashit( home_url() ) . '" class="eazyest-galley-breadcrumb-home">' . __( 'Home', 'eazyest-gallery' ) . '</a>';
		
		$root_url  = get_post_type_archive_link( eazyest_gallery()->post_type );
		$root_text = eazyest_gallery()->gallery_title();
		$crumbs[] = '<a href="' . $root_url . '" class="eazyest-gallery-breadcrumb-root">' . $root_text . '</a>';
		
		if ( count( $ancestors ) ) {
			foreach( $ancestors as $folder_id ) {
				$crumbs[] = '<a href="' . get_permalink( $folder_id ) . '" class="eazyest-gallery-breadcrumb-item">' . get_the_title( $folder_id ) . '</a>';
			}
		}
		$crumbs[] = get_the_title( $post_id );
		
		/* translators: breadcrumb trail separator */
		$sep = __( '&rsaquo;', 'eazyest_gallery' );
		$separator = apply_filters( 'eazyest_gallery_breadcrumb_separator', " $sep " );
		$crumbs    = apply_filters( 'eazyest_gallery_breadcrumb_items',     $crumbs  );
		$trail     = implode( $separator, $crumbs );
		?>
		<div class="eazyest-gallery-breadcrumb">
			<p><?php echo $trail; ?></p>
		</div>
		<?php
	}
	
	/**
	 * Eazyest_Frontend::post_gallery()
	 * Same functionality al;s WordPress gallery_code but with a filter for captions
	 * <code>'eazyest_gallery_thumbsview_caption'</code>
	 * 
	 * @see http://codex.wordpress.org/Gallery_Shortcode
	 * @see gallery_shortcode() in wp-includes/media.php
	 * @used-by Eazyest_Frontend::thumbnails()
	 * 
	 * @since 0.1.0 (r2)
	 * @param array $attr
	 * @return html markup for gallery
	 */
	function post_gallery( $attr ) {
		$post = get_post();
	
		static $instance = 0;
		$instance++;
	
		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) )
				$attr['orderby'] = 'post__in';
			$attr['include'] = $attr['ids'];
		}
	
		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}
	
		extract(shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => ''
		), $attr));
	
		$id = intval($id);
		if ( 'RAND' == $order )
			$orderby = 'none';
	
		if ( !empty($include) ) {
			$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	
			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty($exclude) ) {
			$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		} else {
			$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		}
	
		if ( empty($attachments) )
			return '';
	
		$itemtag = tag_escape($itemtag);
		$captiontag = tag_escape($captiontag);
		$columns = intval($columns);
		$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
		$float = is_rtl() ? 'right' : 'left';
	
		$selector = "gallery-{$instance}";
	
		$gallery_style = $gallery_div = '';
		if ( apply_filters( 'use_default_gallery_style', true ) )
			$gallery_style = "
			<style type='text/css'>
				#{$selector} {
					margin: auto;
				}
				#{$selector} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth}%;
				}
				#{$selector} img {
					border: 2px solid #cfcfcf;
				}
				#{$selector} .gallery-caption {
					margin-left: 0;
				}
			</style>
			<!-- see gallery_shortcode() in wp-includes/media.php -->";
		$size_class = sanitize_html_class( $size );
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
		$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );
	
		$i = 0;
		foreach ( $attachments as $id => $attachment ) {
			$attachment_caption = apply_filters( 'eazyest_gallery_thumbsview_caption', wptexturize($attachment->post_excerpt) );
			$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);
	
			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
				<{$icontag} class='gallery-icon'>
					$link
				</{$icontag}>";
			if ( $captiontag && trim($attachment->post_excerpt) ) {
				$output .= "
					<{$captiontag} class='wp-caption-text gallery-caption'>
					" . $attachment_caption . "
					</{$captiontag}>";
			}
			$output .= "</{$itemtag}>";
			if ( $columns > 0 && ++$i % $columns == 0 )
				$output .= '<br style="clear: both" />';
		}
	
		$output .= "
				<br style='clear: both;' />
			</div>\n";
	
		return $output;
	}
	
	/**
	 * Eazyest_Frontend::thumbnails()
	 * Echo or Return folder thumbnails gallery
	 * If you don't have extra fields, the function puts out a WordPress <code>[gallery]</code> tag.
	 * Plugins that replace this shortcode html, like jetpack will also override the galleryfolder thumbnails.
	 * This allows easier adaptation of galleries
	 * 
	 * If on an archive page and one of <code>[eazyest_folder]</code> or <code>[lg-folder]</code> shortcodes is used, the function echoes a folder icon    
	 * 
	 * @since 0.1.0 (r2)
	 * @uses get_the_ID()
	 * @uses is_single()
	 * @uses do_shortcode()
	 * @param integer $post_id
	 * @param bool $echo echo or return the output default=true
	 * @return void or string
	 */
	function thumbnails( $post_id = 0, $echo = true ) {
		if ( $post_id == 0 )
			$post_id = get_the_ID();
			
		list( $orderby, $order ) = explode( '-', eazyest_gallery()->get_option( 'sort_thumbnails' ) );
		$orderby = $orderby == 'post_id' ? 'ID' : $orderby;
		
		$columns = $this->thumbnail_columns = eazyest_gallery()->get_option( 'thumbs_columns' );
		
		$itemtag    = $this->itemtag();
		$icontag    = $this->icontag();
		$captiontag = $this->captiontag();
		
		$ids = '';
		// TODO: add code when extra fields or description in thumbnail view
		if ( ! is_single() ) {
			$selector = lzg_selector( true, false );
			$html = $this->gallery_style( $selector, $columns );
			$html .= '<div id="' . lzg_selector( false, false ) . '" class="gallery eazyest-gallery gallery-size-thumbnail"><' . 
				$this->itemtag() . ' class="gallery-item"><' . 
				$this->icontag . ' class="gallery-icon">' . 
				$this->post_thumbnail_html( '', $post_id ) . '</' . 
				$this->icontag() . '></' . 
				$this->itemtag() . '><br style="clear:both"</div>';
			if ( $echo ) {
				echo $html;
				return;
			}	else
				return $html;
		} else {
			if ( eazyest_gallery()->get_option( 'thumb_description') || eazyest_extra_fields()->enabled() )
				// use a gallery with filtered captions if thumb_description or eazyest_fields enabled 
				add_filter( 'post_gallery', array( $this, 'post_gallery' ), 2000 ); // priority 2000 to override other plugins
				
			$gallery = do_shortcode( "[gallery id='$post_id' order='$order' orderby='$orderby' columns='$columns' itemtag='$itemtag' icontag='$icontag' captiontag='$captiontag']" );
				
			if ( eazyest_gallery()->get_option( 'thumb_description') || eazyest_extra_fields()->enabled() )
				// remove filter for other shortcodes in post
				remove_filter( 'post_gallery', array( $this, 'post_gallery' ), 2000 );
						
			if ( $echo )
				echo $gallery;
			else
				return $gallery;
		}		
	}
	
	/**
	 * Eazyest_Frontend::subfolders()
	 * Echo subfolders listing for galleryfolder
	 * In earlier versions, subfolders did not show in <code>[lg_folder]</code> shortcodes.
	 * This is still the defult behavior, but it can be overridden by filter (return bool true)
	 * <code>'eazyest_gallery_shortcode_subfolders'</code>
	 * 
	 * Actions used for plugins to <strong>echo</strong> content:
	 * <code>'eazyest_gallery_before_folder_icon'</code>
	 * <code>'eazyest_gallery_after_folder_icon'</code>
	 * <code>'eazyest_gallery_end_of_subfolders'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses apply_filters()
	 * @uses get_the_ID()
	 * @uses WP_Query
	 * @uses do_action()
	 * @uses wp_reset_query()
	 * @uses wp_reset_postdata()
	 * @param integer $post_id
	 * @return void
	 */
	function subfolders( $post_id = 0 ) {		
		if ( defined( 'LAZYEST_GALLERY_SHORTCODE' ) && ! apply_filters( 'eazyest_gallery_shortcode_subfolders', false ) )
			return false;
			
		if ( $post_id == 0 )
			$post_id = get_the_ID();
		
		$global_post = $GLOBALS['post'];
		
		$args = array(
			'post_type'   => eazyest_gallery()->post_type,
			'post_parent' => $post_id
		);
		
		$query = new WP_Query( $args );
		
		if ( $query->have_posts() ) :
		if ( ! defined( 'DOING_GALLERYFOLDERS' ) )
			define( 'DOING_GALLERYFOLDERS', true );
		?>
			<?php	echo $this->gallery_style( lzg_selector( true, false ), eazyest_gallery()->get_option( 'folders_columns' ) ); ?>
			<div id="<?php lzg_selector( false ) ?>" class="<?php lzg_gallery_class( 'archive' ); ?>">
				<h3 class="subfolders"><?php _e( 'Subfolders', 'eazyest-gallery' ); ?></h3>
					<?php $i = 0; ?>
					<?php  /* Start the Loop */ 
								while ( $query->have_posts() ) : $query->the_post(); ?>								
									<<?php lzg_itemtag(); ?> class="gallery-item folder-item">
							
									<?php do_action( 'eazyest_gallery_before_folder_icon' ); ?>
								
									<<?php lzg_icontag(); ?> class="gallery-icon folder-icon">
										<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'View folder &#8220;%s&#8221;', 'eazyest-gallery' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
											<?php lzg_folder_thumbnail(); ?> 
										</a>
									</<?php lzg_icontag(); ?>>
								
								<?php do_action( 'eazyest_gallery_after_folder_icon' ); ?>
								
							</<?php lzg_itemtag(); ?>>
						<?php lzg_folders_break( ++$i ); ?>	
					<?php endwhile; ?>
					<br style="clear: both;"/>
					<?php do_action( 'eazyest_gallery_end_of_subfolders' ); ?>			
			</div>
		<?php
		endif;
		
		wp_reset_query();
		wp_reset_postdata();
		$GLOBALS['post'] = $global_post;
	}
	
	/**
	 * Eazyest_Frontend::attachment_link()
	 * Filter for <code>'attachment_link'</code>
	 * Changes link according to 'on_thumb_click' or 'on_slide_click' settings.
	 * Applies filters to override on_click behavior with the same parameters as the function:
	 * <code>'eazyest_gallery_on_thumb_click_link'</code>
	 * <code>'eazyest_gallery_on_slide_click_link'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses is_admin()
	 * @uses get_post()
	 * @uses WP_Post
	 * @uses get_post_type()
	 * @uses is_singular()
	 * @uses wp_get_attachment_image_src()
	 * @uses apply_filters()
	 * @param string $link
	 * @param integer $post_id
	 * @return string
	 */
	function attachment_link( $link, $post_id ) {
		// bail if admin
		if ( is_admin() )
			return $link;
			
		$attachment = get_post( $post_id );
		$post_type = eazyest_gallery()->post_type;	
		// bail if parent is not a folder
		if ( $post_type != get_post_type( $attachment->post_parent ) )
			return $link;
		// displaying a thumbnail click link according to settings
		if ( is_singular( 'attachment' ) )		
			$option = eazyest_gallery()->get_option( 'on_slide_click' );
		else
			$option = eazyest_gallery()->get_option( 'on_thumb_click' );
		switch(  $option ) {
			case 'nothing' :
				$link = 'javascript:void(0)';
				break;
			case 'medium' :
			case 'large'  :	
			case 'full'   :
				$wp_src = wp_get_attachment_image_src( $attachment->ID, $option );
				$link   = $wp_src[0];
				break;				
		}
		if ( is_singular( 'attachment' ) )
			$link = apply_filters( 'eazyest_gallery_on_slide_click_link', $link, $option );
		else
			$link = apply_filters( 'eazyest_gallery_on_thumb_click_link', $link, $option );	
		return $link;
	}
	
	/**
	 * Eazyest_Frontend::add_attr_to_link()
	 * Adds attributes class="" and rel="" to attachment link to accomodate popup plugins.
	 * Plugins may filter the class names array and rel items array:
	 * <code>'eazyest_gallery_on_attachment_click_class'</code> 
	 * <code>'eazyest_gallery_on_thumb_click_class'</code>
	 * <code>'eazyest_gallery_on_attachment_click_rel'</code>
	 * <code>'eazyest_gallery_on_thumb_click_rel'</code>
	 * 
	 * @since 0.1.0 (r2)
	 * @uses is_admin()
	 * @uses get_post()
	 * @uses WP_Post
	 * @uses get_post_type()
	 * @uses is_singular()
	 * @uses is_attachment()
	 * @uses apply_filters()
	 * @param string $link
	 * @param integer $post_id
	 * @return string
	 */
	function add_attr_to_link( $link, $post_id ) {
		// bail if admin
		if ( is_admin() )
			return $link;
			
		$attachment = get_post( $post_id );
		$post_type = eazyest_gallery()->post_type;
		// bail if parent is not a folder	
		if ( $post_type != get_post_type( $attachment->post_parent ) )
			return $link;		
				
		
		$class_attr = $rel_attr = array();
		$option = '';
		if ( is_singular( $post_type ) || is_attachment() || defined( 'LAZYEST_GALLERY_SHORTCODE' ) ) {
			$option = ( is_singular( 'attachment' ) ) ? eazyest_gallery()->get_option( 'on_slide_click' ) : eazyest_gallery()->get_option( 'on_thumb_click' );
			if ( ( is_singular( 'attachment' ) ) && 'nothing' != $option ) {
				$class_attr = apply_filters( 'eazyest_gallery_on_attachment_click_class', $class_attr );
				$rel_attr   = apply_filters( 'eazyest_gallery_on_attachment_click_rel',   $rel_attr   );
				$popup = eazyest_gallery()->get_option( 'slide_popup' );
			} else	if ( ! in_array( $option, array( 'attachment', 'nothing' ) ) ) {
				$class_attr  = apply_filters( 'eazyest_gallery_on_thumb_click_class', $class_attr );
				$rel_attr    = apply_filters( 'eazyest_gallery_on_thumb_click_rel',   $rel_attr   );
				$popup = eazyest_gallery()->get_option( 'thumb_popup' );
			}
			if( ! empty( $popup ) ) {
				$rel = "{$popup}[gallery-{$attachment->post_parent}]";
				switch( $popup ) {
					case 'thickbox' : 
						$class_attr[] = "thickbox";
						break;
					case 'fancybox' :
						$class_attr[] = "fancybox";
						break;
					case 'shadowbox' :
						$class_attr[] = "shadowbox";
						$rel .= ";player=img;";
						break;	
					case 'none' :
						$rel = '';
						break;	
				}
				if ( ! empty( $rel ) )
					$rel_attr[] = $rel;
			}
		} 	
		if ( count( $rel_attr ) ){			
			$rel_pattern   = "<a rel='" . implode( ' ', $rel_attr ) . "' ";
			$link = str_replace( '<a ', $rel_pattern, $link );
		}		
		if ( count( $class_attr ) ){
			$class_pattern = "<a class='" . implode( ' ', $class_attr ) . "' ";
			$link = str_replace( '<a ', $class_pattern, $link );
		}	 
				
		return $link;
	}	

} // class Eazyest_Frontend;

/**
 * eazyest_frontend()
 * 
 * @since 0.1.0 (r2)
 * @return object Eazyest_Frontend
 */
function eazyest_frontend() {
	return Eazyest_Frontend::instance();
}