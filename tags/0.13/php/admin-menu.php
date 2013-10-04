<?php
add_action('init', 'ff_fields_framework');

function ff_fields_framework() {
	ff_create_section('ff-admin-menu', 'admin_menu', array(
		'page_title' => __('Fields Framework', 'fields-framework'),
		'menu_title' => __('Fields Framework', 'fields-framework'),
	));

	add_action('ff_section_before', 'ff_fields_framework_admin_menu');
	
	ff_create_field('ff-admin-menu-disable-builder', 'radio', array(
		'label' => __('Disable Builder', 'fields-framework'),
		'options' => array('0' => __('No', 'fields-framework'), '1' => __('Yes', 'fields-framework')),
		'default_value' => '0'
	));
	
	ff_add_field_to_section('ff-admin-menu', 'ff-admin-menu-disable-builder');

	$disable_builder = ff_get_field_from_section('ff-admin-menu', 'ff-admin-menu-disable-builder', 'options');

	if(empty($disable_builder)) {
		require_once(plugin_dir_path(__FILE__) . 'builder.php');
	}

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
	}
}

function ff_fields_framework_admin_menu($section_uid) {
	if($section_uid == 'ff-admin-menu') {
		?>
		<h2><?php _e('Fields Framework', 'fields-framework'); ?></h2>

		<p><a href="http://www.rhyzz.com/fields-framework.html" target="_blank"><?php _e('WordPress Fields Framework Documentation', 'fields-framework'); ?></a></p>
		<?php
	}
}
?>