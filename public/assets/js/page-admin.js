var vb_PreviewData; // link object 
jQuery(document).ready(function($){
	jQuery('.linkPreview').linkPreview();
	
	$(document).on('click','.vb-page-edit-link  span',function(e){
		e.preventDefault();
		
		
		$('#popthisup').bPopup({
			fadeSpeed: 'slow', //can be a string ('slow'/'fast') or int
            followSpeed: 1500, //can be a string ('slow'/'fast') or int
            modalColor: 'white',
            modalClose: false,
          	follow: [false, false],
            transition: 'slideDown'
                      
        });
        
	});
	
	 jQuery("#wizard").steps({
			
		transitionEffect: "slide",
		enableAllSteps: true,
		titleTemplate: "#title#",
		onStepChanging: function (event, currentIndex, newIndex) {
			var pageid = jQuery('#wizard').attr('data-pageid');
			jQuery('.vbNotify').html('');
			if (currentIndex > newIndex)
			{
				//return true;
			}
			jQuery('#popthisup').append('<div class="vbNotify">Saving Data ...</div>');
			jQuery('.vbNotify')
				.hide()
				.css('text-decoration','blink')
				.fadeIn();
			
			switch(currentIndex){
				case 0:
					data = {
						action: 'vbEditPage',
						propic: jQuery('.vBpropiC.body.current img:first').attr('id'),
						tab:currentIndex,
						pageid:pageid

					}
					jQuery.ajax({
				
						url:vbAjax.ajaxurl,
						async: false,
						type:'POST',
						data:data,
						success:function(response){
						
							jQuery('.vbNotify').remove();
							
						
						}
					
					});
				break;
				case 1:
					data = {
						action: 'vbEditPage',
						coverpic: jQuery('.vBprOcoveR.body.current img:first').attr('id'),
						tab:currentIndex,
						pageid:pageid
					}
					jQuery.ajax({
				
						url:vbAjax.ajaxurl,
						async: false,
						type:'POST',
						data:data,
						success:function(response){
						
							jQuery('.vbNotify').remove();
						
						}
					
					});
					
					
				break;
				
				case 2:
					data = {
							action: 'vbEditPage',
							pagetag: jQuery('#vb_page_tag').val(),
							pagedesc: jQuery('#vbPageDesc').val(),
							tab:currentIndex,
							pageid:pageid

					}
					jQuery.ajax({
				
						url:vbAjax.ajaxurl,
						async: false,
						type:'POST',
						data:data,
						success:function(response){
						
							jQuery('.vbNotify').remove();
						
						}
					
					});
					
				break;
				
				case 3:
					data = {
							action: 'vbEditPage',
							pagewebsite: jQuery('#vb_page_website').val(),
							pageim: jQuery('#vb_page_im').val(),
							pageinternetcall: jQuery('#vb_page_internetcall').val(),
							pageemail: jQuery('#vb_page_email').val(),
							pagephone: jQuery('#vb_page_phone').val(),
							pagedate: jQuery('#vb_page_date').val(),
							pageid:pageid,
							tab:currentIndex

					}
					jQuery.ajax({
				
						url:vbAjax.ajaxurl,
						async: false,
						type:'POST',
						data:data,
						success:function(response){
						
							jQuery('.vbNotify').remove();
						
						}
					
					});
					
				break;
			
			}
				
				
				 
				return true;
				
			},
			
			 onFinishing: function (event, currentIndex){
			 	var pageid = jQuery('#wizard').attr('data-pageid');
			 	data = {
			 				pageid:pageid,
							action: 'vbEditPage',
							pagenick: jQuery('#vb_page_nickname').val(),
							pagefname: jQuery('#vb_page_firstname').val(),
							pagelname: jQuery('#vb_page_lastname').val(),
							pagecompany: jQuery('#vb_page_company').val(),
							pagejobtitle: jQuery('#vb_page_jobtitle').val(),
							pagerelationship: jQuery('#vb_page_relationship').val(),
							pageaddress: jQuery('#vb_page_address').val(),
							
							tab:'final'

				}
				
				jQuery.ajax({
				
					url:vbAjax.ajaxurl,
					async: false,
					type:'POST',
					data:data,
					success:function(response){
						jQuery('.vbNotify').remove();
						
					}
					
				});
				return true;
				
				
			 },

			onFinished: function (event, currentIndex)
			{
				location.reload();
			}
	} );
	
	jQuery(".vbProfilePic").on('click',function(event) {
		upload_button = jQuery(this);
		var frame;
		if (vbAjax.wp_version >= "3.5") {
			event.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media();
			frame.on( "select", function() {
				// Grab the selected attachment.
				
				var attachment = frame.state().get("selection").first();
				
				if( typeof attachment.attributes.sizes.thumbnail != 'undefined' ) {
					var thumb  = attachment.attributes.sizes.medium.url;
				} else {
					var thumb  = attachment.attributes.sizes.full.url;
				}
				var attachment_id   = attachment.attributes.id;
				
				jQuery('.vBpropiC  img').remove();
				jQuery('.vBpropiC button').before('<img id="'+attachment_id+'" src="'+thumb+'" >');
				jQuery( ".pro-img .page-photo img" ).replaceWith('<img id="'+attachment_id+'" src="'+thumb+'" >');
				frame.close();
				
				
			});
			frame.open();
		}
		else {
			tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
			return false;
		}
	});
	
	jQuery(".vbCoverPic").on('click',function(event) {
		upload_button = jQuery(this);
		var frame;
		if (vbAjax.wp_version >= "3.5") {
			event.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media();
			frame.on( "select", function() {
				// Grab the selected attachment.
				var attachment = frame.state().get("selection").first();
				if( typeof attachment.attributes.sizes.thumbnail != 'undefined' ) {
					var thumb  = attachment.attributes.sizes.thumbnail.url;
					var full  = attachment.attributes.sizes.full.url;
				} else {
					var thumb = attachment.attributes.sizes.full.url;
					var full  = attachment.attributes.sizes.full.url
				}
				var attachment_id   = attachment.attributes.id;
				jQuery('.vBprOcoveR  img').remove();
				jQuery('.vBprOcoveR button').before('<img id="'+attachment_id+'" src="'+thumb+'" >');
				jQuery( ".cover-img img" ).replaceWith('<img id="'+attachment_id+'" src="'+full+'" >');
				frame.close();
				
				
			});
			frame.open();
		}
		else {
			tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
			return false;
		}
	});
	
	// click on profile pic to change it
	jQuery('.vb-wrap').on('click',".pro-img .page-photo img",function(event) {
		upload_button = jQuery(this);
		var frame;
		if (vbAjax.wp_version >= "3.5") {
			event.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media();
			frame.on( "select", function() {
				// Grab the selected attachment.
				
				var attachment = frame.state().get("selection").first();
				console.log(attachment);
				if( typeof attachment.attributes.sizes.thumbnail != 'undefined' ) {
					var thumb  = attachment.attributes.sizes.medium.url;
				} else {
					var thumb  = attachment.attributes.sizes.full.url;
				}
				
				
				var attachment_id   = attachment.attributes.id;
				jQuery( ".pro-img .page-photo img" ).replaceWith('<img id="'+attachment_id+'" src="'+thumb+'" >');
				frame.close();
				data = {
						action: 'vbEditPage',
						propic: jQuery('.pro-img .page-photo img').attr('id'),
						tab:'0',
						pageid:jQuery('.vb-wrap').attr('id'),

					}
				 jQuery.ajax({
			
					url:vbAjax.ajaxurl,
					type:'POST',
					data:data,
					success:function(response){
					}
				
				});
			});
			frame.open();
		}
		else {
			tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
			return false;
		}
	});
	
	// click on cover pic to change it
	jQuery('.vb-wrap').on('click',".cover-img img",function(event) {
		upload_button = jQuery(this);
		var frame;
		if (vbAjax.wp_version >= "3.5") {
			event.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media();
			frame.on( "select", function() {
				// Grab the selected attachment.
				
				var attachment = frame.state().get("selection").first();
				var fullimg  = attachment.attributes.sizes.full.url;
				var attachment_id   = attachment.attributes.id;
				jQuery( ".cover-img img" ).replaceWith('<img id="'+attachment_id+'" src="'+fullimg+'" >');
				frame.close();
				data = {
						action: 'vbEditPage',
						coverpic: jQuery('.cover-img img').attr('id'),
						tab:'1',
						pageid:jQuery('.vb-wrap').attr('id'),

					}
				 jQuery.ajax({
			
					url:vbAjax.ajaxurl,
					type:'POST',
					data:data,
					success:function(response){
					}
				
				});
			});
			frame.open();
		}
		else {
			tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
			return false;
		}
	});
	
	// publish box transition on textarea focus
		
		jQuery('body').on('focus', '.vb-publish-input', function(){
			
			if(jQuery('.vb-publish-option .vb-publish-text').hasClass('focus')){
				jQuery('.vb-publish-option .vb-publish-text span').trigger('click');
				
			}
		
			
			
		});
		
		// publish box transition on choosing post type
		jQuery('body').on('click', '.vb-publish-option > div span', function(){
		
			
			var blog_type = jQuery(this).parent().attr('data-blog_type');
			
			jQuery('.vb-publish-box').attr('data-blog_type',blog_type);
			jQuery('.vb-publish-option > div')
				.removeClass('focus')
				.css({
					'display':'none',
					'width':'51%',			
				});
			jQuery(this).parent().addClass('focus').css('display','block');
			jQuery(this).parent().find('.vb-publish-tools').css('display','block').hide().fadeIn(1500);
			jQuery('.vb-publish-controls').css('display','block');
			jQuery('.vb-post-title').css('display','block');
			
			jQuery('.vb-publish-box').css({
				margin			 : '2% auto',
				float			 : 'none',
				height			 : 'auto',
				width		   	 : '70%',
				WebkitTransition : 'height 0.5s ease, width 0.5s ease',
				MozTransition    : 'height 0.5s ease, width 0.5s ease',
				MsTransition     : 'height 0.5s ease, width 0.5s ease',
				OTransition      : 'height 0.5s ease, width 0.5s ease',
				transition       : 'height 0.5s ease, width 0.5s ease'
			});
			
			jQuery('.vb-publish-input').css({
				
				height			 : '130px',
				width		   	 : '100%',
				WebkitTransition : 'height 0.5s ease, width 0.5s ease',
				MozTransition    : 'height 0.5s ease, width 0.5s ease',
				MsTransition     : 'height 0.5s ease, width 0.5s ease',
				OTransition      : 'height 0.5s ease, width 0.5s ease',
				transition       : 'height 0.5s ease, width 0.5s ease'
			});
		});

		// reset publish box css
		jQuery('body').on('click','.vb-publish-controls .button_cancel',function(e){
			
			e.preventDefault();
			vb_PreviewData = ''; // reset link data
			jQuery('#closePreview').trigger('click');
			jQuery('#text').val('');
			jQuery('.vb-post-title').val('');
			jQuery('.vb-publish-input').val('');
			jQuery('.vb-publish-attachments').html(' ');
			
			jQuery('.vb-publish-option > div').removeClass('focus').css({
					'display':'block',
					'width':'25%',			
				});
			jQuery('.vb-publish-text').addClass('focus');
			jQuery('.vb-publish-tools').css('display','none');
			jQuery('.vb-publish-controls').css('display','none');
			jQuery('.vb-post-title').css('display','none');
			jQuery('.linkPreview').css('display','none');
			jQuery('.vb-publish-box').css({
				margin			 : '1% 0.5%',
				float			 : 'left',
				height			 : 'auto',
				width		   	 : '49%',
				WebkitTransition : 'height 0.5s ease, width 0.5s ease',
				MozTransition    : 'height 0.5s ease, width 0.5s ease',
				MsTransition     : 'height 0.5s ease, width 0.5s ease',
				OTransition      : 'height 0.5s ease, width 0.5s ease',
				transition       : 'height 0.5s ease, width 0.5s ease'
			});
	
			jQuery('.vb-publish-input').css({
				height			 : '70px',
				width		   	 : '100%',
				WebkitTransition : 'height 0.5s ease, width 0.5s ease',
				MozTransition    : 'height 0.5s ease, width 0.5s ease',
				MsTransition     : 'height 0.5s ease, width 0.5s ease',
				OTransition      : 'height 0.5s ease, width 0.5s ease',
				transition       : 'height 0.5s ease, width 0.5s ease'
			});
		});
		
		jQuery('body').on('click','.vb-publish-tools .vb-post-pic',function(e){
			e.preventDefault();
			upload_button = jQuery(this);
			var frame;
			if (vbAjax.wp_version >= "3.5") {
				
				if (frame) {
					frame.open();
					return;
				}
				frame = wp.media();
				frame.on( "select", function() {
					// Grab the selected attachment.
				
					var attachment = frame.state().get("selection").first();
					
					if( typeof attachment.attributes.sizes.thumbnail != 'undefined' ) {
						var thumb  = attachment.attributes.sizes.thumbnail.url;
					} else {
						var thumb  = attachment.attributes.sizes.full.url;
					}
					var attachment_id   = attachment.attributes.id;
					
						jQuery('.vb-publish-box').css('height','auto');
				
					
					jQuery('.vb-publish-attachments').append('<input type="hidden" value="'+attachment_id+'"name="vb_post_attachments[]" class="vb_post_attachments"/>');
					jQuery('.vb-publish-attachments').append('<div class="vb-img-thumb"><img id="'+attachment_id+'" src="'+thumb+'" />');
					
					frame.close();
				
				});
				frame.open();
			}
			else {
				tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
				return false;
			}
		});
		
		jQuery('body').on('click','.vb-publish-controls #vb-publish-post',function(e){
			e.preventDefault();
			
			var post_title = jQuery(this).parents('.vb-publish-box').find('.vb-publish-txtbox .vb-post-title').val();
			post_title = jQuery.trim(post_title);
			
			if(post_title == ''){
				alert('please name your post');
				return false;
			}
			
			
			var previewobj 		= escape(JSON.stringify(vb_PreviewData));
			var master  		=  jQuery('.vb-wrap').attr('id');
			var type  			=  jQuery('.vb-publish-box').attr('data-blog_type');
			var data 			= $('#vb-publish-form').serialize()+'&type='+type+'&master='+master;
			if(vb_PreviewData != '')
			var data 			= $('#vb-publish-form').serialize()+'&type='+type+'&master='+master+'&previewobj='+previewobj;
			
				 jQuery.ajax({
			
					url			:vbAjax.ajaxurl,
					type		:'POST',
					data		:data,
					success		:function(response){
						if((response) && (response!= 0)){
							
							var firstClass = jQuery('.vb-blog-post:first').attr('class');
							jQuery('.vb-publish-controls .button_cancel').trigger('click');
							
							jQuery('.current_page_posts ').prepend(response);
							jQuery('.vb-blog-post:first').fadeIn(1000);
							if(firstClass == 'left'){
								jQuery('.vb-blog-post:first').addClass('right');
							}else{
								jQuery('.vb-blog-post:first').addClass('left');
							}
							
						}
					}
				
				});
			
		});
		
		// show link posting options for first tym & envoke link preview handler
		jQuery('body').one('click','.vb-publish-tools .button_share.vb-post-link, .vb-publish-tools .button_share.vb-post-yt-video',function(e){
			e.preventDefault();
			jQuery('.vb-publish-box .linkPreview').show(function() {
				jQuery('.linkPreview').linkPreview();
			});
		});
		
		// show link posting options for rest of the times
		jQuery('body').on('click','.vb-publish-tools .button_share.vb-post-link, .vb-publish-tools .button_share.vb-post-yt-video',function(e){
			e.preventDefault();
			jQuery('.vb-publish-box .linkPreview').show();
		});
		
		
		
});


