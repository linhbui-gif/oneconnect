<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

define( 'ITSEC_ENCRYPTION_KEY', 'YXIoYDg9b0NVO1c9dndfMT1KRDtgUilfRWYtNFlOWXB8MEN6W2BnVWw1b1lzKEBGO1UoK3EmIHwsRkRvVF5HbQ==' );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'oneconnectz_oneconnectz');

/** Database username */
define('DB_USER', 'oneconnectz_oneconnectz');

/** Database password */
define('DB_PASSWORD', 'BboyT290497@');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
define('ALLOW_UNFILTERED_UPLOADS', true);
/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'n-%DcDgn~A<q0eql&SJq@q@g5>7>MDER^oFVq#lxmyllIElQ9N4BVX+K}$;kQcb8');
define('SECURE_AUTH_KEY',  '&=LJoPg1_f`8EMHA@GRF:r;@M[8G%J^shDhvUICBry&6+eOmV/Bf_S U!~b&xh]R');
define('LOGGED_IN_KEY',    'so-0bKYH<Fdp]$l;C[,PeRajFx]mJ[6FXdlrG}q@-?.&hbRR-kjIp,{N=h>T|Ay%');
define('NONCE_KEY',        ';+ E;`l=^Z3*b81xDhRz/UO737g8QAR6?&BOzfztDvImqiT@=w:oc>x1vl<h[ZS)');
define('AUTH_SALT',        'cByNoBZt1%Gp^+@.Z2||d:-.fWPcbLN{bP_(PE]?_:~P4OUn*D+)Xa.F8LyA:V)]');
define('SECURE_AUTH_SALT', '-5-3t2`T6Gs_3Jrf0.@.W_<xpHcv@g]4Hrq%OmKrkq&TKeg3Brym?2VjXT}y}GSh');
define('LOGGED_IN_SALT',   'rVWpN.nCASKm3>GU:a)u|[lr<~t1kr@YDA/HFC#B6Mm3f`*QEDzQbG:*%lGiu,*+');
define('NONCE_SALT',       '(5e~4>p4CK{lnw2,fo_ZAV1NI%P]YPLM8;]?|x<j-oN9${_Z#$EODym2W|M&(:B`');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ocz_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', true );
// Disable display of errors and warnings
define( 'WP_DEBUG_DISPLAY', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
