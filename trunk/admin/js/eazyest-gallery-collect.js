(function($) {
		
	function eazyest_gallery_collect_finished(response) {			
 		$('#ajax-response').removeClass('collect-folders').html(response).delay(10000).hide(2000, 'linear', function(){
 			$(this).html('');
 		});
	}
	
	function eazyest_gallery_collect_next(nextFolder) {
	 	data = {
	 		action     : 'eazyest_gallery_collect_folders',
	 		subaction  : nextFolder,
	 		'_wpnonce' : eazyestGalleryCollect._wpnonce
	 	}
	 	$.post( ajaxurl, data, function(response){
	 		if ( 'next' == response )
	 			eazyest_gallery_collect_next(response);
	 		else
			 	eazyest_gallery_collect_finished(response);	
	 	});			
	}
	
	$(document).ready(function(){
		
		if ( pagenow == eazyestGalleryCollect.pagenow ) {
		 $('#ajax-response').html( eazyestGalleryCollect.collecting ).addClass('collect-folders').show('fast',function(){
		 	eazyest_gallery_collect_next('start');
		 });
		}
		
	}); // $(document).load
})(jQuery)