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
		
	}); // window.load
	
	function eazyestMoreButton() {
		if ( $('nav.thumbnail-navigation').length ) {
			$('nav.thumbnail-navigation .nav-previous').remove();
			$('nav.thumbnail-navigation .nav-next').removeClass('nav-next alignright').addClass('nav-more alignleft');
			$('nav.thumbnail-navigation .nav-more a').addClass('button').html( eazyestFrontend.moreButton );
			$('nav.thumbnail-navigation .nav-more').on( 'click', 'a', function() {
				$(this).html( eazyestFrontend.moreButton + '&hellip;' );
				thumbsPage = $(this).attr('id').substr(15);
				var data = {
					action : 'eazyest_gallery_more_thumbnails',
					page   : thumbsPage,
					folder : $(this).closest('nav.thumbnail-navigation').attr('id').substr(14)
				};
				$.post( eazyestFrontend.ajaxurl, data, function(response){
					$('nav.thumbnail-navigation').replaceWith(response);
					eazyestMoreButton();
				})
				return false;
			});
		}		
	}
	
	$(document).ready(function() {
		eazyestMoreButton();		
	});
	
})(jQuery)