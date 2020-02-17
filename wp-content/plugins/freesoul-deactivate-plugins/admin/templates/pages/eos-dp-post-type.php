<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Callback for deactivate by post type settings page
function eos_dp_by_post_type_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	$active_plugins = eos_dp_active_plugins();
	$plugins_table = eos_dp_plugins_table();
	$n = count( $active_plugins );
	$values_string = '';
	$values = explode( ',',$values_string );
	wp_nonce_field( 'eos_dp_pt_setts', 'eos_dp_pt_setts' );
	eos_dp_alert_plain_permalink();
	eos_dp_navigation();
	?>
	<section id="eos-dp-by-post-type-section" class="eos-dp-section">
		<?php require_once EOS_DP_PLUGIN_DIR.'/admin/templates/partials/eos-dp-post-types-legend.php'; ?>
		<div id="eos-dp-wrp">
			<?php eos_dp_plugin_names_orientation_ctrl(); ?>
			<table id="eos-dp-setts"  data-zoom="1">
			<?php 
			eos_dp_table_head();
			$row = 0;
			foreach( $plugins_table as $post_type => $plugins ){
				if( !in_array( $post_type,array( 'attachment' ) ) ){
					$active = false;
					$labsObj = get_post_type_object( $post_type );
					if( isset( $labsObj->labels ) ){
						$labs = $labsObj->labels;
						$labs_name = isset( $labs->name ) ? $labs->name : false;
						$singles = add_query_arg( 'eos_dp_post_type',$post_type,admin_url( 'admin.php?page=eos_dp_menu' ) );
					?>
					<tr class="eos-dp-post-type eos-dp-post-row" data-post-type="<?php echo $post_type; ?>">
						<td class="eos-dp-post-name-wrp">
							<span class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate all plugins in %s','eos-dp' ),esc_attr( $labs_name ) ); ?>" class="eos-dp-global-chk-row" type="checkbox" /></span>
							<span class="eos-dp-not-active-wrp<?php echo $plugins[0] == '1' ? ' eos-dp-priority-active' : ''; ?> eos-dp-priority-post-type-wrp"><input title="<?php printf( __( 'If activated the Single %s Settings will be ignored.','eos-dp' ),esc_attr( $labs_name ) ); ?>" class="eos-dp-priority-post-type" type="checkbox" /></span>
							<span class="eos-dp-title"><a style="color:inherit;text-decoration:none" href="<?php echo $singles; ?>"><?php echo esc_html( $labs_name ); ?><span class="dashicons dashicons-admin-links"></span></a></span>
							<span class="<?php echo isset( $plugins[2] ) && $plugins[2] == '1' ? 'eos-dp-default-active' : ''; ?> eos-dp-default-post-type-wrp">
								<span class="eos-dp-default-chk-wrp">
									<input title="<?php printf( __( 'If activated the Single %s Settings will have this row settings as default.','eos-dp' ),esc_attr( $labs_name ) ); ?>" class="eos-dp-default-post-type" type="checkbox"<?php echo isset( $plugins[2] ) && $plugins[2] == '1' ? ' checked' : ''; ?>/>
									<span></span>
								</span>
							</span>
							<span class="eos-dp-x-space"></span>
							<div class="eos-dp-actions">
								<a title="<?php _e( 'Copy this row settings','eos-dp' ); ?>" class="eos-dp-copy" href="#"><span class="dashicons dashicons-admin-page"></span></a>
								<a title="<?php _e( 'Paste last copied row settings','eos-dp' ); ?>" class="eos-dp-paste" href="#"><span class="dashicons dashicons-category"></span></a>
								<?php do_action( 'eos_dp_action_buttons' ); ?>
							</div>							
						</td>
					<?php
					for( $k = 0;$k < $n;++$k ){
						$active = isset( $active_plugins[$k] ) && !in_array( $active_plugins[$k],explode( ',',$plugins[1] ) ) ? true : false;
						?>
						<td class="center<?php echo $active ? ' eos-dp-active' : ''; ?>">
							<div class="eos-dp-td-chk-wrp eos-dp-td-post-type-chk-wrp">
								<input class="eos-dp-row-<?php echo $row; ?> eos-dp-col-<?php echo $k + 1; ?> eos-dp-col-<?php echo ( $k + 1 ).'-'.$post_type; ?>" type="checkbox"<?php echo $active ? ' checked' : ''; ?> />
							</div>
						</td>
					<?php
					}
					?>
					</tr>
					<?php
					}
				}
				++$row;
			}
			?>
			</table>
		</div>
		<?php eos_dp_save_button(); ?>
	</section>
	<?php
}