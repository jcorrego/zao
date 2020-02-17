<?php
if( !defined( 'WP_UNINSTALL_PLUGIN') ){
    die;
}
delete_site_option( 'eos_dp_activation_info' );
delete_site_option( 'eos_dp_new_plugin_activated' );
delete_site_option( 'eos_dp_general_setts' );
delete_site_option( 'eos_dp_archives' );
delete_site_option( 'eos_dp_search' );
delete_site_option( 'eos_dp_mobile' );
delete_site_option( 'eos_dp_by_url' );
delete_site_option( 'eos_post_types_plugins' );
delete_site_option( 'eos_dp_opts' );
delete_site_option( 'eos_dp_admin_theme' );
delete_site_option( 'eos_dp_admin_menu' );
delete_site_option( 'eos_dp_admin_setts' );
delete_site_option( 'eos_dp_admin_url_theme' );
delete_site_option( 'eos_dp_by_admin_url' );
global $wpdb;
$clean_metaboxes = $wpdb->delete( 'wp_postmeta', array( 'meta_key' => '_eos_deactive_plugins_key' ) );