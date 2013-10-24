jQuery(function() {
	ff_load();

	jQuery(document).ajaxComplete(function(event, xhr, settings) {
		if(settings.data.match(/action\=save-widget/) == 'action=save-widget' && settings.data.match(/delete_widget=1/) != 'delete_widget=1') {
			ff_load();
		}
	});
});

function ff_load() {
	ff_repeatable();

	ff_datetimepicker();
	
	ff_colorpicker();

	ff_validationengine();

	ff_placeholder();

	if(typeof ff_media_uploader === 'function') {
		ff_media_uploader();
	}
	
	ff_builder();
}

function ff_repeatable() {
	if(jQuery.fn.repeatable_fields !== undefined) {
		jQuery('.ff-repeatable').each(function() {
			jQuery(this).repeatable_fields({
				wrapper: 'table',
				container: 'tbody',
				row: 'tr',
				add: '.ff-add-row',
				remove: '.ff-remove-row',
				move: '.ff-move-row',
				template: '.ff-add-template',
			});
		});
	}
}

function ff_datetimepicker() {
	if(jQuery.ui.timepicker !== undefined) {
		jQuery('.ff-fields').on('focus', '.ff-datetime', function() {
			var settings = {};
			
			var date_format = jQuery(this).data('date-format');

			var time_format = jQuery(this).data('time-format');
			
			if(date_format == -1) {
				settings.timeOnly = true;
			}
			else {
				settings.dateFormat = date_format;
			}

			if(time_format == -1) {
				settings.showTimepicker = false;
			}
			else {
				settings.timeFormat = time_format;
			}

			jQuery(this).datetimepicker(settings);
		});
	}
}

function ff_colorpicker() {
	if(jQuery.fn.colorpicker !== undefined) {
		jQuery('.ff-fields').on('focus', '.ff-colorpicker', function() {
			jQuery(this).colorpicker({
				alpha: true,
				colorFormat: 'RGBA',
				dragggable: false,
				parts: ['map', 'bar', 'hex', 'hsv', 'rgb', 'alpha', 'lab', 'cmyk', 'preview', 'footer'],
				showNoneButton : true
			});
		});
	}
}

function ff_validationengine() {
	if(jQuery.fn.validationEngine !== undefined) {
		jQuery('#wpbody-content form, form#post, form#addtag, form#edittag, form#your-profile, .widget form').validationEngine({promptPosition: 'topLeft'});
	}
}

function ff_placeholder() {
	if(jQuery.fn.placeholder !== undefined) {
		jQuery('input, textarea').placeholder();
	}
}

function ff_builder() {
	jQuery('.ff-builder a').filter(function() {
		return jQuery(this).attr('href').match(/\=delete&/) == '=delete&';
	}).click(function() {
		return confirm('Confirm Delete?');
	});

	jQuery('.ff-builder input[name="import-builder"]').click(function() {
		return confirm('Confirm Import?');
	});

	if(jQuery.ui.sortable !== undefined) {
		jQuery('.ff-builder-fields-by-sections').each(function() {
			jQuery(this).sortable({
					items: '> li',
			});
		});
	}
}