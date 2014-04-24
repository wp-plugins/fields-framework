<?php
/**
 * @package WordPress
 * @subpackage Fields Framework
 */

if(!class_exists('FF_Registry')) {
	abstract class FF_Registry {
		static $sections = array(), $fields = array(), $fields_by_sections = array();
		
		static $plugins_url;
	}
}

if(!class_exists('FF_Section')) {
	abstract class FF_Section {
		protected $uid, $skip_save = false;

		public function __construct($arguments) {
			ff_set_object_defaults($this, $arguments);
		}
	}
}

if(!class_exists('FF_Admin_Menus')) {	
	class FF_Admin_Menus extends FF_Section {
		public function __construct($arguments) {
			add_action('wp_loaded', array($this, 'save'));

			parent::__construct($arguments);
		}

		public function display() {
			$section_uid = $_GET['page'];

			if($section_uid != $this->uid) {
				return;
			}

			echo '<div class="wrap">';
	
			if(!empty($_GET['ff-updated'])) {
				echo '<div class="updated fade"><p>' . __('Settings saved.', 'fields-framework') . '</p></div>';
			}
	
			ff_render_section($this->uid, 'options');

			echo '</div>';
		}

		public function save() {
			if(empty($_POST['ff-section-uid']) || ff_verify_nonce()) {
				return;
			}
	
			$section_uid = $_POST['ff-section-uid'];

			if($section_uid != $this->uid) {
				return;
			}

			if($this->skip_save == false) {
				foreach(FF_Registry::$fields_by_sections[$this->uid] as $field) {
					$field->save_to_options();
				}
			}
	
			if(!empty($_POST['_wp_http_referer'])) {
				$location = $_POST['_wp_http_referer'];
	
				if($this->skip_save == false) {
					if(strpos($location, 'ff-updated') === false) {
						$location .= '&ff-updated=true';
					}
				}
	
				header('HTTP/1.1 303 See Other');
	
				header("Location: {$location}");
	
				exit;
			}
		}
	}
}

if(!class_exists('FF_Admin_Menu')) {	
	class FF_Admin_Menu extends FF_Admin_Menus {
		protected $page_title, $menu_title, $capability = 'manage_options', $menu_uid, $icon_url, $position;
		
		public function __construct($arguments) {
			parent::__construct($arguments);

			/* Page Title is required */
			if(empty($this->page_title)) {
				ff_throw_exception(__('Empty Menu Section Page Title', 'fields-framework'));
			}
	
			/* Menu Title is required */
			if(empty($this->menu_title)) {
				ff_throw_exception(__('Empty Menu Section Menu Title', 'fields-framework'));
			}
			
			add_action('admin_menu', array($this, 'add'), 1);
		}
		
		public function add() {
			add_menu_page($this->page_title, $this->menu_title, $this->capability, $this->menu_uid, array($this, 'display'), $this->icon_url, $this->position);
		}
	}
}

if(!class_exists('FF_Admin_Sub_Menu')) {
	class FF_Admin_Sub_Menu extends FF_Admin_Menus {
		protected $menu_uid, $parent_uid, $page_title, $menu_title, $capability = 'manage_options';
	
		public function __construct($arguments) {
			parent::__construct($arguments);

			/* Parent UID is required */
			if(empty($this->parent_uid)) {
				ff_throw_exception(__('Empty Sub Menu Section Parent UID', 'fields-framework'));
			}

			/* Page Title is required */
			if(empty($this->page_title)) {
				ff_throw_exception(__('Empty Sub Menu Section Page Title', 'fields-framework'));
			}
	
			/* Menu Title is required */
			if(empty($this->menu_title)) {
				ff_throw_exception(__('Empty Sub Menu Section Menu Title', 'fields-framework'));
			}

			add_action('admin_menu', array($this, 'add'), 1);
		}
		
		public function add() {
			add_submenu_page($this->parent_uid, $this->page_title, $this->menu_title, $this->capability, $this->menu_uid, array($this, 'display'));
		}
	}
}

