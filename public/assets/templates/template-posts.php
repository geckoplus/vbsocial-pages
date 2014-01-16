<div class="vb-posts-wrap">

<?php
	
	
	//check if current user is admin of this page 
	if(get_current_user_id() == get_post_meta(get_the_ID(),'page_admin',true)):
	
	$text_term 		= get_term_by( 'name', 'Text', 'vb_blog_type');
	$video_term 	= get_term_by( 'name', 'Video', 'vb_blog_type');
	$image_term 	= get_term_by( 'name', 'Image', 'vb_blog_type');
	$link_term 		= get_term_by( 'name', 'Link', 'vb_blog_type'); 
?>
	<form id="vb-publish-form">
		<div class="vb-publish-box">
			
			<div class="vb-publish-txtbox">
				<input type="text" placeholder = "whats it about ..." name="vb_post_title" class="vb-post-title">
				<textarea placeholder="Share Whats New .. " name="vb_publish_input" class="vb-publish-input" ></textarea>
			</div>
			
			<div class="linkPreview">
				<div id="previewLoading">
					<?php
						echo '<img src="' . plugins_url( '../images/loading.gif' , __FILE__ ) . '" > ';
					?>
				</div>
				<div style="width:100%; float: left;">
					<textarea type="text" id="text" style="text-align: left" placeholder="Paste Link Here ... "/>
					</textarea>
					<div style="clear: both"></div>
				</div>
				<div id="preview">
					<div id="previewImages">
						<div id="previewImage">
							<?php 
								echo '<img src="' . plugins_url( '../images/loading.gif' , __FILE__ ) . '" > ';
							?>
						</div>
						<input type="hidden" id="photoNumber" value="0" />
					</div>
					<div id="previewContent">
						<div id="closePreview" title="Remove" ></div>
						<div id="previewTitle"></div>
						<div id="previewUrl"></div>
						<div id="previewDescription"></div>
						<div id="hiddenDescription"></div>
						<div id="previewButtons" >
							<div id="previewPreviousImg" class="buttonLeftDeactive" ></div><div id="previewNextImg" class="buttonRightDeactive"  ></div>
						</div>
						
					</div>
					<div style="clear: both"></div>
				</div>
				
			</div>
			
		
			<div class="vb-publish-option">
				<div class="vb-publish-text focus" data-blog_type="<?php echo $text_term->term_id; ?>">
					<span>
						Text
					</span>
				</div>
				<div class="vb-publish-photo" data-blog_type="<?php echo $image_term->term_id; ?>">
					<span>
						Photo
					</span>
					<div class="vb-publish-tools">
					
						<a href="#" class="button_share vb-post-pic" >Add Photo</a> 
					</div>
				</div>
				<div class="vb-publish-link" data-blog_type="<?php echo $link_term->term_id; ?>">
					<span>
						Link
					</span>
					<div class="vb-publish-tools">
					
						<a href="#" class="button_share vb-post-link" >Add Link</a> 
					</div>
				</div>
				<div class="vb-publish-video" data-blog_type="<?php echo $video_term->term_id; ?>">
					<span>
						Video
					</span>
					<div class="vb-publish-tools">
					
						<!--<a href="#" class="button_share vb-post-video" >Upload Video</a> -->
						<a href="#" class="button_share vb-post-yt-video" >Add Video</a>
						
					</div>
				</div>
			</div>
			
			<div class="vb-publish-controls">
				<input type="hidden" name="action" value="vb_publish_post">
				<a href="#"  id="vb-publish-post" class="button_share" >Share</a>
				<a href="#"  class="button_cancel" >cancel</a>
			</div>
			<div class="vb-publish-attachments">
			</div>
			
		
		</div>
	</form>
	
	<?php
	endif;
		echo '<div class="current_page_posts" id="current_page_posts">';
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$vb_page_posts = query_posts(
			array(
				'post_type'			=> 		'vbblog',
				'post_parent'		=>		get_the_ID(),
				'posts_per_page'	=>		$vb_admin_settings['settingstemplate_general_vb_page_per_box'],
				'paged' 			=> 		$paged

			)
		);
		$i = 1;
		foreach($vb_page_posts as $vb_page_post){
			
			$videothumbclass= '';
			$align_class = 'right';
			// is video ?
			if(get_post_meta($vb_page_post->ID,'vb_video_id',true)!= ''){
				
				// is youtube video
				if(get_post_meta($vb_page_post->ID,'is_youtube_video',true) == "1")
					$videothumbclass= 'youtubeimg';
				
				// is vimeo video
				if(get_post_meta($vb_page_post->ID,'is_vimeo_video',true) == "1")
					$videothumbclass= 'vimeoimg';
				
				$vb_video_src_id = get_post_meta($vb_page_post->ID,'vb_video_id',true);
			}
			if($i%2 == 0)
				$align_class = 'left';
		?>
		
			<div class="vb-blog-post <?php echo $align_class; ?>" id="<?php echo $vb_page_post->ID; ?>">
			
				<div class="vb-blog-post-content">

					<div class="vb-blog-post-title">
						<div class="vb-blog-avatar">
							<?php echo get_avatar( get_post_meta(get_the_ID(),'page_admin',true), '32' ); ?>
						</div>
						<?php echo $vb_page_post->post_title; ?>
					</div>

					<div class="vb-blog-post-description">
						<?php echo $vb_page_post->post_content; ?>
					</div>
					<?php
						// if post type is link ?
						if(get_post_meta($vb_page_post->ID,'vb_link_title',true) != ''){
						
							echo '
								<div class="vb-post-thumbhail" id="vb-post-thumbhail">
									<img data-ytid="'.$vb_video_src_id.'" class="'.$videothumbclass.'" src="'.get_post_meta($vb_page_post->ID,'vb_link_image',true).'" />';
									if($videothumbclass != ''){
										echo '<span class="play_icon"></span>';
									}
									
							echo '<div class="vb-post-link-title">'
										.get_post_meta($vb_page_post->ID,'vb_link_title',true).
									'</div>
									<div class="vb-post-link-url">
										<a target="_blank" href="'.get_post_meta($vb_page_post->ID,'vb_link_url',true).'">'
											.get_post_meta($vb_page_post->ID,'vb_link_url',true).'
										</a>
									</div>
									<div class="vb-post-link-desc">'
										.get_post_meta($vb_page_post->ID,'vb_link_desc',true).
									'</div>
								</div>';
							
						} else {
					?>
					<div class="vb-post-thumbhail">
						<?php 
							echo get_the_post_thumbnail( $vb_page_post->ID,'large');
						 ?>
					</div>
					<?php }?>
				</div>
				<div class="vb-comments-container">
					<?php

					$this_post_comments = get_comments(array(
						'post_id' 	=>	$vb_page_post->ID,
						'status'	 => 'approve' 
					));
					
					foreach($this_post_comments as $this_post_comment){ ?>
					
							<div class="vb-blog-single-comment" id="<?php echo $this_post_comment->comment_ID; ?>">
								<div class="vb-user-avatar">
									<?php echo get_avatar( $this_post_comment->comment_author_email, '32' ); ?>
								</div>
								<div class="vb-user-comment">
									<span> <?php echo $this_post_comment->comment_author.'</span>  <span class="date"> on '.date("F j, Y, g:i a",strtotime($this_post_comment->comment_date)) ?> </span>
									<?php echo '<p>'.$this_post_comment->comment_content.'</p>'; ?>
								</div>
								<?php /* if(is_user_logged_in()): ?>
								<a class="replay-link" href="#">reply</a>
								<div data-parent_comment="<?php echo $this_post_comment->comment_ID; ?>" data-post-id="<?php echo $vb_page_post->ID; ?>" class="vb-comment-reply">
									
									<textarea class="vb-comment-input" name="vb_comment_input" placeholder="Reply comment .. "></textarea>
									<a href="#" class="button_share reply_this_comment">Reply</a>
								</div>
								<?php endif; */ ?>
							</div>
						
					
				
					<?php
					}

					?>
				</div>
				<div class="vb-comment-box">
					<form class="vb-comment-send-form">
						<?php if(!is_user_logged_in()){ ?>
							<input type="text" name="author" placeholder="name ">
							<input type="text" name="email" placeholder="email">
						<?php }?>
						<textarea placeholder="add comment .. " name="vb_comment_input" class="vb-comment-input" ></textarea>
						<input type="hidden" value="<?php echo $vb_page_post->ID; ?>" name="subpostid">
						<input type="submit" value="add comment" class="vb-add-comment" name="add-comment">
					</form>
				</div>
			</div>
		
		<?php
			$i++;
		}
		
	 ?>
	</div>
	<div class="vb-pagination" id="vb-pagination">
		<div class="nav-previous alignleft"><?php next_posts_link( 'Older posts' ); ?></div>
		<div class="nav-next alignright"><?php previous_posts_link( 'Newer posts' ); ?></div>
	</div>
</div>
<?php
	wp_reset_query();
?>
