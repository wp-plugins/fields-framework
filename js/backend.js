jQuery(function() {
	ff_load();

	jQuery(document).ajaxComplete(function(event, xhr, settings) {
		if(settings.data.match(/action\=save-widget/) == 'action=save-widget' && settings.data.match(/delete_widget=1/) != 'delete_widget=1') {
			ff_load();
		}
	});
});

function ff_load() {
	jQuery('.ff-repeatable').each(function() {
		ff_repeatable(this);
	});

	ff_datetimepicker();
	
	ff_colorpicker();

	ff_validationengine();

	ff_placeholder();

	if(typeof ff_media_uploader === 'function') {
		ff_media_uploader();
	}
	
	ff_builder();
}

function ff_repeatable(ff_table) {
	var table_class = '.ff-repeatable';
	
	var add_row = '.ff-add-row';
	
	var template_class = '.ff-add-template';
	
	var remove_row = '.ff-remove-row';

	var move_class = '.ff-move-row';

	if(jQuery.ui.sortable !== undefined) {
		jQuery(table_class).find('tbody').each(function() {
			jQuery(this).sortable({
					handle: move_class,
					helper: function(e, ui) {
						ui.children().each(function() {
							jQuery(this).width(jQuery(this).width());
						});

						return ui;
					},
					items: '> tr',
			});
		});
	}

	jQuery(table_class).on('click', add_row, function(event) {
		event.stopImmediatePropagation();

		var table = jQuery(this).parents('table').first();

		var table_body = jQuery(table).children('tbody');

		var row_template = JSON.parse(jQuery(table_body).children(template_class).html());

		var new_row = jQuery(row_template).appendTo(table_body);

		var row_count = jQuery(table_body).children('tr').length;

		jQuery('> td label', new_row).each(function() {
			var ff_for = jQuery(this).attr('for');

			ff_for = ff_for.replace(/-0/, '-' + row_count);

			jQuery(this).attr('for', ff_for);
		});

		jQuery('> td :input', new_row).each(function() {
			var ff_name = jQuery(this).attr('name');

			ff_name = ff_name.replace(/\[0\]/, '[' + row_count + ']');

			jQuery(this).attr('name', ff_name);

			var ff_id = jQuery(this).attr('id');

			ff_id = ff_id.replace(/-0/, '-' + row_count);
			
			jQuery(this).attr('id', ff_id);
		});

		jQuery('> td .ff_upload_media', new_row).each(function() {
			var ff_id = jQuery(this).data('to');

			ff_id = ff_id.replace(/-0/, '-' + row_count);
			
			jQuery(this).data('to', ff_id);
		});
	});

	jQuery(table_class).on('click', remove_row, function(event) {
		event.stopImmediatePropagation();

		var row = jQuery(this).parents('tr').first();

		row.remove();

		event.stopImmediatePropagation();
	});
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
		if(jQuery(this).attr('href').match(/\=delete&/) == '=delete&') {
			return confirm('Confirm Delete?');
		}

		return false;
	});
	


	if(jQuery.ui.sortable !== undefined) {
		jQuery('.ff-builder-fields-by-sections').each(function() {
			jQuery(this).sortable({
					items: '> li',
			});
		});
	}
}