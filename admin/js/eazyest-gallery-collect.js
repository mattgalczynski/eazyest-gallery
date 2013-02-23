(function($) {
		
	function eazyest_gallery_collect_finished(response) {
		if ( 0 == response ) {
			$('#eazyest-collect-folders').removeClass('collect-folders').html(eazyestGalleryCollect.notfound).delay(10000).hide(2000, 'linear', function(){
				$(this).html('');
			});
		} else {			
			var totalCount = 0;
			$.each( response, function(key, folder){
				tdImages = 'tr#post-' + folder.id + ' td.galleryfolder_images';
				var count = parseInt( $(tdImages).html(), 10 ) + folder.images['add'] - folder.images['delete'];
				totalCount += ( folder.images['add'] - folder.images['delete'] );
				$(tdImages).html(count);
			});
			$('#eazyest-collect-folders').removeClass('collect-folders').html(eazyestGalleryCollect.foundimages.replace('%d', totalCount ) ).delay(10000).hide(2000, 'linear', function(){
				$(this).html('');
			});
		}
	}
	
	function eazyest_gallery_collect_next(nextFolder) {
	 	data = {
	 		action     : 'eazyest_gallery_collect_folders',
	 		subaction  : nextFolder,
	 		_wpnonce   : eazyestGalleryCollect._wpnonce
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
			$( eazyestGalleryCollect.collecting ).insertBefore('#posts-filter table.wp-list-table');
			$('#eazyest-collect-folders').show('fast',function(){
		 		eazyest_gallery_collect_next('start');
		 	}).click(function(){
				$(this).css('color','red');	
				eazyest_gallery_collect_next('stop');
			});
		}
		
	}); // $(document).load
})(jQuery)