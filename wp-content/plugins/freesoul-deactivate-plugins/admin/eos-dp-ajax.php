<?php
/**
 *  File required by options.php that includes all the functions needed for admin ajax requests
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action("wp_ajax_eos_dp_save_settings", "eos_dp_save_settings");
//Saves activation/deactivation settings for each post
function eos_dp_save_settings(){
	define( 'EOS_DP_SAVING_OPZIOND',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce, 'eos_dp_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['eos_dp_setts'] ) && !empty( $_POST['eos_dp_setts'] ) ){
		foreach( $_POST['eos_dp_setts'] as $post_id => $opts ){
			$post_id = absint( str_replace( 'post_id_','',$post_id ) );
			if( $post_id > 0 ){
				update_post_meta( $post_id,'_eos_deactive_plugins_key',sanitize_text_field( $opts ) );
			}
		}
		$opts = $_POST['eos_dp_setts'];
		if( isset( $opts['post_type'] ) ){
			$post_types_matrix = get_site_option( 'eos_post_types_plugins' );
			$post_type = sanitize_key( $opts['post_type'] );
			$post_types_matrix_pt = $post_types_matrix[$post_type];
			if( isset( $post_types_matrix_pt[3] ) && is_array( $post_types_matrix_pt[3] ) && !empty( $post_types_matrix_pt[3] ) ){
				if( isset( $opts['ids_locked'] ) ){
					foreach( $opts['ids_locked'] as $id_locked ){


						if( !in_array( $id_locked,$post_types_matrix_pt[3] ) ){
							$post_types_matrix_pt[3] = array_merge( $post_types_matrix_pt[3],array( $id_locked ) );
						}
					}
				}
				if( isset( $opts['ids_unlocked'] ) ){
					foreach( $opts['ids_unlocked'] as $id_unlocked ){
						if( in_array( $id_unlocked,$post_types_matrix_pt[3] ) ){
							$post_types_matrix_pt[3] = array_diff( $post_types_matrix_pt[3],array( $id_unlocked ) );
						}
					}
				}
			}
			else{
				$post_types_matrix_pt[3] = $opts['ids_locked'];
				$post_types_matrix[$opts['post_type']] = $post_types_matrix_pt;
			}
			$post_types_matrix[$post_type] = $post_types_matrix_pt;
			eos_dp_update_option( 'eos_post_types_plugins',$post_types_matrix );
			
			
		}	
		eos_dp_update_option( 'eos_dp_new_plugin_activated',false );
	}
	echo 1;
	die();
	exit;
}
add_action("wp_ajax_eos_dp_save_archives_settings", "eos_dp_save_archives_settings");
//Saves activation/deactivation settings for each archive
function eos_dp_save_archives_settings(){
	define( 'EOS_DP_SAVING_OPZIOND',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce, 'eos_dp_arch_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['eos_dp_setts_archives'] ) && !empty( $_POST['eos_dp_setts_archives'] ) ){
		$archiveSetts = $_POST['eos_dp_setts_archives'];
		foreach( $archiveSetts as $k => $v ){
			unset( $archiveSetts[$k] );
			$kArr = explode( '//',$k );
			if( isset( $kArr[1] ) ){
				$k = rtrim( $kArr[1],'/' );
			}
			$k = str_replace( '/','__',$k );
			$archiveSetts[sanitize_key( $k )] = sanitize_text_field( $v );
		}
		$currentOpts = eos_dp_get_option( 'eos_dp_archives' );
		if( null !== $currentOpts && !empty( $currentOpts ) && null !== $archiveSetts && !empty( $archiveSetts ) ){
			$archiveSetts = array_merge( $currentOpts,$archiveSetts );
		}
		eos_dp_update_option( 'eos_dp_archives',$archiveSetts );
		eos_dp_update_option( 'eos_dp_new_plugin_activated',false );
	}
	echo 1;
	die();
	exit;
}
add_action("wp_ajax_eos_dp_save_post_type_settings", "eos_dp_save_post_type_settings");
//Saves activation/deactivation settings for each post type
function eos_dp_save_post_type_settings(){
	define( 'EOS_DP_SAVING_OPZIONS',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce, 'eos_dp_pt_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['eos_dp_pt_setts'] ) && !empty( $_POST['eos_dp_pt_setts'] ) ){
		$opts = get_site_option( 'eos_post_types_plugins' );
		if( !is_array( $opts ) ){
			$opts = array();
		}		
		$eos_dp_pt_setts = json_decode( str_replace( '\\','',$_POST['eos_dp_pt_setts'] ),true );
		foreach( $eos_dp_pt_setts as $post_type => $data ){
			$opts_post_type = $opts[sanitize_key( $post_type )];
			$locked_ids = isset( $opts_post_type[3] ) ? $opts_post_type[3] : array();
			$opts[sanitize_key( $post_type )] = array( absint( $data[0] ),sanitize_text_field( $data[1] ),absint( $data[2] ),$locked_ids );
		}
		eos_dp_update_option( 'eos_post_types_plugins',$opts );
		eos_dp_update_option( 'eos_dp_new_plugin_activated',false );
		echo 1;
		die();
	}
	echo 0;
	die();
}

add_action("wp_ajax_eos_dp_save_url_settings", "eos_dp_save_url_settings");
//Saves activation/deactivation settings by URL
function eos_dp_save_url_settings(){
	define( 'EOS_DP_SAVING_OPZIONS',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce, 'eos_dp_url_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['eos_dp_url_setts'] ) && !empty( $_POST['eos_dp_url_setts'] ) ){
		$opts = get_site_option( 'eos_dp_by_url' );
		$eos_url_setts = json_decode( str_replace( '\\','',$_POST['eos_dp_url_setts'] ),true );
		$n = 0;
		foreach( $eos_url_setts as $arr ){
			$eos_dp_url_setts[$n] = array(
					'url' => sanitize_text_field( $arr['url'] ),
					'plugins' => sanitize_text_field( $arr['plugins'] )
				);
			++$n;
		}
		eos_dp_update_option( 'eos_dp_by_url',$eos_dp_url_setts );
		eos_dp_update_option( 'eos_dp_new_plugin_activated',false );
		echo 1;
		die();
	}
	echo 0;
	die();
}
add_action("wp_ajax_eos_dp_save_mobile_settings", "eos_dp_save_mobile_settings");
//Saves activation/deactivation settings for mobile
function eos_dp_save_mobile_settings(){
	define( 'EOS_DP_SAVING_OPZIONS',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce, 'eos_dp_mobile_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['eos_dp_mobile'] ) && !empty( $_POST['eos_dp_mobile'] ) ){
		$opts = array_filter( explode( ',',sanitize_text_field( $_POST['eos_dp_mobile'] ) ) );
		eos_dp_update_option( 'eos_dp_mobile',$opts );
		echo 1;
		die();
	}
	echo 0;
	die();
}
add_action("wp_ajax_eos_dp_save_search_settings", "eos_dp_save_search_settings");
//Saves activation/deactivation settings for search
function eos_dp_save_search_settings(){
	define( 'EOS_DP_SAVING_OPZIONS',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce, 'eos_dp_search_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['eos_dp_search'] ) && !empty( $_POST['eos_dp_search'] ) ){
		$opts = array_filter( explode( ',',sanitize_text_field( $_POST['eos_dp_search'] ) ) );
		eos_dp_update_option( 'eos_dp_search',$opts );
		echo 1;
		die();
	}
	echo 0;
	die();
}

add_action("wp_ajax_eos_dp_save_admin_url_settings", "eos_dp_save_admin_url_settings");
//Saves activation/deactivation settings by Admin URL
function eos_dp_save_admin_url_settings(){
	define( 'EOS_DP_SAVING_OPZIONS',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce, 'eos_dp_admin_url_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['eos_dp_admin_url_setts'] ) && !empty( $_POST['eos_dp_admin_url_setts'] ) ){
		$opts = get_site_option( 'eos_dp_by_admin_url' );
		$eos_url_setts = json_decode( str_replace( '\\','',$_POST['eos_dp_admin_url_setts'] ),true );
		$n = 0;
		foreach( $eos_url_setts as $arr ){
			$eos_dp_admin_url_setts[$n] = array(
					'url' => sanitize_text_field( $arr['url'] ),
					'plugins' => sanitize_text_field( $arr['plugins'] )
				);
			++$n;
		}
		eos_dp_update_option( 'eos_dp_by_admin_url',$eos_dp_admin_url_setts );
		if( isset( $_POST['theme_activation'] ) ){
			eos_dp_update_option( 'eos_dp_admin_url_theme',json_decode( str_replace( '\\','',sanitize_text_field( $_POST['theme_activation'] ) ),true ) );
		}		
		eos_dp_update_option( 'eos_dp_new_plugin_activated',false );
		echo 1;
		die();
	}
	echo 0;
	die();
}

add_action( 'wp_ajax_eos_dp_save_admin_settings', 'eos_dp_save_admin_settings' );
//Saves admin options
function eos_dp_save_admin_settings(){
	define( 'EOS_DP_SAVING_OPTIONS',true );
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if ( 
		false === $nonce 
		|| ! wp_verify_nonce( $nonce, 'eos_dp_admin_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	$opts = json_decode( str_replace( '\\','',$_POST['eos_dp_admin_setts'] ),true );
	eos_dp_update_option( 'eos_dp_new_plugin_activated',false );
	echo eos_dp_update_option( 'eos_dp_admin_setts',array_map( 'sanitize_text_field',$opts ) );	
	if( isset( $_POST['admin_menus'] ) ){
		eos_dp_update_option( 'eos_dp_admin_menu',sanitize_text_field( $_POST['admin_menus'] ) );
	}
	if( isset( $_POST['theme_activation'] ) ){
		eos_dp_update_option( 'eos_dp_admin_theme',json_decode( str_replace( '\\','',sanitize_text_field( $_POST['theme_activation'] ) ),true ) );
	}
	if( isset( $_POST['menu_in_topbar'] ) ){
		$opts = eos_dp_get_option_array( 'eos_dp_general_setts' );
		$opts['menu_in_topbar'] = sanitize_text_field( $_POST['menu_in_topbar'] );
		eos_dp_update_option( 'eos_dp_general_setts',$opts );
	}
	die();
}

add_action("wp_ajax_eos_dp_save_firing_order", "eos_dp_save_firing_order");
//Saves activation/deactivation settings for search
function eos_dp_save_firing_order(){
	define( 'EOS_DP_SAVING_OPZIONS',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce, 'eos_dp_firing_order_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['eos_dp_plugins'] ) && !empty( $_POST['eos_dp_plugins'] ) ){
		$opts = array_map( 'sanitize_text_field',$_POST['eos_dp_plugins'] );	
		eos_dp_update_option( 'active_plugins',$opts );
		echo 1;
		die();
	}
	echo 0;
	die();
}
add_action("wp_ajax_eos_dp_preview", "eos_dp_preview");
//Prepare the transient for the preview
function eos_dp_preview(){
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	$nonceStr = isset( $_POST['post_id'] ) ? 'eos_dp_setts' : 'eos_dp_arch_setts';
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce,$nonceStr ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	if( isset( $_POST['plugin_path'] ) && !empty( $_POST['plugin_path'] ) ){
		if( isset( $_POST['post_id'] ) && absint( $_POST['post_id'] ) > 0 ){
			set_transient( 'fdp_test_'.sanitize_key( $_POST['post_id'] ),sanitize_text_field( $_POST['plugin_path'] ),60 );
		}
		if( isset( $_POST['post_type'] ) && '' !== $_POST['post_type'] ){
			set_transient( 'fdp_test_'.sanitize_key( $_POST['post_type'] ),sanitize_text_field( $_POST['plugin_path'] ),60 );
		}
		if( isset( $_POST['tax'] ) && '' !== $_POST['tax'] ){
			set_transient( 'fdp_test_'.sanitize_key( $_POST['tax'] ),sanitize_text_field( $_POST['plugin_path'] ),60 );
		}
		if( isset( $_POST['page_speed_insights'] ) && 'true' === $_POST['page_speed_insights'] ){
			set_transient( 'fdp_psi_nonce_'.sanitize_key( $_POST['post_id'],60 ),1000*( absint( time()/1000 ) ) );
		}		
	}
	echo 1;
	die();
	exit;
}
add_action( "wp_ajax_eos_dp_updated_key_for_preview", "eos_dp_updated_key_for_preview" );
//Update the key for preview. Called every 2 minutes
function eos_dp_updated_key_for_preview(){
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if (
		false === $nonce
		|| ! wp_verify_nonce( $nonce,'eos_dp_key' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}

	echo 1000*absint( time()/1000 );
	die();
	exit;
}