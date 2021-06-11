<?php
/*
Plugin Name: MF STR Webshop
Plugin URI: https://github.com/frostkom/mf_str_webshop
Description: Wordpress plugin that adds support for an STR webshop
Version: 1.5.7
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://frostkom.se
Text Domain: lang_str_webshop
Domain Path: /lang

Depends: MF Base
GitHub Plugin URI: frostkom/mf_str_webshop
*/

if(is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	load_plugin_textdomain('lang_str_webshop', false, dirname(plugin_basename(__FILE__)).'/lang/');

	$obj_str_webshop = new mf_str_webshop();

	add_action('cron_base', array($obj_str_webshop, 'cron_base'), mt_rand(1, 10));

	add_action('init', array($obj_str_webshop, 'init'));

	if(is_admin())
	{
		register_uninstall_hook(__FILE__, 'uninstall_str_webshop');

		add_action('admin_init', array($obj_str_webshop, 'settings_str_webshop'));
		add_action('admin_menu', array($obj_str_webshop, 'admin_menu'));
	}

	else
	{
		add_action('wp_head', array($obj_str_webshop, 'wp_head'), 0);
	}

	add_shortcode('mf_str_webshop', array($obj_str_webshop, 'shortcode_str_webshop'));

	add_filter('recommend_config', array($obj_str_webshop, 'recommend_config'));

	function uninstall_str_webshop()
	{
		global $obj_str_webshop;

		mf_uninstall_plugin(array(
			'post_types' => array($obj_str_webshop->post_type),
			'options' => array('setting_str_webshop_post_id', 'setting_str_webshop_api_mode', 'setting_str_webshop_customer_number', 'setting_str_webshop_google_analytics', 'setting_str_webshop_include_css', 'setting_str_webshop_include_extra_css', 'setting_str_webshop_header_selector', 'setting_str_webshop_sitemap_api_activate', 'setting_str_webshop_new_version_action', 'option_str_webshop_notified_version'),
		));
	}
}