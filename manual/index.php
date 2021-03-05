<?php

$obj_str_webshop = new mf_str_webshop();

echo "<div class='wrap'>
	<h2>".__("Manual", $obj_str_webshop->lang_key)."</h2>
	<div id='poststuff'>
		<form action='#' method='post' class='mf_form mf_settings'>
			<div id='post-body' class='columns-2'>
				<div id='post-body-content'>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Install webshop on a site", $obj_str_webshop->lang_key)."</span></h3>
						<div class='inside'>
							<ol>
								<li>".sprintf(__("Download ZIP files from %s", $obj_str_webshop->lang_key), "<a href='//drive.google.com/drive/folders/1Z7dtsUwxUMJW_Cf5AN6FR2G78Qqqi-e1'>Drive</a>")."</li>
								<li>".sprintf(__("Upload ZIP files to %s", $obj_str_webshop->lang_key), "<code>wp-content/plugins</code>")."</li>
								<li>".sprintf(__("Login to %s", $obj_str_webshop->lang_key), "<a href='".wp_login_url()."'>Wordpress</a>")."</li>
								<li>".sprintf(__("Go to %sPlugins%s and activate %s first, then %s and %s", $obj_str_webshop->lang_key), "<a href='".admin_url("plugins.php")."'>", "</a>", "MF Base", "MF STR Webshop", "GitHub Updater")."</li>
							</ol>
						</div>
					</div>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Activate webshop on a page", $obj_str_webshop->lang_key)."</span></h3>
						<div class='inside'>
							<ol>
								<li>".sprintf(__("Open %sPages%s and edit the page you plan to have the webshop on", $obj_str_webshop->lang_key), "<a href='".admin_url("edit.php?post_type=page")."'>", "</a>")."</li>
								<li>".sprintf(__("Add the shortcode %s where you want the webshop to appear on the page", $obj_str_webshop->lang_key), "<code>[mf_str_webshop]</code>")."</li>
								<li>".sprintf(__("Go to Settings -> My Settings -> %sCommon%s", $obj_str_webshop->lang_key), "<a href='".admin_url("options-general.php?page=settings_mf_base")."'>", "</a>")."</li>
								<li>".sprintf(__("Choose Yes on Automatically Update %s and save", $obj_str_webshop->lang_key), ".htaccess")."</li>
								<li>".sprintf(__("Go to Settings -> My Settings -> %sWebshop%s", $obj_str_webshop->lang_key), "<a href='".admin_url("options-general.php?page=settings_mf_base#settings_str_webshop")."'>", "</a>")."</li>
								<li>".__("Choose the page that you previously added the shortcode to, choose Test/Live, add the Customer Number and save", $obj_str_webshop->lang_key)."</li>
								<li>".__("Clear the server cache if there is a Cache plugin installed", $obj_str_webshop->lang_key)."</li>
							</ol>
						</div>
					</div>
					<div class='postbox'>
						<h3 class='hndle'><span>".sprintf(__("Activate automatic updates of plugins on %s", $obj_str_webshop->lang_key), "GitHub")."</span></h3>
						<div class='inside'>
							<ol>
								<li>".sprintf(__("Go to Settings -> %s -> %s", $obj_str_webshop->lang_key), "GitHub Updater", "<a href='".$obj_str_webshop->github_settings_url."'>GitHub</a>")."</li>
								<li>";

									$github_updater = get_site_option('github_updater');
								
									if(isset($github_updater['github_access_token']) && $github_updater['github_access_token'] != '' && substr($github_updater['github_access_token'], 0, 3) == "409")
									{
										echo sprintf(__("Add %s in the field %s and save", $obj_str_webshop->lang_key), "<code>".$github_updater['github_access_token']."</code>", "GitHub.com Access Token");
									}
									
									else
									{
										echo sprintf(__("Contact an admin and ask for the %s. Then add it in the field %s and save", $obj_str_webshop->lang_key), "GitHub.com Access Token", "GitHub.com Access Token");
									}
									
								echo "</li>
							</ol>
						</div>
					</div>
				</div>
				<div id='postbox-container-1'>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Status", $obj_str_webshop->lang_key)."</span></h3>
						<div class='inside'>".$obj_str_webshop->get_status(array('type' => 'html'))."</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>";