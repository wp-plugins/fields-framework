jQuery(function() {
	if(jQuery.ui.timepicker !== undefined) {
		jQuery('.ff-fields').on('click', '.ff-datetime', function() {
			jQuery(this).datetimepicker({dateFormat: jQuery(this).data('date-format'), timeFormat: jQuery(this).data('time-format')});
		});
	}

	if(jQuery.fn.ColorPicker !== undefined) {
		jQuery('.ff-fields').on('click', '.ff-colorpicker', function() {
			jQuery(this).ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					jQuery(el).val('#' + hex);
		
					jQuery(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					jQuery(this).ColorPickerSetColor(this.value);
				}
			}).bind('keyup', function() {
				jQuery(this).ColorPickerSetColor(this.value);
			});
		});
	}

	jQuery('.ff-repeatable').each(function() {
		ff_repeatable(this);
	});

	if(jQuery.fn.placeholder !== undefined) {
		jQuery('input, textarea').placeholder();
	}
});

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