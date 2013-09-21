<?php
ff_create_section('ff-builder', 'admin_sub_menu', array('skip_save' => true, 'parent_uid' => 'ff-admin-menu', 'page_title' => __('Builder', 'fields-framework'), 'menu_title' => __('Builder', 'fields-framework')));

add_action('ff_section_before', 'ff_builder_before');

function ff_builder_before($section_uid) {
	if($section_uid != 'ff-builder') {
		return;
	}

	$builder = get_option('ff-builder');

	$options['area'] = ff_create_field('ff-builder-area', 'hidden',
		array(
			'name' => 'area',
			'label' => __('Area', 'fields-framework'),
		),
		true
	);

	$options['type'] = ff_create_field('ff-builder-type', 'hidden',
		array(
			'name' => 'type',
			'label' => __('Type', 'fields-framework'),
		),
		true
	);

	$options['action'] = ff_create_field('ff-builder-action', 'hidden',
		array(
			'name' => 'action',
			'label' => __('Action', 'fields-framework'),
		),
		true
	);

	$options['uid-text'] = ff_create_field('ff-builder-uid', 'text',
		array(
			'name' => 'uid',
			'label' => __('UID', 'fields-framework'),
			'validator' => array('validation-engine' => 'validate[required]'),
		),
		true
	);

	$options['uid-hidden'] = ff_create_field('ff-builder-uid', 'hidden',
		array(
			'name' => 'uid',
			'label' => __('UID', 'fields-framework'),
		),
		true
	);

	$options['key'] = ff_create_field('ff-builder-key', 'text',
		array(
			'name' => 'key',
			'label' => __('Key', 'fields-framework'),
		),
		true
	);

	$options['value'] = ff_create_field('ff-builder-value', 'text',
		array(
			'name' => 'value',
			'label' => __('Value', 'fields-framework'),
		),
		true
	);

	$options['skip-save'] = ff_create_field('ff-builder-skip-save', 'select',
		array(
			'name' => 'skip-save',
			'label' => __('Skip Save', 'fields-framework'),
			'prepend_blank' => true,
			'options' => array('false' => 'No', 'true' => 'Yes'),
		),
		true
	);

	$options['page-title'] = ff_create_field('ff-builder-page-title', 'text',
		array(
			'name' => 'page-title',
			'label' => __('Page Title', 'fields-framework'),
			'validator' => array('validation-engine' => 'validate[required]'),
		),
		true
	);

	$options['menu-title'] = ff_create_field('ff-builder-menu-title', 'text',
		array(
			'name' => 'menu-title',
			'label' => __('Menu Title', 'fields-framework'),
			'validator' => array('validation-engine' => 'validate[required]'),
		),
		true
	);

	$options['capability'] = ff_create_field('ff-builder-capability', 'text',
		array(
			'name' => 'capability',
			'label' => __('Capability', 'fields-framework'),
			'placeholder' => 'manage_options',
		),
		true
	);

	$options['icon-url'] = ff_create_field('ff-builder-icon-url', 'text',
		array(
			'name' => 'icon-url',
			'label' => __('Icon URL', 'fields-framework'),
		),
		true
	);

	$options['position'] = ff_create_field('ff-builder-position', 'text',
		array(
			'name' => 'position',
			'label' => __('Position', 'fields-framework'),
		),
		true
	);

	$options['parent-uid'] = ff_create_field('ff-builder-parent-uid', 'text',
		array(
			'name' => 'parent-uid',
			'label' => __('Parent UID', 'fields-framework'),
			'validator' => array('validation-engine' => 'validate[required]'),
		),
		true
	);

	$options['id'] = ff_create_field('ff-builder-id', 'text',
		array(
			'name' => 'id',
			'label' => __('ID', 'fields-framework'),
		),
		true
	);

	$options['title'] = ff_create_field('ff-builder-title', 'text',
		array(
			'name' => 'title',
			'label' => __('Title', 'fields-framework'),
			'validator' => array('validation-engine' => 'validate[required]'),
		),
		true
	);

	$post_types = get_post_types(null, 'objects');

	foreach($post_types as $post_type) {
		$post_types_options[$post_type->name] = $post_type->labels->name;
	}

	$options['post-types'] = ff_create_field('ff-builder-post-types', 'select',
		array(
			'name' => 'post-types',
			'label' => __('Post Types', 'fields-framework'),
			'prepend_blank' => true,
			'validator' => array('validation-engine' => 'validate[required]'),
			'multiple' => true,
			'options' => $post_types_options
		),
		true
	);

	$options['context'] = ff_create_field('ff-builder-context', 'text',
		array(
			'name' => 'context',
			'label' => __('Context', 'fields-framework'),
			'placeholder' => 'advanced',
		),
		true
	);

	$options['priority'] = ff_create_field('ff-builder-priority', 'text',
		array(
			'name' => 'priority',
			'label' => __('Priority', 'fields-framework'),
			'placeholder' => 'default',
		),
		true
	);

	$page_templates = get_page_templates();

	foreach($page_templates as $key => $value) {
		$page_templates_options[$value] = $key;
	}

	$options['page-templates'] = ff_create_field('ff-builder-page-templates', 'select',
		array(
			'name' => 'page-templates',
			'label' => __('Page Templates', 'fields-framework'),
			'prepend_blank' => true,
			'multiple' => true,
			'options' => $page_templates_options
		),
		true
	);

	$post_formats_options = array(
		'aside' => __('Aside', 'fields-framework'),
		'chat' => __('Chat', 'fields-framework'),
		'gallery' => __('Gallery', 'fields-framework'),
		'link' => __('Link', 'fields-framework'),
		'image' => __('Image', 'fields-framework'),
		'quote' => __('Quote', 'fields-framework'),
		'status' => __('Status', 'fields-framework'),
		'video' => __('Video', 'fields-framework'),
		'audio ' => __('Audio', 'fields-framework'),
	);

	$options['post-formats'] = ff_create_field('ff-builder-post-formats', 'select',
		array(
			'name' => 'post-formats',
			'label' => __('Post Formats', 'fields-framework'),
			'prepend_blank' => true,
			'multiple' => true,
			'options' => $post_formats_options
		),
		true
	);

	$taxonomies = get_taxonomies(null, 'objects');

	foreach($taxonomies as $taxonomy) {
		$taxonomies_options[$taxonomy->name] = $taxonomy->labels->name;
	}
	
	$options['taxonomies'] = ff_create_field('ff-builder-taxonomies', 'select',
		array(
			'name' => 'taxonomies',
			'label' => __('Taxonomies', 'fields-framework'),
			'prepend_blank' => true,
			'multiple' => true,
			'validator' => array('validation-engine' => 'validate[required]'),
			'options' => $taxonomies_options
		),
		true
	);

	$options['name'] = ff_create_field('ff-builder-name', 'text',
		array(
			'name' => 'name',
			'label' => __('Name', 'fields-framework'),
		),
		true
	);
	
	$options['label'] = ff_create_field('ff-builder-label', 'text',
		array(
			'name' => 'label',
			'label' => __('Label', 'fields-framework'),
		),
		true
	);
	
	$options['class'] = ff_create_field('ff-builder-class', 'text',
		array(
			'name' => 'class',
			'label' => __('Class', 'fields-framework'),
		),
		true
	);

	$options['description'] = ff_create_field('ff-builder-description', 'textarea',
		array(
			'name' => 'description',
			'label' => __('Description', 'fields-framework'),
		),
		true
	);

	$options['placeholder'] = ff_create_field('ff-builder-placeholder', 'text',
		array(
			'name' => 'placeholder',
			'label' => __('Placeholder', 'fields-framework'),
		),
		true
	);

	$options['default-value'] = ff_create_field('ff-builder-default-value', 'text',
		array(
			'name' => 'default-value',
			'label' => __('Default Value', 'fields-framework'),
		),
		true
	);

	$options['repeatable'] = ff_create_field('ff-builder-repeatable', 'select',
		array(
			'name' => 'repeatable',
			'label' => __('Repeatable', 'fields-framework'),
			'prepend_blank' => true,
			'options' => array('false' => 'No', 'true' => 'Yes'),
		),
		true
	);

	$options['minimal'] = ff_create_field('ff-builder-minimal', 'select',
		array(
			'name' => 'minimal',
			'label' => __('Minimal', 'fields-framework'),
			'prepend_blank' => true,
			'options' => array('false' => 'No', 'true' => 'Yes'),
		),
		true
	);

	$options['hide-content-editor'] = ff_create_field('ff-builder-hide-content-editor', 'select',
		array(
			'name' => 'hide-content-editor',
			'label' => __('Hide Content Editor', 'fields-framework'),
			'prepend_blank' => true,
			'options' => array('false' => 'No', 'true' => 'Yes'),
		),
		true
	);

	$options['library'] = ff_create_field('ff-builder-library', 'text',
		array(
			'name' => 'library',
			'label' => __('Library', 'fields-framework'),
		),
		true
	);

	$options['rows'] = ff_create_field('ff-builder-rows', 'text',
		array(
			'name' => 'rows',
			'label' => __('Rows', 'fields-framework'),
			'placeholder' => '5',
		),
		true
	);

	$options['cols'] = ff_create_field('ff-builder-cols', 'text',
		array(
			'name' => 'cols',
			'label' => __('Cols', 'fields-framework'),
			'placeholder' => '50',
		),
		true
	);

	$options['size'] = ff_create_field('ff-builder-size', 'text',
		array(
			'name' => 'size',
			'label' => __('Size', 'fields-framework'),
			'placeholder' => '5',
		),
		true
	);

	$options['date-format'] = ff_create_field('ff-builder-date-format', 'text',
		array(
			'name' => 'date-format',
			'label' => __('Date Format', 'fields-framework'),
			'placeholder' => 'mm/dd/yy',
		),
		true
	);

	$options['time-format'] = ff_create_field('ff-builder-time-format', 'text',
		array(
			'name' => 'time-format',
			'label' => __('Time Format', 'fields-framework'),
			'placeholder' => 'hh:mm:ss tt',
		),
		true
	);

	$options['options'] = ff_create_field('ff-builder-options', 'group',
		array(
			'name' => 'options',
			'label' => __('Options', 'fields-framework'),
			'repeatable' => true,
		),
		true
	);

	$options['options']->add_field($options['key']);

	$options['options']->add_field($options['value']);

	$options['parameters'] = ff_create_field('ff-builder-parameters', 'group',
		array(
			'name' => 'parameters',
			'label' => __('Parameters', 'fields-framework'),
			'repeatable' => true,
		),
		true
	);

	$options['parameters']->add_field($options['key']);

	$options['parameters']->add_field($options['value']);

	$options['settings'] = ff_create_field('ff-builder-settings', 'group',
		array(
			'name' => 'settings',
			'label' => __('Settings', 'fields-framework'),
			'repeatable' => true,
		),
		true
	);

	$options['settings']->add_field($options['key']);

	$options['settings']->add_field($options['value']);

	$options['validator'] = ff_create_field('ff-builder-validator', 'group',
		array(
			'name' => 'validator',
			'label' => __('Validator', 'fields-framework'),
			'repeatable' => true,
		),
		true
	);

	$options['validator']->add_field($options['key']);

	$options['validator']->add_field($options['value']);

	$options['multiple'] = ff_create_field('ff-builder-multiple', 'select',
		array(
			'name' => 'multiple',
			'label' => __('Multiple', 'fields-framework'),
			'prepend_blank' => true,
			'options' => array('false' => 'No', 'true' => 'Yes'),
		),
		true
	);

	$options['prepend-blank'] = ff_create_field('ff-builder-prepend-blank', 'select',
		array(
			'name' => 'prepend-blank',
			'label' => __('Prepend Blank', 'fields-framework'),
			'prepend_blank' => true,
			'options' => array('false' => 'No', 'true' => 'Yes'),
		),
		true
	);

	$options['arguments'] = ff_create_field('ff-builder-arguments-admin_menu', 'group',
		array(
			'name' => 'arguments',
			'label' => __('Arguments', 'fields-framework'),
		),
		true
	);

	$sections_options = array();

	if(!empty($builder['sections'])) {
		foreach($builder['sections'] as $section_uid => $section) {
			$sections_options[$section_uid] = $section_uid;
		}
	}

	$options['section-uid'] = ff_create_field('ff-builder-section-uid', 'select',
		array(
			'name' => 'section-uid',
			'label' => __('Section UID', 'fields-framework'),
			'validator' => array('validation-engine' => 'validate[required]'),
			'prepend_blank' => true,
			'options' => $sections_options,
		),
		true
	);

	$fields_options = array();

	if(!empty($builder['fields'])) {
		foreach($builder['fields'] as $field_uid => $field) {
			$fields_options[$field_uid] = $field_uid;
		}
	}

	$options['field-uid'] = ff_create_field('ff-builder-field-uid', 'select',
		array(
			'name' => 'field-uid',
			'label' => __('Field UID', 'fields-framework'),
			'validator' => array('validation-engine' => 'validate[required]'),
			'prepend_blank' => true,
			'options' => $fields_options,
		),
		true
	);

	$options['fields'] = ff_create_field('ff-builder-fields', 'select',
		array(
			'name' => 'fields',
			'label' => __('Fields', 'fields-framework'),
			'repeatable' => true,
			'prepend_blank' => true,
			'options' => $fields_options,
		),
		true
	);

	$associations['sections']['admin_menu'] = array(
		$options['page-title'],
		$options['menu-title'],
		$options['capability'],
		$options['icon-url'],
		$options['position'],
	);

	$associations['sections']['admin_sub_menu'] = array(
		$options['parent-uid'],
		$options['page-title'],
		$options['menu-title'],
		$options['capability'],
	);

	$associations['sections']['post'] = array(
		$options['title'],
		$options['post-types'],
		$options['context'],
		$options['priority'],
		$options['page-templates'],
		$options['post-formats'],
		$options['hide-content-editor'],
	);

	$associations['sections']['taxonomy'] = array(
		$options['taxonomies'],
	);

	$associations['sections']['widget'] = array(
		$options['title'],
	);

	$associations['sections']['user'] = array(
	);

	foreach($associations['sections'] as $key => $section) {
		array_unshift($associations['sections'][$key], $options['skip-save']);
	}

	$associations['fields']['group'] = array(
		$options['fields'],
	);

	$associations['fields']['text'] = array(
	);

	$associations['fields']['hidden'] = array(
	);

	$associations['fields']['media'] = array(
		$options['library'],
	);

	$associations['fields']['textarea'] = array(
		$options['cols'],
		$options['rows'],
	);

	$associations['fields']['checkbox'] = array(
		$options['options'],
		$options['multiple'],
	);

	$associations['fields']['radio'] = array(
		$options['options'],
	);

	$associations['fields']['select'] = array(
		$options['options'],
		$options['multiple'],
		$options['size'],
		$options['prepend-blank'],
	);

	$associations['fields']['select_posts'] = array(
		$options['multiple'],
		$options['size'],
		$options['prepend-blank'],
		$options['parameters'],
	);

	$associations['fields']['select_terms'] = array(
		$options['multiple'],
		$options['size'],
		$options['prepend-blank'],
		$options['parameters'],
		$options['taxonomies'],
	);

	$associations['fields']['select_users'] = array(
		$options['multiple'],
		$options['size'],
		$options['prepend-blank'],
		$options['parameters'],
	);

	$associations['fields']['editor'] = array(
		$options['settings'],
	);

	$associations['fields']['datetime'] = array(
		$options['date-format'],
		$options['time-format'],
	);

	$associations['fields']['colorpicker'] = array(
	);

	foreach($associations['fields'] as $key => $field) {
		array_unshift(
			$associations['fields'][$key],
			$options['name'],
			$options['default-value']
		);
	}

	foreach($associations['fields'] as $key => $field) {
		if($key == 'hidden') {
			continue;
		}

		array_unshift(
			$associations['fields'][$key],
			$options['id'],
			$options['label'],
			$options['class'],
			$options['description'],
			$options['placeholder'],
			$options['minimal'],
			$options['validator']
		);
	}

	foreach($associations['fields'] as $key => $field) {
		if($key == 'hidden' || $key == 'editor') {
			continue;
		}
		
		array_push(
			$associations['fields'][$key],
			$options['repeatable']
		);
	}

	if(!empty($_POST)) {
		if(!empty($_POST['action'])) {
			$area = $_POST['area'];

			if($area == 'sections' || $area == 'fields') {
				$uid = $_POST['uid'];

				$type = $_POST['type'];

				if($_POST['action'] == 'create' && !empty($builder[$area][$uid])) {
					if($area == 'sections') {
						?><p class="error-message"><?php printf(__('Section UID "%s" is not unique!', 'fields-framework'), $uid); ?></p><?php
					}
					elseif($area == 'fields') {
						?><p class="error-message"><?php printf(__('Field UID "%s" is not unique!', 'fields-framework'), $uid); ?></p><?php
					}
					
					return;
				}
				else {
					$builder[$area][$uid] = array('type' => $type, 'arguments' => $_POST['arguments']);
				}
			}
			elseif($area == 'fields-by-sections') {
				$section_uid = $_POST['section-uid'];

				$field_uid = $_POST['field-uid'];

				if(!empty($builder[$area][$section_uid]) && in_array($field_uid, $builder[$area][$section_uid])) {
					?><p class="error-message"><?php printf(__('Field UID "%s" is not unique to Section with UID "%s"!', 'fields-framework'), $field_uid, $section_uid); ?></p><?php
						
					return;
				}
				else {
					$builder[$area][$section_uid][] = $field_uid;
				}
			}
		}
		elseif(!empty($_POST['fields-by-sections'])) {
			$builder['fields-by-sections'] = $_POST['fields-by-sections'];
		}

		$builder = ff_sanitize($builder);

		update_option('ff-builder', $builder);
	}
	
	if(!empty($_GET['action']) && $_GET['action'] == 'delete') {
		if(empty($_GET['area']) || empty($_GET['uid'])) {
			return;
		}
		
		$area = $_GET['area'];

		$uid = $_GET['uid'];

		if($area == 'sections' || $area == 'fields') {
			unset($builder[$area][$uid]);
			
			if($area == 'sections' && !empty($builder['fields-by-sections'][$uid])) {
				unset($builder['fields-by-sections'][$uid]);
			}
			
			if($area == 'fields') {
				if(!empty($builder['fields-by-sections'])) {
					foreach($builder['fields-by-sections'] as $section_uid => &$section) {
						foreach($section as $key => $field_uid) {
							if($field_uid == $uid) {
								unset($section[$key]);
							}
						}
					}
				}

				foreach($builder['fields'] as $field_group_uid => &$field) {
					if($field['type'] == 'group' && !empty($field['arguments']['fields'])) {
						foreach($field['arguments']['fields'] as $key => $field_uid) {
							if($field_uid == $uid) {
								unset($field['arguments']['fields'][$key]);
							}
						}
					}
				}
			}
		}
		elseif($area == 'fields-by-sections') {
			if(ff_empty($_GET['index'])) {
				return;
			}

			$index = $_GET['index'];

			unset($builder[$area][$uid][$index]);
		}

		$builder = ff_sanitize($builder);

		update_option('ff-builder', $builder);
	}
	?>
	<div class="ff-builder">
	<h2 class="nav-tab-wrapper">  
		<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=sections'); ?>" class="nav-tab"><?php _e('Sections', 'fields-framework'); ?></a>
		<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields'); ?>" class="nav-tab"><?php _e('Fields', 'fields-framework'); ?></a>
		<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields-by-sections'); ?>" class="nav-tab"><?php _e('Fields By Sections', 'fields-framework'); ?></a>
	</h2>
	<?php
	$area = !empty($_GET['area']) ? $_GET['area'] : null;

	$action = !empty($_GET['action']) ? $_GET['action'] : null;

	$type = !empty($_GET['type']) ? $_GET['type'] : null;

	if(empty($area)) {
		?>
		<p><?php printf(__('To learn how to integrate the code generated below, please refer to the "%s" section of the documentation.', 'fields-framework'), '<a href="http://www.rhyzz.com/fields-framework.html#usage-instructions" target="_blank">Usage Instructions</a>'); ?></p>

		<p><?php printf(__('You must replace the "%s" part with the code you see below.', 'fields-framework'), '<code>// Add your function calls here!</code>'); ?></p>
		<?php
		echo '<p><textarea class="large-text" readonly="readonly" rows="25" cols="100">';

		if(!empty($builder['sections'])) {
			ff_builder_create($builder['sections'], 'section');
		}

		if(!empty($builder['fields'])) {
			ff_builder_create($builder['fields'], 'field');
		}

		if(!empty($builder['fields'])) {
			foreach($builder['fields'] as $field_group_uid => $field) {
				if($field['type'] == 'group' && !empty($field['arguments']['fields'])) {
					foreach($field['arguments']['fields'] as $field_uid) {
						echo "ff_add_field_to_field_group('{$field_group_uid}', '{$field_uid}');\n\n";
					}
				}
			}
		}

		if(!empty($builder['fields-by-sections'])) {
			foreach($builder['fields-by-sections'] as $section_uid => $section) {
				foreach($section as $field_uid) {
					echo "ff_add_field_to_section('{$section_uid}', '{$field_uid}');\n\n";
				}
			}
		}

		echo '</textarea></p>';
	}
	elseif($area == 'sections') {
		?>
		<h3 class="nav-tab-wrapper">  
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=sections&type=admin_menu'); ?>" class="nav-tab"><?php _e('Admin Menu', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=sections&type=admin_sub_menu'); ?>" class="nav-tab"><?php _e('Admin Sub Menu', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=sections&type=post'); ?>" class="nav-tab"><?php _e('Post', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=sections&type=taxonomy'); ?>" class="nav-tab"><?php _e('Taxonomy', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=sections&type=widget'); ?>" class="nav-tab"><?php _e('Widget', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=sections&type=user'); ?>" class="nav-tab"><?php _e('User', 'fields-framework'); ?></a>
		</h3>
		<?php
	}
	elseif($area == 'fields') {
		?>
		<h3 class="nav-tab-wrapper">  
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=group'); ?>" class="nav-tab"><?php _e('Group', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=text'); ?>" class="nav-tab"><?php _e('Text', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=hidden'); ?>" class="nav-tab"><?php _e('Hidden', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=media'); ?>" class="nav-tab"><?php _e('Media', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=textarea'); ?>" class="nav-tab"><?php _e('Textarea', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=checkbox'); ?>" class="nav-tab"><?php _e('Checkbox', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=radio'); ?>" class="nav-tab"><?php _e('Radio', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=select'); ?>" class="nav-tab"><?php _e('Select', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=select_posts'); ?>" class="nav-tab"><?php _e('Select Posts', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=select_terms'); ?>" class="nav-tab"><?php _e('Select Terms', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=select_users'); ?>" class="nav-tab"><?php _e('Select Users', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=editor'); ?>" class="nav-tab"><?php _e('Editor', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=datetime'); ?>" class="nav-tab"><?php _e('DateTime', 'fields-framework'); ?></a>
			<a href="<?php echo admin_url('/admin.php?page=ff-builder&area=fields&type=colorpicker'); ?>" class="nav-tab"><?php _e('ColorPicker', 'fields-framework'); ?></a>
		</h3>
		<?php
	}

	if(empty($action) && !empty($area)) {
		if($area == 'sections' || $area == 'fields') {
			if(!empty($type)) {
				?>
				<p><a href="<?php echo admin_url('/admin.php?page=ff-builder&area=' . $area . (!empty($type) ? '&type=' . $type : null) . '&action=create'); ?>" class="button"><?php _e('Create New', 'fields-framework'); ?></a></p>
				<?php
			}
		}
		elseif($area == 'fields-by-sections') {
			?>
			<p><a href="<?php echo admin_url('/admin.php?page=ff-builder&area=' . $area . '&action=create'); ?>" class="button"><?php _e('Create New', 'fields-framework'); ?></a></p>
			<?php
		}
	}

	if($action == 'create' || $action == 'edit') {
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?page=ff-builder" method="post">';

		$options['area']->set_saved_value($area);

		$options['area']->container();

		$options['action']->set_saved_value($action);

		$options['action']->container();

		if($area == 'sections' || $area == 'fields') {
			if($action == 'edit') {
				$uid = $_GET['uid'];

				$options['uid-hidden']->set_saved_value($uid);

				$options['uid-hidden']->container();
			}
			else {
				$options['uid-text']->container();
			}

			$options['type']->set_saved_value($type);

			$options['type']->container();

			foreach($associations[$area][$type] as $field) {
				$options['arguments']->add_field($field);
			}

			if($action == 'edit') {
				$options['arguments']->set_saved_value($builder[$area][$uid]['arguments']);
			}

			$options['arguments']->container();
		}
		elseif($area == 'fields-by-sections') {
			$options['section-uid']->container();

			$options['field-uid']->container();
		}

		submit_button();
		
		echo '</form>';
	}
	else {	
		if(!empty($builder[$area])) {
			if($area == 'fields-by-sections') {
				echo '<form action="' . $_SERVER['PHP_SELF'] . '?page=ff-builder" method="post">';
	
				$options['area']->set_saved_value($area);
	
				$options['area']->container();
			}
	
			echo '<table class="widefat">';
	
			echo '<thead>';
	
			echo '<tr>';
	
			echo '<th>Arguments</th>';
	
			echo '<th>Actions</th>';
	
			echo '</tr>';
	
			echo '</thead>';
	
			echo '<tbody>';
	
			foreach($builder[$area] as $uid => $item) {
				if(!empty($type) && $item['type'] != $type) {
					continue;
				}
				
				echo '<tr>';
	
				echo '<td>';
	
				if($area == 'sections' || $area == 'fields') {
					if(empty($type)) {
						echo '<h3>' . ucwords(str_replace(array('-', '_'), ' ', $uid)) . ' (' . ucwords(str_replace(array('-', '_'), ' ', $item['type'])) . ')</h3>';
					}
					else {
						echo '<h3>' . ucwords(str_replace(array('-', '_'), ' ', $uid)) . '</h3>';
					}

					if(!empty($item['arguments'])) {
						foreach($item['arguments'] as $key => $value) {
							echo '<dl>';
		
							echo '<dt>' . ucwords(str_replace('-', ' ', $key)) . '</dt>';
		
							if(is_scalar($value)) {
								if(ff_empty($value)) {
									$value = 'null';
								}
							}
							elseif(is_array($value)) {
								$value = implode(', ', $value);
							}
		
							echo "<dd>{$value}</dd>";
		
							echo '</dl>';
						}
					}
				}
				elseif($area == 'fields-by-sections') {
					echo '<strong>' . ucwords(str_replace('-', ' ', $uid)) . ' (' . ucwords(str_replace('_', ' ', $builder['sections'][$uid]['type'])) . ')</strong>';
	
					echo '<ul class="ff-builder-fields-by-sections">';
	
					foreach($item as $key => $field) {
						?>
						<li>
							<?php echo ucwords(str_replace('-', ' ', $field)) . ' (' . ucwords(str_replace('-', ' ', $builder['fields'][$field]['type'])) . ')'; ?>
							
							<input type="hidden" name="fields-by-sections[<?php echo $uid; ?>][]" value="<?php echo $field; ?>" />

							<?php echo '<a href="' . admin_url('/admin.php?page=ff-builder&area=' . $area . '&action=delete&uid=' . $uid . '&index=' . $key) . '" class="button button-small">' . __('Delete', 'fields-framework') . '</a>'; ?>
						</li>
						<?php
					}
	
					echo '</ul>';
				}

				echo '</td>';
	
				echo '<td>';
	
				if($area == 'sections' || $area == 'fields') {
					echo '<a href="' . admin_url('/admin.php?page=ff-builder&area=' . $area . '&type=' . $item['type'] . '&action=edit&uid=' . $uid) . '" class="button button-small">' . __('Edit', 'fields-framework') . '</a> ';

					echo '<a href="' . admin_url('/admin.php?page=ff-builder&area=' . $area . '&action=delete&uid=' . $uid) . '" class="button button-small">' . __('Delete', 'fields-framework') . '</a>';
				}

				echo '</td>';
	
				echo '</tr>';
			}
	
			echo '</tbody>';
	
			echo '</table>';
	
			if($area == 'fields-by-sections') {
				submit_button();
	
				echo '</form>';
			}
		}
	}
	
	?></div><?php
}

