<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Return $plugins_table
function eos_dp_plugins_table(){
	$plugins_table = eos_dp_get_updated_plugins_table();
	$plugins_table = is_array( $plugins_table ) && !empty( $plugins_table ) ? $plugins_table : eos_dp_post_types_empty();	
	return $plugins_table;
}
//It displays the admin navigation
function eos_dp_navigation(){
	$plugins_table = eos_dp_plugins_table();
	$post_types = array_keys( $plugins_table );
	$show_on_front = eos_dp_get_option( 'show_on_front' );
	if( 'page' === $show_on_front ){
		$page = 'eos_dp_menu';
		$sec = 'by-posts';
	}
	elseif( 'posts' === $show_on_front ){
		$page = 'eos_dp_by_archive';
		$sec = 'archives';
	}
	?>
	<div class="eos-pre-nav">
		<h3>
			<a style="text-decoration:none;color:inherit" href="https://freesoul-deactivate-plugins.com/" target="_blank" rel="noopener">
				<span class="dashicons dashicons-plugins-checked"></span> <?php printf( 'Freesoul Deactivate Plugins v%s',EOS_DP_VERSION ); ?>
			</a>
		</h3>
	</div>
	<div id="eos-dp-setts-nav-wrp">
		<ul id="eos-dp-setts-nav">
			<li data-section="eos-dp-<?php echo $sec; ?>" class="hover<?php echo $_GET['page'] === $page && isset( $_GET['eos_dp_home'] ) && 'true' === $_GET['eos_dp_home'] ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page='.$page.'&eos_dp_home=true' ); ?>"><?php _e( 'Homepage','eos-dp' ); ?></a></li>
			<li data-section="eos-dp-control-panel-section" class="eos-dp-has-children hover<?php echo $_GET['page'] === 'eos_dp_menu' && !isset( $_GET['eos_dp_home'] ) ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_menu' ); ?>"><?php _e( 'Singles','eos-dp' ); ?></a>
				<?php
				if( !empty( $plugins_table ) ){
				?>
				<span class="dashicons dashicons-arrow-down"></span>
				<ul id="eos-dp-singles-sub" class="eos-dp-sub-menu">
				<?php
				foreach( $plugins_table as $pt  => $arr ){
					$postTypeObj = get_post_type_object( $pt );
					if( !in_array( $pt,array( 'attachment' ) ) && is_object( $postTypeObj ) ){
						$labels = get_post_type_labels( $postTypeObj );
						$labels_name = isset( $labels->name ) ? $labels->name : esc_html( $pt );
						?>
						<li class="eos-dp-submenu-item<?php echo isset( $_GET['eos_dp_post_type'] ) && $pt === $_GET['eos_dp_post_type'] ? ' eos-dp-current-submenu' : ''; ?>">
							<?php if( in_array( $pt,array( 'page','post' ) ) ){ ?>
							<span class="dashicons dashicons-admin-<?php echo esc_attr( $pt ); ?>"></span>
							<?php } ?>
							<a class="eos-dp-single-item-<?php echo esc_attr( $pt ); ?>" href="<?php echo add_query_arg( 'eos_dp_post_type',esc_attr( $pt ),admin_url( 'admin.php?page=eos_dp_menu' ) ); ?>"><?php echo esc_html( $labels_name ); ?></a>
						<?php
						if( 'page' === $pt ){
						?>
						<span class="dashicons dashicons-arrow-right"></span>
						<ul class="eos-dp-sub-menu">
							<li class="eos-dp-sub-sub-menu<?php echo isset( $_GET['eos_dp_relevant_pages'] ) && 'true' === $_GET['eos_dp_relevant_pages'] ? ' eos-active' : ''; ?>"><a href="<?php echo add_query_arg( array( 'eos_dp_relevant_pages' => 'true','eos_dp_post_type' => 'page' ),admin_url( 'admin.php?page=eos_dp_menu' ) ); ?>"><?php _e( 'Relevant pages','eos-dp' ); ?></a></li>
						</ul>
						<?php
						}
						?>
						</li>
						<?php
					}
				}
				?>
				</ul>
				<?php
				}
				?>
			</li>
			<li data-section="eos-dp-by-posts" class="hover<?php echo $_GET['page'] === 'eos_dp_by_post_type' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_by_post_type' ); ?>"><?php _e( 'Post Types','eos-dp' ); ?></a></li>
			<li data-section="eos-dp-archives" class="hover<?php echo $_GET['page'] === 'eos_dp_by_archive' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_by_archive' ); ?>"><?php _e( 'Archives','eos-dp' ); ?></a></li>
			<li data-section="eos-dp-term-archives" class="eos-dp-has-children hover<?php echo $_GET['page'] === 'eos_dp_by_term_archive' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_by_term_archive' ); ?>"><?php _e( 'Terms Archives','eos-dp' ); ?></a>
				<?php
				$taxs = get_taxonomies( array(),'objects' );
				if( $taxs ){
				?>
				<span class="dashicons dashicons-arrow-down"></span>
				<ul id="eos-dp-taxs-sub" class="eos-dp-sub-menu">
				<?php
					foreach( $taxs as $tax ){
						if( '1' == $tax -> public && isset( $tax -> object_type ) ){
							$show = false;
							$labels_names = array();
							foreach( $tax -> object_type as $term_post_type ){
								if( in_array( $term_post_type,$post_types ) ){
									$show = true;
									$postTypeObj = get_post_type_object( $term_post_type );
									$labels = get_post_type_labels( $postTypeObj );
									$labels_names[] = isset( $labels->name ) ? $labels->name : $term_post_type;
								}
							}
							if( $show ){
								?>
								<li class="eos-dp-submenu-item<?php echo isset( $_GET['eos_dp_tax'] ) && $tax->name === $_GET['eos_dp_tax'] ? ' eos-dp-current-submenu' : ''; ?>"><a href="<?php echo add_query_arg( 'eos_dp_tax',$tax -> name,admin_url( 'admin.php?page=eos_dp_by_term_archive' ) ); ?>"><?php printf( __( '%s (%s)','eos-dp' ),esc_html( $tax -> label ),esc_html( implode( ',',$labels_names ) ) ); ?></a></li>
								<?php
							}
						}
					}
				?>
				</ul>
				<?php
				}
				?>
			</li>
			<li data-section="eos-dp-term-mobile" class="hover<?php echo $_GET['page'] === 'eos_dp_mobile' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_mobile' ); ?>"><?php _e( 'Mobile','eos-dp' ); ?></a></li>
			<li data-section="eos-dp-search" class="hover<?php echo $_GET['page'] === 'eos_dp_search' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_search' ); ?>"><?php _e( 'Search','eos-dp' ); ?></a></li>
			<li data-section="eos-dp-url" class="eos-dp-has-children hover<?php echo $_GET['page'] === 'eos_dp_url' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_url' ); ?>"><?php _e( 'Custom URLs','eos-dp' ); ?></a>
				<span class="dashicons dashicons-arrow-down"></span>
				<ul class="eos-dp-sub-menu">
					<li data-section="eos-dp-url" class="eos-dp-submenu-item hover<?php echo $_GET['page'] === 'eos_dp_url' ? ' eos-active' : ''; ?>"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_url' ); ?>"><?php _e( 'Front-end URLs','eos-dp' ); ?></a></li>
					<li data-section="eos-dp-admin-url" class="eos-dp-submenu-item hover<?php echo $_GET['page'] === 'eos_dp_admin_url' ? ' eos-active' : ''; ?>"><a href="<?php echo admin_url( 'plugins.php?page=eos_dp_admin_url' ); ?>"><?php _e( 'Back-end URLs','eos-dp' ); ?></a></li>
				</ul>
			</li>
			<li data-section="eos-dp-admin" class="eos-dp-has-children hover<?php echo $_GET['page'] === 'eos_dp_admin' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'plugins.php?page=eos_dp_admin' ); ?>"><?php _e( 'Back-end','eos-dp' ); ?></a>					
				<span class="dashicons dashicons-arrow-down"></span>
				<ul class="eos-dp-sub-menu">
					<li data-section="eos-dp-admin" class="hover<?php echo $_GET['page'] === 'eos_dp_admin' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'plugins.php?page=eos_dp_admin' ); ?>"><?php _e( 'Back-end Singles','eos-dp' ); ?></a></li>		
					<li data-section="eos-dp-admin-url" class="eos-dp-submenu-item hover<?php echo $_GET['page'] === 'eos_dp_admin_url' ? ' eos-active' : ''; ?>"><a href="<?php echo admin_url( 'plugins.php?page=eos_dp_admin_url' ); ?>"><?php _e( 'Back-end URLs','eos-dp' ); ?></a></li>
				</ul>
			</li>			
			<li data-section="eos-dp-firing-order" class="hover<?php echo $_GET['page'] === 'eos_dp_firing_order' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_firing_order' ); ?>"><?php _e( 'Firing Order','eos-dp' ); ?></a></li>
			<li data-section="eos-dp-code-risk" class="hover<?php echo $_GET['page'] === 'eos_dp_code_risk' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_code_risk' ); ?>"><?php _e( 'Plugin Tests','eos-dp' ); ?></a></li>
			<?php do_action( 'eos_dp_tabs' ); ?>
			<li data-section="eos-dp-help" class="hover<?php echo $_GET['page'] === 'eos_dp_help' ? ' eos-active' : ''; ?> eos-dp-setts-menu-item"><a href="<?php echo admin_url( 'admin.php?page=eos_dp_help&tab=flowchart' ); ?>"><?php printf( __( '%s Help','eos-dp' ),'<span class="dashicons dashicons-editor-help"></span>' ); ?></a></li>
		</ul>
	</div>
	<?php do_action( 'eos_dp_after_settings_nav' );
}