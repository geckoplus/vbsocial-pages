<?php

 /**
 * Plugin Name: vBSocial Pages
 * Plugin URI: http://vbsocial.com/
 * Description: Allow your users to create business, personal, recreational pages on your site completely from the user interface.
 * Version: 1.0
 * Author: vBSocial.com
 * Author URI: http://vbsocial.com/
 
 * Tested up to: 3.8
 *

 */
 

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/


require_once( plugin_dir_path( __FILE__ ) . 'public/class-vbsocial-pages.php' );
require_once(plugin_dir_path( __FILE__ ).'/common/background-image.php');
/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'vbsocial', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'vbsocial', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'vbsocial', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin()  ) {
	
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-vbsocial-pages-admin.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/admin/categories-images.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/settings-class.php' );
	add_action( 'plugins_loaded', array( 'vbsocial_Admin', 'get_instance' ) );

}
