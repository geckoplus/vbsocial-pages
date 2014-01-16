<?php
	get_header();
	// get admin settings
	$vb_admin_settings  = get_option('settingstemplate_settings');
	function is_vb_fan(){
	
		if(!is_user_logged_in()){
			return false;
		}
		
		global $wpdb;
		$result = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'vb_follow WHERE `user_id` = '.get_current_user_id().' AND  `post_id` = '.get_the_ID().'');
		
		if(!empty($result))
			return true;
		else
			return false;
	}
	
	function page_followers_count(){
		global $wpdb; 
		$result = $wpdb->get_row('SELECT count(*) as followers FROM '.$wpdb->prefix.'vb_follow WHERE  `post_id` = '.get_the_ID().'');
		return $result->followers;
	}
	
?>

<div class="vb-wrap" id="<?php echo get_the_ID(); ?>">
	<?php if(get_the_ID() != 0):?>
	<div class="vb-pro-head">
	
		<div class="pro-img">
			<div class="page-photo">
				<?php
					if(has_post_thumbnail(get_the_ID())) {
						echo get_the_post_thumbnail(get_the_ID(), array(260,270));
					} else {
						echo '<img src="'. plugins_url( 'images/default_profile_pic.jpg' , __DIR__ ) .'">';
					}
				?>
				
			</div>
			<div class="page-name">
				<?php
					echo '<a href="'.get_permalink(get_the_ID()).'">'.get_the_title(get_the_ID()).'</a>';
				?>
			</div>
			<div class="page-follow">
				<?php if(is_vb_fan()): ?>
				
				<span class="fan">
					<a href="#">
						<?php
							echo 'following';
							page_followers_count();
							
						?>
					</a>
				</span>
				
				<?php else: ?>
				<span class="follow">
					<a href="#">
						<?php
							echo 'follow';
						?>
					</a>
				</span>
				<?php endif;?>
				<p>
					<?php 
						if(page_followers_count() >= 1)
						echo  page_followers_count().' Followers';
					?>
				</p>
			</div>
			<div class="clear"></div>
			<div title="share this page" class="vb-share-icon"></div> 
			<div class="vb-page-sharing" style="display:none">
				<div class="vb-facebook-sharing">
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(get_the_ID()); ?>" >
					 
					</a>
				</div>
				<div class="vb-twitter-sharing">
					<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
				</div>
				<div class="vb-gplus-sharing">
					<div class="g-plus" data-action="share" ... ></div>
				</div>
			</div>
		</div>
		<div class="cover-img">
			<?php
				$background_image_id = get_post_meta(get_the_ID(),'background_id',true);
				if(!empty($background_image_id) &&  $background_image_id != '') {
				
					$background_image_url = wp_get_attachment_url( intval($background_image_id) );
					echo '<img src="'.$background_image_url.'">';
				} else {
					echo '<img src="'. plugins_url( 'images/default-cover.jpg' , __DIR__ ) .'">';
				}
				
			?>
			
		</div>
		
		
	</div>
	<div id="vb-pro-posts">
		<h1>About</h1>
			<?php include('template-about.php'); ?>

		<h1>Posts</h1>
			<?php include('template-posts.php'); ?>
		

	</div>
	<?php endif;?>
</div>
       
<?php
	do_action('vbpage_get_edit_box');
	get_footer();
?>
