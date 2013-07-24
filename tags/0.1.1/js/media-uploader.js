/*
 * Modified version of custom-header.js
 *
 * Modified by http://www.rhyzz.com
 */

(function($) {
	var frame;

	$( function() {
		// Build the choose from library frame.
		$('.ff_upload_media').click( function( event ) {
			var $el = $(this);

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			var library_types = null
			
			if($el.data('library')) {
				library_types = $el.data('library');
			}

			// Create the media frame.
			frame = wp.media.frames.custom_upload = wp.media({
				// Set the title of the modal.
				title: $el.data('choose'),

				// Tell the modal to show only images.
				library: {
					type: library_types
				},

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: $el.data('update'),
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: true
				}
				,multiple: false
			});

			// When an image is selected, run a callback.
			frame.on( 'select', function() {
				var attachment = frame.state().get('selection').first().toJSON();

				jQuery('#' + $el.data('to')).val(attachment.url);

			});

			frame.open();
		});
	});
}(jQuery));