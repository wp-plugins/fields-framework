<?php
/**
 * @package WordPress
 * @subpackage Fields Framework
 */

if(!class_exists('FF_Registry')) {
	abstract class FF_Registry {
		static $sections = array(), $fields = array(), $fields_by_sections = array();
	}
}

if(!class_exists('FF_Section')) {
	abstract class FF_Section {
		public function __construct($arguments) {
			$properties = get_class_vars(get_class($this));

			foreach($properties as $property_name => $property_value) {
				if(isset($arguments[$property_name])) {
					if(is_array($arguments[$property_name])) {
						$this->$property_name = $arguments[$property_name];
					}
					else {
						$this->$property_name = trim($arguments[$property_name]);
					}
				}
			}
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
		protected $name, $label, $id, $class, $description, $value, $placeholder, $repeatable = false;
	
		public function __construct($arguments) {
			$properties = get_class_vars(get_class($this));

			foreach($properties as $property_name => $property_value) {
				if(isset($arguments[$property_name])) {
					if(is_array($arguments[$property_name])) {
						$this->$property_name = $arguments[$property_name];
					}
					else {
						$this->$property_name = trim($arguments[$property_name]);
					}
				}
			}

			if(empty($this->id)) {
				$this->id = $this->name;
			}

			if($this->repeatable == true) {
				add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));		
			}
		}
	
		public function admin_enqueue_scripts() {	
			wp_enqueue_script('ff-dynotable', plugins_url('js/jquery.dynotable.js', dirname(__FILE__)), array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-sortable'));
		}
	
		public function container($saved_value = null) {
			?>
			<div class="ff-fields">
				<table>
				<tr>
					<th><label for="<?php echo $this->id; ?>"><?php echo $this->label; ?></label></th>
					
					<td>
						<table <?php if($this->repeatable == true) echo 'class="ff-repeatable"'; ?>>
							<?php if($this->repeatable == true) : ?>
							<thead>
								<tr>
									<th>Move</th>
				
									<th>Field</th>
				
									<th><img src="<?php echo plugins_url('images/add.png', dirname(__FILE__)); ?>" class="ff-add-row" alt="<?php _e('Add Row', 'fields-framework'); ?>" /></th>
								</tr>
							</thead>
							<?php endif; ?>
	
							<tbody>
								<?php
									if($this->repeatable == true) {
										$i = 0;

										$original_name = $this->name;
	
										$original_id = $this->id;
										?>
										<tr class="ff-add-template">
											<th><img src="<?php echo plugins_url('images/move.png', dirname(__FILE__)); ?>" class="ff-move-row" alt="<?php _e('Move Row', 'fields-framework'); ?>" /></th>
						
											<td>
											<?php
												$this->name = "{$original_name}[{$i}]";
	
												$this->id = "{$original_id}[{$i}]";
	
												$i++;

												$this->html($this->value);
											?>
											</td>
			
											<td><img src="<?php echo plugins_url('images/remove.png', dirname(__FILE__)); ?>" class="ff-remove-row" alt="<?php _e('Remove Row', 'fields-framework'); ?>" /></td>
										</tr>
										<?php
										$values = $saved_value;

										if(empty($values)) {
											$values[] = $this->value;
										}
				
										foreach($values as $value) {
											?>
											<tr>
												<th><img src="<?php echo plugins_url('images/move.png', dirname(__FILE__)); ?>" class="ff-move-row" alt="<?php _e('Move Row', 'fields-framework'); ?>" /></th>
	
												<td>
												<?php
													$this->name = "{$original_name}[{$i}]";
			
													$this->id = "{$original_id}[{$i}]";
	
													$i++;
	
													$this->html($value);
												?>
												</td>
	
												<td><img src="<?php echo plugins_url('images/remove.png', dirname(__FILE__)); ?>" class="ff-remove-row" alt="<?php _e('Remove Row', 'fields-framework'); ?>" /></td>
											</tr>
											<?php
										}

										$this->name = $original_name;
	
										$this->id = $original_id;
									}
									else {
										echo '<tr><td>';
	
										$this->html($saved_value);
				
										echo '</td></tr>';
									}
								?>
							</tbody>
						</table>
	
						<?php echo wpautop($this->description); ?>
					</td>
				</tr>
				</table>
			</div>
			<?php
		}

		abstract public function html($saved_value = null);

		public function get_default($value) {
			if(ff_empty($value)) {
				$value = $this->value;
			}

			if($this->repeatable == true && !is_array($value)) {
				$value = (array) $value;
			}

			return $value;
		}

		public function get_from_options($option_type = null, $object_id = null, $load_default = false) {
			$name = $this->name;
	
			if($option_type == 'taxonomy') {
				$name = "ttid_{$object_id}_{$name}";
			}
	
			$value = get_option($name, null);

			if($load_default == true) {
				$value = $this->get_default($value);
			}

			return $value;
		}
	
		public function get_from_meta($meta_type, $object_id, $load_default = false) {
			$name = $this->name;

			$value = get_metadata($meta_type, $object_id, $name, true);

			if($load_default == true) {
				$value = $this->get_default($value);
			}

			return $value;
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
	}
}

if(!class_exists('FF_Field_Group')) {
	class FF_Field_Group extends FF_Field {
		protected $fields = array();

		public function html($saved_value = null) {
			$field_group_name = $this->name;

			foreach($this->fields as $field) {
				$original_field_name = $field->name;

				$original_field_id = $field->id;

				$field_value = null;

				if(is_array($saved_value) && array_key_exists($original_field_name, $saved_value)) {
					$field_value = $saved_value[$original_field_name];
				}

				if(ff_empty($field_value)) {
					$field_value = $field->value;
				}

				if($field->repeatable == true && !is_array($field_value)) {
					$field_value = (array) $field_value;
				}

				$field->name = "{$field_group_name}[{$original_field_name}]";

				$field->id = "{$field_group_name}-{$original_field_id}";

				$field->container($field_value);

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
	
		public function html($saved_value = null) {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($saved_value) . '" class="' . esc_attr($this->class) . '" />';
		}
	}
}

if(!class_exists('FF_Field_DateTime')) {
	class FF_Field_DateTime extends FF_Field {
		protected $class = 'ff-datetime', $date_format = 'mm/dd/yy', $time_format = 'hh:mm:ss tt';

		public function __construct($arguments) {
			parent::__construct($arguments);

			wp_enqueue_style('ff-ui-custom', plugins_url('css/jquery-ui.custom.css', dirname(__FILE__)));

			wp_enqueue_style('ff-ui-timepicker', plugins_url('css/jquery-ui-timepicker-addon.css', dirname(__FILE__)));

			wp_enqueue_script('ff-ui-timepicker', plugins_url('js/jquery-ui-timepicker-addon.js', dirname(__FILE__)), array('jquery-ui-core', 'jquery-ui-datepicker'));
		}

		public function html($saved_value = null) {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($saved_value) . '" class="' . esc_attr($this->class) . '" data-date-format="' . esc_attr($this->date_format) . '" data-time-format="' . esc_attr($this->time_format) . '" />';
		}
	}
}

if(!class_exists('FF_Field_ColorPicker')) {
	class FF_Field_ColorPicker extends FF_Field {
		protected $class = 'ff-colorpicker';

		public function __construct($arguments) {
			parent::__construct($arguments);

			wp_enqueue_style('ff-colorpicker', plugins_url('css/colorpicker.css', dirname(__FILE__)));

			wp_enqueue_script('ff-colorpicker', plugins_url('js/colorpicker.js', dirname(__FILE__)), array('jquery'));
		}

		public function html($saved_value = null) {
			echo '<input type="text" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($saved_value) . '" class="' . esc_attr($this->class) . '" data-date-format="' . esc_attr($this->date_format) . '" data-time-format="' . esc_attr($this->time_format) . '" />';
		}
	}
}

if(!class_exists('FF_Field_Hidden')) {
	class FF_Field_Hidden extends FF_Field {
		public function container($saved_value) {
			$this->html($saved_value);
		}
		
		public function html($saved_value = null) {
			echo '<input type="hidden" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" value="' . esc_attr($saved_value) . '" />';
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
	
			wp_enqueue_script('ff-media-uploader', plugins_url('js/media-uploader.js', dirname(__FILE__)));
		}
	
		public function html($saved_value = null) {
			echo '<input type="url" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" value="' . esc_attr($saved_value) . '" class="' . esc_attr($this->class) . '" />';

			echo '<p><img data-to="' . esc_attr($this->id) . '"' . (!empty($this->library) ? (' data-library="' . esc_attr(json_encode($this->library)) . '"') : null) . ' src="' . plugins_url('images/upload.png', dirname(__FILE__)) . '" alt="' . __('Upload', 'fields-framework') . '" class="ff_upload_media" /></p>';
		}
	}
}

if(!class_exists('FF_Field_Textarea')) {
	class FF_Field_Textarea extends FF_Field {
		protected $class = 'large-text', $rows = 5, $cols = 50;

		public function html($saved_value = null) {
			echo '<textarea name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" placeholder="' . esc_attr($this->placeholder) . '" class="' . esc_attr($this->class) . '" rows="' . esc_attr($this->rows) . '" cols="' . esc_attr($this->cols) . '">' . esc_textarea($saved_value) . '</textarea>>';
		}
	}
}

if(!class_exists('FF_Field_Checkbox')) {
	class FF_Field_Checkbox extends FF_Field {
		protected $options = array(), $multiple = true;

		public function html($saved_value = null) {
			$name = $this->name;

			if($this->multiple == true) {
				$name .= '[]';
			}

			$options = $this->options;

			if(!empty($options)) {
				foreach($options as $option_name => $option_value) {
					echo '<input type="checkbox" name="' . esc_attr($name) . '" id="' . esc_attr($this->id) . '" value="' . esc_attr($option_name) . '" class="' . esc_attr($this->class) . '"' . ((in_array($option_name, (array) $saved_value)) ? ' checked="checked"' : null) . '  /> ' . $option_value . '<br />';
				}
			}
		}
	}
}

if(!class_exists('FF_Field_Radio')) {
	class FF_Field_Radio extends FF_Field {
		protected $options = array();

		public function html($saved_value = null) {
			$options = $this->options;

			if(!empty($options)) {
				foreach($options as $option_name => $option_value) {
					echo '<input type="radio" name="' . esc_attr($this->name) . '" id="' . esc_attr($this->id) . '" value="' . esc_attr($option_name) . '" class="' . esc_attr($this->class) . '"' . ((in_array($option_name, (array) $saved_value)) ? ' checked="checked"' : null) . '  /> ' . $option_value . '<br />';
				}
			}
		}
	}
}

if(!class_exists('FF_Field_Select')) {
	class FF_Field_Select extends FF_Field {
		protected $options = array(), $multiple = false, $size = 5;

		public function html($saved_value = null) {
			$name = $this->name;

			if($this->multiple == true) {
				$name .= '[]';
			}

			echo '<select name="' . esc_attr($name) . '" id="' . esc_attr($this->id) . '" class="' . esc_attr($this->class) . '"' . (($this->multiple == true) ? ' multiple="multiple" size="' . $this->size . '"' : null) . '>';

			$options = $this->options;

			if(!empty($options)) {
				foreach($options as $option_name => $option_value) {
					echo '<option value="' . $option_name  . '"' . ((in_array($option_name, (array) $saved_value)) ? ' selected="selected"' : null) . '>' . $option_value  . '</option>';
				}
			}

			echo '</select>';
		}
	}
}

if(!class_exists('FF_Field_Select_Posts')) {
	class FF_Field_Select_Posts extends FF_Field_Select {
		protected $query_parameters = array();

		public function __construct($arguments) {
			parent::__construct($arguments);

			$posts = get_posts($this->query_parameters);

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

			/* Editor cannot be made repeatable so force it to be false in case the user has tried setting it to true */
			$this->repeatable = false;
		}

		public function html($saved_value = null) {
			$id = null;

			for ($i = 0; $i < strlen($this->id); $i++)  {
				$character = ord($this->id[$i]);

				if($character >= 97 && $character <= 122) {
					$id .= chr($character);
				}
			}

			$this->id = $id;

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

			wp_editor($saved_value, $this->id, $arguments);
		}
	}
}
?>