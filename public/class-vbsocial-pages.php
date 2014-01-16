<?php
/**
 * 
 *
 * @package   vbsocial
 *
 */

/**
 * vbsocial class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-vbsocial-pages-admin.php`
 *
 *
 *
 * @package vbsocial
 * 
 */
class vbsocial {

	/**
	 * vb version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * 
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $vb_slug = 'vbsocial-pages';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the vb by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		
		// Load vb text domain
		add_action( 'init', array( $this, 'load_vb_textdomain' ) );
		add_shortcode('vb_master_page',array( $this,'get_master_page'));
		add_shortcode('vb_create_page',array( $this,'get_create_page'));
		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
		add_action( 'init', array( $this, 'Create_vbsocial_posttype' ) );
		add_action( 'init', array( $this, 'Create_vbsocial_taxonomies' ) );
		add_action( 'get_header', array( $this, 'is_vbpage_admin' ) );
		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_nopriv_vbCreatePage', array( $this, 'VbCreatePage' ) );
		add_action( 'wp_ajax_vbCreatePage', array( $this, 'VbCreatePage' ) );
		add_filter('media_upload_default_tab', array( $this,'media_uplaoder_tabs'));
		add_action( 'wp_ajax_nopriv_vbEditPage', array( $this, 'vbEditPage' ) );
		add_action( 'wp_ajax_vbEditPage', array( $this, 'vbEditPage' ) );
		
		add_action( 'wp_ajax_generate_link_preview', array( $this, 'generate_link_preview' ) );
		add_action( 'wp_ajax_nopriv_generate_link_preview', array( $this, 'generate_link_preview' ) );
		add_action( 'wp_ajax_highlight_urls', array( $this, 'highlight_urls' ) );
		add_action( 'wp_ajax_nopriv_highlight_urls', array( $this, 'highlight_urls' ) );
		
		add_action( 'wp_ajax_vb_add_comment', array( $this, 'vb_add_comment' ) );
		add_action( 'wp_ajax_nopriv_vb_add_comment', array( $this, 'vb_add_comment' ) );
		
		add_action( 'wp_ajax_vb_load_more_posts', array( $this, 'vb_load_more_posts' ) );
		add_action( 'wp_ajax_nopriv_vb_load_more_posts', array( $this, 'vb_load_more_posts' ) );
		
		add_action( 'wp_ajax_vb_remove_notification', array( $this, 'remove_notification' ) );
		add_action( 'wp_ajax_nopriv_vb_remove_notification', array( $this, 'remove_notification' ) );
		
		
		add_action( 'wp_ajax_follow_page', array( $this, 'follow_page' ) );
		add_action( 'wp_ajax_nopriv_follow_page', array( $this, 'follow_page' ) );
		add_action( 'wp_ajax_unfollow_page', array( $this, 'unfollow_page' ) );
		add_action( 'wp_ajax_nopriv_unfollow_page', array( $this, 'unfollow_page' ) );
		
		add_action( 'wp_ajax_vb_publish_post', array( $this, 'vb_publish_post' ) );
		add_action( 'wp_ajax_nopriv_vb_publish_post', array( $this, 'vb_publish_post' ) );
		
		//add_filter( 'manage_edit-vbblog_columns', array( $this, 'add_columns_to_vbblog') );
		//add_action( 'manage_vbblog_posts_custom_column' , array( $this,'add_columns_content_to_vbblog'), 10, 2 );
		
		add_filter('media_view_strings',array( $this,'remove_medialibrary_tab'));
		add_action('wp_ajax_query-attachments',array( $this, 'restrict_non_admins'),1);
		add_action('wp_ajax_nopriv_query-attachments',array( $this, 'restrict_non_admins'),1);
		
		add_action('template_redirect',array($this,'assign_templates_tovbposts'));
		

	}

	/**
	 * Return the vb slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    vb slug variable.
	 */
	public function get_vb_slug() {
		return $this->vb_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or vb is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}
		
		

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
	
		global $wpdb;
 	
