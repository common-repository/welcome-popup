(function( $ ) {
	'use strict';

 	// Uploading files
	var file_frame;
	var $upload_btn;

	jQuery( document ).on( 'click', '.welcome_popup_upload_image_button', function( event ) {

		event.preventDefault();

		$upload_btn = $(this);

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media({
			title: WelcomePopupScriptsData.choose_image_title,
			button: {
				text: WelcomePopupScriptsData.use_image_btn_text
			},
			multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
			var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;
			$upload_btn.siblings( 'input.welcome_popup_upload_img_id' ).val( attachment.id );
			$upload_btn.parent().siblings( '.welcome_popup_upload_img_preview' ).find('img').attr( 'src', attachment_thumbnail.url );
			$upload_btn.siblings( '.welcome_popup_remove_image_button' ).show();
		});

		// Finally, open the modal.
		file_frame.open();
	});

	jQuery( document ).on( 'click', '.welcome_popup_remove_image_button', function() {
		var $this = $(this);
		$this.parent().siblings( '.welcome_popup_upload_img_preview' ).find( 'img' ).attr( 'src', WelcomePopupScriptsData.placeholder_img_src );
		$this.siblings( '.welcome_popup_upload_img_id' ).val( '' );
		$this.hide();
		return false;
	});

})( jQuery );