if(!class_exists('FF_Post')) {
	class FF_Post extends FF_Section {
		protected $id, $title, $context = 'advanced', $priority = 'default';
		
		protected $post_types = array(), $page_templates = array(), $post_formats = array();
		
		protected $page_templates_not = false, $post_formats_not = false;

		protected $post_ids = array(), $post_titles = array(), $post_slugs = array();

		protected $post_ids_not = false, $post_titles_not = false, $post_slugs_not = false;

		protected $hide_content_editor = false;

		public function __construct($arguments) {
			parent::__construct($arguments);

			/* Post Types are required. Atleast one must be provided. */
			if(empty($this->post_types)) {
				ff_throw_exception(__('Empty Post Types', 'fields-framework'));
			}

			/* Title is required */
			if(empty($this->title)) {
				ff_throw_exception(__('Empty Meta Section Title', 'fields-framework'));
			}

			add_action('add_meta_boxes', array($this, 'add'), 10, 2);

			/* The following conditionals basically insure the relevant actions are only called if sections pertaining to them exist */
			if(in_array('attachment', $this->post_types)) {
				add_action('edit_attachment', array($this, 'save'));
			}
			else {
				add_action('save_post', array($this, 'save'));
			}
		}
		
		public function add($post_type, $post) {
			foreach($this->post_types as $post_type) {
				/* Post meta box will be displayed even on posts or pages not saved hence such posts can't have a page template or post format, because they aren't saved. */
				if((!empty($this->page_templates) || !empty($this->post_formats) || !empty($this->post_ids) || !empty($this->post_titles) || !empty($this->post_slugs)) && $post->post_status == 'auto-draft') {
					return;
				}

				/* Check if this section requires a page template. This check is ignored if the post type doesn't support this feature. */
				if(!empty($this->page_templates) && post_type_supports($post_type, 'page-attributes')) {
					$page_template = get_post_meta($post->ID, '_wp_page_template', true);

					/* Selected page does not have the required page template hence return */
					if($this->page_templates_not == false && !in_array($page_template, $this->page_templates)) {
						return;
					}
					/* Selected page has the required page template to be ignored hence return */
					elseif($this->page_templates_not == true && in_array($page_template, $this->page_templates)) {
						return;
					}
				}

				/* Check if this section requires a post format. This check is ignored if the post type doesn't support this feature. */
				if(!empty($this->post_formats) && post_type_supports($post_type, 'post-formats')) {
					$post_format = get_post_format($post->ID);

					/* Selected post does not have the required post format hence return */
					if($this->post_formats_not == false && !in_array($post_format, $this->post_formats)) {
						return;
					}
					/* Selected post has the required post format to be ignored hence return */
					elseif($this->post_formats_not == true && in_array($post_format, $this->post_formats)) {
						return;
					}
				}

				/* Check if this section requires a post ID. */
				if(!empty($this->post_ids)) {
					$post_id = $post->ID;

					/* Selected post does not have the required post ID hence return */
					if($this->post_ids_not == false && !in_array($post_id, $this->post_ids)) {
						return;
					}
					/* Selected post has the required post ID to be ignored hence return */
					elseif($this->post_ids_not == true && in_array($post_id, $this->post_ids)) {
						return;
					}
				}

				/* Check if this section requires a post Title. */
				if(!empty($this->post_titles)) {
					$post_title = $post->post_title;

					/* Selected post does not have the required post Title hence return */
					if($this->post_titles_not == false && !in_array($post_title, $this->post_titles)) {
						return;
					}
					/* Selected post has the required post Title to be ignored hence return */
					elseif($this->post_titles_not == true && in_array($post_title, $this->post_titles)) {
						return;
					}
				}

				/* Check if this section requires a post Slug. */
				if(!empty($this->post_slugs)) {
					$post_slug = $post->post_name;

					/* Selected post does not have the required post Slug hence return */
					if($this->post_slugs_not == false && !in_array($post_slug, $this->post_slugs)) {
						return;
					}
					/* Selected post has the required post Slug to be ignored hence return */
					elseif($this->post_slugs_not == true && in_array($post_slug, $this->post_slugs)) {
						return;
					}
				}

				add_meta_box($this->id, $this->title, array($this, 'display'), $post_type, $this->context, $this->priority);
			}
		}

		public function display($post, $meta_box) {
			if(!in_array($post->post_type, $this->post_types) || $meta_box['id'] != $this->uid) {
				return;
			}

			if(!empty($this->page_templates) && post_type_supports($post->post_type, 'page-attributes')) {
				$page_template = get_post_meta($post->ID, '_wp_page_template', true);
				
				if($this->page_templates_not == false && !in_array($page_template, $this->page_templates)) {
					return;
				}
				elseif($this->page_templates_not == true && in_array($page_template, $this->page_templates)) {
					return;
				}
			}

			if(!empty($this->post_formats) && post_type_supports($post->post_type, 'post-formats')) {
				$post_format = get_post_format($post->ID);
				
				if($this->post_formats_not == false && !in_array($post_format, $this->post_formats)) {
					return;
				}
				elseif($this->post_formats_not == true && in_array($post_format, $this->post_formats)) {
					return;
				}
			}

			if($this->hide_content_editor == true) {
				echo '<style type="text/css">#postdivrich {display: none;}</style>';
			}

			ff_render_section($this->uid, 'meta', 'post', $post->ID);
		}

		public function save($post_id) {
			if(ff_verify_nonce()) {
				return;
			}
		
			$post = get_post($post_id);
	
			if($this->skip_save != false || empty(FF_Registry::$fields_by_sections[$this->uid]) || !in_array($post->post_type, $this->post_types)) {				
				return;
			}
			
			if(!empty($this->page_templates) && post_type_supports($post->post_type, 'page-attributes')) {
				$page_template = get_post_meta($post->ID, '_wp_page_template', true);

				if($this->page_templates_not == false && !in_array($page_template, $this->page_templates)) {
					return;
				}
				elseif($this->page_templates_not == true && in_array($page_template, $this->page_templates)) {
					return;
				}
			}

			if(!empty($this->post_formats) && post_type_supports($post->post_type, 'post-formats')) {
				$post_format = get_post_format($post->ID);
				
				if($this->post_formats_not == false && !in_array($post_format, $this->post_formats)) {
					return;
				}
				elseif($this->post_formats_not == true && in_array($post_format, $this->post_formats)) {
					return;
				}
			}

			foreach(FF_Registry::$fields_by_sections[$this->uid] as $field) {
				$field->save_to_meta('post', $post_id);
			}
		}
	}
}

