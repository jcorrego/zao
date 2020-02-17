<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'plugins_loaded','eos_dp_redirect_to_fdp_page' );
// add_action( 'admin_init','eos_dp_redirect_to_fdp_page' );
//Redirect to settings page if it's a FDP page
function eos_dp_redirect_to_fdp_page(){
	if( eos_dp_is_fdp_page() && false !== strpos( $_SERVER['REQUEST_URI'],'/wp-admin/plugins.php' ) ){
		wp_safe_redirect( esc_url( add_query_arg( $_GET,admin_url( 'admin.php' ) ) ) );
	}
}
//Enqueue scripts for back-end
function eos_dp_scripts() {	
	$params = array( 'is_rtl' => is_rtl() );
	if( 
		isset( $_GET['page'] ) 
		&& in_array( 
			$_GET['page'],
			array( 
				'eos_dp_url',
				'eos_dp_firing_order',
				'eos_dp_ajax' 
			) ) 
	){
		$params['page'] = esc_js( esc_attr( $_GET['page'] ) );
		wp_enqueue_script( 'jquery-ui-sortable',array( 'jquery' ) );
	}
	wp_enqueue_script( 'eos-dp-backend',EOS_DP_PLUGIN_URL.'/admin/js/fdp-admin.js', array( 'jquery' ),EOS_DP_VERSION );
	wp_localize_script( 'eos-dp-backend','eos_dp_js',$params );
}
//Enqueue style for back-end
function eos_dp_style() {
	$action = false;
	if( function_exists( 'get_current_screen' ) ){
		$screen = get_current_screen();
		$action = isset( $screen->action ) ? $screen->action : false;
	}
	if( 
		( $action && $action === 'add' ) 
		|| ( isset( $_GET['post'] ) 
		&& isset( $_GET['action'] ) 
		&& $_GET['action'] === 'edit' ) 
		|| eos_dp_is_fdp_page()
		){
		wp_enqueue_style( 'eos-dp-admin-style',EOS_DP_PLUGIN_URL.'/admin/css/fdp-admin.css',array(),EOS_DP_VERSION );
	}
}

add_action( 'admin_head','eos_dp_admin_inline_style' );
//Add inline style on admin pages
function eos_dp_admin_inline_style(){
	echo '<style>.eos_dp_plugin_upgrade_notice+p:before{content:"";display:none;opacity:0}</style>';
}

