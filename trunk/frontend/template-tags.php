<?php
/**
 * Template tags to use in your WordPress theme templates or plugins
 * Notice: the function prefix has changed from <code>lg_</code> to <code>ezg_</code> since Eazyest Gallery 2.0.0
 * 
 * @package Eazyest Gallery
 * @subpackage Frontend/Template Tags
 * @since 0.1.0 (r2)
 */ 
  
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;  


// template functions ---------------------------------------------------------
/**
 * ezg_get_template_part()
 * Wrap for Eazyest_Frontend::get_template_part()
 * @see EazyestFrontend::get_template_part()
 * 
 * @since 0.1.0 (r2)
 * @param string $slug
 * @param string $name
 * @return void
 */
function ezg_get_template_part( $slug, $name = null ) {
	eazyest_frontend()->get_template_part( $slug, $name );
}

// gallery output functions ---------------------------------------------------
/**
 * ezg_gallery_title()
 * Wrap for Eazyest_Gallery::gallery_title()
 * @see Eazyest_Gallery::gallery_title()
 * 
 * @since 0.1.0 (r2)
 * @return void
 */
function ezg_gallery_title() {
	echo eazyest_gallery()->gallery_title();
}

/**
 * ezg_selector()
 * Returns selector for gallery element
 * 
 * @since 0.1.0 (r2)
 * @param bool $increase wether to increase the id number
 * @param bool $echo wether to echo the selector or return
 * @return string eazyest-gallery-$selector
 */
function ezg_selector( $increase= true, $echo = true ) {
	static $instance = 0;
	if ( $increase )
		$instance++;
	$selector = "eazyest-gallery-{$instance}";
	
	if ( $echo )
		echo $selector;
	else	
		return $selector;
}

/**
 * ezg_gallery_class()
 * Wrap for Eazyest_Frontend::gallery_class()
 * @see Eazyest_Frontend::gallery_class()
 * 
 * @since 0.1.0 (r2)
 * @param string $type
 * @return void
 */
function ezg_gallery_class( $type = 'archive' ) {
	echo eazyest_frontend()->gallery_class( $type );
}

/**
 * ezg_gallery_style()
 * Wrap for Eazyest_Frontend::gallery_style
 * Filter to override including style element:
 * <code>'use_default_gallery_style'</code> (bool)
 * 
 * @since 0.1.0 (r2)
 * @uses apply_filters()
 * @return string style element
 */
function ezg_gallery_style() {
	// bail if themes do not use default style
	if ( apply_filters( 'use_default_gallery_style', false ) )
		return;
		
	$selector = ezg_selector( true, false );
	echo eazyest_frontend()->gallery_style( $selector, eazyest_gallery()->get_option( 'folders_columns' ) );
}

/**
 * ezg_itemtag()
 * Wrap for Eazyest_Frontend::itemtag()
 * @see Eazyest_Frontend::itemtag()
 * 
 * @since 0.1.0 (r2)
 * @return void
 */
function ezg_itemtag() {
	echo eazyest_frontend()->itemtag();
}

/**
 * ezg_itemtag()
 * Wrap for Eazyest_Frontend::icontag()
 * @see Eazyest_Frontend::icontag()
 * 
 * @since 0.1.0 (r2)
 * @return void
 */
function ezg_icontag() {
	echo eazyest_frontend()->icontag();
}

/**
 * ezg_folders_break()
 * Wrap for Eazyest_Frontend::folders_break()
 * @see  Eazyest_Frontend::folders_break()
 * 
 * @since 0.1.0 (r2) 
 * @param int $i counter
 * @return void
 */
function ezg_folders_break( $i ) {
	echo eazyest_frontend()->folders_break( $i );
}

/**
 * ezg_folder_thumbnail()
 * Wrap for Eazyest_Frontend::folder_thumbnail_html()
 * @see Eazyest_Frontend::folder_thumbnail_html()
 * 
 * @since 0.1.0 (r2)
 * @param integer $post_id
 * @return void
 */
function ezg_folder_thumbnail( $post_id = 0 ) {
	echo eazyest_frontend()->folder_thumbnail_html( $post_id );
} 

/**
 * ezg_folder_thumbnail()
 * Wrap for Eazyest_Frontend::folder_icon_caption()
 * @see Eazyest_Frontend::folder_icon_caption()
 * 
 * @since 0.1.0 (r2)
 * @param integer $post_id
 * @return void
 */
function ezg_folder_icon_caption( $post_id = 0 ) {
	eazyest_frontend()->folder_icon_caption( $post_id );
}

