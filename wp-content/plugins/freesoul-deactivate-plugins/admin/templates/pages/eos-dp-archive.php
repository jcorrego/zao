<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Callback for deactivate by archive settings page
function eos_dp_by_archive_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	$shop_url = false;
	if( function_exists( 'woocommerce_get_page_id' ) ){
		$shop_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
	}
	$active_plugins = eos_dp_active_plugins();
	$plugins_table = eos_dp_plugins_table();
	$archiveSetts = eos_dp_get_option( 'eos_dp_archives' );
	$n = count( $active_plugins );
	wp_nonce_field( 'eos_dp_arch_setts','eos_dp_arch_setts' );
	wp_nonce_field( 'eos_dp_key', 'eos_dp_key' );
	eos_dp_alert_plain_permalink();
	eos_dp_navigation();
	$filter_home = false;
	if( isset( $_GET['eos_dp_home'] ) && 'true' === $_GET['eos_dp_home'] ){
		$show_on_front = eos_dp_get_option( 'show_on_front' );
		if( 'posts' === $show_on_front ){
			$filter_home = true;
		}
	}
	$page_for_posts = eos_dp_get_option( 'page_for_posts' );
	$show_on_front = eos_dp_get_option( 'show_on_front' );
	?>
	<section id="eos-dp-by-archive-section" class="eos-dp-section">
		<div id="eos-dp-wrp">
			<?php eos_dp_plugin_names_orientation_ctrl(); ?>
			<table id="eos-dp-setts"  data-zoom="1">
			<?php eos_dp_table_head();
			$row = 1;
			$key = '';		
			foreach( $plugins_table as $post_type => $plugins ){
				if( !$filter_home || ( $filter_home && 'post' === $post_type ) ){
					$args = array( 'test_id'=>time(),'fdp_post_type'=>$post_type );		
					$active = false;
					$postTypeObj = get_post_type_object( $post_type );
					if( !is_object( $postTypeObj ) ) continue;
					$labels = get_post_type_labels( get_post_type_object( $post_type ) );
					$labels_name = isset( $labels->name ) ? $labels->name : $post_type;
					$archive_url = get_post_type_archive_link( $post_type );
					
					if( $shop_url !== $archive_url && $post_type !== 'page' && $archive_url && ( $post_type !== 'post' || ( !$page_for_posts && 'posts' === $show_on_front ) ) ):
						$archive_url = remove_query_arg( 'lang',$archive_url );
						$kArr = explode( '//',$archive_url );
						if( isset( $kArr[1] ) ){
							$key = $kArr[1];
						}
						$key = sanitize_key( str_replace( '/','__',rtrim( $key,'/' ) ) );
						$values = isset( $archiveSetts[$key] ) ? explode( ',',$archiveSetts[$key] ) : array_fill( 0,count( $active_plugins ),',' );		
					?>
					<tr class="eos-dp-archive-row eos-dp-post-row" data-post-type="<?php echo $post_type; ?>">
						<td class="eos-dp-post-name-wrp">
							<span class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate all plugins in %s','eos-dp' ),esc_attr( $labels_name ) ); ?>" data-row="<?php echo esc_attr( $row ); ?>" class="eos-dp-global-chk-row" type="checkbox" /></span>
							<span class="eos-dp-reset-row" data-row="<?php echo $row; ?>"><span title="<?php printf( __( 'Restore last saved optons in %s','eos-dp' ),esc_attr( $labels_name ) ); ?>" class="dashicons dashicons-image-rotate"></span></span>
							<span class="eos-dp-title"><?php printf( __( '%s Archive','eos-dp' ),$labels_name ); ?></span>
							<div class="eos-dp-actions">
								<a title="<?php _e( 'View page loading plugins according to the saved options','eos-dp' ); ?>" class="eos-dp-view" href="<?php echo add_query_arg( 'show_disabled_plugins',md5( $_SERVER['REMOTE_ADDR'].( absint( time()/1000 ) ) ),esc_url( $archive_url ) ); ?>" data-href="<?php echo esc_url( $archive_url ); ?>" target="_blank"><span class="dashicons dashicons-visibility"></span></a>
								<?php 
								$themes_list = eos_dp_active_themes_list();
								if( $themes_list ){
								?>
								<a title="<?php _e( 'Select a different Theme ONLY FOR PREVIEW','eos-dp' ); ?>" class="eos-dp-theme-sel"><span class="dashicons dashicons-admin-appearance"></span><?php echo $themes_list; ?></a>
								<?php } ?>								
								<a title="<?php _e( 'Preview the page according to the settings you see now on this row ','eos-dp' ); ?>" class="eos-dp-preview eos-dp-archive-preview" oncontextmenu="return false;" href="<?php echo wp_nonce_url( add_query_arg( $args,esc_url( $archive_url ) ),'eos_dp_preview','eos_dp_preview' ); ?>" target="_blank"><span class="dashicons dashicons-search"></span></a>
								<a title="<?php _e( 'Prevent JavaScript from running and preview the page according to the settings you see now on this row','eos-dp' ); ?>" class="eos-dp-preview" oncontextmenu="return false;" href="<?php echo wp_nonce_url( add_query_arg( array_merge( $args,array( 'js' => 'off' ) ),esc_url( $archive_url ) ),'eos_dp_preview','eos_dp_preview' ); ?>" target="_blank">
									<span class="dashicons dashicons-search">
										<span class="eos-dp-no-js">JS</span>
									</span>
								</a>								
								<a title="<?php _e( 'Copy this row settings','eos-dp' ); ?>" class="eos-dp-copy" href="#"><span class="dashicons dashicons-admin-page"></span></a>
								<a title="<?php _e( 'Paste last copied row settings','eos-dp' ); ?>" class="eos-dp-paste" href="#"><span class="dashicons dashicons-category"></span></a>
								<?php do_action( 'eos_dp_archive_action_buttons' ); ?>
								<a title="<?php _e( 'Close','eos-dp' ); ?>" class="eos-dp-close-actions" href="#"><span class="dashicons dashicons-no-alt"></span></a>
							</div>
						</td>
						<?php
						for( $k = 0;$k < $n;++$k ){
							$active = isset( $active_plugins[$k] ) && !in_array( $active_plugins[$k],$values ) ? true : false;
						?>
						<td class="center<?php echo $active ? ' eos-dp-active' : ''; ?>">
							<div class="eos-dp-td-chk-wrp eos-dp-td-archive-chk-wrp">
								<input class="eos-dp-row-<?php echo $row; ?> eos-dp-col-<?php echo $k + 1; ?> eos-dp-col-<?php echo ( $k + 1 ).'-'.$post_type; ?>" data-checked="<?php echo in_array( $active_plugins[$k],$values ) ? 'checked' : 'not-checked'; ?>" type="checkbox"<?php echo in_array( $active_plugins[$k],$values ) ? ' checked' : ''; ?> />
							</div>
						</td>
						<?php
						}
						?>
					</tr>
					<?php endif;
					++$row;
				}
			}
			?>
			</table>
		</div>
		<?php eos_dp_save_button(); ?>
	</section>
	<?php
}