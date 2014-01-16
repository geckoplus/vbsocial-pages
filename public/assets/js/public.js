jQuery(document).ready(function($){
          
	jQuery('body').on('click','#vb-pagination a', function(e){
		e.preventDefault();
		var data = {
			action:'vb_load_more_posts',
		}
		jQuery.ajax({
			url:vbAjax.ajaxurl,
			type:'POST',
			data:data,
			success:function(response){
				
			}
		
		});

	});
  
	$('ul.createpage li ').on('click',function(e){
		e.preventDefault();
		var id =  $(this).attr('id');
		$('#page-create-wizard').attr('data-pagetype',id);
		$('#popthisup').bPopup({
			fadeSpeed: 'slow', //can be a string ('slow'/'fast') or int
            followSpeed: 1500, //can be a string ('slow'/'fast') or int
            modalColor: 'white',
            modalClose: false,
                       
        });
        
	});
	
	$('.vbCreate button.VbFirstStepBtn').on('click',function(e){
	
		
		jQuery('.vbNotify').css('display','block');
		
		var VBFirstStep = jQuery('#VbFirstStep').val();
		if(jQuery.trim(VBFirstStep) == ''){			
			jQuery('.vbNotify').hide().html('Page Name Cannot be empty !').fadeIn(1500).fadeOut('slow');
			return false;
		}
				
		jQuery('.vbNotify').html(' Creating ...');
		data = {
			pagetype : jQuery('#page-create-wizard').attr('data-pagetype'),
			action: 'vbCreatePage',
			pagename: jQuery('#VbFirstStep').val(),
			

		}
		jQuery.ajax({
			url:vbAjax.ajaxurl,
			type:'POST',
			data:data,
			success:function(response){
				if(response != '') {
					
					var response = JSON.parse(response);
					if(response.status == 1){
						jQuery('.vbNotify').html('Redirecting ... ');
						//console.log(response.message);
						window.location.replace(response.message);
					} 
					if(response.status == 2){
						jQuery('.vbNotify').html(response.message);
						
					}
					
				} 
			}
		
		});
	});
	
		jQuery('.vb-wrap').on('click','.page-follow .follow',function(e){
			e.preventDefault();
			var data={
				action:'follow_page',
				pageid: jQuery('.vb-wrap').attr('id')
			}
			jQuery.ajax({
				url:vbAjax.ajaxurl,
				type:'POST',
				data:data,
				success:function(response){
					if(response == 1) {
						jQuery('.page-follow .follow').replaceWith('<span class="fan"><a href="#">following</a>');
					} 
				}
		
			});
		});

	
		jQuery('.vb-wrap').on('click','.page-follow .fan',function(e){
			e.preventDefault();
			var data={
				action:'unfollow_page',
				pageid: jQuery('.vb-wrap').attr('id')
			}
			jQuery.ajax({
				url:vbAjax.ajaxurl,
				type:'POST',
				data:data,
				success:function(response){
					if(response == 1) {
						jQuery('.page-follow .fan').replaceWith('<span class="follow"><a href="#">follow</a>');
					} 
				}
		
			});
		});
	
		var elClicked;

		// comment box transition
		jQuery('body').on('focus', '.vb-comment-input', function(){
			
			jQuery(this).parent().find('.vb-add-comment').css('visibility','visible');
		});
		
		jQuery('body').on('blur', '.vb-comment-input', function(event){
			
			jQuery(this).parent().find('.vb-add-comment').css('visibility','hidden');
		});
		
		//  This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      //  This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      
		jQuery('body').on('click', '.vb-post-thumbhail > img.youtubeimg', function(){
			jQuery(this).parent().find('span.play_icon').hide();
			videoId = jQuery(this).attr('data-ytid');
			jQuery(this).attr('id',videoId);
		    player = new YT.Player(videoId, {
		      height: '390',
		      width: '640',
		      videoId: videoId,
		      events: {
		        'onReady': onPlayerReady,
		        'onStateChange': onPlayerStateChange
		      }
		    });
		});
		
	 // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) {
      
        event.target.playVideo();
      }

      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var done = false;
      function onPlayerStateChange(event) {
      	
        if (event.data == YT.PlayerState.PLAYING && !done) {
          setTimeout(stopVideo, 6000);
          done = true;
        }
      }
      function stopVideo() {
        player.stopVideo();
      }
	
	//===== vimeo videos ==== //
	
	jQuery('body').on('click', '.vb-post-thumbhail > img.vimeoimg', function(){
			jQuery(this).parent().find('span.play_icon').hide();
			
			var videoId 	 = jQuery(this).attr('data-ytid');
			var videoFrame  = '<iframe id="'+videoId+'" width="500" height="281" src="http://player.vimeo.com/video/'+videoId+'" webkitAllowFullScreen mozallowfullscreen allowFullScreen ></iframe>';
			jQuery(this).replaceWith(videoFrame);
			
		    
		});
		
	//===== vimeo videos ==== //	
		
		jQuery('body').on('click', '.vb-post-thumbhail > span.play_icon', function(){
			jQuery(this).parent().find('img.youtubeimg,img.vimeoimg').trigger('click');
		});
	
		jQuery('body').on('submit', '.vb-comment-send-form', function(event){
			var el_form = $(this);
			
			el_form.css('visibility','visible');
			event.preventDefault();
			
			jQuery.ajax({
				url:vbAjax.ajaxurl,
				type:'POST',
				data:el_form.serialize()+'&action=vb_add_comment',
				success:function(response){
					if(response != '') {
						jQuery('.vb_comment_error').remove();
						try
						{
							var response = JSON.parse(response);
							
							if(response.success == 1) {
								
								el_form.parents('.vb-blog-post:first').find('.vb-comments-container').prepend(response.message);
								el_form.parents('.vb-blog-post:first').find('.vb-comments-container .vb-blog-single-comment:first').hide().fadeIn('slow');
							} else if(response.success == 2) {
									el_form.parents('.vb-blog-post:first').find('.vb-comments-container').append('<div class="vb_comment_error">'+response.message+'</div>');
									jQuery('.vb_comment_error').hide().fadeIn().delay(3000).fadeOut('slow');
								} else if(response.success == 0) {
								  		el_form.parents('.vb-blog-post:first').find('.vb-comments-container').append('<div class="vb_comment_error">'+response.message+'</div>');
								  		jQuery('.vb_comment_error').hide().fadeIn().delay(3000).fadeOut('slow');
								}
						}
						catch(e)
						{
							el_form.parents('.vb-blog-post:first').find('.vb-comments-container').append('<div class="vb_comment_error">'+response+'</div>');
							jQuery('.vb_comment_error').hide().fadeIn().delay(3000).fadeOut('slow');
						}
						
						
					} 
				}
		
			});
		});
	
		jQuery('body').on('mousedown', '.vb-add-comment', function(event){
			event.preventDefault();
		});
		
		jQuery('body').on('click', '.replay-link', function(event){
			event.preventDefault();
			jQuery(this).parent().find('.vb-comment-reply').slideToggle('slow');
		});
		
		/*
		jQuery('body').on('click', '.vb-comment-reply a.button_share.reply_this_comment', function(event){
			event.preventDefault();
			var data = {
				action:'vb_add_comment',
				subpostid: jQuery(this).parent().attr('data-post-id'),
				comment_parent: jQuery(this).parent().attr('data-parent_comment'),
				vb_comment_input:jQuery(this).parent().find('.vb-comment-input').val(),
			}
			jQuery.ajax({
				url:vbAjax.ajaxurl,
				type:'POST',
				data:data,
				success:function(response){
					if(response) {
						
					} 
				}
		
			});
			
		});
		*/
		
	  $("#vb-pro-posts").steps({
		transitionEffect: "fade",
		enableFinishButton: false,
		enablePagination: false,
		enableAllSteps: true,
		titleTemplate: "#title#",
		
		onStepChanged: function (event, currentIndex, newIndex){
			 history.pushState("", document.title, window.location.pathname
                                                       + window.location.search);
			switch(currentIndex){
				case 1:
					jQuery.ias({
						container : '#current_page_posts',
						item: '.vb-blog-post',
						pagination: '#vb-pagination',
						next: '.nav-previous a',
						loader: 'loading ... ',
						triggerPageThreshold: 1,
						
					});
				break;
				
				case 2:
					jQuery.ias({
						container : '.vb-photos-container',
						item: '.single-post-images',
						pagination: '#vb-pic-pagination',
						next: '.nav-pic-previous a',
						loader: 'loading ... ',
						triggerPdageThreshold: 1,
						onRenderComplete: function(items) {
							jQuery('.single-post-images ul li a[rel^="prettyPhoto"]').prettyPhoto({
								social_tools: false,
								show_title	:	false,
								 theme: 'facebook',
							});
						}
						
					});
					jQuery('.single-post-images ul li a[rel^="prettyPhoto"]').prettyPhoto({
						social_tools: false,
						show_title	:	false,
						 theme: 'facebook',
					});
					
				break;
				
				case 3:
					jQuery.ias({
						container : '.vb-videos-container',
						item: '.single-post-video',
						pagination: '#vb-vid-pagination',
						next: '.nav-vid-previous a',
						loader: 'loading ... ',
						triggerPageThreshold: 1,
						onRenderComplete: function(items) {
							jQuery('.single-post-video a[rel^="prettyPhoto"]').prettyPhoto({
								social_tools	: 	false,
								show_title		:	false,
								theme			: 	'facebook',
							});
						}
					}); 
					  jQuery('.single-post-video a[rel^="prettyPhoto"]').prettyPhoto({
							social_tools	: 	false,
							show_title		:	false,
							theme			: 	'facebook',
						});
				break;
    
			}
			return true;
		}
		
		
    });

	// handle notifications 
	jQuery('.vb-notification-container').on('click','.single-notification span',function(){
		
		var noticeid = jQuery(this).parents('.single-notification').attr('data-noticeid');
		var notice_block = jQuery(this).parents('.single-notification');
		var data = {
			action:'vb_remove_notification',
			noticeid:noticeid
		}
		jQuery.ajax({
			url:vbAjax.ajaxurl,
			type:'POST',
			data:data,
			success:function(response){
				
				if(response == 1)
					
					jQuery(notice_block).slideUp('slow');
			}
		
		});
		
	});
	
	// master page pagination 
	if(jQuery('.vb_master_wrapper').length){
		jQuery.ias({
			container : '.vb_master_wrapper',
			item: '.vb_master_single',
			pagination: '#vb-master-pagination',
			next: '.nav-master-previous a',
			loader: 'loading ... ',
			triggerPageThreshold: 1,
			
		}); 
	}
	
	jQuery('.vb-share-icon').on('click',function(){
		jQuery('.vb-page-sharing').slideToggle();
	});
	
	jQuery('.pro-img').on('click','.vb-facebook-sharing a',function(e){
		e.preventDefault();
		var url = jQuery(this).attr('href');
		window.open(url, 'facebook_share', 'height=320, width=640, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no');
	});
	
	
});

 
   
     

