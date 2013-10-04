/*
 * Modified version of custom-header.js
 *
 * Modified by http://www.rhyzz.com
 */

function ff_media_uploader() {
	(function($) {
		var frame;
	
		$(function() {
			// Build the choose from library frame.
			$('.ff-fields').on('click', '.ff_upload_media', function(event) {
				var $el = $(this);
	
				event.preventDefault();
	
				if(frame !== undefined) {
					frame.close();
				}
	
				// Create the media frame.
				frame = wp.media.frames.custom_upload = wp.media({
					// Tell the modal to show only images.
					library: {
						type: $el.data('library')
					}
				});
	
				// When an image is selected, run a callback.
				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
	
					jQuery('#' + $el.data('to')).val(attachment.url);
				});
	
				frame.open();
	
				var post_id = $('#post_ID').val();
	
				if(post_id !== undefined && frame.uploader.uploader.param('post_id') === undefined) {
					frame.uploader.uploader.param('post_id', post_id);
				}
			});
		});
	}(jQuery));
}