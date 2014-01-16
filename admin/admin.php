<?php

// settings page
if(class_exists('vb_social_settings')){
	global $vb_settings_object; ?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
			<h2>vBSocial Pages Settings</h2>
            <h3><a href="http://vbsocial.com/buy-wordpress-plugins">Go Pro</a>! Unlock the full potential of <a href="http://vbsocial.com/buy-wordpress-plugins">vBSocial Pages</a> including Images tab with content, Videos tab with content, notifications and integrations with our other social plugins with the <a href="http://vbsocial.com/vbsocial-pages">Pro Version</a>. </h3>
            <h4>To Install:
      Add each shortcode onto a seperate Wordpress Page</h4>
            <h4> 1. [vb_create_page] - Allows your users to create pages</h4>
            <h4>2. [vb_master_page] - Allows you to view all the create pages</h4>
			<?php 
				$vb_settings_object->settings();
				
			?>
	</div>
<h3>Unlock the full potential of <a href="http://vbsocial.com/buy-wordpress-plugins">vBSocial Pages</a> including Images tab with content, Videos tab with content, notifications and integrations with our other social plugins with the <a href="http://vbsocial.com/vbsocial-pages">Pro Version</a>. </h3>

<?php	
}

?>

