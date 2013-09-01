<?php
ff_create_section('ff-field-testing', 'admin_sub_menu', array('parent_uid' => 'ff-admin-menu', 'page_title' => __('Unit Test', 'fields-framework'), 'menu_title' => __('Unit Test', 'fields-framework')));

ff_create_field('ff-field-testing-a-text-field', 'text', array(
	'label' => __('A text field', 'fields-framework'),
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-text-field');

ff_create_field('ff-field-testing-a-text-field-with-default', 'text', array(
	'label' => __('A text field with default', 'fields-framework'),
	'value' => __('Here is a default value for this text field', 'fields-framework')
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-text-field-with-default');

ff_create_field('ff-field-testing-a-repeatable-text-field', 'text', array(
	'label' => __('A repeatable text field', 'fields-framework'),
	'repeatable' => true,
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-repeatable-text-field');

ff_create_field('ff-field-testing-a-repeatable-text-field-with-default', 'text', array(
	'label' => __('A repeatable text field with default', 'fields-framework'),
	'repeatable' => true,
	'value' => __('Here is a default value for this repeatable text field', 'fields-framework')
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-repeatable-text-field-with-default');

ff_create_field('ff-field-testing-a-select-field', 'select', array(
	'label' => __('A select field', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-select-field');

ff_create_field('ff-field-testing-a-select-field-with-default', 'select', array(
	'label' => __('A select field with default', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'value' => 2
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-select-field-with-default');

ff_create_field('ff-field-testing-a-repeatable-select-field', 'select', array(
	'label' => __('A repeatable select field', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'repeatable' => true,
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-repeatable-select-field');

ff_create_field('ff-field-testing-a-repeatable-select-field-with-default', 'select', array(
	'label' => __('A repeatable select field with default', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'repeatable' => true,
	'value' => 4
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-repeatable-select-field-with-default');

ff_create_field('ff-field-testing-a-multi-select-field', 'select', array(
	'label' => __('A multi-select field', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'multiple' => true,
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-multi-select-field');

ff_create_field('ff-field-testing-a-multi-select-field-with-default', 'select', array(
	'label' => __('A multi-select field with default', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'multiple' => true,
	'value' => array(3, 5)
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-multi-select-field-with-default');

ff_create_field('ff-field-testing-a-multi-select-field-with-multi-default', 'select', array(
	'label' => __('A multi-select field with multi-default', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'multiple' => true,
	'value' => array(2, 4)
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-multi-select-field-with-multi-default');

ff_create_field('ff-field-testing-a-repeatable-multi-select-field', 'select', array(
	'label' => __('A repeatable multi-select field', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'repeatable' => true,
	'multiple' => true,
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-repeatable-multi-select-field');

ff_create_field('ff-field-testing-a-repeatable-multi-select-field-with-default', 'select', array(
	'label' => __('A repeatable multi-select field with default', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'repeatable' => true,
	'multiple' => true,
	'value' => array(4)
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-repeatable-multi-select-field-with-default');

ff_create_field('ff-field-testing-a-repeatable-multi-select-field-with-multi-default', 'select', array(
	'label' => __('A repeatable multi-select field with multi-default', 'fields-framework'),
	'options' => array('Duis at nunc a sapien pharetra pretium', 'Aliquam sed risus at eros cursus iaculis', 'Fusce non odio at nisi eleifend gravida', 'Donec condimentum libero vitae tellus vestibulum', 'Non consequat nisl ornare'),
	'prepend_blank' => true,
	'repeatable' => true,
	'multiple' => true,
	'value' => array(2, 4)
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-repeatable-multi-select-field-with-multi-default');

ff_create_field('ff-field-testing-a-group-field', 'group', array(
	'label' => __('A group field', 'fields-framework'),
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-group-field');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-text-field');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-text-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-repeatable-text-field');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-repeatable-text-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-select-field');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-select-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-repeatable-select-field');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-repeatable-select-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-multi-select-field');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-multi-select-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-multi-select-field-with-multi-default');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-repeatable-multi-select-field');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-repeatable-multi-select-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-group-field', 'ff-field-testing-a-repeatable-multi-select-field-with-multi-default');

ff_create_field('ff-field-testing-a-repeatable-group-field', 'group', array(
	'label' => __('A repeatable group field', 'fields-framework'),
	'repeatable' => true,
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-repeatable-group-field');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-text-field');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-text-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-repeatable-text-field');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-repeatable-text-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-select-field');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-select-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-repeatable-select-field');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-repeatable-select-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-multi-select-field');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-multi-select-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-multi-select-field-with-multi-default');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-repeatable-multi-select-field');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-repeatable-multi-select-field-with-default');

ff_add_field_to_field_group('ff-field-testing-a-repeatable-group-field', 'ff-field-testing-a-repeatable-multi-select-field-with-multi-default');

ff_create_field('ff-field-testing-a-nested-group-field', 'group', array(
	'label' => __('A nested group field', 'fields-framework'),
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-nested-group-field');

ff_add_field_to_field_group('ff-field-testing-a-nested-group-field', 'ff-field-testing-a-group-field');

ff_create_field('ff-field-testing-a-nested-repeatable-group-field', 'group', array(
	'label' => __('A nested repeatable group field', 'fields-framework'),
	'repeatable' => true,
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-nested-repeatable-group-field');

ff_add_field_to_field_group('ff-field-testing-a-nested-repeatable-group-field', 'ff-field-testing-a-group-field');

ff_create_field('ff-field-testing-a-nested-nested-group-field', 'group', array(
	'label' => __('A nested nested group field', 'fields-framework'),
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-nested-nested-group-field');

ff_add_field_to_field_group('ff-field-testing-a-nested-nested-group-field', 'ff-field-testing-a-nested-group-field');

ff_create_field('ff-field-testing-a-nested-nested-repeatable-group-field', 'group', array(
	'label' => __('A nested nested repeatable group field', 'fields-framework'),
	'repeatable' => true,
));

ff_add_field_to_section('ff-field-testing', 'ff-field-testing-a-nested-nested-repeatable-group-field');

ff_add_field_to_field_group('ff-field-testing-a-nested-nested-repeatable-group-field', 'ff-field-testing-a-nested-repeatable-group-field');
?>