<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_str_webshop/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

echo "#str-ecom .campaigns
{
	padding: 2rem;
}

	#str-ecom .campaigns:after
	{
		content: '".__("Loading", 'lang_str_webshop')."...';
	}

		#str-ecom .campaigns.loaded:after
		{
			content: '';
		}

		#str-ecom .campaigns > .carousel
		{
			display: none;
		}

		#str-ecom .campaigns .webshop_category_text p:last-of-type
		{
			margin-bottom: 0;
		}";