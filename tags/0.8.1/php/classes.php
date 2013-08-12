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
		public function __construct($arguments) {
			ff_set_object_defaults($this, $arguments);
		}
	}
}

if(!class_exists('FF_Taxonomy')) {
	class FF_Taxonomy extends FF_Section {
		public $taxonomies;
	
		public function __construct($arguments) {
			parent::__construct($arguments);

			/* Taxonomies are required. Atleast one must be provided. */
			if(empty($this->taxonomies)) {
				ff_throw_exception(__('Empty Taxonomies', 'fields-framework'));
			}
		}
	}
}

if(!class_exists('FF_User')) {
	class FF_User extends FF_Section {
	}
}

if(!class_exists('FF_Post')) {
	class FF_Post extends FF_Section {
		public $id, $title, $context = 'advanced', $priority = 'default';
		
		public $post_types = array(), $page_templates = array(), $post_formats = array();
	
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
		}
	}
}

if(!class_exists('FF_Admin_Menu')) {	
	class FF_Admin_Menu extends FF_Section {
		public $page_title, $menu_title, $capability = 'manage_options', $menu_uid, $icon_url, $position;
		
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
		}
	}
}

if(!class_exists('FF_Admin_Sub_Menu')) {
	class FF_Admin_Sub_Menu extends FF_Section {
		public $menu_uid, $parent_uid, $page_title, $menu_title, $capability = 'manage_options';
	
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
		protected $name, $label, $id, $class, $description, $value, $saved_value, $default_value, $placeholder, $repeatable = false, $minimal = false;
	
		public function __construct($arguments) {
			ff_set_object_defaults($this, $arguments);

			/* While constructing the object for the first time, if the value property is set then it will be assigned to the default_value property */
			if(!ff_empty($this->value)) {
				$this->default_value = $this->value;
			}
		}

		public function use_value($type = null) {
			if($type == 'saved') {
				$this->value = $this->saved_value;
			}
			elseif($type == 'default') {
				$this->value = $this->default_value;
			}
			else {
				/* Automatically determine the value that should be used */
				if(!ff_empty($this->saved_value)) {
					$this->value = $this->saved_value;
				}
				else {
					$this->value = $this->default_value;
				}
			}
			
			return $this->value;
		}

		public function set_saved_value($saved_value) {
			if($this->repeatable == true && ff_empty($saved_value)) {
				$saved_value = array();
			}

			$this->saved_value = $saved_value;

			/* This will set the value property to either saved_value or default_value */
			$this->use_value();

			return $this->saved_value;
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

		public function save_to_options($option_type = null, $object_id = null) {
			$name = $this->name;

			if(isset($_POST[$name])) {
				$value = ff_sanitize($_POST[$name]);

				if($option_type == 'taxonomy') {
					$name = "ttid_{$object_id}_{$name}";
				}

				if(!ff_empty($value)) {
					update_option($name, $value);
				}
				else {
					delete_option($name);
				}
			}
			else {
				delete_option($name);
			}
		}
	
		public function save_to_meta($meta_type, $object_id) {
			$name = $this->name;

			if(isset($_POST[$name])) {
				$value = ff_sanitize($_POST[$name]);
			
				if(!ff_empty($value)) {
					update_metadata($meta_type, $object_id, $name, $value);
				}
				else {
					delete_metadata($meta_type, $object_id, $name);
				}
			}
			else {
				delete_metadata($meta_type, $object_id, $name);
			}
		}
	
		public function delete_from_options($object_id = null) {
			$name = $this->name;
	
			$name = "ttid_{$object_id}_{$name}";
	
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
							<table <?php if($this->repeatable == true) echo 'class="ff-repeatable"'; ?>>
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
											$i = 0;
	
											$original_name = $this->name;
		
											$original_id = $this->id;
											
											ob_start();
											?>
											<tr>
												<th><img src="<?php echo FF_Registry::$plugins_url . '/images/move.png'; ?>" class="ff-move-row" alt="<?php _e('Move Row', 'fields-framework'); ?>" /></th>
							
												<td>
												<?php
													$this->name = "{$original_name}[{$i}]";
		
													$this->id = "{$original_id}-{$i}";
		
													$i++;
	
													/* Reset this object's instance to the default value */
													$this->use_value('default');
	
													$this->html();
												?>
												</td>
				
												<td><img src="<?php echo FF_Registry::$plugins_url . '/images/remove.png'; ?>" class="ff-remove-row" alt="<?php _e('Remove Row', 'fields-framework'); ?>" /></td>
											</tr>
											<?php
											$content = ob_get_contents();
											
											ob_end_clean();
	
											echo '<script type="application/json" class="ff-add-template">' . json_encode($content) . '</script>';
	
											$values = $this->saved_value;
	
											if(ff_empty($values)) {
												$values = array(null);
											}
											elseif(!is_array($values)) {
												$values = array($values);
											}
	
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

			if(ff_empty($saved_value)) {
				$saved_value = array();

				foreach($saved_value as &$value) {
					foreach($this->fields as $field) {
						$set_saved_value = null;
	
						if(!ff_empty($value) && is_array($value) && array_key_exists($field->name, $value)) {
							$set_saved_value = $value[$field->name];
						}
	
						$field->set_saved_value($set_saved_value);
	
						$value[$field->name] = $field->use_value();
					}
				}
			}

			$this->saved_value = $saved_value;

			return $this->saved_value;
		}

		public function html() {
			$field_group_id = $this->id;

			$field_group_name = $this->name;

			foreach($this->fields as $field) {
				$original_field_name = $field->name;

				$original_field_id = $field->id;

				$field->name = "{$field_group_name}[{$original_field_name}]";

				$field->id = "{$field_group_id}-{$original_field_id}";

				$value = null;

				if(is_array($this->value) && array_key_exists($original_field_name, $this->value)) {
					$value = $this->value[$original_field_name];
				}

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
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($this->value) . '" class="' . esc_attr($this->class) . '" />';
		}
	}
}

if(!class_exists('FF_Field_DateTime')) {
	class FF_Field_DateTime extends FF_Field {
		protected $class = 'ff-datetime', $date_format = 'mm/dd/yy', $time_format = 'hh:mm:ss tt';

		public function __construct($arguments) {
			parent::__construct($arguments);

			wp_enqueue_style('ff-ui-custom', FF_Registry::$plugins_url . '/css/jquery-ui.custom.css');

			wp_enqueue_style('ff-ui-timepicker', FF_Registry::$plugins_url . '/css/jquery-ui-timepicker-addon.css');

			wp_enqueue_script('ff-ui-timepicker', FF_Registry::$plugins_url . '/js/jquery-ui-timepicker-addon.js', array('jquery-ui-core', 'jquery-ui-datepicker'));
		}

		public function html() {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($this->value) . '" class="' . esc_attr($this->class) . '" data-date-format="' . esc_attr($this->date_format) . '" data-time-format="' . esc_attr($this->time_format) . '" />';
		}
	}
}

if(!class_exists('FF_Field_ColorPicker')) {
	class FF_Field_ColorPicker extends FF_Field {
		protected $class = 'ff-colorpicker';

		public function __construct($arguments) {
			parent::__construct($arguments);

			wp_enqueue_style('ff-colorpicker', FF_Registry::$plugins_url . '/css/colorpicker.css');

			wp_enqueue_script('ff-colorpicker', FF_Registry::$plugins_url . '/js/colorpicker.js', array('jquery'));
		}

		public function html() {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($this->value) . '" class="' . esc_attr($this->class) . '" />';
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
			wp_enqueue_media();
	
			wp_enqueue_script('ff-media-uploader', FF_Registry::$plugins_url . '/js/media-uploader.js');
		}
	
		public function html() {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($this->value) . '" class="' . esc_attr($this->class) . '" />';

			echo '<p><img data-to="' . esc_attr($this->id) . '"' . (!empty($this->library) ? (' data-library="' . esc_attr(json_encode($this->library)) . '"') : null) . ' src="' . FF_Registry::$plugins_url . '/images/upload.png' . '" alt="' . __('Upload', 'fields-framework') . '" class="ff_upload_media" /></p>';
		}
	}
}

if(!class_exists('FF_Field_Textarea')) {
	class FF_Field_Textarea extends FF_Field {
		protected $class = 'large-text', $rows = 5, $cols = 50;

		public function html() {
			echo '<textarea name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" class="' . esc_attr($this->class) . '" rows="' . esc_attr($this->rows) . '" cols="' . esc_attr($this->cols) . '">' . esc_textarea($this->value) . '</textarea>>';
		}
	}
}

if(!class_exists('FF_Field_Multiple')) {
	abstract class FF_Field_Multiple extends FF_Field {
		protected $multiple;

		public function set_saved_value($saved_value) {
			$saved_value = parent::set_saved_value($saved_value);

			if($this->multiple == true && ff_empty($saved_value)) {
				$saved_value = array();
			}

			$this->saved_value = $saved_value;

			return $this->saved_value;
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
					echo '<input type="checkbox" name="' . esc_attr($name) . '" id="' . esc_attr($this->id) . '-' . $i . '" value="' . esc_attr($option_name) . '" class="' . esc_attr($this->class) . '"' . ((in_array($option_name, (array) $this->value)) ? ' checked="checked"' : null) . '  /> <label for="' . esc_attr($this->id) . '-' . $i . '">' . $option_value . '</label><br />';
					
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
					echo '<input type="radio" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '-' . $i . '" value="' . esc_attr($option_name) . '" class="' . esc_attr($this->class) . '"' . ((in_array($option_name, (array) $this->value)) ? ' checked="checked"' : null) . '  /> <label for="' . esc_attr($this->id) . '-' . $i . '">' . $option_value . '</label><br />';
					
					$i++;
				}
			}
		}
	}
}

