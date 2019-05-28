<?php
/**
 * Template part with actual header.
 *
 * @package The7
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?><!DOCTYPE html>
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?> class="no-js">
<!--<![endif]-->
<head><!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WHQLH5K');</script>
<!-- End Google Tag Manager -->
	<meta charset="<?php bloginfo( 'charset' ) ?>" />
	<?php if ( presscore_responsive() ) : ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
	<?php endif ?>
    <?php presscore_theme_color_meta() ?>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
    <?php
    presscore_js_resize_event_hack();
    wp_head();
    ?>
</head>
<body <?php body_class() ?>><!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WHQLH5K"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php
do_action( 'presscore_body_top' );

$config = presscore_config();
?>

<div id="page"<?php if ( 'boxed' == $config->get( 'template.layout' ) ) echo ' class="boxed"'; ?>>
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'the7mk2' ) ?></a>
<?php
if ( apply_filters( 'presscore_show_header', true ) ) {
	presscore_get_template_part( 'theme', 'header/header', str_replace( '_', '-', $config->get( 'header.layout' ) ) );
	presscore_get_template_part( 'theme', 'header/mobile-header' );
}

if ( presscore_is_content_visible() && $config->get( 'template.footer.background.slideout_mode' ) ) {
	echo '<div class="page-inner">';
}
?>