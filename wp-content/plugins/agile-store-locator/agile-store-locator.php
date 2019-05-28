<?php

/**
 *
 * @link              https://agilestorelocator.com
 * @since             1.0.0
 * @package           AgileStoreLocator
 *
 * @wordpress-plugin
 * Plugin Name:       Agile Store Locator
 * Plugin URI:        https://agilestorelocator.com
 * Description:       Agile Store Locator is a WordPress Store Finder/Locator Plugin that renders stores list with Location markers on Google Maps v3, it supports GeoLocation and render nearest stores with direction over google maps.
 * Version:           1.1.4
 * Author:            AGILELOGIX
 * Author URI:        https://agilestorelocator.com/
 * License:           Copyrights 2018
 * License URI:       
 * Text Domain:       asl_locator
 * Domain Path:       /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'AGILESTORELOCATOR_URL_PATH', plugin_dir_url( __FILE__ ) );
define( 'AGILESTORELOCATOR_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'AGILESTORELOCATOR_PREFIX', $wpdb->prefix."asl_" );
define( 'AGILESTORELOCATOR_PLUGIN_BASE', dirname( plugin_basename( __FILE__ ) ) );
define( 'AGILESTORELOCATOR_CVERSION', "1.1.4" );

global $wp_version;

if (version_compare($wp_version, '3.3.2', '<=')) {
	//die('version not supported');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-agile-store-locator-activator.php
 */
function activate_AgileStoreLocator() {
	require_once AGILESTORELOCATOR_PLUGIN_PATH . 'includes/class-agile-store-locator-activator.php';
	AgileStoreLocator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-agile-store-locator-deactivator.php
 */
function deactivate_AgileStoreLocator() {
	require_once AGILESTORELOCATOR_PLUGIN_PATH . 'includes/class-agile-store-locator-deactivator.php';
	AgileStoreLocator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_AgileStoreLocator' );
register_deactivation_hook( __FILE__, 'deactivate_AgileStoreLocator' );



add_action( 'upgrader_process_complete', 'asl_upgrate_process',10, 2);


function asl_upgrate_process( $upgrader_object, $options ) {

  	//Store Timing
	require_once AGILESTORELOCATOR_PLUGIN_PATH . 'includes/class-agile-store-locator-helper.php';
	AgileStoreLocator_Helper::fix_backward_compatible();
}



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require AGILESTORELOCATOR_PLUGIN_PATH . 'includes/class-agile-store-locator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_AgileStoreLocator() {

	$plugin = new AgileStoreLocator();
	$plugin->run();
}

run_AgileStoreLocator();
