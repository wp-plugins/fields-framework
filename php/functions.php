<?php
/**
 * @package WordPress
 * @subpackage Fields Framework
 */

/**
 * @param array $arguments must contain two variables viz. uid (a unique identifier) and type (currently supported type of section include admin_menu, admin_sub_menu, post, taxonomy and user)
 * @return void
 */
if(!function_exists('ff_create_section')) {
	function ff_create_section($uid, $type, $arguments = null, $skip_registry = false) {
		$uid = trim($uid);
	
		if(empty($uid)) {
			ff_throw_exception(__('Empty Section UID', 'fields-framework'));
		}

		if($skip_registry == false && array_key_exists($uid, FF_Registry::$sections)) {
			ff_throw_exception(__('Duplicate Section UID', 'fields-framework'));
		}

		$arguments['uid'] = $uid;
	
		$type = trim($type);
	
		if(empty($type)) {
			ff_throw_exception(__('Empty Section Type', 'fields-framework'));
		}

		if($type == 'admin_menu' || $type == 'admin_sub_menu') {
			$arguments['menu_uid'] = $uid;
		}
		elseif($type == 'post') {
			$arguments['id'] = $uid;
		}
	
		$class_name = "FF_{$type}";

		if(class_exists($class_name)) {
			$section = new $class_name($arguments);
		}
		else {
			ff_throw_exception(__('Invalid Section Type', 'fields-framework'));
		}

		if($skip_registry == false) {
			/* Add this to the Registry so that it can later be referenced */
			FF_Registry::$sections[$uid] = $section;
		}
		
		return $section;
	}
}

/**
 * This function registers a field which is also associated with a section
 *
 * @param array $arguments
 * @return void
 */
if(!function_exists('ff_create_field')) {
	function ff_create_field($uid, $type, $arguments, $skip_registry = false) {
		$uid = trim($uid);
	
		if(empty($uid)) {
			ff_throw_exception(__('Empty Field UID', 'fields-framework'));
		}
	
		if($skip_registry == false && array_key_exists($uid, FF_Registry::$fields)) {
			ff_throw_exception(__('Duplicate Field UID', 'fields-framework'));
		}

		$arguments['uid'] = $uid;

		if(empty($arguments['name'])) {
			$arguments['name'] = $uid;
		}

		if(empty($arguments['id'])) {
			$arguments['id'] = $uid;
		}

		$type = trim($type);
	
		if(empty($type)) {
			ff_throw_exception(__('Empty Section Type', 'fields-framework'));
		}

		$class_name = "FF_Field_{$type}";

		if(class_exists($class_name)) {
			$field = new $class_name($arguments);
		}
		else {
			ff_throw_exception(__('Invalid Field Type', 'fields-framework'));
		}

		if($skip_registry == false) {
			/* Add this to the Registry so that it can later be referenced */
			FF_Registry::$fields[$uid] = $field;
		}
		
		return $field;
	}
}

if(!function_exists('ff_add_field_to_section')) {
	function ff_add_field_to_section($section_uid, $field_uid) {
		if(empty(FF_Registry::$sections[$section_uid])) {
			ff_throw_exception(__('Invalid Section UID', 'fields-framework'));
		}
		
		if(empty(FF_Registry::$fields[$field_uid])) {
			ff_throw_exception(__('Invalid Field UID', 'fields-framework'));
		}

		FF_Registry::$fields_by_sections[$section_uid][$field_uid] = FF_Registry::$fields[$field_uid];
	}
}

if(!function_exists('ff_add_field_to_field_group')) {
	function ff_add_field_to_field_group($field_group_uid, $field_uid) {
		if(empty(FF_Registry::$fields[$field_group_uid])) {
			ff_throw_exception(__('Invalid Field Group UID', 'fields-framework'));
		}
		
		if(empty(FF_Registry::$fields[$field_uid])) {
			ff_throw_exception(__('Invalid Field UID', 'fields-framework'));
		}

		FF_Registry::$fields[$field_group_uid]->add_field(FF_Registry::$fields[$field_uid]);
	}
}

