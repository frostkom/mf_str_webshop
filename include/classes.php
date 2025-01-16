<?php

class mf_str_webshop
{
	var $meta_prefix = 'str_webshop_';
	var $post_type = "";
	var $github_settings_url = "";
	var $github_access_token_start = "ghp_i4v";
	var $page_header = "";
	var $page_footer = "";

	function __construct()
	{
		$this->post_type = $this->meta_prefix.'page';

		//$this->github_settings_url = (is_multisite() ? network_admin_url("settings.php?page=github-updater&tab=github_updater_settings&subtab=github") : admin_url("options-general.php?page=github-updater&tab=github_updater_settings&subtab=github"));
		$this->github_settings_url = (is_multisite() ? network_admin_url("settings.php?page=git-updater&tab=git_updater_settings&subtab=github") : admin_url("options-general.php?page=git-updater&tab=git_updater_settings&subtab=github"));
	}

	function get_base_path($data = array())
	{
		global $wpdb;

		if(!isset($data['post_id'])){		$data['post_id'] = get_option('setting_str_webshop_post_id');}

		$site_domain = get_url_part(array('type' => 'domain'));
		$post_url = get_permalink($data['post_id']);

		return str_replace($site_domain, "", $post_url);
	}

	function get_header_footer_from_page($setting_str_webshop_post_id)
	{
		$this->page_header = $this->page_footer = "";

		//
		##########################
		/*list($content, $headers) = get_url_content(array(
			'url' => get_site_url(),
			'catch_head' => true,
		));

		switch($headers['http_code'])
		{
			case 200:
			case 201:
				// Where do I split and insert h1 etc?
				list($part_one, $part_two) = explode("str-ecom", $content, 2);

				list($this->page_header, $rest) = explode("<body>", $part_one);
				list($rest, $this->page_footer) = explode("</body>", $part_two, 2);
			break;
		}*/
		##########################

		//
		##########################
		$post_url = get_permalink($setting_str_webshop_post_id);

		list($content, $headers) = get_url_content(array(
			'url' => $post_url,
			'catch_head' => true,
		));

		switch($headers['http_code'])
		{
			case 200:
			case 201:
				if(strpos($content, 'id="str-ecom"'))
				{
					list($part_one, $part_two) = explode('id="str-ecom"', $content);
				}

				else if(strpos($content, "id='str-ecom'"))
				{
					list($part_one, $part_two) = explode("id='str-ecom'", $content);
				}

				else if(strpos($content, "id=str-ecom "))
				{
					list($part_one, $part_two) = explode("id=str-ecom ", $content);
				}

				else
				{
					return false;
				}

				if(isset($part_one) && isset($part_two))
				{
					$arr_header = explode("<noscript>", $part_one);
					list($rest, $this->page_footer) = explode("</script>", $part_two, 2);

					$this->page_header = "";

					for($i = 0; $i < (count($arr_header) - 1); $i++)
					{
						$this->page_header .= $arr_header[$i];
					}
				}
			break;
		}
		##########################

		// BB does not let me fetch the correct header/footer this way
		##########################
		/*ob_start();

		$post = get_post($setting_str_webshop_post_id);

		get_header();

		echo "<div class='fl-archive container'>
			<div class='row'>";

				FLTheme::sidebar('left');

				echo "<div class='fl-content ";

					FLTheme::content_class();

				echo "' itemscope='itemscope' itemtype='http://schema.org/Blog'>";

					FLTheme::archive_page_header();

					echo "<h1>".$post->post_title."</h1>";

		$this->page_header = ob_get_clean();

		ob_start();

					echo "</div>";

				FLTheme::sidebar('right');

			echo "</div>
		</div>";

		get_footer();

		$this->page_footer = ob_get_clean();*/
		##########################

		return true;
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
				case 'test_if_logged_in':
					if(is_user_logged_in())
					{
						$script_url = "https://ecommercetest.str.se";
					}

					else
					{
						$script_url = "https://ecommerce.str.se";
					}
				break;

				case 'test':
					$script_url = "https://ecommercetest.str.se";
				break;

				default:
				case 'live':
					$script_url = "https://ecommerce.str.se";
				break;
			}

			$script_url .= "/static/js/";

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

