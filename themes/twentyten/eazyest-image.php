<?php
/**
 * The template for displaying eazyest gallery image attachments.
 *
 * @package Lazyest Gallery
 * @subpackage Twenty_Ten
 * @since 0.1.0 (r31)
 */

get_header(); ?>

		<div id="container" class="single-attachment">
			<div id="content" role="main">

			<?php
			/* Run the loop to output the image attachment.
			 * If you want to overload this in a child theme then include a file
			 * called loop-eazyest-image.php and that will be used instead.
			 */
			ezg_get_template_part( 'loop', 'eazyest-image' );
			?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_footer(); ?>
