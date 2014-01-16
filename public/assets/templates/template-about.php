<div class="vb-about-wrap">
	<div class="vb-page-post vbstory">
		<div class="vb-page-post-title ">
			<h2> Story </h2>
		</div>
		<div class="vb-page-post-content">
			<div class="vb-page-post-content-inner">
				<span> Tagline </span>
				<?php echo get_post_meta(get_the_ID(),'vb_tagline',true); ?>
			</div>
			<div class="vb-page-post-content-inner">
				<span> Introduction </span>
				<?php echo get_post_field( 'post_content', get_the_ID()); ?>
			</div>
			<?php
				do_action('vbpage_bottom_about_blocks');
			?>
			
		</div>
		
	</div>
	
	<div class="vb-page-post vbcontact">
		<div class="vb-page-post-title">
			<h2> Contact Information </h2>
		</div>
		<div class="vb-page-post-content">
			<div class="vb-page-post-content-inner">
			
				<?php if('' != get_post_meta(get_the_ID(),'vb_nickname',true)) {?>
					<span> Nickname  </span>
					<?php echo get_post_meta(get_the_ID(),'vb_nickname',true); }?>
				
			</div>
			
			<div class="vb-page-post-content-inner">
			
				<?php if('' != get_post_meta(get_the_ID(),'vb_phonetic_firstname',true)) {?>
					<span> First Name </span>
					<?php echo get_post_meta(get_the_ID(),'vb_phonetic_firstname',true); }?>
				
			</div>
			
			<div class="vb-page-post-content-inner">
			
				<?php if('' != get_post_meta(get_the_ID(),'vb_phonetic_lastname',true)) {?>
					<span> Last Name </span>
					<?php echo get_post_meta(get_the_ID(),'vb_phonetic_lastname',true); }?>
				
			</div>
			
			<div class="vb-page-post-content-inner">
			
				<?php if('' != get_post_meta(get_the_ID(),'vb_company',true)) {?>
					<span> Company </span>
					<?php echo get_post_meta(get_the_ID(),'vb_company',true); }?>
				
			</div>
			
			<div class="vb-page-post-content-inner">
			
				<?php if('' != get_post_meta(get_the_ID(),'vb_job_title',true)) {?>
					<span> Job Title </span>
					<?php echo get_post_meta(get_the_ID(),'vb_job_title',true); }?>
				
			</div>
			
			
			<div class="vb-page-post-content-inner">
			
				<?php if('' != get_post_meta(get_the_ID(),'vb_address',true)) {?>
					<span> Address </span>
					<?php echo get_post_meta(get_the_ID(),'vb_address',true); }?>
				
			</div>
			
			<div class="vb-page-post-content-inner">
			
				<?php if('' != get_post_meta(get_the_ID(),'vb_relationship',true)) {?>
					<span> Relationship </span>
					<?php echo get_post_meta(get_the_ID(),'vb_relationship',true); }?>
				
			</div>
			
			
			
			
			<?php
				do_action('vbpage_bottom_about_blocks');
			?>
			
		</div>
		
	</div>
	
	<div class="vb-page-post vblinks">
		<div class="vb-page-post-title">
			<h2> Links </h2>
		</div>
		<div class="vb-page-post-content-inner">
			
			<?php if('' != get_post_meta(get_the_ID(),'vb_website',true)) {?>
				<span> Website </span>
				<?php echo get_post_meta(get_the_ID(),'vb_website',true); }?>
			
		</div>
		<div class="vb-page-post-content-inner">
			
			<?php if('' != get_post_meta(get_the_ID(),'vb_im',true)) {?>
				<span> IM </span>
				<?php echo get_post_meta(get_the_ID(),'vb_im',true); }?>
			
		</div>
		<div class="vb-page-post-content-inner">
		
			<?php if('' != get_post_meta(get_the_ID(),'vb_internet_call',true)) {?>
				<span> Internet Call </span>
				<?php echo get_post_meta(get_the_ID(),'vb_internet_call',true); }?>
			
		</div>
		<div class="vb-page-post-content-inner">
			
			<?php if('' != get_post_meta(get_the_ID(),'vb_email',true)) {?>
				<span> Email </span>
				<?php echo get_post_meta(get_the_ID(),'vb_email',true); }?>
			
		</div>
		
		<div class="vb-page-post-content-inner">
		
			<?php if('' != get_post_meta(get_the_ID(),'vb_phone',true)) {?>
				<span> Phone  </span>
				<?php echo get_post_meta(get_the_ID(),'vb_phone',true); }?>
			
		</div>
		
		<div class="vb-page-post-content-inner">
		
			<?php if('' != get_post_meta(get_the_ID(),'vb_date',true)) {?>
				<span> Date </span>
				<?php echo get_post_meta(get_the_ID(),'vb_date',true); }?>
			
		</div>
		<?php
				do_action('vbpage_bottom_about_blocks');
		?>
		
		
	</div>


</div>
