(function($){
	
	$(window).load( function(){
		// make all gallery items same height for responsive gallery
		if ( $('.gallery-columns-0').length ) {				
			var maxHeight = 0;
			$('.gallery-columns-0').children('dl').each( function(){
				if ( $(this).outerHeight() > maxHeight )
					maxHeight = $(this).outerHeight(); 
			});	
			
			$('.gallery-columns-0').children('dl').each( function(){
				$(this).css('height',maxHeight);
			});
		}
		
	});
	
})(jQuery)