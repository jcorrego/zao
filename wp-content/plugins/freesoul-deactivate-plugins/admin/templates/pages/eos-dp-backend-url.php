<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Callback for deactivating by admin URL settings page
function eos_dp_by_admin_url_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	$active_plugins = eos_dp_active_plugins();
	$n = count( $active_plugins );
	wp_nonce_field( 'eos_dp_admin_url_setts', 'eos_dp_admin_url_setts' );
	eos_dp_alert_plain_permalink();
	eos_dp_navigation();
	$urls = eos_dp_get_option( 'eos_dp_by_admin_url' );
	if( !$urls || '' === $urls ){
		$urls = array( array( 'url' => '','plugins' => '' ) );
	}
	$urls[] = array( '','' );
	$urlsN = count( $urls );
	$home_url = get_home_url();
	$adminTheme = eos_dp_get_option( 'eos_dp_admin_url_theme' );
	?>
	<section id="eos-dp-by-url-section" class="eos-dp-section">
		<h2><?php esc_html_e( 'Uncheck the plugins you want to disable in the back-end depending on the URL','eos-dp' ); ?></h2>	
		<h2><span class="dashicons dashicons-warning"></span><?php _e( 'It will work only for BACK-END','eos-dp' ); ?></h2>	
		<div id="eos-dp-wrp">		
			<?php eos_dp_plugin_names_orientation_ctrl(); ?>
			<div class="eos-dp-explanation">
				<p><?php _e( 'Use the star "*" as replacement of groups of characters.','eos-dp' ); ?></p>
				<p><?php printf( esc_html__( 'E.g. %s/wp-admin*example/ will match URLs as %s/wp-admin/an-example/, %s/wp-admin/another-example/...','eos-dp' ),$home_url,$home_url,$home_url ); ?></p>
				<p><?php printf( esc_html__( 'You can use these options to disable plugins by URL query arguments. E.g. *?example-paramameter=true* will match URLS as %s/wp-admin?example-paramameter=true, %s/wp-admin/page-example/?example-paramameter=true...','eos-dp' ),$home_url,$home_url ); ?></p>
			</div>
			<?php eos_dp_plugin_names_orientation_ctrl(); ?>
			<table id="eos-dp-setts"  data-zoom="1">
				<tbody class="eos-dp-urls">
				<?php 
				eos_dp_table_head();
				$row = 0;
				foreach( $urls as $urlA ){
				?>
					<tr class="eos-dp-url eos-dp-post-row<?php echo $row + 1 === $urlsN ? ' eos-hidden' : ''; ?>">
						<td class="eos-dp-post-name-wrp">
							<span class="eos-dp-not-active-wrp"><input title="<?php _e( 'Activate/deactivate all plugins for this URL','eos-dp' ); ?>" class="eos-dp-global-chk-row" type="checkbox" /></span>
							<span class="dashicons dashicons-move" title="<?php _e( 'Move it up to assign higher priority','eos-dp' ); ?>"></span>
							<input type="text" class="eos-dp-url-input" placeholder="<?php printf( __( 'Write here the URL','eos-dp' ),$home_url ); ?>" value="<?php echo isset( $urlA['url'] ) ? esc_attr( $urlA['url'] ) : ''; ?>" />
							<span class="eos-dp-delete-url dashicons dashicons-trash hover" title="<?php _e( 'Delete','eos-dp' ); ?>"></span>
							<span class="eos-dp-x-space"></span>
						</td>
					<?php
					for( $k = 0;$k < $n;++$k ){
						if( !isset( $urlA['plugins'] ) ){
							$active = true;
						}
						else{
							$active = isset( $active_plugins[$k] ) && !in_array( $active_plugins[$k],explode( ',',$urlA['plugins'] ) ) ? true : false;
						}
						?>
						<td class="center<?php echo $active ? ' eos-dp-active' : ''; ?>">
							<div class="eos-dp-td-chk-wrp eos-dp-td-url-chk-wrp">
								<input class="eos-dp-row-<?php echo $row; ?> eos-dp-col-<?php echo $k + 1; ?>" type="checkbox"<?php echo $active ? ' checked' : ''; ?> />
							</div>
						</td>
					<?php } ?>
					</tr>
				<?php ++$row; } ?>
					<tr>
						<td colspan="<?php echo $n + 2; ?>" id="eos-dp-url-actions" style="border:none;padding:0">
							<button id="eos-dp-add-url" style="margin-top:16px"><?php _e( 'Add URL','eos-dp' ); ?></button>
						</td>	
					</tr>
				</tbody>
			</table>			
		</div>
		<script>
			var eos_dp_admin_pages = '<?php echo str_replace( "&quot;","\"",esc_js( json_encode( $admin_pages ) ) ); ?>';
		</script>		
		<?php eos_dp_save_button(); ?>
	</section>
	<?php
}