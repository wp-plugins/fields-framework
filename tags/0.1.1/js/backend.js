jQuery(function() {
	jQuery('.ff-repeatable').each(function() {
		ff_repeatable(this);
	});
});

function ff_repeatable(ff_table) {
	var ff_rows = 0;

	jQuery('> tbody > tr', ff_table).each(function() {
		ff_rows++;
	});

	jQuery(ff_table).dynoTable({
		removeClass: jQuery('> tbody > tr > td > .ff-remove-row', ff_table),
		addRowTemplateClass: jQuery('> tbody > .ff-add-template', ff_table),
		addRowButtonClass: jQuery('> thead > tr > th > .ff-add-row', ff_table),
		lastRowRemovable: true,
		orderable: true,
		dragHandleClass: jQuery('> tbody > tr > th > .ff-move-row', ff_table),
		hideTableOnEmpty: false,
		afterRowAdd: function() {
			ff_repeatable(ff_table);
		},
		onRowAdd: function() {
			jQuery('> tbody > tr:last > td :input', ff_table).each(function() {
				var ff_name = jQuery(this).attr('name');

				ff_name = ff_name.replace(/\[(\d+)\]$/, '[' + ff_rows + ']');

				if(ff_name == jQuery(this).attr('name')) {
					ff_name = ff_name.replace(/\[(\d+)\]\[/, '[' + ff_rows + '][');
				}

				jQuery(this).attr('name', ff_name);
			});

			jQuery('> tbody > tr:last > td :input', ff_table).each(function() {
				var ff_id = jQuery(this).attr('id');

				ff_id = ff_id.replace(/\[(\d+)\]__c$/, '[' + ff_rows + ']__c');

				if(ff_id == jQuery(this).attr('id')) {
					ff_id = ff_id.replace(/\[(\d+)\]\[/, '[' + ff_rows + '][');
				}

				jQuery(this).attr('id', ff_id);
			});

			jQuery('> tbody > tr:last > td label', ff_table).each(function() {
				var ff_for = jQuery(this).attr('for');

				ff_for = ff_for.replace(/\[(\d+)\]$/, '[' + ff_rows + ']');

				if(ff_for == jQuery(this).attr('for')) {
					ff_for = ff_for.replace(/\[(\d+)\]\[/, '[' + ff_rows + '][');
				}

				jQuery(this).attr('for', ff_for);
			});

			ff_rows++;
		}
	});
}