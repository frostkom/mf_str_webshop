<?php

if(!defined('ABSPATH'))
{
	$dirname = dirname(__FILE__);
	$separator = (strpos($dirname, "/") !== false ? "/" : "\\");
	$folder = str_replace($separator."wp-content".$separator."plugins".$separator."mf_str_webshop".$separator."view", $separator, $dirname);

	require_once($folder."wp-load.php");
}

do_action('run_cache', array('suffix' => 'html'));

include_once("../include/classes.php");

$obj_str_webshop = new mf_str_webshop();

$obj_str_webshop->page_header = "<html><body>";
$obj_str_webshop->page_footer = "</body></html>";

$setting_str_webshop_post_id = get_option('setting_str_webshop_post_id');

if($setting_str_webshop_post_id > 0)
{
	if($obj_str_webshop->get_header_footer_from_page($setting_str_webshop_post_id))
	{
		// Do nothing
	}

	else
	{
		do_log("I could not find the str-ecom tag on ".get_permalink($setting_str_webshop_post_id));
	}
}

echo $obj_str_webshop->page_header;

	$setting_str_webshop_customer_number = get_option('setting_str_webshop_customer_number');

	if($setting_str_webshop_customer_number > 0)
	{
		echo $obj_str_webshop->get_view_code(array('post_id' => $setting_str_webshop_post_id, 'customer_number' => $setting_str_webshop_customer_number));
	}

	else
	{
		$error_text = __("I could not find a Customer Number. Please add one and try again.", $obj_str_webshop->lang_key);

		echo get_notification();
	}

echo $obj_str_webshop->page_footer;