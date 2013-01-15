(function($) {
	
	$(document).ready(function(){
	
	// filetree dropdown ---------------------------------------------------------  
		$('#folder-select').click(function(){
				if ($(this).hasClass('open') ) {
					if ( ! $(this).hasClass('loaded') ) {
						$('#file-tree').fileTree({
							root        : fileTreeSettings.root,
							script      : fileTreeSettings.script,
							loadMessage : fileTreeSettings.loadMessage	
						}, function(directory){
							var data = {
								dir    : directory,
								action : 'eazyest_gallery_select_dir',
								_wpnonce : $('#file-tree-nonce').val()
							}
							$.post( fileTreeSettings.script, data, function(response){
								$('#gallery_folder').val(response);
							});
						})
						$('#file-tree').show();
						$(this).removeClass('open');
						$(this).addClass('loaded');
					} else {					
						$('#file-tree').show();
						$(this).removeClass('open');
					}
			} else {
				$('#file-tree').hide();
				$(this).addClass('open');
			}
			return false;
		});
		
		$('#wpcontent').click(function(){
			$('#file-tree').hide();
			$('#folder-select').addClass('open');
		});
		
	// folder image select -------------------------------------------------------		
		function showRandomSubFolder() {			
			if ( $('#folder_image').val() == 'random_image' )
				$('#random-subfolder').css('visibility','visible');
			else
				$('#random-subfolder').css('visibility','hidden');
		}
		
		showRandomSubFolder();
		
		$('#folder_image').change( function(){
			showRandomSubFolder();
		});
		
	// thumbnail popup select ----------------------------------------------------
		function showThumbnailPopup( aField, aPopup ) {
			var options = new Array('medium', 'large', 'full');
			var value = $(aField).val();
			if ( 0 <= options.indexOf(value) )
				$(aPopup).css('visibility','visible');
			else
				$(aPopup).css('visibility','hidden');
		}
		
		showThumbnailPopup( '#on_thumb_click', '#thumb-popup' );
		showThumbnailPopup( '#on_slide_click', '#slide-popup' );
		
		$('#on_thumb_click').change( function(){
			showThumbnailPopup( '#on_thumb_click', '#thumb-popup' );
		});
		$('#on_slide_click').change( function(){			
			showThumbnailPopup( '#on_slide_click', '#slide-popup' );
		});
		
	});
		
})(jQuery)