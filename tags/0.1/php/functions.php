<?php
/**
 * @package WordPress
 * @subpackage Fields Framework
 */

/**
 * @param array $arguments must contain two variables viz. uid (a unique identifier) and type (type of section)
 * @return void
 */
if(!function_exists('ff_create_section')) {
	function ff_create_section($uid, $type, $arguments = null) {
		$uid = trim($uid);
	
		if(empty($uid)) {
			trigger_error(__('Empty Section UID', 'ff'), E_USER_ERROR);
		}
	
		if(array_key_exists($uid, FF_Registry::$sections)) {
			trigger_error(__('Duplicate Section UID', 'ff'), E_USER_ERROR);
		}
	
		$type = trim($type);
	
		if(empty($type)) {
			trigger_error(__('Empty Section Type', 'ff'), E_USER_ERROR);
		}

		if($type == 'admin_menu' || $type == 'admin_sub_menu') {
			if(empty($arguments['menu_slug'])) {
				$arguments['menu_slug'] = $uid;
			}
		}
	
		$class_name = "FF_{$type}";

		if(class_exists($class_name)) {
			$section = new $class_name($arguments);
		}
		else {
			trigger_error(__('Invalid Section Type', 'ff'), E_USER_ERROR);
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
			trigger_error(__('Empty Field UID', 'ff'), E_USER_ERROR);
		}
	
		if(array_key_exists($uid, FF_Registry::$fields)) {
			trigger_error(__('Duplicate Field UID', 'ff'), E_USER_ERROR);
		}

		if(empty($arguments['name'])) {
			$arguments['name'] = $uid;
		}

		$type = trim($type);
	
		if(empty($type)) {
			trigger_error(__('Empty Section Type', 'ff'), E_USER_ERROR);
		}

		$class_name = "FF_Field_{$type}";

		if(class_exists($class_name)) {
			$field = new $class_name($arguments);
		}
		else {
			trigger_error(__('Invalid Field Type', 'ff'), E_USER_ERROR);
		}
		
		/* Add this to the Registry so that it can later be referenced */
		FF_Registry::$fields[$uid] = $field;
	}
}

if(!function_exists('ff_add_field_to_section')) {
	function ff_add_field_to_section($section_uid, $field_uid) {
		FF_Registry::$fields_by_sections[$section_uid][$field_uid] = FF_Registry::$fields[$field_uid];
	}
}

if(!function_exists('ff_add_field_to_field_group')) {
	function ff_add_field_to_field_group($field_group_uid, $field_uid) {
		FF_Registry::$fields[$field_group_uid]->add_field(FF_Registry::$fields[$field_uid]);
	}
}

