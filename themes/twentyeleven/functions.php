<?php
/**
 * Twenty Eleven compatible functions and definitions

 * @package Eazyest Gallery
 * @subpackage Twenty_Eleven
 * @since Eazyest Gallery 2.0.0 (r440)
 */

/**
 */
/**
 * eazyestgallery_content_nav()
 * Display navigation to next/previous pages when applicable
 * 
 * @since 0.1.0 (r2)
 * @param mixed $html_id
 * @return void
 */
function eazyestgallery_content_nav( $html_id ) {
	global $wp_query;
	$older_posts_link_des = __( '<span class="meta-nav">&larr;</span> Older folders',         'eazyest-gallery' );
	$newer_posts_link_des = __( 'Newer folders <span class="meta-nav">&rarr;</span>',         'eazyest-gallery' );	
	$newer_posts_link_asc = __( '<span class="meta-nav">&larr;</span> Newer folders',         'eazyest-gallery' );
	$older_posts_link_asc = __( 'Older folders <span class="meta-nav">&rarr;</span>',         'eazyest-gallery' );
	$next_page_link       = __( '<span class="meta-nav">&larr;</span> Next folders page',     'eazyest-gallery' );
	$previous_page_link   = __( 'Previous folders page <span class="meta-nav">&rarr;</span>', 'eazyest-gallery' );
	$option = eazyest_gallery()->sort_by();
	switch( $option ) {
		case 'post_date-DESC' :
			$next_posts_link     = $older_posts_link_des;
			$previous_posts_link = $newer_posts_link_des;
			break;
		case 'post_date-ASC' :
			$next_posts_link     = $newer_posts_link_asc;
			$previous_posts_link = $older_posts_link_asc;
			break;
		default:	
			$next_posts_link     = $next_page_link;
			$previous_posts_link = $previous_page_link;
	}
	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo esc_attr( $html_id ); ?>">
			<h3 class="assistive-text"><?php _e( 'Folder navigation', 'eazyest-gallery' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( $next_posts_link ); ?></div>
			<div class="nav-next"><?php previous_posts_link( $previous_posts_link ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}