if(!function_exists('ff_render_section')) {
	function ff_render_section($section_uid, $source, $source_type = null, $object_id = null) {
		do_action('ff_section_before', $section_uid);

		if(!empty(FF_Registry::$fields_by_sections[$section_uid])) {
			if($source == 'options' && $source_type == null) {
				echo '<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $section_uid . '" method="post">';
			}
	
			if($source != 'custom') {
				wp_nonce_field('ff-save', 'ff-nonce');
			}
	
			ff_render_fields(FF_Registry::$fields_by_sections[$section_uid], $source, $source_type, $object_id);
	
			if($source == 'options' && $source_type == null) {
				$section_uid_field = ff_create_field('ff-section-uid', 'hidden', array('value' => $section_uid), true);
	
				$section_uid_field->container();
		
				submit_button();
			
				echo '</form>';
			}
		}

		do_action('ff_section_after', $section_uid);
	}
}

/*
 * @param array $fields
 * @param string $source specifies whether the source is going to be options or meta
 * @param string $source_type if source is meta then this specifies whether it's type will be post meta or user meta
 * @param int $object_id if source is meta then this specifies the object's id. Or else if source is taxonomy then this specifies the term's id
 * @return void
*/
if(!function_exists('ff_render_fields')) {
	function ff_render_fields($fields, $source, $source_type = null, $object_id = null) {
		foreach($fields as $field_uid => $field) {
			if($source == 'options') {
				$field->get_from_options($source_type, $object_id);
			}
			elseif($source == 'meta') {
				$field->get_from_meta($source_type, $object_id);
			}
			elseif($source == 'custom') {
				$field->get_from_custom($source_type, $object_id);
			}

			do_action('ff_field_before', $field_uid);

			$field->container();

			do_action('ff_field_after', $field_uid);
		}
	}
}

/*
 * @param string Unique ID of section
 * @param string Unique ID of field
 * @param string $source specifies whether the source is going to be options or meta
 * @param string $source_type if source is meta then this specifies whether it's type will be post meta or user meta
 * @param int $object_id if source is meta then this specifies the object's id. Or else if source is taxonomy then this specifies the term's id
 * @return string|array
*/
if(!function_exists('ff_get_field_from_section')) {
	function ff_get_field_from_section($section_uid, $field_uid, $source, $source_type = null, $object_id = null) {
		if(empty(FF_Registry::$sections[$section_uid])) {
			ff_throw_exception(__('Invalid Section UID', 'fields-framework'));
		}
		
		if(empty(FF_Registry::$fields[$field_uid])) {
			ff_throw_exception(__('Invalid Field UID', 'fields-framework'));
		}

		if(empty(FF_Registry::$fields_by_sections[$section_uid][$field_uid])) {
			ff_throw_exception(__('Field UID does not exist in Section specified by Section UID', 'fields-framework'));
		}

		$field = FF_Registry::$fields_by_sections[$section_uid][$field_uid];

		if($source == 'options') {
			return $field->get_from_options($source_type, $object_id);
		}
		elseif($source == 'meta') {
			return $field->get_from_meta($source_type, $object_id);
		}
		elseif($source == 'custom') {
			return $field->get_from_custom($source_type, $object_id);
		}
	}
}

/*
 * @param string Unique ID of section
 * @param string $source specifies whether the source is going to be options or meta
 * @param string $source_type if source is meta then this specifies whether it's type will be post meta or user meta
 * @param int $object_id if source is meta then this specifies the object's id. Or else if source is taxonomy then this specifies the term's id
 * @return array
*/
if(!function_exists('ff_get_all_fields_from_section')) {
	function ff_get_all_fields_from_section($section_uid, $source, $source_type = null, $object_id = null) {
		if(empty(FF_Registry::$sections[$section_uid])) {
			ff_throw_exception(__('Invalid Section UID', 'fields-framework'));
		}

		if(empty(FF_Registry::$fields_by_sections[$section_uid])) {
			ff_throw_exception(__('No Fields exist in Section specified by Section UID', 'fields-framework'));
		}

		$values = null;

		foreach(FF_Registry::$fields_by_sections[$section_uid] as $field_uid => $field) {
			if($source == 'options') {
				$values[$field_uid] = $field->get_from_options($source_type, $object_id);
			}
			elseif($source == 'meta') {
				$values[$field_uid] = $field->get_from_meta($source_type, $object_id);
			}
			elseif($source == 'custom') {
				$values[$field_uid] = $field->get_from_custom($source_type, $object_id);
			}
		}

		return $values;
	}
}

