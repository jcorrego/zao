<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Callback function for the plugin settings page
function eos_dp_options_page_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	wp_nonce_field( 'eos_dp_setts', 'eos_dp_setts' );
	wp_nonce_field( 'eos_dp_key', 'eos_dp_key' );
	$paths = array();
	$active_plugins = eos_dp_active_plugins();
	$plugins_table = eos_dp_plugins_table();
	$posts_per_page = isset( $_GET['posts_per_page'] ) && absint( $_GET['posts_per_page'] ) > 0 ? esc_attr( $_GET['posts_per_page'] ) : 30;
	$dpOpts = eos_dp_get_option( 'eos_dp_opts' );
	$post_type = isset( $_GET['eos_dp_post_type'] ) ? esc_attr( $_GET['eos_dp_post_type'] ) : 'page';
	$labsObj = get_post_type_object( $post_type );
	if( isset( $labsObj->labels ) ){
		$labs = $labsObj->labels;
		$active_label = isset( $labs->name ) ? $labs->name : __( 'posts','eos-dp' );	
	}
	
	$post_types_matrix = get_site_option( 'eos_post_types_plugins' );
	$post_types_matrix_pt = $post_types_matrix[$post_type];
	$post_types_plugins_pt = $plugins_table[$post_type];
	global $overridable;
	$overridable = $post_types_plugins_pt[0];
	$posts_per_page = isset( $_GET['posts_per_page'] ) ? absint( $_GET['posts_per_page'] ) : '30';
	$current_labels_name = '';
	if( '1' == $overridable ){
		?>
		<div class="eos-dp-warning notice notice-warning is-dismissible">
			<h2><?php 
				printf( 
					__( 'The %s are overriding these settings for all the rows that don\'t have the closed padlock %s.','eos-dp' ),
					'<a style="text-decoration:none;color:#D3C4B8" href="'.admin_url( 'admin.php?page=eos_dp_by_post_type' ).'">'.__( 'Post Type Settings','eos-dp' ).'</a>',
					'<span class="eos-post-locked-icon"></span>'
				);

			?></h2>
		</div>
		<?php
	}
	$ofs = isset( $_GET['eos_page'] ) && absint( $_GET['eos_page'] ) > 0 ? ( absint( $_GET['eos_page'] ) - 1 ) * $posts_per_page : 0;
	$args = array( 'post_type' => $post_type,'posts_per_page' => $posts_per_page,'offset' => $ofs,'publicly_queryable' => true );
	$args['orderby'] = isset( $_GET['orderby'] ) ? esc_attr( $_GET['orderby'] ) : 'title';
	$args['order'] = isset( $_GET['order'] ) ? esc_attr( $_GET['order'] ) : 'ASC';
	if( isset( $_GET['eos_post_title'] ) ){
		?><h2><?php printf( __( 'Results for %s','eos-dp' ),esc_html( $_GET['eos_post_title'] ) ); ?></h2><?php
		$args['s'] = esc_attr( urldecode( $_GET['eos_post_title'] ) );
	}
	if( isset( $_GET['eos_cat'] ) ){
		$cat = get_term( (int) $_GET['eos_cat'] );
		$cat = $cat && !is_wp_error( $cat ) ? sprintf( __( 'the category "%s"','eos-dp' ),$cat->name ) : sprintf( __( 'Category id %s','eos-dp' ),(int) $_GET['eos_cat'] );
		?><h2><?php printf( __( 'Results for %s','eos-dp' ),$cat ); ?></h2><?php
		$args['category'] = sanitize_title( $_GET['eos_cat'] );
	}
	if( in_array( $post_type,array( 'post','page' ) ) ){
		if( function_exists( 'eos_scfm_get_mobile_ids' ) ){
			if( isset( $_GET['device'] ) && 'mobile' === $_GET['device'] ){
				$args['post__in'] = eos_scfm_get_mobile_ids();
				$args['post_status'] = 'any';
			}
			elseif( isset( $_GET['device'] ) && 'desktop' === $_GET['device'] ){
				$args['post__not_in'] = eos_scfm_get_mobile_ids();
			}
		}
	}
	$is_home = false;
	$is_relevant_pages = false;
	if( isset( $_GET['eos_dp_home'] ) && 'true' === $_GET['eos_dp_home'] ){
		$show_on_front = eos_dp_get_option( 'show_on_front' );
		if( 'page' === $show_on_front ){
			$is_home = true;
		}
	}	
	elseif( isset( $_GET['eos_dp_relevant_pages'] ) && 'true' === $_GET['eos_dp_relevant_pages'] ){
		$is_relevant_pages = true;
		$args['post__in'] = eos_dp_important_pages();
	}
	$page_on_front = eos_dp_get_option( 'page_on_front' );
	$page_for_posts = eos_dp_get_option( 'page_for_posts' );
	if( 0 === absint( $page_on_front ) && absint( $page_for_posts ) > 0 ){
		$page_on_front = absint( $page_for_posts );
	}
	$posts = !$is_home ? get_posts( $args ) : array( get_post( $page_on_front ) );
	eos_dp_alert_plain_permalink();
	eos_dp_navigation();
	$pageSpeedInsightsUrl = 'https://developers.google.com/speed/pagespeed/insights/';
	if( !$is_home && !$is_relevant_pages ): ?>
	<?php
	$count_posts = wp_count_posts( $post_type );
	$published_posts = $count_posts->publish;
	$pagesN = ceil( $published_posts/max( 1,$posts_per_page ) );
	$current_page = isset( $_GET['eos_page'] ) ? $_GET['eos_page'] : 1;
	?>

	<div id="eos-dp-order-wrp" style="display:inline-block">
		<div class="eos-dp-display-data" style="display:inline-block;float:<?php echo is_rtl() ? 'right' : 'left'; ?>">
			<h4 style="margin-bottom:0"><?php _e( 'Number of posts','eos-dp' ); ?></h4>
			<input id="eos-dp-posts-per-page" type="number" min="10" max="200" value="<?php echo $posts_per_page; ?>" />
		</div>
		<div class="eos-dp-display-data" style="display:inline-block">
			<h4 style="margin-bottom:0"><?php _e( 'Order by','eos-dp' ); ?></h4>
			<select id="eos-dp-orderby-sel">
				<option value="title"<?php echo isset( $_GET['orderby'] ) && $_GET['orderby'] === 'title' ? ' selected' : ''; ?>><?php _e( 'Title','eos-dp' ); ?></option>
				<option value="ID"<?php echo isset( $_GET['orderby'] ) && $_GET['orderby'] === 'ID' ? ' selected' : ''; ?>><?php _e( 'Post id','eos-dp' ); ?></option>
				<option value="author"<?php echo isset( $_GET['orderby'] ) && $_GET['orderby'] === 'author' ? ' selected' : ''; ?>><?php _e( 'Author','eos-dp' ); ?></option>
				<option value="date"<?php echo isset( $_GET['orderby'] ) && $_GET['orderby'] === 'date' ? ' selected' : ''; ?>><?php _e( 'Date','eos-dp' ); ?></option>
				<option value="modified"<?php echo isset( $_GET['orderby'] ) && $_GET['orderby'] === 'modified' ? ' selected' : ''; ?>><?php _e( 'Last modified date','eos-dp' ); ?></option>
			</select>
		</div>
		<div class="eos-dp-display-data" style="display:inline-block">
			<h4 style="margin-bottom:0"><?php _e( 'Order','eos-dp' ); ?></h4>
			<select id="eos-dp-order-sel">
				<option value="ASC"<?php echo isset( $_GET['order'] ) && strtolower( $_GET['order'] ) === 'asc' ? ' selected' : ''; ?>><?php _e( 'Ascending','eos-dp' ); ?></option>
				<option value="DESC"<?php echo isset( $_GET['order'] ) && strtolower( $_GET['order'] ) === 'desc' ? ' selected' : ''; ?>><?php _e( 'Descending','eos-dp' ); ?></option>
			</select>
		</div>
		<?php
		if( function_exists( 'eos_scfm_get_mobile_ids' ) && in_array( $post_type,array( 'post','page' ) ) ){
		?>
		<div class="eos-dp-display-data" style="display:inline-block">
			<h4 style="margin-bottom:0"><?php _e( 'Device','eos-dp' ); ?></h4>
			<select id="eos-dp-device">
				<option value="all"<?php echo isset( $_GET['device'] ) && strtolower( $_GET['device'] ) === 'all' ? ' selected' : ''; ?>><?php _e( 'All devices','eos-dp' ); ?></option>
				<option value="desktop"<?php echo isset( $_GET['device'] ) && strtolower( $_GET['device'] ) === 'desktop' ? ' selected' : ''; ?>><?php _e( 'Desktop devices','eos-dp' ); ?></option>
				<option value="mobile"<?php echo isset( $_GET['device'] ) && strtolower( $_GET['device'] ) === 'mobile' ? ' selected' : ''; ?>><?php _e( 'Mobile devices','eos-dp' ); ?></option>				
			</select>
		</div>
		<?php
		}
		?>
		<div class="eos-dp-display-data" style="display:inline-block;line-height:2;margin:0 6px">
			<a href="<?php echo add_query_arg( array( 'posts_per_page' => 30,'orderby'=>'title','order'=>'ASC' ),admin_url( 'admin.php?page=eos_dp_menu&eos_dp_post_type='.$post_type ) ); ?>" class="button" id="eos-dp-order-refresh"><?php _e( 'Apply','eos-dp' ); ?></a>
		</div>
	</div>
	<?php if( $pagesN > 1 ): ?>
	<div id="eos-dp-posts-nav" style="margin:0 20px;float:<?php echo is_rtl() ? 'left' : 'right'; ?>">
		<div class="tablenav-pages"><span class="displaying-num"><?php printf( __( '%s items','eos-dp' ),$published_posts ); ?></span>
			<span class="pagination-links">
				<a class="button next-page<?php echo $current_page -1 === 0 ? ' eos-no-events' : ''; ?>" href="<?php echo add_query_arg( array( 'posts_per_page' => $posts_per_page,'eos_dp_post_type' => $post_type,'eos_page' => 1 ),admin_url( 'admin.php?page=eos_dp_menu' ) ); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">&#xab;</span></a>
				<a class="button next-page<?php echo $current_page < 2 ? ' eos-no-events' : ''; ?>" href="<?php echo add_query_arg( array( 'posts_per_page' => $posts_per_page,'eos_dp_post_type' => $post_type,'eos_page' => $current_page - 1 ),admin_url( 'admin.php?page=eos_dp_menu' ) ); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">&#x2039;</span></a>
				<span class="paging-input">
					<label for="current-page-selector" class="screen-reader-text"><?php _e( 'Current Page','eos-dp' ); ?></label>
					<input data-url="<?php echo add_query_arg( 'eos_dp_post_type',$post_type,admin_url( 'admin.php?page=eos_dp_menu' ) ); ?>" class="current-page" id="current-page-selector" type="number" min="1" max="<?php echo $pagesN; ?>" step="1" name="paged" value="<?php echo esc_attr( $current_page ); ?>" size="1" aria-describedby="table-paging">
					<span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $pagesN ; ?></span>
				</span>
			</span>
			<a class="button next-page<?php echo $current_page - $pagesN == 0 ? ' eos-no-events' : ''; ?>" href="<?php echo add_query_arg( array( 'posts_per_page' => $posts_per_page,'eos_dp_post_type' => $post_type,'eos_page' => $current_page + 1 ),admin_url( 'admin.php?page=eos_dp_menu' ) ); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">&#8250;</span></a>
			<a class="button last-page<?php echo $current_page - $pagesN == 0 ? ' eos-no-events' : ''; ?>" href="<?php echo add_query_arg( array( 'posts_per_page' => $posts_per_page,'eos_dp_post_type' => $post_type,'eos_page' => $pagesN ),admin_url( 'admin.php?page=eos_dp_menu' ) ); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">&#187;</span></a></span>
		</div>
		<div class="eos-dp-display-data eos-dp-toggle-search-wrp" style="display:inline-block;line-height:2;margin:16px 0">
			<span><?php _e( 'Search Post','eos-dp' ); ?></span>
			<span id="eos-dp-toggle-search" class="hover dashicons dashicons-search" style="line-height:32px"></span>
		</div>
		<div class="eos-dp-search-wrp eos-hidden">
			<div class="eos-dp-search-box" style="margin:32px 20px">
				<label class="screen-reader-text" for="post-search-input"><?php _e( 'Search By Title','eos-dp' ); ?></label>
				<input type="search" id="eos-dp-post-search" value="">
				<input type="submit" id="eos-dp-post-search-submit" data-url="<?php echo add_query_arg( 'eos_post_title','',admin_url( 'admin.php?page=eos_dp_menu&eos_dp_post_type='.$post_type ) ); ?>" class="button" value="<?php _e( 'Search By Title','eos-dp' ); ?>" />
			</div>
			<div class="eos-dp-search-box" style="margin:32px 20px">
				<label class="screen-reader-text" for="post-search-input"><?php _e( 'Search By Category','eos-dp' ); ?></label>
				<div id="eos-dp-by-cat-search" style="display:inline-block"><?php wp_dropdown_categories(); ?></div>
				<input type="submit" id="eos-dp-by-cat-search-submit" data-url="<?php echo add_query_arg( 'eos_cat','',admin_url( 'admin.php?page=eos_dp_menu&eos_dp_post_type='.$post_type ) ); ?>" class="button" value="<?php _e( 'Search By Category','eos-dp' ); ?>" />
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php
	endif;
	if( count( $posts ) < 1 ){
		?>
		<p><?php printf( __( 'You have no %s','eos-dp' ),$active_label ); ?></p>
		<?php
		return;
	}
	?>
	<section id="eos-dp-control-panel-section" class="eos-dp-section<?php echo '1' == $overridable ? ' eos-single-overrided' : ''; ?>">
		<h2 id="eos-dp-singles-title" style="margin:32px 0 -16px 0"><?php echo esc_html( $active_label ); ?></h2>
		<?php do_action( 'eos_dp_after_singles_title' ); ?>
		<div id="eos-dp-table-head-actions"><?php do_action( 'eos_dp_pre_table_head' ); ?></div>
			<?php eos_dp_plugin_names_orientation_ctrl(); ?>
			<table id="eos-dp-setts" data-post_type="<?php echo $post_type; ?>" data-zoom="1">
				<tr id="eos-dp-table-head">
					<th style="vertical-align:top;background:transparent;border-style:none;text-align:initial">
						<div style="margin-bottom:12px;margin-top:12px">
							<span class="eos-dp-locked-wrp eos-dp-icon-wrp"><span class="eos-post-locked-icon" style="width:20px;height:20px"></span>
							<span class="eos-dp-help eos-dp-legend-txt" title="<?php _e( 'The Post Types Settings will never override the locked row options','eos-dp' ); ?>">?</span>
						</div>
						<div style="margin-bottom:12px">
							<span class="eos-dp-unlocked-wrp eos-dp-icon-wrp"><span class="eos-post-unlocked-icon" style="width:20px;height:20px"></span>
							<span class="eos-dp-help eos-dp-legend-txt" title="<?php _e( 'The Post Types Settings may override the unlocked row options','eos-dp' ); ?>">?</span>
						</div>
						<div style="margin-bottom:12px;margin-top:32px">
							<span class="eos-dp-active-wrp eos-dp-icon-wrp"><input style="width:20px;height:20px" type="checkbox" /></span>
							<span class="eos-dp-legend-txt"><?php _e( 'Plugin active','eos-dp' ); ?></span>
						</div>
						<div>
							<span class="eos-dp-not-active-wrp eos-dp-icon-wrp"><input style="width:20px;height:20px" type="checkbox" checked/></span>
							<span class="eos-dp-legend-txt"><?php _e( 'Plugin not active','eos-dp' ); ?></span>
						</div>
						<div style="margin-top:8px;margin-bottom:16px">
							<span style="margin:0;font-size:20px eos-dp-icon-wrp" title="<?php __( 'Restore last saved options','eos-dp' ); ?>" class="dashicons dashicons-image-rotate"></span>
							<span class="eos-dp-legend-txt"><?php _e( 'Back to last saved settings','eos-dp' ); ?></span>
						</div>
					</th>
					<?php
					$n = 0;
					foreach( $active_plugins as $plugin ){
						$plugin_name = strtoupper( str_replace( '-',' ',dirname( $plugin ) ) );
						$plugin_name_short = substr( $plugin_name,0,25 );
						$plugin_name_short = $plugin_name === $plugin_name_short ? $plugin_name : $plugin_name_short.' ...';
						$details_url = add_query_arg( 
							array( 
								'tab' => 'plugin-information',
								'plugin' => dirname( $plugin ),
								'TB_iframe' => true,
								'eos_dp' => $plugin,
								'eos_dp_info' => 'true'
							),
							admin_url( 'plugin-install.php' )
						);						
						?>
						<th class="eos-dp-name-th">
							<div>
								<div id="eos-dp-plugin-name-<?php echo $n + 1; ?>" class="eos-dp-plugin-name" data-path="<?php echo $plugin; ?>">
									<span><a title="<?php printf( esc_attr__( 'View details of %s','eos-dp' ),esc_attr( $plugin_name ) ); ?>" href="<?php echo esc_url( $details_url ); ?>" target="_blank"><?php echo esc_html( $plugin_name_short ); ?></a></span>
								</div>
								<div class="eos-dp-global-chk-col-wrp">
									<div class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate %s everywhere','eos-dp' ),esc_attr( $plugin_name ) ); ?>" data-col="<?php echo $n + 1; ?>" class="eos-dp-global-chk-col" type="checkbox" /></div>
									<div class="eos-dp-reset-col" data-col="<?php echo $n + 1; ?>"><span title="<?php printf( __( 'Restore last saved options for %s everywhere','eos-dp' ),esc_attr( $plugin_name ) ); ?>" class="dashicons dashicons-image-rotate"></span></div>
								</div>
							</div>
						</th>
						<?php
						++$n;
					}
					?>
				</tr>
			<?php
				if( $posts && !empty( $posts ) ){
					$row = 1;
					foreach( $posts as $post ){
						if( $post->post_type === $post_type ){
							$desktop_id = 0;
							$mobileClass = '';
							if( in_array( $post_type,array( 'post','page' ) ) ){
								if( function_exists( 'eos_scfm_related_desktop_id' ) ){			
									$desktop_id = eos_scfm_related_desktop_id( $post->ID );
									if( $desktop_id > 0 ){
										$mobileClass = ' eos-dp-mobile';
									}
								}
							}
							$locked = '';
							if( isset( $post_types_matrix_pt[3] ) && is_array( $post_types_matrix_pt[3] ) && !empty( $post_types_matrix_pt[3] ) && in_array( $post->ID,$post_types_matrix_pt[3] ) ){
								$locked = ' eos-post-locked';
							}
						?>
						<tr class="eos-dp-post-row eos-dp-post-<?php echo $post_type.$mobileClass.$locked; ?>" data-post-id="<?php echo $post->ID; ?>">
							<?php
							if( isset( $post->post_title ) ){
								?>
								<td class="eos-dp-post-name-wrp">
									<span class="eos-dp-lock-post-wrp"><input title="<?php printf( __( 'If locked the Post Types Settings will never override these row options for %s','eos-dp' ),esc_attr( $post->post_title ) ); ?>" data-row="<?php echo esc_attr( $row ); ?>" class="eos-dp-lock-post" type="checkbox" /></span>
									<span class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate all plugins in %s','eos-dp' ),esc_attr( $post->post_title ) ); ?>" data-row="<?php echo esc_attr( $row ); ?>" class="eos-dp-global-chk-row" type="checkbox" /></span>
									<span class="eos-dp-title"><?php echo '' !== $post->post_title ? esc_html( $post->post_title ) : sprintf( __( 'Untitled (post id:%s)','eos-dp' ),$post->ID ); ?></span>
									<?php
									if( $desktop_id > 0 ){
										$desktop_title = get_the_title( $desktop_id );
										?><span title="<?php printf( __( 'Mobile version of %s','eos-dp' ),$desktop_title ); ?>" class="eos-dp-mobile dashicons dashicons-smartphone"></span><?php
									}
									$args = array( 'test_id'=>time(),'fdp_post_id'=>$post->ID );
									?>
									<div class="eos-dp-actions" data-post-id="<?php echo $post->ID; ?>">
										<a title="<?php _e( 'Edit page','eos-dp' ); ?>" class="eos-dp-edit" href="<?php echo get_edit_post_link( $post->ID ); ?>" target="_blank"><span class="dashicons dashicons-edit"></span></a>
										<a title="<?php _e( 'View page loading plugins according to the saved options','eos-dp' ); ?>" class="eos-dp-view" href="<?php echo add_query_arg( 'show_disabled_plugins',md5( $_SERVER['REMOTE_ADDR'].( absint( time()/1000 ) ) ),get_permalink( $post->ID ) ); ?>" target="_blank"><span class="dashicons dashicons-visibility"></span></a>
										<?php 
										$themes_list = eos_dp_active_themes_list();
										if( $themes_list ){
										?>
										<a title="<?php _e( 'Select a different Theme ONLY FOR PREVIEW','eos-dp' ); ?>" class="eos-dp-theme-sel"><span class="dashicons dashicons-admin-appearance"></span><?php echo $themes_list; ?></a>
										<?php } ?>
										<a data-page_speed_insights="false" title="<?php _e( 'Preview the page according to the settings you see now on this row','eos-dp' ); ?>" class="eos-dp-preview" oncontextmenu="return false;" href="<?php echo wp_nonce_url( add_query_arg( $args,get_permalink( $post->ID ) ),'eos_dp_preview','eos_dp_preview' ); ?>" target="_blank"><span class="dashicons dashicons-search"></span>
										<a data-page_speed_insights="false" title="<?php _e( 'Prevent JavaScript from running and preview the page according to the settings you see now on this row','eos-dp' ); ?>" class="eos-dp-preview" oncontextmenu="return false;" href="<?php echo wp_nonce_url( add_query_arg( array_merge( $args,array( 'js' => 'off' ) ),get_permalink( $post->ID ) ),'eos_dp_preview','eos_dp_preview' ); ?>" target="_blank">
											<span class="dashicons dashicons-search">
												<span class="eos-dp-no-js">JS</span>
											</span>
										</a>
										<?php
										if( !in_array( $_SERVER['REMOTE_ADDR'],array( '127.0.0.1','::1' ) ) ){
											$args['eos_dp_preview'] = 1000*absint( time()/1000 );
											$psi_url = add_query_arg(
												array(
													'url' => 
													urlencode( add_query_arg( $args,get_permalink( $post->ID )	) )
												),
												$pageSpeedInsightsUrl
											);
										?>
										<a data-page_speed_insights="true" title="<?php _e( 'Calculate Google PageSpeed Insights of the page according to the settings you see now on this row','eos-dp' ); ?>" class="eos-dp-preview eos-dp-psi-preview" oncontextmenu="return false;" href="<?php echo $psi_url; ?>" target="_blank">
											<span class="dashicons dashicons-search">
												<img width="20" height="20" src="<?php echo EOS_DP_PLUGIN_URL.'/img/pagespeed.png'; ?>" />
											</span>
										</a>
										<?php } ?>
										<a title="<?php _e( 'Copy this row settings','eos-dp' ); ?>" class="eos-dp-copy" href="#"><span class="dashicons dashicons-admin-page"></span></a>
										<a title="<?php _e( 'Paste last copied row settings','eos-dp' ); ?>" class="eos-dp-paste" href="#"><span class="dashicons dashicons-category"></span></a>
										<?php do_action( 'eos_dp_action_buttons' ); ?>
										<a title="<?php _e( 'Close','eos-dp' ); ?>" class="eos-dp-close-actions" href="#"><span class="dashicons dashicons-no-alt"></span></a>
									</div>
								</td>
								<?php
								for( $k = 0;$k < $n;++$k ){
									$values_string = get_post_meta( $post->ID, '_eos_deactive_plugins_key',true );
									$values = explode( ',',$values_string );
								?>
								<td class="center<?php echo !in_array( $active_plugins[$k],$values ) ? ' eos-dp-active' : ''; ?>">
									<div class="eos-dp-td-chk-wrp">
										<input class="eos-dp-row-<?php echo $row; ?> eos-dp-col-<?php echo $k + 1; ?> eos-dp-col-<?php echo ( $k + 1 ).'-'.$post_type; ?>" data-checked="<?php echo in_array( $active_plugins[$k],$values ) ? 'checked' : 'not-checked'; ?>" type="checkbox"<?php echo in_array( $active_plugins[$k],$values ) ? ' checked' : ''; ?> />
									</div>
								</td>
								<?php
								}
							} ?>
						</tr>
						<?php
						++$row;
						}
					}
				}
			?>
			</table>
		<?php eos_dp_save_button(); ?>
	</section>
	<?php
}