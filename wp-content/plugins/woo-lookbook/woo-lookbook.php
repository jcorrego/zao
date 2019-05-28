<?php
/*
Plugin Name: WooCommerce Lookbook
Plugin URI: https://villatheme.com/extensions/woocommerce-lookbook/
Description: Allows you to create realistic lookbooks of your products. Help your customers visualize what they purchase from you.
Version: 1.0.5
Author: VillaTheme
Author URI: http://villatheme.com
Copyright 2018 VillaTheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'WOO_F_LOOKBOOK_VERSION', '1.0.5' );
/**
 * Detect plugin. For use on Front End only.
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce-lookbook/woocommerce-lookbook.php' ) ) {
	return;
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo-lookbook" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "define.php";
	require_once $init_file;
}

/**
 * Class WOO_LOOKBOOK
 */
class WOO_F_LOOKBOOK {
	public function __construct() {

		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		add_action( 'admin_notices', array( $this, 'global_note' ) );
		add_action( 'init', array( $this, 'init' ) );

	}

	public function init() {
		add_image_size( 'lookbook', 400, 400, false );
	}

	/**
	 * Notify if WooCommerce is not activated
	 */
	function global_note() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			?>
			<div id="message" class="error">
				<p><?php _e( 'Please install and active WooCommerce. WooCommerce Multi Currency is going to working.', 'woo-lookbook' ); ?></p>
			</div>
			<?php
		}

	}

	/**
	 * When active plugin Function will be call
	 */
	public function install() {
		global $wp_version;
		if ( version_compare( $wp_version, "4.4", "<" ) ) {
			deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
			wp_die( "This plugin requires WordPress version 2.9 or higher." );
		}
	}

	/**
	 * When deactive function will be call
	 */
	public function uninstall() {

	}
}

new WOO_F_LOOKBOOK();