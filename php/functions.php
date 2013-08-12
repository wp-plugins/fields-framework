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
	function ff_create_section($uid, $type, $arguments = null) {
		$uid = trim($uid);
	
		if(empty($uid)) {
			ff_throw_exception(__('Empty Section UID', 'fields-framework'));
		}
	
		if(array_key_exists($uid, FF_Registry::$sections)) {
			ff_throw_exception(__('Duplicate Section UID', 'fields-framework'));
		}
	
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
	
		/* Add this to the Registry so that it can later be referenced */
		FF_Registry::$sections[$uid] = $section;
	}
}

/**
 * This function registers a field which is also associated with a section
 *
 * @param array $arguments
 * @return void
 */
if(!function_exists('ff_create_field')) {
	function ff_create_field($uid, $type, $arguments) {
		$uid = trim($uid);
	
		if(empty($uid)) {
			ff_throw_exception(__('Empty Field UID', 'fields-framework'));
		}
	
		if(array_key_exists($uid, FF_Registry::$fields)) {
			ff_throw_exception(__('Duplicate Field UID', 'fields-framework'));
		}

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

		/* Add this to the Registry so that it can later be referenced */
		FF_Registry::$fields[$uid] = $field;
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

			do_action("ff_field_before", $field_uid);

			$field->container();

			do_action("ff_field_after", $field_uid);
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
		}

		return $values;
	}
}

/**
 * This function is responsible for all registered sections.
 *
 * @param void
 * @return void
 */
if(!function_exists('ff_admin_menu')) {
	function ff_admin_menu() {
		/* Because this action is always called even if no sections are registered, first check if any section is registered or not. */
		if(empty(FF_Registry::$sections)) {
			return;
		}

		foreach(FF_Registry::$sections as $section_uid => $section) {
			$class_name = get_class($section);

			do_action("ff_section_before", $section_uid);

			switch($class_name) {
				case 'FF_Admin_Menu':
					add_menu_page($section->page_title, $section->menu_title, $section->capability, $section->menu_uid, 'ff_admin_section', $section->icon_url, $section->position);
				break;

				case 'FF_Admin_Sub_Menu':
					add_submenu_page($section->parent_uid, $section->page_title, $section->menu_title, $section->capability, $section->menu_uid, 'ff_admin_section');
				break;

				case 'FF_Post':
					/* The following conditionals basically insure the relevant actions are only called if sections pertaining to them exist */

					if(in_array('attachment', $section->post_types)) {
						add_action('edit_attachment', 'ff_save_post');
					}
					else {
						add_action('save_post', 'ff_save_post');
					}

					if(!empty($_GET['post'])) {
						$post = get_post($_GET['post']);
					}

					foreach($section->post_types as $post_type) {
						/*
							Post meta box will be displayed even on posts or pages not saved hence such posts can't have a page template or post format, because they aren't saved.
							Hence continue with next section.
						*/
						if((!empty($section->page_templates) || !empty($section->post_formats)) && empty($post)) {
							continue;
						}

						/* Check if this section requires a page template. This check is ignored if the post type doesn't support this feature. */
						if(!empty($section->page_templates) && post_type_supports($post->post_type, 'page-attributes')) {
							$page_template = get_post_meta($post->ID, '_wp_page_template', true);

							/* Selected page does not have the required page template hence continue */
							if(!in_array($page_template, $section->page_templates)) {
								continue;
							}
						}

						/* Check if this section requires a post format. This check is ignored if the post type doesn't support this feature. */
						if(!empty($section->post_formats) && post_type_supports($post->post_type, 'post-formats')) {
							$post_format = get_post_format($post->ID);

							/* Selected post does not have the required post format hence continue */
							if(!in_array($post_format, $section->post_formats)) {
								continue;
							}
						}

						add_meta_box($section->id, $section->title, 'ff_post_section', $post_type, $section->context, $section->priority);
					}
				break;

				case 'FF_Taxonomy':
					foreach($section->taxonomies as $taxonomy) {
						add_action("{$taxonomy}_add_form_fields", 'ff_taxonomy_add_form_fields');
		
						add_action("{$taxonomy}_edit_form_fields", 'ff_taxonomy_edit_form_fields', 10, 2);
					}
				break;

				case 'FF_User':
					/* The following basically insures the relevant actions are only called if sections pertaining to them exist */

					add_action('personal_options_update', 'ff_save_user');
			
					add_action('edit_user_profile_update', 'ff_save_user');

					add_action('show_user_profile', 'ff_user_section');
		
					add_action('edit_user_profile', 'ff_user_section');
				break;
			}

			do_action("ff_section_after", $section_uid);
		}
	}
}

