<?php
/**
 * The Template for displaying all single gallery folders.
 *
 * @package Eazyest Gallery
 * @subpackage Weaver II
 * @since Eazyest Gallery 0.1.0 (r2)
 */

weaverii_get_header('single');
if ( weaverii_getopt('wii_infobar_location') == 'top' ) get_template_part('infobar');
weaverii_inject_area('premain');
echo("\t<div id=\"main\">\n");
weaverii_trace_template(__FILE__);
global $weaverii_cur_post_id;
$weaverii_cur_post_id = get_the_ID();
weaverii_get_sidebar_left('single');
?>
		<div id="container_wrap"<?php weaverii_get_page_class('single', 'container-single'); ?>>
<?php		if (weaverii_getopt('wii_infobar_location') == 'content') get_template_part('infobar');
		weaverii_inject_area('precontent'); ?>
		<div id="container">
<?php		weaverii_get_sidebar_top('single'); ?>
			<div id="content" role="main">

<?php 		weaverii_post_count_clear();
		$cats = weaverii_getopt_checked('wii_single_nav_link_cats');
		while ( have_posts() ) {
		    the_post(); ?>
			    <nav id="nav-above" class="navigation">
				<h3 class="assistive-text"><?php echo __( 'Post navigation','eazyest-gallery'); ?></h3>
<?php			    if (weaverii_getopt('wii_single_nav_style')=='prev_next') {
?>
			        <div class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous','eazyest-gallery'), $cats ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>','eazyest-gallery'), $cats); ?></div>
<?php			    } else {
?>
			        <div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link','eazyest-gallery') . '</span> %title', $cats ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link','eazyest-gallery') . '</span>' , $cats); ?></div>
<?php			    }
?>
			    </nav><!-- #nav-above -->

<?php 			    ezg_get_template_part( 'content-single', 'galleryfolder' ); ?>

			    <nav id="nav-below" class="navigation">
				<h3 class="assistive-text"><?php echo __( 'Post navigation','eazyest-gallery'); ?></h3>
<?php			    if (weaverii_getopt('wii_single_nav_style')=='prev_next') {
?>
			        <div class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous','eazyest-gallery'), $cats ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>','eazyest-gallery'), $cats ); ?></div>
<?php			    } else {
?>
			        <div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link','eazyest-gallery') . '</span> %title', weaverii_getopt_checked('wii_single_nav_link_cats') ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link','eazyest-gallery') . '</span>', $cats ); ?></div>
<?php			    }
?>
			    </nav><!-- #nav-above -->


<?php 			    comments_template( '', true ); ?>

<?php 		} // end of the loop. ?>

			</div><!-- #content -->
<?php		weaverii_get_sidebar_bottom('single'); ?>
		</div><!-- #container -->
		</div><!-- #container_wrap -->

<?php	weaverii_get_sidebar_right('single');
	weaverii_get_footer('single'); ?>
