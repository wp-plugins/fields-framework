<?php
/*
Plugin Name: Fields Framework
Plugin URI: http://www.rhyzz.com/fields-framework.html
Description: A framework which can be used by developers to add fields to various areas of the administration panel.
Version: 0.11.1
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
			// This must be loading as a standlone plugin from within a theme
			FF_Registry::$plugins_url = get_template_directory_uri() . '/' . basename(dirname(__FILE__));
		}
		else {
			FF_Registry::$plugins_url = plugins_url(null, __FILE__);
		}

		load_plugin_textdomain('fields-framework', false, dirname(plugin_basename(__FILE__)) . '/languages/');

		add_action('widgets_init', 'ff_widgets_init');

		/* This actions are only used in the backend so putting them inside a conditional */
		if(is_admin()) {
			/* Priority set to 1 so that this is called before any other call */
			add_action('admin_menu', 'ff_admin_menu', 1);

			add_action('wp_loaded', 'ff_save_options');

			add_action('created_term', 'ff_save_term', 10, 3);

			add_action('edited_term', 'ff_save_term', 10, 3);

			add_action('delete_term', 'ff_delete_term', 10, 4);

			add_action('admin_enqueue_scripts', 'ff_admin_enqueue_scripts');
		}
	}
}
?>