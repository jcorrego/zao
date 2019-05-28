<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'WOO_F_LOOKBOOK_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo-lookbook" . DIRECTORY_SEPARATOR );
define( 'WOO_F_LOOKBOOK_ADMIN', WOO_F_LOOKBOOK_DIR . "admin" . DIRECTORY_SEPARATOR );
define( 'WOO_F_LOOKBOOK_FRONTEND', WOO_F_LOOKBOOK_DIR . "frontend" . DIRECTORY_SEPARATOR );
define( 'WOO_F_LOOKBOOK_LANGUAGES', WOO_F_LOOKBOOK_DIR . "languages" . DIRECTORY_SEPARATOR );
define( 'WOO_F_LOOKBOOK_INCLUDES', WOO_F_LOOKBOOK_DIR . "includes" . DIRECTORY_SEPARATOR );

define( 'WOO_F_LOOKBOOK_CSS', WP_PLUGIN_URL . "/woo-lookbook/css/" );
define( 'WOO_F_LOOKBOOK_CSS_DIR', WOO_F_LOOKBOOK_DIR . "css" . DIRECTORY_SEPARATOR );
define( 'WOO_F_LOOKBOOK_JS', WP_PLUGIN_URL . "/woo-lookbook/js/" );
define( 'WOO_F_LOOKBOOK_JS_DIR', WOO_F_LOOKBOOK_DIR . "js" . DIRECTORY_SEPARATOR );
define( 'WOO_F_LOOKBOOK_IMAGES', WP_PLUGIN_URL . "/woo-lookbook/images/" );


/*Include functions file*/
if ( is_file( WOO_F_LOOKBOOK_INCLUDES . "data.php" ) ) {
	require_once WOO_F_LOOKBOOK_INCLUDES . "data.php";
}

if ( is_file( WOO_F_LOOKBOOK_INCLUDES . "functions.php" ) ) {
	require_once WOO_F_LOOKBOOK_INCLUDES . "functions.php";
}
/*Include functions file*/
if ( is_file( WOO_F_LOOKBOOK_INCLUDES . "support.php" ) ) {
	require_once WOO_F_LOOKBOOK_INCLUDES . "support.php";
}
if ( is_file( WOO_F_LOOKBOOK_INCLUDES . "instagram.php" ) ) {
	require_once WOO_F_LOOKBOOK_INCLUDES . "instagram.php";
}

vi_include_folder( WOO_F_LOOKBOOK_ADMIN, 'WOO_F_LOOKBOOK_Admin_' );
vi_include_folder( WOO_F_LOOKBOOK_FRONTEND, 'WOO_F_LOOKBOOK_Frontend_' );
?>