if(!function_exists('ff_throw_exception')) {
	function ff_throw_exception($message) {
		set_exception_handler('ff_exception_handler');

		throw new Exception(__($message, 'fields-framework'));
		
		restore_exception_handler();
	}
}

if(!function_exists('ff_exception_handler')) {
	function ff_exception_handler($exception) {
		$traces = $exception->getTrace();

		$trace = $traces[1];

		echo 'Uncaught Error Exception: ' . $exception->getMessage() . "\n<br />";

		echo "File: {$trace['file']}\n<br />";

		echo "Line: {$trace['line']}\n<br />";

		echo "Function: {$trace['function']}\n<br />";
		
		exit;
	}
}

if(!function_exists('ff_admin_enqueue_scripts')) {
	function ff_admin_enqueue_scripts() {
		wp_enqueue_style('ff-backend', FF_Registry::$plugins_url . '/css/backend.css');

		wp_enqueue_script('ff-backend', FF_Registry::$plugins_url . '/js/backend.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
	}
}

if(!function_exists('ff_verify_nonce')) {
	function ff_verify_nonce() {
		return empty($_POST['ff-nonce']) || !wp_verify_nonce($_POST['ff-nonce'], 'ff-save');
	}
}

if(!function_exists('ff_get_attachment_id_by_url')) {
	function ff_get_attachment_id_by_url($url) {
		$upload_directory = wp_upload_dir();

		$file = str_replace($upload_directory['baseurl'] . '/', null, $url);

		$arguments = array(
			'post_type' => 'attachment',
			'post_status' => 'any',
			'posts_per_page' => 1,
			'meta_query' =>	array(
				array(
					'key' => '_wp_attached_file',
					'value' => $file,
				)
			)
		);

		$attachment_id = null;

		$attachments = get_posts($arguments);

		if(!empty($attachments)) {
			$attachment = array_shift($attachments);

			$attachment_id = $attachment->ID;
		}
		
		return $attachment_id;
	}
}

if(!function_exists('ff_validator_attributes')) {
	function ff_validator_attributes($attributes) {
		foreach($attributes as $key => $value) {
			echo ' data-' . $key . '="' . $value . '"';
		}
	}
}

if(!function_exists('ff_set_object_defaults')) {
	function ff_set_object_defaults($object, $arguments) {
		$reflection = new ReflectionClass($object); 

		$properties = $reflection->getProperties();

		foreach($properties as $property) {
			$property_name = $property->getName();

			if(isset($arguments[$property_name])) {
				if(!is_scalar($arguments[$property_name]) || is_bool($arguments[$property_name])) {
					$property_value = $arguments[$property_name];
				}
				else {
					$property_value = trim($arguments[$property_name]);
				}

				$property->setValue($object, $property_value);
			}
		}
	}
}

if(!function_exists('ff_empty')) {
	function ff_empty($variable) {
		return empty($variable) && $variable !== 0 && $variable !== '0';
	}
}

if(!function_exists('ff_sanitize')) {
	function ff_sanitize($variable) {
		if(is_array($variable)) {
			foreach($variable as $key => $value) {
				$variable[$key] = ff_sanitize($value);

				if(ff_empty($variable[$key])) {
					unset($variable[$key]);
				}
			}
		}
		elseif(is_scalar($variable)) {
			$variable = trim($variable);

			$variable = stripslashes($variable);
		}

		if(ff_empty($variable)) {
			unset($variable);
		}
		else {	
			return $variable;
		}
	}
}
?>