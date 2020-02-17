<?php
/*
Plugin Name: Freesoul Deactivate Plugins
Plugin URI: https://freesoul-deactivate-plugins.com/
Description: Freesoul Deactivate Plugins allows you to disable specific plugins on specific pages. Useful to reach excellent performance and for support in problem-solving even when many plugins are active.
Author: Jose Mortellaro
Author URI: https://josemortellaro.com/
Text Domain: eos-dp
Domain Path: /languages/
Version: 1.7.1
*/
/*  Copyright 2019 Jose Mortellaro (email: info at josemortellaro.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Definitions
define( 'EOS_DP_VERSION','1.7.1' );
define( 'EOS_DP_NEED_UPDATE_MU',true );
define( 'EOS_DP_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'EOS_DP_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'EOS_DP_PLUGIN_BASE_NAME', untrailingslashit( plugin_basename( __FILE__ ) ) );
//Actions triggered after plugin activation or after a new site of a multisite installation is created
function eos_dp_initialize_plugin( $networkwide ){
    if( is_multisite() && $networkwide ){
		wp_die( sprintf( __( "Freesoul Deactivate Plugins can't be activated networkwide, but only on each single site. %s%s%s","eos-dp" ),'<div><a class="button" href="'.admin_url( 'network/plugins.php' ).'">',__( 'Back to plugins','eos-dp' ),'</a></div>' ) );
	}
	require EOS_DP_PLUGIN_DIR.'/plugin-activation.php';
}
register_activation_hook( __FILE__, 'eos_dp_initialize_plugin' );
//Actions triggered after plugin deaactivation
function eos_dp_deactivate_plugin(){
	if( !is_multisite() && file_exists( WPMU_PLUGIN_DIR.'/eos-deactivate-plugins.php' ) ){
		unlink( WPMU_PLUGIN_DIR.'/eos-deactivate-plugins.php' );
	}
}
register_deactivation_hook( __FILE__, 'eos_dp_deactivate_plugin' );

//It loads plugin translation files
function eos_load_dp_plugin_textdomain(){
	load_plugin_textdomain( 'eos-dp', FALSE,EOS_DP_PLUGIN_DIR . '/languages/' );
}
add_action( 'admin_init', 'eos_load_dp_plugin_textdomain' );
//Filter function to read plugin translation files
function eos_dp_load_translation_file( $mofile, $domain ) {
	if ( 'eos-dp' === $domain ) {
		$loc = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$mofile = EOS_DP_PLUGIN_DIR . '/languages/freesoul-deactivate-plugins-' . $loc . '.mo';
	}
	return $mofile;
}
if( is_admin() ){
	//Filter translation files
	add_filter( 'load_textdomain_mofile', 'eos_dp_load_translation_file',99,2 ); //loads plugin translation files
	require_once EOS_DP_PLUGIN_DIR.'/admin/eos-dp-helper.php';
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		require EOS_DP_PLUGIN_DIR. '/admin/eos-dp-ajax.php'; //file including all ajax requests functions
	}
	else{
		require EOS_DP_PLUGIN_DIR . '/inc/eos-dp-metaboxes.php'; //file including the needed functions for the metaboxes
		require EOS_DP_PLUGIN_DIR . '/admin/eos-dp-admin.php'; //file including the functions for back-end
		if( eos_dp_is_fdp_page() ){
			remove_all_actions( 'parse_request' );
			add_action( 'admin_init','eos_dp_remove_other_admin_notices' );
			add_action( 'admin_enqueue_scripts', 'eos_dp_scripts',10 ); //we enqueue the scripts for back-end
		}
		add_action( 'admin_enqueue_scripts', 'eos_dp_style',10 ); //we enqueue the style for back-end
	}
}