//Update options in case of single or multisite installation.
function eos_dp_update_option( $option,$newvalue,$autoload = true ){
	if( !is_multisite() ){
		return update_option( $option,$newvalue,$autoload );
	}
	else{
		return update_blog_option( get_current_blog_id(),$option,$newvalue );
	}
}
if( !function_exists( 'eos_dp_get_option' ) ){
	//Get options in case of single or multisite installation.
	function eos_dp_get_option( $option ){
		if( !is_multisite() ){
			return get_option( $option );
		}
		else{
			return get_blog_option( get_current_blog_id(),$option );
		}
	}
}
//It adds a settings link to the action links in the plugins page
function eos_dp_plugin_add_settings_link( $links ) {
    $settings_link = '<a class="eos-dp-setts" href="'.admin_url( 'admin.php?page=eos_dp_by_post_type' ).'">' . __( 'Settings','eos-dp' ). '</a>';
    array_push( $links, $settings_link );
    $help_link = '<a class="eos-dp-help" href="'.admin_url( 'admin.php?page=eos_dp_help' ).'">' . __( 'Help','eos-dp' ). '</a>';
    array_push( $links, $help_link );
  	return $links;
}
//It redirects to the plugin settings page on successfully plugin activation
function eos_dp_redirect_to_settings(){
	if( get_transient( 'freesoul-dp-notice-succ' ) ){
		delete_transient( 'freesoul-dp-notice-succ' );
		if( !get_transient( 'freesoul-dp-updating-mu' ) ){
			wp_safe_redirect( admin_url( 'admin.php?page=eos_dp_by_post_type' ) );
		}
	}
	$previous_version = eos_dp_get_option( 'eos_dp_version' );
	$version_compare = version_compare( $previous_version, EOS_DP_VERSION,'<' );
	if( $version_compare && EOS_DP_NEED_UPDATE_MU ){
		//if the plugin was updated and we need to update also the mu-plugin
		define( 'EOS_DP_DOING_MU_UPDATE',true );
		if( file_exists( WPMU_PLUGIN_DIR.'/eos-deactivate-plugins.php' ) ){
			unlink( WPMU_PLUGIN_DIR.'/eos-deactivate-plugins.php' );
		}
		require EOS_DP_PLUGIN_DIR.'/plugin-activation.php';
		eos_dp_update_option( 'eos_dp_version',EOS_DP_VERSION );
		set_transient( 'freesoul-dp-updating-mu',5 );
	}
}
//It creates the transient needed for displaing plugin notices after activation
function eos_dp_admin_notices(){
	//It creates the transient needed for displaing plugin notices after activation
	if( get_transient( 'freesoul-dp-notice-fail' ) ){
		delete_transient( 'freesoul-dp-notice-fail' );
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php _e( 'You have no direct write access, Freesoul Deactivate Plugins was not able to create the necessary mu-plugin and will not work.', 'eos-dp' ); ?></p>
	</div>
	<?php
	}
	if( !defined( 'EOS_DP_MU_VERSION' ) || EOS_DP_MU_VERSION !== EOS_DP_VERSION ){
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php _e( 'It looks you have updated Freesoul Deactivate Plugins using FTP. In any case to be sure everything is up to date, disable Freesoul Deactivate Plugins and then activate it again.', 'eos-dp' ); ?></p>
		</div>
		<?php		
	}
}
//It adds the plugin setting page under plugins menu
function eos_dp_options_page(){
	add_menu_page( __( 'Freesoul Deactivate Plugins','eos-dp' ),__( 'Freesoul Deactivate Plugins','eos-dp' ),'manage_options','eos_dp_by_post_type','eos_dp_by_post_type_callback','dashicons-plugins-checked',20 );
	add_plugins_page( __( 'Freesoul Deactivate Plugins','eos-dp' ),__( 'Freesoul Deactivate Plugins','eos-dp' ),'manage_options','eos_dp_by_post_type','eos_dp_by_post_type_callback',20 );
	//add_submenu_page( 'eos_dp_by_post_type',__( 'Deactivate by Post Type','eos-dp' ),__( 'Deactive by post type','eos-dp' ),'manage_options','eos_dp_by_post_type','eos_dp_options_page_callback',10 );
	add_submenu_page( null,__( 'Deactivate on each single post','eos-dp' ),__( 'Deactive on each single post','eos-dp' ),'manage_options','eos_dp_menu','eos_dp_options_page_callback',20 );
	add_submenu_page( null,__( 'Deactivate by Archive','eos-dp' ),__( 'Deactivate by Archive','eos-dp' ),'manage_options','eos_dp_by_archive','eos_dp_by_archive_callback',30 );
	add_submenu_page( null,__( 'Deactivate by Term Archive','eos-dp' ),__( 'Deactivate by Term Archive','eos-dp' ),'manage_options','eos_dp_by_term_archive','eos_dp_by_term_archive_callback',40 );
	add_submenu_page( null,__( 'Deactivate on mobile devices','eos-dp' ),__( 'Deactivate on mobile devices','eos-dp' ),'manage_options','eos_dp_mobile','eos_dp_mobile_callback',50 );
	add_submenu_page( null,__( 'Deactivate on search resutls page','eos-dp' ),__( 'Deactivate on search results page','eos-dp' ),'manage_options','eos_dp_search','eos_dp_search_callback',60 );
	add_submenu_page( null,__( 'Deactivate by URL','eos-dp' ),__( 'Deactivate by URL','eos-dp' ),'manage_options','eos_dp_url','eos_dp_by_url_callback',70 );
	add_submenu_page( null,__( 'Deactivate in Administration Pages by custom URLs','eos-dp' ),__( 'Deactivate in Administration Pages by custom URLs','eos-dp' ),'manage_options','eos_dp_admin_url','eos_dp_by_admin_url_callback',80 );
	add_submenu_page( null,__( 'Deactivate in Administration Pages','eos-dp' ),__( 'Deactivate in Administration Pages','eos-dp' ),'manage_options','eos_dp_admin','eos_dp_admin_callback' );	
	add_submenu_page( null,__( 'Firing Order','eos-dp' ),__( 'Firing Order','eos-dp' ),'manage_options','eos_dp_firing_order','eos_dp_firing_order_callback',90 );
	add_submenu_page( null,__( 'Code Risk','eos-dp' ),__( 'Code Risk','eos-dp' ),'manage_options','eos_dp_code_risk','eos_dp_code_risk_callback',100 );
	add_submenu_page( null,__( 'Help','eos-dp' ),__( 'Help','eos-dp' ),'manage_options','eos_dp_help','eos_dp_help_callback',110 );
}
//It displays the ajax loader gif
function eos_dp_ajax_loader_img(){
	?>
	<img alt="<?php _e( 'Ajax loader','eos-dp' ); ?>" class="ajax-loader-img eos-not-visible" width="30" height="30" src="<?php echo EOS_DP_PLUGIN_URL; ?>/img/ajax-loader.gif" />
	<?php
}
function eos_dp_alert_plain_permalink(){
	$permalink_structure = basename( get_option( 'permalink_structure' ) );
	if( '%postname%' !== $permalink_structure  ){
		$permalinks_label = __( 'the actual permalinks structure is not supported','eos-dp' );
		if( '' === $permalink_structure ){
			$permalinks_label = __( 'the permalinks are set as plain','eos-dp' );
		}
		elseif( '/archives/%post_id%' === $permalink_structure ){
			$permalinks_label = __( 'the permalinks are set as numeric','eos-dp' );
		}
	?>
	<div id="eos-dp-plain-permalink-wrg" style="line-height:1;margin:20px 0;padding:10px;color:#23282d;background:#fff;border-<?php echo is_rtl() ? 'right' : 'left'; ?>:4px solid  #dc3232">
		<div>
			<h1><?php printf( __( 'No plugins will be permanently deactivated because %s.','eos-dp' ),$permalinks_label ); ?></h1>
			<h1><?php _e( 'Only the permalinks structures "Day and name", "Month and name", "Post name"  and the custom ones ending with "%postname%" are supported (they are also better for SEO).','eos-dp' ); ?></h1>
		</div>
		<div>
			<a class="button" target="_blank" href="<?php echo admin_url( 'options-permalink.php' ); ?>"><?php  _e( 'Change Permalinks Structure','eos-dp' ); ?></a>
		</div>
	</div>
	<?php
	}
}
//It adds the plugin names orientation controll
function eos_dp_plugin_names_orientation_ctrl(){
	?>
	<div id="eos-dp-orientation-wrp">
		<span id="eos-dp-names-orientation"><?php _e( 'Plugin names orientation','eos-dp' ); ?></span>
		<span id="eos-dp-orientation-icon">
			<span></span>
		</span>
	</div>
	<div id="eos-dp-fit-to-screen" class="right" style="margin-top:22px">
		<span style="position:relative;top:-9px"><?php _e( 'Zoom','eos-dp' ); ?></span><span title="<?php _e( 'Zoom','eos-dp' ); ?>" class="hover dashicons dashicons-search"></span>
	</div>
	<?php
}
//It gets the plugins that are active/deactive for each post type.
function eos_dp_post_types_empty(){
	return array_fill_keys( 
		array_merge( 
				array( 'page' => 'page' ),
				get_post_types( array( 'publicly_queryable' => true ) )
			),
			array( 
				'1',
				implode( ',',array_fill( 0,count( get_option( 'active_plugins' ) ),'' ) )
			)
		);
}
//It returns the active plugins excluding Freesoul Deactivate Plugins
function eos_dp_active_plugins(){
	$active = get_option( 'active_plugins' );
	unset( $active[array_search( EOS_DP_PLUGIN_BASE_NAME,$active )] );
	if( defined( 'EOS_DP_PRO_PLUGIN_BASE_NAME' ) ){
		unset( $active[array_search( EOS_DP_PRO_PLUGIN_BASE_NAME,$active )] );
	}
	$active = array_values( $active );
	$n = 0;
	foreach( $active as $v ){
		if( false === strpos( $v,'/' ) ){
			unset( $active[$n] );
		}
		++$n;
	}
	return array_values( $active );
}
function eos_dp_get_plugins() {
	$plugin_root = WP_PLUGIN_DIR;
	// Files in wp-content/plugins directory
	$plugins_dir = @ opendir( $plugin_root);
	$plugin_files = array();
	if ( $plugins_dir ) {
		while (($file = readdir( $plugins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' || strpos( '_'.$file,'freesoul-deactivate-plugins' ) > 0 ) continue;
			if ( is_dir( $plugin_root.'/'.$file ) ) {
				$plugins_subdir = @ opendir( $plugin_root.'/'.$file );
				if ( $plugins_subdir ) {
					while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
							if ( substr($subfile, 0, 1) == '.' )
									continue;
							if ( substr($subfile, -4) == '.php' )
									$plugin_files[] = "$file/$subfile";
					}
					closedir( $plugins_subdir );
				}
			}
			else {
				if( substr($file, -4) == '.php' ) $plugin_files[] = $file;
			}
		}
		closedir( $plugins_dir );
	}
	if ( empty( $plugin_files ) ) return array();
	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( "$plugin_root/$plugin_file" ) ) continue;
		$plugins[plugin_basename( $plugin_file )] = 1;
	}
	uasort( $plugins,'_sort_uname_callback' );
	return $plugins;
}
//It returns the updated plugins table after a third plugin activation
function eos_dp_get_updated_plugins_table(){
	$plugins_table = eos_dp_get_option( 'eos_post_types_plugins' );
	if( !$plugins_table || !is_array( $plugins_table ) || empty( $plugins_table ) ) return eos_dp_post_types_empty();
	if( 'activated' !== eos_dp_get_option( 'eos_dp_new_plugin_activated' ) ){
		$plugins_table = !empty( $plugins_table ) ? $plugins_table : eos_dp_post_types_empty();	
	}
	else{
		$old_post_types = array_keys( $plugins_table );
		$new_post_types = get_post_types( array( 'publicly_queryable' => true ) );
		if( isset( $new_post_types['attachment'] ) ){
			unset( $new_post_types['attachment'] );
		}
		$new_post_types = array_keys( array_merge( array( 'page' => 'page' ),$new_post_types ) );
		if( $old_post_types !== $new_post_types ){
			foreach( $new_post_types as $key ){
				if( !isset( $plugins_table[$key] ) ){
					$plugins_table[$key] = array( 
						'1',
						implode( ',',array_fill( 0,count( get_option( 'active_plugins' ) ),'' ) )
					);
				}
			}
		}
	}	
	return $plugins_table;
}

