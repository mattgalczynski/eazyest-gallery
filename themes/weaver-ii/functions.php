<?php
/** 
 * The weaver-ii themes have a breadcrumb trail.
 * The Eazyest gallery breadcrumb trail is hidden by default
 * If you want to show the Eazyest Gallery breadcrumb trail, remove line 11  
 */ 
function remove_eazyest_gallery_breadcrumb() {
	remove_action('eazyest_gallery_before_attachment',     'ezg_breadcrumb', 5);
  remove_action('eazyest_gallery_before_folder_content', 'ezg_breadcrumb', 5); 
}
add_action( 'eazyest_gallery_ready', 'remove_eazyest_gallery_breadcrumb', 1 );

function eazyest_gallery_content_nav( $nav_id , $from_search=false) {
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

    if ( $wp_query->max_num_pages > 1 ) {
?>
	<nav id="<?php echo $nav_id; ?>">
	    <h3 class="assistive-text"><?php echo __( 'Post navigation','eazyest-gallery'); ?></h3>
<?php
	if (weaverii_getopt('wii_nav_style') == 'prev_next') {
?>
	    <div class="nav-previous"><?php next_posts_link( $next_posts_link ); ?></div>
	    <div class="nav-next"><?php previous_posts_link( $previous_posts_link ); ?></div>
<?php
	} else if (weaverii_getopt('wii_nav_style') == 'paged_left') {
	    echo ("\t<div class=\"nav-previous\">");
	    if (function_exists ('wp_pagenavi')) {
		wp_pagenavi();
	    } else if ( function_exists( 'wp_paginate' ) ) {
		wp_paginate( 'title=' );
	    } else {
		echo weaverii_get_paginate_archive_page_links( 'plain',2,3 );
	    }
	    echo "\t</div>\n";
	} else if (weaverii_getopt('wii_nav_style') == 'paged_right') {
	    echo ("\t<div class=\"nav-next\">");
	    if (function_exists ('wp_pagenavi')) {
		wp_pagenavi();
	    } else if ( function_exists( 'wp_paginate' ) ) {
		wp_paginate( 'title=' );
	    } else {
		echo weaverii_get_paginate_archive_page_links( 'plain',2,3 );
	    }
	    echo "\t</div>\n";
	} else {	// Older/Newer posts
?>
	    <div class="nav-previous"><?php next_posts_link( weaverii_trans('w_15_trans', $next_posts_link ) ) ?></div>
	    <div class="nav-next"><?php previous_posts_link( weaverii_trans('w_16_trans', $previous_posts_link ) ); ?></div>
<?php	} ?>
	</nav><div class="weaver-clear"></div><!-- #<?php echo $nav_id;?> -->
<?php
    }
}