<?php
/**
 * vbsocial.
 * @package vbsocial_Admin
 * 
 */
class vbsocial_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the vb screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $vb_screen_hook_suffix = null;

	/**
	 * Initialize the vb by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $vb_slug from public vb class.
		 */
		 
		$vb = vbsocial::get_instance();
		$this->vb_slug = $vb->get_vb_slug();

		
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_filter('wp_handle_upload_prefilter',array( $this, 'custom_upload_filter') );
		add_action('init',array( $this, 'review_capabilities') );
		
		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_vb_admin_menu' ) );
		
		// Add an action link pointing to the options page.
		$vb_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->vb_slug . '.php' );
		add_filter( 'vb_action_links_' . $vb_basename, array( $this, 'add_action_links' ) );
		
		add_action( 'admin_head', array( $this,'vb_post_css' ));
		add_action( 'add_meta_boxes', array( $this,'vb_add_custom_box' ));
		add_action( 'save_post', array( $this,'save_vb_page_details' ));
		add_action( 'save_post', array( $this,'save_vb_blog_details' )); 
		
		add_action('admin_menu', array( $this,'hide_media_buttons'));
		
		

	}
	
	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles($hook) {
		
		if ( ! isset( $this->vb_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->vb_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->vb_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), vbsocial::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->vb_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		
		if ( $screen->post_type == 'vbblog' || $screen->post_type == 'vbsocial') {
			
      		wp_enqueue_script('vb-admin-js', plugins_url( 'assets/js/admin.js', __FILE__ )	);
      		
        	if ( $screen->post_type == 'vbblog'){
        		wp_enqueue_media();
        	}
		
			if ( $screen->post_type == 'vbsocial' ) {
			
				wp_enqueue_script( 'jquery-ui-datepicker' );
				
				wp_enqueue_style( 'vb-datepicker.css', plugins_url( 'assets/css/vb-datepicker.css', __FILE__ ) );
			}
		 }

	}

	/**
	 * Register the administration menu for this vb into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_vb_admin_menu() {

		/*
		 * Add a settings page for this vb to the Settings menu.
		 *
		 */
		
		$this->vb_screen_hook_suffix = add_submenu_page(
				'edit.php?post_type=vbsocial',
			__( 'Settings', $this->vb_slug ),
			__( 'Settings', $this->vb_slug ),
			'manage_options',
			$this->vb_slug,
			array( $this, 'display_vb_admin_page' )
		);

	}

	/**
	 * Render the settings page for this vb.
	 *
	 * @since    1.0.0
	 */
	public function display_vb_admin_page() {
		require_once(plugin_dir_path( __FILE__ ).'admin.php');
		if( class_exists('Vb_Setting')){
			$VbSettings = new Vb_Setting();
			$VbSettings ->settings_page();
		}
		 		
	}

	/**
	 * Add settings action link to the vbs page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->vb_slug ) . '">' . __( 'Settings', $this->vb_slug ) . '</a>'
			),
			$links
		);

	}

	function vb_add_custom_box() {

		$screens = array( 'vbsocial' );

		foreach ( $screens as $screen ) {

		    add_meta_box(
		        'vbplugin_sectionid',
		        __( 'Further Details', 'vbsocial-pages' ),
		        array($this,'vb_generate_detail_box'),
		        $screen
		    );
		}
		
		$screens = array( 'vbblog' );

		foreach ( $screens as $screen ) {

		    add_meta_box(
		        'vbblog_sectionid',
		        __( 'Link Details', 'vbsocial-pages' ),
		        array($this,'vb_blog_detail_box'),
		        $screen
		    );
		}
	}
	
	function vb_blog_detail_box($post){
	
		 wp_nonce_field( 'vb_blog_detail_box', 'vb_blog_detail_box_nonce' );
		 $value = get_post_meta( $post->ID);
	
	  	 extract($value);
		 echo '
				<label for="vb_link_title">
					Link Title <br />
					<input id="vb_link_title" type="text" size="36" name="vb_link_title" value="'.$vb_link_title['0'].'" />
				</label>
				<br />
				<label for="vb_link_url">
					Link Url <br />
					<input id="vb_link_url" type="text" size="36" name="vb_link_url" value="'.$vb_link_url['0'].'" />
				</label>
				<br />
				<label for="vb_link_image">
					Link Image Thumb <br />
					<input id="vb_link_image" type="text" size="36" name="vb_link_image" value="'.$vb_link_image['0'].'" />
				</label>
				<br />
				<label for="vb_link_desc">
					Link Description  <br />
					<textarea id="vb_link_desc" rows="10"  type="text" name="vb_link_desc" >'.$vb_link_desc['0'].'</textarea>
				</label>
				<br />
				
		  ';
	
	}
	function vb_generate_detail_box( $post ) {

  
	  wp_nonce_field( 'vb_generate_detail_box', 'vb_generate_detail_box_nonce' );


	  $value = get_post_meta( $post->ID);
	  extract($value);
	  echo '<table class="vb_meta_table"><tr><td>';
	  echo '<label for="vb_nickname">';
		   _e( "Add a Nickname", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_nickname" name="vb_nickname" value="' . esc_attr( $vb_nickname['0'] ) . '" size="25" /></td>';
	  
	   echo '<td><label for="vb_phonetic_lastname">';
		   _e( "Add a Phonetic Last Name", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_phonetic_lastname" name="vb_phonetic_lastname" value="' . esc_attr($vb_phonetic_lastname['0'] ) . '" size="25" /></td></tr>';
	  
	  echo '<tr><td><label for="vb_phonetic_firstname">';
		   _e( "Add a Phonetic First Name", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_phonetic_firstname" name="vb_phonetic_firstname" value="' . esc_attr( $vb_phonetic_firstname['0'] ) . '" size="25" /></td>';
	  
	  echo '<td><label for="vb_company">';
		   _e( "Add a Company", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_company" name="vb_company" value="' . esc_attr( $vb_company['0'] ) . '" size="25" /></td></td>';
	  
	  echo '<tr><td><label for="vb_job_title">';
		   _e( "Add a Job Title", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_job_title" name="vb_job_title" value="' . esc_attr( $vb_job_title['0'] ) . '" size="25" /></td>';
	  
	  echo '<td><label for="vb_email">';
		   _e( "Add an email", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_email" name="vb_email" value="' . esc_attr( $vb_email['0'] ) . '" size="25" /></td></tr>';
	  
	  echo '<tr><td><label for="vb_phone">';
		   _e( "Add a Phone Number", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_phone" name="vb_phone" value="' . esc_attr( $vb_phone['0'] ) . '" size="25" /></td>';
	  
	  echo '<td><label for="vb_address">';
		   _e( "Add an Address", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<textarea id="vb_address" name="vb_address" >'.esc_attr( $vb_address['0'] ).'</textarea></td></tr>';
	  
	  echo '<tr><td><label for="vb_website">';
		   _e( "Add a Web Site", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_website" name="vb_website" value="' . esc_attr( $vb_website['0'] ) . '" size="25" /></td>';
	  
	  echo '<td><label for="vb_relationship">';
		   _e( "Add a Relationship", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_relationship" name="vb_relationship" value="' . esc_attr( $vb_relationship['0'] ) . '" size="25" /></td></tr>';
	  
	  echo '<tr><td><label for="vb_im">';
		   _e( "Add an IM", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_im" name="vb_im" value="' . esc_attr( $vb_im['0'] ) . '" size="25" /></td>';
	  
	  echo '<td><label for="vb_internet_call">';
		   _e( "Add an Internet Call", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_internet_call" name="vb_internet_call" value="' . esc_attr( $vb_internet_call['0'] ) . '" size="25" /></td></tr>';
	  
	  echo '<tr><td><label for="vb_date">';
		   _e( "Add a Date", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<input type="text" id="vb_date" class="vb_date" name="vb_date" value="' . esc_attr( $vb_date['0'] ) . '" size="25" /></td>';
	  
	  echo '<td><label for="vb_tagline">';
		   _e( "Add a Tagline", 'vbsocial-pages' );
	  echo '</label> ';
	  echo '<textarea id="vb_tagline" name="vb_tagline" >'.esc_attr( $vb_tagline['0'] ).'</textarea></td></tr>';
	  
	  echo '</table>';
	
	}
	
	
	function save_vb_page_details( $post_id ) {

	  if ( ! isset( $_POST['vb_generate_detail_box_nonce'] ) )
		return $post_id;

	  $nonce = $_POST['vb_generate_detail_box_nonce'];

	 
	  if ( ! wp_verify_nonce( $nonce, 'vb_generate_detail_box' ) )
		  return $post_id;

	 
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return $post_id;

	  
	  if ( 'vbsocial' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) )
		    return $post_id;
	  
	  } else {

		if ( ! current_user_can( 'edit_post', $post_id ) )
		    return $post_id;
	  }

	  foreach($_POST as $k => $v){
		if (0 === strpos($k, 'vb_')) {
		
			//$v = sanitize_text_field( $v );
			update_post_meta( $post_id, $k,$v );
		}
	  	
	  }
	  
	}
	
	function save_vb_blog_details( $post_id ) {
	

	  if ( ! isset( $_POST['vb_blog_detail_box_nonce'] ) )
		return $post_id;

	  $nonce = $_POST['vb_blog_detail_box_nonce'];

	 
	  if ( ! wp_verify_nonce( $nonce, 'vb_blog_detail_box' ) )
		  return $post_id;

	 
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return $post_id;

	  
	  if ( 'vbblog' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) )
		    return $post_id;
	  
	  } else {

		if ( ! current_user_can( 'edit_post', $post_id ) )
		    return $post_id;
	  }

	  foreach($_POST as $k => $v){
		if (0 === strpos($k, 'vb_')) {
		
			//$v = sanitize_text_field( $v );
			update_post_meta( $post_id, $k,$v );
		}
	  	
	  }
	  
	}
	
	function vb_post_css(){
		$screen = get_current_screen();
		if($screen->post_type == 'vbsocial'){ ?>
			
			<style>
				.vb_meta_table  label {
					float: left;
					margin-left: 20px;
					width: 196px;
				}
			</style>
	<?php
		}
		
	}
	
	function hide_media_buttons() {
		
      if(!current_user_can('edit_posts')){
           $screen_info = pathinfo($_SERVER[REQUEST_URI]);
           
           if($screen_info['filename'] == 'upload'|| $screen_info['filename'] == 'media-new'){
           		die('Unauthorized Access !');
           }
           global $menu;
           unset($menu[10]); // hide the Media Library & hide the add new button, user is only allowed to add media from front end
          
      }
	}
	
	

	function custom_upload_filter( $file ){
		
		$vb_admin_settings  = get_option('settingstemplate_settings');
		$filetype = wp_check_filetype($file['name']);
		$allowed_size = $vb_admin_settings['settingstemplate_upload_settings_vb_image_size'] * 1024*1024;
		
		if($allowed_size < $file['size'])
			$file['error'] = 'size limit exceeded';
		
		if($vb_admin_settings['settingstemplate_upload_settings_vb_allowed_img_types_'.$filetype['ext']] != $filetype['ext'] )
			$file['error'] = 'file type not allowed';
		
		return $file['error'];
	}
	
	function review_capabilities(){
		
		$vb_admin_settings  = get_option('settingstemplate_settings'); 
		
		if($vb_admin_settings['settingstemplate_general_vb_allowed_roles_administrator'] != 'administrator'){
			$role = get_role( 'administrator' );
  			$role->remove_cap( 'upload_files' );
		}
		if($vb_admin_settings['settingstemplate_general_vb_allowed_roles_editor'] != 'editor'){
			$role = get_role( 'editor' );
  			$role->remove_cap( 'upload_files' );
		}
		if($vb_admin_settings['settingstemplate_general_vb_allowed_roles_author'] != 'author'){
			$role = get_role( 'author' );
  			$role->remove_cap( 'upload_files' );
		}
		if($vb_admin_settings['settingstemplate_general_vb_allowed_roles_contributor'] != 'contributor'){
			$role = get_role( 'contributor' );
  			$role->remove_cap( 'upload_files' );
		}
		if($vb_admin_settings['settingstemplate_general_vb_allowed_roles_subscriber'] != 'subscriber'){
			$role = get_role( 'subscriber' );
  			$role->remove_cap( 'upload_files' );
		}
			
		
		
	}

	

}



