			// This will make explode() in view/index.php split at the wrong position
			//mf_enqueue_script('script_str_webshop_ecom', $script_url);
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

	function get_type_for_select()
	{
		return array(
			'old' => __("Old", 'lang_str_webshop'),
			'iframe' => __("iFrame", 'lang_str_webshop'),
			'api' => __("API", 'lang_str_webshop'),
		);
	}

	function get_api_mode_for_select()
	{
		return array(
			'test' => __("Test", 'lang_str_webshop'),
			'live' => __("Live", 'lang_str_webshop'),
			'test_if_logged_in' => __("Test", 'lang_str_webshop')." (".__("If logged in", 'lang_str_webshop').")",
		);
	}

	function get_api_class_ids_for_select()
	{
		return array(
			'308' => __("Behörighet B", 'lang_str_webshop'),
			'322' => __("Behörighet BAut", 'lang_str_webshop'),
			'485' => __("Utökad B", 'lang_str_webshop'),
			'310' => __("Behörighet BE", 'lang_str_webshop'),
			//'309' => __("Behörighet B96 (ikke i bruk)", 'lang_str_webshop'),
			'307' => __("Behörighet A", 'lang_str_webshop'),
			'305' => __("Behörighet A1", 'lang_str_webshop'),
			'306' => __("Behörighet A2", 'lang_str_webshop'),
			//'323' => __("Behörighet AM147 (ikke i bruk)", 'lang_str_webshop'),
			'304' => __("Behörighet AM", 'lang_str_webshop'),
			'565' => __("Förarbevis - Moped", 'lang_str_webshop'),
			'313' => __("Behörighet C", 'lang_str_webshop'),
			'312' => __("Behörighet C1E", 'lang_str_webshop'),
			'311' => __("Behörighet C1", 'lang_str_webshop'),
			'314' => __("Behörighet CE", 'lang_str_webshop'),
			'317' => __("Behörighet D", 'lang_str_webshop'),
			'318' => __("Behörighet DE", 'lang_str_webshop'),
			'316' => __("Behörighet D1E", 'lang_str_webshop'),
			'315' => __("Behörighet D1", 'lang_str_webshop'),
			'319' => __("Behörighet T", 'lang_str_webshop'),
			'320' => __("Behörighet S", 'lang_str_webshop'),
			'354' => __("YSK Persontransport", 'lang_str_webshop'),
			'355' => __("YSK Godstransport", 'lang_str_webshop'),
			'356' => __("YSK Etterutdanning persontransport", 'lang_str_webshop'),
			'357' => __("YSK Etterutdanning godstransport", 'lang_str_webshop'),
			'358' => __("YSK Etterutdanning kombinert", 'lang_str_webshop'),
			'560' => __("Riskettan A", 'lang_str_webshop'),
			'561' => __("Riskettan B", 'lang_str_webshop'),
			'563' => __("Risktvåan A", 'lang_str_webshop'),
			'562' => __("Risktvåan B", 'lang_str_webshop'),
			'482' => __("Introduktionskurs", 'lang_str_webshop'),
			'571' => __("Terränghjuling", 'lang_str_webshop'),
			'404' => __("Presentkort", 'lang_str_webshop'),
			'538' => __("Taxi", 'lang_str_webshop'),
			'567' => __("Lift (Mobila arbetsplattformar)", 'lang_str_webshop'),
			'568' => __("Truck AB", 'lang_str_webshop'),
			'569' => __("Truck C", 'lang_str_webshop'),
			'351' => __("Övrigt", 'lang_str_webshop'),
		);
	}

	function is_correct_github_access_token($github_updater)
	{
		return (isset($github_updater['github_access_token']) && $github_updater['github_access_token'] != '' && substr($github_updater['github_access_token'], 0, strlen($this->github_access_token_start)) == $this->github_access_token_start);
	}

