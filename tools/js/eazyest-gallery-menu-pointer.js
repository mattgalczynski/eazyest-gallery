(function($) {
	$(document).ready(function() {
		
		$('#menu-posts-galleryfolder').pointer({
	  	content: lazyestUpgraderPointer.content,
			 position: {'edge':'top'},
			 close: function() {
			 	$.post( ajaxurl, {
					pointer: 'lazyest_gallery_upgrader',
					action: 'dismiss-wp-pointer'
				});
			 }
		}).pointer('open');
			
	});  // $(document).ready();
})(jQuery)