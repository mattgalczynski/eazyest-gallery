<?php
/**
 * Deprecated functions from past Eazyest Gallery versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package Eazyest Gallery
 * @subpackage Deprecated
 * @since 0.1.0 (r2)
 */
  
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * _ezg_deprecated_function() 
 * Marks a function as deprecated and informs when it has been used.
 *
 * The current behavior is to trigger a user error if WP_DEBUG is true.
 * 
 * @since 0.1.0 (r2)
 * @uses apply_filters() calls 'deprecated_function_trigger_error' and expects boolean value of true to trigger, or false to not trigger error.
 * @param string $function
 * @param string $version
 * @param string $replacement
 * @return void
 */
function _ezg_deprecated_function( $function, $version, $replacement = null ) {
	
	// allow plugin to filter the output error trigger
	if ( WP_DEBUG && apply_filters( 'deprecated_function_trigger_error', true ) ) {
		if ( ! is_null($replacement) )
			trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since eazyest-gallery plugin version %2$s! Use %3$s instead.', 'eazyest-gallery' ), $function, $version, $replacement ) );
		else
			trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since eazyest-gallery plugin version %2$s with no alternative available.', 'eazyest-gallery' ), $function, $version ) );
	}
}  

// deprecated template tags ---------------------------------------------------

/**
 * lg_list_folders()
 * 
 * @since lazyest-gallery 0.8
 * @deprecated 2.0.0
 * @deprecated use lzg_list_folders()
 * @see lzg_list_folders() in eazyest-gallery/frontend/template-tags.php
 * @param string $title
 * @return void
 */
function lg_list_folders( $title = '' ) {
	_ezg_deprecated_function( __FUNCTION__, '2.0.0', 'lzg_list_folders()' );
	
	if ( ! empty( $title ) ) :
		?>
		<h2><?php echo esc_html( $title ); ?></h2>
		<?php
	endif;
	
	$title = '';
	lzg_list_folders( $title, 'echo' );	
}

/**
 * lg_random_image()
 * 
 * @since lazyest-gallery 0.8
 * @deprecated 2.0.0
 * @deprecated use lzg_random_image()
 * @see lzg_random_image() in eazyest-gallery/frontend/template-tags.php
 * @param string $title
 * @param string $count
 * @param string $folder
 * @param bool $sub
 * @return void
 */
function lg_random_image(  $title = '', $count = '1', $folder = '', $sub = true ) {
	_ezg_deprecated_function( __FUNCTION__, '2.0.0', 'lzg_random_image()' );
	$arg = array(
		'number'     => intval( $count ),
		'columns'    => eazyest_gallery()->get_option( 'thumbs_columns' ),
		'title'      => '',
		'subfolders' => $sub ? 1 : 0,
		'size'       => 'thumbnail'
	);
	?>
	<?php if ( ! empty( $title ) ) : ?>
	<h2><?php echo esc_html( $title ); ?></h2>
	<?php	endif;
	lzg_random_image( $args );
}