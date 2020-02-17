<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//It displays the table head for the plugin filters
function eos_dp_table_head( $reset = false ){
	$plugins = eos_dp_get_plugins();
	$active_plugins = eos_dp_active_plugins();
	?>
	<tr id="eos-dp-table-head">
		<th style="vertical-align:top;background:transparent;border-style:none;text-align:initial;pointer-events:none">
			<div style="margin-bottom:12px">
				<span class="eos-dp-active-wrp"><input style="width:20px;height:20px" type="checkbox" /></span>
				<span class="eos-dp-legend-txt"><?php _e( 'Plugin active','eos-dp' ); ?></span>
			</div>
			<div>
				<span class="eos-dp-not-active-wrp"><input style="width:20px;height:20px" type="checkbox" checked/></span>
				<span class="eos-dp-legend-txt"><?php _e( 'Plugin not active','eos-dp' ); ?></span>
			</div>
			<?php if( $reset ): ?>
			<div style="margin-top:8px;margin-bottom:16px">
				<span style="margin:0;font-size:20px" title="<?php __( 'Restore last saved options','eos-dp' ); ?>" class="dashicons dashicons-image-rotate"></span><span class="eos-dp-legend-txt"><?php _e( 'Back to last saved settings','eos-dp' ); ?></span>
			</div>
			<?php endif; ?>
		</th>
		<?php
		$n = 0;
		$fdp = array();
		foreach( $active_plugins as $p ){
			if( isset( $plugins[$p] ) ){
				$plugin = $plugins[$p];
				$plugin_name = strtoupper( str_replace( '-',' ',dirname( $p ) ) );
				$plugin_name_short = substr( $plugin_name,0,28 );
				$plugin_name_short = $plugin_name === $plugin_name_short ? $plugin_name : $plugin_name_short.' ...';
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
				<th class="eos-dp-name-th">
					<div>
						<div id="eos-dp-plugin-name-<?php echo $n + 1; ?>" class="eos-dp-plugin-name" title="<?php echo esc_attr( $plugin_name ); ?>" data-path="<?php echo $p; ?>">
							<span><a title="<?php printf( esc_attr__( 'View details of %s','eos-dp' ),esc_attr( $plugin_name ) ); ?>" href="<?php echo esc_url( $details_url ); ?>" target="_blank"><?php echo esc_html( $plugin_name_short ); ?></a></span>
						</div>
						<div class="eos-dp-global-chk-col-wrp">
							<div class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate %s everywhere','eos-dp' ),esc_attr( $plugin_name ) ); ?>" data-col="<?php echo $n + 1; ?>" class="eos-dp-global-chk-col" type="checkbox" /></div>
							<?php if( $reset ): ?>
							<div class="eos-dp-reset-col" data-col="<?php echo $n + 1; ?>"><span title="<?php printf( __( 'Restore last saved options for %s everywhere','eos-dp' ),esc_attr( $plugin_name ) ); ?>" class="dashicons dashicons-image-rotate"></span></div>
							<?php endif; ?>
						</div>
					</div>
				</th>
				<?php
				++$n;
			}
		}
		do_action( 'eos_dp_after_table_head_columns' ); ?>
	</tr>
	<?php
}