if(!class_exists('FF_Taxonomy')) {
	class FF_Taxonomy extends FF_Section {
		protected $taxonomies = array();
	
		public function __construct($arguments) {
			parent::__construct($arguments);

			/* Taxonomies are required. Atleast one must be provided. */
			if(empty($this->taxonomies)) {
				ff_throw_exception(__('Empty Taxonomies', 'fields-framework'));
			}
			
			$this->add();
			
			add_action('created_term', array($this, 'save'), 10, 3);

			add_action('edited_term', array($this, 'save'), 10, 3);

			add_action('delete_term', array($this, 'delete'), 10, 4);
		}
		
		public function add() {
			foreach($this->taxonomies as $taxonomy) {
				add_action("{$taxonomy}_add_form_fields", array($this, 'add_form_fields'));

				add_action("{$taxonomy}_edit_form_fields", array($this, 'edit_form_fields'), 10, 2);
			}
		}

		public function add_form_fields($taxonomy) {
			$this->display($taxonomy);
		}
	
		public function edit_form_fields($tag, $taxonomy) {
			$this->display($taxonomy, $tag);
		}

		public function display($taxonomy, $tag = null) {
			$ttid = null;

			if(!empty($tag)) {
				$ttid = $tag->term_taxonomy_id;
			}

			if(!in_array($taxonomy, $this->taxonomies)) {
				return;
			}

			if(!empty($ttid)) {
				echo '<tr><td colspan="2">';
			}

			ff_render_section($this->uid, 'options', 'taxonomy', $ttid);

			if(!empty($ttid)) {
				echo '</td></tr>';
			}
		}

		public function save($term_id, $tt_id, $taxonomy) {
			if(ff_verify_nonce()) {
				return;
			}
	
			if($this->skip_save != false || empty(FF_Registry::$fields_by_sections[$this->uid]) || !in_array($taxonomy, $this->taxonomies)) {
				return;
			}

			foreach(FF_Registry::$fields_by_sections[$this->uid] as $field) {
				$field->save_to_options('taxonomy', $tt_id);
			}
		}

		public function delete($term, $tt_id, $taxonomy, $deleted_term) {
			if($this->skip_save != false || empty(FF_Registry::$fields_by_sections[$this->uid]) || !in_array($taxonomy, $this->taxonomies)) {
				return;
			}

			foreach(FF_Registry::$fields_by_sections[$this->uid] as $field) {
				$field->delete_from_options('taxonomy', $tt_id);
			}
		}
	}
}

if(!class_exists('FF_User')) {
	class FF_User extends FF_Section {
		public function __construct($arguments) {
			parent::__construct($arguments);

			$this->add();
		}

		public function add() {
			/* The following basically insures the relevant actions are only called if sections pertaining to them exist */
			add_action('personal_options_update', array($this, 'save'));
	
			add_action('edit_user_profile_update', array($this, 'save'));

			add_action('show_user_profile', array($this, 'display'));

			add_action('edit_user_profile', array($this, 'display'));
		}
		
		public function display($user) {
			ff_render_section($this->uid, 'meta', 'user', $user->ID);
		}

		public function save($user_id) {
			if(ff_verify_nonce()) {
				return;
			}
	
			if($this->skip_save != false || empty(FF_Registry::$fields_by_sections[$this->uid])) {
				return;
			}

			foreach(FF_Registry::$fields_by_sections[$this->uid] as $field) {
				$field->save_to_meta('user', $user_id);
			}
		}
	}
}

