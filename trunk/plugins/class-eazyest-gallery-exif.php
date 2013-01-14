<?php
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 

class Eazyest_Gallery_Exif {
	
	private static $instance;
	
	function __construct(){}
	
	private function init() {
		$this->filters();
	}
	
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Eazyest_Gallery_Exif;
			self::$instance->init();
		}
		return self::$instance;
	}
	
	function filters() {
		add_filter( 'eazyest_gallery_image_settings', array( $this, 'exif_option' ) );
	}
	
	function exif_option( $options ) {
		$options['enable_exif'] = array(
			'title' => __( 'Exif', 'eazyest-gallery' ),
			'callback' => array( $this, 'enable_exif' )
		);
		return $options;
	}
	
	function enable_exif() {
		$enable_exif = eazyest_gallery()->get_option( 'enable_exif' );
		?>
		<input type="checkbox" id="enable_exif" name="eazyest-gallery[enable_exif]" <?php checked( $enable_exif ) ?> />
		<label for="enable_exif"><?php _e( 'Show Exif information on the attachment page', 'eazyest-gallery' ) ?> </label>
		<?php
	}	
	
} // Eazyest_Gallery_Exif

function eazyest_gallery_exif() {
	return Eazyest_Gallery_Exif::instance();
}