/**
 * ezg_folder_thumbnail()
 * Wrap for Eazyest_Frontend::folder_attachments_count()
 * @see Eazyest_Frontend::folder_attachments_count()
 * 
 * @since 0.1.0 (r2)
 * @param integer $post_id
 * @return void
 */
function ezg_folder_attachments_count( $post_id = 0 ) {
	eazyest_frontend()->folder_attachments_count( $post_id );
}

/**
 * ezg_folder_thumbnail()
 * Wrap for Eazyest_Frontend::breadcrumb()
 * @see Eazyest_Frontend::breadcrumb()
 * 
 * @since 0.1.0 (r2)
 * @param integer $post_id
 * @return void
 */
function ezg_breadcrumb( $post_id = 0 ) {
	eazyest_frontend()->breadcrumb( $post_id );
}

/**
 * ezg_folder_thumbnail()
 * Wrap for Eazyest_Frontend::thumbnails()
 * @see Eazyest_Frontend::thumbnails()
 * 
 * @since 0.1.0 (r2)
 * @param integer $post_id
 * @return void
 */
function ezg_thumbnails( $post_id = 0 ) {
	eazyest_frontend()->thumbnails( $post_id );
}

/**
 * ezg_folder_thumbnail()
 * Wrap for Eazyest_Frontend::subfolders()
 * @see Eazyest_Frontend::subfolders()
 * 
 * @since 0.1.0 (r2)
 * @param integer $post_id
 * @return void
 */
function ezg_subfolders( $post_id = 0 ) {
	eazyest_frontend()->subfolders( $post_id );
}

// template tags --------------------------------------------------------------

/**
 * ezg_list_folders()
 * Output or return an unordered list of galleryfolder posts
 * 
 * @since 0.1.0 (r2)
 * @uses wp_list_pages() to produce the listing
 * @param string $title
 * @param string $echo if not 'echo', markup is returned
 * @return void | string unordered list
 */
function ezg_list_folders( $title = '', $echo = 'echo' ) {
	list( $sort_column, $sort_order ) = explode( '-', eazyest_gallery()->sort_by() );
	$output = '<ul>' . wp_list_pages( array( 
		'post_type' => eazyest_gallery()->post_type, 
		'title_li' => $title, 
		'sort_column' => $sort_column, 
		'sort_order' => $sort_order, 
		'post_status' => 'publish', 
		'echo' => 0 ) 
		) . '</ul>';
	
	if ( 'echo' == $echo )
		echo $output;
	else
		return $output;		
}

/**
 * ezg_random_images()
 * Output a gallery with one or more random images from a folder with or without subfolders
 * 
 * @since 0.1.0 (r2)
 * @uses absint()
 * @uses esc_html()
 * @uses get_post
 * @uses WP_Post
 * @uses wp_get_attachment_image_src()
 * @uses wp_get_attachment_link
 * @param array $attr
 * <code>array(
 * 	'folder'     => '',
 *  'id'         => 0,
 *	'number'     => 1,
 *  'columns'    => eazyest_gallery option  'thumbs_columns',
 * 	'title'      => '',
 * 	'subfolders' => 0,
 * 	'size'       => 'thumbnail'
 * 	)</code>
 * @return void
 */
function ezg_random_images( $attr = array() ) {
	extract( shortcode_atts( array(
	  'folder'     => '',
		'id'         => 0,
		'number'     => 1,
		'columns'    => eazyest_gallery()->get_option( 'thumbs_columns' ),
		'title'      => '',
		'subfolders' => eazyest_gallery()->get_option( 'random_subfolder' ),
		'size'       => 'thumbnail'
	), $attr ) );
	$post_number = max( 1, absint( $number ) );	
	$subfolders = $subfolders ? true : false;
	$columns = absint( $columns );	
			
	if ( isset( $folder ) && ! isset( $id ) )
		$id = eazyest_folderbase()->get_folder_by_string( $folder );
	if ( $id ) {
		$results = eazyest_folderbase()->random_images( $post_id, $number, $subfolders );
		if ( ! empty( $results ) ) :
			?>
			<?php if ( ! empty( $title ) ) : ?>
			<h3><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>
			<?php ezg_gallery_style() ?>
			<div id="<?php ezg_selector( false, true ) ?>" class="gallery eazyest-gallery gallery-size-<?php echo $size; ?>">
				<?php 
					$i = 0;
					foreach( $results as $attachment_id ) : 
						$attachment = get_post( $attachment_id );
						$wp_sr      = wp_get_attachment_image_src( $attachment->ID, $size );
						$link       = eazyest_frontend()->add_attr_to_link( wp_get_attachment_link( $attachment->ID, $size ), $attachment->ID ); 
				?>
				<<?php ezg_itemtag(); ?> class="gallery-item">							
					<<?php ezg_icontag(); ?> class="gallery-icon">
						<?php echo $link ?>
					</<?php ezg_icontag(); ?>>
				</<?php ezg_itemtag(); ?>>
				<?php ezg_folders_break( ++$i ); ?>
				<?php	endforeach; ?>
				<br style="clear:both;"/>
			</div>
		<?php endif;
	}	
}

