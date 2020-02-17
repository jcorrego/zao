<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$plugin = EOS_DP_PLUGIN_BASE_NAME;
define( 'EOS_DP_DOCUMENTATION_URL','https://freesoul-deactivate-plugins.com/documentation/' );
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/partials/eos-dp-navigation.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/partials/eos-dp-table-head.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/partials/eos-dp-save-button.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-settings.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-post-type.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-archive.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-terms-archive.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-mobile.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-search.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-url.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-backend-url.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-backend.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-code-risk.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-help.php';
require_once EOS_DP_PLUGIN_DIR.'/admin/templates/pages/eos-dp-firing-order.php';


//It adds a settings link to the action links in the plugins page
add_filter( "plugin_action_links_$plugin", 'eos_dp_plugin_add_settings_link' );

//It redirects to the plugin settings page on successfully plugin activation
add_action( 'admin_init', 'eos_dp_redirect_to_settings' );

//It creates the transient needed for displaing plugin notices after activation
add_action( 'admin_notices','eos_dp_admin_notices',100 );

//It adds the plugin setting page under plugins menu
add_action( 'admin_menu','eos_dp_options_page',999 );

function eos_dp_external_plugin_activation( $plugin, $network_activation ) {
	eos_dp_update_option( 'eos_dp_new_plugin_activated','activated' );
}
add_action( 'activated_plugin', 'eos_dp_external_plugin_activation', 10, 2 );



add_filter( 'admin_title', 'eos_dp_admin_page_title',99, 2 );
//It set the browser tab title depending the options page
function eos_dp_admin_page_title( $title,$sep ){
	$titles = array(
		'eos_dp_by_post_type' => __( 'Disable plugins | Post Types','eos-dp' ),
		'eos_dp_menu' => __( 'Disable plugins | Singles','eos-dp' ),
		'eos_dp_by_archive' => __( 'Disable plugins | Archives','eos-dp' ),
		'eos_dp_by_term_archive' => __( 'Disable plugins | Terms Archives','eos-dp' ),
		'eos_dp_mobile' => __( 'Disable plugins | Mobile','eos-dp' ),
		'eos_dp_search' => __( 'Disable plugins | Search','eos-dp' ),
		'eos_dp_url' => __( 'Disable plugins | URL','eos-dp' ),
		'eos_dp_admin_url' => __( 'Disable plugins | Admin URL','eos-dp' ),
		'eos_dp_admin' => __( 'Disable plugins | Back-end','eos-dp' ),
		'eos_dp_documentation' => __( 'Freesoul Deactivate Plugins Documentation','eos-dp' ),
	);
	if( isset( $_GET['page'] ) && in_array( $_GET['page'],array_keys( $titles ) ) ){	
		return esc_html( $titles[$_GET['page']] );
	}
	if( isset( $_GET['eos_dp_info'] ) && 'true' == $_GET['eos_dp_info'] ){
		if( isset( $_GET['plugin'] ) ){
			return __( 'Plugin Details','eos-dp' );
		}
	}
	return $title;
}

//Remove other admin notices on the settings pages
function eos_dp_remove_other_admin_notices(){
	remove_all_actions( 'admin_notices' );
	remove_all_actions( 'all_admin_notices' );
}

if( isset( $_GET['page'] ) && in_array( $_GET['page'],array( 'eos_dp_admin','eos_dp_ajax' ) ) ){
	add_action( 'eos_dp_after_table_head_columns','eos_dp_add_theme_to_table_head' );
}
//It adds the theme column in the table header
function eos_dp_add_theme_to_table_head(){
	$theme = wp_get_theme();
	if( !is_object( $theme ) ) return;
	$theme_name = strtoupper( $theme->get( 'TextDomain' ) );
	$theme_name_short = substr( $theme_name,0,28 );
	$theme_name_short = $theme_name === $theme_name_short ? $theme_name : strtoupper( $theme_name_short ).' ...';
	?>
	<th class="eos-dp-name-th eos-dp-name-th-theme">
		<div>
			<div id="eos-dp-theme-name" class="eos-dp-theme-name" title="<?php echo esc_attr( $theme_name ); ?>" data-path="<?php echo get_stylesheet_directory_uri(); ?>">
				<span><?php echo esc_html( $theme_name_short ); ?></span>
			</div>
			<div id="eos-dp-global-chk-col-wrp" class="eos-dp-global-chk-col-wrp">
				<div class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate %s everywhere','eos-dp' ),esc_attr( $theme_name ) ); ?>" data-col="theme" class="eos-dp-global-chk-col" type="checkbox" /></div>
			</div>
		</div>
	</th>	
	<?php
}