//It returns the important pages
function eos_dp_important_pages(){
	$locations = get_nav_menu_locations();
	$ids = array();
	foreach( $locations as $location ){
		$menuItems = wp_get_nav_menu_items( $location );
		foreach($menuItems as $page) {
			$ids[] = $page->object_id;
		}
	}
	$keys = array(
		'comingsoon_input_page',
		'woocommerce_shop_page_id', 
		'woocommerce_cart_page_id',
		'woocommerce_checkout_page_id',
		'woocommerce_pay_page_id',
		'woocommerce_thanks_page_id',
		'woocommerce_myaccount_page_id',
		'woocommerce_edit_address_page_id',
		'woocommerce_view_order_page_id',
		'woocommerce_terms_page_id',
		'wp_page_for_privacy_policy'
	);
	if( 'page' === eos_dp_get_option( 'show_on_front' ) ){
		$keys[] = 'page_for_posts';
	}
	elseif( 'posts' === eos_dp_get_option( 'show_on_front' ) ){
		$keys[] = 'page_on_front';
	}
	foreach( $keys as $opt_key ){
		$id = eos_dp_get_option( $opt_key );
		if( $id ){
			$ids[]= $id;
		}
	}
	$sticky_posts = eos_dp_get_option( 'sticky_posts' );
	if( $sticky_posts ){
		$ids = array_merge( $ids,$sticky_posts );
	}
	return array_values( array_unique( $ids ) );
}