	function get_status($data = array())
	{
		if(!isset($data['type'])){		$data['type'] = '';}

		$out = "";
		$status_warnings = 0;

		$arr_plugins = array(
			//'github-updater/github-updater.php' => "GitHub Updater",
			'git-updater/git-updater.php' => "GitHub Updater",
			'mf_base/index.php' => "MF Base",
			'mf_str_webshop/index.php' => "MF STR Webshop",
		);

		foreach($arr_plugins as $key => $value)
		{
			switch($data['type'])
			{
				case 'html':
					$out .= "<li>
						<i class='fa ".(is_plugin_active($key) ? "fa fa-check green" : "fa fa-times red")."'></i> ";

						if(is_plugin_active($key) == false)
						{
							$out .= "<a href='".admin_url("plugins.php")."'>";
						}

							$out .= $value;

						if(is_plugin_active($key) == false)
						{
							$out .= "</a>";
						}

				break;
			}

				if(is_plugin_active($key))
				{
					switch($key)
					{
						//case 'github-updater/github-updater.php':
						case 'git-updater/git-updater.php':
							$github_updater = get_site_option('git_updater');

							switch($data['type'])
							{
								case 'html':
									$out .= "<ul>
										<li>&nbsp;&nbsp;<i class='fa ".($this->is_correct_github_access_token($github_updater) ? "fa fa-check green" : "fa fa-times red")."'></i> <a href='".$this->github_settings_url."'>".sprintf(__("%s Access Token", 'lang_str_webshop'), "GitHub.com")."</a></li>
									</ul>";
								break;

								case 'menu':
									if($this->is_correct_github_access_token($github_updater) == false)
									{
										$status_warnings++;
									}
								break;
							}
						break;

						case 'mf_base/index.php':
							if(!is_multisite() || is_main_site())
							{
								$setting_base_update_htaccess = get_option('setting_base_update_htaccess');
								$settings_url = admin_url("options-general.php?page=settings_mf_base");
							}

							else
							{
								$main_site_id = get_main_site_id();

								$setting_base_update_htaccess = get_blog_option($main_site_id, 'setting_base_update_htaccess');
								$settings_url = get_admin_url($main_site_id, "options-general.php?page=settings_mf_base");
							}

							switch($data['type'])
							{
								case 'html':
									$out .= "<ul>
										<li>&nbsp;&nbsp;<i class='fa ".($setting_base_update_htaccess == 'yes' ? "fa fa-check green" : "fa fa-times red")."'></i> <a href='".$settings_url."'>".sprintf(__("Automatically Update %s", 'lang_str_webshop'), ".htaccess")."</a></li>
									</ul>";
								break;

								case 'menu':
									if($setting_base_update_htaccess != 'yes')
									{
										$status_warnings++;
									}
								break;
							}
						break;

						case 'mf_str_webshop/index.php':
							$settings_url = admin_url("options-general.php?page=settings_mf_base#settings_str_webshop");
							$setting_str_webshop_post_id = get_option('setting_str_webshop_post_id');

							switch($data['type'])
							{
								case 'html':
									$out .= "<ul>
										<li>&nbsp;&nbsp;<i class='fa ".($setting_str_webshop_post_id > 0 ? "fa fa-check green" : "fa fa-times red")."'></i> <a href='".$settings_url."'>".__("Page", 'lang_str_webshop')."</a></li>";

										if($setting_str_webshop_post_id > 0)
										{
											$has_header_footer = $this->get_header_footer_from_page($setting_str_webshop_post_id);

											$out .= "<li title='".sprintf(__("The header is %d long and the footer is %d long", 'lang_str_webshop'), strlen($this->page_header), strlen($this->page_footer))."'>&nbsp;&nbsp;&nbsp;&nbsp;<i class='fa ".($has_header_footer ? "fa fa-check green" : "fa fa-times red")."'></i> <a href='".get_permalink($setting_str_webshop_post_id)."'>".__("Public and Contains the Correct Data", 'lang_str_webshop')."</a></li>";
										}

										$out .= "<li>&nbsp;&nbsp;<i class='fa ".(get_option('setting_str_webshop_customer_number') > 0 ? "fa fa-check green" : "fa fa-times red")."'></i> <a href='".$settings_url."'>".__("Customer Number", 'lang_str_webshop')."</a></li>
									</ul>";
								break;

								case 'menu':
									if($setting_str_webshop_post_id > 0)
									{
										if($this->get_header_footer_from_page($setting_str_webshop_post_id) == false)
										{
											$status_warnings++;
										}
									}

									else
									{
										$status_warnings++;
									}

									if(!(get_option('setting_str_webshop_customer_number') > 0))
									{
										$status_warnings++;
									}
								break;
							}
						break;
					}
				}

			switch($data['type'])
			{
				case 'html':
					$out .= "</li>";
				break;
			}
		}

		switch($data['type'])
		{
			case 'html':
				return "<ul>".$out."</ul>";
			break;

			case 'menu':
				if($status_warnings > 0)
				{
					return "&nbsp;<span class='update-plugins' title='".__("Warnings", 'lang_str_webshop')."'>
						<span>".$status_warnings."</span>
					</span>";
				}
			break;
		}
	}

