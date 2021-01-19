<?php

class mf_str_webshop
{
	function __construct(){}

	function get_base_path($data = array())
	{
		global $wpdb;

		if(!isset($data['post_id'])){		$data['post_id'] = get_option('setting_str_webshop_post_id');}

		$site_domain = get_url_part(array('type' => 'domain'));
		$post_url = get_permalink($data['post_id']);

		return str_replace($site_domain, "", $post_url);
	}

	function get_view_code($data = array())
	{
		if(!isset($data['post_id'])){			$data['post_id'] = get_option('setting_str_webshop_post_id');}
		if(!isset($data['customer_number'])){	$data['customer_number'] = get_option('setting_str_webshop_customer_number');}

		if($data['customer_number'] > 0)
		{
			if(strpos($_SERVER['REQUEST_URI'], "mf_str_webshop"))
			{
				$base_path = get_url_part(array('type' => 'path'))."wp-content/plugins/mf_str_webshop/view";
			}

			else
			{
				$base_path = rtrim($this->get_base_path($data), "/");
			}

			$setting_str_webshop_api_mode = get_option('setting_str_webshop_api_mode', 'live');
			$setting_str_webshop_google_analytics = get_option('setting_str_webshop_google_analytics');
			$setting_str_webshop_include_css = get_option('setting_str_webshop_include_css');
			$setting_str_webshop_header_selector = get_option_or_default('setting_str_webshop_header_selector', ".fl-theme-builder-header-sticky");

			switch($setting_str_webshop_api_mode)
			{
				case 'test':
					$script_url = "https://ecommercetest.str.se/static/js/";
				break;

				default:
				case 'live':
					$script_url = "https://ecommerce.str.se/static/js/";
				break;
			}

			switch($setting_str_webshop_include_css)
			{
				case 'no':
					$script_url .= "str_ecommerce_app_no_css.js";
				break;

				default:
				case 'yes':
					$script_url .= "str_ecommerce_app.js";
				break;
			}

			$out = "<noscript>You need to enable JavaScript to run this app.</noscript>
			<div id='str-ecom' data-customer-number='".$data['customer_number']."' data-base-path='".$base_path."'";

				if($setting_str_webshop_google_analytics != '')
				{
					$out .= " data-ga-id='".$setting_str_webshop_google_analytics."'";
				}

				if($setting_str_webshop_header_selector != '')
				{
					$out .= " data-header='".$setting_str_webshop_header_selector."'";
				}

			$out .= "></div>
			<script src='".$script_url."'></script>";
		}

		else
		{
			$out = "<p id='str-ecom'>"
				.__("We do not have a webshop yet...", 'lang_str_webshop');

				if(IS_EDITOR)
				{
					$out .= "&nbsp;<a href='".admin_url("options-general.php?page=settings_mf_base#settings_str_webshop")."'>".__("Go to Settings and enter your Customer Number", 'lang_str_webshop')."</a>";
				}

			$out .= "</p>";
		}

		return $out;
	}

	function get_api_mode_for_select()
	{
		return array(
			'test' => __("Test", 'lang_str_webshop'),
			'live' => __("Live", 'lang_str_webshop'),
		);
	}

	function cron_base()
	{
		global $wpdb;

		$obj_cron = new mf_cron();
		$obj_cron->start(__CLASS__);

		if($obj_cron->is_running == false)
		{
			$setting_str_webshop_post_id = get_option('setting_str_webshop_post_id');
			$setting_str_webshop_api_mode = get_option('setting_str_webshop_api_mode');
			$setting_str_webshop_customer_number = get_option('setting_str_webshop_customer_number');
			$setting_str_webshop_google_analytics = get_option('setting_str_webshop_google_analytics');
			$setting_str_webshop_include_css = get_option('setting_str_webshop_include_css');
			$setting_str_webshop_header_selector = get_option('setting_str_webshop_header_selector');

			if(!($setting_str_webshop_post_id > 0) || $setting_str_webshop_api_mode == '' || $setting_str_webshop_customer_number == '' || $setting_str_webshop_google_analytics == '' || $setting_str_webshop_include_css == '' || $setting_str_webshop_header_selector == '')
			{
				$result = $wpdb->get_results($wpdb->prepare("SELECT post_id, meta_value FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_status = %s AND meta_key = %s AND meta_value LIKE %s ORDER BY post_modified DESC LIMIT 0, 1", 'publish', '_fl_builder_data', '%data-customer-number%'));

				foreach($result as $r)
				{
					$post_id = $r->post_id;
					$meta_value = $r->meta_value;

					if(!($setting_str_webshop_post_id > 0))
					{
						update_option('setting_str_webshop_post_id', $post_id);
					}

					if($meta_value != '')
					{
						if($setting_str_webshop_api_mode == '')
						{
							$setting_str_webshop_api_mode = (preg_match("/ecommercetest\.str\.se/i", $meta_value) ? 'test' : 'live');

							update_option('setting_str_webshop_api_mode', $setting_str_webshop_api_mode);
						}

						if($setting_str_webshop_customer_number == '')
						{
							$data_customer_number = get_match("/data-customer-number\=[\"\'](.*?)[\"\']/i", $meta_value, false);

							if($data_customer_number > 0)
							{
								update_option('setting_str_webshop_customer_number', $data_customer_number);
							}
						}

						if($setting_str_webshop_google_analytics == '')
						{
							$data_ga_id = get_match("/data-ga-id\=[\"\'](.*?)[\"\']/i", $meta_value, false);

							if($data_ga_id != '')
							{
								update_option('setting_str_webshop_google_analytics', $data_ga_id);
							}
						}

						if($setting_str_webshop_include_css == '')
						{
							$setting_str_webshop_include_css = (preg_match("/str_ecommerce_app_no_css/i", $meta_value) ? 'no' : 'yes');

							update_option('setting_str_webshop_include_css', $setting_str_webshop_include_css);
						}

						if($setting_str_webshop_header_selector == '')
						{
							$data_header = get_match("/data-header\=[\"\'](.*?)[\"\']/i", $meta_value, false);

							if($data_header != '')
							{
								update_option('setting_str_webshop_header_selector', $data_header);
							}
						}
					}
				}
			}

			if($setting_str_webshop_post_id > 0)
			{
				global $obj_base;

				$post_content = mf_get_post_content($setting_str_webshop_post_id);

				if($post_content == '' || (strpos($post_content, "str-ecom") === false && strpos($post_content, "[mf_str_webshop]") === false && strpos($post_content, "wp:fl-builder/layout") === false))
				{
					$post_data = array(
						'ID' => $setting_str_webshop_post_id,
						'post_content' => $post_content."[mf_str_webshop]",
					);

					wp_update_post($post_data);
				}

				if(!isset($obj_base))
				{
					$obj_base = new mf_base();
				}

				if($obj_base->get_server_type() == 'apache')
				{
					$this->recommend_config(array('file' => get_home_path().".htaccess", 'html' => ''));
				}
			}
		}

		$obj_cron->end();
	}

