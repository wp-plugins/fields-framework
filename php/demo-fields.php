<?php
ff_create_section('ff-demo-fields', 'admin_sub_menu', array('parent_uid' => 'ff-admin-menu', 'page_title' => __('Demo Fields', 'fields-framework'), 'menu_title' => __('Demo Fields', 'fields-framework')));

ff_create_field('ff-demo-fields-a-datetime-field', 'datetime', array(
	'label' => __('A datetime field', 'fields-framework'),
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-datetime-field');

ff_create_field('ff-demo-fields-a-colorpicker-field', 'colorpicker', array(
	'label' => __('A colorpicker field', 'fields-framework'),
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-colorpicker-field');

ff_create_field('ff-demo-fields-a-text-field', 'text', array(
	'label' => __('A text field', 'fields-framework'),
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-text-field');

ff_create_field('ff-demo-fields-a-media-field', 'media', array(
	'label' => __('A media upload field', 'fields-framework'),
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-media-field');

ff_create_field('ff-demo-fields-a-textarea-field', 'textarea', array(
	'label' => __('A textarea field', 'fields-framework'),
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-textarea-field');

ff_create_field('ff-demo-fields-a-checkbox-field', 'checkbox', array(
	'label' => __('A checkbox field', 'fields-framework'),
	'options' => array('Vestibulum tempor', 'Diam eget', 'Consequat ultricies', 'Praesent faucibus', 'Elit sollicitudin', 'Magna porta viverra')
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-checkbox-field');

ff_create_field('ff-demo-fields-a-radio-field', 'radio', array(
	'label' => __('A radio field', 'fields-framework'),
	'options' => array('Nam nec sem', 'Quisque rutrum diam', 'Vivamus aliquam ', 'Aliquam vulputate', 'Aenean vulputate')
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-radio-field');

ff_create_field('ff-demo-fields-a-select-field', 'select', array(
	'label' => __('A select field', 'fields-framework'),
	'options' => array('Integer tristique', 'Sit amet venenatis', 'Morbi vel eros', 'Morbi blandit est', 'Vestibulum ullamcorper'),
	'prepend_blank' => true,
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-select-field');

ff_create_field('ff-demo-fields-a-select-posts-field', 'select_posts', array(
	'label' => __('A select posts field', 'fields-framework'),
	'description' => 'A select posts field with a list of pages',
	'parameters' => 'post_type=page&posts_per_page=-1',
	'prepend_blank' => true,
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-select-posts-field');

ff_create_field('ff-demo-fields-a-select-terms-field', 'select_terms', array(
	'label' => __('A select terms field', 'fields-framework'),
	'taxonomies' => 'category',
	'parameters' => 'hide_empty=0',
	'prepend_blank' => true,
	'description' => 'A select terms field with category terms',
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-select-terms-field');

ff_create_field('ff-demo-fields-a-select-users-field', 'select_users', array(
	'label' => __('A select users field', 'fields-framework'),
	'description' => 'A select users field',
	'prepend_blank' => true,
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-select-users-field');

ff_create_field('ff-demo-fields-a-editor-field', 'editor', array(
	'label' => __('A editor field', 'fields-framework'),
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-a-editor-field');

ff_create_field('ff-demo-fields-an-email-field', 'text', array(
	'label' => __('An email field', 'fields-framework'),
	'validator' => array('validation-engine' => 'validate[required,custom[email]]'),
));

ff_add_field_to_section('ff-demo-fields', 'ff-demo-fields-an-email-field');
?>