if(!class_exists('FF_Widget')) {
	class FF_Widget extends FF_Section {
		protected $title;

		public function __construct($arguments) {
			parent::__construct($arguments);

			/* Title is required */
			if(empty($this->title)) {
				ff_throw_exception(__('Empty Widget Section Title', 'fields-framework'));
			}
		}
		
		public static function add() {
			register_widget('FF_WP_Widget');
		}
	}
}

if(!class_exists('FF_WP_Widget')) {
	class FF_WP_Widget extends WP_Widget {
		public function __construct() {
			parent::__construct('ff_wp_widget', __('Fields Framework Widget', 'fields-framework'), null, array('width' => 450));
		}

		public function widget($arguments, $instance) {
			$section_title = apply_filters('widget_title', $instance['ff-section-title']);

			echo $arguments['before_widget'];

			if (!empty($section_title)) {
				echo $arguments['before_title'] . $section_title . $arguments['after_title'];
			}

			if(!empty($instance['ff-section-uid'])) {
				$section_uid = $instance['ff-section-uid'];

				$values = !empty(FF_Registry::$fields_by_sections[$section_uid]) ? ff_get_all_fields_from_section($section_uid, 'custom', 'widget', $instance) : null;
	
				do_action('ff_wp_widget', $section_uid, $values);
			}

			echo $arguments['after_widget'];
		}

		public function form($instance) {
			$section_title = !empty($instance['ff-section-title']) ? $instance['ff-section-title'] : null;

			$section_title_field = ff_create_field('ff-section-title', 'text', array(
				'name' => $this->get_field_name('ff-section-title'),
				'id' => $this->get_field_id('ff-section-title'),
				'label' => __('Title', 'fields-framework'),
				'value' => $section_title,
			), true);

			$section_title_field->container();

			if(empty(FF_Registry::$sections)) {
				return;
			}

			if(!empty($instance['ff-section-uid'])) {
				$section_uid = $instance['ff-section-uid'];

				ff_render_section($section_uid, 'custom', 'widget', $instance);

				$section_uid_field = ff_create_field('ff-section-uid', 'hidden', array(
					'name' => $this->get_field_name('ff-section-uid'),
					'id' => $this->get_field_id('ff-section-uid'),
					'value' => $section_uid,
				), true);
			}
			else {
				$options = null;
	
				foreach(FF_Registry::$sections as $section_uid => $section) {
					if(!is_a($section, 'FF_Widget') || empty(FF_Registry::$fields_by_sections[$section_uid])) {
						continue;
					}

					$class = new ReflectionClass($section);

					$property = $class->getProperty('title');
					
					$property->setAccessible(true);

					$options[$section_uid] = $property->getValue($section);
				}
	
				if(!empty($options)) {
					$section_uid_field = ff_create_field('ff-section-uid', 'select', array(
						'name' => $this->get_field_name('ff-section-uid'),
						'id' => $this->get_field_id('ff-section-uid'),
						'label' => __('Section', 'fields-framework'),
						'options' => $options,
						'prepend_blank' => true,
					), true);
				}
			}

			if(!empty($section_uid_field)) {
				$section_uid_field->container();
			}
		}

		public function update($new_instance, $old_instance) {
			$instance['ff-section-title'] = !empty($new_instance['ff-section-title']) ? $new_instance['ff-section-title'] : null;

			$instance['ff-section-uid'] = !empty($new_instance['ff-section-uid']) ? $new_instance['ff-section-uid'] : null;

			if(!empty($instance['ff-section-uid'])) {
				$section_uid = $instance['ff-section-uid'];
		
				$section = FF_Registry::$sections[$section_uid];

				$class = new ReflectionClass($section);

				$property = $class->getProperty('skip_save');
				
				$property->setAccessible(true);

				if($property->getValue($section) == false && !empty(FF_Registry::$fields_by_sections[$section_uid])) {
					foreach(FF_Registry::$fields_by_sections[$section_uid] as $field) {
						$field->save_to_custom('widget', $instance);
					}
				}
			}
		
			return $instance;
		}
	}
}