	function settings_str_webshop()
	{
		$options_area = __FUNCTION__;

		add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

		$arr_settings = array(
			'setting_str_webshop_post_id' => __("Page", 'lang_str_webshop'),
			'setting_str_webshop_api_mode' => __("API Mode", 'lang_str_webshop'),
			'setting_str_webshop_customer_number' => __("Customer Number", 'lang_str_webshop'),
			'setting_str_webshop_google_analytics' => __("Google Analytics", 'lang_str_webshop'),
			'setting_str_webshop_include_css' => __("Include CSS", 'lang_str_webshop'),
			'setting_str_webshop_include_extra_css' => __("Include Extra CSS", 'lang_str_webshop'),
			'setting_str_webshop_header_selector' => __("Header Selector", 'lang_str_webshop'),
		);

		show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
	}

	function settings_str_webshop_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Webshop", 'lang_str_webshop'));
	}

	function setting_str_webshop_post_id_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		$arr_data = array();
		get_post_children(array('add_choose_here' => true), $arr_data);

		echo show_select(array('data' => $arr_data, 'name' => $setting_key, 'value' => $option, 'suffix' => get_option_page_suffix(array('value' => $option))));
	}

	function setting_str_webshop_api_mode_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'live');

		echo show_select(array('data' => $this->get_api_mode_for_select(), 'name' => $setting_key, 'value' => $option));
	}

	function setting_str_webshop_customer_number_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option));
	}

	function setting_str_webshop_google_analytics_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, get_option('setting_analytics_google'));

		$suffix = ($option == '' ? "<a href='//analytics.google.com/analytics/web/'>".__("Get yours here", 'lang_str_webshop')."</a>" : "");

		echo show_textfield(array('name' => $setting_key, 'value' => $option, 'placeholder' => "UA-0000000-0", 'suffix' => $suffix));
	}

	function setting_str_webshop_include_css_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'yes');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option, 'description' => __("This adds basic styling when the Javascript is loaded", 'lang_str_webshop')));
	}

	function setting_str_webshop_include_extra_css_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option, 'description' => __("This adds compatibility styling for the cart when it is open", 'lang_str_webshop')));
	}

	function setting_str_webshop_header_selector_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_textfield(array('name' => $setting_key, 'value' => $option, 'placeholder' => "#header, .header"));
	}

	function wp_head()
	{
		if(get_option('setting_str_webshop_include_extra_css') == 'yes')
		{
			$plugin_include_url = plugin_dir_url(__FILE__);
			$plugin_version = get_plugin_version(__FILE__);

			mf_enqueue_style('style_str_webshop', $plugin_include_url."style.css", $plugin_version);
		}
	}

	function shortcode_str_webshop()
	{
		return $this->get_view_code();
	}

	function recommend_config($data)
	{
		global $obj_base;

		if(!isset($obj_base))
		{
			$obj_base = new mf_base();
		}

		if(!isset($data['file'])){		$data['file'] = '';}

		if(get_option('setting_str_webshop_post_id') > 0)
		{
			$subfolder = get_url_part(array('type' => 'path'));
			$base_path = trim($this->get_base_path(), "/");

			switch($obj_base->get_server_type())
			{
				default:
				case 'apache':
					$update_with = "<IfModule mod_rewrite.c>\r\n"
					."	RewriteEngine On\r\n";

					/*if($subfolder != "")
					{
						$update_with .= "	RewriteBase ".$subfolder."\r\n";
					}*/

					$update_with .= "\r\n"
					."	RewriteCond %{REQUEST_URI} !^/".$base_path."/$\r\n"
					."	RewriteCond %{REQUEST_URI} ^/".$base_path."/(.*)$\r\n"
					."	RewriteRule (.*) ".$subfolder."wp-content/plugins/mf_str_webshop/view/ [L]\r\n"
					."</IfModule>";
				break;

				case 'nginx':
					$update_with = "location ~ /".$base_path."/$ {}\r\n"
					."\r\n"
					."location / {\r\n"
					."	rewrite ^/".$base_path."/(.*)$ ".$subfolder."wp-content/plugins/mf_str_webshop/view/ break;\r\n"
					."}";
				break;
			}

			$data['html'] .= $obj_base->update_config(array(
				'plugin_name' => "MF STR Webshop",
				'file' => $data['file'],
				'update_with' => $update_with,
				'auto_update' => true,
			));
		}

		return $data;
	}
}