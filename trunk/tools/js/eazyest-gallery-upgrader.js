(function($) {
		
	var lazyestUpgraderRunning = false;
	
	function lazyestUpgradeFolder( folderCount ) {		
		if ( lazyestUpgraderRunning ) {
			var data = {
				action         : 'lazyest_gallery_upgrade_folder',
				gallery_folder : $('#gallery_folder').val(),
				images_max     : $('#import_image_max' ).val(),
				allow_comments : $('input[name=allow_comments]:checked').val(),
				remove_xml     : $('input[name=remove_xml]:checked').val(),
				_ajax_nonce    : $('#_wpnonce').val()				
			}
			$.post( ajaxurl, data, function(response){
				if ( response != '0' ) {
					var newCount = parseInt( response, 10 ); 
					if( newCount != folderCount ) {
						var current = parseInt( $('#current-folder').html() );
						current++;
						$('#current-folder').html(current);
					}
					lazyestUpgradeFolder( newCount );
				} else {				
					$('#folder-counter').html( lazyestUpgraderSettings.ready );
					if ( '1' == $('input[name=convert_page]:checked').val() ){
						lazyestUpgradePage();
					} else {	
						lazyestUpdateSettings();			
					}
				}
			});
		}
	}
	
	function lazyestUpgradePage() {
		if ( '1' == $('input[name=convert_page]:checked').val() && lazyestUpgraderRunning ) {
			$('#upgrade_page').show();
			var data = {
				action      : 'lazyest_gallery_convert_page',
				gallery_id  : $('#gallery_id').val(),
				_ajax_nonce : $('#_wpnonce').val()		
			};
			$.post( ajaxurl, data, function(response){
				lazyestUpdateSettings();			
			});
		}
	}
	
	function lazyestUpdateSettings() {
		if ( lazyestUpgraderRunning ) {
			$('#upgrade-settings').show();
			var data = {				
				action         : 'lazyest_gallery_update_settings',
				gallery_folder : $('#gallery_folder').val(),
				_ajax_nonce    : $('#_wpnonce').val()		
			};
			$.post( ajaxurl, data, function(response){	
				$('#upgrade-cleanup').show();
				lazyestCleanup();				
			});
		}
	}
	
	function lazyestCleanup() {
		if ( lazyestUpgraderRunning ) {			
			var data = {				
				action         : 'lazyest_gallery_cleanup',
				gallery_folder : $('#gallery_folder').val(),
				_ajax_nonce    : $('#_wpnonce').val()		
			};
			$.post( ajaxurl, data, function(response ){
				$('#start-upgrade').hide();
				$('#upgrade-process-title').html( lazyestUpgraderSettings.finished );
				$('#upgrade-success').show();
			});
		}
	}
	
	function lazyestUpgrader() {
		if ( ! lazyestUpgraderRunning ) {
			$('#skip').hide();
			$('#upgrade-process').show();
			$('#upgrade-error').hide();
			$('#start-upgrade').html( lazyestUpgraderSettings.stop );
			$('#start-upgrade').removeClass( 'button-primary' );
			$('#upgrade-form :input').attr( 'disabled', true );
			lazyestUpgraderRunning = true;
		} else {
			$('#skip').show();
			$('#upgrade-process').hide();
			$('#current-folder').html( '0' );
			$('#start-upgrade').html( lazyestUpgraderSettings.restart );
			$('#start-upgrade').addClass( 'button-primary' );
			lazyestUpgraderRunning = false;
		}
		if ( lazyestUpgraderRunning ) {
			var data = {
				action         : 'lazyest_gallery_get_upgrade_folders',
				gallery_folder : $('#gallery_folder').val(),
				_ajax_nonce    : $('#_wpnonce').val()
			}
			$.post( ajaxurl, data, function(response){
				if ( 'empty' ==  response ) {
					$('#upgrade-error').show();
					$('#start-upgrade').html( lazyestUpgraderSettings.restart );
					lazyestUpgraderRunning = false;
				} else {
					var folderCount = parseInt( response, 10 );
					if ( folderCount > 0 ) {
						$('#current-folder').html( '1' );
						$('#all-folders').html(folderCount);
						$('#folder-counter').show();
						lazyestUpgradeFolder( folderCount );
					}
				}
			});					
		}
	}
	
	$(document).ready(function() {
		
		$('#start-upgrade').click(function(){
			lazyestUpgrader();
			return false;	
		});
		
		$('#skip_upgrade').click(function(){
			$(this).parents('form').submit();
		});
		
		$('#gallery_folder').change(function(){
			data = {
				action         : 'lazyest_gallery_gallery_folder_change',
				gallery_folder : $('#gallery_folder').val(),
				_ajax_nonce    : $('#gallery-folder').val()				
			};
			$.post( ajaxurl, data, function(response){
				
			});
		});	
		
	}); // $(document).ready();
	
})(jQuery)