if(!class_exists('FF_Field')) {
	abstract class FF_Field {
		/*
		saved_value is a property which holds the field's value saved in the database
		default_value is a property which holds the field's default value
		value is a property which is either set to use saved_value or if that's empty then it uses default_value
		*/
		protected $uid, $name, $label, $id, $class, $description, $value, $saved_value, $default_value, $placeholder, $repeatable = false, $minimal = false;

		protected $validator = array();

		public function __construct($arguments) {
			ff_set_object_defaults($this, $arguments);

			/* While constructing the object for the first time, if the value property is set then it will be assigned to the default_value property */
			if(!ff_empty($this->value)) {
				$this->default_value = $this->value;
			}

			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		}

		public function admin_enqueue_scripts() {
			if($this->repeatable == true) {
				wp_enqueue_script('ff-repeatable-fields', FF_Registry::$plugins_url . '/js/repeatable-fields.js', array('jquery'));
			}
			
			if(!empty($this->validator)) {
				wp_enqueue_style('ff-validationEngine', FF_Registry::$plugins_url . '/css/validationEngine.jquery.css');
		
				wp_enqueue_script('ff-validationEngine-en', FF_Registry::$plugins_url . '/js/jquery.validationEngine-en.js', array('jquery'));
		
				wp_enqueue_script('ff-validationEngine', FF_Registry::$plugins_url . '/js/jquery.validationEngine.js', array('jquery'));
			}

			if(!empty($this->placeholder)) {
				wp_enqueue_script('ff-placeholder', FF_Registry::$plugins_url . '/js/jquery.placeholder.js', array('jquery'));
			}
		}

		public function use_value($type = null) {
			$value = null;

			if($type == 'saved') {
				$this->value = $this->saved_value;
			}
			elseif($type == 'default') {
				$this->value = $this->default_value;
			}
			else {
				/* Automatically determine the value that should be used */
				if(!ff_empty($this->saved_value)) {
					$value = $this->value = $this->saved_value;
				}
				elseif($this->repeatable != true) {
					$value = $this->value = $this->default_value;
				}
			}

			if($this->repeatable == true && ff_empty($value) && !is_array($value)) {
				$value = array();
			}

			return $value;
		}

		public function set_saved_value($saved_value) {
			$this->saved_value = $saved_value;

			/* This will set the field's value property to either saved_value or default_value */
			return $this->use_value();
		}

		public function get_from_options($option_type = null, $object_id = null) {
			$name = $this->name;
	
			if($option_type == 'taxonomy') {
				$name = "ttid_{$object_id}_{$name}";
			}
	
			$saved_value = get_option($name, null);

			return $this->set_saved_value($saved_value);
		}
	
		public function get_from_meta($meta_type, $object_id) {
			$name = $this->name;

			$saved_value = get_metadata($meta_type, $object_id, $name, true);

			return $this->set_saved_value($saved_value);
		}

		public function get_from_custom($custom_type, $object_id) {
			$name = $this->name;

			$saved_value = $custom_type == 'widget' && !empty($object_id[$name]) ? $object_id[$name] : null;
			
			return $this->set_saved_value($saved_value);
		}

		public function save() {
			$name = $this->name;

			$value = isset($_POST[$name]) ? ff_sanitize($_POST[$name]) : null;

			return $value;
		}

		public function save_to_options($option_type = null, $object_id = null) {
			$name = $this->name;

			if($option_type == 'taxonomy') {
				$name = "ttid_{$object_id}_{$name}";
			}

			$value = $this->save();

			if(!ff_empty($value)) {
				update_option($name, $value);
			}
			else {
				delete_option($name);
			}
		}
	
		public function save_to_meta($meta_type, $object_id) {
			$name = $this->name;

			$value = $this->save();

			if(!ff_empty($value)) {
				update_metadata($meta_type, $object_id, $name, $value);
			}
			else {
				delete_metadata($meta_type, $object_id, $name);
			}
		}

		public function save_to_custom($custom_type, &$object_id) {
			$name = $this->name;

			$value = $this->save();

			$object_id[$name] = $value;
		}

		public function delete_from_options($option_type = null, $object_id = null) {
			$name = $this->name;

			if($option_type == 'taxonomy') {
				$name = "ttid_{$object_id}_{$name}";
			}
	
			delete_option($name);
		}

		public function container() {
			?>
			<div class="ff-fields">
				<?php if($this->minimal == false) : ?>
				<table>
					<tr>
						<th><label for="<?php echo $this->id; ?>"><?php echo $this->label; ?></label></th>
						
						<td>
				<?php endif; ?>

				<?php if($this->repeatable == true) echo '<div class="ff-repeatable">'; ?>

				<table>
					<?php if($this->repeatable == true) : ?>
					<thead>
						<tr>
							<th>Move</th>
		
							<th>Field</th>
		
							<th><img src="<?php echo FF_Registry::$plugins_url . '/images/add.png'; ?>" class="ff-add-row" alt="<?php _e('Add Row', 'fields-framework'); ?>" /></th>
						</tr>
					</thead>
					<?php endif; ?>

					<tbody>
						<?php
							if($this->repeatable == true) {
								$original_name = $this->name;

								$original_id = $this->id;
								?>
								<tr class="ff-add-template">
									<th><img src="<?php echo FF_Registry::$plugins_url . '/images/move.png'; ?>" class="ff-move-row" alt="<?php _e('Move Row', 'fields-framework'); ?>" /></th>
				
									<td>
									<?php
										$this->name = "{$original_name}[{{row-count-placeholder}}]";

										$this->id = "{$original_id}-{{row-count-placeholder}}";

										/* Reset this object's instance to the default value */
										$this->use_value('default');

										$this->html();
									?>
									</td>
	
									<td><img src="<?php echo FF_Registry::$plugins_url . '/images/remove.png'; ?>" class="ff-remove-row" alt="<?php _e('Remove Row', 'fields-framework'); ?>" /></td>
								</tr>
								<?php
								$values = $this->saved_value;

								if(ff_empty($values)) {
									$values = array(null);
								}
								elseif(!is_array($values)) {
									$values = array($values);
								}

								$i = 0;

								foreach($values as $value) {
									?>
									<tr>
										<th><img src="<?php echo FF_Registry::$plugins_url . '/images/move.png'; ?>" class="ff-move-row" alt="<?php _e('Move Row', 'fields-framework'); ?>" /></th>

										<td>
										<?php
											$this->name = "{$original_name}[{$i}]";
	
											$this->id = "{$original_id}-{$i}";

											$i++;

											$this->set_saved_value($value);

											$this->html();
										?>
										</td>

										<td><img src="<?php echo FF_Registry::$plugins_url . '/images/remove.png'; ?>" class="ff-remove-row" alt="<?php _e('Remove Row', 'fields-framework'); ?>" /></td>
									</tr>
									<?php
								}

								$this->name = $original_name;

								$this->id = $original_id;
							}
							else {
								echo '<tr><td>';

								$this->html();
		
								echo '</td></tr>';
							}
						?>
					</tbody>
				</table>

				<?php if($this->repeatable == true) echo '</div>'; ?>

				<?php if($this->minimal == false) : ?>
							<?php echo wpautop($this->description); ?>
						</td>
					</tr>
				</table>
				<?php endif; ?>
			</div>
			<?php
		}

		abstract public function html();
	}
}