		$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."vb_follow (

				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`user_id` bigint(20) NOT NULL,
				`post_id` bigint(20) NOT NULL,
				`followed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				 PRIMARY KEY (`id`)

		)";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	   
		dbDelta( $sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."vb_activities (

				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`users` text NOT NULL,
				`page_id` bigint(20) NOT NULL,
				`post_id` bigint(20) NOT NULL,
				`activity` varchar(200) NOT NULL,
				`time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				 PRIMARY KEY (`id`)

		)";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	   
		dbDelta( $sql);
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the vb text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_vb_textdomain() {
	
		

		$domain = $this->vb_slug;
		$locale = apply_filters( 'vb_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->vb_slug . '-vb-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post;
		
		
		wp_enqueue_script( $this->vb_slug . 'prettyphoto', plugins_url( 'assets/js/jquery.prettyPhoto.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( $this->vb_slug . '-vb-ias', plugins_url( 'assets/js/jquery-ias.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( $this->vb_slug . '-vb-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_localize_script( $this->vb_slug . '-vb-script', 'vbAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'wp_version' => get_bloginfo('version')));
		wp_enqueue_script( 'vb-bpopup.min', plugins_url( 'assets/js/jquery.bpopup.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( 'vb-steps.min', plugins_url( 'assets/js/jquery.steps.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		if (get_bloginfo('version') >= 3.5)
		wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		
		if($post->post_type == 'vbsocial'){
			wp_enqueue_script( 'gplusshare', 'https://apis.google.com/js/plusone.js', array( 'jquery' ), self::VERSION );
		}
		
		
		//check if current user is admin of this page & if he/she is .. load page admin specific script
			if(get_current_user_id() == get_post_meta(get_the_ID(),'page_admin',true)){
				wp_enqueue_script( $this->vb_slug . '-vb-page-admin', plugins_url( 'assets/js/page-admin.js', __FILE__ ), array( 'jquery' ), self::VERSION );
				wp_localize_script( $this->vb_slug . '-vb-page-admin', 'vbAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'wp_version' => get_bloginfo('version')));
				
				wp_enqueue_script( $this->vb_slug . '-vb-link-preview', plugins_url( 'assets/js/linkPreview.js', __FILE__ ), array( 'jquery' ), self::VERSION );
				wp_localize_script( $this->vb_slug . '-vb-link-preview', 'LinkPre', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'wp_version' => get_bloginfo('version')));
				
			
			}
			
	}

	

	public function Create_vbsocial_posttype() {
	
		
		
		$labels = array(
						'name' 					=> _x('VbSocial Pages', 'vbsocial'),
						'singular_name' 		=> _x('VbSocial Page', 'vbsocial'),
						'add_new' 				=> __('Add New', 'vbsocial'),
						'add_new_item' 			=> __(' Add New', 'vbsocial'),
						'edit_item' 			=> __('Edit Page', 'vbsocial'),
						'new_item' 				=> __('New Page', 'vbsocial'),
						'all_items' 			=> __('All Pages', 'vbsocial'),
						'view_item' 			=> __('View Pages', 'vbsocial'),
						'search_items' 			=> __('Search Pages', 'vbsocial'),
						'not_found' 			=>  __('No Pages found', 'vbsocial'),
						'not_found_in_trash' 	=> __('No Page found in Trash', 'vbsocial'),
						'parent_item_colon' 	=> '',
						'menu_name' 			=> __('vBSocial Pages', 'vbsocial')
					);
		$args = array(
						'labels' 				=> $labels,
						'public' 				=> true,
						'publicly_queryable' 	=> true,
						'show_ui' 				=> true,
						'show_in_menu'			=> true,
						'query_var' 			=> true,
						'rewrite'            	=> array( 'slug' => 'vbsocial-pages' ),
						'capability_type' 		=> 'post',
						'has_archive' 			=> true,
						'hierarchical' 			=> true,
						'menu_position' 		=> null,
					   
						'supports' 				=> array( 'title', 'editor','thumbnail','excerpt','comments','page-attributes')
					);
		register_post_type('vbsocial', $args);
		
		$labels = array(
				'name' 					=> _x('vBSocial Pages Items', 'vbsocial'),
				'singular_name' 		=> _x('vBSocial Pages Items', 'vbsocial'),
				'add_new' 				=> __('Add Item', 'vbsocial'),
				'add_new_item' 			=> __(' Add Item', 'vbsocial'),
				'edit_item' 			=> __('Edit Item', 'vbsocial'),
				'new_item' 				=> __('New Item', 'vbsocial'),
				'all_items' 			=> __('All Item', 'vbsocial'),
				'view_item' 			=> __('View Item', 'vbsocial'),
				'search_items' 			=> __('Search Item', 'vbsocial'),
				'not_found' 			=>  __('No Item found', 'vbsocial'),
				'not_found_in_trash' 	=> __('No Item found in Trash', 'vbsocial'),
				'parent_item_colon' 	=> '',
				'menu_name' 			=> __('vBSocial Pages Items', 'vbsocial')
			);
	$post_args = array(
					'labels' 				=> $labels,
					'public' 				=> true,
					'publicly_queryable' 	=> true,
					'show_ui' 				=> true,
					'show_in_menu'			=> true,
					'query_var' 			=> true,
					'rewrite'            	=> array( 'slug' => 'vb-blog' ),
					'capability_type' 		=> 'post',
					'has_archive' 			=> true,
					'hierarchical' 			=> true,
					'menu_position' 		=> null,
				   
					'supports' 				=> array( 'title', 'editor','thumbnail','excerpt','comments','page-attributes')
				);
	register_post_type('vbblog', $post_args);
	
	
		
	}

	public function Create_vbsocial_taxonomies(){
		$tax_labels = array(
							'name'              => __( 'Genres', 'vbsocial'),
							'singular_name'     => __( 'Genre' , 'vbsocial'),
							'search_items'      => __( 'Search Genres' , 'vbsocial'),
							'all_items'         => __( 'Genres' , 'vbsocial' ),
							'edit_item'         => __( 'Edit Genre' , 'vbsocial'),
							'update_item'       => __( 'Update Genre' , 'vbsocial'),
							'add_new_item'      => __( 'Add New Genre' , 'vbsocial'),
							'new_item_name'     => __( 'New Genre Name' , 'vbsocial'),
							'menu_name'         => __( 'Genres' , 'vbsocial' ),
					 );
		register_taxonomy(	'vb_genre',
							'vbsocial',
							array(
								"hierarchical" 			=> true,
								"labels" 				=> $tax_labels,
								'update_count_callback' => '_update_post_term_count',
								'query_var' 			=> true,
								'rewrite' 				=> array( 'slug' => 'vb_genre', 'with_front' => false ),
								'public' 				=> true,
								'show_ui' 				=> true,
								'show_tagcloud' 		=> true,
								'_builtin' 				=> false,
								'show_in_nav_menus' 	=> false
							)
						);
					
		$genres_list = array(
							__( 'Local business or place', 'vbsocial'),
							__( 'Company Organisation', 'vbsocial'),
							
							__( 'Brand or product', 'vbsocial'),
							__( 'Artist band or public figure', 'vbsocial'),
							__( 'Cause community', 'vbsocial'),
						
						);
		foreach($genres_list as $genre){
			if(!term_exists($genre,'vb_genre')){
		
				$this_term = wp_insert_term($genre,'vb_genre');
				
				switch($genre){
				
					case 'Local business or place':
						$default_img = plugins_url('vbsocial-pages')."/assets/icon-map.png";
						update_option('z_taxonomy_image'.$this_term['term_id'], $default_img);
					 break;
						
					case 'Artist band or public figure':
						$default_img = plugins_url('vbsocial-pages')."/assets/icon-ent.png";
						update_option('z_taxonomy_image'.$this_term['term_id'], $default_img);
					 break;
						
					case 'Company Organisation':
						$default_img = plugins_url('vbsocial-pages')."/assets/icon-bulding.png";
						update_option('z_taxonomy_image'.$this_term['term_id'], $default_img);
					 break;
						
					case 'Brand or product':
						$default_img = plugins_url('vbsocial-pages')."/assets/icon-brand.png";
						update_option('z_taxonomy_image'.$this_term['term_id'], $default_img);
					 break;
						
					case 'Cause community':
						$default_img = plugins_url('vbsocial-pages')."/assets/icon-other.png";
						update_option('z_taxonomy_image'.$this_term['term_id'], $default_img);
					 break;
				}
				
				
			}
		}
	
		$tax_labels = array(
							'name'              => __( 'Type', 'vbsocial'),
							'singular_name'     => __( 'Type' , 'vbsocial'),
							'search_items'      => __( 'Search Types' , 'vbsocial'),
							'all_items'         => __( 'Types' , 'vbsocial' ),
							'edit_item'         => __( 'Edit Type' , 'vbsocial'),
							'update_item'       => __( 'Update Type' , 'vbsocial'),
							'add_new_item'      => __( 'Add New Type' , 'vbsocial'),
							'new_item_name'     => __( 'New Type Name' , 'vbsocial'),
							'menu_name'         => __( 'Blog Type' , 'vbsocial' ),
					 );
		register_taxonomy(	'vb_blog_type',
							'vbblog',
							array(
								"hierarchical" 			=> true,
								"labels" 				=> $tax_labels,
								'update_count_callback' => '_update_post_term_count',
								'query_var' 			=> true,
								'rewrite' 				=> array( 'slug' => 'vb_blog_type', 'with_front' => false ),
								'public' 				=> true,
								'show_ui' 				=> true,
								'show_tagcloud' 		=> true,
								'_builtin' 				=> false,
								'show_in_nav_menus' 	=> false
							)
						);
					
		$type_list = array(
							__( 'Text', 'vbsocial'),
							__( 'Link', 'vbsocial'),
							__( 'Video', 'vbsocial'),
							__( 'Image', 'vbsocial'),
							
						
						);
		foreach($type_list as $type){
			if(!term_exists($type,'vb_blog_type')){
		
				$this_term = wp_insert_term($type,'vb_blog_type');
				
				
			}
		}
	
	}
	
	// generate list of vbsocial pages 
	public function get_master_page(){
		global $wpdb;
		$vb_admin_settings  = get_option('settingstemplate_settings'); 
		$return_string = '';
		
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$vb_master_pages = query_posts(
			array(
				'post_type'			=> 		'vbsocial',
				'posts_per_page'	=>		$vb_admin_settings['settingstemplate_general_vb_page_per_box'],
				'paged' 			=> 		$paged,
				'post_status'		=>		'publish',
				'meta_key'			=>		$vb_admin_settings['settingstemplate_general_vb_default_sort'],
				'orderby'			=>		'meta_value_num',
				'order'				=>		'DESC'
			)
		);
		$return_string .=  '<div class="vb_master_wrapper">';
			foreach($vb_master_pages as $vb_master_page){
				
				$members_string = get_post_meta($vb_master_page->ID,'vb_nickname',true);
				if($members_string == '')
					$members_string = 'followers';
					
				$followers_count = $wpdb->get_row('SELECT count(*) as followers FROM '.$wpdb->prefix.'vb_follow WHERE  `post_id` = '.$vb_master_page->ID.'');
				$return_string .=  '
					
					<div class="vb_master_single">
						<a href="'.get_permalink($vb_master_page->ID).'" >
							<div class="master-page-thumb">';
							
								if (has_post_thumbnail($vb_master_page->ID)){
									$return_string .=  get_the_post_thumbnail( $vb_master_page->ID,'thumbnail');
								}
								else {
									$return_string .= '<img width="150" src="'. plugins_url( 'public/assets/images/default_profile_pic.jpg' , __DIR__ ) .'">';
								}
								
				$return_string .= '
								<span>'.$vb_master_page->post_title.'</span>'.
							'</div>
							 <div class="vb-master-info">'
							 	.$followers_count->followers.' '.$members_string.
							 '</div>
						</a>
					</div>
					';
			
			}
		
		
		$return_string .= '<div  id="vb-master-pagination" >
								<div class="nav-master-previous">'.get_next_posts_link( 'More Pages' ).'</div>
							</div>';
		$return_string .=  '</div>';
		wp_reset_query();
		return $return_string;
		
	}
	
	public function get_create_page(){
		
		
		$return_string = '';
		$args = array(
			'hide_empty'    => false,
		); 
		$all_types = get_terms('vb_genre',$args);
		
		$return_string.= '<div class="vb_page_create"><ul class="createpage">';
		foreach($all_types as $this_type){
			$return_string.= '
				<li  id="'.$this_type->term_id.'">
					<a href="#">
						<span class="for-img"><img src="'.get_option('z_taxonomy_image'.$this_type->term_id).'" alt="icon"></span>
						<h3>'.$this_type->name.'</h3>
						<p>'.$this_type->description.'</p>
					</a>
				</li>
			';
		}
		$return_string.='</ul></div>
		
		
		<div id="popthisup" style="display:none;" >
			<span class="button b-close"><span>close</span></span>
				<div id="page-create-wizard">
					<h1> Name Your Page </h1>
					<div class="vbCreate">
						
						<input type="text" required name="VbFirstStep" id="VbFirstStep" class="" >
						<button class="VbFirstStepBtn"> Create </button>
					</div>
					<div style="display:none;" class="vbNotify"></div>
				</div>
		</div>
		';
		
		return $return_string;
	}
	
	public function VbCreatePage(){
		global $current_user;
		global $wpdb;
		
		extract($_POST);
		if ( is_user_logged_in() ) {
			
		
			if ( !empty( $current_user->roles ) && is_array( $current_user->roles ) ) {
				 $allowed_vbpost_authors  =  get_option('settingstemplate_settings');
				 $this_role = $current_user->roles[0];
				
				 if($allowed_vbpost_authors['settingstemplate_general_vb_allowed_roles_'.$this_role] == $this_role){
				 
				 	$role = get_role( $this_role );
			 	    $role->add_cap( 'upload_files' );
			 	    
					if($pagename == '')
						return; 
						
					$vbpost_id = wp_insert_post(
										array(
										
											'post_type'		=>		'vbsocial',
											'post_status'	=>		'publish',
											'post_title'	=>		sanitize_text_field($pagename),
							
										)
								);
					
					$this->update_activity($vbpost_id,'last_create');
					// init others sorting options too
					update_post_meta($vbpost_id,'last_follow','1');
					update_post_meta($vbpost_id,'last_comment','1');
					
					wp_set_object_terms($vbpost_id,intval($pagetype),'vb_genre');
					
					$current_user_pages = get_user_meta(get_current_user_id(), 'page_admin', true);

					if(is_array($current_user_pages)) 
						$current_user_pages[] = get_the_ID(); 
						
					else
						$current_user_pages = array(get_the_ID());

					
					update_post_meta($vbpost_id,'page_admin',get_current_user_id());
					update_user_meta(get_current_user_id(),'page_admin',$current_user_pages);
					
					
					
					$created_pageid =  get_permalink( $vbpost_id);
					$return_arr =  array( 
									'status'	=> 1,
									'message'	=> $created_pageid
						
									);

						
			 	   
				 } else {
						$return_arr =  array( 
											'status'	=> 2,
											'message'	=> 'You are not allowed to create page'
				
										);
					}
				
				
			}
			
			
		} else {
			$return_arr =  array( 
									'status'	=> 2,
									'message'	=> 'You need to be logged in to create a page.'
						
									);
		}
		echo json_encode($return_arr);
		exit;
	}
	
	public function VbEditPage(){
		
		global $current_user;
		extract($_POST);
		
		if ( is_user_logged_in() ) {
		
			//check if current user is admin of this page 
			if(get_current_user_id() != get_post_meta($pageid,'page_admin',true))
				return ;
				
			if(!isset($pageid)|| ('' == $pageid))
				return;
			$pageid = intval($pageid);
			if ( !empty( $current_user->roles ) && is_array( $current_user->roles ) ) {
				 $allowed_vbpost_authors  =  get_option('settingstemplate_settings');
				 $this_role = $current_user->roles[0];
				
				 if($allowed_vbpost_authors['settingstemplate_general_vb_allowed_roles_'.$this_role] == $this_role){
				 
				 	$role = get_role( $this_role );
			 	    $role->add_cap( 'upload_files' );
						
						switch($tab){

							case '0': // set feature image
								
								if(set_post_thumbnail( $pageid, $propic ))
									echo 1;

							break;
							
							case '1':  //set background image 
								if(update_post_meta( $pageid, 'background_id', $coverpic ))
									echo 1;
							break;
							
							case '2':  //set tagline and description & other info of the vb page
								wp_update_post( array('ID'           => $pageid,'post_content'	=>		wp_kses_post($pagedesc)) );
								update_post_meta( $pageid, 'vb_tagline', $pagetag );
								
							break;
							
							case '3':  // set links 
								if(isset($pagewebsite))
									update_post_meta( $pageid, 'vb_website', $pagewebsite );
								if(isset($pagephone))
									update_post_meta( $pageid, 'vb_phone', $pagephone );
								if(isset($pageinternetcall))
									update_post_meta( $pageid, 'vb_internet_call', $pageinternetcall );
								if(isset($pageim))
									update_post_meta( $pageid, 'vb_im', $pageim );
								if(isset($pageemail))
									update_post_meta( $pageid, 'vb_email', $pageemail );
								if(isset($pagedate))
									update_post_meta( $pageid, 'vb_date', $pagedate );
								
							break;
							
							case 'final':  // set details 
								if(isset($pagenick))
									update_post_meta( $pageid, 'vb_nickname', $pagenick );
								if(isset($pagefname))
									update_post_meta( $pageid, 'vb_phonetic_firstname', $pagefname );
								if(isset($pagelname))
									update_post_meta( $pageid, 'vb_phonetic_lastname', $pagelname );
								if(isset($pagecompany))
									update_post_meta( $pageid, 'vb_company', $pagecompany );
								if(isset($pagejobtitle))
									update_post_meta( $pageid, 'vb_job_title', $pagejobtitle );
								if(isset($pagerelationship))
									update_post_meta( $pageid, 'vb_relationship', $pagerelationship );
								if(isset($pageaddress))
									update_post_meta( $pageid, 'vb_address', $pageaddress );
								
								
							break;
							
							
						}
				 	
				 } 
				
				
			}
			
			
		}
		exit;
	}
	
	function remove_medialibrary_tab($strings) {
        if ( !current_user_can( 'administrator' ) ) {
            unset($strings["mediaLibraryTitle"]);
        return $strings;
        }
        else
        {
            return $strings;
        }
    }
    
    
    function restrict_non_admins(){
		
        if(!current_user_can('administrator')){
            exit;
        }
    }
    
    function assign_templates_tovbposts(){
    	 global $wp;
    	
    	 if ($wp->query_vars["post_type"] == 'vbsocial') {
    	 	require_once(plugin_dir_path( __FILE__ ).'assets/templates/template-vbsocial.php');
    	 	exit;
    	 }
    }
	
	// check if current user is admin of the current page 
	function is_vbpage_admin(){
	
		if(!is_user_logged_in()){
			return false;
		}
		
		global $post;
		
		
		if('vbsocial' == $post->post_type) {

			$page_admin_id = get_post_meta($post->ID,'page_admin',true);
			
			if(empty($page_admin_id) ||  $page_admin_id == '') {
				
				return false;
				
			} else {
				  if ($page_admin_id == get_current_user_id()){
					  // current user is admin, provide him special privileges
					  add_action('vbpage_bottom_about_blocks',array($this,'admin_link_to_edit'));	
					  add_action('vbpage_get_edit_box',array($this,'get_edit_box'));	
					  				  
				  }	else {
					  return false;
				  }
			  }
		} 		
		
	
	}
	
	
	// change default tab to upload media
	function media_uplaoder_tabs($tab) {

		return 'uploadFiles';
	}


	function admin_link_to_edit(){
		echo '
			<div class="vb-page-edit-link">
				<span> Edit </span>
			</div>
		';
	}
	
	function follow_page(){
	
		if(!is_user_logged_in())
			return;
			
		$this_user_id = get_current_user_id();
		extract($_POST);
		if($pageid == ''){
			return;
		}
		global $wpdb;
		$success = $wpdb->insert($wpdb->prefix.'vb_follow',
			array(
				'user_id'	=> "$this_user_id",
				'post_id'	=> "$pageid"
			)
		);
		update_post_meta($pageid,'last_follow',time());
		$wpdb->print_error();
		if($success)
			echo 1;
		 exit;
	}
	
	function unfollow_page(){
	
		extract($_POST);
		if($pageid == ''){
			return;
		}
		global $wpdb;
		$success = $wpdb->delete($wpdb->prefix.'vb_follow',
			array(
				'user_id'	=> get_current_user_id(),
				'post_id'	=> $pageid
			)
		);
		
		if($success)
			echo 1;
		 exit;
	}
	
	function get_edit_box(){ 
		
	?>
	
		<div id="popthisup" style="display:none;" >
			<span class="button b-close"><span style="color:teal">X</span></span>
				<div id="wizard" data-pageid="<?php echo get_the_ID();?>">

					<h1>Profile</h1>
					<div class="vBpropiC">
						<label for="vbProfilePic"> Choose a Profile Photo  </label>
							<?php 
								if (has_post_thumbnail( get_the_ID() ) ): 
									$attach_id = get_post_thumbnail_id( get_the_ID());
									$image_src = wp_get_attachment_image_src($attach_id);
									echo '<img id="'.$attach_id.'" src="'.$image_src[0].'"/>';
								endif;
							?>
						<button class="vbProfilePic"> Upload </button>
					</div>
					
					<h1>Cover</h1>
					<div class="vBprOcoveR">
						<label for="vbCoverPic"> Choose a Cover Photo  </label>
							<?php 
								if ('' != get_post_meta( get_the_ID(), 'background_id', true)  ): 
									$cover_attach_id = get_post_meta( get_the_ID(), 'background_id', true);
									$cover_image_src = wp_get_attachment_image_src($cover_attach_id);
									echo '<img id="'.$cover_attach_id.'" src="'.$cover_image_src[0].'"/>';
								endif;
							?>
						<button class="vbCoverPic"> Upload </button>
					</div>
					
					<h1>Info</h1>
					<div class="vBproInfo">
						
						<label for="vb_page_tag"> Add a Tagline </label>
						<input type="text" name="vb_page_tag" id="vb_page_tag" value="<?php echo get_post_meta(get_the_ID(),'vb_tagline',true); ?>" class="" >
						<label for="vbPageDesc"> Add Description </label>
						<textarea id="vbPageDesc" rows="5" col="15" name="vbPageDesc">
							<?php echo get_post_field( 'post_content', get_the_ID()); ?>
						</textarea>
						
						
						
						
						
					</div>
					
					<h1>Links</h1>
					<div class="vBproLinks">
					
						<label for="vb_page_links"> Add Contact Information </label>
						<input type="text" value="<?php echo get_post_meta(get_the_ID(),'vb_website',true); ?>"placeholder="Website" name="vb_page_website" id="vb_page_website" class="" >
						
						<input type="text" value="<?php echo get_post_meta(get_the_ID(),'vb_im',true); ?>" placeholder="IM" name="vb_page_im" id="vb_page_im" class="" >
						<input type="text" placeholder="Internet Call" value="<?php echo get_post_meta(get_the_ID(),'vb_internet_call',true); ?>" name="vb_page_internetcall" id="vb_page_internetcall" class="" >
						
						<input type="text" value="<?php echo get_post_meta(get_the_ID(),'vb_email',true); ?>" placeholder="Email" name="vb_page_email" id="vb_page_email" class="" >
						<input type="text" value="<?php echo get_post_meta(get_the_ID(),'vb_phone',true); ?>" placeholder="Phone" name="vb_page_phone" id="vb_page_phone" class="" >
						<input type="text" value="<?php echo get_post_meta(get_the_ID(),'vb_date',true); ?>" placeholder="Date" name="vb_page_date" id="vb_page_date" class="" >
						
						
						
					</div>
					<h1>Details</h1>
					<div class="vBproDetails">
						
						<label for="vbPageOthers"> Other Details </label>
						<input type="text" placeholder="Nickname" value="<?php echo get_post_meta(get_the_ID(),'vb_nickname',true); ?>" name="vb_page_nickname" id="vb_page_nickname" class="" >
						
						<input type="text" placeholder="First name" value="<?php echo get_post_meta(get_the_ID(),'vb_phonetic_firstname',true); ?>" name="vb_page_firstname" id="vb_page_firstname" class="" >
						
						<input type="text" value="<?php echo get_post_meta(get_the_ID(),'vb_phonetic_lastname',true); ?>" placeholder="Last Name" name="vb_page_lastname" id="vb_page_lastname" class="" >
						<input type="text" value="<?php echo get_post_meta(get_the_ID(),'vb_company',true); ?>" placeholder="Company" name="vb_page_company" id="vb_page_company" class="" >
						<input type="text" value="<?php echo get_post_meta(get_the_ID(),'vb_job_title',true); ?>" placeholder="Job Title" name="vb_page_jobtitle" id="vb_page_jobtitle" class="" >
						<input type="text" placeholder="Relationship" value="<?php echo get_post_meta(get_the_ID(),'vb_relationship',true); ?>" name="vb_page_relationship" id="vb_page_relationship" class="" >
						<textarea placeholder="Address" id="vb_page_address" rows="5" col="15" name="vb_page_address">
							<?php echo get_post_meta(get_the_ID(),'vb_address',true); ?>
						</textarea>
						
					</div>
					
					
				</div>
				
			</div>
	
	
	<?php
	}
	
	function Youtubesourcetoid($text) {
		$text = preg_replace('~
		    # Match non-linked youtube URL in the wild. (Rev:20130823)
		    https?://         # Required scheme. Either http or https.
		    (?:[0-9A-Z-]+\.)? # Optional subdomain.
		    (?:               # Group host alternatives.
		      youtu\.be/      # Either youtu.be,
		    | youtube         # or youtube.com or
		      (?:-nocookie)?  # youtube-nocookie.com
		      \.com           # followed by
		      \S*             # Allow anything up to VIDEO_ID,
		      [^\w\-\s]       # but char before ID is non-ID char.
		    )                 # End host alternatives.
		    ([\w\-]{11})      # $1: VIDEO_ID is exactly 11 chars.
		    (?=[^\w\-]|$)     # Assert next char is non-ID or EOS.
		    (?!               # Assert URL is not pre-linked.
		      [?=&+%\w.-]*    # Allow URL (query) remainder.
		      (?:             # Group pre-linked alternatives.
		        [\'"][^<>]*>  # Either inside a start tag,
		      | </a>          # or inside <a> element text contents.
		      )               # End recognized pre-linked alts.
		    )                 # End negative lookahead assertion.
		    [?=&+%\w.-]*        # Consume any URL (query) remainder.
		    ~ix', 
		    "$1",
		    $text);
		return $text;
	}
	
	// insert notifications 
	function push_notification($parent,$child,$activity){
		
		global $wpdb;
		$page_followers = $wpdb->get_results('select `user_id` from '.$wpdb->prefix.'vb_follow where `post_id` ='.intval($parent),ARRAY_A);
		$followers_string = '';
		foreach($page_followers as $k => $v){
			$followers_string .= $v['user_id'].',';
		}
		$followers_string = rtrim($followers_string,',');
		$wpdb->insert($wpdb->prefix.'vb_activities',
							array(
								'post_id'	=>	$child,
								'page_id'	=>	intval($parent),
								'activity'	=>  $activity,
								'users'     =>  $followers_string 
							)
					);
	} 
	
	function update_activity($postid,$activity){
	
		update_post_meta($postid,$activity,time());
		update_post_meta($postid,'last_activity',time());
	}
	// publish posts by page admin
	function vb_publish_post(){
		
		global $wpdb;
		extract($_POST);
		
		if(trim($vb_post_title) == '')
			return;
		
		$previewobj = json_decode(stripslashes($previewobj));
	
		if(get_current_user_id() != get_post_meta($master,'page_admin',true))
			return; 
			
		$thispost_id = wp_insert_post(
				array(
				
					'post_type'			=>		'vbblog',
					'post_status'		=>		'publish',
					'post_title'		=>		sanitize_text_field($vb_post_title),
					'post_content'		=>		wp_kses_post($vb_publish_input),
					'post_parent'		=>		intval($master),
	
				)
		);
		
		
		$this->push_notification($master,$thispost_id,'create');
		$this->update_activity($master,'last_create');	
		
		wp_set_object_terms($thispost_id,intval($type),'vb_blog_type');
	
		
		// attach images to post for post category - image
		if(isset($vb_post_attachments) && !empty($vb_post_attachments)) {
			foreach($vb_post_attachments as $key => $value){
			
				// set first attachment as featured image
				if($key == 0)
					set_post_thumbnail( $thispost_id, $value );
					
				wp_update_post(
					array(
						'ID'        	   => $value,
						'post_parent'	   => $thispost_id
					)
				);
		
			}
		}
		
		// check if link is set 
		if(isset($previewobj) && !empty($previewobj)){
		
			update_post_meta($thispost_id,'vb_link_title',$previewobj->title);
			update_post_meta($thispost_id,'vb_link_url',$previewobj->url);
			update_post_meta($thispost_id,'vb_link_desc',$previewobj->description);
			
			// set first image as thumb of the link
			if($previewobj->images != false) {
				$link_images  = explode('|',$previewobj->images);
				update_post_meta($thispost_id,'vb_link_image',$link_images['0']);
			}
			
			// is video link ??
			
			if($previewobj->video == "yes") {
			
				// is youtube video link ?
				if (strpos($previewobj->url, "youtube.com") !== false) {
					
						update_post_meta($thispost_id,'vb_video_id',$previewobj->video_id);
						update_post_meta($thispost_id,'is_youtube_video',1);
				}
				// is vimeo video link ?
				if (strpos($previewobj->url, "vimeo.com") !== false) {
					
						update_post_meta($thispost_id,'vb_video_id',$previewobj->video_id);
						update_post_meta($thispost_id,'is_vimeo_video',1);
				}
				
				$video_term 	= get_term_by( 'name', 'Video', 'vb_blog_type');
				wp_set_object_terms($thispost_id,array(intval($type),intval($video_term->term_id)),'vb_blog_type');
				
			}
			
		}
		if(intval($thispost_id) > 0 ){
			$this->vb_blog_ajax_response($thispost_id);
		}
		exit;
	}
	
	
	function vb_blog_ajax_response($this_post_id){
		$videothumbclass= '';
			// is video ?
			if(get_post_meta($this_post_id,'vb_video_id',true)!= ''){
				
				// is youtube video
				if(get_post_meta($this_post_id,'is_youtube_video',true) == "1")
				$videothumbclass= 'youtubeimg';
				
				// is vimeo video
				if(get_post_meta($this_post_id,'is_vimeo_video',true) == "1")
				$videothumbclass= 'vimeoimg';
				
				$vb_video_src_id = get_post_meta($this_post_id,'vb_video_id',true);
			}
		?>
		
			<div class="vb-blog-post" id="<?php echo $this_post_id; ?>">
			
				<div class="vb-blog-post-content">

					<div class="vb-blog-post-title">
						<?php echo get_post_field( 'post_title', $this_post_id); ?>
						<div class="vb-blog-avatar">
							<?php echo get_avatar( get_post_meta(get_the_ID(),'page_admin',true), '32' ); ?>
						</div>
					</div>

					<div class="vb-blog-post-description">
						<?php echo get_post_field( 'post_content', $this_post_id); ?>
					</div>
					<?php
						// if post type is link ?
						if(get_post_meta($this_post_id,'vb_link_title',true) != ''){
						
							echo '
								<div class="vb-post-thumbhail" id="vb-post-thumbhail">
									<img data-ytid="'.$vb_video_src_id.'" class="'.$videothumbclass.'" src="'.get_post_meta($this_post_id,'vb_link_image',true).'" />';
									if($videothumbclass != ''){
										echo '<span class="play_icon"></span>';
									}
									
							echo '<div class="vb-post-link-title">'
										.get_post_meta($this_post_id,'vb_link_title',true).
									'</div>
									<div class="vb-post-link-url">
										<a target="_blank" href="'.get_post_meta($this_post_id,'vb_link_url',true).'">'
											.get_post_meta($this_post_id,'vb_link_url',true).'
										</a>
									</div>
									<div class="vb-post-link-desc">'
										.get_post_meta($this_post_id,'vb_link_desc',true).
									'</div>
								</div>';
							
						} else {
					?>
					<div class="vb-post-thumbhail">
						<?php 
							echo get_the_post_thumbnail( $this_post_id,'large');
						 ?>
					</div>
					<?php }?>
				</div>
				<div class="vb-comments-container">
					<?php

					$this_post_comments = get_comments(array(
						'post_id' 	=>	$this_post_id,
						'status'	 => 'approve' 
					));

					foreach($this_post_comments as $this_post_comment){ ?>
					
							<div class="vb-blog-single-comment">
								<div class="vb-user-avatar">
									<?php echo get_avatar( $this_post_comment->comment_author_email, '32' ); ?>
								</div>
								<div class="vb-user-comment">
									<span> <?php echo $this_post_comment->comment_author.'</span>  <span class="date"> on '.date("F j, Y, g:i a",strtotime($this_post_comment->comment_date)) ?> </span>
									<?php echo '<p>'.$this_post_comment->comment_content.'</p>'; ?>
								</div>
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
						<input type="hidden" value="<?php echo $this_post_id; ?>" name="subpostid">
						<input type="submit" value="add comment" class="vb-add-comment" name="add-comment">
					</form>
				</div>
			</div>
		
		<?php
	}
	
	
	function generate_link_preview(){
		require_once('textCrawler.php');
		exit;
	}
	
	function highlight_urls(){
		require_once('highlightUrls.php');
		exit;
	}
	
	function 	vb_add_comment(){
		$return = array();
		$comment_post_ID = isset($_POST['subpostid']) ? (int) $_POST['subpostid'] : 0;
		
		
		$post = get_post($comment_post_ID);

		if ( empty( $post->comment_status ) ) {
	
			$return = array(
					'success'	=>	0,
					'mesage'	=>	'post doesnt exists'
				);
		}


		$status = get_post_status($post);

		$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
		$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
		$comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
		$comment_content      = ( isset($_POST['vb_comment_input']) ) ? trim($_POST['vb_comment_input']) : null;

		// If the user is logged in
		$user = wp_get_current_user();
		if ( $user->exists() ) {
			if ( empty( $user->display_name ) )
				$user->display_name=$user->user_login;
			$comment_author       = wp_slash( $user->display_name );
			$comment_author_email = wp_slash( $user->user_email );
			$comment_author_url   = wp_slash( $user->user_url );
	
		} else {
			if ( get_option('comment_registration') || 'private' == $status )
				$return = array(
					'success'	=>	0,
					'mesage'	=>	'Sorry, you must be logged in to post a comment.'
				);
		}

		$comment_type = '';

		if ( get_option('require_name_email') && !$user->exists() ) {
			if ( 6 > strlen($comment_author_email) || '' == $comment_author )
				wp_die( __('<strong>ERROR</strong>: please fill the required fields (name, email).') );
			elseif ( !is_email($comment_author_email))
				wp_die( __('<strong>ERROR</strong>: please enter a valid email address.') );
		}

		if ( '' == $comment_content )
			$return = array(
					'success'	=>	0,
					'message'	=>	'<strong>ERROR</strong>: please type a comment.'
				);
		
		if(empty($return)) {
			$comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

			$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

			
			$comment_id = wp_new_comment( $commentdata );
			
			
			if($comment_id) {
				$this_post_parent = get_post_ancestors($_POST['subpostid']);
				$this->update_activity($this_post_parent[0],'last_comment'); 
				$comment_status = wp_get_comment_status( $comment_id );
		
				if ( $comment_status == "approved" ) {
					
					$this->push_notification($this_post_parent[0],$_POST['subpostid'],'comment');
				 	$return = array(
				 		'success'	=> 1,
				 		'message'	=>	$this->get_comment_html($comment_id)
				 	);
				} else {
					$return = array(
				 		'success'	=> 2,
				 		'message'	=>	'Comment successfully posted, it will be visible once approved by admin !'
				 	);
				}
			}
		}
		
		echo json_encode($return);
		exit;
	}
	
	function get_comment_html($comment_id){ 
		$this_post_comment = get_comment(intval($comment_id));
		$return_str = '
			<div class="vb-blog-single-comment" id="'.$this_post_comment->comment_ID.'">
				<div class="vb-user-avatar">
					'.get_avatar( $this_post_comment->comment_author_email, '32' ).'
				</div>
				<div class="vb-user-comment">
					<span> '.$this_post_comment->comment_author.'</span>  <span class="date"> on '.date("F j, Y, g:i a",strtotime($this_post_comment->comment_date)).' </span>
					<p>'.$this_post_comment->comment_content.'</p>
				</div>			
			</div>
		';
		return $return_str;
	}
	
	function remove_notification(){
		global $wpdb;
		extract($_POST);
		
		$all_users = $wpdb->get_row('SELECT `users` FROM `vb_vb_activities` WHERE `id` = ' .$noticeid);
		
		if(($all_users !==false) && !empty($all_users)){
			
			$all_users = explode(',',$all_users->users);
			
			if(($key = array_search(get_current_user_id(), $all_users)) !== false) {
				unset($all_users[$key]);
			}
			
			$all_users = implode(',',$all_users);
			
			$delete_notice = $wpdb->query('UPDATE  `vb_vb_activities` SET `users` = "'.$all_users.'" WHERE `id` = '.$noticeid);
			if ($delete_notice !== false){
				echo 1;
			}
			
			
		}
		
		
		exit;
		
		
		
		
	}
	
	
}