	function get_github_version()
	{
		global $wpdb;

		$out = '';

		$meta_value = '';

		if(is_multisite())
		{
			$meta_key = $wpdb->get_var($wpdb->prepare("SELECT meta_key FROM ".$wpdb->base_prefix."sitemeta WHERE meta_key LIKE %s AND meta_value LIKE %s", "ghu-%", "%mf_str_webshop%"));

			if($meta_key != '')
			{
				$meta_value = get_site_option($meta_key);
			}
		}

		else
		{
			$meta_key = $wpdb->get_var($wpdb->prepare("SELECT option_name FROM ".$wpdb->prefix."options WHERE option_name LIKE %s AND option_value LIKE %s", "ghu-%", "%mf_str_webshop%"));

			if($meta_key != '')
			{
				$meta_value = get_option($meta_key);
			}
		}

		if(is_array($meta_value))
		{
			//do_log("GitHub version: ".var_export($meta_value, true));

			foreach($meta_value as $key1 => $value1)
			{
				switch($key1)
				{
					case 'mf_str_webshop':
						foreach($value1 as $key2 => $value2)
						{
							switch($key2)
							{
								case 'Version':
									$out .= $value2;
								break;
							}
						}
					break;
				}
			}
		}

		/*else
		{
			do_log("Could not find GitHub version: ".$wpdb->last_query);
		}*/

		if($out == '')
		{
			$out = "<em>(".__("unknown", 'lang_str_webshop').")</em>";
		}

		return $out;
	}

	function cron_base()
	{
		global $wpdb;

		$obj_cron = new mf_cron();
		$obj_cron->start(__CLASS__);

		if($obj_cron->is_running == false)
		{
			// Get settings from page with str-ecom code
			###################################
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
					wp_update_post(array(
						'ID' => $setting_str_webshop_post_id,
						'post_content' => $post_content."[mf_str_webshop]",
					));
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
			###################################

			// Sync API
			###################################
			if(get_option('setting_str_webshop_sitemap_api_activate', 'yes') == 'yes')
			{
				$setting_str_webshop_api_mode = get_option('setting_str_webshop_api_mode', 'live');
				$setting_str_webshop_customer_number = get_option('setting_str_webshop_customer_number');

				if($setting_str_webshop_customer_number > 0)
				{
					switch($setting_str_webshop_api_mode)
					{
						case 'test_if_logged_in':
						case 'test':
							$api_url = "https://ecommerceapitest.str.se";
						break;

						default:
						case 'live':
							$api_url = "https://ecommerceapi.str.se";
						break;
					}

					if($api_url != '')
					{
						$api_url .= "/api/sitemap";

						list($content, $headers) = get_url_content(array(
							'url' => $api_url,
							'catch_head' => true,
							'headers' => array(
								'Authorization: CustomerNumber '.base64_encode($setting_str_webshop_customer_number),
								'Accept: application/vnd.str.ecommerce.v2+json',
							),
						));

						$log_message = "I could not get a successful result from the Sitemap API";

						switch($headers['http_code'])
						{
							case 200:
								$json = json_decode($content, true);

								/*[
									{
										"name": "Mc",
										"url": "/mc"
									},
									{
										"name": "Teori",
										"url": "/mc/teori"
									},
									{
										"name": "Personbil med släp",
										"url": "/personbil med släp"
									},
									{
										"name": "Körkortsboken",
										"url": "/productdetails/2"
									}
								]*/

								//do_log("API Result: ".var_export($json, true));

								foreach($json as $item)
								{
									$post_title = $item['name'];
									$post_slug = trim($item['url'], "/");

									$arr_post_slug = explode("/", $post_slug);
									$depth_count = count($arr_post_slug);

									$post_parent = 0;

									foreach($arr_post_slug as $depth => $post_slug)
									{
										if($depth == 0 && $post_slug == 'productdetails')
										{
											$post_title_temp = __("Product Details", 'lang_str_webshop');
											$post_status = 'draft';
										}

										else
										{
											$post_title_temp = $post_title;
											$post_status = 'publish';
										}

										$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." WHERE post_type = %s AND post_name = %s", $this->post_type, $post_slug));

										if($wpdb->last_query != '')
										{
											if($wpdb->num_rows > 0)
											{
												$i = 0;

												foreach($result as $r)
												{
													if($i == 0)
													{
														if($depth == ($depth_count - 1))
														{
															$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->posts." SET post_title = %s, post_name = %s, post_parent = '%d', post_modified = NOW() WHERE ID = '%d'", $post_title_temp, $post_slug, $post_parent, $r->ID));
														}

														else
														{
															$post_parent = $r->ID;
														}

														$i++;
													}

													else
													{
														wp_trash_post($r->ID);
													}
												}
											}

											else
											{
												$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->posts." SET post_type = %s, post_status = %s, post_title = %s, post_name = %s, post_parent = '%d', post_date = NOW(), post_modified = NOW()", $this->post_type, $post_status, $post_title_temp, $post_slug, $post_parent));

												if($depth == ($depth_count - 1))
												{
													// Do nothing
												}

												else
												{
													$post_parent = $wpdb->insert_id;
												}
											}
										}
									}
								}

