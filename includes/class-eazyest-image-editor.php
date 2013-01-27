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
 * @version 0.1.0 (r36)
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
	 * @uses get_option to get image sizes
	 * @uses trailingslashit to build save path
	 * @uses wp_mkdir_p to cerate output directory
	 * @param string $suffix not used in Eazyest Gallery
	 * @param string $dest_path
	 * @param string $extension
	 * @return string filename
	 */
	public function generate_filename( $suffix = null, $dest_path = null, $extension = null )  {
		
		if ( ( false === strpos( $this->file, eazyest_gallery()->address() ) ) && ( false === strpos( $this->file, eazyest_gallery()->root() ) ) )
			return parent::generate_filename( $suffix, $dest_path, $extension );
			
		$sizes  = array( 'thumbnail', 'medium', 'large' );
		$subdir = '';
		$width  = $this->size['width'];
		$height = $this->size['height'];
		if ( ! empty( $this->size ) )	
			foreach( $sizes as $size ) {
				if ( $width <= intval( get_option( "{$size}_size_w" ) ) && $height <= intval( get_option( "{$size}_size_h" ) ) ){
					$subdir = eazyest_folderbase()->size_dir( $size );	
					break;
				}		
			}					
		if ( false !== strpos( $this->file, eazyest_gallery()->address ) ) {
			$gallery_path = substr( $this->file, strlen( eazyest_gallery()->address ) );
			$this->file = trailingslashit( eazyest_gallery()->root() ) . $gallery_path;
		}
		
		$info   = pathinfo( $this->file );
		$dir    = $info['dirname'];
		$ext    = $info['extension'];
		$name   = basename( $this->file );
		
		$dest_path = trailingslashit( $dir ) . $subdir;
		if ( ! file_exists( $dest_path ) )
			wp_mkdir_p( $dest_path );
		
		if ( ! is_null( $dest_path ) && $_dest_path = realpath( $dest_path ) )
			$dir = $_dest_path;	
		return trailingslashit( $dir ) . $name;	
	}
} // Eazyest_Image_Editor
