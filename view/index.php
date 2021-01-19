<?php

if(!defined('ABSPATH'))
{
	$folder = str_replace("/wp-content/plugins/mf_str_webshop/view", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

do_action('run_cache', array('suffix' => 'html'));

include_once("../include/classes.php");

$obj_str_webshop = new mf_str_webshop();

$header = "<html><body>";
$footer = "</body></html>";

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

		list($header, $rest) = explode("<body>", $part_one);
		list($rest, $footer) = explode("</body>", $part_two, 2);
	break;
}*/
##########################

$setting_str_webshop_post_id = get_option('setting_str_webshop_post_id');

if($setting_str_webshop_post_id > 0)
{
	// 
	##########################
	list($content, $headers) = get_url_content(array(
		'url' => get_permalink($setting_str_webshop_post_id),
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

			else
			{
				list($part_one, $part_two) = explode("id='str-ecom'", $content);
			}

			$arr_header = explode("<noscript>", $part_one);
			list($rest, $footer) = explode("</script>", $part_two, 2);

			$header = "";

			for($i = 0; $i < (count($arr_header) - 1); $i++)
			{
				$header .= $arr_header[$i];
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

	$header = ob_get_clean();

	ob_start();

				echo "</div>";

			FLTheme::sidebar('right');

		echo "</div>
	</div>";

	get_footer();

	$footer = ob_get_clean();*/
	##########################
}

echo $header;

	$setting_str_webshop_customer_number = get_option('setting_str_webshop_customer_number');

	if($setting_str_webshop_customer_number > 0)
	{
		echo $obj_str_webshop->get_view_code(array('post_id' => $setting_str_webshop_post_id, 'customer_number' => $setting_str_webshop_customer_number));
	}

	else
	{
		$error_text = __("I could not find a Customer Number. Please add one and try again.", 'lang_str_webshop');

		echo get_notification();
	}

echo $footer;