if(!class_exists('FF_Field_Group')) {
	class FF_Field_Group extends FF_Field {
		protected $fields = array();

		public function set_saved_value($saved_value) {
			$saved_value = parent::set_saved_value($saved_value);

			if($this->repeatable != true) {
				foreach($this->fields as $field) {
					$set_saved_value = !ff_empty($saved_value) && is_array($saved_value) && array_key_exists($field->name, $saved_value) ? $saved_value[$field->name] : null;
	
					$saved_value[$field->name] = $field->set_saved_value($set_saved_value);
				}
			}
			elseif(!ff_empty($saved_value) && is_array($saved_value)) {
				foreach($saved_value as &$value) {
					foreach($this->fields as $field) {
						$set_saved_value = !ff_empty($value) && is_array($value) && array_key_exists($field->name, $value) ? $value[$field->name] : null;

						if(ff_empty($value) || is_array($value)) {
							$value[$field->name] = $field->set_saved_value($set_saved_value);
						}
					}
				}
			}
			
			return $saved_value;
		}

		public function html() {
			$field_group_id = $this->id;

			$field_group_name = $this->name;

			foreach($this->fields as $field) {
				$original_field_name = $field->name;

				$original_field_id = $field->id;

				$field->name = "{$field_group_name}[{$original_field_name}]";

				$field->id = "{$field_group_id}-{$original_field_id}";

				$value = is_array($this->value) && array_key_exists($original_field_name, $this->value) ? $this->value[$original_field_name] : null;

				$field->set_saved_value($value);

				$field->container();

				$field->name = $original_field_name;

				$field->id = $original_field_id;
			}
		}

		public function add_field($field) {
			$this->fields[] = $field;
		}
	}
}

if(!class_exists('FF_Field_Text')) {
	class FF_Field_Text extends FF_Field {
		protected $class = 'large-text';
	
		public function html() {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($this->value) . '" class="' . esc_attr($this->class) . '"';

			if(!empty($this->validator)) {
				ff_validator_attributes($this->validator);
			}

			echo ' />';
		}
	}
}

