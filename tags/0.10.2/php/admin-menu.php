<?php
add_action('init', 'ff_fields_framework');

function ff_fields_framework() {
	ff_create_section('ff-admin-menu', 'admin_menu', array(
		'page_title' => __('Fields Framework', 'fields-framework'),
		'menu_title' => __('Fields Framework', 'fields-framework'),
	));
	
	ff_create_field('ff-admin-menu-enable-field-testing-and-demo-fields', 'radio', array(
		'label' => __('Enable Field Testing and Demo Fields', 'fields-framework'),
		'options' => array('0' => __('No', 'fields-framework'), '1' => __('Yes', 'fields-framework')),
		'default_value' => '0'
	));
	
	ff_add_field_to_section('ff-admin-menu', 'ff-admin-menu-enable-field-testing-and-demo-fields');
	
	$enable_field_testing = ff_get_field_from_section('ff-admin-menu', 'ff-admin-menu-enable-field-testing-and-demo-fields', 'options');
	
	if(!empty($enable_field_testing)) {
		require_once(plugin_dir_path(__FILE__) . 'field-testing.php');

		require_once(plugin_dir_path(__FILE__) . 'demo-fields.php');

		add_action('ff_section_after', 'ff_field_testing_after');
	}
}

function ff_field_testing_after($section_uid) {
	if($section_uid == 'ff-field-testing') {
		echo '<pre>';

		print_r(ff_get_all_fields_from_section($section_uid, 'options'));

		echo '</pre>';
	}
}
?>