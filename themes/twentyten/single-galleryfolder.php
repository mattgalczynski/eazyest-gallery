<?php
/**
 * The Template for displaying all single galleryfolders.
 *
 * @package Eazyest Gallery
 * @subpackage Twenty_Ten
 * @since 0.1.0 (r2)
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

			<?php
			/* Run the loop to output the post.
			 * If you want to overload this in a child theme then include a file
			 * called loop-single.php and that will be used instead.
			 */
			ezg_get_template_part( 'loop', 'single-galleryfolder' );
			?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
