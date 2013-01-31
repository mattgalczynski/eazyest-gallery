<?php
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( extension_loaded( 'imagick' ) && is_callable( 'Imagick', 'queryFormats' ) )
	include_once( eazyest_gallery()->plugin_dir  . 'includes/class-_eazyest-image-editor-imagick.php' );
else	
	if ( extension_loaded('gd') && function_exists('gd_info') )
		include_once( eazyest_gallery()->plugin_dir  . 'includes/class-_eazyest-image-editor-gd.php' );

/**
 * Eazyest_Image_Editor
 * 
 * @since 0.1.0 (r36)
 * @version 0.1.0 (r58)
 * @package Eazyest Gallery
 * @subpackage Image Editor
 * @see WP_Image_Editor 
 */
class Eazyest_Image_Editor extends _Eazyest_Image_Editor {

	/**
	 * Builds an output filename based on current file
	 * If file is in eazyest gallery, resized files will be stored in subdirectories. 
	 *
	 * @since 0.1.0 (r36)
	 * @version 0.1.0 (r36)
	 * @uses trailingslashit to build save path
	 * @uses wp_mkdir_p to create output directory
	 * @param string $suffix not used in Eazyest Gallery
	 * @param string $dest_path
	 * @param string $extension
	 * @return string filename
	 */
	public function generate_filename( $suffix = null, $dest_path = null, $extension = null )  {
		$filename = parent::generate_filename( $suffix, $dest_path, $extension );
		
		if ( ( false === strpos( $this->file, eazyest_gallery()->address() ) ) && ( false === strpos( $this->file, eazyest_gallery()->root() ) ) )
			return $filename; 
			
		$dir    = dirname( $this->file );
		$name   = basename( $filename );	
			
		$dest_path = $dir . '/_cache';
		if ( ! file_exists( $dest_path ) )
			wp_mkdir_p( $dest_path );
			
		return trailingslashit( $dest_path ) . $name;	
	}
} // Eazyest_Image_Editor
