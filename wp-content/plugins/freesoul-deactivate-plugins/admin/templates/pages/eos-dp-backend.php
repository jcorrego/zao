<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Callback for deactivate in admin
function eos_dp_admin_callback(){
	if( !current_user_can( 'activate_plugins' ) && function_exists( 'eos_dp_active_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	$opts = eos_dp_get_option( 'eos_dp_general_setts' );
	$no_admin_topbar = $opts && isset( $opts['menu_in_topbar'] ) && 'false' === $opts['menu_in_topbar'];
	$active_plugins = eos_dp_active_plugins();
	global $menu;
	global $submenu;
	$labels = array();
	foreach( $menu as $arr ){
		$labels[$arr[2]] = $arr[0];
	}
	$adminSetts = eos_dp_get_option( 'eos_dp_admin_setts' );
	$adminTheme = eos_dp_get_option( 'eos_dp_admin_theme' );
	$n = count( $active_plugins );
	$values_string = '';
	$values = explode( ',',$values_string );
	wp_nonce_field( 'eos_dp_admin_setts','eos_dp_admin_setts' );
	eos_dp_navigation();
	$admin_pages = array();
	$admin_pages_key = array();
	$printedUrls = array();
	?>
	<section id="eos-dp-by-admin-section" class="eos-dp-section">
		<div class="eos-dp-margin-top-32">
			<h2><?php esc_html_e( 'Simplified top bar admin navigation (only for who can manage options).','eos-dp' ); ?></h2>
			<div>
				<p><?php _e( 'Disabling plugins in the back-end will remove some menu items from the admin navigation.','eos-dp' ); ?></p>
				<input type="checkbox" id="menu_in_topbar" name="menu_in_topbar"<?php echo $no_admin_topbar ? '' : ' checked'; ?> />
				<span><?php _e( 'Check it to show a simplified admin navigation in the top bar when the main navigation misses some menu items.','eos-dp' ); ?></span>
			</div>
			<?php if( defined( 'EOS_DP_BETA_VERSION' ) && EOS_DP_BETA_VERSION && !$no_admin_topbar ) { ?>
			<div class="left">
				<span id="eos-dp-get-screen" class="dashicons dashicons-external"></span>
			</div>
			<?php } ?>			
		</div>
		<div class="eos-dp-margin-top-32">
			<h2><?php esc_html_e( 'Uncheck the plugins that you want to deactivate for each admin page.','eos-dp' ); ?></h2>
			<div id="eos-dp-wrp">
				<?php
				$row = 1;
				?>
				<?php eos_dp_plugin_names_orientation_ctrl(); ?>
				<table id="eos-dp-setts"  data-zoom="1">
				<?php eos_dp_table_head();
				foreach( $submenu as $submenu_item ){
					$submenu_item = array_values( $submenu_item );
					$keyArr = $submenu_item[0];
					$key = $keyArr[2];
					$admin_pages_key = array();
					$labels_name = isset( $labels[$key] ) ? preg_replace('/[0-9]+/', '',wp_strip_all_tags( $labels[$key] ) ) : preg_replace('/[0-9]+/', '',wp_strip_all_tags( $keyArr[0] ) );
					$menu_item_url = false !== strpos( $keyArr[2],'.php' ) ?  admin_url( $keyArr[2] ) : add_query_arg( 'page', $keyArr[2] ,admin_url( 'admin.php' ) );
					if( 
						false !== strpos( $menu_item_url,'eos_dp_' )
					){
						continue;
					}
					?>
					<tr style="border-style:none">
						<td style="border-style:none" colspan="<?php echo count( $active_plugins ); ?>">
							<a class="eos-dp-admin-main-menu-link" href="<?php echo esc_url( $menu_item_url ); ?>" target="_blank">
								<h4><?php echo $labels_name; ?></h4>
							</a>
						</td>
					</tr>
					<?php
					foreach( $submenu_item as $menu_item ){
						$active = false;
						$menu_item[2] = str_replace( '&amp;','&',$menu_item[2] );
						if( false === filter_var( $menu_item[2],FILTER_VALIDATE_URL ) ){
							$menu_item_url = false !== strpos( $menu_item[2],'.php' ) ?  admin_url( $menu_item[2] ) : add_query_arg( 'page',$menu_item[2],admin_url( 'admin.php' ) );
						}
						else{
							$menu_item_url = $menu_item[2];
						}
						$values = isset( $adminSetts[$menu_item[2]] ) ? explode( ',',$adminSetts[$menu_item[2]] ) : array_fill( 0,count( $active_plugins ),',' );
						if( '' !== $labels_name && false === strpos( $menu_item[2],'eos_dp' ) ){
							$title = wp_strip_all_tags( reset( $menu_item ) );
							$title = preg_replace( '!\d+!','',$title );
							$admin_page = array( 'title' =>$title,'page' => $menu_item[2],'url' => $menu_item_url );
							$admin_pages_key[] = $admin_page;
						?>
						<tr class="eos-dp-admin-row eos-dp-post-row<?php echo $menu_item_url && '' !== $menu_item_url && in_array( $menu_item_url,$printedUrls ) ? ' eos-dp-duplicated-url' : '';echo false !== strpos( $menu_item_url,'plugins.php' ) ? ' eos-dp-not-active eos-dp-not-allowed' : ''; ?>" data-admin="<?php echo $menu_item[2]; ?>">
							<td class="eos-dp-post-name-wrp">
								<span class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate all plugins in %s','eos-dp' ),esc_attr( $labels_name ) ); ?>" data-row="<?php echo esc_attr( $row ); ?>" class="eos-dp-global-chk-row" type="checkbox" /></span>
								
								<a class="eos-dp-title" href="<?php echo esc_url( $menu_item_url ); ?>" target="_blank"><?php echo $title; ?></a>
								<div class="eos-dp-actions">
									<a title="<?php _e( 'View page loading plugins according the saved options','eos-dp' ); ?>" class="eos-dp-view" href="<?php echo esc_url( $menu_item_url ); ?>" target="_blank"><span class="dashicons dashicons-visibility"></span></a>
									<a title="<?php _e( 'Copy this row settings','eos-dp' ); ?>" class="eos-dp-copy" href="#"><span class="dashicons dashicons-admin-page"></span></a>
									<a title="<?php _e( 'Paste last copied row settings','eos-dp' ); ?>" class="eos-dp-paste" href="#"><span class="dashicons dashicons-category"></span></a>
									<?php do_action( 'eos_dp_archive_action_buttons' ); ?>
								</div>						
							</td>
							<?php
							$printedUrls[] = $menu_item_url;
							for( $k = 0;$k < $n;++$k ){
								$extra_class = isset( $active_plugins[$k] ) && $active_plugins[$k] === EOS_DP_PLUGIN_BASE_NAME ? ' eos-hidden' : '';
								?>
								<td class="center<?php echo !in_array( $active_plugins[$k],$values ) ? ' eos-dp-active' : '';echo $extra_class; ?>">
									<div class="eos-dp-td-chk-wrp eos-dp-td-admin-chk-wrp">
										<input class="eos-dp-row-<?php echo $row; ?> eos-dp-col-<?php echo $k + 1; ?> eos-dp-col-<?php echo ( $k + 1 ).'-'.esc_attr( $menu_item[2] ); ?>" data-checked="<?php echo in_array( $active_plugins[$k],$values ) ? 'checked' : 'not-checked'; ?>" type="checkbox"<?php echo in_array( $active_plugins[$k],$values ) ? ' checked' : ''; ?> />
									</div>
								</td>				
								<?php	
							}
							?>
							<td class="center<?php echo !isset( $adminTheme[$menu_item[2]] ) || $adminTheme[$menu_item[2]] ? ' eos-dp-active' : ''; ?>">
								<div class="eos-dp-td-chk-wrp eos-dp-td-admin-chk-wrp">
									<input class="eos-dp-row-theme eos-dp-col-<?php echo $k + 1; ?> eos-dp-col-<?php echo ( $k + 1 ).'-'.esc_attr( $menu_item[2] ); ?>" data-checked="checked" type="checkbox" checked />
								</div>
							</td>						
						</tr>
						<?php
							$admin_pages[$key] = array( 'title' => $labels_name,'submenu' => $admin_pages_key );
						}
						++$row;
					}
				}
				?>
				</table>
			</div>
		</div>
		<script>
		var eos_dp_admin_pages = '<?php echo str_replace( "&quot;","\"",esc_js( json_encode( $admin_pages ) ) ); ?>';
		</script>
		<?php eos_dp_save_button(); ?>
	</section>
	<?php
}