if(!class_exists('FF_Field_DateTime')) {
	class FF_Field_DateTime extends FF_Field {
		protected $class = 'large-text ff-datetime', $date_format = 'mm/dd/yy', $time_format = 'hh:mm:ss tt';

		public function __construct($arguments) {
			parent::__construct($arguments);

			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		}
	
		public function admin_enqueue_scripts() {
			parent::admin_enqueue_scripts();

			wp_enqueue_style('ff-ui-custom', FF_Registry::$plugins_url . '/css/jquery-ui.custom.css');

			wp_enqueue_style('ff-ui-timepicker', FF_Registry::$plugins_url . '/css/jquery-ui-timepicker-addon.css');

			wp_enqueue_script('ff-ui-timepicker', FF_Registry::$plugins_url . '/js/jquery-ui-timepicker-addon.js', array('jquery-ui-core', 'jquery-ui-datepicker'));
		}

		public function html() {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($this->value) . '" class="' . esc_attr($this->class) . '" data-date-format="' . esc_attr($this->date_format) . '" data-time-format="' . esc_attr($this->time_format) . '"';

			if(!empty($this->validator)) {
				ff_validator_attributes($this->validator);
			}

			echo ' />';
		}
	}
}

if(!class_exists('FF_Field_ColorPicker')) {
	class FF_Field_ColorPicker extends FF_Field {
		protected $class = 'large-text ff-colorpicker';

		public function __construct($arguments) {
			parent::__construct($arguments);

			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		}
	
		public function admin_enqueue_scripts() {
			parent::admin_enqueue_scripts();

			wp_enqueue_style('ff-ui-custom', FF_Registry::$plugins_url . '/css/jquery-ui.custom.css');

			wp_enqueue_style('ff-colorpicker', FF_Registry::$plugins_url . '/css/jquery.colorpicker.css');

			wp_enqueue_script('ff-colorpicker', FF_Registry::$plugins_url . '/js/jquery.colorpicker.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-button'));
		}

		public function html() {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($this->value) . '" class="' . esc_attr($this->class) . '"';

			if(!empty($this->validator)) {
				ff_validator_attributes($this->validator);
			}

			echo ' />';
		}
	}
}

if(!class_exists('FF_Field_Hidden')) {
	class FF_Field_Hidden extends FF_Field {
		public function container() {
			$this->html();
		}
		
		public function html() {
			echo '<input type="hidden" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" value="' . esc_attr($this->value) . '" />';
		}
	}
}

if(!class_exists('FF_Field_Media')) {
	class FF_Field_Media extends FF_Field {
		protected $class = 'large-text', $library;
	
		public function __construct($arguments) {
			parent::__construct($arguments);

			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		}
	
		public function admin_enqueue_scripts() {
			parent::admin_enqueue_scripts();

			$arguments = array();

			global $post;

			if(!empty($post->ID)) {
				$arguments['post'] = $post->ID;
			}

			wp_enqueue_media($arguments);
	
			wp_enqueue_script('ff-media-uploader', FF_Registry::$plugins_url . '/js/media-uploader.js');
		}
	
		public function html() {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($this->value) . '" class="' . esc_attr($this->class) . '"';

			if(!empty($this->validator)) {
				ff_validator_attributes($this->validator);
			}

			echo ' />';

			echo '<p><img data-to="' . esc_attr($this->id) . '"' . (!empty($this->library) ? (' data-library="' . esc_attr(json_encode($this->library)) . '"') : null) . ' src="' . FF_Registry::$plugins_url . '/images/upload.png' . '" alt="' . __('Upload', 'fields-framework') . '" class="ff_upload_media" /></p>';
		}
	}
}

if(!class_exists('FF_Field_Textarea')) {
	class FF_Field_Textarea extends FF_Field {
		protected $class = 'large-text', $rows = 5, $cols = 50;

		public function html() {
			echo '<textarea name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" class="' . esc_attr($this->class) . '" rows="' . esc_attr($this->rows) . '" cols="' . esc_attr($this->cols) . '"';

			if(!empty($this->validator)) {
				ff_validator_attributes($this->validator);
			}

			echo '>' . esc_textarea($this->value) . '</textarea>';
		}
	}
}

if(!class_exists('FF_Field_Multiple')) {
	abstract class FF_Field_Multiple extends FF_Field {
		protected $multiple;

		public function use_value($type = null) {
			$value = parent::use_value($type);

			if($this->multiple == true && ff_empty($value) && !is_array($value)) {
				$value = array();
			}

			return $value;
		}

		public function get_name() {
			$name = $this->name;

			if($this->multiple == true) {
				$name .= '[]';
			}
			
			return $name;
		}
	}
}

if(!class_exists('FF_Field_Checkbox')) {
	class FF_Field_Checkbox extends FF_Field_Multiple {
		protected $options = array(), $multiple = true;

		public function html() {
			$name = $this->get_name();

			$options = $this->options;

			if(!empty($options)) {
				$i = 1;

				foreach($options as $option_name => $option_value) {
					echo '<input type="checkbox" name="' . esc_attr($name) . '" id="' . esc_attr($this->id) . '-' . $i . '" value="' . esc_attr($option_name) . '" class="' . esc_attr($this->class) . '"' . ((in_array($option_name, (array) $this->value)) ? ' checked="checked"' : null);

					if(!empty($this->validator)) {
						ff_validator_attributes($this->validator);
					}

					echo ' /> <label for="' . esc_attr($this->id) . '-' . $i . '">' . $option_value . '</label><br />';
					
					$i++;
				}
			}
		}
	}
}

