(function($) {
	
	$(document).ready(function(){
		
		function doDragTable( dTable ) {
			$( 'td.galleryfolder_drag' ).addClass( 'drag-handle' );
			$( 'td.media_drag' ).addClass( 'drag-handle' );
			
			$( '.column-galleryfolder_drag, .column-media_drag' ).show();
			
			$( dTable ).tableDnD({
				onDragClass : 'dragging',
				dragHandle  : '.drag-handle',
				onDrop      : function( table, row ) {
					var dropStr = '';
					var i = 0;
					$( table ).find( 'input.drag-id' ).each( function() {
						if ( i % 2 )
							$( this ).closest( 'tr' ).addClass( 'alternate' );
						else
							$( this ).closest( 'tr' ).removeClass( 'alternate' );
						dropStr += $( this ).val() + ' ';
						i++;
					});
					$( '#save-order' ).removeAttr( 'disabled' );
					dropStr = dropStr.replace(/(^\s*)|(\s*$)/g, '');
					if ( $( table ).hasClass( 'pages' ) ) {
						$( '#gallery-order-pages' ).val( dropStr );
						$( '#gallery-changed-pages').val( '1' );
					}
					else {
						$( '#gallery-order-media' ).val( dropStr );
						$( '#gallery-changed-media').val( '1' );
					}
				} 
			});	// $( dTable ).tableDnD					
		}
		
		if ( $( '.column-galleryfolder_drag' ).length ) {	
			
			doDragTable( '.wp-list-table.pages' );
			doDragTable( '.wp-list-table.media' );
			
		} // $( '.column-galleryfolder_drag' ).length
		
		$( '.media-button-insert' ).live( 'click', function(){
			var data = {
				action : 'lazyest_gallery_upload',
				_wpnonce : $( '#_wpnonce' ).val(),
				post     : $( '#post_ID' ).val(),
			};
			$.post( ajaxurl, data, function( response ){
				$( '.attached-images' ).html( response );
				doDragTable( '.wp-list-table.media' );
			});
			return false;
		});
						
	}); // $(document).ready
	
	
})(jQuery)