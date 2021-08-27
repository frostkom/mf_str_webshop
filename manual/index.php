<?php

$obj_str_webshop = new mf_str_webshop();

$site_url = get_site_url();
$strSiteUrl = check_var('strSiteUrl', 'char', true, $site_url);

$obj_str_webshop->admin_url = str_replace($site_url, $strSiteUrl, $site_url."/wp-admin/");
$obj_str_webshop->plugins_url = str_replace($site_url, $strSiteUrl, admin_url("plugins.php"));
$obj_str_webshop->pages_url = str_replace($site_url, $strSiteUrl, admin_url("edit.php?post_type=page"));
$obj_str_webshop->settings_base_url = str_replace($site_url, $strSiteUrl, admin_url("options-general.php?page=settings_mf_base"));
$obj_str_webshop->settings_webshop_url = str_replace($site_url, $strSiteUrl, admin_url("options-general.php?page=settings_mf_base#settings_str_webshop"));
$obj_str_webshop->settings_github_url = str_replace($site_url, $strSiteUrl, $obj_str_webshop->github_settings_url);

echo "<div class='wrap'>
	<h2>".__("Manual", 'lang_str_webshop')."</h2>
	<div id='poststuff'>
		<form action='#' method='post' class='mf_form mf_settings'>
			<div id='post-body' class='columns-2'>
				<div id='post-body-content'>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Install webshop on a site", 'lang_str_webshop')."</span></h3>
						<div class='inside'>
							<ol>
								<li>".sprintf(__("Download ZIP files from %s", 'lang_str_webshop'), "<a href='//drive.google.com/drive/folders/1Z7dtsUwxUMJW_Cf5AN6FR2G78Qqqi-e1'>Drive</a>")."</li>
								<li>".sprintf(__("Upload ZIP files to %s", 'lang_str_webshop'), "<code>wp-content/plugins</code>")."</li>
								<li>".sprintf(__("Login to %s", 'lang_str_webshop'), "<a href='".$obj_str_webshop->admin_url."'>Wordpress</a>")."</li>
								<li>".sprintf(__("Go to %sPlugins%s and activate %s first, then %s and %s", 'lang_str_webshop'), "<a href='".$obj_str_webshop->plugins_url."'>", "</a>", "MF Base", "MF STR Webshop", "GitHub Updater")."</li>
							</ol>
						</div>
					</div>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Activate webshop on a page", 'lang_str_webshop')."</span></h3>
						<div class='inside'>
							<ol>
								<li>".sprintf(__("Open %sPages%s and edit the page you plan to have the webshop on", 'lang_str_webshop'), "<a href='".$obj_str_webshop->pages_url."'>", "</a>")."</li>
								<li>".sprintf(__("Add the shortcode %s where you want the webshop to appear on the page", 'lang_str_webshop'), "<code>[mf_str_webshop]</code>")."</li>
								<li>".sprintf(__("Go to Settings -> My Settings -> %sCommon%s", 'lang_str_webshop'), "<a href='".$obj_str_webshop->settings_base_url."'>", "</a>")."</li>
								<li>".sprintf(__("Choose Yes on Automatically Update %s and save", 'lang_str_webshop'), ".htaccess")."</li>
								<li>".sprintf(__("Go to Settings -> My Settings -> %sWebshop%s", 'lang_str_webshop'), "<a href='".$obj_str_webshop->settings_webshop_url."'>", "</a>")."</li>
								<li>".__("Choose the page that you previously added the shortcode to, choose Test/Live, add the Customer Number and save", 'lang_str_webshop')."</li>
								<li>".sprintf(__("If the Cachier button is not visible in the Cart, go to Settings -> My Settings -> %sWebshop%s and set Include Extra CSS to Yes", 'lang_str_webshop'), "<a href='".$obj_str_webshop->settings_webshop_url."'>", "</a>")."</li>
								<li>".__("Clear the server cache if there is a Cache plugin installed", 'lang_str_webshop')."</li>
							</ol>
						</div>
					</div>
					<div class='postbox'>
						<h3 class='hndle'><span>".sprintf(__("Activate automatic updates of plugins on %s", 'lang_str_webshop'), "GitHub")."</span></h3>
						<div class='inside'>
							<ol>
								<li>".sprintf(__("Go to Settings -> %s -> %s", 'lang_str_webshop'), "GitHub Updater", "<a href='".$obj_str_webshop->settings_github_url."'>GitHub</a>")."</li>
								<li>";

									$github_updater = get_site_option('github_updater');

									if($obj_str_webshop->is_correct_github_access_token($github_updater))
									{
										echo sprintf(__("Add %s in the field %s and save", 'lang_str_webshop'), "<code>".$github_updater['github_access_token']."</code>", sprintf(__("%s Access Token", 'lang_str_webshop'), "GitHub.com"));
									}

									else
									{
										echo sprintf(__("Contact an admin and ask for the %s. Then add it in the field %s and save", 'lang_str_webshop'), sprintf(__("%s Access Token", 'lang_str_webshop'), "GitHub.com"), sprintf(__("%s Access Token", 'lang_str_webshop'), "GitHub.com"));
									}

								echo "</li>
								<li>".sprintf(__("If the tab %s and field %s is not immediately visible, be patient and go back in a few minutes to check", 'lang_str_webshop'), "GitHub", sprintf(__("%s Access Token", 'lang_str_webshop'), "GitHub.com"))."</li>
							</ol>
						</div>
					</div>
				</div>
				<div id='postbox-container-1'>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Send Instruction", 'lang_str_webshop')."</span></h3>
						<div class='inside'>"
							.show_textfield(array('type' => 'url', 'name' => 'strSiteUrl', 'value' => $strSiteUrl, 'description' => __("If you want to send any of the instructions below to someone else, add their site URL here to change all links below", 'lang_str_webshop')))
							.show_button(array('name' => 'btnWebshopReload', 'text' => __("Reload", 'lang_str_webshop')))
						."</div>
					</div>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Status", 'lang_str_webshop')."</span></h3>
						<div class='inside'>".$obj_str_webshop->get_status(array('type' => 'html'))."</div>
					</div>
					<div class='postbox'>
						<h3 class='hndle'><span>".__("Version", 'lang_str_webshop')."</span></h3>
						<div class='inside'>";

							$plugin_version = get_plugin_version(__FILE__);
							$github_version = $obj_str_webshop->get_github_version();

							echo "<p>".sprintf(__("The installed version is %s and the latest version on GitHub is %s.", 'lang_str_webshop'), $plugin_version, $github_version)."</p>
							<p>".sprintf(__("Take a look at the version history %shere%s.", 'lang_str_webshop'), "<a href='//github.com/frostkom/mf_str_webshop/commits/master' rel='external'>", "</a>")."</p>";

						echo "</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>";