if(!function_exists('ff_save_options')) {
	function ff_save_options() {
		if(empty($_POST['ff-section-name']) || !wp_verify_nonce($_POST['ff-options-nonce'], 'ff-options')) {
			return;
		}
	
		$section_name = $_POST['ff-section-name'];
	
		$section = FF_Registry::$sections[$section_name];
	
		if(!is_a($section, 'FF_Admin_Menu') && !is_a($section, 'FF_Admin_Sub_Menu')) {
			return;
		}
	
		if(empty(FF_Registry::$fields_by_sections[$section_name])) {
			continue;
		}
	
		foreach(FF_Registry::$fields_by_sections[$section_name] as $field) {
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

if(!function_exists('ff_admin_menu')) {
	function ff_admin_menu() {
		if(empty(FF_Registry::$sections)) {
			return;
		}

		foreach(FF_Registry::$sections as $section_name => $section) {
			if(empty(FF_Registry::$fields_by_sections[$section_name])) {
				continue;
			}

			if(is_a($section, 'FF_Post')) {
				foreach($section->post_types as $post_type) {
					add_meta_box($section->id, $section->title, 'ff_post_section', $post_type, $section->context, $section->priority);
				}
			}
			elseif(is_a($section, 'FF_Taxonomy')) {
				foreach($section->taxonomies as $taxonomy) {
					add_action("{$taxonomy}_add_form_fields", 'ff_taxonomy_add_form_fields');
	
					add_action("{$taxonomy}_edit_form_fields", 'ff_taxonomy_edit_form_fields', 10, 2);
				}
			}
			elseif(is_a($section, 'FF_User')) {
				add_action('show_user_profile', 'ff_user_section');
	
				add_action('edit_user_profile', 'ff_user_section');
			}
			elseif(is_a($section, 'FF_Admin_Menu')) {
				add_menu_page($section->page_title, $section->menu_title, $section->capability, $section->menu_slug, 'ff_admin_section', $section->icon_url, $section->position);
			}
			elseif(is_a($section, 'FF_Admin_Sub_Menu')) {
				add_submenu_page($section->parent_slug, $section->page_title, $section->menu_title, $section->capability, $section->menu_slug, 'ff_admin_section');
			}
		}
	}
}

/*
 * @param array $fields
 * @param string $source specifies whether the source is going to be options or meta
 * @param string $source_type if source is meta then this specifies whether it's type will be post meta or user meta
 * @param int $object_id if source is meta then this specifies the object's id
 * @return void
*/
if(!function_exists('ff_render_fields')) {
	function ff_render_fields($fields, $source, $source_type = null, $object_id = null) {
		foreach($fields as $field) {
			if($source == 'options') {
				$field->get_from_options($source_type, $object_id);
			}
			elseif($source == 'meta') {
				$field->get_from_meta($source_type, $object_id);
			}
	
			$field->container();
		}
	}
}

if(!function_exists('ff_admin_section')) {
	function ff_admin_section() {
		$section_name = $_GET['page'];
	
		if(empty(FF_Registry::$sections[$section_name])) {
			return;
		}
	
		$section = FF_Registry::$sections[$section_name];
	
		if(empty(FF_Registry::$fields_by_sections[$section_name]) || $section->menu_slug != $section_name) {
			return;
		}
	
		echo '<div class="wrap">';

		if(!empty($_GET['ff-updated'])) {
			echo '<div class="updated fade"><p>' . __('Settings saved.', 'ff') . '</p></div>';
		}

		echo "<h2>{$section->page_title}</h2>";
	
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $section_name . '" method="post">';
	
		wp_nonce_field('ff-options', 'ff-options-nonce');

		ff_create_field('ff-section-name', 'hidden', array(
			'name' => 'ff-section-name',
			'value' => $section_name,
		));

		ff_add_field_to_section($section_name, 'ff-section-name');
	
		ff_render_fields(FF_Registry::$fields_by_sections[$section_name], 'options');

		submit_button();
	
		echo '</form>';
		
		echo '</div>';
	}
}

if(!function_exists('ff_post_section')) {
	function ff_post_section($post, $arguments) {
		if(empty(FF_Registry::$sections)) {
			return;
		}
	
		foreach(FF_Registry::$sections as $section_name => $section) {
			if(!is_a($section, 'FF_Post') || empty(FF_Registry::$fields_by_sections[$section_name]) || !in_array($post->post_type, $section->post_types)) {
				continue;
			}
	
			wp_nonce_field('ff-meta', 'ff-meta-nonce');

			ff_create_field('ff-section-name', 'hidden', array(
				'name' => 'ff-section-name',
				'value' => $section_name,
			));
	
			ff_add_field_to_section($section_name, 'ff-section-name');

			ff_render_fields(FF_Registry::$fields_by_sections[$section_name], 'meta', 'post', $post->ID);
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
		
		if(empty(FF_Registry::$sections)) {
			return;
		}
	
		$ttid = null;
	
		if(!empty($tag)) {
			$term = get_term($tag, $taxonomy);
	
			$ttid = $term->term_taxonomy_id;
		}
	
		foreach(FF_Registry::$sections as $section_name => $section) {
			if(!is_a($section, 'FF_Taxonomy') || empty(FF_Registry::$fields_by_sections[$section_name]) || !in_array($taxonomy, $section->taxonomies)) {
				continue;
			}

			if(!empty($ttid)) {
				echo '<tr><td colspan="2">';
			}

			wp_nonce_field('ff-options', 'ff-options-nonce');

			ff_create_field('ff-section-name', 'hidden', array(
				'name' => 'ff-section-name',
				'value' => $section_name,
			));
	
			ff_add_field_to_section($section_name, 'ff-section-name');

			ff_render_fields(FF_Registry::$fields_by_sections[$section_name], 'options', 'taxonomy', $ttid);

			if(!empty($ttid)) {
				echo '</td></tr>';
			}
		}
	}
}

if(!function_exists('ff_user_section')) {
	function ff_user_section($user) {
		if(empty(FF_Registry::$sections)) {
			return;
		}
	
		foreach(FF_Registry::$sections as $section_name => $section) {
			if(!is_a($section, 'FF_User') || empty(FF_Registry::$fields_by_sections[$section_name])) {
				continue;
			}
	
			wp_nonce_field('ff-meta', 'ff-meta-nonce');

			ff_create_field('ff-section-name', 'hidden', array(
				'name' => 'ff-section-name',
				'value' => $section_name,
			));
	
			ff_add_field_to_section($section_name, 'ff-section-name');

			ff_render_fields(FF_Registry::$fields_by_sections[$section_name], 'meta', 'user', $user->ID);
		}
	}
}

if(!function_exists('ff_save_post')) {
	function ff_save_post($post_id) {
		if(empty($_POST['ff-section-name']) || !wp_verify_nonce($_POST['ff-meta-nonce'], 'ff-meta')) {
			return;
		}
	
		$section_name = $_POST['ff-section-name'];
	
		$section = FF_Registry::$sections[$section_name];
		
		if(empty(FF_Registry::$fields_by_sections[$section_name])) {
			continue;
		}
	
		foreach(FF_Registry::$fields_by_sections[$section_name] as $field) {
			$field->save_to_meta('post', $post_id);
		}
	}
}

if(!function_exists('ff_save_user')) {
	function ff_save_user($user_id) {
		if(empty($_POST['ff-section-name']) || !wp_verify_nonce($_POST['ff-meta-nonce'], 'ff-meta')) {
			return;
		}

		$section_name = $_POST['ff-section-name'];
	
		$section = FF_Registry::$sections[$section_name];

		if(empty(FF_Registry::$fields_by_sections[$section_name])) {
			continue;
		}
	
		foreach(FF_Registry::$fields_by_sections[$section_name] as $field) {
			$field->save_to_meta('user', $user_id);
		}
	}
}

if(!function_exists('ff_save_term')) {
	function ff_save_term($term_id, $tt_id, $taxonomy) {
		if(empty($_POST['ff-section-name']) || !wp_verify_nonce($_POST['ff-options-nonce'], 'ff-options')) {
			return;
		}

		$section_name = $_POST['ff-section-name'];

		$section = FF_Registry::$sections[$section_name];

		if(empty(FF_Registry::$fields_by_sections[$section_name])) {
			continue;
		}

		foreach(FF_Registry::$fields_by_sections[$section_name] as $field) {
			$field->save_to_options('taxonomy', $tt_id);
		}
	}
}

if(!function_exists('ff_delete_term')) {
	function ff_delete_term($term, $tt_id, $taxonomy, $deleted_term) {
		if(empty(FF_Registry::$sections)) {
			return;
		}

		foreach(FF_Registry::$sections as $section_name => $section) {
			if(empty(FF_Registry::$fields_by_sections[$section_name]) || !is_a($section, 'FF_Taxonomy') || !in_array($taxonomy, $section->taxonomies)) {
				continue;
			}

			foreach(FF_Registry::$fields_by_sections[$section_name] as $field) {
				$field->delete_from_options($tt_id);
			}
		}
	}
}

if(!function_exists('ff_admin_enqueue_scripts')) {
	function ff_admin_enqueue_scripts() {
		wp_enqueue_style('ff-backend', plugins_url('css/backend.css', dirname(__FILE__)));

		wp_enqueue_script('ff-backend', plugins_url('js/backend.js', dirname(__FILE__)), array('jquery'));
	}
}

if(!function_exists('ff_sanitize')) {
	function ff_sanitize($variable) {
		if(is_array($variable)) {
			foreach($variable as $key => $value) {
				$variable[$key] = ff_sanitize($value);

				if(empty($variable[$key]) && $variable[$key] !== 0 && $variable[$key] !== '0') {
					unset($variable[$key]);
				}
			}
		}
		elseif(is_scalar($variable)) {
			$variable = trim($variable);

			$variable = stripslashes($variable);
		}

		if(empty($variable) && $variable !== 0 && $variable !== '0') {
			unset($variable);
		}
		else {	
			return $variable;
		}
	}
}
?>