function ff_builder_create($items, $area) {
	foreach($items as $uid => $item) {
		echo "ff_create_{$area}('{$uid}', '{$item['type']}', array(";

		if(!empty($item['arguments'])) {
			foreach($item['arguments'] as $key => $value) {
				if($item['type'] == 'group' && $key == 'fields') {
					continue;
				}
				
				$key = str_replace('-', '_', $key);
				
				$value = ff_builder_create_process($value);
	
				echo "\n\t\t'$key' => {$value},";
			}
		}

		echo "\n\t)\n);\n\n";
	}
}

function ff_builder_create_process($value) {
	if(is_scalar($value)) {
		if(ff_empty($value)) {
			$value = 'null';
		}
		elseif($value !== 'true' && $value !== 'false' && !is_numeric($value)) {
			$value = "'{$value}'";
		}
	}
	elseif(is_array($value)) {
		foreach($value as &$value_1) {
			if(is_scalar($value_1) && !ff_empty($value_1) && $value_1 !== 'true' && $value_1 !== 'false' && !is_numeric($value_1)) {
				$value_1 = "'{$value_1}'";
			}
			elseif(is_array($value_1)) {
				if(ff_empty($value_1['value'])) {
					$value_1 = "'{$value_1['key']}'";
				}
				else {
					$value_1 = "'{$value_1['key']}' => '{$value_1['value']}'";
				}
			}
		}
		
		$value = 'array(' . implode(', ', $value) . ')';
	}
	
	return $value;
}
?>