if(!function_exists('ff_admin_section')) {
	function ff_admin_section() {
		$section_uid = $_GET['page'];
	
		$section = FF_Registry::$sections[$section_uid];
	
		echo '<div class="wrap">';

		if(!empty($_GET['ff-updated'])) {
			echo '<div class="updated fade"><p>' . __('Settings saved.', 'fields-framework') . '</p></div>';
		}

		echo '<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $section_uid . '" method="post">';
	
		wp_nonce_field('ff-options', 'ff-options-nonce');

		ff_create_field('ff-section-uid', 'hidden', array('value' => $section_uid));

		ff_add_field_to_section($section_uid, 'ff-section-uid');
	
		ff_render_fields(FF_Registry::$fields_by_sections[$section_uid], 'options');

		submit_button();
	
		echo '</form>';
		
		echo '</div>';
	}
}

if(!function_exists('ff_save_options')) {
	function ff_save_options() {
		/* Because the hook that calls this function always runs, make sure there are sections registered and that we are inside an administration menu save process */
		if(empty(FF_Registry::$sections) || empty($_POST['ff-section-uid']) || empty($_POST['ff-options-nonce']) || !wp_verify_nonce($_POST['ff-options-nonce'], 'ff-options')) {
			return;
		}

		$section_uid = $_POST['ff-section-uid'];

		foreach(FF_Registry::$fields_by_sections[$section_uid] as $field) {
			$field->save_to_options();
		}

		if(!empty($_POST['_wp_http_referer'])) {
			$location = $_POST['_wp_http_referer'];

			if(strpos($location, 'ff-updated') === false) {
				$location .= '&ff-updated=true';
			}

			header('HTTP/1.1 303 See Other');

			header("Location: {$location}");

			exit;
		}
	}
}

if(!function_exists('ff_post_section')) {
	function ff_post_section($post) {
		foreach(FF_Registry::$sections as $section_uid => $section) {
			if(!is_a($section, 'FF_Post') || empty(FF_Registry::$fields_by_sections[$section_uid]) || !in_array($post->post_type, $section->post_types)) {
				continue;
			}

			if(!empty($section->page_templates) && post_type_supports($post->post_type, 'page-attributes')) {
				$page_template = get_post_meta($post->ID, '_wp_page_template', true);
				
				if(!in_array($page_template, $section->page_templates)) {
					continue;
				}
			}

			if(!empty($section->post_formats) && post_type_supports($post->post_type, 'post-formats')) {
				$post_format = get_post_format($post->ID);
				
				if(!in_array($post_format, $section->post_formats)) {
					continue;
				}
			}

			wp_nonce_field('ff-meta', 'ff-meta-nonce');

			ff_render_fields(FF_Registry::$fields_by_sections[$section_uid], 'meta', 'post', $post->ID);
		}
	}
}

if(!function_exists('ff_save_post')) {
	function ff_save_post($post_id) {
		if(empty($_POST['ff-meta-nonce']) || !wp_verify_nonce($_POST['ff-meta-nonce'], 'ff-meta')) {
			return;
		}
	
		$post = get_post($post_id);

		foreach(FF_Registry::$sections as $section_uid => $section) {
			if(!is_a($section, 'FF_Post') || empty(FF_Registry::$fields_by_sections[$section_uid]) || !in_array($post->post_type, $section->post_types)) {				
				continue;
			}
			
			if(!empty($section->page_templates) && post_type_supports($post->post_type, 'page-attributes')) {
				$page_template = get_post_meta($post->ID, '_wp_page_template', true);

				if(!in_array($page_template, $section->page_templates)) {
					continue;
				}
			}

			if(!empty($section->post_formats) && post_type_supports($post->post_type, 'post-formats')) {
				$post_format = get_post_format($post->ID);
				
				if(!in_array($post_format, $section->post_formats)) {
					continue;
				}
			}

			foreach(FF_Registry::$fields_by_sections[$section_uid] as $field) {
				$field->save_to_meta('post', $post_id);
			}
		}
	}
}

if(!function_exists('ff_taxonomy_add_form_fields')) {
	function ff_taxonomy_add_form_fields($taxonomy) {
		ff_taxonomy_form_fields($taxonomy);
	}
}

if(!function_exists('ff_taxonomy_edit_form_fields')) {
	function ff_taxonomy_edit_form_fields($tag, $taxonomy) {
		ff_taxonomy_form_fields($taxonomy, $tag);
	}
}

