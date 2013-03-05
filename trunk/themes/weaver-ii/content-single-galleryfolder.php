<?php
/**
 * The template for displaying content in the single-galleryfolder.php template
 *
 * @package WordPress
 * @subpackage Weaver II
 * @since Eazyest Gallery 0.1.0 (r206)
 */
weaverii_trace_template(__FILE__);
global $weaverii_cur_post_id;
$weaverii_cur_post_id = get_the_ID();
weaverii_per_post_style();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('content-single ' . weaverii_post_count_class(true)); ?>>
	<header class="entry-header">
<?php
		weaverii_post_title('<hgroup class="entry-hdr"><h1 class="entry-title">', "</h1></hgroup>\n", 'single');

		if ( 'page' != get_post_type() ) { ?>
		<div class="entry-meta">
			<?php weaverii_post_top_info('single'); ?>
		</div><!-- .entry-meta -->
		<?php } ?>
	</header><!-- .entry-header -->

<div class="entry-content eazyest-gallery">
			<?php do_action( 'eazyest_gallery_before_folder_content', $post->ID ); // this action uis used for breadcrumb trail and for thumbnail images ?>
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'eazyest-gallery' ) ); // this is the text content like a regular WordPress post ?>
			<?php do_action( 'eazyest_gallery_after_folder_content', $post->ID ); // this action is used for extra fields and for subfolders ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'eazyest-gallery' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->

	<footer class="entry-utility">
<?php
		weaverii_post_bottom_info('single');

		if ( get_the_author_meta( 'description' ) && !weaverii_getopt('wii_hide_author_bio')) { // If a user has filled out their description, show a bio on their entries ?>
		<div id="author-info">
			<div id="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'weaverii_author_bio_avatar_size', 68 ) ); ?>
			</div><!-- #author-avatar -->
			<div id="author-description">
				<h2><?php printf( esc_attr__( 'About %s','eazyest-gallery'), get_the_author() ); ?></h2>
				<?php the_author_meta( 'description' ); ?>
				<div id="author-link">
					<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
						<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>','eazyest-gallery'), get_the_author() ); ?>
					</a>
				</div><!-- #author-link	-->
			</div><!-- #author-description -->
		</div><!-- #entry-author-info -->
<?php 		} ?>

	</footer><!-- .entry-utility -->
<?php		    weaverii_inject_area('postpostcontent');	// inject post comment body ?>
</article><!-- #post-<?php the_ID(); ?> -->
