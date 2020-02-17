<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Callback for deactivate by archive settings page
function eos_dp_help_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	eos_dp_alert_plain_permalink();
	eos_dp_navigation();
	$dir = is_rtl() ? 'left' : 'right';
	require_once EOS_DP_PLUGIN_DIR.'/admin/templates/partials/eos-dp-help-navigation.php';
	eos_dp_help_navigation();	
	if( isset( $_GET['tab'] ) && function_exists( 'eos_dp_help_'.sanitize_key( $_GET['tab'] ).'_section' ) ){
		call_user_func( 'eos_dp_help_'.sanitize_key( $_GET['tab'] ).'_section' );
	}
	?>

	<script>
	var support_button = document.getElementById('eos-dp-search-on-support-button'),
		support_input = document.getElementById('eos-dp-search-on-support-input');
	support_button.addEventListener('click',function(){
		support_button.href = 'https://wordpress.org/search/freesoul+deactivate+plugins+' + support_input.value.replace(' ','+').split('?')[0] + '?forum=1';
	});
	</script>
	<?php
}

//Flowchart section
function eos_dp_help_flowchart_section(){
	?>
	<section class="eos-dp-section">
		<div id="eos-dp-flow-chart">
			<h2><?php _e( 'How Freesoul Deactivate Plugins disables plugins according to your settings.','eos-dp' ); ?></h2>
			<img width="1221" height="1069" usemap="#eos_dp_flow_map" src="<?php echo EOS_DP_PLUGIN_URL.'/img/flow-chart.png'; ?>" />
			<map id="eos_dp_flow_map" name="eos_dp_flow_map">
				<area target="_blank" alt="<?php esc_attr_e( 'Mobile Settings','eos-dp' ); ?>" title="<?php esc_attr_e( 'Mobile Settings','eos-dp' ); ?>" href="<?php echo admin_url( 'admin.php?page=eos_dp_mobile','eos-dp' ); ?>" coords="140,640,299,696" shape="rect">
				<area target="_blank" alt="<?php esc_attr_e( 'Search Settings','eos-dp' ); ?>" title="<?php esc_attr_e( 'Search Settings','eos-dp' ); ?>" href="<?php echo admin_url( 'admin.php?page=eos_dp_search','eos-dp' ); ?>" coords="294,1067,147,1010" shape="rect">
				<area target="_blank" alt="<?php esc_attr_e( 'Archives Settings','eos-dp' ); ?>" title="<?php esc_attr_e( 'Archives Settings','eos-dp' ); ?>" href="<?php echo admin_url( 'admin.php?page=eos_dp_by_archive','eos-dp' ); ?>" coords="794,285,634,225" shape="rect">
				<area target="_blank" alt="<?php esc_attr_e( 'Terms Archives Settings','eos-dp' ); ?>" title="<?php esc_attr_e( 'Terms Archives Settings','eos-dp' ); ?>" href="<?php echo admin_url( 'admin.php?page=eos_dp_by_term_archive','eos-dp' ); ?>" coords="981,225,1141,282" shape="rect">
				<area target="_blank" alt="<?php esc_attr_e( 'Singles Settings','eos-dp' ); ?>" title="<?php esc_attr_e( 'Singles Settings','eos-dp' ); ?>" href="<?php echo admin_url( 'admin.php?page=eos_dp_menu','eos-dp' ); ?>" coords="719,534,880,597" shape="rect">
				<area target="_blank" alt="<?php esc_attr_e( 'Post Types Settings','eos-dp' ); ?>" title="<?php esc_attr_e( 'Post Types Settings','eos-dp' ); ?>" href="<?php echo admin_url( 'admin.php?page=eos_dp_by_post_type','eos-dp' ); ?>" coords="702,801,866,861" shape="rect">
				<area target="_blank" alt="<?php esc_attr_e( 'Custom URLs Settings','eos-dp' ); ?>" title="<?php esc_attr_e( 'Custom URLs Settings','eos-dp' ); ?>" href="<?php echo admin_url( 'admin.php?page=eos_dp_url','eos-dp' ); ?>" coords="578,1066,417,1009" shape="rect">
			</map>
		</div>
	</section>
	<?php
}