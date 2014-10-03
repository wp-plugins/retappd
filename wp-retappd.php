<?php
/**
   Plugin Name: Retappd
   Plugin URI: http://jaydensibert.com/retappd-wordpress-plugin
   Description: Displays recent Untappd checkins for a provided user. This plugin can be used to display a user's most recent checkins in a post or a page by the use of a shortcode. You can choose what pieces of Untappd checkin information that you would like to display in the plugin's settings. Example usage of the shortcode is as follows: [retappd].
   Version: 1.2
   Author: Jayden Sibert
   Author URI: http://www.jaydensibert.com
   License: GPLv2 or later
*/

// Turn debug on
define('WP_DEBUG', true);

// Don't allow direct access to the plugin's absolute path
defined('ABSPATH') or die("Na son!");

// Add uninstall script to run when the plugin is removed
register_uninstall_hook( __FILE__, 'wp-retappd-uninstall.php' );

// Include Retappd Functions
require('wp-retappd-functions.php');

// Gets the page that the user is currently on
$retappd_current_page = $_SERVER["REQUEST_URI"];

// Add settings link to the WordPress plugin page
$retappd_plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$retappd_plugin", 'retappd_plugin_settings_link' );

// Add shortcode for retappd
add_shortcode('retappd', 'retappd');

// Add the admin setting to the Wordpress admin page
add_action( 'admin_menu', 'my_retappd_menu' );

// Add a custom donate link to the WordPress plugin page
if ( ! function_exists( 'retappd_plugin_meta' ) ) :
	function retappd_plugin_meta( $retappd_links, $retappd_file ) { // add 'Donate' links to plugin meta row
		if ( strpos( $retappd_file, 'wp-retappd.php' ) !== false ) {
			// Get some configuration settings
			$retappd_settings = retappd_get_settings();
			parse_str($retappd_settings);
			$retappd_links = array_merge( $retappd_links, array( '<a href="' . urldecode($donate_link) . '" target="_blank">Donate</a>' ) );
		}
		return $retappd_links;
	}
	add_filter( 'plugin_row_meta', 'retappd_plugin_meta', 10, 2 );
endif;
?>