add_action( 'in_plugin_update_message-freesoul-deactivate-plugins/freesoul-deactivate-plugins.php','eos_dp_get_update_notice' );
//Check if it's a major release and return the upgrade notice
function eos_dp_get_update_notice(){
	$transient_name = 'eos_dp_changelog_'.EOS_DP_VERSION;
	$upgrade_notice = get_transient( $transient_name );
	if ( false === $upgrade_notice || '' === $upgrade_notice ) {
		$response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/freesoul-deactivate-plugins/trunk/readme.txt' );
		if ( !is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$upgrade_notice = substr( $response['body'],strpos( $response['body'],'== Changelog ==' ) + 15 );
			$arr = explode( '==',$upgrade_notice );
			$upgrade_notice = $arr[0];
			$upgrade_notice = wp_kses_post( $upgrade_notice );	
			set_transient( $transient_name, $upgrade_notice,3600*24 );
		}
	}
	echo '<div class="eos_dp_plugin_upgrade_notice"><br/>Last changes:<br/>'.wp_kses_post( str_replace( '*','</br>',$upgrade_notice ) ).'</div>';
}

if( isset( $_GET['eos_dp_info'] ) && 'true' === $_GET['eos_dp_info'] ){
	add_action( 'install_plugins_pre_plugin-information','eos_dp_plugin_information' );
}
//Add plugin information if it's not on the repository
function eos_dp_plugin_information(){
	if( isset( $_GET['eos_dp'] ) ){
		$api = plugins_api(
			'plugin_information',
			array(
				'slug' => wp_unslash( $_REQUEST['plugin'] ),
			)
		);		
		if ( is_wp_error( $api ) ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR.'/'.$_GET['eos_dp'] );
			if( $plugin_data ){
				remove_all_actions('admin_notices');
				remove_all_actions('all_admin_notices');				
				require_once  ABSPATH.'/wp-admin/admin-header.php';
				?>
				<div id="plugin-information" style="position:fixed;width:100vw;left:0;right:0;background:#fff;z-index:9999;height:100vh">
					<div id="plugin-information-title" class="with-banner">
						<div class="vignette"></div>
						<h2><?php echo esc_html( $plugin_data['Name'] ); ?></h2>
					</div>
					<div id="plugin-information-tabs" class="with-banner">
						<a class="current" href="#"><?php _e( 'Description','eos-dp' ); ?></a>
					</div>
					<div class="fyi" style="min-width:240px">
						<ul>
							<?php 
							foreach( $plugin_data as $key => $value ){
								if( !$value || '' === $value || in_array( $key,array( 'Name','Description','PluginURI','TextDomain' ) ) ) continue; 
							?>
							<li><strong><?php echo esc_html( $key ); ?>:</strong> <?php echo wp_kses( $value,array( 'a' => array( 'href' => array() ) ) ); ?></li>
							<?php } ?>
						</ul>
					</div>					
					<div id="section-holder">
						<div id="section-description" style="padding:20px" class="section">
							<?php if( isset( $plugin_data['Author'] ) ){ ?>
							<div style="margin-top:64px">
								<p><?php printf( __( 'By %s','eos-dp' ),wp_kses( $plugin_data['Author'],array( 'a' => array( 'href' => array() ) ) ) ); ?></p>
							</div>
							<?php } ?>
							<?php if( isset( $plugin_data['Description'] ) ){ ?>
							<div style="margin-top:64px">
								<p><?php echo wp_kses( $plugin_data['Description'],array( 'a' => array( 'href' => array() ) ) ); ?></p>
							</div>
							<?php
							}
							if( isset( $plugin_data['PluginURI'] ) ){
							?>	
							<div style="margin-top:32px">
								<p><?php printf( __( 'More info at %s','eos-dp' ),'<a href="'.esc_url( $plugin_data['PluginURI'] ).'">'.esc_url( $plugin_data['PluginURI'] ).'</a>' ); ?></p>
							</div>	
						</div>
					</div>				
				</div>
				<?php 
				}
				require_once  ABSPATH.'/wp-admin/admin-footer.php';
				exit;
			}
		}
		else{
		?>
		<div id="eos-dp-plugin-badge" style="position:fixed;top:10px;padding:10px;z-index:99999">
			<p>
				<a target="_blank" rel="noopener" href="https://plugintests.com/plugins/<?php echo esc_attr( $_GET['plugin'] ); ?>/latest"><img src="https://plugintests.com/plugins/<?php echo esc_attr( $_GET['plugin'] ); ?>/php-badge.svg"></a>
				<a target="_blank" rel="noopener" href="https://coderisk.com/wp/plugin/<?php echo esc_attr( $_GET['plugin'] ); ?>"><img src="https://coderisk.com/wp/plugin/<?php echo esc_attr( $_GET['plugin'] ); ?>/badge"></a>
			</p>
			<p>
				<a class="button" target="_blank" rel="noopener" href="https://plugintests.com/plugins/<?php echo esc_attr( $_GET['plugin'] ); ?>/latest"><?php _e( 'Go to the last plugin test results','eos-dp' ); ?></a>
				<a class="button" target="_blank" rel="noopener" href="https://coderisk.com/wp/plugin/<?php echo esc_attr( $_GET['plugin'] ); ?>"><?php _e( 'Go to Code Risk','eos-dp' ); ?></a>
			</p>
		</div>
		<?php
		}
	}
}
//Return list of active themes
function eos_dp_active_themes_list(){
	$active_themes = wp_get_themes();
	if( count ( $active_themes ) < 1 ) return false;
	$output = '<select class="eos-dp-themes-list">';
	$output .= '<option value="false">'.__( 'Current Theme','eos-dp' ).'</option>'; 
	foreach ( $active_themes as $theme => $v ){
		$output .= '<option value="'.esc_attr( $theme ).'">'.esc_html( $theme ).'</option>'; 
	}
	$output .= '</select>';
	return $output;
}