/**
 * ezg_recent_images()
 * Output a WordPress gallery (shortcode) of the latest images in the gallery
 * 
 * @since 0.1.0 (r2)
 * @uses shortcode_atts
 * @uses get_the_ID()
 * @param array $attr
 * array(
 *  'folder'     => '',
 *	'id'         => 0,
 *	'number'     => 1,
 *	'columns'    => eazyest_gallery option 'thumbs_columns',
 *	'title'      => '',
 *	'subfolders' => 0,
 *	'size'       => 'thumbnail'
 * )
 * @return void
 */
function ezg_recent_images( $attr = array() ) {
	extract( shortcode_atts( array(
	  'folder'     => '',
		'id'         => 0,
		'number'     => 1,
		'columns'    => eazyest_gallery()->get_option( 'thumbs_columns' ),
		'title'      => '',
		'subfolders' => 0,
		'size'       => 'thumbnail'
	), $attr ) );
	$subfolders = $subfolders ? true : false;
	if ( isset( $folder ) && ! isset( $id ) )
		$id = eazyest_folderbase()->get_folder_by_string( $folder );
		
	if ( ! $id )
		$subfolders = true;
	
	if ( $id == 0 )
		$id = get_the_ID();
			
	$ids = implode( ', ', eazyest_folderbase()->recent_images( $id, $number, $subfolders ) );

	$itemtag    = ezg_itemtag();
	$icontag    = ezg_icontag();
	$captiontag = ezg_captiontag();
	?>
	<?php if ( ! empty( $title ) ) : ?>
	<h3><?php echo esc_html( $title ); ?></h3>
	<?php endif; ?>	
	<?php
	echo do_shortcode( "[gallery ids='$ids' columns='$columns' itemtag='$itemtag' icontag='$icontag' captiontag='$captiontag' size='$size']" );
}

/**
 * lgz_recent_folders()
 * Display a gallery of folder icons of the latest folders
 * 
 * @since 0.1.0 (r2)
 * @uses shortcode_atts()
 * @uses WP_Query
 * @uses do_action for <code>'eazyest_gallery_before_folder_icon'</code> and <code>'eazyest_gallery_after_folder_icon'</code>
 * @uses wp_reset_query()
 * @uses wp_reset_postdata()
 * @global $GLOBALS['post']
 * @param mixed $attr
 * @return void
 */
function lgz_recent_folders( $attr = array() ) {
	extract( shortcode_atts( array(
		'number'     => 1,
		'columns'    => eazyest_gallery()->get_option( 'thumbs_columns' ),
		'title'      => '', 
	), $attr ) );
	
	$args = array(
		'post_type'   => eazyest_gallery()->post_type,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => $number
	);
	eazyest_frontend()->folder_columns = $columns;
	
	$global_post = $GLOBALS['post'];
	$query = new WP_Query( $args );	
	?>
	<?php while( $query->have_posts() ) : $query->the_post(); ?>							
		<<?php ezg_itemtag(); ?> class="gallery-item folder-item">
	
		<?php do_action( 'eazyest_gallery_before_folder_icon' ); ?>
	
		<<?php ezg_icontag(); ?> class="gallery-icon folder-icon">
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'View folder &#8220;%s&#8221;', 'eazyest-gallery' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
				<?php ezg_folder_thumbnail(); ?> 
			</a>
		</<?php ezg_icontag(); ?>>
	
		<?php do_action( 'eazyest_gallery_after_folder_icon' ); ?>
	
		</<?php ezg_itemtag(); ?>>
		<?php ezg_folders_break( ++$i ); ?>	
	<?php endwhile; ?>
	<?php
		
	wp_reset_query();
	wp_reset_postdata();
	$GLOBALS['post'] = $global_post;
}

/**
 * Include support for deprecated tags.
 * 
 * @since 0.1.0 (r2)
 */
include( eazyest_gallery()->plugin_dir . '/frontend/deprecated-tags.php' );