if(!class_exists('FF_Field_Select')) {
	class FF_Field_Select extends FF_Field_Multiple {
		protected $options = array(), $multiple = false, $size = 5, $prepend_blank = false;

		public function html() {
			$name = $this->get_name();

			echo '<select name="' . esc_attr($name) . '" id="' . esc_attr($this->id) . '" class="' . esc_attr($this->class) . '"' . (($this->multiple == true) ? ' multiple="multiple" size="' . $this->size . '"' : null) . '>';

			if(!empty($this->placeholder) || $this->prepend_blank == true) {
				echo '<option value="">' . $this->placeholder . '</option>';
			}

			$options = $this->options;

			if(!empty($options)) {
				foreach($options as $option_name => $option_value) {
					echo '<option value="' . $option_name  . '"' . ((in_array($option_name, (array) $this->value)) ? ' selected="selected"' : null) . '>' . $option_value  . '</option>';
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

if(!class_exists('FF_Field_Editor')) {
	class FF_Field_Editor extends FF_Field {
		protected $wpautop = true, $media_buttons  = true, $textarea_rows = 10, $tabindex, $editor_css, $editor_class, $teeny = false, $dfw = false, $tinymce = true, $quicktags = true;

		public function __construct($arguments) {
			parent::__construct($arguments);

			$id = null;

			for ($i = 0; $i < strlen($this->id); $i++)  {
				$character = ord($this->id[$i]);

				if($character >= 97 && $character <= 122) {
					$id .= chr($character);
				}
			}

			$this->id = $id;

			/* Editor cannot be made repeatable so force it to be false in case the user has tried setting it to true */
			$this->repeatable = false;
		}

		public function html() {
			$arguments = array(
				'wpautop' => $this->wpautop,
				'media_buttons' => $this->media_buttons,
				'textarea_name' => $this->name,
				'textarea_rows' => $this->textarea_rows,
				'tabindex' => $this->tabindex,
				'editor_css' => $this->editor_css,
				'editor_class' => $this->editor_class,
				'teeny' => $this->teeny,
				'dfw' => $this->dfw,
				'tinymce' => $this->tinymce,
				'quicktags' => $this->quicktags,
			);

			wp_editor($this->value, $this->id, $arguments);
		}
	}
}
?>