<?php
/*
Plugin Name: Fields Framework
Plugin URI: http://www.rhyzz.com/fields-framework.html
Description: A framework which can be used by developers to add fields to various areas of the administration panel.
Version: 0.17
Author: Naif Amoodi
Author URI: http://www.rhyzz.com/
*/

if(defined('FF_INSTALLED')) {
	return;
}

define('FF_INSTALLED', true);

/* Load this after all plugins and the active theme has been loaded so that users can override functions or classes if they wish to */
add_action('after_setup_theme', 'ff_load');

if(!function_exists('ff_load')) {
	function ff_load() {
		$files = array('classes.php', 'functions.php', 'admin-menu.php');

		foreach($files as $file) {
			require_once(plugin_dir_path(__FILE__) . 'php/' . $file);
		}

		if(strpos(plugins_url(__FILE__), str_replace(ABSPATH, null, get_template_directory())) !== false) {
			// Plugin is being loaded from inside a theme (as an embedded standalone version)
			FF_Registry::$plugins_url = get_template_directory_uri() . '/' . basename(dirname(__FILE__));
		}
		else {
			FF_Registry::$plugins_url = plugins_url(null, __FILE__);
		}

		load_plugin_textdomain('fields-framework', false, dirname(plugin_basename(__FILE__)) . '/languages/');

		/* Adding to the widgets_init action from an init action would be too late hence register the widget here itself */
		add_action('widgets_init', array('FF_Widget', 'add'));

		if(is_admin()) {
			add_action('admin_enqueue_scripts', 'ff_admin_enqueue_scripts');
		}
	}
}
?>