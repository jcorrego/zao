<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Callback for deactivatin plugins for the mobile version
function eos_dp_mobile_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	$active_plugins = eos_dp_active_plugins();
	$plugins = eos_dp_get_plugins();
	$mobile = eos_dp_get_option( 'eos_dp_mobile' );
	wp_nonce_field( 'eos_dp_mobile_setts', 'eos_dp_mobile_setts' );
	eos_dp_alert_plain_permalink();
	eos_dp_navigation();
	?>
	<section id="eos-dp-by-archive-section" class="eos-dp-section">
		<h2><?php _e( 'Uncheck the plugins that you want to disable everywhere for the mobile version.','eos-dp' ); ?></h2>
		<p><?php _e( 'The plugins you uncheck here will always be disabled on mobile devices, no matter which pages and what you set on other options','eos-dp' ); ?></p>
		<p><?php _e( 'Be sure you have a server cache plugin that distinguishes between mobile and desktop.','eos-dp' ); ?></p>
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
				$checked = $mobile && in_array( $p,$mobile ) ? '' : ' checked';
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
					<input id="eos-dp-mobile-<?php echo $n + 1; ?>" class="eos-dp-mobile" title="<?php printf( __( 'Activate/deactivate %s everywhere on mobile','eos-dp' ),esc_attr( $plugin_name ) ); ?>" data-path="<?php echo $p; ?>" type="checkbox"<?php echo $checked; ?> />
				</div>
				<?php
				++$n;
			}
		}
		?>
		</div>
		<div>
		<?php 
		if( !defined( 'EOS_SCFM_PLUGIN_BASE_NAME' ) ){
			$slug = 'specific-content-for-mobile';
			if ( !is_dir( WP_PLUGIN_DIR . '/specific-content-for-mobile' ) ) {
				$action = 'install-plugin';
				$url = wp_nonce_url(
					add_query_arg(
						array(
							'action' => $action,
							'plugin' => $slug
						),
						admin_url( 'update.php' )
					),
					'install-plugin_'.$slug
				);				
				$msg = __( 'If you need to disable plugins only on specific pages for mobile, you should also install "Specific Content For Mobile"','eos-dp' );
				$msg .= sprintf( __( '%sInstall Specific Content For Mobile%s','eos-dp' ),' <a class="button" href="'.$url.'" target="_blank">','</a>' );
			}
			else{
				
				$plugin = 'specific-content-for-mobile/specific-content-for-mobile.php';
				$action = 'activate';
				$url = sprintf( admin_url( 'plugins.php?action='.$action.'&plugin=%s&plugin_status=all&paged=1&s' ),$plugin );
				$url = wp_nonce_url( $url,$action.'-plugin_'.$plugin );
				$msg = __( 'If you need to disable plugins on mobile only on specific pages, you should activate "Specific Content For Mobile"','eos-dp' );
				$msg .= sprintf( __( '%sActivate Specific Content For Mobile%s','eos-dp' ),' <a class="button" href="'.$url.'" target="_blank">','</a>' );
			}		
			?>
			<p><?php echo wp_kses( $msg,array( 'a' => array( 'class' => array(),'href' => array(),'target' => array() ) ) ); ?></p>
			<?php
		}
		?>
		</div>
		<div class="eos-dp-for-developers" style="margin-top:32px">
			<h2><?php _e( 'For Developers','eos-dp' ); ?></h2>
			<p><?php _e( 'If you are a developer, you can use the constant "EOS_{PLUGIN-SLUG}_ACTIVE" to check if a plugin is globally active but disabled only on mobile.','eos-dp' ); ?></p>
			<p><?php _e( 'Replace {PLUGIN-SLUG} with the slug of the plugin you want to check','eos-dp' ); ?></p>
			<?php 
			$const = $active_plugins[absint( rand( 0,count( $active_plugins ) - 1 ) )];
			$plugin_name = strtoupper( str_replace( '-',' ',dirname( $const ) ) );
			$const = str_replace( '-','_',strtoupper( str_replace( '.php','',basename( $const ) ) ) );
			?>
			<p><?php printf( __( "Let's suppose you disable %s on mobile","eos-dp" ),$plugin_name ); ?></p>
			<p><?php _e( 'In this case you can use the following snippet:','eos-dp' ); ?></p>
<pre>
if( wp_is_mobile() ){
	if( defined( '<?php echo esc_attr( $const ); ?>' ) && <?php echo esc_attr( $const ); ?> ){ 	
		//Whatever you need to fire
		add_shortcode( 'shortcode_example','__return_false' ); //e.g if you need to remove orphan shortcodes on mobile
	}
}
</pre>
			<p><?php _e( 'Here you have the complete list of constants you need:','eos-dp' ); ?></p>
			<ul>
			<?php
			foreach( $active_plugins as $p => $const ){
				$plugin_name = ucwords( str_replace( '-',' ',dirname( $const ) ) );
				$const = str_replace( '-','_',strtoupper( str_replace( '.php','',basename( $const ) ) ) );
				?>
				<li><?php echo '<strong>'.$plugin_name.'</strong>:    EOS_'.$const.'_ACTIVE';?></li>
				<?php
			}
			?>
			</ul>
		</div>
		<?php eos_dp_save_button(); ?>
	</section>
	<?php
}