<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Callback for deactivatin plugins for the search version
function eos_dp_search_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	$active_plugins = eos_dp_active_plugins();
	$plugins = eos_dp_get_plugins();
	$search = eos_dp_get_option( 'eos_dp_search' );
	wp_nonce_field( 'eos_dp_search_setts', 'eos_dp_search_setts' );
	eos_dp_alert_plain_permalink();
	eos_dp_navigation();
	?>
	<section id="eos-dp-by-archive-section" class="eos-dp-section">
		<h2><?php _e( 'Uncheck the plugins that you want to disable on the search results page.','eos-dp' ); ?></h2>
		<div style="margin:32px 0">
			<span><span class="dashicons dashicons-plugins-checked"></span> <?php _e( 'Plugin enabled','eos-dp' ); ?></span><span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
			<span><span class="dashicons dashicons-admin-plugins" style="opacity:0.4"></span> <?php _e( 'Plugin disabled','eos-dp' ); ?></span>
		</div>		
		<div id="eos-dp-wrp" style="margin-top:32px">
		<?php
		$n = 0;
		foreach( $active_plugins as $p ){
			if( isset( $plugins[$p] ) ){
				$plugin_name = strtoupper( str_replace( '-',' ',dirname( $p ) ) ); 
				$checked = $search && in_array( $p,$search ) ? '' : ' checked';
				$details_url = add_query_arg( 
					array( 
						'tab' => 'plugin-information',
						'plugin' => dirname( $p ),
						'TB_iframe' => true,
						'eos_dp' => $p,
						'eos_dp_info' => 'true'
					),
					admin_url( 'plugin-install.php' )
				);				
				?>
				<div class="eos-dp-mb-16">
					<span><a title="<?php _e( 'View details','eos-dp' ); ?>" target="_blank" class="eos-dp-no-decoration" href="<?php echo esc_url( $details_url ); ?>"><?php echo esc_html( $plugin_name ); ?></a></span>
					<input id="eos-dp-search-<?php echo $n + 1; ?>" class="eos-dp-search" title="<?php printf( __( 'Activate/deactivate %s everywhere on search','eos-dp' ),esc_attr( $plugin_name ) ); ?>" data-path="<?php echo $p; ?>" type="checkbox"<?php echo $checked; ?> />
				</div>
				<?php
				++$n;
			}
		}
		?>
		</div>
		<?php eos_dp_save_button(); ?>
	</section>
	<?php
}