//Return true if it's a FDP settings page, or false if not socket_accept
function eos_dp_is_fdp_page(){
	return isset( $_GET['page'] ) 
	&& in_array( $_GET['page'],
		array( 
			'eos_dp_menu',
			'eos_dp_by_post_type',
			'eos_dp_by_archive',
			'eos_dp_by_term_archive',
			'eos_dp_admin',
			'eos_dp_admin_url',
			'eos_dp_mobile',
			'eos_dp_search',
			'eos_dp_url',
			'eos_dp_ajax',
			'eos_dp_firing_order',
			'eos_dp_code_risk',
			'eos_dp_testing',
			'eos_dp_status',
			'eos_dp_help',
			'eos_dp_pro_installations'
		) );
}

add_action( 'admin_bar_menu','eos_dp_light_admin_menu',40 );
// Add ligth admin menu to admin top  bar
function eos_dp_light_admin_menu( $wp_admin_bar ){
	if( !current_user_can( 'manage_options' ) ) return $wp_admin_bar;
	$opts = eos_dp_get_option( 'eos_dp_general_setts' );
	if( $opts && isset( $opts['menu_in_topbar'] ) && 'false' === $opts['menu_in_topbar'] ) return $wp_admin_bar;
	$admin_menus = eos_dp_get_option( 'eos_dp_admin_menu' );
	if( !$admin_menus ) return $wp_admin_bar;
	$admin_menus = json_decode( stripslashes( $admin_menus ),true );
	$wp_admin_bar->add_menu( array(
		'id'    => 'eos-dp-menu',
		'title' => '<span class="dashicons dashicons-plugins-checked" style="font-family:dashicons"></span> '.__( 'Admin Menu','eos-dp' ),
	));
	$n = 0;
	$k = 0;
	foreach( $admin_menus as $admin_menu => $arr ){
		$wp_admin_bar->add_node( array(
			'parent' => 'eos-dp-menu',
			'id'     => 'eos-dp-parent-menu-item-'.$n,
			'title'  => esc_html( $arr['title'] )
		) );
		$submenu = $arr['submenu'];
		foreach( $submenu as $arr2 ){
			$wp_admin_bar->add_node( array(
				'parent' => 'eos-dp-parent-menu-item-'.$n,
				'id'     => 'eos-dp-sub-menu-item-'.$k,
				'title'  => esc_html( $arr2['title'] ),
				'href'  => esc_url( $arr2['url'] )
			) );
			++$k;
		}
		++$n;
	}
	$wp_admin_bar->add_node( array(
		'parent' => 'eos-dp-menu',
		'id'     => 'eos-dp-menu-item-separator',
		'title' => '__________'
	) );
	$wp_admin_bar->add_node( array(
		'parent' => 'eos-dp-menu',
		'id'     => 'eos-dp-menu-item-backend-settings',
		'title' => '<span class="dashicons dashicons-admin-generic" style="line-height:27px;font-family:dashicons"></span> '.__( 'Backend Settings','eos-dp' ),
		'href' => admin_url( 'admin.php?page=eos_dp_admin' )
	) );
	return $wp_admin_bar;
}

//Return option as array
function eos_dp_get_option_array( $option ){
	$opts = eos_dp_get_option( 'eos_dp_general_setts' );
	if( !$opts || !is_array( $opts ) ){
		$opts = array();
	}
	return $opts;
}	