								do_log($log_message, 'trash');
							break;

							default:
								do_log($log_message." (".$content.", ".htmlspecialchars(var_export($headers, true)).")");
							break;
						}
					}
				}
			}
			###################################

			// Notify user when a new version is available
			###################################
			$setting_str_webshop_new_version_action = get_option('setting_str_webshop_new_version_action');
			$setting_str_webshop_new_version_action = array_map('trim', explode(",", $setting_str_webshop_new_version_action));

			if(count($setting_str_webshop_new_version_action) > 0)
			{
				$option_str_webshop_notified_version = get_option('option_str_webshop_notified_version');
				$plugin_version = get_plugin_version(__FILE__);
				$github_version = $this->get_github_version();

				if(version_compare($github_version, $plugin_version, ">") && version_compare($github_version, $option_str_webshop_notified_version, ">"))
				{
					foreach($setting_str_webshop_new_version_action as $user_email)
					{
						if($user_email != '')
						{
							$blog_name = get_bloginfo('name');

							$mail_to = $user_email;
							$mail_subject = sprintf(__("A new version of %s is available on %s", 'lang_str_webshop'), "MF STR Webshop", $blog_name);
							$mail_content = sprintf(__("The version %s is now available for update on %s and the version history can be read on %sGitHub%s", 'lang_str_webshop'), $github_version, "<a href='".(is_multisite() ? network_admin_url("plugins.php") : admin_url("plugins.php"))."'>".$blog_name."</a>", "<a href='https://github.com/frostkom/mf_str_webshop/commits/master'>", "</a>");

							$sent = send_email(array('to' => $mail_to, 'subject' => $mail_subject, 'content' => $mail_content));

							if($sent)
							{
								// Do nothing
							}

							else
							{
								do_log(sprintf("I could not send the message regarding a new version of %s to %s", "MF STR Webshop", var_export($setting_str_webshop_new_version_action, true)." -> ".$mail_to));
							}
						}
					}

					update_option('option_str_webshop_notified_version', $github_version, 'no');
				}
			}
			###################################
		}

		$obj_cron->end();
	}

	function init()
	{
		if(get_option('setting_str_webshop_sitemap_api_activate', 'yes') == 'yes')
		{
			if(get_option('setting_str_webshop_version', 'old') == 'old')
			{
				$labels = array(
					'name' => _x(__("Sitemap Pages", 'lang_str_webshop'), 'post type general name'),
					'singular_name' => _x(__("Sitemap Page", 'lang_str_webshop'), 'post type singular name'),
					'menu_name' => __("Sitemap Pages", 'lang_str_webshop')
				);

				$args = array(
					'labels' => $labels,
					'public' => does_post_exists(array('post_type' => $this->post_type)),
					//'show_ui' => false,
					//'show_in_menu' => false,
					//'show_in_nav_menus' => false,
					//'exclude_from_search' => true,
					'supports' => array('title'),
					'hierarchical' => true,
					'has_archive' => false,
				);

				$setting_str_webshop_post_id = get_option('setting_str_webshop_post_id');

				if($setting_str_webshop_post_id > 0)
				{
					$args['rewrite'] = array(
						'slug' => mf_get_post_content($setting_str_webshop_post_id, 'post_name'),
					);
				}

				register_post_type($this->post_type, $args);
			}
		}

		else
		{
			mf_uninstall_plugin(array(
				'post_types' => array($this->post_type),
			));
		}
	}

	function settings_str_webshop()
	{
		$options_area_orig = $options_area = __FUNCTION__;

		// Generic
		############################
		add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

		$arr_settings = array();

		$arr_settings['setting_str_webshop_version'] = __("Version", 'lang_str_webshop');

		$setting_str_webshop_version = get_option('setting_str_webshop_version', 'old');

		switch($setting_str_webshop_version)
		{
			default:
			case 'old':
				$arr_settings['setting_str_webshop_post_id'] = __("Page", 'lang_str_webshop');
				$arr_settings['setting_str_webshop_api_mode'] = __("API Mode", 'lang_str_webshop');
				$arr_settings['setting_str_webshop_customer_number'] = __("Customer Number", 'lang_str_webshop');
				$arr_settings['setting_str_webshop_google_analytics'] = "Google Analytics";
			break;

			case 'iframe':
				$arr_settings['setting_str_webshop_iframe_url'] = __("URL", 'lang_str_webshop');
			break;

			case 'api':
				$arr_settings['setting_str_webshop_office_id'] = __("Office ID", 'lang_str_webshop');
			break;
		}

		show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
		############################

		if($setting_str_webshop_version == 'old')
		{
			// Style
			############################
			$options_area = $options_area_orig."_style";

			add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

			$arr_settings = array(
				'setting_str_webshop_include_css' => __("Include CSS", 'lang_str_webshop'),
				'setting_str_webshop_include_extra_css' => __("Include Extra CSS", 'lang_str_webshop'),
				'setting_str_webshop_header_selector' => __("Header Selector", 'lang_str_webshop'),
				//'setting_str_webshop_replace_campaigns' => __("Replace Campaigns with Catergory Text", 'lang_str_webshop'),
			);

			show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
			############################

			// Advanced
			############################
			$options_area = $options_area_orig."_advanced";

			add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

			$arr_settings = array(
				'setting_str_webshop_disable_canonical' => __("Disable Canonical", 'lang_str_webshop'),
				'setting_str_webshop_sitemap_api_activate' => __("Activate Sitemap API", 'lang_str_webshop'),
				'setting_str_webshop_new_version_action' => __("New Version Notification", 'lang_str_webshop'),
			);

			show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
			############################
		}
	}

	function settings_str_webshop_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Webshop", 'lang_str_webshop'));
	}

		function setting_str_webshop_version_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key, 'live');

			echo show_select(array('data' => $this->get_type_for_select(), 'name' => $setting_key, 'value' => $option));
		}

	/* Old */
	#######################
		function setting_str_webshop_post_id_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key);

			$arr_data = array();
			get_post_children(array('add_choose_here' => true), $arr_data);

			$suffix = get_option_page_suffix(array('value' => $option));

			if($option > 0)
			{
				//$suffix .= "&nbsp;<a href='".get_permalink($option)."'><i class='fa fa-eye fa-lg' title='".__("Preview on Public Page", 'lang_str_webshop')."'></i></a>";
				$suffix .= "&nbsp;<a href='".get_site_url()."/wp-content/plugins/mf_str_webshop/view/'><i class='fas fa-hard-hat fa-lg' title='".__("Preview on Test Page", 'lang_str_webshop')."'></i></a>";
			}

			echo show_select(array('data' => $arr_data, 'name' => $setting_key, 'value' => $option, 'suffix' => $suffix));
		}

		function setting_str_webshop_api_mode_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key, 'old');

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

	function settings_str_webshop_style_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Webshop", 'lang_str_webshop')." - ".__("Style", 'lang_str_webshop'));
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
			$option = get_option($setting_key, 'yes');

			echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option, 'description' => __("This adds compatibility styling for the cart when it is open", 'lang_str_webshop')));
		}

		function setting_str_webshop_header_selector_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key);

			if($option != '' && strpos($option, "{"))
			{
				list($option, $rest) = explode("{", $option);
				$option = trim($option);
			}

			echo show_textfield(array('name' => $setting_key, 'value' => $option, 'placeholder' => "#header, .header"));
		}

		/*function setting_str_webshop_replace_campaigns_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key, 'yes');

			echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
		}*/

	function settings_str_webshop_advanced_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Webshop", 'lang_str_webshop')." - ".__("Advanced", 'lang_str_webshop'));
	}

		function setting_str_webshop_disable_canonical_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key, 'no');

			echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
		}

		function setting_str_webshop_sitemap_api_activate_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key, 'yes');

			echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
		}

		function setting_str_webshop_new_version_action_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key);

			$description = "";

			if($option == '')
			{
				$description = __("Please choose who to notify when a new version is available", 'lang_str_webshop'); //"<i class='fa fa-exclamation-triangle yellow display_warning'></i> ".
			}

			echo show_textfield(array('name' => $setting_key, 'value' => $option, 'placeholder' => get_placeholder_email(), 'description' => $description));
		}
	#######################

	/* iFrame */
	#######################
		function setting_str_webshop_iframe_url_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key);

			echo show_textfield(array('type' => 'url', 'name' => $setting_key, 'value' => $option));
		}
	#######################

	/* API */
	#######################
		function setting_str_webshop_office_id_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key);

			echo show_textfield(array('name' => $setting_key, 'value' => $option));
		}
	#######################

	function admin_menu()
	{
		$menu_root = 'mf_str_webshop/';
		$menu_start = $menu_root."manual/index.php";
		$menu_capability = override_capability(array('page' => $menu_start, 'default' => 'edit_posts'));

		$count_message = $this->get_status(array('type' => 'menu'));

		$menu_title = __("Webshop", 'lang_str_webshop');
		add_menu_page($menu_title, $menu_title.$count_message, $menu_capability, $menu_start, '', 'dashicons-cart', 99);

		$menu_title = __("Manual", 'lang_str_webshop');
		add_submenu_page($menu_start, $menu_title, $menu_title.$count_message, $menu_capability, $menu_start);

		$menu_title = __("Settings", 'lang_str_webshop');
		add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, admin_url("options-general.php?page=settings_mf_base#settings_str_webshop"));
	}

	function wp_head()
	{
		global $post;

		$plugin_include_url = plugin_dir_url(__FILE__);

		switch(get_option('setting_str_webshop_version', 'old'))
		{
			default:
			case 'old':
				if(isset($post->ID) && $post->ID == get_option('setting_str_webshop_post_id'))
				{
					if(get_option('setting_str_webshop_disable_canonical') == 'yes')
					{
						// WP native
						remove_action('wp_head', 'rel_canonical');

						// Yoast
						add_filter('wpseo_canonical', '__return_false', 10, 1);
					}

					// Test this before use
					/*global $obj_base;

					if(!isset($obj_base))
					{
						$obj_base = new mf_base();
					}

					$obj_base->load_font_awesome(array('type' => 'external', 'plugin_include_url' => plugins_url()."/mf_base/include/"));*/

					/*if(get_option('setting_str_webshop_replace_campaigns', 'yes') == 'yes')
					{
						mf_enqueue_style('style_str_webshop_category_text', $plugin_include_url."style_category_text.php");
						mf_enqueue_script('script_str_webshop_category_text', $plugin_include_url."script_category_text.js");
					}*/

					if(get_option('setting_str_webshop_include_extra_css') != 'no')
					{
						mf_enqueue_style('style_str_webshop_extra', $plugin_include_url."style_extra.css");
					}

					/*if($has_custom_style)
					{
						mf_enqueue_style('style_str_webshop_custom', $plugin_include_url."style.php");
					}*/
				}
			break;

			case 'iframe':
				mf_enqueue_style('style_str_webshop_iframe', $plugin_include_url."style_iframe.css");
			break;

			case 'api':
				// None yet
			break;
		}
	}

	function shortcode_str_webshop()
	{
		$setting_str_webshop_version = get_option('setting_str_webshop_version', 'old');

		switch($setting_str_webshop_version)
		{
			default:
			case 'old':
				$out = $this->get_view_code();
			break;

			case 'iframe':
				$setting_str_webshop_iframe_url = get_option('setting_str_webshop_iframe_url');

				if($setting_str_webshop_iframe_url != '')
				{
					$out = "";
					$out .= "<script src='https://api.web.stroptima.se/js/api.js?v2'></script>";
					$out .= "<iframe class='str_webshop_iframe' src='".$setting_str_webshop_iframe_url."'></iframe>";
				}

				else
				{
					$out = "<em>".__("There is no content to display here yet", 'lang_str_webshop')."</em>";
				}
			break;

			case 'api':
				$setting_str_webshop_office_id = get_option('setting_str_webshop_office_id');

				if($setting_str_webshop_office_id != '')
				{
					$out = "";
					//$out .= "<em>".__("There is an OfficeID connected to display here", 'lang_str_webshop')."</em>";

					$plugin_version = get_plugin_version(__FILE__);

					$out .= "<div id='api-course' data-version='v2'"
						//." data-class-id='482'"
						//." data-accordion='true'"
						//." data-expanded-id='482'"
						//." data-limit='3'"
					."></div>
					<script>
						var officeId = ".$setting_str_webshop_office_id.",
							apiHost = 'https://api.stroptima.se',
							locale = 'sv';
					</script>
					<script	src='https://api.stroptima.se/js/api.js?v".$plugin_version."'></script>";

					/*$heading_bg = "#2A787A";
					$heading_color = "#fff";
					$panel_color = "#226062";

					$css = ".TABS .panel-default > .panel-heading
					{
						background-color: ".$heading_bg." !important;
						border-color: ".$heading_bg.";
						color: ".$heading_color." !important;
					}

					.TABS .modal-title
					{
						background-color: ".$heading_bg." !important;
						color: ".$heading_color." !important;
					}

					.TABS .modal-header
					{
						background-color: ".$heading_bg." !important;
					}

					.btn
					{
						background-color: ".$heading_bg." !important;
						border-color: ".$heading_bg.";
						color: ".$heading_color." !important;
					}

					.panel-group p
					{
						color: ".$panel_color." !important;
						border-radius: 5px;
					}

					.str_webshop_iframe
					{
						height: 4000px;
					}

					.TABS div.col-sm-4.col-xs-12
					{
						width: 60%;
					}

					.TABS .col-sm-4.col-xs-6
					{
						text-align: right;
						color: ".$panel_color.";
					}";*/
				}

				else
				{
					$out = "<em>".__("There is no OfficeID connected to display here yet", 'lang_str_webshop')."</em>";
				}
			break;
		}

		return $out;
	}

	function recommend_config($data)
	{
		global $obj_base;

		if(!isset($obj_base))
		{
			$obj_base = new mf_base();
		}

		if(!isset($data['file'])){		$data['file'] = '';}

		switch(get_option('setting_str_webshop_version', 'old'))
		{
			default:
			case 'old':
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
							$update_with = "";

							$update_with .= "location ~ /".$base_path."/$ {}\r\n"
							."\r\n";

							$update_with .= "location / {\r\n"
							."	rewrite ^/".$base_path."/(.*)$ ".$subfolder."wp-content/plugins/mf_str_webshop/view/index.php last;\r\n"
							//."	rewrite ^".$subfolder."wp-content/plugins/mf_str_webshop/view/(.*)$ ".$subfolder."wp-content/plugins/mf_str_webshop/view/index.php last;\r\n"
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
			break;

			case 'iframe':
				$subfolder = get_url_part(array('type' => 'path'));
				$base_path = trim($this->get_base_path(), "/");

				switch($obj_base->get_server_type())
				{
					default:
					case 'apache':
						$update_with = "";
					break;

					case 'nginx':
						$update_with = "";
					break;
				}

				$data['html'] .= $obj_base->update_config(array(
					'plugin_name' => "MF STR Webshop",
					'file' => $data['file'],
					'update_with' => $update_with,
					'auto_update' => true,
				));
			break;
		}

		return $data;
	}
}