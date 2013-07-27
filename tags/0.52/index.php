<?php
/*
Plugin Name: Fields Framework
Plugin URI: http://www.rhyzz.com/fields-framework.html
Description: A framework which can be used by developers to add fields to various sections of the administration panel.
Version: 0.52
Author: Naif Amoodi
Author URI: http://www.rhyzz.com/
*/

/* Load this after all plugins and the active theme has been loaded so that users can override functions or classes if they wish to */
add_action('after_setup_theme', 'ff_load');

if(!function_exists('ff_load')) {
	function ff_load() {
		foreach(array('classes.php', 'functions.php') as $file) {
			require_once(plugin_dir_path(__FILE__) . '/php/' . $file);
		}

		load_plugin_textdomain('fields-framework', false, dirname(plugin_basename(dirname(__FILE__))) . '/languages/');

		/* This actions are only used in the backend so putting them inside a conditional */
		if(is_admin()) {
			add_action('admin_menu', 'ff_admin_menu');

			add_action('wp_loaded', 'ff_save_options');

			add_action('created_term', 'ff_save_term', 10, 3);

			add_action('edited_term', 'ff_save_term', 10, 3);

			add_action('delete_term', 'ff_delete_term', 10, 4);

			add_action('admin_enqueue_scripts', 'ff_admin_enqueue_scripts');
		}
	}
}
?>