if(!function_exists('ff_taxonomy_form_fields')) {
	function ff_taxonomy_form_fields($taxonomy, $tag = null) {
		$taxonomy = $_GET['taxonomy'];
	
		$ttid = null;
	
		if(!empty($tag)) {
			$term = get_term($tag, $taxonomy);
	
			$ttid = $term->term_taxonomy_id;
		}
	
		foreach(FF_Registry::$sections as $section_uid => $section) {
			if(!is_a($section, 'FF_Taxonomy') || empty(FF_Registry::$fields_by_sections[$section_uid]) || !in_array($taxonomy, $section->taxonomies)) {
				continue;
			}

			if(!empty($ttid)) {
				echo '<tr><td colspan="2">';
			}

			wp_nonce_field('ff-options', 'ff-options-nonce');

			ff_render_fields(FF_Registry::$fields_by_sections[$section_uid], 'options', 'taxonomy', $ttid);

			if(!empty($ttid)) {
				echo '</td></tr>';
			}
		}
	}
}

if(!function_exists('ff_save_term')) {
	function ff_save_term($term_id, $tt_id, $taxonomy) {
		/* Because the hook that calls this function always runs, make sure there are sections registered */
		if(empty(FF_Registry::$sections) || empty($_POST['ff-options-nonce']) || !wp_verify_nonce($_POST['ff-options-nonce'], 'ff-options')) {
			return;
		}

		foreach(FF_Registry::$sections as $section_uid => $section) {
			if(!is_a($section, 'FF_Taxonomy') || empty(FF_Registry::$fields_by_sections[$section_uid]) || !in_array($taxonomy, $section->taxonomies)) {
				continue;
			}

			foreach(FF_Registry::$fields_by_sections[$section_uid] as $field) {
				$field->save_to_options('taxonomy', $tt_id);
			}
		}
	}
}

if(!function_exists('ff_delete_term')) {
	function ff_delete_term($term, $tt_id, $taxonomy, $deleted_term) {
		/* Because the hook that calls this function always runs, make sure there are sections registered */
		if(empty(FF_Registry::$sections)) {
			return;
		}

		foreach(FF_Registry::$sections as $section_uid => $section) {
			if(!is_a($section, 'FF_Taxonomy') || empty(FF_Registry::$fields_by_sections[$section_uid]) || !in_array($taxonomy, $section->taxonomies)) {
				continue;
			}

			foreach(FF_Registry::$fields_by_sections[$section_uid] as $field) {
				$field->delete_from_options($tt_id);
			}
		}
	}
}

if(!function_exists('ff_user_section')) {
	function ff_user_section($user) {
		foreach(FF_Registry::$sections as $section_uid => $section) {
			if(!is_a($section, 'FF_User') || empty(FF_Registry::$fields_by_sections[$section_uid])) {
				continue;
			}
	
			wp_nonce_field('ff-meta', 'ff-meta-nonce');

			ff_render_fields(FF_Registry::$fields_by_sections[$section_uid], 'meta', 'user', $user->ID);
		}
	}
}

if(!function_exists('ff_save_user')) {
	function ff_save_user($user_id) {
		if(empty($_POST['ff-meta-nonce']) || !wp_verify_nonce($_POST['ff-meta-nonce'], 'ff-meta')) {
			return;
		}

		foreach(FF_Registry::$sections as $section_uid => $section) {
			if(!is_a($section, 'FF_User') || empty(FF_Registry::$fields_by_sections[$section_uid])) {
				continue;
			}

			foreach(FF_Registry::$fields_by_sections[$section_uid] as $field) {
				$field->save_to_meta('user', $user_id);
			}
		}
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

		echo 'Uncaught Error Exception: ' . $exception->getMessage() .  "\n<br />";

		echo "File: {$trace['file']}\n<br />";

		echo "Line: {$trace['line']}\n<br />";

		echo "Function: {$trace['function']}\n<br />";
		
		exit;
	}
}

if(!function_exists('ff_admin_enqueue_scripts')) {
	function ff_admin_enqueue_scripts() {
		wp_enqueue_style('ff-backend', FF_Registry::$plugins_url . '/css/backend.css');

		wp_enqueue_script('ff-placeholder', FF_Registry::$plugins_url . '/js/jquery.placeholder.js', array('jquery'));

		wp_enqueue_script('ff-backend', FF_Registry::$plugins_url . '/js/backend.js', array('jquery'));
	}
}

if(!function_exists('ff_set_class_defaults')) {
	function ff_set_object_defaults($object, $arguments) {
		$reflection = new ReflectionClass($object); 

		$properties = $reflection->getProperties();

		foreach($properties as $property) {
			$property->setAccessible(true);

			$property_name = $property->getName();

			if(isset($arguments[$property_name])) {
				if(is_array($arguments[$property_name])) {
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