if(!class_exists('FF_Field_Radio')) {
	class FF_Field_Radio extends FF_Field {
		protected $options = array();

		public function html() {
			$options = $this->options;

			if(!empty($options)) {
				$i = 1;
				
				foreach($options as $option_name => $option_value) {
					echo '<input type="radio" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '-' . $i . '" value="' . esc_attr($option_name) . '" class="' . esc_attr($this->class) . '"' . ((in_array($option_name, (array) $this->value)) ? ' checked="checked"' : null);

					if(!empty($this->validator)) {
						ff_validator_attributes($this->validator);
					}

					echo ' /> <label for="' . esc_attr($this->id) . '-' . $i . '">' . $option_value . '</label><br />';
					
					$i++;
				}
			}
		}
	}
}

if(!class_exists('FF_Field_Select')) {
	class FF_Field_Select extends FF_Field_Multiple {
		protected $options = array(), $multiple = false, $size = 5, $prepend_blank = true;

		public function html() {
			$name = $this->get_name();

			echo '<select name="' . esc_attr($name) . '" id="' . esc_attr($this->id) . '" class="' . esc_attr($this->class) . '"' . (($this->multiple == true) ? ' multiple="multiple" size="' . $this->size . '"' : null);

			if(!empty($this->validator)) {
				ff_validator_attributes($this->validator);
			}

			echo '>';

			if(!empty($this->placeholder) || $this->prepend_blank == true) {
				echo '<option value="">' . $this->placeholder . '</option>';
			}

			$options = $this->options;

			if(!empty($options)) {
				foreach($options as $option_name => $option_value) {
					echo '<option value="' . $option_name . '"' . ((in_array($option_name, (array) $this->value)) ? ' selected="selected"' : null) . '>' . $option_value . '</option>';
				}
			}

			echo '</select>';
		}
	}
}

if(!class_exists('FF_Field_Select_Posts')) {
	class FF_Field_Select_Posts extends FF_Field_Select {
		protected $parameters = array();

		public function __construct($arguments) {
			parent::__construct($arguments);

			$posts = get_posts($this->parameters);

			if(empty($posts)) {
				return;
			}

			foreach($posts as $post) {
				$this->options[$post->ID] = $post->post_title;
			}
		}
	}
}

if(!class_exists('FF_Field_Select_Terms')) {
	class FF_Field_Select_Terms extends FF_Field_Select {
		protected $taxonomies, $parameters = array();

		public function __construct($arguments) {
			parent::__construct($arguments);

			/* Atleast one taxonomy must be supplied */
			if(empty($this->taxonomies)) {
				ff_throw_exception(__('Empty Taxonomies', 'fields-framework'));
			}

			$terms = get_terms($this->taxonomies, $this->parameters);

			if(empty($terms)) {
				return;
			}

			if(is_wp_error($terms)) {
				ff_throw_exception(__('One of the Specified Taxonomy is not valid', 'fields-framework'));
			}

			foreach($terms as $term) {
				$this->options[$term->term_id] = $term->name;
			}
		}
	}
}

if(!class_exists('FF_Field_Select_Users')) {
	class FF_Field_Select_Users extends FF_Field_Select {
		protected $parameters = array();

		public function __construct($arguments) {
			parent::__construct($arguments);

			$users = get_users($this->parameters);

			if(empty($users)) {
				return;
			}

			foreach($users as $user) {
				$this->options[$user->ID] = "{$user->display_name} ({$user->user_login})";
			}
		}
	}
}

if(!class_exists('FF_Field_Editor')) {
	class FF_Field_Editor extends FF_Field {
		protected $settings = array();

		public function __construct($arguments) {
			parent::__construct($arguments);

			/* Editor cannot be made repeatable so force it to be false in case the user has tried setting it to true */
			$this->repeatable = false;
		}

		public function container() {
			$id = null;

			for ($i = 0; $i < strlen($this->id); $i++) {
				$character = ord($this->id[$i]);

				if(($character >= 97 && $character <= 122) || ($character >= 48 && $character <= 57)) {
					$id .= chr($character);
				}
			}

			$this->id = $id;

			parent::container();
		}

		public function html() {
			$this->settings['textarea_name'] = $this->name;

			wp_editor($this->value, $this->id, $this->settings);
		}
	}
}
?>